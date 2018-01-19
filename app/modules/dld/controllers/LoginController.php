<?php
namespace Modules\Dld\Controllers;

use Basic\BaseController;
use Services\BasicService;

class LoginController extends BaseController
{
    public function authAction() {
        (new BasicService())->auth($this->_user['openid']);
    }
}