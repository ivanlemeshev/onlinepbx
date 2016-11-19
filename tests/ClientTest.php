<?php

use IvanLemeshev\OnPBX\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    const DOMAIN = 'domain';

    const KEY = 'key';

    /**
     * @var Client
     */
    private $client;

    public function setUp()
    {
        $this->client = new Client(self::DOMAIN, self::KEY);

        parent::setUp();
    }

    public function testGet()
    {
        $result = $this->client->get('/history/search.json', []);

        $this->assertEquals($result['status'], 0);
        $this->assertEquals($result['comment'], 'not authenticated');
    }

    public function makeRequest()
    {
        $url = $result = $this->invokeMethod($this->client, 'getFullUrl', ['/history/search.json']);
        $result = $result = $this->invokeMethod($this->client, 'makeRequest', ['POST', $url, [], []]);

        $this->assertEquals($result['status'], 0);
        $this->assertEquals($result['comment'], 'not authenticated');
    }

    public function testFullUrl()
    {
        $url = '/any_url';
        $expected = 'api.onlinepbx.ru/' . self::DOMAIN . $url;
        $result = $result = $this->invokeMethod($this->client, 'getFullUrl', [$url]);

        $this->assertEquals($expected, $result);
    }

    public function testCreateHeaders()
    {
        $url = $result = $this->invokeMethod($this->client, 'getFullUrl', ['/history/search.json']);

        $date = date('r');
        $hashedContent = hash('md5', '123');

        $encodeString = 'POST' . PHP_EOL
            . $hashedContent . PHP_EOL
            . 'application/x-www-form-urlencoded' . PHP_EOL
            . $date . PHP_EOL
            . $url . PHP_EOL;

        $signature = base64_encode(hash_hmac('sha1', $encodeString, null, false));

        $expected = [
            'Date: ' . $date,
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded',
            'x-pbx-authentication: ' . null . ':' . $signature,
            'Content-MD5: ' . $hashedContent,
        ];

        $result = $result = $this->invokeMethod($this->client, 'createHeaders', [$url, '123', 'POST']);

        $this->assertEquals($expected, $result);
    }

    private function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
