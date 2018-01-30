<?php
namespace Modules\Dld\Controllers;

use Basic\BaseController;
use Services\LilianService;

class ConfigController extends BaseController
{
    public function updateLilianRewardAction() {
        (new LilianService())->updateLilianReward($this->_user, 10001);
    }
}