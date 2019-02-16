<?php
namespace Modules\Zyhx\Controllers;

use Basic\BaseController;
use Services\ZyhxHomeService;

class HomeController extends BaseController {
    
    public function infoAction()
    {
        $a = (new ZyhxHomeService())->getInfo($this->_user);
        return $this->ajaxReturn($a);
    }
    
}