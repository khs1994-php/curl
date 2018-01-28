<?php

namespace Curl;

class Curl
{
    public $url;

    public $ch;

    public function __construct(string $url= null)
    {
        $this->ch = curl_init();
        $this->setUrl($url);
        $this->setOpt(CURLOPT_RETURNTRANSFER, 1);
    }

    public function setOpt(string $opt, $value)
    {
        curl_setopt($this->ch, $opt, $value);
    }

    public function setUrl(string $url=null)
    {
        $this->url=$url;
        $this->setOpt(CURLOPT_URL, $this->url);
    }

    public function setHeader(array $header=[])
    {
        $this->setOpt(CURLOPT_HTTPHEADER, $header);
    }

    public function get(string $url=null, string $data = null)
    {
        $header = ['content-type: application/x-www-form-urlencoded;charset=UTF-8'];

        $this->setUrl($url.'?'.$data);
        $this->setHeader($header);

        return $this->exec();
    }

    public function post(string $url=null, string $data = null)
    {
        $header = ['content-type: application/x-www-form-urlencoded;charset=UTF-8'];

        $this->setUrl($url);
        $this->setHeader($header);
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

        return $output;
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

    // 析构函数

    public function __destruct()
    {
        curl_close($this->ch);
    }
}
