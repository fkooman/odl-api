<?php

namespace fkooman\ODL;

use fkooman\Json\Json;
use GuzzleHttp\Client;

class ApiCall
{
    /** @var \GuzzleHttp\Client */
    private $client;

    /** @var string */
    private $authUser;

    /** @var string */
    private $authPass;

    public function __construct(Client $client, $authUser = 'admin', $authPass = 'admin')
    {
        $this->client = $client;
        $this->authUser = $authUser;
        $this->authPass = $authPass;
    }

    public function send($apiUrl, $apiData)
    {
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

#    public function setEther($macAddress)
#    {
#        $this->apiTemplate['flow'][0]['match']['ethernet-match']['ethernet-source']['address'] = $macAddress;
#    }

#    public function getJson()
#    {
#        return Json::encode($this->apiTemplate);
#    }
}
