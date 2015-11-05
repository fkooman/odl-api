<?php

require_once dirname(__DIR__).'/vendor/autoload.php';

use fkooman\Http\RedirectResponse;
use fkooman\Http\Request;
use fkooman\Ini\IniReader;
use fkooman\IO\IO;
use fkooman\ODL\ApiCall;
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

    $io = new IO();

    $dataDir = dirname(__DIR__).'/data';

    // API call
    $client = new Client();
    $authUser = $iniReader->v('Api', 'authUser');
    $authPass = $iniReader->v('Api', 'authPass');
    $apiCall = new ApiCall($client, $authUser, $authPass);

    // supported flow names
    $supportedFlows = array();
    foreach (glob($dataDir.'/*.json') as $fileName) {
        $flowName = basename($fileName, '.json');
        // remove the table from it
        $flowName = substr($flowName, 0, strrpos($flowName, '-'));

        $found = false;
        foreach ($supportedFlows as $sf) {
            if ($flowName === $sf['id']) {
                $found = true;
            }
        }

        if (!$found) {
            $supportedFlows[] = array(
                'id' => $flowName,
                'name' => $flowName,
            );
        }
    }

    // REST service
    $service = new Service();

    // GET 
    $service->get(
        '/',
        function (Request $request) use ($templateManager, $supportedFlows) {
            return $templateManager->render(
                'index',
                array(
                    'supportedFlows' => $supportedFlows,
                    'active' => $request->getUrl()->getQueryParameter('active'),
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
            $btn = $request->getPostParameter('flow');
            $output = '';

            $tables = array('0', '2', '3', '10');
            foreach ($tables as $t) {
                $file = $dataDir.'/'.$btn.'-'.$t.'.json';
                $apiData = $io->readFile($file);
                $output .= $apiCall->send($apiUrl.$t, $apiData).PHP_EOL;
            }

            return new RedirectResponse(
                $request->getUrl()->getRoot().sprintf('?active=%s&output=%s', $btn, base64_encode($output)),
                302
            );
        }
    );

    $service->run($request)->send();
} catch (Exception $e) {
    echo '<pre>'.$e->getMessage().'</pre>';
    echo '<hr>';
    echo '<pre>'.$e->getTraceAsString().'</pre>';
}
