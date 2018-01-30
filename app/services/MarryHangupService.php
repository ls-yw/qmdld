<?php
namespace Services;

use Basic\BaseService;
use Library\Curl;
use Library\Log;

class MarryHangupService extends BaseService
{
    
    public function main($user)
    {
        $userConfig = (new UserService())->getUserConfig($user['id']);
        $arr = $this->index($user, $userConfig);
        if($arr && $arr['encourage'] == 0 && $userConfig['hangup_encourage'] == 1)$this->encourage($user);
        if($arr && $arr['fight'] < $arr['maxfight'] && $userConfig['hangup_fight'] == 1){
            Log::dld($user['id'], '开始挑战仙缘历练');
            for($i=$arr['fight'];$i<$arr['maxfight'];$i++){
                $this->fight($user);
            }
        }
        if($userConfig['hangup_equip'] == 1)$this->getBag($user);
    }
    
    public function index($user, $userConfig) {
        //cmd=marry_hangup&op=query&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=74c930322d4f66819dd0800940743c24&pf=wx2;
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'marry_hangup';
        $params['op']             = 'query';
        $params['uid']            = $user['uid'];
        $params['uin']            = null;
        $params['skey']           = null;
        $params['h5openid']       = $user['h5openid'];
        $params['h5token']        = $user['h5token'];
        $params['pf']             = 'wx2';
        
        $result = Curl::dld($url, $params);
        if($result['code'] == 0){
            $data = $result['data'];
            $this->dealResult($data, $user['id']);
            if($data['result'] == '0'){
                if(isset($data['account']))Log::dld($user['id'], "获得仙缘 x {$data['account']['point_award']}，装备 x ".count($data['account']['equip_award'])."，宝箱 x ".count($data['account']['box_award']));
                if(count($data['selfbox']) > 0 && $userConfig['hangup_box'] == 1){  //我的宝箱
                    foreach ($data['selfbox'] as $val){
                        if($val['locked'] == 0)$this->getBox($user, $val['idx'], 0);
                    }
                }
                if(count($data['oppbox']) > 0 && $userConfig['hangup_box'] == 1){  //对方的宝箱
                    foreach ($data['oppbox'] as $v){
                        if($v['locked'] == 1)$this->getBox($user, $v['idx'], 1);
                    }
                }
                $res = [];
                $res['encourage'] = $data['encourage'];
                $res['fight']     = $data['fight'];
                $res['maxfight']  = $data['maxfight'];
                
                if($data['rlcoin'] >= 1000)$this->exchange($user);
//                 if($data['encourage'] == 0){
//                     $this->encourage($user);
//                 }
//                 if($data['fight'] < $data['maxfight'] && $autoFight){
//                     Log::dld($user['id'], '开始挑战仙缘历练');
//                     for($i=$data['fight'];$i<$data['maxfight'];$i++){
//                         $this->fight($user);
//                     }
//                 }
                return $res;
            }
            return false;
        }
    }
    
    /**
     * 获取背包详情
     * @param unknown $user
     * @create_time 2018年1月23日
     */
    public function getBag($user)
    {
        //cmd=marry_hangup&op=bag_query&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=74c930322d4f66819dd0800940743c24&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'marry_hangup';
        $params['op']             = 'bag_query';
        $params['uid']            = $user['uid'];
        $params['uin']            = null;
        $params['skey']           = null;
        $params['h5openid']       = $user['h5openid'];
        $params['h5token']        = $user['h5token'];
        $params['pf']             = 'wx2';
        
        $result = Curl::dld($url, $params);
        if($result['code'] == 0){
            $data = $result['data'];
            $this->dealResult($data, $user['id']);
            if($data['result'] == '0'){
                if(count($data['bag']) > 0)$this->_dealEquip($user, $data['bag']);
            }
            return true;
        }
    }
    
