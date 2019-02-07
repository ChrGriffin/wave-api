<?php

namespace ChrGriffin\WaveApi;

use GuzzleHttp\Client as GuzzleClient;
use ChrGriffin\WaveApi\Exceptions\{ InvalidArgumentException, ResponseException };
use TypeError;

class Client
{
    /**
     * @const array
     */
    const REQUEST_PARAMS = [
        'key'           => 'string',
        'format'        => 'string',
        'viewportwidth' => 'integer',
        'evaldelay'     => 'integer',
        'reporttype'    => 'integer',
        'username'      => 'string',
        'password'      => 'string'
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
     * @return GuzzleClient|mixed
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
    protected $format = 'json';

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
        $this->checkFormatIsValid($format);
        $this->format = $format;
        return $this;
    }

    /**
     * @param string $format
     * @throws InvalidArgumentException
     */
    protected function checkFormatIsValid(string $format) : void
    {
        if(!in_array($format, self::FORMATS)) {
            throw new InvalidArgumentException("$format is not a valid format.");
        }
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
        $this->checkReportTypeIsValid($reporttype);
        $this->reporttype = $reporttype;
        return $this;
    }

    /**
     * @param int $reportType
     * @throws InvalidArgumentException
     */
    protected function checkReportTypeIsValid(int $reportType) : void
    {
        if(!in_array($reportType, self::REPORT_TYPES)) {
            throw new InvalidArgumentException("$reportType is not a valid report type.");
        }
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
     * @var string
     */
    protected $responseContents;

    /**
     * @return string
     */
    public function getResponseContent()
    {
        return $this->responseContents;
    }

    /**
     * @param array $params
     * @return void
     */
    public function setParams(array $params) : void
    {
        foreach($params as $param => $value) {
            $this->checkParamExists($param);
            $this->{'set' . ucfirst($param)}($value);
        }
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
        $this->setParams($params);

        if($client === null) {
            $client = new GuzzleClient([
                'base_uri' => 'http://wave.webaim.org/api/'
            ]);
        }

        $this->client = $client;
    }

    /**
     * @param string $url
     * @param array $params
     * @return mixed
     * @throws InvalidArgumentException
     * @throws ResponseException
     * @throws TypeError
     */
    public function analyze(string $url, array $params = [])
    {
        $this->setParams($params);

        $responseContents = $this->client->get('request', [
            'query' => $this->buildRequestParams($url)
        ])->getBody()->getContents();

        return $this->responseContents = $this->{'validate' . ucfirst($this->format) . 'Response'}($responseContents);
    }

    /**
     * @param string $contents
     * @return \stdClass
     * @throws ResponseException
     */
    protected function validateJsonResponse(string $contents) : \stdClass
    {
        $contents = json_decode($contents);

        if(isset($contents->success) && $contents->success === false) {
            throw new ResponseException($contents->error ?? 'Request was not successful.');
        }

        return $contents;
    }

    /**
     * @param string $contents
     * @return \SimpleXMLElement
     * @throws ResponseException
     */
    protected function validateXmlResponse(string $contents) : \SimpleXMLElement
    {
        $contents = simplexml_load_string($contents);
        if((string)$contents->success === 'false') {
            throw new ResponseException((string)$contents->error ?? 'Request was not successful.');
        }

        return $contents;
    }

    /**
     * @param string $param
     * @return void
     * @throws InvalidArgumentException
     */
    protected function checkParamExists(string $param) : void
    {
        if(!in_array($param, array_keys(static::REQUEST_PARAMS))) {
            throw new InvalidArgumentException("$param is not a valid parameter.");
        }
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

        foreach(array_keys(self::REQUEST_PARAMS) as $param) {
            $value = $this->{'get' . ucfirst($param)}();
            if($value) {
                $requestParams[$param] = $value;
            }
        }

        return $requestParams;
    }
}
