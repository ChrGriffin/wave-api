<?php

namespace ChrGriffin\WaveApi\Tests;

use Closure;
use GuzzleHttp\Handler\MockHandler;
use PHPUnit\Framework\TestCase as PHPUnit;

abstract class TestCase extends PHPUnit
{
    /**
     * @var MockHandler
     */
    protected $mockHandler;

    /**
     * @var string
     */
    protected $fixturePath;

    /**
     * @param string $filename
     * @return string
     */
    protected function loadFixture(string $filename) : string
    {
        return file_get_contents(rtrim($this->fixturePath, '/') . '/' . $filename);
    }

    /**
     * @param string $contents
     * @return \stdClass
     */
    protected function decodeJsonFixture(string $contents) : \stdClass
    {
        return json_decode($contents);
    }

    /**
     * @param string $contents
     * @return \SimpleXMLElement
     */
    protected function decodeXmlFixture(string $contents) : \SimpleXMLElement
    {
        return simplexml_load_string($contents);
    }

    /**
     * @param Closure $logic
     * @param array $params
     * @param bool $assertNotEmpty
     * @return mixed|null
     */
    public function assertNoException(Closure $logic, array $params = [], $assertNotEmpty = true)
    {
        $exception = null;
        $response = null;
        try {
            $response = $logic($params);
        }
        catch(\Exception $exception) {
            // do nothing
        }
        $this->assertEmpty(
            $exception,
            !empty($exception)
                ? $exception->getMessage()
                : ''
        );
        if($assertNotEmpty) {
            $this->assertNotEmpty($response);
        }
        return $response;
    }
}
