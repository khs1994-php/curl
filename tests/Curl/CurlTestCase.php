<?php

declare(strict_types=1);

namespace Curl\Tests;

use Curl\Curl;
use PHPUnit\Framework\TestCase;

class CurlTestCase extends TestCase
{
    private static $curl;

    public static function curl()
    {
        if (!(self::$curl instanceof Curl)) {
            self::$curl = new Curl(null, false);
        }

        return self::$curl;
    }
}
