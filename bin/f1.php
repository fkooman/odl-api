<?php

require_once dirname(__DIR__).'/vendor/autoload.php';

use GuzzleHttp\Client;
use fkooman\ODL\ApiCall;
use fkooman\Ini\IniReader;
use fkooman\IO\IO;

$iniReader = IniReader::fromFile(
    dirname(__DIR__).'/config/config.ini'
);

$apiFile = dirname(__DIR__).'/data/01-arp-sender-to-loop.json';

$apiData = IO::readFile($apiFile);

#$apiFile = dirname(__DIR__).'/data/f1.json';
#$targetEther = 'ff:ff:42:ff:ff:00';

$baseUrl = $iniReader->v('Api', 'baseUrl');
$apiUrl = $baseUrl; //$baseUrl.'/foo/bar';

try {
    $client = new Client();
    $apiCall = new ApiCall(
        $client,
        $iniReader->v('Api', 'authUser'),
        $iniReader->v('Api', 'authPass')
    );
    $response = $apiCall->send($apiUrl, $apiData);
    echo $response;
} catch (Exception $e) {
    echo $e->getMessage().PHP_EOL;
    exit(1);
}
