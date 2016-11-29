<?php

namespace CBSan\Tools;

/**
 * Ferramenta utilizada para efetuar chamadas http/https via curl.
 *
 * @author Cristian B. Santos <cristian.santos@bludata.com>
 */
class CurlClient
{
    /**
     * @var constante com metodos suportados pela classe
     */
    const ONLY_METHODS = ['GET','POST'];

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $uri;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var int
     */
    protected $timeout = 30;

    /**
     * @var array
     */
    protected $opt = [];

    /**
     * @var string
     */
    protected $response;

    /**
     * @var string
     */
    protected $info;

    /**
     * @var string
     */
    protected $method = 'GET';

    private function curl()
    {
        ini_set('default_socket_timeout', $this->getTimeout());

        if (in_array($this->getMethod(), ['POST', 'PUT'])) {
            if ($opt = $this->getOpt()) {
                $length = strlen($opt[CURLOPT_POSTFIELDS]);
                $this->addHeader(sprintf('Content-Length: %u', $length));
            }
        }

        if ($path = parse_url($this->getUrl())) {
            $this->addHeader(sprintf('Host: %s', $path['host']));
        }

        $this->addOpt(CURLOPT_RETURNTRANSFER, true)
             ->addOpt(CURLOPT_FOLLOWLOCATION, true)
             ->addOpt(CURLOPT_BINARYTRANSFER, true)
             ->addOpt(CURLOPT_HEADER, true)
             ->addOpt(CURLOPT_SSL_VERIFYPEER, false)
             ->addOpt(CURLOPT_SSL_VERIFYHOST, false)
             ->addOpt(CURLOPT_TIMEOUT, $this->getTimeout())
             ->addOpt(CURLOPT_URL, trim($this->getUrl().$this->getUri()))
             ->addOpt(CURLOPT_HTTPHEADER, $this->getHeaders());

        $curl = curl_init();

        curl_setopt_array($curl, $this->getOpt());

        return $curl;
    }

    public function exec()
    {
        if (!$curl = $this->curl()) {
            throw new \Exception('CURL não foi inicializado', 1);
        }

        $this->setResponse(curl_exec($curl))
             ->setInfo(curl_getinfo($curl));

        curl_close($curl);

        return $this;
    }

    public function post($post)
    {
        $this->setMethod('POST')
             ->addOpt(CURLOPT_POST, true)
             ->addOpt(CURLOPT_POSTFIELDS, $post);

        return $this;
    }

    private function addOpt($key, $value)
    {
        $this->opt[$key] = $value;

        return $this;
    }

    public function getOpt()
    {
        return $this->opt;
    }

    /**
     * Gets the value of url.
     *
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Sets the value of url.
     *
     * @param mixed $url the url
     *
     * @return self
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Gets the value of uri.
     *
     * @return mixed
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Sets the value of uri.
     *
     * @param mixed $uri the uri
     *
     * @return self
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * Gets the value of headers.
     *
     * @return mixed
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Sets the value of headers.
     *
     * @param mixed $headers the headers
     *
     * @return self
     */
    public function addHeader($header)
    {
        $this->headers[] = $header;

        return $this;
    }

    /**
     * Gets the value of timeout.
     *
     * @return mixed
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Sets the value of timeout.
     *
     * @param mixed $timeout the timeout
     *
     * @return self
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Gets the value of info.
     *
     * @return mixed
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Sets the value of info.
     *
     * @param mixed $info the info
     *
     * @return self
     */
    private function setInfo($info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Gets the value of response.
     *
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Sets the value of response.
     *
     * @param mixed $response the response
     *
     * @return self
     */
    private function setResponse($response)
    {
        $pos = strpos($response, "\r\n\r\n");

        $headers = explode("\r\n", substr($response, 0, $pos));
        $body = substr($response, $pos + 4);

        $obj = new \stdClass();
        $obj->headers = $headers;
        $obj->body = $body;

        $this->response = $obj;

        return $this;
    }


    public function getStatusCode()
    {
        if (!$info = $this->getInfo()) {
            return null;
        }

        return isset($info['http_code']) ? $info['http_code'] : null;
    }

    /**
     * Gets the value of method.
     *
     * @return mixed
     */
    public function getMethod()
    {
        return strtoupper($this->method);
    }

    /**
     * Sets the value of method.
     *
     * @param mixed $method the method
     *
     * @return self
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Sets the value of headers.
     *
     * @param mixed $headers the headers
     *
     * @return self
     */
    protected function setHeaders($headers)
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * Sets the value of opt.
     *
     * @param mixed $opt the opt
     *
     * @return self
     */
    protected function setOpt($opt)
    {
        $this->opt = $opt;

        return $this;
    }
}
