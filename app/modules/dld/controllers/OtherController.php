<?php
namespace Modules\Dld\Controllers;

use Basic\BaseController;
use Services\OtherService;
use Services\GoodsService;
use Services\BasicService;

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
    
    /**
     * 分享武器
     * 
     * @create_time 2018年2月1日
     */
    public function unlockWeaponAction() {
        (new OtherService())->onekeyWeapon($this->_user);
        return $this->ajaxReturn('', 0, '成功');
    }
    
    /**
     * 分享技能
     * 
     * @create_time 2018年2月1日
     */
    public function unlockSkillAction() {
        (new OtherService())->onekeySkill($this->_user);
        return $this->ajaxReturn('', 0, '成功');
    }
    
    public function updateGoodsAction() {
        (new GoodsService())->updateGoods($this->_user);
    }
    
    public function codeAction() {
        $code = $this->request->getPost('code');
        if(empty($code))return $this->ajaxReturn('', 1, '兑换码不能为空');
        
        $row = (new OtherService())->exchangeCode($this->_user, $code);
        if($row){
            return $this->ajaxReturn('', 0, '成功');
        }else{
            return $this->ajaxReturn('', 1, '兑换失败');
        }
    }
    
    /**
     * 开启属性
     * 
     * @create_time 2018年2月11日
     */
    public function addStatusAction()
    {
        $id = $this->request->getPost('id');
        if(empty($id))return $this->ajaxReturn('', 1, '参数错误');
        
        $row = (new BasicService())->useGood($this->_user, ($id+10));
        
        return $this->ajaxReturn($id, 0, '成功');
    }
}