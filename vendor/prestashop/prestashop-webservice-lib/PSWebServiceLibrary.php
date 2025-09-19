<?php
/*
* 2007-2022 PrestaShop SA and Contributors
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to https://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2022 PrestaShop SA
*  @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
* PrestaShop Webservice Library - Version Améliorée
* @package PrestaShopWebservice
*/

namespace prestashop;

/**
 * @package PrestaShopWebservice
 */
class PrestaShopWebservice
{
    /** @var string Shop URL */
    protected $url;

    /** @var string Authentication key */
    protected $key;

    /** @var boolean is debug activated */
    protected $debug;

    /** @var string debug HTML */
    public $debugLog;

    /** @var int Shop ID for multistore */
    public $id_shop;

    /** @var int Group shop ID for multistore */
    public $id_group_shop;

    /** @var string PS version */
    protected $version;

    public $rawResponse;

    /** @var string Minimal version of PrestaShop to use with this library */
    const psCompatibleVersionsMin = '1.4.0.0';
    /** @var string Maximal version of PrestaShop to use with this library */
    const psCompatibleVersionsMax = '12.0'; // Version élargie pour compatibilité

    /** @var int Temporisation pour relancer une requête KO */
    private $temporisation = 0;

    /** @var array Cache pour optimisation */
    public $cache_get_schema;

    /**
     * PrestaShopWebservice constructor. Throw an exception when CURL is not installed/activated
     *
     * @param string $url Root URL for the shop
     * @param string $key Authentication key
     * @param mixed $debug Debug mode Activated (true) or deactivated (false)
     *
     * @throws PrestaShopWebserviceException if curl is not loaded
     */
    function __construct($url, $key, $debug = true)
    {
        if (!extension_loaded('curl')) {
            throw new PrestaShopWebserviceException(
                'Please activate the PHP extension \'curl\' to allow use of PrestaShop webservice library'
            );
        }
        $this->url = rtrim($url, '/'); // Normalisation de l'URL
        $this->key = $key;
        $this->debug = $debug;
        $this->debugLog = '';
        $this->version = 'unknown';
        $this->id_shop = -1;
        $this->id_group_shop = -1;
    }

    /**
     * Take the status code and throw an exception if the server didn't return 200 or 201 code
     *
     * @param int $status_code Status code of an HTTP return
     * @return string Error message or empty string
     */
    protected function checkStatusCode($status_code)
    {
        $error_label = 'Erreur du webservice Prestashop (code HTTP %d : %s)';
        
        switch ($status_code) {
            case 200:
            case 201:
                return '';
            case 204:
                return sprintf($error_label, $status_code, 'No content');
            case 400:
                return sprintf($error_label, $status_code, 'Bad Request');
            case 401:
                return sprintf($error_label, $status_code, 'Accès non autorisé - Vérifiez votre clé API et les permissions du webservice');
            case 404:
                return sprintf($error_label, $status_code, 'Not Found - Ressource introuvable');
            case 405:
                return 'Méthode interdite, vérifiez la configuration du serveur et les droits du webservice Prestashop';
            case 500:
                return sprintf($error_label, $status_code, 'Internal Server Error. Une erreur fatale PHP est probablement survenue. Si le problème persiste, contactez votre agence web');
            case 502:
            case 503:
            case 504:
                return sprintf($error_label, $status_code, 'Erreur temporaire de connexion. Si le problème persiste, contactez votre hébergeur ou votre fournisseur d\'accès à internet.');
            case 521:
                return "Erreur inconnue Cloudflare, contactez leur support technique.";
            case 522:
                return "Le serveur a refusé la connexion depuis Cloudflare.";
            case 523:
                return "Cloudflare n'a pas pu négocier un TCP handshake avec le serveur d'origine.";
            case 524:
                return "Cloudflare a établi une connexion TCP avec le serveur d'origine mais n'a pas reçu de réponse HTTP avant l'expiration du délai de connexion.";
            case 525:
                return "Cloudflare n'a pas pu négocier un SSL/TLS handshake avec le serveur d'origine.";
            case 526:
                return "Cloudflare n'a pas pu valider le certificat SSL présenté par le serveur d'origine.";
            case 527:
                return "L'erreur 527 indique que la requête a dépassé le délai de connexion ou a échoué après que la connexion WAN ait été établie.";
            default:
                return 'Erreur HTTP inattendue code ' . $status_code . '. Si le problème persiste, contactez le support technique.';
        }
    }

