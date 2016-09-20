<?php

namespace CBSan\Tools;

/**
 * Ferramenta utilizada para efetuar chamadas http/https via curl
 *
 * @author Cristian B. Santos <cristian.santos@bludata.com>
 */
class CurlClient
{
	private $url;
	private $uri;
	private $headers = [];
	private $timeout = 30;
	private $opt = [];
	private $curlInit;
	private $response;
	private $info;

	public function __construct()
	{
		ini_set('default_socket_timeout', $this->getTimeout());

		$this->addOpt(CURLOPT_RETURNTRANSFER, true)
			 ->addOpt(CURLOPT_FOLLOWLOCATION, true)
			 ->addOpt(CURLOPT_BINARYTRANSFER, true)
			 ->addOpt(CURLOPT_HEADER, true)
			 ->addOpt(CURLOPT_SSL_VERIFYPEER, false)
			 ->addOpt(CURLOPT_SSL_VERIFYHOST, false);
	}

	public function curl()
	{
		$this->addOpt(CURLOPT_TIMEOUT, $this->getTimeout())
			 ->addOpt(CURLOPT_URL, trim($this->getUrl().$this->getUri()))
			 ->addOpt(CURLOPT_HTTPHEADER, $this->getHeaders());

		$this->curlInit = curl_init();

		curl_setopt_array($this->curlInit, $this->getOpt());

		return $this;
	}

	public function exec()
	{
		if (!$this->curlInit) {
			throw new \Exception("CURL não foi inicializado", 1);
			
		}

		$this->setResponse(curl_exec($this->curlInit))
			 ->setInfo(curl_getinfo($this->curlInit));

		curl_close($this->curlInit);

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

        $obj = new \stdClass;
        $obj->headers = $headers;
        $obj->body = $body;
        
        $this->response = $obj;

        return $this;
    }
}