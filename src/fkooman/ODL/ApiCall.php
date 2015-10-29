<?php

namespace fkooman\ODL;

use fkooman\Json\Json;
use GuzzleHttp\Client;

class ApiCall
{
    /** @var \GuzzleHttp\Client */
    private $client;

    /** @var string */
    private $dataDir;

    /** @var string */
    private $authUser;

    /** @var string */
    private $authPass;

    public function __construct(Client $client, $dataDir, $authUser = 'admin', $authPass = 'admin')
    {
        $this->client = $client;
        $this->dataDir = $dataDir;
        $this->authUser = $authUser;
        $this->authPass = $authPass;
    }

    public function send($baseUrl, $apiData)
    {
        // add the flowId to the baseUrl
        $decodedApiData = Json::decode($apiData);

        $flowId = $decodedApiData['flow'][0]['id'];
        $apiUrl = $baseUrl.$flowId;

        return $this->client->put(
            $apiUrl,
            array(
                'body' => $apiData,
                'auth' => array(
                    $this->authUser,
                    $this->authPass,
                ),
                //'verify' => false,
                'headers' => array(
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ),
            )
        );
    }

    public function activate($baseUrl, $flowName)
    {
        $output = '';
        foreach (glob($this->dataDir.sprintf('/%s/*.json', $flowName)) as $apiFile) {
            $output .= $apiFile.'<br>';
            $apiData = $io->readFile($apiFile);
            $response = $apiCall->send($baseUrl, $apiData);
            $output .= $response;
        }

        return $output;
    }
}