    /**
     * Provides default parameters for the curl connection(s)
     * @return array Default parameters for curl connection(s)
     */
    protected function getCurlDefaultParams()
    {
        return [
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLINFO_HEADER_OUT => true,
            CURLOPT_USERAGENT => 'Vaisonet e-connecteur',
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => $this->key . ':',
            CURLOPT_TIMEOUT => 300, // Timeout plus long
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_HTTPHEADER => ['Expect:'], // Important pour éviter les problèmes avec certains serveurs
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_SSL_VERIFYPEER => false, // Désactivé par défaut comme dans le code original
            CURLOPT_SSL_VERIFYHOST => 0,
        ];
    }

    /**
     * Handles a CURL request to PrestaShop Webservice. Can throw exception.
     *
     * @param string $url Resource name
     * @param mixed $curl_params CURL parameters (sent to curl_set_opt)
     *
     * @return array status_code, response, header
     *
     * @throws PrestaShopWebserviceException
     */
    protected function executeRequest($url, $curl_params = [])
    {
        $defaultParams = $this->getCurlDefaultParams();

        // Pour tous les clients incapables de configurer correctement leurs Prestashops
        $separator = (strpos($url, '?') !== false) ? '&' : '?';
        $url .= $separator . 'ws_key=' . $this->key;

        // Gestion multistore
        if ($this->id_shop != -1 && !empty($this->id_shop)) {
            $url .= '&id_shop=' . $this->id_shop;
        }

        if ($this->id_group_shop != -1 && !empty($this->id_group_shop)) {
            $url .= '&id_group_shop=' . $this->id_group_shop;
        }

        $session = curl_init();
        if ($session === false) {
            throw new PrestaShopWebserviceException('Failed to initialize CURL session');
        }

        curl_setopt($session, CURLOPT_URL, $url);

        // Fusion des paramètres
        $curl_options = [];
        foreach ($defaultParams as $defkey => $defval) {
            $curl_options[$defkey] = isset($curl_params[$defkey]) ? $curl_params[$defkey] : $defval;
        }
        foreach ($curl_params as $defkey => $defval) {
            if (!isset($curl_options[$defkey])) {
                $curl_options[$defkey] = $defval;
            }
        }

        // Gestion SSL améliorée
        if (strpos($url, 'https://') !== false) {
            // Configuration SSL plus robuste
            curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($session, CURLOPT_SSL_VERIFYHOST, 0);
            // Possibilité d'ajouter un certificat personnalisé si nécessaire
            // curl_setopt($session, CURLOPT_CAINFO, $certificat);
        }

        $setOptResult = curl_setopt_array($session, $curl_options);
        if ($setOptResult === false) {
            curl_close($session);
            throw new PrestaShopWebserviceException('Failed to set CURL options');
        }

        $response = curl_exec($session);
        $this->rawResponse = $response;

        if ($response === false) {
            $curl_error = curl_error($session);
            curl_close($session);
            
            // Gestion spécifique des erreurs de redirection HTTP vers HTTPS
            if (stripos($url, 'http://') !== false && stripos($curl_error, 'SSL certificate problem') !== false) {
                throw new PrestaShopWebserviceException(
                    'Votre hébergement redirige les connexions HTTP vers HTTPS. Vous devez modifier la configuration pour utiliser HTTPS. Erreur: ' . $curl_error
                );
            }
            
            throw new PrestaShopWebserviceException('Erreur de connexion : ' . $this->translateCurlError($curl_error));
        }

        $status_code = curl_getinfo($session, CURLINFO_HTTP_CODE);
        
        // Gestion améliorée de la séparation header/body
        $index = strpos($response, "\r\n\r\n");
        if ($index === false && (!isset($curl_params[CURLOPT_CUSTOMREQUEST]) || $curl_params[CURLOPT_CUSTOMREQUEST] != 'HEAD')) {
            $this->handleBadHttpResponse($url, $response, $curl_error ?? curl_error($session));
        }

        $header = ($index !== false) ? substr($response, 0, $index) : '';
        $body = ($index !== false) ? substr($response, $index + 4) : $response;

        // Vérification de compatibilité des versions
        $this->checkVersionCompatibility($header);

        // Debug
        $this->appendDebug('URL', $url);
        $this->appendDebug('HTTP REQUEST HEADER', curl_getinfo($session, CURLINFO_HEADER_OUT));
        $this->appendDebug('HTTP RESPONSE HEADER', $header);
        $this->appendDebug('HTTP STATUS CODE', $status_code);

        if ($status_code === 0) {
            curl_close($session);
            throw new PrestaShopWebserviceException('Erreur de connexion : ' . $this->translateCurlError(curl_error($session)));
        }

        curl_close($session);

        // Debug conditionnel pour les requêtes POST/PUT
        if (isset($curl_params[CURLOPT_CUSTOMREQUEST]) && 
            ($curl_params[CURLOPT_CUSTOMREQUEST] == 'PUT' || $curl_params[CURLOPT_CUSTOMREQUEST] == 'POST')) {
            $this->appendDebug('XML SENT', $curl_params[CURLOPT_POSTFIELDS] ?? '');
        }

        if ((!isset($curl_params[CURLOPT_CUSTOMREQUEST]) || 
            ($curl_params[CURLOPT_CUSTOMREQUEST] != 'DELETE' && $curl_params[CURLOPT_CUSTOMREQUEST] != 'HEAD'))) {
            $this->appendDebug('RETURN HTTP BODY', $body);
        }

        // Gestion du fichier de debug
        if ($this->debug) {
            $this->writeDebugLog();
        }

        return [
            'status_code' => $status_code,
            'response' => $body,
            'header' => $header
        ];
    }

