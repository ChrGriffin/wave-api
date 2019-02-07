# Wave Api

`wave-api` is a strongly typed package for interacting with Webaim's [WAVE API](http://wave.webaim.org/api/register).

## Requirements

`wave-api` requires PHP 7.1 or greater. You will also need to create an account with the [WAVE API](http://wave.webaim.org/api/register) in order to use it.

## Installation

You can install via composer:

```
composer install chrgriffin/wave-api
```

## Usage

### Basic Usage

Using the client is extremely simple:

```php
require_once './vendor/autoload.php';

use ChrGriffin\WaveApi\Client as WaveClient;

$wave = new WaveClient('my-api-key');
$response = $wave->analyze('my-url');
```

### Additional Parameters

If you want to specify optional parameters, such as `viewportwidth`, `reporttype`, or `format`, there are multiple ways to do so.

Firstly, you can specify any additional parameters when instantiating the client:

```php
$wave = new WaveClient('my-api-key', [
    'viewportwidth' => 1440,
    'reporttype'    => 2,
    'format'        => 'json'
]);
```

Secondly, you can specify any additional parameters when making the request:

```php
$response = $wave->analyze('my-url', [
    'viewportwidth' => 1440,
    'reporttype'    => 2,
    'format'        => 'json'
]);
```

Or thirdly, you can chain multiple setters:

```php
$wave->setViewportwidth(1440)
    ->setReporttype(2)
    ->setFormat('json')
    ->analyze('my-url);
```

### Response Format

The `format` parameter has two valid options: `json` and `xml`. Setting this parameter will change the response type:
* if format is `json`, then `$wave->analyze()` will return a `\stdClass` object
* if format is `xml`, then `$wave->analyze()` will return a `\SimpleXMLElement` object

## Documentation

### Available Methods

* `getClient() : GuzzleHttp\Client` _(by default)_
* `setClient($client) : ChrGriffin\WaveApi\Client`
  * `$client`: Replace the default GuzzleHttp client with your own.
* `getKey() : string`
* `setKey(string $key) : ChrGriffin\WaveApi\Client`
  * `$key`: Your WAVE API key.
* `getFormat() : string` _(`json` by default)_
* `setFormat(string $format) : ChrGriffin\WaveApi\Client`
  * `$format`: The desired report format. An exception will be thrown if the value is neither `json` nor `xml`.
* `getViewportwidth() : int`
* `setViewportwidth(int $viewportwidth) : ChrGriffin\WaveApi\Client`
  * `$viewportwidth`: The desired viewport width when analyzing.
* `getEvaldelay() : int`
* `setEvaldelay(int $evaldelay) : ChrGriffin\WaveApi\Client`
  * `$evaldelay`: The desired number of milliseconds to wait before analyzing.
* `getReporttype() : int`
* `setReporttype(int $reporttype) : ChrGriffin\WaveApi\Client`
  * `$reporttype`: The desired report type. An exception will be thrown if the value is none of `1`, `2`, or `3`.
* `getUsername() : string`
* `setUsername(string $username) : ChrGriffin\WaveApi\Client`
  * `$username`: The desired username for basic HTTP authentication to the given URL.
* `getPassword() : string`
* `setPassword(string $password) : ChrGriffin\WaveApi\Client`
  * `$password`: The desired password for basic HTTP authentication to the given URL.
* `getResponseContents() : null|\stdClass|\SimpleXMLElement` _(will be null before a request is made)
* `setParams(array $params) : ChrGriffin\WaveApi\Client`
  * `$params`: An associative array of any or all paramaters. Valid paramaters:
    * `format : string` _(must be one of `json` or `xml`)_
    * `viewportwidth : int`
    * `evaldelay : int`
    * `reporttype : int` _(must be one of `1`, `2`, or `3`)_
    * `username : string`
    * `password : string`
* `analyze(string $url, array $params = []) : \stdClass|\SimpleXMLElement`
  * `$url`: The desired URL to analyze.
  * `$params`: An associative array of any or all paramaters. Valid paramaters:
    * `format : string` _(must be one of `json` or `xml`)_
    * `viewportwidth : int`
    * `evaldelay : int`
    * `reporttype : int` _(must be one of `1`, `2`, or `3`)_
    * `username : string`
    * `password : string`
    
### Exceptions

* A `\TypeError` will be thrown whenever an invalidly typed parameter is passed to a method (refer to the above documentation for valid types).
* A `ChrGriffin\WaveApi\Exceptions\InvalidArgumentException` (extends `\InvalidArgumentException`)will be thrown in the following instances:
  * Trying to set `format` to an invalid value (not one of `xml` or `json`).
  * Trying to set `reporttype` to an invalid value (not one of `1`, `2`, or `3`).
  * Trying to set a parameter that doesn't exist.
* A `ChrGriffin\WaveApi\Exceptions\ResponseException` will be thrown when the WAVE API responds with an error. The error, if provided, will be the exception's message.
