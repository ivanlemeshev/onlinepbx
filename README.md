# onlinePBX HTTP API client

Requirements:

* PHP >= 5.6
* PHP Curl extension

Example:

```php
$client = new \IvanLemeshev\OnPBX\Client('domain', 'key');
$history = $client->get('/history/search.json', ['billsec_from' => 30]);
```