    /**
     * Gère les mauvaises réponses HTTP avec temporisation
     */
    private function handleBadHttpResponse($url, $response, $curl_error)
    {
        $this->writeLog('Le format de l\'entête HTTP est incorrect (Bad HTTP response)', true);
        $this->writeLog('URL : ' . $url, true);
        $this->writeLog('Réponse : ' . substr($response, 0, 500), true);
        $this->writeLog('Serveur : ' . $curl_error, true);
        $this->writeLog('Erreur de communication avec le serveur, tentative dans 60 secondes');
        
        sleep(60);
        $this->temporisation++;

        if ($this->temporisation > 5) {
            throw new PrestaShopWebserviceException(
                'Le serveur Prestashop ne répond pas correctement (Bad HTTP response). Message du serveur : ' . $curl_error
            );
        }
    }

    /**
     * Vérifie la compatibilité des versions
     */
    private function checkVersionCompatibility($header)
    {
        $headerArrayTmp = explode("\n", $header);
        $headerArray = [];
        
        foreach ($headerArrayTmp as $headerItem) {
            $tmp = explode(':', $headerItem);
            $tmp = array_map('trim', $tmp);
            $tmp = array_map('strtolower', $tmp);
            if (count($tmp) == 2) {
                $headerArray[$tmp[0]] = $tmp[1];
            }
        }

        if (array_key_exists('psws-version', $headerArray)) {
            if (version_compare(self::psCompatibleVersionsMin, $headerArray['psws-version']) == 1 ||
                version_compare(self::psCompatibleVersionsMax, $headerArray['psws-version']) == -1) {
                throw new PrestaShopWebserviceException(
                    'This library is not compatible with this version of PrestaShop. Please upgrade/downgrade this library'
                );
            }
        }
    }

