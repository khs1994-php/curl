# Curl SDK

[![GitHub stars](https://img.shields.io/github/stars/khs1994-php/curl.svg?style=social&label=Stars)](https://github.com/khs1994-php/curl) [![PHP from Packagist](https://img.shields.io/packagist/php-v/khs1994/curl.svg)](https://packagist.org/packages/khs1994/curl) [![GitHub (pre-)release](https://img.shields.io/github/release/khs1994-php/curl/all.svg)](https://github.com/khs1994-php/curl/releases) [![Build Status](https://travis-ci.org/khs1994-php/curl.svg?branch=master)](https://travis-ci.org/khs1994-php/curl) [![StyleCI](https://styleci.io/repos/116448226/shield?branch=master)](https://styleci.io/repos/116448226)

## Installation

To Use Curl SDK, simply:

```bash
$ composer require khs1994/curl
```

For latest commit version:

```bash
$ composer config minimum-stability dev

$ composer require khs1994/curl @dev
```

## Usage

```php
<?php

require_once __DIR__.'/vendor/autoload.php';

use Curl\Curl;

$curl = new Curl(null, true);

$url = 'https://www.khs1994.com';

$output = $curl->get($url);

var_dump($output);
```

## PHP CaaS

**Powered By [khs1994-docker/lnmp](https://github.com/khs1994-docker/lnmp)**

## CI/CD

* [Drone](https://www.khs1994.com/categories/CI/Drone/)

* [Travis CI](https://travis-ci.org/khs1994-php/curl)

* [Style CI](https://styleci.io/repos/116448226)

* [Aliyun CodePipeline](https://www.aliyun.com/product/codepipeline)

* [Tencent Cloud Continuous Integration](https://cloud.tencent.com/product/cci)

* [Docker Build Powered By Tencent Cloud Container Service](https://cloud.tencent.com/product/ccs)

* [Docker Build Powered By Docker Cloud](https://cloud.docker.com)

* [Docker Build Powered By Aliyun Container Service](https://www.aliyun.com/product/containerservice)
