<?php
namespace Modules\Index\Controllers;

use Basic\BaseController;
use Models\User;
use Services\BasicService;

class LoginController extends BaseController{
    
    public function isloginAction() {
        if(!$this->_islogin()){
            return $this->ajaxReturn(['code'=>1, 'msg'=>'未登录']);
        }else{
            return $this->ajaxReturn(['code'=>0, 'msg'=>'成功']);
        }
    }
    
    public function loginAction() {
        $userName = $this->request->getPost('userName');
        if(empty($userName)){
            return $this->ajaxReturn(['code'=>1, 'msg'=>'参数不能未空']);
        }
        $user = (new User())->getByUsername($userName);
        if(!$user){
            return $this->ajaxReturn(['code'=>1, 'msg'=>'用户不存在']);
        }
        (new BasicService())->updateAll($user);
        $this->session->set('user', $user);
        return $this->ajaxReturn(['code'=>0, 'msg'=>'登录成功']);
    }
    
    public function logoutAction() {
        $this->session->remove('user');
        return $this->ajaxReturn(['code'=>0, 'msg'=>'退出成功']);
    }

}