<?php

require_once './vendor/autoload.php';

use ChrGriffin\WaveApi\Client as WaveClient;

$wave = new WaveClient('qH18dF691129');

$response = $wave->setFormat('json')
    ->analyze('https://christiangriffin.ca')
    ->getResponseContent();

var_dump($response);
