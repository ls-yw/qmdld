<?php
namespace Modules\Dld\Controllers;

use Basic\BaseController;
use Services\FactionService;
use Services\BasicService;

class FactionController extends BaseController
{
    public function getCaveInfoAction()
    {
        if(!$this->_user || empty($this->_user['h5token']))return $this->ajaxReturn(['code'=>1, 'msg'=>'未登录或未授权']);
        $lists = (new FactionService())->getCaveInfo($this->_user);
        if(!$lists)return $this->ajaxReturn(['code'=>1, 'msg'=>'数据获取失败']);
        return $this->ajaxReturn($lists, 0 ,'成功');
    }
    
    public function caveFightAction() {
        if(!$this->_user || empty($this->_user['h5token']))return $this->ajaxReturn(['code'=>1, 'msg'=>'未登录或未授权']);
        $sep        = (int)$this->request->getPost('sep');
        $id         = (int)$this->request->getPost('id');
        $autoStatus = (int)$this->request->getPost('autoStatus');
        if($autoStatus == 1)(new BasicService())->addStatus($this->_user);
        $msg = (new FactionService())->caveFight($this->_user, $sep, $id);
        return $this->ajaxReturn('', 0, $msg);
    }
}