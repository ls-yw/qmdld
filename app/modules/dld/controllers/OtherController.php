<?php
namespace Modules\Dld\Controllers;

use Basic\BaseController;
use Services\OtherService;

class OtherController extends BaseController
{
    /**
     * 一键解锁72变
     * 
     * @create_time 2018年1月20日
     */
    public function unlockPageAction() {
        (new OtherService())->goUnlockPage($this->_user);
        return $this->ajaxReturn('', 0, '成功');
    }
    
    /**
     * 一键解锁场景
     *
     * @create_time 2018年1月20日
     */
    public function unlockSceneAction() {
        (new OtherService())->onekeyScene($this->_user);
        return $this->ajaxReturn('', 0, '成功');
    }
}