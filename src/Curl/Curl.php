<?php

namespace Curl;

use Curl\Error\CurlError;

class Curl
{
    const VERSION = 'v18.06';

    const TIMEOUT = 60;

    public $url = null;

    public $ch;

    private $headers = [];

    /**
     * 构造函数.
     *
     * @param string|null $url
     * @param bool        $http2
     */
    public function __construct(string $url = null, bool $http2 = false)
    {
        $this->ch = curl_init();
        $this->setUrl($url);
        /*
         * 获取的信息以字符串返回，而不是直接输出
         */
        $this->setOpt(CURLOPT_RETURNTRANSFER, 1);
        $this->setTimeout(self::TIMEOUT);
        $this->setUserAgent(null);
        /*
         * 根据服务器返回 HTTP 头中的 "Location: " 重定向
         */
        $this->setOpt(CURLOPT_FOLLOWLOCATION, 1);
        /*
         * http2
         *
         * @since 7.0.7
         */
        if ($http2) {
            $this->setOpt(CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
        }
    }

    /**
     * 启用 HTTP2.
     *
     * @since 7.0.7
     */
    public function enableHttp2(): void
    {
        $this->setOpt(CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
    }

    /**
     * 设置 htpasswd.
     *
     * @param string $username
     * @param string $password
     */
    public function setHtpasswd(string $username, string $password): void
    {
        $this->setOpt(CURLOPT_USERPWD, $username.':'.$password);
    }

    /**
     * 超时设置.
     *
     * @param int $sec
     */
    public function setTimeout(int $sec): void
    {
        $this->setOpt(CURLOPT_TIMEOUT, $sec);
    }

    /**
     * 设置 User-Agent.
     *
     * @param string $userAgent
     */
    public function setUserAgent(?string $userAgent): void
    {
        /*
         *  在 HTTP 请求中包含一个 "User-Agent: " 头的字符串。
         */
        if ($userAgent) {
            $this->setOpt(CURLOPT_USERAGENT, $userAgent);
        } else {
            $userAgent = 'KHS1994-Curl/'.self::VERSION.' (https://github.com/khs1994-php/curl)';
            $userAgent .= ' PHP/'.PHP_VERSION;
            $curlVersionArray = curl_version();
            $userAgent .= ' Curl/'.$curlVersionArray['version'];
            $this->setUserAgent($userAgent);
        }
    }

    /**
     * 设置 curl 选项.
     *
     * @param string $opt
     * @param        $value
     */
    public function setOpt(string $opt, $value): void
    {
        curl_setopt($this->ch, $opt, $value);
    }

    /**
     * 以数组形式设置 curl.
     *
     * @param array $array
     */
    public function setOptArray(array $array = []): void
    {
        curl_setopt_array($this->ch, $array);
    }

    /**
     * 设置 URL.
     *
     * @param string|null $url
     */
    public function setUrl(string $url = null): void
    {
        if ($url) {
            $this->url = $url;
            $this->setOpt(CURLOPT_URL, $url);
        }
    }

    /**
     * 设置 header.
     *
     * @param string $name
     * @param string $value
     */
    public function setHeader(string $name, string $value): void
    {
        $this->headers[$name] = $value;
        $headers = [];
        foreach ($this->headers as $key => $value) {
            $headers[] = $key.':'.$value;
        }

        $this->setOpt(CURLOPT_HTTPHEADER, $headers);
    }

    /**
     * 设置 CA 根证书.
     *
     * @param string $ca
     */
    public function setCAInfo(string $ca): void
    {
        $this->setOpt(CURLOPT_CAINFO, $ca);
    }

    /**
     * 原生支持 Docker TLS.
     *
     * Support Docker Daemon TLS
     *
     * @param string $cert_path
     */
    public function docker(string $cert_path): void
    {
        /*
         * 下面两个参数为默认值，安全原因，严禁修改此项
         */
        $this->setOpt(CURLOPT_SSL_VERIFYPEER, 1);
        $this->setOpt(CURLOPT_SSL_VERIFYHOST, 2);
        $this->setOpt(CURLOPT_SSL_VERIFYSTATUS, 1);
        $this->setOpt(CURLOPT_CAINFO, $cert_path.'/ca.pem');
        /*
         * 一个包含 SSL 私钥的文件名
         */
        $this->setOpt(CURLOPT_SSLKEY, $cert_path.'/key.pem');
        /*
         * 一个包含 PEM 格式证书的文件名
         */
        $this->setOpt(CURLOPT_SSLCERT, $cert_path.'/cert.pem');
    }

    public function cookie(): void
    {
    }

    /**
     * get 方法.
     *
     * @param string|null $url
     * @param             $data
     * @param array       $header
     *
     * @throws CurlError
     *
     * @return mixed
     */
    public function get(string $url = null, $data = null, array $header = [])
    {
        $url = $data ? $url.'?'.$data : $url;
        $this->url = $url;
        $this->setUrl($url);
        if ($header) {
            foreach ($header as $key => $value) {
                $this->setHeader($key, $value);
            }
        }
        
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'GET');

        return $this->exec();
    }

    /**
     * post 方法.
     *
     * @param string|null $url
     * @param             $data
     * @param array       $header
     *
     * @throws CurlError
     *
     * @return mixed
     */
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

    /**
     * delete 方法.
     *
     * @param string $url
     * @param        $data
     *
     * @throws CurlError
     *
     * @return mixed
     */
    public function delete(string $url, $data = null)
    {
        $this->setUrl($url);
        /*
         * HTTP 请求时，使用自定义的 Method 来代替 "GET" 或 "HEAD"。对 "DELETE" 或者其他更隐蔽的 HTTP 请求有用
         */
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'DELETE');
        $this->setOpt(CURLOPT_POSTFIELDS, $data);

        return $this->exec();
    }

    /**
     * patch 方法.
     *
     * @param string $url
     * @param        $data
     *
     * @throws CurlError
     *
     * @return mixed
     */
    public function patch(string $url, $data = null)
    {
        $this->setUrl($url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'PATCH');
        $this->setOpt(CURLOPT_POSTFIELDS, $data);

        return $this->exec();
    }

    /**
     * put 方法.
     *
     * @param string $url
     * @param        $data
     *
     * @throws CurlError
     *
     * @return mixed
     */
    public function put(string $url, $data = null)
    {
        $this->setUrl($url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'PUT');
        $this->setOpt(CURLOPT_POSTFIELDS, $data);

        return $this->exec();
    }

    /**
     * @throws CurlError
     *
     * @return mixed
     */
    public function exec()
    {
        $output = curl_exec($this->ch);
        $errorCode = curl_errno($this->ch);
        $errorMessage = curl_error($this->ch);

        if ($errorCode) {
            throw new CurlError($errorMessage, $errorCode);
        }

        return $output;
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->$name;
    }

    /**
     * 魔术方法，把对象当字符串，该方法不接受参数.
     *
     * @throws CurlError
     */
    public function __toString()
    {
        return $this->get($this->url);
    }

    /**
     * 析构函数.
     */
    public function __destruct()
    {
        if (is_resource($this->ch)) {
            curl_close($this->ch);
        }
    }
}
