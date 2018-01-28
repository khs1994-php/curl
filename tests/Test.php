<?php

require '../vendor/autoload.php';

use Curl\Curl;

$curl=new Curl();

echo $curl('http://www.baidu.com','a=1');

$output=$curl->get('https://www.khs1994.com');

var_dump($output);
