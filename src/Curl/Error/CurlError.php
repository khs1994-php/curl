<?php

namespace Curl\Error;

use Throwable;

class CurlError extends \Error
{
    public $message;
    public $code;

    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->message = $message;
        $this->code = $code;
    }
}