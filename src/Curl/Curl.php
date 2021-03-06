<?php

declare(strict_types=1);

namespace Curl;

use Exception;

class Curl
{
    const VERSION = 'v18.06';

    const TIMEOUT = 60;

    public $url = null;

    public $ch;

    public $timeout;

    private $info;

    private $headers = [];

    private $common_header = [];

    private $responseHeaders;

    private $base_path;

    /**
     * 构造函数.
     *
     * @param string|null $url
     * @param bool        $http2
     * @param array       $common_header
     */
    public function __construct(string $url = null, bool $http2 = false, array $common_header = [])
    {
        $this->ch = curl_init();
        $this->setUrl($url);

        // 获取的信息以字符串返回，而不是直接输出

        $this->setOpt(CURLOPT_RETURNTRANSFER, 1);
        $this->setTimeout(self::TIMEOUT);
        $this->setUserAgent(null);

        // 根据服务器返回 HTTP 头中的 "Location: " 重定向

        $this->setOpt(CURLOPT_FOLLOWLOCATION, 1);
        // 获取请求头
        $this->setOpt(CURLINFO_HEADER_OUT, true);
        // 获取响应头
        $this->setOpt(CURLOPT_HEADER, 1);
        /*
         * http2
         *
         * @since 7.0.7
         */
        if ($http2) {
            $this->setOpt(CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
        }

        if ($common_header) {
            $this->common_header = $common_header;

            foreach ($common_header as $k => $v) {
                $this->headers[$k] = $v;
            }
        }
    }

    /**
     * @param mixed $base_path
     *
     * @return Curl
     */
    public function setBasePath($base_path)
    {
        $this->base_path = $base_path;

        return $this;
    }

    /**
     * 启用 HTTP2.
     *
     * @since 7.0.7
     */
    public function enableHttp2()
    {
        $this->setOpt(CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);

        return $this;
    }

    /**
     * 设置 htpasswd.
     *
     * @param string $username
     * @param string $password
     *
     * @return Curl
     */
    public function setHtpasswd(string $username, string $password)
    {
        $this->setOpt(CURLOPT_USERPWD, $username.':'.$password);

        return $this;
    }

    /**
     * 超时设置.
     *
     * @param int $sec
     *
     * @return Curl
     */
    public function setTimeout(int $sec)
    {
        $this->setOpt(CURLOPT_TIMEOUT, $sec);

        $this->timeout = $sec;

        return $this;
    }

    /**
     * 设置 User-Agent.
     *
     * @param string $userAgent
     *
     * @return Curl
     */
    public function setUserAgent(?string $userAgent)
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

        return $this;
    }

    /**
     * 设置 curl 选项.
     *
     * @param int $opt
     * @param     $value
     *
     * @return Curl
     */
    public function setOpt(int $opt, $value)
    {
        curl_setopt($this->ch, $opt, $value);

        return $this;
    }

    /**
     * 以数组形式设置 curl.
     *
     * @param array $array
     *
     * @return Curl
     */
    public function setOptArray(array $array = [])
    {
        curl_setopt_array($this->ch, $array);

        return $this;
    }

    /**
     * 设置 URL.
     *
     * @param string|null $url
     *
     * @return Curl
     */
    public function setUrl(string $url = null)
    {
        if ($url) {
            $this->url = $url;
            $this->setOpt(CURLOPT_URL, $this->base_path.$url);
        }

        return $this;
    }

    /**
     * 设置 header.
     *
     * @param string $name
     * @param string $value
     *
     * @return Curl
     */
    public function setHeader(?string $name, ?string $value)
    {
        $headers = [];

        $this->headers[$name] = $value;

        foreach ($this->headers as $key => $value) {
            $headers[] = $key.':'.$value;
        }

        $this->setOpt(CURLOPT_HTTPHEADER, $headers);

        return $this;
    }

    /**
     * 设置 CA 根证书.
     *
     * @param string $ca
     *
     * @return Curl
     */
    public function setCAInfo(string $ca)
    {
        $this->setOpt(CURLOPT_CAINFO, $ca);

        return $this;
    }

