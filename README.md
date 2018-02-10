# Curl SDK

[![GitHub stars](https://img.shields.io/github/stars/khs1994-php/curl.svg?style=social&label=Stars)](https://github.com/khs1994-php/curl) [![PHP from Packagist](https://img.shields.io/packagist/php-v/khs1994/curl.svg)](https://packagist.org/packages/khs1994/curl) [![GitHub (pre-)release](https://img.shields.io/github/release/khs1994-php/curl/all.svg)](https://github.com/khs1994-php/curl/releases)

## Installation

```bash
$ composer require khs1994/curl @dev
```

## Usage

```php
<?php

require_once __DIR__.'/vendor/autoload.php';

use Curl\Curl;

$curl = new Curl();

$url = 'https://www.baidu.com';

$output = $curl->get($url);

var_dump($output);
```