    /**
     * Traduit les erreurs CURL en français
     */
    private function translateCurlError($error)
    {
        $translations = [
            'Could not resolve host' => 'Impossible de résoudre l\'hôte',
            'Connection timed out' => 'Délai de connexion dépassé',
            'SSL certificate problem' => 'Problème de certificat SSL',
            'Failed to connect' => 'Échec de la connexion',
        ];

        foreach ($translations as $en => $fr) {
            if (strpos($error, $en) !== false) {
                return str_replace($en, $fr, $error);
            }
        }

        return $error;
    }

    /**
     * Ajoute du contenu au log de debug
     */
    public function appendDebug($title, $content = '')
    {
        if ($this->debug) {
            if ($content == '') {
                $this->debugLog .= "\n\n  **** $title ****\n";
            } else {
                $this->debugLog .= "\n\n  ****|| $title ||****\n $content \n\n ------- \n\n";
            }
        }
    }

    /**
     * Écrit le log de debug dans un fichier
     */
    private function writeDebugLog()
    {
        $fichier = 'psdebuglog.dat'; // Vous pouvez personnaliser le chemin
        file_put_contents($fichier, $this->debugLog, FILE_APPEND);
        
        if (is_file($fichier) && filesize($fichier) > 10000000) {
            // Rotation du log si trop volumineux
            rename($fichier, $fichier . '.old');
            $this->debugLog = '';
        }
    }

    /**
     * Écrit dans les logs
     */
    private function writeLog($message, $debug = false)
    {
        if ($debug && !$this->debug) return;
        
        // Implémentation basique - vous pouvez l'adapter à votre système de logs
        error_log('[PrestaShop WebService] ' . $message);
    }

    public function getRawResponse()
    {
        return $this->rawResponse ?? null;
    }

    public function printDebug($title, $content)
    {
        if (php_sapi_name() == 'cli') {
            echo $title . PHP_EOL . $content . PHP_EOL;
        } else {
            echo '<div style="display:table;background:#CCC;font-size:8pt;padding:7px"><h6 style="font-size:9pt;margin:0">'
                . $title
                . '</h6><pre>'
                . htmlentities($content)
                . '</pre></div>';
        }
    }

    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Load XML from string with improved error handling
     *
     * @param array $request Array with status_code and response
     * @return \SimpleXMLElement
     * @throws PrestaShopWebserviceException
     */
    protected function parseXML($request)
    {
        $errors = [];

        // Vérification du code de statut
        $status_code_error = $this->checkStatusCode($request['status_code']);
        if ($status_code_error != '') {
            $errors[] = $status_code_error;
        }

        if (!$request['response'] && $status_code_error == '') {
            throw new PrestaShopWebserviceException(
                'Le site web a fourni une réponse vide, c\'est anormal (HTTP response is empty). Contactez votre agence web si cela persiste.'
            );
        }

        if ($request['response']) {
            libxml_use_internal_errors(false); // Purge du cache d'erreur
            libxml_use_internal_errors(true);
            
            // Nettoyage de la réponse XML
            $cleanResponse = $this->cleanXmlResponse($request['response']);
            
            $xml = simplexml_load_string(trim($cleanResponse), 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NONET);

            if (libxml_get_errors()) {
                $xmlErrors = libxml_get_errors();
                $errorDetails = [];
                foreach ($xmlErrors as $error) {
                    $errorDetails[] = "Ligne {$error->line}: {$error->message}";
                }
                
                $errors[] = 'Le webservice ne fournit pas une réponse dans un format XML valide. Détails: ' . 
                           implode('; ', $errorDetails) . 
                           ' Réponse: ' . substr($request['response'], 0, 200);
                
                libxml_clear_errors();
            }

            // Gestion des erreurs PrestaShop
            if (isset($xml->errors)) {
                foreach ($xml->errors->children() as $error) {
                    $errors[] = $this->translatePrestaShopError((string) $error->message);
                }
            }
        }

        if (count($errors)) {
            throw new PrestaShopWebserviceException(implode("\n", $errors), $request['status_code']);
        }

        return $xml;
    }

