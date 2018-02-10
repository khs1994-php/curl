<?php

namespace Curl;

class Curl
{
    const VERSION = 'v18.03';

    const TIMEOUT = 20;

    public $url;

    public $ch;

    private $headers = [];

    public function __construct(string $url = null)
    {
        $this->ch = curl_init();
        $this->setUrl($url);
        $this->setOpt(CURLOPT_RETURNTRANSFER, 1);
        $this->setTimeout(self::TIMEOUT);
    }

    public function setTimeout(int $sec)
    {
        $this->setOpt(CURLOPT_TIMEOUT, $sec);
    }

    public function setOpt(string $opt, $value)
    {
        curl_setopt($this->ch, $opt, $value);
    }

    public function setOptArray(array $array = [])
    {
        curl_setopt_array($this->ch, $array);
    }

    public function setUrl(string $url = null)
    {
        if ($url) {
            $this->setOpt(CURLOPT_URL, $url);
        }
    }

    public function setHeader(string $name, string $value)
    {
        $this->headers[$name] = $value;
        $headers = [];
        foreach ($this->headers as $key => $value) {
            $headers[] = $name.':'.$value;
        }

        $this->setOpt(CURLOPT_HTTPHEADER, $headers);
    }

    public function setCA(string $ca)
    {
        $this->setOpt(CURLOPT_CAINFO, $ca);
    }

    // Support Docker Daemon TLS

    public function docker($cert_path)
    {
        $this->setOpt(CURLOPT_SSL_VERIFYPEER, 1);
        $this->setOpt(CURLOPT_CAINFO, $cert_path.'/ca.pem');
        $this->setOpt(CURLOPT_SSLKEY, $cert_path.'/key.pem');
        $this->setOpt(CURLOPT_SSLCERT, $cert_path.'/cert.pem');
    }

    public function get(string $url = null, string $data = null, array $header = [])
    {
        $url = $data ? $url.'?'.$data : $url;
        $this->setUrl($url);
        if ($header) {
            foreach ($header as $key => $value) {
                $this->setHeader($key, $value);
            }
        }

        return $this->exec();
    }

    public function post(string $url = null, $data = null, array $header = [])
    {
        $this->setUrl($url);
        if ($header) {
            foreach ($header as $key => $value) {
                $this->setHeader($key, $value);
            }
        }
        $this->setOpt(CURLOPT_POST, 1);
        $this->setOpt(CURLOPT_POSTFIELDS, $data);

        return $this->exec();
    }

    public function delete(string $url, string $data)
    {
        $this->setUrl($url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'DELETE');
        $this->setOpt(CURLOPT_POSTFIELDS, $data);
        return $this->exec();
    }

    public function patch(string $url, string $data)
    {
        $this->setUrl($url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'PATCH');
        $this->setOpt(CURLOPT_POSTFIELDS, $data);
        return $this->exec();
    }

    public function put(string $url, string $data)
    {
        $this->setUrl($url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'PUT');
        $this->setOpt(CURLOPT_POSTFIELDS, $data);
        return $this->exec();
    }

    public function exec()
    {
        $output = curl_exec($this->ch);
        $errorCode = curl_errno($this->ch);
        $errorMssage = curl_error($this->ch);

        if ($errorCode) {
            throw new \Error($errorMssage, $errorCode);
        }

        return $output;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    // 魔术方法，把对象当做函数调用

    public function __invoke(string $url = null, string $data = null)
    {
        return $this->get($url, $data);
    }

    // 魔术方法，把对象当字符串，该方法不接受参数

    public function __toString()
    {
        return $this->get($this->url);
    }

    public function __destruct()
    {
        curl_close($this->ch);
    }
}
