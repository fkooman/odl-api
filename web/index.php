<?php

require_once dirname(__DIR__).'/vendor/autoload.php';

use fkooman\Rest\Service;
use fkooman\Http\Request;
use fkooman\Tpl\Twig\TwigTemplateManager;
use fkooman\Ini\IniReader;
use GuzzleHttp\Client;
use fkooman\ODL\ApiCall;
use fkooman\Http\RedirectResponse;

try {
    $request = new Request($_SERVER);

    // config
    $iniReader = IniReader::fromFile(
        dirname(__DIR__).'/config/config.ini'
    );

    $baseUrl = $iniReader->v('Api', 'baseUrl');

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

    $dataDir = dirname(__DIR__).'/data';

    // API call
    $client = new Client();
    $authUser = $iniReader->v('Api', 'authUser');
    $authPass = $iniReader->v('Api', 'authPass');
    $apiCall = new ApiCall($client, $dataDir, $authUser, $authPass);

    // supported flow names
    $supportedFlows = array();
    foreach (glob($dataDir.'/*', GLOB_ONLYDIR) as $dirName) {
        $flowName = basename($dirName);
        $supportedFlows[] = array(
            'id' => $flowName,
            'name' => ucfirst($flowName),
        );
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
        function (Request $request) use ($templateManager, $apiCall, $baseUrl) {
            // determine the flow to activate
            $flowName = $request->getPostParameter('flow');

            $output = $apiCall->activate($baseUrl, $flowName);

            return new RedirectResponse(
                $request->getUrl()->getRoot().sprintf('?active=%s&output=%s', $flowName, base64_encode($output)),
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
