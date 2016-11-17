# onlinePBX HTTP API client

Example:

```php
$client = new \IvanLemeshev\OnPBX\Client('domain', 'key');
$history = $client->get('/history/search.json', ['billsec_from' => 30]);
```
