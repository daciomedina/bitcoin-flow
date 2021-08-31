<?php

class CurlApi
{
    /**
     * @var string API Base Endpoint
     */
    private $baseURL;

    /**
     * @var string API Key to authenticate in API.
     */
    private $apiKey;


    /**
     * @var int API timeout to reach to cancell request if no response is received.
     */
    private $apiTimeout;

    /**
     * @var bool 
     */
    private $is_debug;

    private $endpoint;
    /**
     * Get base URL from Module configuration and API Key.
     */
    public function __construct(String $url)
    {
        $this->endpoint = $url;
        $this->apiTimeout = 10000;
    }

    /**
     * Call API
     * @param string $entityName Entity name such as orders or other entity.
     * @param string $method Verb to use on request (GET, POST, PUT, DELETE, OPTIONS, ...)
     * @param string $url Relative path to method without base url.
     * @param array|null $data null if no data to send (GET, OPTIONS) or array with data to send.
     * @param int $identifier Identifier of request to trace in logs. For example the order id could be sent as identifier.
     * @return array Array with the status code returned by the api, a bool with success/fail and a message with error details.
     */
    public function callAPI(string $entityName, string $method, array $data = null, int $identifier = 0): array
    {
        $statusCode = 200;
        $success = true;
        $message = '';
        $response = null;
        
        try {
            // Build full URL
            $endpoint = $this->endpoint;
            
            
            // Build CURL request
            $curl = curl_init();

            switch (strtoupper($method)) {
                case "GET":
                    if ($data) {
                        $endpoint = sprintf("%s?%s", $endpoint, http_build_query($data));
                    }
                    break;
                case "POST":
                    curl_setopt($curl, CURLOPT_POST, 1);
                    if ($data) {
                        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                    }
                    break;
                case "PUT":
                    //curl_setopt($curl, CURLOPT_PUT, 1);
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                    if ($data) {
                        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                    }
                    break;
                default:
                    $success = false;
                    $message = "Call API: Ko. Method: {$method}. Resource Name: {$entityName}. Identifier: {$identifier}. URL: {$endpoint}. Unsupported method called.";
                    $statusCode = 400;
                    break;
            }
            if ($success) {
                // Set CURL Options
                curl_setopt($curl, CURLOPT_URL, $endpoint);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_TIMEOUT, $this->apiTimeout);
                curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // Only for dev
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // Only for dev
                // Attach authentication headers
                $headers = $this->buildRequestHeaders();
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

                // Execute request and get response
                $response = curl_exec($curl);

                if ($response === false) {
                    $message = curl_error($curl);
                }

                // Get Http code
                $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                if ($statusCode !== 200){
                    $message ="API returned a non OK response code: {$statusCode}.";
                }
                

                curl_close($curl);
                $requestId = array_key_exists(1, $headers) ? $headers[1] : '';
            }
        } catch (Exception $e) {
            $success = false;
            $message = "Call API: Ko. Method: {$method}. Resource Name: {$entityName}. Identifier: {$identifier}. URL: {$endpoint}. Api Client Error Exception details:.";
            $statusCode = 400;
        } catch (Throwable $throwable) {
            $success = false;
            $message = "Call API: Ko. Method: {$method}. URL: {$endpoint}. Exception details: {$throwable->getMessage()}.";
            $statusCode = 500;
        } finally {
            $result = array(
                'statusCode' => $statusCode,
                'success' => $success,
                'message' => $message,
                'response' => $response
            );
        }
        return $result;
    }

    /**
     * Build authentication headers and content type header and return as an array.
     * @return array
     */
    private function buildRequestHeaders(): array
    {
        try {
            $uniqueRequestId = md5(uniqid(rand(), true));
            $headers = array(
                'Content-Type: application/json'
            );
        } catch (Throwable $throwable) {
            $headers = array();
        }
        return $headers;
    }
}