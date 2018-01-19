<?php
namespace Modules\Index\Controllers;

use Basic\BaseController;
use Models\User;
use Services\BasicService;

class IndexController extends BaseController{
	public function indexAction($params=null){
		return ;
	}

	/**
	 * 更新h5token
	 * 
	 * @create_time 2018年1月12日
	 */
    public function updateTokenAction() {
        $token = $this->request->getPost('h5token');
        if(empty($token)){
            return $this->ajaxReturn(['code'=>1, 'msg'=>'参数错误']);
        }
        $row = (new User())->updateData(['h5token'=>$token], ['id'=>$this->_user['id']]);
        if(!$row){
            return $this->ajaxReturn(['code'=>1, 'msg'=>'保存失败']);
        }
        $this->_user = (new User())->getById($this->_user['id']);
        $this->session->set('user', $this->_user);
        (new BasicService())->getInfo($this->_user);
        return $this->ajaxReturn(['code'=>0, 'msg'=>'保存成功']);
    }
}