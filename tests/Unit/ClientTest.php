<?php

namespace ChrGriffin\WaveApi\Tests\Unit;

use ChrGriffin\WaveApi\Client;
use ChrGriffin\WaveApi\Tests\TestCase;
use ChrGriffin\WaveApi\Exceptions\InvalidArgumentException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use TypeError;

class ClientTest extends TestCase
{
    /**
     * @return array
     */
    public function provideValidParameters() : array
    {
        return [
            'format: json'  => ['parameter' => 'format', 'value' => 'json'],
            'format: xml'   => ['parameter' => 'format', 'value' => 'xml'],
            'viewportwidth' => ['parameter' => 'viewportwidth', 'value' => 1440],
            'evaldelay'     => ['parameter' => 'evaldelay', 'value' => 220],
            'reporttype: 1' => ['parameter' => 'reporttype', 'value' => 1],
            'reporttype: 2' => ['parameter' => 'reporttype', 'value' => 2],
            'reporttype: 3' => ['parameter' => 'reporttype', 'value' => 3],
            'username'      => ['parameter' => 'username', 'value' => 'Geralt'],
            'password'      => ['parameter' => 'password', 'value' => 'of Rivia']
        ];
    }

    /**
     * @return array
     */
    public function provideInvalidTypeParameters() : array
    {
        return [
            'format'        => ['parameter' => 'format', 'value' => []],
            'viewportwidth' => ['parameter' => 'viewportwidth', 'value' => 'sort of medium-ish, you know?'],
            'evaldelay'     => ['parameter' => 'evaldelay', 'value' => 'two ticks'],
            'reporttype'    => ['parameter' => 'reporttype', 'value' => 'good'],
            'username'      => ['parameter' => 'username', 'value' => null],
            'password'      => ['parameter' => 'password', 'value' => null]
        ];
    }

    /**
     * @return array
     */
    public function provideInvalidParameters() : array
    {
        return [
            'format'     => ['parameter' => 'format', 'value' => 'csv'],
            'reporttype' => ['parameter' => 'reporttype', 'value' => 4]
        ];
    }

    /**
     * @return void
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->fixturePath = __DIR__ . '/fixtures';
    }

    /**
     * @return void
     */
    public function testClientCanBeInstantiated() : void
    {
        $this->assertNoException(
            function () {
                return new Client('key');
            }
        );
    }

    /**
     * @param string $parameter
     * @param mixed $value
     * @return void
     * @dataProvider provideValidParameters
     */
    public function testValidParametersCanBeSetOnTheClient(string $parameter, $value) : void
    {
        $client = new Client('key');
        $client->{'set' . ucfirst($parameter)}($value);

        $this->assertEquals(
            $value,
            $client->{'get' . ucfirst($parameter)}()
        );
    }

    /**
     * @param string $parameter
     * @param $value
     * @return void
     * @dataProvider provideInvalidTypeParameters
     * @expectedException TypeError
     */
    public function testClientThrowsExceptionWhenTryingToSetInvalidlyTypedParameters(string $parameter, $value) : void
    {
        $client = new Client('key');
        $client->{'set' . ucfirst($parameter)}($value);
    }

    /**
     * @param string $parameter
     * @param mixed $value
     * @return void
     * @dataProvider provideInvalidParameters
     * @expectedException InvalidArgumentException
     */
    public function testClientThrowsExceptionWhenTryingToSetInvalidParameters(string $parameter, $value) : void
    {
        $client = new Client('key');
        $client->{'set' . ucfirst($parameter)}($value);
    }

    /**
     * @return void
     */
    public function testClientCanBeConstructedWithValidParameters() : void
    {
        $params = [
            'format'        => 'json',
            'viewportwidth' => 1440,
            'evaldelay'     => 30,
            'reporttype'    => 1,
            'username'      => 'Geralt',
            'password'      => 'of Rivia'
        ];

        $client = new Client('key', $params);
        foreach($params as $param => $value) {
            $this->assertEquals(
                $value,
                $client->{'get' . ucfirst($param)}()
            );
        }
    }

    /**
     * @param string $parameter
     * @param mixed $value
     * @return void
     * @dataProvider provideInvalidTypeParameters
     * @expectedException TypeError
     */
    public function testClientThrowsExceptionWhenConstructingWithInvalidlyTypedParameters(string $parameter, $value) : void
    {
        new Client('key', [$parameter => $value]);
    }

    /**
     * @param string $parameter
     * @param mixed $value
     * @return void
     * @dataProvider provideInvalidParameters
     * @expectedException InvalidArgumentException
     */
    public function testClientThrowsExceptionWhenConstructingWithInvalidParameters(string $parameter, $value) : void
    {
        new Client('key', [$parameter => $value]);
    }

    /**
     * @return void
     * @expectedException InvalidArgumentException
     */
    public function testClientThrowsExceptionWhenConstructingWithNonexistentParameters() : void
    {
        new Client('key', ['Geralt' => 'of Rivia']);
    }

    /**
     * @return void
     */
    public function testClientCanMakeRequestWithValidParameters() : void
    {
        $params = [
            'format'        => 'json',
            'viewportwidth' => 1440,
            'evaldelay'     => 30,
            'reporttype'    => 1,
            'username'      => 'Geralt',
            'password'      => 'of Rivia'
        ];

        $client = new Client('key', [], $this->getMockedGuzzle());
        $expectedResponse = $this->loadFixture('successful_analysis.json');
        $this->mockHandler->append(new Response(200, [], $expectedResponse));

        $client->analyze('https://christiangriffin.ca', $params);
        $this->assertEquals(
            $expectedResponse,
            $client->getResponseContent()
        );
    }

    /**
     * @return GuzzleClient
     */
    protected function getMockedGuzzle()
    {
        $this->mockHandler = new MockHandler;

        return new GuzzleClient([
            'handler' => $this->mockHandler
        ]);
    }
}
