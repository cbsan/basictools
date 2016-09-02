<?php

namespace CBSan\Tools;

/**
 * Representa a comunicação com o WSDL através do SoapClient.
 *
 * @author Cristian B. Santos <cristian.santos@bludata.com>
 */
class SoapClient
{
    protected $host;
    protected $options;
    protected $client;
    protected $service;
    protected $request = [];

    /**
     * Cria um instancia para comunicação com WSDL utilizando SoapClient.
     *
     * @param string $host    Parametro obrigatório, endereço do WSDL
     * @param array  $options Parametro opcional, referente aos options que serão utilizados
     */
    public function __construct($host, array $options = [])
    {
        $this->setHost($host.'?wsdl')
             ->setOptions($options);
    }

    /**
     * Retorna o host do WSDL.
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Recebe o endereço do WSDL.
     *
     * @param string $host
     *
     * @return self
     */
    protected function setHost($host)
    {
        $host = substr($host, 0, strpos($host, '?'));

        $this->host = $host;

        return $this;
    }

    /**
     * Efetua a conexão inicial com o WSDL.
     *
     * @return self
     */
    public function connect()
    {
        if (!$host = $this->getHost()) {
            throw new \Exception('HOST do WSDL não foi informado', 1);
        }

        $host = sprintf('%s?wsdl', $host);

        $client = new \SoapClient($host,
                                  $this->getOptions());

        $this->setClient($client);

        return $this;
    }

    /**
     * Faz a chamada para o WSDL, de acordo com os parametros pré-configurados para o serviço.
     *
     * @return SoapClient::__soapCall
     */
    public function call()
    {
        if (!$this->getHost()) {
            throw new \Exception('HOST do WSDL não foi informado', 1);
        }

        if (!$this->getService()) {
            throw new \Exception('Serviço não informado', 1);
        }

        if (!$this->getRequest()) {
            throw new \Exception('Request não informado', 1);
        }

        if (!$this->getClient()) {
            if (!$this->connect()) {
                throw new \Exception('Não foi possivel conectar ao WSDL', 1);
            }
        }

        try {
            $client = $this->getClient();

            return $client->__soapCall($this->getService(),
                                       $this->getRequest());
        } catch (\SoapFault $e) {
            throw new \Exception($e->faultstring, 1);
        }
    }

    /**
     * Retorna os options utilizados no SoapClient.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Recebe os options que serão utilizados no SoapClient.
     *
     * @param array $options
     *
     * @return self
     */
    protected function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Retorna dados da requisição.
     *
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Recebe dados para montar o request.
     *
     * @param array
     *
     * @return self
     */
    public function setRequest(array $request)
    {
        array_push($this->request, $request);

        return $this;
    }

    /**
     * Retorna o serviço utilizado.
     *
     * @return mixed
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Recebe o serviço que sera utilizado no WSDL.
     *
     * @param string $service
     *
     * @return self
     */
    public function setService($service)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * Retorna a instancia do SoapClient que foi incializada.
     *
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Recebe o SoapClient que sera utilizado.
     *
     * @param SoapClient $client
     *
     * @return self
     */
    protected function setClient(\SoapClient $client)
    {
        $this->client = $client;

        return $this;
    }
}
