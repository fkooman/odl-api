<?php

namespace fkooman\ODL;

use fkooman\Json\Json;
use GuzzleHttp\Client;
use fkooman\IO\IO;
use Exception;

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

    /** @var \fkooman\IO\IO */
    private $io;

    public function __construct(Client $client, $dataDir, $authUser = 'admin', $authPass = 'admin')
    {
        $this->client = $client;
        $this->dataDir = $dataDir;
        $this->authUser = $authUser;
        $this->authPass = $authPass;
        $this->io = new IO();
    }

    public function send($baseUrl, $apiData, $isReset = false)
    {
        // add the flowId to the baseUrl
        $decodedApiData = Json::decode($apiData);

        if (array_key_exists('flow', $decodedApiData)) {
            $flowId = $decodedApiData['flow'][0]['id'];
        } elseif (array_key_exists('flow-node-inventory:table', $decodedApiData)) {
            $flowId = $decodedApiData['flow-node-inventory:table'][0]['id'];
        } else {
            throw new Exception('unable to determine flow ID');
        }

        if ($isReset) {
            $apiUrl = $baseUrl;
        } else {
            $apiUrl = $baseUrl.'/flow/'.$flowId;
        }

        return $this->client->put(
            $apiUrl,
            array(
                'body' => $apiData,
                'auth' => array(
                    $this->authUser,
                    $this->authPass,
                ),
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
            $apiData = $this->io->readFile($apiFile);
            $response = $this->send($baseUrl, $apiData, 'reset' === $flowName);
            $output .= $response.'<br>';
        }

        return $output;
    }
}