    /**
     * Nettoie la réponse XML
     */
    private function cleanXmlResponse($response)
    {
        // Supprime le BOM UTF-8 si présent
        $response = preg_replace('/^\xEF\xBB\xBF/', '', $response);
        
        // Supprime les espaces en début et fin
        $response = trim($response);
        
        // Supprime tout ce qui précède la première balise XML
        $xmlStart = strpos($response, '<?xml');
        if ($xmlStart === false) {
            $xmlStart = strpos($response, '<');
            if ($xmlStart === false) {
                throw new PrestaShopWebserviceException('Aucun contenu XML trouvé dans la réponse');
            }
        }
        
        if ($xmlStart > 0) {
            $response = substr($response, $xmlStart);
        }
        
        return $response;
    }

    /**
     * Traduit les erreurs PrestaShop
     */
    private function translatePrestaShopError($message)
    {
        switch ($message) {
            case 'Internal error. To see this error please display the PHP errors.':
                return 'Prestashop refuse les données envoyées. Pour en connaître les raisons, vous devez activer l\'affichage des erreurs PHP.';
            
            case 'Unable to save resource':
                return "Prestashop répond l'erreur 'Unable to save resource'.";
            
            default:
                if (stripos($message, '[PHP') === false) {
                    return $message;
                } elseif (stripos($message, 'StockAvailable->quantity') !== false && 
                         stripos($message, 'Validation error') !== false) {
                    return "Prestashop n'accepte pas des stocks à virgule. Mettez un nombre entier en gestion commerciale.";
                } else {
                    return 'Votre agence web doit corriger les erreurs PHP suivantes : ' . $message;
                }
        }
    }

    /**
     * Add (POST) a resource
     */
    public function add($options)
    {
        if (isset($options['resource'], $options['postXml']) || isset($options['url'], $options['postXml'])) {
            $url = (isset($options['resource']) ? $this->url . '/api/' . $options['resource'] : $options['url']);
            $xml = $options['postXml'];
        } else {
            throw new PrestaShopWebserviceException('Bad parameters given');
        }

        $request = $this->executeRequest($url, [
            CURLOPT_CUSTOMREQUEST => 'POST', 
            CURLOPT_POSTFIELDS => 'xml=' . $xml
        ]);

        return $this->parseXML($request);
    }

    /**
     * Retrieve (GET) a resource with enhanced error handling
     */
    public function get($options)
    {
        // Construction de l'URL
        if (isset($options['url'])) {
            $url = $options['url'];
        } elseif (isset($options['resource'])) {
            $url = $this->url . '/api/' . $options['resource'];
            $url_params = [];

            if (isset($options['id'])) {
                $url .= '/' . $options['id'];
            }

            $params = ['filter', 'display', 'sort', 'limit', 'language'];
            foreach ($params as $p) {
                foreach ($options as $k => $o) {
                    if (strpos($k, $p) !== false) {
                        $url_params[$k] = $options[$k];
                    }
                }
            }

            if (count($url_params) > 0) {
                $url .= '?' . http_build_query($url_params);
            }
        } else {
            throw new PrestaShopWebserviceException('Bad parameters given');
        }

        // Cache pour les schémas
        if (strpos($url, '?schema=blank') !== false && isset($this->cache_get_schema[$url])) {
            $request = $this->cache_get_schema[$url];
        } else {
            $request = $this->executeRequest($url, [CURLOPT_CUSTOMREQUEST => 'GET']);
            $this->setCacheGetSchema($url, $request);
        }

        return $this->parseXML($request);
    }

