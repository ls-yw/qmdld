<?php
namespace Modules\Index\Controllers;

use Basic\BaseController;
use Services\QualifyingService;

class QualifyingController extends BaseController{

    public function doushenIndexAction() {
        if(!$this->_user || empty($this->_user['h5token']))return $this->ajaxReturn(['code'=>1, 'msg'=>'未登录或未授权']);
        $lists = (new QualifyingService())->doushenIndex($this->_user);
        if(!$lists)return $this->ajaxReturn(['code'=>1, 'msg'=>'数据获取失败']);
        return $this->ajaxReturn($lists, 0 ,'成功');
    }
}