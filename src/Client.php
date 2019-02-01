<?php

namespace ChrGriffin\WaveApi;

use GuzzleHttp\Client as GuzzleClient;
use ChrGriffin\WaveApi\Exceptions\{ InvalidArgumentException };

class Client
{
    /**
     * @const array
     */
    const REQUEST_PARAMS = [
        'key',
        'format',
        'viewportwidth',
        'evaldelay',
        'reporttype',
        'username',
        'password'
    ];

    /**
     * @const array
     */
    const FORMATS = [
        'json', 'xml'
    ];

    /**
     * @const array
     */
    const REPORT_TYPES = [
        1, 2, 3
    ];

    /**
     * @var GuzzleClient|mixed
     */
    protected $client;

    /**
     * @return Client|mixed
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param GuzzleClient|mixed $client
     * @return $this
     */
    public function setClient($client) : Client
    {
        $this->client = $client;
        return $this;
    }

    /**
     * @var string
     */
    protected $key;

    /**
     * @return string|null
     */
    public function getKey() : ?string
    {
        return $this->key;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function setKey(string $key) : Client
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @var string
     */
    protected $format;

    /**
     * @return string|null
     */
    public function getFormat() : ?string
    {
        return $this->format;
    }

    /**
     * @param string $format
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setFormat(string $format) : Client
    {
        if(!in_array($format, self::FORMATS)) {
            throw new InvalidArgumentException("$format is not a valid format.");
        }

        $this->format = $format;
        return $this;
    }

    /**
     * @var int
     */
    protected $viewportwidth;

    /**
     * @return int|null
     */
    public function getViewportwidth() : ?int
    {
        return $this->viewportwidth;
    }

    /**
     * @param int $viewportwidth
     * @return $this
     */
    public function setViewportwidth(int $viewportwidth) : Client
    {
        $this->viewportwidth = $viewportwidth;
        return $this;
    }

    /**
     * @var int
     */
    protected $evaldelay;

    /**
     * @return int|null
     */
    public function getEvaldelay() : ?int
    {
        return $this->evaldelay;
    }

    /**
     * @param int $evaldelay
     * @return $this
     */
    public function setEvaldelay(int $evaldelay) : Client
    {
        $this->evaldelay = $evaldelay;
        return $this;
    }

    /**
     * @var int
     */
    protected $reporttype;

    /**
     * @return int|null
     */
    public function getReporttype() : ?int
    {
        return $this->reporttype;
    }

    /**
     * @param int $reporttype
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setReporttype(int $reporttype) : Client
    {
        if(!in_array($reporttype, self::REPORT_TYPES)) {
            throw new InvalidArgumentException("$reporttype is not a valid report type.");
        }

        $this->reporttype = $reporttype;
        return $this;
    }

    /**
     * @var string
     */
    protected $username;

    /**
     * @return string|null
     */
    public function getUsername() : ?string
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return $this
     */
    public function setUsername(string $username) : Client
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @var string
     */
    protected $password;

    /**
     * @return string|null
     */
    public function getPassword() : ?string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function setPassword(string $password) : Client
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @var \Psr\Http\Message\ResponseInterface
     */
    protected $response;

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return string
     */
    public function getResponseContent()
    {
        return $this->response->getBody()->getContents();
    }

    /**
     * @param string $key
     * @param array $params
     * @param null|mixed $client
     * @return void
     */
    public function __construct(string $key, array $params = [], $client = null)
    {
        $this->key = $key;

        foreach($params as $param => $value) {
            if(!in_array($param, static::REQUEST_PARAMS)) {
                throw new InvalidArgumentException("$param is not a valid parameter.");
            }
            $this->{'set' . ucfirst($param)}($value);
        }

        if($client == null) {
            $client = new GuzzleClient([
                'base_uri' => 'http://wave.webaim.org/api/'
            ]);
        }

        $this->client = $client;
    }

    /**
     * @param string $url
     * @param array $params
     * @return $this
     */
    public function analyze(string $url, array $params = []) : Client
    {
        foreach($params as $param => $value) {
            if(!in_array($param, static::REQUEST_PARAMS)) {
                throw new InvalidArgumentException("$param is not a valid parameter.");
            }
        }

        $this->response = $this->client->get('request', [
            'query' => array_merge($params, $this->buildRequestParams($url))
        ]);

        return $this;
    }

    /**
     * @param string $url
     * @return array
     */
    protected function buildRequestParams(string $url)
    {
        $requestParams = [
            'url' => $url
        ];

        foreach(self::REQUEST_PARAMS as $param) {
            $value = $this->{'get' . ucfirst($param)}();
            if($value) {
                $requestParams[$param] = $value;
            }
        }

        return $requestParams;
    }
}
