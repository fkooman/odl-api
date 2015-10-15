<?php

require_once __DIR__.'/vendor/autoload.php';

use GuzzleHttp\Client;
use fkooman\Json\Json;
use fkooman\ODL\ApiCall;

// CONFIG
$apiFile = __DIR__.'/data/f1.json';
$targetEther = 'ff:ff:42:ff:ff:00';
$putUrl = 'http://localhost/foo';
$user = 'admin';
$pass = 'admin';

// APP
try {
    $apiCall = new ApiCall($apiFile);
    $apiCall->setEther($targetEther);
    $apiData = $apiCall->getJson();

    $client = new Client();
    $response = $client->put(
        $putUrl,
        array(
            'body' => $apiData,
            'auth' => array(
                $user,
                $pass,
            ),
        )
    );

    echo $response;
} catch (Exception $e) {
    echo $e->getMessage().PHP_EOL;
    exit(1);
}
