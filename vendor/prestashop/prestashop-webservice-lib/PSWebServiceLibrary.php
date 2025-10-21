<?php
/*
* PrestaShop Webservice Library - Version Optimisée
* @package PrestaShopWebservice
* Open Software License (OSL 3.0)
*/

namespace prestashop;

class PrestaShopWebservice
{
    protected $url;
    protected $key;
    protected $debug;
    public $debugLog;
    public $id_shop;
    public $id_group_shop;
    protected $version;
    public $rawResponse;
    public $cache_get_schema;
    
    const psCompatibleVersionsMin = '1.4.0.0';
    const psCompatibleVersionsMax = '12.0';
    
    private $temporisation = 0;

    /**
     * Constructeur - Initialise la connexion au webservice
     */
    public function __construct($url, $key, $debug = false)
    {
        if (!extension_loaded('curl')) {
            throw new PrestaShopWebserviceException(
                'L\'extension PHP \'curl\' doit être activée pour utiliser le webservice PrestaShop'
            );
        }
        
        $this->url = rtrim($url, '/');
        $this->key = $key;
        $this->debug = $debug;
        $this->debugLog = '';
        $this->version = 'unknown';
        $this->id_shop = -1;
        $this->id_group_shop = -1;
    }

    /**
     * Vérifie le code HTTP et retourne un message d'erreur adapté
     */
    protected function checkStatusCode($status_code)
    {
        $errors = [
            200 => '',
            201 => '',
            204 => 'Aucun contenu retourné',
            400 => 'Requête incorrecte - Vérifiez les données envoyées',
            401 => 'Accès non autorisé - Vérifiez votre clé API et les permissions',
            404 => 'Ressource introuvable - L\'élément demandé n\'existe pas',
            405 => 'Méthode interdite - Vérifiez la configuration du webservice',
            500 => 'Erreur serveur - Une erreur PHP est probablement survenue sur PrestaShop',
            502 => 'Erreur de passerelle - Le serveur ne répond pas correctement',
            503 => 'Service temporairement indisponible',
            504 => 'Délai d\'attente dépassé',
        ];

        return $errors[$status_code] ?? "Erreur HTTP $status_code - Erreur inattendue";
    }

    /**
     * Configuration CURL par défaut
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
            CURLOPT_TIMEOUT => 300,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_HTTPHEADER => ['Expect:'],
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
        ];
    }

    /**
     * Exécute une requête CURL vers le webservice
     */
    protected function executeRequest($url, $curl_params = [])
    {
        $defaultParams = $this->getCurlDefaultParams();

        // Ajout de la clé d'API à l'URL
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
            throw new PrestaShopWebserviceException('Impossible d\'initialiser CURL');
        }

        curl_setopt($session, CURLOPT_URL, $url);

        // Fusion des paramètres CURL
        foreach ($defaultParams as $key => $val) {
            if (!isset($curl_params[$key])) {
                $curl_params[$key] = $val;
            }
        }

        curl_setopt_array($session, $curl_params);

        $response = curl_exec($session);
        $this->rawResponse = $response;

        if ($response === false) {
            $curl_error = curl_error($session);
            curl_close($session);
            
            // Détection redirection HTTP vers HTTPS
            if (stripos($url, 'http://') !== false && stripos($curl_error, 'SSL certificate') !== false) {
                throw new PrestaShopWebserviceException(
                    'Votre hébergement redirige HTTP vers HTTPS. Utilisez https:// dans votre URL'
                );
            }
            
            throw new PrestaShopWebserviceException('Erreur de connexion : ' . $curl_error);
        }

        $status_code = curl_getinfo($session, CURLINFO_HTTP_CODE);
        
        // Séparation header/body
        $index = strpos($response, "\r\n\r\n");
        if ($index === false && (!isset($curl_params[CURLOPT_CUSTOMREQUEST]) || $curl_params[CURLOPT_CUSTOMREQUEST] != 'HEAD')) {
            curl_close($session);
            $this->handleBadHttpResponse($url, $response);
        }

        $header = ($index !== false) ? substr($response, 0, $index) : '';
        $body = ($index !== false) ? substr($response, $index + 4) : $response;

        // Debug
        if ($this->debug) {
            $this->appendDebug('URL', $url);
            $this->appendDebug('HTTP STATUS', $status_code);
            $this->appendDebug('RESPONSE HEADER', $header);
            if (!in_array($curl_params[CURLOPT_CUSTOMREQUEST] ?? 'GET', ['DELETE', 'HEAD'])) {
                $this->appendDebug('RESPONSE BODY', substr($body, 0, 1000));
            }
            $this->writeDebugLog();
        }

