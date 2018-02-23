<?php
namespace Modules\Dld\Controllers;

use Basic\BaseController;
use Services\TowerService;

class TowerController extends BaseController
{
    public function getInfoAction()
    {
        if(!$this->_user || empty($this->_user['h5token']))return $this->ajaxReturn(['code'=>1, 'msg'=>'未登录或未授权']);
        $lists = (new TowerService())->index($this->_user);
        if(!$lists)return $this->ajaxReturn(['code'=>1, 'msg'=>'数据获取失败']);
        return $this->ajaxReturn($lists, 0 ,'成功');
    }
    
    /**
     * 挑战千层塔
     * 
     * @create_time 2018年2月11日
     */
    public function fightAction() {
        if(!$this->_user || empty($this->_user['h5token']))return $this->ajaxReturn(['code'=>1, 'msg'=>'未登录或未授权']);
        $floor        = $this->request->getPost('floor');
        $index        = (int)$this->request->getPost('index');
        $row = (new TowerService())->fight($this->_user, $index, $floor);
        if($row){
            return $this->ajaxReturn('', 0 ,'挑战成功');
        }else{
            return $this->ajaxReturn('', 0 ,'挑战失败');
        }
    }
    
    /**
     * 设置属性
     * 
     * @create_time 2018年2月11日
     */
    public function saveAttrAction()
    {
        if(!$this->_user || empty($this->_user['h5token']))return $this->ajaxReturn(['code'=>1, 'msg'=>'未登录或未授权']);
        $qm = $this->request->getPost('qm', 'string');
        $yy = $this->request->getPost('yy', 'string');
        $jg = $this->request->getPost('jg', 'string');
        $js = $this->request->getPost('js', 'string');
        $row = (new TowerService())->setTechtree($this->_user, "{$qm}|{$yy}|{$jg}|{$js}");
        if($row){
            return $this->ajaxReturn('', 0 ,'设置成功');
        }else{
            return $this->ajaxReturn('', 0 ,'设置失败');
        }
    }
    
    public function reviveAction()
    {
        if(!$this->_user || empty($this->_user['h5token']))return $this->ajaxReturn(['code'=>1, 'msg'=>'未登录或未授权']);
        $row = (new TowerService())->buylife($this->_user);
        if($row){
            return $this->ajaxReturn('', 0 ,'复活成功');
        }else{
            return $this->ajaxReturn('', 0 ,'复活失败');
        }
    }
}