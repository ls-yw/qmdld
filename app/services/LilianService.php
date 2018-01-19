<?php
namespace Services;

use Basic\BaseService;
use Library\Curl;

class LilianService extends BaseService
{
    public function updateLilianConfig() {
        $url = $this->_config->dldUrl->staticUrl.$this->_config->dldUrl->lilian->config;
        $result = Curl::dld($url, '', 'get');
        if($result['code'] == 0){
            $data = json_decode($result['data'], true);
            print_r($result['data']);
        }else {
            return false;
        }
    }
}