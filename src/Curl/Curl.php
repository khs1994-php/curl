<?php

namespace Curl;

class Curl
{
    const VERSION = 'v18.03';

    const TIMEOUT = 20;

    public $url;

    public $ch;

    private $headers=[];

    public function __construct(string $url= null)
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

    public function setOptArray(array $array=[])
    {
        curl_setopt_array($this->ch, $array);
    }

    public function setUrl(string $url=null)
    {
        if (!$this->url) {
            $this->url=$url;
        }
        $this->setOpt(CURLOPT_URL, $this->url);
    }

    public function setHeader(string $name, string $value)
    {
        $this->headers[$name]=$value;
        $headers=[];
        foreach ($this->headers as $key => $value) {
            $headers[]=$name.':'.$value;
        }

        $this->setOpt(CURLOPT_HTTPHEADER, $headers);
    }

    public function setCA(string $ca)
    {
        $this->setOpt(CURLOPT_CAINFO, $ca);
    }

    // Support Docker Daemon TLS

    public function docker(string $ca, string $key, string $cert)
    {
        $this->setOpt(CURLOPT_SSL_VERIFYPEER, 1);
        $this->setOpt(CURLOPT_CAINFO, $ca);
        $this->setOpt(CURLOPT_SSLKEY, $key);
        $this->setOpt(CURLOPT_SSLCERT, $cert);
    }

    public function get(string $url=null, string $data = null)
    {
        if ($data) {
            $this->setUrl($url.'?'.$data);
        } else {
            $this->setUrl($url);
        }

        $this->setHeader('content-type', 'application/x-www-form-urlencoded;charset=UTF-8');

        return $this->exec();
    }

    public function post(string $url=null, string $data = null)
    {
        $this->setUrl($url);
        $this->setHeader('content-type', 'application/x-www-form-urlencoded;charset=UTF-8');
        $this->setOpt(CURLOPT_POST, 1);
        $this->setOpt(CURLOPT_POSTFIELDS, $data);

        return $this->exec();
    }

    public function delete(string $url)
    {
        # code...
    }

    public function patch(string $url)
    {
        # code...
    }

    public function put(string $url)
    {
    }

    public function exec()
    {
        $output = curl_exec($this->ch);

        $errorCode=curl_errno($this->ch);
        $errorMssage=curl_error($this->ch);

        curl_close($this->ch);

        if ($errorCode) {
            return [$errorCode=>$errorMssage];
        }

        return $output;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    // 魔术方法，把对象当做函数调用

    public function __invoke(string $url=null, string $data=null)
    {
        return $this->get($url, $data);
    }

    // 魔术方法，把对象当字符串，该方法不接受参数

    public function __toString()
    {
        return $this->get($this->url);
    }
}
