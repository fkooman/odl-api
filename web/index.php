<?php

require_once dirname(__DIR__).'/vendor/autoload.php';

use fkooman\Http\Request;
use fkooman\Ini\IniReader;
use fkooman\IO\IO;
use fkooman\ODL\ApiCall;
use fkooman\ODL\Utils;
use fkooman\Rest\Service;
use fkooman\Tpl\Twig\TwigTemplateManager;
use GuzzleHttp\Client;

try {
    $request = new Request($_SERVER);

    // config
    $iniReader = IniReader::fromFile(
        dirname(__DIR__).'/config/config.ini'
    );

    $apiUrl = $iniReader->v('Api', 'apiUrl');

    // templates
    $templateManager = new TwigTemplateManager(
        array(
            dirname(__DIR__).'/views',
            dirname(__DIR__).'/config/views',
        ),
        null
    );
    $templateManager->setDefault(
        array(
            'rootFolder' => $request->getUrl()->getRoot(),
        )
    );

    // API call
    $client = new Client();
    $authUser = $iniReader->v('Api', 'authUser');
    $authPass = $iniReader->v('Api', 'authPass');
    $apiCall = new ApiCall($client, $authUser, $authPass);

    $io = new IO();
    $dataDir = dirname(__DIR__).'/data';
    $supportedLocations = Utils::extractLocations($dataDir);
    $supportedTables = array('0', '2', '3', '10');

    // REST service
    $service = new Service();

    // GET 
    $service->get(
        '/',
        function (Request $request) use ($templateManager, $supportedLocations) {
            return $templateManager->render(
                'index',
                array(
                    'supportedLocations' => $supportedLocations,
                    'output' => base64_decode($request->getUrl()->getQueryParameter('output')),
                )
            );
        }
    );

    // POST
    $service->post(
        '/',
        function (Request $request) use ($io, $dataDir, $apiCall, $apiUrl) {
            // determine the flow to activate on the table
            $flowName = $request->getPostParameter('flow');
            if (null === $flowName || empty($flowName)) {
                $output = 'No flow specified!';
            } else {
                $output = '';
                if ('loop' === $flowName) {
                    $fileName = sprintf('%s/loop.json', $dataDir);
                    $apiData = $io->readFile($fileName);
                    $output .= sprintf('%s: %s<br>', basename($fileName, '.json'), $apiCall->send($apiUrl.'/0', $apiData));
                } else {
                    $tables = array('0', '2', '3', '10');
                    foreach ($tables as $table) {
                        $fileName = sprintf('%s/%s-%s.json', $dataDir, $flowName, $table);
                        $apiData = $io->readFile($fileName);
                        $output .= sprintf('%s: %s<br>', basename($fileName, '.json'), $apiCall->send($apiUrl.$table, $apiData));
                    }
                }
            }

            return $output;
        }
    );

    $service->run($request)->send();
} catch (Exception $e) {
    echo '<pre>'.$e->getMessage().'</pre>';
    echo '<hr>';
    echo '<pre>'.$e->getTraceAsString().'</pre>';
}
