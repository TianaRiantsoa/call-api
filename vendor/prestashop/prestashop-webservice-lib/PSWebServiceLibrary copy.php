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
* PrestaShop Webservice Library
* @package PrestaShopWebservice
*/

namespace prestashopqsdqsdqsdqsd;

/**
 * @package PrestaShopWebservice
 */

class PrestaShopWebserviceqsdqsdqsdqsd
{
    /** @var string Shop URL */
    protected $url;

    /** @var string Authentication key */
    protected $key;

    /** @var boolean is debug activated */
    protected $debug;

    /** @var string PS version */
    protected $version;

    public $rawResponse;

    /** @var string Minimal version of PrestaShop to use with this library */
    const psCompatibleVersionsMin = '1.4.0.0';
    /** @var string Maximal version of PrestaShop to use with this library */
    const psCompatibleVersionsMax = '10.0.0';

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
        $this->url = $url;
        $this->key = $key;
        $this->debug = $debug;
        $this->version = 'unknown';
    }

    /**
     * Take the status code and throw an exception if the server didn't return 200 or 201 code
     *
     * @param array $request Response elements of CURL request
     *
     * @throws PrestaShopWebserviceException if HTTP status code is not 200 or 201
     */
    protected function checkStatusCode($request)
    {
        switch ($request['status_code']) {
            case 200:
            case 201:
                break;
            case 204:
                $error_message = 'No content';
                break;
            case 400:
                $error_message = 'Bad Request';
                break;
            case 401:
                $error_message = 'Unauthorized';
                break;
            case 404:
                $error_message = 'Not Found';
                break;
            case 405:
                $error_message = 'Method Not Allowed';
                break;
            case 500:
                $error_message = 'Internal Server Error';
                break;
            default:
                throw new PrestaShopWebserviceException(
                    'This call to PrestaShop Web Services returned an unexpected HTTP status of:' . $request['status_code']
                );
        }

        if (!empty($error_message)) {
            // Tentative de parsing XML pour récupérer le message d'erreur détaillé
            try {
                $response = $this->parseXML($request['response']);
                $errors = $response->children()->children();
                if ($errors && count($errors) > 0) {
                    foreach ($errors as $error) {
                        $error_message .= ' - (Code ' . $error->code . '): ' . $error->message;
                    }
                }
            } catch (PrestaShopWebserviceException $e) {
                // Si le parsing XML échoue, on garde juste le message d'erreur de base
            }

            $error_label = 'This call to PrestaShop Web Services failed and returned an HTTP status of %d. That means: %s.';
            throw new PrestaShopWebserviceException(sprintf($error_label, $request['status_code'], $error_message));
        }
    }

    /**
     * Provides default parameters for the curl connection(s)
     * @return array Default parameters for curl connection(s)
     */
    protected function getCurlDefaultParams()
    {
        return [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => $this->key . ':',
            CURLOPT_USERAGENT => 'Vaisonet e-connecteur',
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/xml',
                'Accept: application/xml'
            ],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_VERBOSE => false,
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
    protected function executeRequest($url, $curl_params = array())
    {
        $defaultParams = $this->getCurlDefaultParams();

        // Remplacez CURLOPT_HEADEROUT par une fonction de rappel
        $capturedHeaders = '';
        $defaultParams[CURLOPT_HEADERFUNCTION] = function ($ch, $header) use (&$capturedHeaders) {
            $capturedHeaders .= $header;
            return strlen($header);
        };

        $session = curl_init($url);
        if ($session === false) {
            throw new PrestaShopWebserviceException('Failed to initialize CURL session');
        }

        $curl_options = array();
        foreach ($defaultParams as $defkey => $defval) {
            $curl_options[$defkey] = isset($curl_params[$defkey]) ? $curl_params[$defkey] : $defval;
        }
        foreach ($curl_params as $defkey => $defval) {
            if (!isset($curl_options[$defkey])) {
                $curl_options[$defkey] = $defval;
            }
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
            throw new PrestaShopWebserviceException('CURL Execution Error: ' . $curl_error);
        }

        $status_code = curl_getinfo($session, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($session, CURLINFO_HEADER_SIZE);
        $header = '';
        $body = $response;

        if ($headerSize !== false && $headerSize > 0) {
            $header = substr($response, 0, $headerSize);
            $body = substr($response, $headerSize);
        }

        if ($this->debug) {
            $this->printDebug('REQUEST URL', $url);
            $this->printDebug('HTTP REQUEST HEADER (SENT)', $capturedHeaders ?: 'HEADERS NOT CAPTURED');
            $this->printDebug('HTTP RESPONSE HEADER', $header);
            $this->printDebug('HTTP STATUS CODE', $status_code);
            $this->printDebug('RETURN HTTP BODY', $body);
        }

        curl_close($session);

        return [
            'status_code' => $status_code,
            'response' => $body,
            'header' => $header,
            'request_headers' => $capturedHeaders
        ];
    }
    public function getRawResponse()
    {
        return $this->rawResponse ?? null;
    }

    public function printDebug($title, $content)
    {
        if (php_sapi_name() == 'cli') {
            echo $title . PHP_EOL . $content;
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
     * Valide l'URL de la boutique et la clé API
     * @return array Informations de diagnostic
     * @throws PrestaShopWebserviceException
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

        // Test basique de connectivité
        try {
            $testUrl = rtrim($this->url, '/') . '/api/';
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
     * Test d'authentification simple avec debug complet
     * @return array Résultat du test
     */
    public function testAuthentication()
    {
        $testUrl = rtrim($this->url, '/') . '/api/';

        echo "=== TEST D'AUTHENTIFICATION ===\n";
        echo "URL: $testUrl\n";
        echo "API Key: " . substr($this->key, 0, 10) . "...\n";
        echo "Expected Auth Header: Basic " . substr(base64_encode($this->key . ':'), 0, 20) . "...\n\n";

        try {
            // Test avec CURL minimal et authentification forcée
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $testUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => true,
                CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
                // CURLOPT_HEADEROUT => true,
                CURLOPT_USERPWD => $this->key . ':',
                CURLOPT_HTTPHEADER => [
                    'Authorization: Basic ' . base64_encode($this->key . ':'),
                    'User-Agent: Mozilla/5.0'
                ],
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT => 30
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $requestHeaders = curl_getinfo($ch, CURLINFO_HEADER_OUT);
            $error = curl_error($ch);
            curl_close($ch);

            echo "CURL Error: " . ($error ?: 'None') . "\n";
            echo "HTTP Code: $httpCode\n";
            echo "Request Headers Sent:\n$requestHeaders\n";
            echo "Response (first 500 chars):\n" . substr($response, 0, 500) . "\n";

            return [
                'success' => $httpCode == 200,
                'http_code' => $httpCode,
                'error' => $error,
                'headers_sent' => !empty($requestHeaders)
            ];
        } catch (\Exception $e) {
            echo "Exception: " . $e->getMessage() . "\n";
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Nettoie et valide une réponse XML avant le parsing
     *
     * @param string $response Réponse brute
     * @return string Réponse nettoyée
     * @throws PrestaShopWebserviceException
     */
    protected function cleanXmlResponse($response)
    {
        if (empty($response)) {
            throw new PrestaShopWebserviceException('HTTP response is empty');
        }

        // Supprime le BOM UTF-8 si présent
        $response = preg_replace('/^\xEF\xBB\xBF/', '', $response);

        // Supprime les espaces/retours à la ligne en début et fin
        $response = trim($response);

        // Supprime tout ce qui précède la première balise XML
        $xmlStart = strpos($response, '<?xml');
        if ($xmlStart === false) {
            // Pas de déclaration XML, cherche la première balise
            $xmlStart = strpos($response, '<');
            if ($xmlStart === false) {
                throw new PrestaShopWebserviceException(
                    'No XML content found in response. Response content: ' . substr($response, 0, 500)
                );
            }
        }

        if ($xmlStart > 0) {
            $response = substr($response, $xmlStart);
        }

        // Vérification basique de la structure XML
        if (!preg_match('/^<\?xml|^<[a-zA-Z]/', $response)) {
            throw new PrestaShopWebserviceException(
                'Response does not appear to be valid XML. First 200 chars: ' . substr($response, 0, 200)
            );
        }

        return $response;
    }

    /**
     * Load XML from string. Can throw exception
     *
     * @param string $response String from a CURL response
     *
     * @return SimpleXMLElement status_code, response
     * @throws PrestaShopWebserviceException
     */
    protected function parseXML($response)
    {
        // Nettoyage de la réponse
        $cleanedResponse = $this->cleanXmlResponse($response);

        libxml_clear_errors();
        libxml_use_internal_errors(true);

        if (LIBXML_VERSION < 20900) {
            // Avoid load of external entities (security problem).
            // Required only if LIBXML_VERSION < 20900
            libxml_disable_entity_loader(true);
        }

        $xml = simplexml_load_string($cleanedResponse, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NONET);

        $errors = libxml_get_errors();
        if ($errors) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = "Line {$error->line}, Column {$error->column}: {$error->message}";
            }
            libxml_clear_errors();

            // Debug info pour aider au diagnostic
            $debugInfo = [
                'Original response length' => strlen($response),
                'Cleaned response length' => strlen($cleanedResponse),
                'First 500 chars of cleaned response' => substr($cleanedResponse, 0, 500),
                'XML errors' => $errorMessages
            ];

            if ($this->debug) {
                $this->printDebug('XML PARSING DEBUG INFO', print_r($debugInfo, true));
            }

            throw new PrestaShopWebserviceException(
                'HTTP XML response is not parsable: ' . implode('; ', $errorMessages) .
                    '. Response preview: ' . substr($cleanedResponse, 0, 200)
            );
        }

        if ($xml === false) {
            throw new PrestaShopWebserviceException(
                'Failed to parse XML response. Response preview: ' . substr($cleanedResponse, 0, 200)
            );
        }

        return $xml;
    }

    /**
     * Add (POST) a resource
     *
     * @param array $options
     *
     * @return SimpleXMLElement status_code, response
     * @throws PrestaShopWebserviceException
     */
    public function add($options)
    {
        $xml = '';

        if (isset($options['resource'], $options['postXml']) || isset($options['url'], $options['postXml'])) {
            $url = (isset($options['resource']) ? $this->url . '/api/' . $options['resource'] : $options['url']);
            $xml = $options['postXml'];
            if (isset($options['id_shop'])) {
                $url .= '&id_shop=' . $options['id_shop'];
            }
            if (isset($options['id_group_shop'])) {
                $url .= '&id_group_shop=' . $options['id_group_shop'];
            }
        } else {
            throw new PrestaShopWebserviceException('Bad parameters given');
        }

        $request = $this->executeRequest($url, array(CURLOPT_CUSTOMREQUEST => 'POST', CURLOPT_POSTFIELDS => $xml));

        $this->checkStatusCode($request);
        return $this->parseXML($request['response']);
    }

    /**
     * Retrieve (GET) a resource
     *
     * @param array $options Array representing resource to get.
     *
     * @return SimpleXMLElement status_code, response
     * @throws PrestaShopWebserviceException
     */
    public function get($options)
    {
        // Construction de l'URL
        if (isset($options['url'])) {
            $url = $options['url'];
        } elseif (isset($options['resource'])) {
            $url = $this->url . '/api/' . $options['resource'];
            $url_params = array();

            // Ajout de l'ID si spécifié
            if (isset($options['id'])) {
                $url .= '/' . $options['id'];
            }

            // Gestion des paramètres optionnels
            $params = array('filter', 'display', 'sort', 'limit', 'id_shop', 'id_group_shop', 'schema', 'language', 'date', 'price');
            foreach ($params as $p) {
                foreach ($options as $k => $o) {
                    if (strpos($k, $p) !== false) {
                        $url_params[$k] = $options[$k];
                    }
                }
            }

            // Ajout des paramètres à l'URL
            if (count($url_params) > 0) {
                $url .= '?' . http_build_query($url_params);
            }
        } else {
            throw new PrestaShopWebserviceException('Bad parameters given: Missing "url" or "resource" in options.');
        }

        try {
            // Exécution de la requête GET
            $request = $this->executeRequest($url, array(CURLOPT_CUSTOMREQUEST => 'GET'));

            // Vérification du code de statut HTTP
            $this->checkStatusCode($request);

            // Parsing de la réponse XML avec nettoyage automatique
            return $this->parseXML($request['response']);
        } catch (PrestaShopWebserviceException $e) {
            // Enrichissement du message d'erreur avec des informations de debug
            $errorMsg = "GET request failed for URL: {$url}. " . $e->getMessage();

            if ($this->debug && isset($request)) {
                $errorMsg .= " [Debug: HTTP Status: {$request['status_code']}, Response length: " . strlen($request['response']) . "]";
            }

            throw new PrestaShopWebserviceException($errorMsg, $e->getCode(), $e);
        } catch (\Exception $e) {
            throw new PrestaShopWebserviceException("Unexpected error during GET request: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Head method (HEAD) a resource
     *
     * @param array $options Array representing resource for head request.
     *
     * @return SimpleXMLElement status_code, response
     * @throws PrestaShopWebserviceException
     */
    public function head($options)
    {
        if (isset($options['url'])) {
            $url = $options['url'];
        } elseif (isset($options['resource'])) {
            $url = $this->url . '/api/' . $options['resource'];
            $url_params = array();
            if (isset($options['id'])) {
                $url .= '/' . $options['id'];
            }

            $params = array('filter', 'display', 'sort', 'limit');
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

        $request = $this->executeRequest($url, array(CURLOPT_CUSTOMREQUEST => 'HEAD', CURLOPT_NOBODY => true));
        $this->checkStatusCode($request);
        return $request['header'];
    }

    /**
     * Edit (PUT) a resource
     *
     * @param array $options Array representing resource to edit.
     *
     * @return SimpleXMLElement
     * @throws PrestaShopWebserviceException
     */
    public function edit($options)
    {
        $xml = '';
        if (isset($options['url'])) {
            $url = $options['url'];
        } elseif ((isset($options['resource'], $options['id']) || isset($options['url'])) && $options['putXml']) {
            $url = (isset($options['url']) ? $options['url'] :
                $this->url . '/api/' . $options['resource'] . '/' . $options['id']);
            $xml = $options['putXml'];
            if (isset($options['id_shop'])) {
                $url .= '&id_shop=' . $options['id_shop'];
            }
            if (isset($options['id_group_shop'])) {
                $url .= '&id_group_shop=' . $options['id_group_shop'];
            }
        } else {
            throw new PrestaShopWebserviceException('Bad parameters given');
        }

        $request = $this->executeRequest($url, array(CURLOPT_CUSTOMREQUEST => 'PUT', CURLOPT_POSTFIELDS => $xml));
        $this->checkStatusCode($request);
        return $this->parseXML($request['response']);
    }

    /**
     * Delete (DELETE) a resource.
     *
     * @param array $options Array representing resource to delete.
     *
     * @return bool
     * @throws PrestaShopWebserviceException
     */
    public function delete($options)
    {
        if (isset($options['url'])) {
            $url = $options['url'];
        } elseif (isset($options['resource']) && isset($options['id'])) {
            $url = (is_array($options['id']))
                ? $this->url . '/api/' . $options['resource'] . '/?id=[' . implode(',', $options['id']) . ']'
                : $this->url . '/api/' . $options['resource'] . '/' . $options['id'];
        } else {
            throw new PrestaShopWebserviceException('Bad parameters given');
        }

        if (isset($options['id_shop'])) {
            $url .= '&id_shop=' . $options['id_shop'];
        }
        if (isset($options['id_group_shop'])) {
            $url .= '&id_group_shop=' . $options['id_group_shop'];
        }

        $request = $this->executeRequest($url, array(CURLOPT_CUSTOMREQUEST => 'DELETE'));
        $this->checkStatusCode($request);
        return true;
    }
}

/**
 * @package PrestaShopWebservice
 */
class PrestaShopWebserviceExceptionqsdqsdqsdqsdqsd extends \Exception {}
