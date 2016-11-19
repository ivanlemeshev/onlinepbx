# onlinePBX HTTP API client

[![Build Status](https://img.shields.io/travis/ivanlemeshev/onlinepbx.png)](https://img.shields.io/travis/ivanlemeshev/onlinepbx.png)
[![Latest Stable Version](https://img.shields.io/github/release/ivanlemeshev/onlinepbx.png)](https://img.shields.io/github/release/ivanlemeshev/onlinepbx.png)
[![Total Downloads](https://img.shields.io/packagist/dt/ivanlemeshev/onlinepbx.png)](https://img.shields.io/packagist/dt/ivanlemeshev/onlinepbx.png)
[![Latest Unstable Version](https://poser.pugx.org/ivanlemeshev/onlinepbx/v/unstable?format=flat-square)](https://packagist.org/packages/ivanlemeshev/onlinepbx)
[![License](https://img.shields.io/github/license/ivanlemeshev/onlinepbx.png)](https://img.shields.io/github/license/ivanlemeshev/onlinepbx.png)

Requirements:

* PHP >= 5.6
* PHP Curl extension

Example:

```php
$client = new \IvanLemeshev\OnPBX\Client('domain', 'key');
$history = $client->get('/history/search.json', ['billsec_from' => 30]);
```