    /**
     * Met en cache les schémas
     */
    public function setCacheGetSchema($url, $data)
    {
        if (strpos($url, '?schema=blank') !== false && !isset($this->cache_get_schema[$url])) {
            $this->cache_get_schema[$url] = $data;
        }
    }

    /**
     * Head method (HEAD) a resource
     */
    public function head($options)
    {
        if (isset($options['url'])) {
            $url = $options['url'];
        } elseif (isset($options['resource'])) {
            $url = $this->url . '/api/' . $options['resource'];
            $url_params = [];
            if (isset($options['id'])) {
                $url .= '/' . $options['id'];
            }

            $params = ['filter', 'display', 'sort', 'limit'];
            foreach ($params as $p) {
                foreach ($options as $k => $o) {
                    if (strpos($k, $p) !== false) {
                        $url_params[$k] = $options[$k];
                    }
                }
            }
            if (count($url_params) > 0) {
                $url .= '?' . http_build_query($url_params);
            }
        } else {
            throw new PrestaShopWebserviceException('Bad parameters given');
        }

        $request = $this->executeRequest($url, [CURLOPT_CUSTOMREQUEST => 'HEAD', CURLOPT_NOBODY => true]);
        $status_code_error = $this->checkStatusCode($request['status_code']);
        if ($status_code_error != '') {
            throw new PrestaShopWebserviceException($status_code_error);
        }
        return $request['header'];
    }

    /**
     * Edit (PUT) a resource - Configuration exacte du code original
     */
    public function edit($options)
    {
        $xml = '';
        if (isset($options['url'])) {
            $url = $options['url'];
        } elseif ((isset($options['resource'], $options['id']) || isset($options['url'])) && $options['putXml']) {
            $url = ($options['url'] ?? $this->url . '/api/' . $options['resource'] . '/' . $options['id']);
            $xml = $options['putXml'];
        } else {
            throw new PrestaShopWebserviceException('Bad parameters given');
        }

        // IMPORTANT: Utiliser exactement les mêmes headers que le code original
        $curlParams = [
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => $xml,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/xml',
                'Expect:'  // Header critique du code original
            ]
        ];

