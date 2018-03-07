<?php

namespace Curl\Error;

use Throwable;

class CurlError extends \Error
{
    protected $message;
    protected $code;

    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->message = $message;
        $this->code = $code;
    }

    public function getErrorAsJson()
    {
        return json_encode($this->getErrorAsArray());
    }

    public function getErrorAsArray()
    {
        return [
            'ret' => $this->code,
            'message' => $this->message,
        ];
    }
}
