# SDKName

[![GitHub stars](https://img.shields.io/github/stars/khs1994-php/curl.svg?style=social&label=Stars)](https://github.com/khs1994-php/curl) [![PHP from Packagist](https://img.shields.io/packagist/php-v/khs1994/curl.svg)](https://packagist.org/packages/khs1994/curl) [![GitHub (pre-)release](https://img.shields.io/github/release/khs1994-php/curl/all.svg)](https://github.com/khs1994-php/curl/releases)

# Usage

Exec `composer` command

```bash
$ composer require khs1994/curl:dev-master
```

Or edit `composer.json`

```json
{
    "require": {
        "khs1994/curl": "dev-master"
    }
}
```

```php
<?php

require_once "vendor/autoload.php";

use Curl\Curl;

$curl = new Curl();

$url = 'https://www.baidu.com';

$output = $curl->curl($url);

var_dump($output);
```