        $request = $this->executeRequest($url, $curlParams);
        return $this->parseXML($request);
    }

    /**
     * Delete (DELETE) a resource
     */
    public function delete($options)
    {
        if (isset($options['url'])) {
            $url = $options['url'];
        } elseif (isset($options['resource']) && isset($options['id'])) {
            if (is_array($options['id'])) {
                $url = $this->url . '/api/' . $options['resource'] . '/?id=[' . implode(',', $options['id']) . ']';
            } else {
                $url = $this->url . '/api/' . $options['resource'] . '/' . $options['id'];
            }
        } else {
            throw new PrestaShopWebserviceException('Bad parameters given');
        }

        $request = $this->executeRequest($url, [CURLOPT_CUSTOMREQUEST => 'DELETE']);
        $status_code_error = $this->checkStatusCode($request['status_code']);
        if ($status_code_error != '') {
            throw new PrestaShopWebserviceException($status_code_error);
        }
        return true;
    }

    /**
     * Définit l'ID de la boutique pour le multistore
     */
    public function setShop($id_shop)
    {
        $this->id_shop = $id_shop;
    }

    /**
     * Définit l'ID du groupe de boutiques pour le multistore
     */
    public function setGroupShop($id_group_shop)
    {
        $this->id_group_shop = $id_group_shop;
    }

    /**
     * Méthode pour diagnostiquer les problèmes de connexion
     */
    public function validateCredentials()
    {
        $diagnostics = [
            'url' => $this->url,
            'key_length' => strlen($this->key),
            'key_format' => ctype_alnum($this->key) ? 'Valid (alphanumeric)' : 'Invalid format',
            'url_format' => filter_var($this->url, FILTER_VALIDATE_URL) ? 'Valid URL' : 'Invalid URL',
            'ssl_enabled' => strpos($this->url, 'https://') === 0,
        ];

        try {
            $testUrl = $this->url . '/api/';
            $request = $this->executeRequest($testUrl, [CURLOPT_CUSTOMREQUEST => 'GET']);
            $diagnostics['connection_test'] = 'HTTP ' . $request['status_code'];

            if ($request['status_code'] == 401) {
                $diagnostics['auth_issue'] = 'Authentication failed - Check API key and webservice permissions';
            } elseif ($request['status_code'] == 200) {
                $diagnostics['auth_issue'] = 'Authentication successful';
            }
        } catch (PrestaShopWebserviceException $e) {
            $diagnostics['connection_test'] = 'Failed: ' . $e->getMessage();
        }

        return $diagnostics;
    }

    /**
     * Test d'authentification avec debug complet
     */
    public function testAuthentication()
    {
        $testUrl = $this->url . '/api/';
        
        if ($this->debug) {
            echo "=== TEST D'AUTHENTIFICATION ===\n";
            echo "URL: $testUrl\n";
            echo "API Key: " . substr($this->key, 0, 10) . "...\n";
            echo "Expected Auth Header: Basic " . substr(base64_encode($this->key . ':'), 0, 20) . "...\n\n";
        }

        try {
            $request = $this->executeRequest($testUrl, [CURLOPT_CUSTOMREQUEST => 'GET']);
            
            if ($this->debug) {
                echo "HTTP Code: " . $request['status_code'] . "\n";
                echo "Response (first 500 chars):\n" . substr($request['response'], 0, 500) . "\n";
            }

            return [
                'success' => $request['status_code'] == 200,
                'http_code' => $request['status_code'],
                'error' => ($request['status_code'] != 200) ? $this->checkStatusCode($request['status_code']) : null,
                'headers_received' => !empty($request['header'])
            ];
        } catch (\Exception $e) {
            if ($this->debug) {
                echo "Exception: " . $e->getMessage() . "\n";
            }
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Méthode pour ajouter des images (si supportée par votre environnement)
     */
    public function addImage($method, $image_path, $type, $id = '')
    {
        $url = $this->url . '/api/images/' . $type . '/' . $id;

        if ($this->id_shop != -1) {
            $url .= (strpos($url, '?') !== false ? '&' : '?') . 'id_shop=' . $this->id_shop;
        }

        if ($this->id_group_shop != -1) {
            $url .= (strpos($url, '?') !== false ? '&' : '?') . 'id_group_shop=' . $this->id_group_shop;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, 'Vaisonet e-connecteur');
        curl_setopt($ch, CURLOPT_URL, $url);
        
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
        } elseif ($method == 'PUT') {
            curl_setopt($ch, CURLOPT_PUT, true);
        }
        
        curl_setopt($ch, CURLOPT_USERPWD, $this->key . ':');
        curl_setopt($ch, CURLOPT_POSTFIELDS, ['image' => '@' . $image_path]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (strpos($url, 'https://') !== false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($result === false) {
            throw new PrestaShopWebserviceException('Erreur lors de l\'upload de l\'image');
        }
        
        return ['result' => $result, 'http_code' => $httpCode];
    }

    /**
     * Destructeur avec optimisations
     */
    public function __destruct()
    {
        // Nettoyage des ressources si nécessaire
        if ($this->debug && !empty($this->debugLog)) {
            $this->writeDebugLog();
        }
    }
}

/**
 * Exception personnalisée pour le webservice PrestaShop
 */
class PrestaShopWebserviceException extends \Exception 
{
    public function __construct($message = "", $code = 0, \Throwable $previous = null) 
    {
        parent::__construct($message, $code, $previous);
    }
}