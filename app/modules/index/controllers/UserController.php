<?php
namespace Modules\Index\Controllers;

use Basic\BaseController;
use Models\UserConfig;
use Services\UserService;
use Library\Redis;
use Models\Shops;
use Models\LilianReward;

class UserController extends BaseController{

	
    public function saveConfigAction() {
        $userId = $this->_user['id'];
        $data = [];
        $data['meridian_auto']             = (int)$this->request->getPost('meridian_auto', 'int', 0);
        $data['meridian_flag']             = (int)$this->request->getPost('meridian_flag', 'int', 0);
        $data['meridian_reward']           = (int)$this->request->getPost('meridian_reward', 'int', 0);
        $data['pvp_auto']                  = (int)$this->request->getPost('pvp_auto', 'int', 0);
        $data['pvp_potion']                = (int)$this->request->getPost('pvp_potion', 'int', 0);
        $data['pvp_friend_vit']            = (int)$this->request->getPost('pvp_friend_vit', 'int', 0);
        $data['master_auto']               = (int)$this->request->getPost('master_auto', 'int', 0);
        $data['servant_auto']              = (int)$this->request->getPost('servant_auto', 'int', 0);
        $data['servant_catch']             = (int)$this->request->getPost('servant_catch', 'int', 0);
        $data['servant_train']             = (int)$this->request->getPost('servant_train', 'int', 0);
        $data['servant_rob']               = (int)$this->request->getPost('servant_rob', 'int', 0);
        $data['servant_release']           = (int)$this->request->getPost('servant_release', 'int', 0);
        $data['faction_auto']              = (int)$this->request->getPost('faction_auto', 'int', 0);
        $data['faction_club']              = (int)$this->request->getPost('faction_club', 'int', 0);
        $data['qualifying_person']         = (int)$this->request->getPost('qualifying_person', 'int', 0);
        $data['qualifying_team']           = (int)$this->request->getPost('qualifying_team', 'int', 0);
        $data['qualifying_doushen']        = (int)$this->request->getPost('qualifying_doushen', 'int', 0);
        $data['qualifying_doushen_type']   = (int)$this->request->getPost('qualifying_doushen_type', 'int', 0);
        $data['hangup_equip']              = (int)$this->request->getPost('hangup_equip', 'int', 0);
        $data['hangup_box']                = (int)$this->request->getPost('hangup_box', 'int', 0);
        $data['hangup_encourage']          = (int)$this->request->getPost('hangup_encourage', 'int', 0);
        $data['hangup_fight']              = (int)$this->request->getPost('hangup_fight', 'int', 0);
        $data['lilian_ordinary']           = (int)$this->request->getPost('lilian_ordinary', 'int', 0);
        $data['lilian_ordinary_type']      = (int)$this->request->getPost('lilian_ordinary_type', 'int', 1);
        $data['lilian_used']               = (int)$this->request->getPost('lilian_used', 'int', 0);
        $data['lilian_hero_ordinary']      = (int)$this->request->getPost('lilian_hero_ordinary', 'int', 0);
        $data['lilian_hero_ordinary_type'] = (int)$this->request->getPost('lilian_hero_ordinary_type', 'int', 1);
        
        
        //商店保存
        $pvpShop                         = $this->request->getPost('pvp_shop');
        $servantShop                     = $this->request->getPost('servant_shop');
        $qualifyingShop                  = $this->request->getPost('qualifying_shop');
        $doushenShop                     = $this->request->getPost('doushen_shop');
        $data['pvp_shop']                = !empty($pvpShop) ? implode(',', $pvpShop) : '';
        $data['servant_shop']            = !empty($servantShop) ? implode(',', $servantShop) : '';
        $data['qualifying_shop']         = !empty($qualifyingShop) ? implode(',', $qualifyingShop) : '';
        $data['doushen_shop']            = !empty($doushenShop) ? implode(',', $doushenShop) : '';
        
        $lilianHeroGoods                         = $this->request->getPost('lilian_hero_ordinary_goods');
        $data['lilian_hero_ordinary_goods']      = !empty($lilianHeroGoods) ? implode(',', $lilianHeroGoods) : '';
        
        $userConfig = (new UserService())->getUserConfig($userId);
        if($userConfig){
            $row = (new UserConfig())->updateData($data, ['id'=>$userConfig['id']]);
        }else{
            $row = (new UserConfig())->insertData($data);
        }
        if($row){
            $redisKeys = Redis::getInstance()->keys('*_'.$userId);
            if(count($redisKeys) > 0){
                foreach ($redisKeys as $val){
                    Redis::getInstance()->del(substr($val, 4));
                }
            }
            return $this->ajaxReturn(['code'=>1, 'msg'=>'保存成功']);
        }
        return $this->ajaxReturn(['code'=>1, 'msg'=>'保存失败']);
    }
    
    public function getConfigAction()
    {
        $userConfig = (new UserService())->getUserConfig($this->_user['id']);
        
        return $this->ajaxReturn($userConfig, 0, '成功');
    }
    
    public function shopAction() {
        $shops = (new Shops())->getList('1=1');
        $list = [];
        foreach ($shops as $key => $val){
            $goods = json_decode($val['goods'], true);
            foreach ($goods as $v) {
                $list[$val['mark']][$v['id']] = $v['name'];
            }
        }
        return $this->ajaxReturn($list, 0, '成功');
    }
    
    public function lilianGoodsAction() {
        $dup = $this->request->getPost('dup');
        $goods = (new LilianReward())->getList(['dup'=>$dup]);
        return $this->ajaxReturn($goods, 0, '成功');
    }
}