        curl_close($session);

        return [
            'status_code' => $status_code,
            'response' => $body,
            'header' => $header
        ];
    }

    /**
     * Gère les réponses HTTP malformées
     */
    private function handleBadHttpResponse($url, $response)
    {
        $this->temporisation++;
        
        if ($this->temporisation > 3) {
            throw new PrestaShopWebserviceException(
                'Le serveur PrestaShop ne répond pas correctement. Format de réponse HTTP invalide.'
            );
        }
        
        if ($this->debug) {
            error_log("Bad HTTP response - Attempt {$this->temporisation}/3");
        }
        
        sleep(5); // Attente avant nouvelle tentative
    }

    /**
     * Parse la réponse XML
     */
    protected function parseXML($request)
    {
        $errors = [];

        // Vérification du code HTTP
        $status_error = $this->checkStatusCode($request['status_code']);
        if ($status_error != '') {
            $errors[] = $status_error;
        }

        // Vérification de la réponse
        if (empty($request['response']) && $status_error == '') {
            throw new PrestaShopWebserviceException(
                'Le serveur PrestaShop a retourné une réponse vide'
            );
        }

        if ($request['response']) {
            libxml_use_internal_errors(true);
            
            // Nettoyage XML
            $cleanResponse = $this->cleanXmlResponse($request['response']);
            $xml = simplexml_load_string(trim($cleanResponse), 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NONET);

            // Erreurs de parsing XML
            if (libxml_get_errors()) {
                $xmlErrors = libxml_get_errors();
                $errorDetails = [];
                foreach ($xmlErrors as $error) {
                    $errorDetails[] = "Ligne {$error->line}: {$error->message}";
                }
                
                $errors[] = 'Réponse XML invalide : ' . implode('; ', $errorDetails);
                libxml_clear_errors();
            }

            // Erreurs PrestaShop dans le XML
            if (isset($xml->errors)) {
                foreach ($xml->errors->children() as $error) {
                    $errors[] = $this->translatePrestaShopError((string) $error->message);
                }
            }
        }

        if (count($errors)) {
            throw new PrestaShopWebserviceException(
                implode("\n", $errors),
                $request['status_code']
            );
        }

        return $xml;
    }

    /**
     * Nettoie la réponse XML
     */
    private function cleanXmlResponse($response)
    {
        // Supprime BOM UTF-8
        $response = preg_replace('/^\xEF\xBB\xBF/', '', $response);
        $response = trim($response);
        
        // Trouve le début du XML
        $xmlStart = strpos($response, '<?xml');
        if ($xmlStart === false) {
            $xmlStart = strpos($response, '<');
        }
        
        if ($xmlStart !== false && $xmlStart > 0) {
            $response = substr($response, $xmlStart);
        }
        
        return $response;
    }

    /**
     * Traduit les erreurs PrestaShop
     */
    private function translatePrestaShopError($message)
    {
        $translations = [
            'Internal error. To see this error please display the PHP errors.' => 
                'Erreur interne PrestaShop. Activez l\'affichage des erreurs PHP pour plus de détails.',
            'Unable to save resource' => 
                'Impossible de sauvegarder la ressource. Vérifiez les données envoyées.',
        ];

        foreach ($translations as $en => $fr) {
            if (stripos($message, $en) !== false) {
                return $fr;
            }
        }

        // Erreur de validation de stock
        if (stripos($message, 'StockAvailable->quantity') !== false && 
            stripos($message, 'Validation error') !== false) {
            return 'PrestaShop n\'accepte pas les stocks décimaux. Utilisez un nombre entier.';
        }

        // Erreur PHP
        if (stripos($message, '[PHP') !== false) {
            return 'Erreur PHP sur PrestaShop : ' . $message;
        }

        return $message;
    }

    /**
     * Ajoute des logs de debug
     */
    public function appendDebug($title, $content = '')
    {
        if ($this->debug) {
            $this->debugLog .= "\n\n=== $title ===\n$content\n";
        }
    }

    /**
     * Écrit les logs dans un fichier
     */
    private function writeDebugLog()
    {
        if (!$this->debug) return;
        
        $logFile = \Yii::getAlias('@runtime/logs/prestashop_debug.log');
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        
        file_put_contents(
            $logFile,
            date('[Y-m-d H:i:s] ') . $this->debugLog . "\n\n",
            FILE_APPEND
        );
        
        // Rotation si fichier trop volumineux (> 10MB)
        if (file_exists($logFile) && filesize($logFile) > 10000000) {
            rename($logFile, $logFile . '.' . date('YmdHis') . '.old');
        }
    }

    /**
     * GET - Récupère une ressource
     */
    public function get($options)
    {
        if (isset($options['url'])) {
            $url = $options['url'];
        } elseif (isset($options['resource'])) {
            $url = $this->url . '/api/' . $options['resource'];
            
            if (isset($options['id'])) {
                $url .= '/' . $options['id'];
            }

            // Paramètres de requête
            $url_params = [];
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
            throw new PrestaShopWebserviceException('Paramètres manquants pour la requête GET');
        }

        // Utilisation du cache pour les schémas
        if (strpos($url, '?schema=blank') !== false && isset($this->cache_get_schema[$url])) {
            $request = $this->cache_get_schema[$url];
        } else {
            $request = $this->executeRequest($url, [CURLOPT_CUSTOMREQUEST => 'GET']);
            if (strpos($url, '?schema=blank') !== false) {
                $this->cache_get_schema[$url] = $request;
            }
        }

        return $this->parseXML($request);
    }

    /**
     * POST - Ajoute une ressource
     */
    public function add($options)
    {
        if (!isset($options['postXml'])) {
            throw new PrestaShopWebserviceException('Le paramètre postXml est requis');
        }

        $url = isset($options['url']) 
            ? $options['url'] 
            : $this->url . '/api/' . $options['resource'];

        $request = $this->executeRequest($url, [
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'xml=' . $options['postXml']
        ]);

        return $this->parseXML($request);
    }

    /**
     * PUT - Modifie une ressource
     */
    public function edit($options)
    {
        if (!isset($options['putXml'])) {
            throw new PrestaShopWebserviceException('Le paramètre putXml est requis');
        }

        $url = isset($options['url']) 
            ? $options['url']
            : $this->url . '/api/' . $options['resource'] . '/' . $options['id'];

        $request = $this->executeRequest($url, [
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => $options['putXml'],
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/xml',
                'Expect:'
            ]
        ]);

        return $this->parseXML($request);
    }

    /**
     * DELETE - Supprime une ressource
     */
    public function delete($options)
    {
        if (isset($options['url'])) {
            $url = $options['url'];
        } elseif (isset($options['resource']) && isset($options['id'])) {
            $id_param = is_array($options['id']) 
                ? '?id=[' . implode(',', $options['id']) . ']'
                : '/' . $options['id'];
            $url = $this->url . '/api/' . $options['resource'] . $id_param;
        } else {
            throw new PrestaShopWebserviceException('Paramètres manquants pour DELETE');
        }

        $request = $this->executeRequest($url, [CURLOPT_CUSTOMREQUEST => 'DELETE']);
        
        $error = $this->checkStatusCode($request['status_code']);
        if ($error != '') {
            throw new PrestaShopWebserviceException($error);
        }
        
        return true;
    }

    /**
     * HEAD - Vérifie l'existence d'une ressource
     */
    public function head($options)
    {
        if (isset($options['url'])) {
            $url = $options['url'];
        } elseif (isset($options['resource'])) {
            $url = $this->url . '/api/' . $options['resource'];
            if (isset($options['id'])) {
                $url .= '/' . $options['id'];
            }
        } else {
            throw new PrestaShopWebserviceException('Paramètres manquants pour HEAD');
        }

        $request = $this->executeRequest($url, [
            CURLOPT_CUSTOMREQUEST => 'HEAD',
            CURLOPT_NOBODY => true
        ]);
        
        $error = $this->checkStatusCode($request['status_code']);
        if ($error != '') {
            throw new PrestaShopWebserviceException($error);
        }
        
        return $request['header'];
    }

    // Méthodes utilitaires
    public function setShop($id_shop) { $this->id_shop = $id_shop; }
    public function setGroupShop($id_group_shop) { $this->id_group_shop = $id_group_shop; }
    public function getRawResponse() { return $this->rawResponse; }
    public function getVersion() { return $this->version; }
}

/**
 * Exception personnalisée pour le webservice
 */
class PrestaShopWebserviceException extends \Exception 
{
    public function __construct($message = "", $code = 0, \Throwable $previous = null) 
    {
        parent::__construct($message, $code, $previous);
    }
}