    /**
     * 熔炼
     * @param unknown $user
     * @param unknown $idx
     * @create_time 2018年1月23日
     */
    public function ronglian($user, $idx)
    {
        //cmd=marry_hangup&op=ronglian&grid_id=2&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=74c930322d4f66819dd0800940743c24&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'marry_hangup';
        $params['op']             = 'ronglian';
        $params['grid_id']        = $idx;
        $params['uid']            = $user['uid'];
        $params['uin']            = null;
        $params['skey']           = null;
        $params['h5openid']       = $user['h5openid'];
        $params['h5token']        = $user['h5token'];
        $params['pf']             = 'wx2';
        
        $result = Curl::dld($url, $params);
        if($result['code'] == 0){
            $data = $result['data'];
            $this->dealResult($data, $user['id']);
            if($data['result'] == '0'){
                Log::dld($user['id'], $data['msg']);
                return $data['bag'];
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    
    /**
     * 打开宝箱
     * @param unknown $user
     * @param unknown $idx
     * @create_time 2018年1月23日
     */
    public function getBox($user, $idx, $type) {
        //cmd=marry_hangup&op=unlock&type=0&idx=1&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=74c930322d4f66819dd0800940743c24&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'marry_hangup';
        $params['op']             = 'unlock';
        $params['type']           = $type;
        $params['idx']            = $idx;
        $params['uid']            = $user['uid'];
        $params['uin']            = null;
        $params['skey']           = null;
        $params['h5openid']       = $user['h5openid'];
        $params['h5token']        = $user['h5token'];
        $params['pf']             = 'wx2';
        
        $result = Curl::dld($url, $params);
        if($result['code'] == 0){
            $data = $result['data'];
            $this->dealResult($data, $user['id']);
            if($data['result'] == '0'){
                Log::dld($user['id'], ($type == 0 ? '领取' : '解锁')."宝箱，获得：仙缘 x {$data['point_award']}");
            }
            return true;
        }
    }
    
    /**
     * 互动
     * @param unknown $user
     * @return boolean
     * @create_time 2018年1月23日
     */
    public function encourage($user)
    {
        //cmd=marry_hangup&op=encourage&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=74c930322d4f66819dd0800940743c24&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'marry_hangup';
        $params['op']             = 'encourage';
        $params['uid']            = $user['uid'];
        $params['uin']            = null;
        $params['skey']           = null;
        $params['h5openid']       = $user['h5openid'];
        $params['h5token']        = $user['h5token'];
        $params['pf']             = 'wx2';
        
        $result = Curl::dld($url, $params);
        if($result['code'] == 0){
            $data = $result['data'];
            $this->dealResult($data, $user['id']);
            if($data['result'] == '0'){
                Log::dld($user['id'], '夫妻互动成功');
            }
            return true;
        }
    }
    
    /**
     * 战斗
     * @param unknown $user
     * @create_time 2018年1月23日
     */
    public function fight($user) {
        //cmd=marry_hangup&op=fight&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=74c930322d4f66819dd0800940743c24&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'marry_hangup';
        $params['op']             = 'fight';
        $params['uid']            = $user['uid'];
        $params['uin']            = null;
        $params['skey']           = null;
        $params['h5openid']       = $user['h5openid'];
        $params['h5token']        = $user['h5token'];
        $params['pf']             = 'wx2';
        
        $result = Curl::dld($url, $params);
        if($result['code'] == 0){
            $data = $result['data'];
            $this->dealResult($data, $user['id']);
            if($data['result'] == '0'){
                Log::dld($user['id'], $data['msg']);
            }
            return true;
        }
    }
    
    /**
     * 换装
     * @param unknown $user
     * @param unknown $idx
     * @create_time 2018年1月24日
     */
    public function toslot($user, $idx) {
        //cmd=marry_hangup&op=toslot&grid_id=2&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=615ae395d1b6cdf762d6d8233d001b7e&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'marry_hangup';
        $params['op']             = 'toslot';
        $params['grid_id']        = $idx;
        $params['uid']            = $user['uid'];
        $params['uin']            = null;
        $params['skey']           = null;
        $params['h5openid']       = $user['h5openid'];
        $params['h5token']        = $user['h5token'];
        $params['pf']             = 'wx2';
        
        $result = Curl::dld($url, $params);
        if($result['code'] == 0){
            $data = $result['data'];
            $this->dealResult($data, $user['id']);
            if($data['result'] == '0'){
                Log::dld($user['id'], $data['msg']);
                return $data['bag'];
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    
    /**
     * 赠送
     * @param unknown $user
     * @param unknown $idx
     * @return Ambigous <>|boolean
     * @create_time 2018年1月24日
     */
    public function send($user, $idx) {
        //cmd=marry_hangup&op=send&grid_id=1&uid=769448&uin=null&skey=null&h5openid=oKIwA0aGacUIRZjEHNXgzQvT65CA&h5token=4d8d1ae5c30083b8f0cbc6f61f797545&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'marry_hangup';
        $params['op']             = 'send';
        $params['grid_id']        = $idx;
        $params['uid']            = $user['uid'];
        $params['uin']            = null;
        $params['skey']           = null;
        $params['h5openid']       = $user['h5openid'];
        $params['h5token']        = $user['h5token'];
        $params['pf']             = 'wx2';
    
        $result = Curl::dld($url, $params);
        if($result['code'] == 0){
            $data = $result['data'];
            $this->dealResult($data, $user['id']);
            if($data['result'] == '0'){
                Log::dld($user['id'], '装备赠送成功');
                return $data['bag'];
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    
    /**
     * 处理装备
     * @param unknown $user
     * @param unknown $bag
     * @create_time 2018年1月24日
     */
    private function _dealEquip($user, $bag) {
        $newBag = [];
        $arr1 = current($bag);
        if($arr1['cansend'] == 0 && $arr1['canequip'] == 0){
            $newBag = $this->ronglian($user, $arr1['grid_id']);
            if(count($newBag) > 0)$this->_dealEquip($user, $newBag);
        }elseif ($arr1['canequip'] == 1){
            $newBag = $this->toslot($user, $arr1['grid_id']);
            if(count($newBag) > 0)$this->_dealEquip($user, $newBag);
        }elseif ($arr1['cansend'] == 1){
            $newBag = $this->send($user, $arr1['grid_id']);
            if(count($newBag) > 0)$this->_dealEquip($user, $newBag);
        }
    }
    
    public function exchange($user) {
        //cmd=marry_hangup&op=exchange&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=0fe4d9abbe7bef974653b084ca18fc0a&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'marry_hangup';
        $params['op']             = 'exchange';
        $params['uid']            = $user['uid'];
        $params['uin']            = null;
        $params['skey']           = null;
        $params['h5openid']       = $user['h5openid'];
        $params['h5token']        = $user['h5token'];
        $params['pf']             = 'wx2';
        
        $result = Curl::dld($url, $params);
        if($result['code'] == 0){
            $data = $result['data'];
            $this->dealResult($data, $user['id']);
            if($data['result'] == '0'){
                Log::dld($user['id'], '兑换成功，获得：'.$data['point_award'].' 仙缘，'.$data['stone_award'].' 强化石'.(count($data['equip_award']) > 0 ? '，'.$data['equip_award']['name'] : ''));
                return $data['bag'];
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}