    /**
     * 原生支持 Docker TLS.
     *
     * Support Docker Daemon TLS
     *
     * @param string $cert_path ,must include ca.pem key.pem cert.pem
     *
     * @return Curl
     */
    public function docker(string $cert_path)
    {
        // 下面两个参数为默认值，安全原因，严禁修改此项
        $this->setOpt(CURLOPT_SSL_VERIFYPEER, 1);
        $this->setOpt(CURLOPT_SSL_VERIFYHOST, 2);
        $this->setOpt(CURLOPT_SSL_VERIFYSTATUS, 0);
        $this->setOpt(CURLOPT_CAINFO, $cert_path.'/ca.pem');

        // 一个包含 SSL 私钥的文件名
        $this->setOpt(CURLOPT_SSLKEY, $cert_path.'/key.pem');

        // 一个包含 PEM 格式证书的文件名
        $this->setOpt(CURLOPT_SSLCERT, $cert_path.'/cert.pem');

        return $this;
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
     * @return mixed
     *
     * @throws Exception
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
        } else {
            $this->setHeader(null, null);
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
     * @return mixed
     *
     * @throws Exception
     */
    public function post(string $url = null, $data = null, array $header = [])
    {
        $this->setUrl($url);
        if ($header) {
            foreach ($header as $key => $value) {
                $this->setHeader($key, $value);
            }
        } else {
            $this->setHeader(null, null);
        }

        $this->setOpt(CURLOPT_POST, 1);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'POST');
        $this->setOpt(CURLOPT_POSTFIELDS, $data);

        return $this->exec();
    }

    /**
     * delete 方法.
     *
     * @param string $url
     * @param        $data
     * @param array  $header
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function delete(string $url, $data = null, array $header = [])
    {
        $this->setUrl($url);

        // HTTP 请求时，使用自定义的 Method 来代替 "GET" 或 "HEAD"。对 "DELETE" 或者其他更隐蔽的 HTTP 请求有用
        if ($header) {
            foreach ($header as $key => $value) {
                $this->setHeader($key, $value);
            }
        } else {
            $this->setHeader(null, null);
        }

        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'DELETE');
        $this->setOpt(CURLOPT_POSTFIELDS, $data);
        $this->setHeader(null, null);

        return $this->exec();
    }

    /**
     * patch 方法.
     *
     * @param string $url
     * @param        $data
     * @param array  $header
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function patch(string $url, $data = null, array $header = [])
    {
        $this->setUrl($url);
        if ($header) {
            foreach ($header as $key => $value) {
                $this->setHeader($key, $value);
            }
        } else {
            $this->setHeader(null, null);
        }

        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'PATCH');
        $this->setOpt(CURLOPT_POSTFIELDS, $data);
        $this->setHeader(null, null);

        return $this->exec();
    }

    /**
     * put 方法.
     *
     * @param string $url
     * @param        $data
     * @param array  $header
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function put(string $url, $data = null, array $header = [])
    {
        $this->setUrl($url);
        if ($header) {
            foreach ($header as $key => $value) {
                $this->setHeader($key, $value);
            }
        } else {
            $this->setHeader(null, null);
        }

        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'PUT');
        $this->setOpt(CURLOPT_POSTFIELDS, $data);
        $this->setHeader(null, null);

        return $this->exec();
    }

    /**
     * @return mixed
     *
     * @throws Exception
     */
    public function exec()
    {
        $output = curl_exec($this->ch);

        // 请求之后清空 header
        $this->headers = [];

        // 载入初始化的 common header
        $common_header = $this->common_header;

        if ($common_header) {
            foreach ($common_header as $k => $v) {
                $this->headers[$k] = $v;
            }
        }

        $this->info = curl_getinfo($this->ch);
        $errorCode = curl_errno($this->ch);
        $errorMessage = curl_error($this->ch);

        if ($errorCode) {
            throw new Exception($errorMessage, $errorCode);
        }

        $header_size = curl_getinfo($this->ch, CURLINFO_HEADER_SIZE);

        $this->responseHeaders = trim(substr($output, 0, $header_size));

        return trim(substr($output, $header_size));
    }

    public function getInfo()
    {
        return $this->info;
    }

    public function getCode()
    {
        return curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
    }

    public function getRequestHeaders()
    {
        return trim(curl_getinfo($this->ch, CURLINFO_HEADER_OUT));
    }

    public function getResponseHeaders()
    {
        return $this->responseHeaders;
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
     * @return mixed
     *
     * @throws Exception
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
