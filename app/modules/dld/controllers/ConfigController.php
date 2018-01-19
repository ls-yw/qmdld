<?php
namespace Modules\Dld\Controllers;

use Basic\BaseController;
use Services\LilianService;

class ConfigController extends BaseController
{
    public function updateLilianConfigAction() {
        (new LilianService())->updateLilianConfig();
    }
}