<?php

require_once dirname(__DIR__).'/vendor/autoload.php';

use GuzzleHttp\Client;
use fkooman\Json\Json;
use fkooman\ODL\ApiCall;
use fkooman\Ini\IniReader;

$iniReader = IniReader::fromFile(
    dirname(__DIR__).'/config/config.ini'
);

$apiFile = dirname(__DIR__).'/data/f1.json';
$targetEther = 'ff:ff:42:ff:ff:00';

$baseUrl = $iniReader->v('Api', 'baseUrl');
$authUser = $iniReader->v('Api', 'authUser');
$authPass = $iniReader->v('Api', 'authPass');
$apiUrl = $baseUrl.'/foo/bar';

try {
    $apiCall = new ApiCall($apiFile);
    $apiCall->setEther($targetEther);
    $apiData = $apiCall->getJson();

    $client = new Client();
    $response = $client->put(
        $apiUrl,
        array(
            'body' => $apiData,
            'auth' => array(
                $authUser,
                $authPass,
            ),
        )
    );

    echo $response;
} catch (Exception $e) {
    echo $e->getMessage().PHP_EOL;
    exit(1);
}
