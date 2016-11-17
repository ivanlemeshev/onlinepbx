<?php

namespace IvanLemeshev\OnPBX;

class Client
{
    /**
     * Request method
     */
    const METHOD = 'POST';

    /**
     * OnPBX API domain
     */
    const API_DOMAIN = 'api.onlinepbx.ru';

    /**
     * Accept header
     */
    const HEADER_ACCEPT = 'application/json';

    /**
     * Content type header
     */
    const HEADER_CONTENT_TYPE = 'application/x-www-form-urlencoded';

    /**
     * @var string
     */
    private $domain;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $authKey;

    /**
     * @var string
     */
    private $authKeyId;

    /**
     * Client constructor.
     *
     * @param string $domain
     * @param string $apiKey
     *
     * @throws \Exception
     */
    public function __construct($domain, $apiKey)
    {
        $this->domain = $domain;
        $this->apiKey = $apiKey;

        $this->auth();
    }

    /**
     * Make HTTP request and return a response.
     *
     * @param string $url
     * @param array $body
     *
     * @return array|bool
     */
    public function get($url, $body = [])
    {
        $url = $this->getFullUrl($url);

        if (is_array($body)) {
            foreach ($body as $key => $value) {
                if (is_string($key) && preg_match('/^@(.+)/', $value, $m)) {
                    $body[$key] = [
                        'name' => basename($m[1]),
                        'data' => base64_encode(file_get_contents($m[1])),
                    ];
                }
            }
        }

        $post = http_build_query($body);
        $headers = $this->createHeaders($url, $post, self::METHOD);

        return $this->makeRequest(self::METHOD, $url, $post, $headers);
    }

    /**
     * Make authenticated HTTP requests and set API key and API key ID.
     */
    private function auth()
    {
        $url = $this->getFullUrl('/auth.json');

        $data = ['auth_key' => $this->apiKey];

        $response = $this->makeRequest(self::METHOD, $url, $data);

        if ($response['comment'] == 'not authenticated') {
            $data['new'] = 'true';
            $response = $this->makeRequest(self::METHOD, $url, $data);
        }

        if (isset($response['data']['key']) && isset($response['data']['key_id'])) {
            $this->authKey = $response['data']['key'];
            $this->authKeyId = $response['data']['key_id'];
        }
    }

    /**
     * Make HTTP request.
     *
     * @param string $method
     * @param string $url
     * @param $post
     * @param array $headers
     *
     * @return bool|mixed
     */
    private function makeRequest($method, $url, $post, $headers = [])
    {
        $ch = curl_init("https://{$url}");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        $response = json_decode(curl_exec($ch), true);

        if ($response) {
            return $response;
        }

        return false;
    }

    /**
     * Generate full URL.
     *
     * @param string $url
     *
     * @return string
     */
    private function getFullUrl($url)
    {
        return self::API_DOMAIN . '/' . $this->domain . $url;
    }

    /**
     * Create HTTP request headers.
     *
     * @param string $url
     * @param string $post
     * @param string $method
     *
     * @return array
     */
    private function createHeaders($url, $post, $method)
    {
        $date = date('r');
        $hashedContent = hash('md5', $post);

        $encodeString = $method . PHP_EOL
                      . $hashedContent . PHP_EOL
                      . self::HEADER_CONTENT_TYPE . PHP_EOL
                      . $date . PHP_EOL
                      . $url . PHP_EOL;

        $signature = base64_encode(hash_hmac('sha1', $encodeString, $this->authKey, false));

        return [
            'Date: ' . $date,
            'Accept: ' . self::HEADER_ACCEPT,
            'Content-Type: ' . self::HEADER_CONTENT_TYPE,
            'x-pbx-authentication: '. $this->authKeyId . ':' . $signature,
            'Content-MD5: ' . $hashedContent,
        ];
    }
}
