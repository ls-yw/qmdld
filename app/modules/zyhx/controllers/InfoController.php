<?php
namespace Modules\Zyhx\Controllers;

use Basic\BaseController;
use Models\UserInfo;
use Models\User;

class InfoController extends BaseController
{
    /**
     * 获取用户信息
     * 
     * @create_time 2018年1月11日
     */
    public function getinfoAction() {
//         $userInfo = (new UserInfo())->getByUserId($this->_user['id']);
//         if(!$userInfo){
//             (new BasicService())->getInfo($this->_user);
//             $userInfo = (new UserInfo())->getByUserId($this->_user['id']);
//             if(!$userInfo){
//                 return $this->ajaxReturn('', 1, '用户信息获取失败');
//             }
//         }
        $userInfo['h5token'] = (new User())->getByid($this->_user['id'])['h5token'];
        return $this->ajaxReturn($userInfo, 0, '成功');
    }
    
    /**
     * 从接口更新用户信息
     * 
     * @create_time 2018年1月11日
     */
    public function updateinfoAction() {
//         $res = (new BasicService())->updateAll($this->_user);
//         if($res === false){
//             return $this->ajaxReturn('', 1, '更新失败');
//         }
        return $this->ajaxReturn('', 0, '更新成功');
    }
}