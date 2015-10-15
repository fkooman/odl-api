<?php

namespace fkooman\ODL;

use fkooman\Json\Json;

class ApiCall
{
    /** @var array */
    private $apiTemplate;

    public function __construct($apiTemplateFile)
    {
        $this->apiTemplate = Json::decodeFile($apiTemplateFile);
    }

    public function setEther($macAddress)
    {
        $this->apiTemplate['flow'][0]['match']['ethernet-match']['ethernet-source']['address'] = $macAddress;
    }

    public function getJson()
    {
        return Json::encode($this->apiTemplate);
    }
}
