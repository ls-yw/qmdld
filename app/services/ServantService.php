<?php
namespace Services;

use Basic\BaseService;
use Library\Curl;
use Library\Log;
use Models\UserInfo;

class ServantService extends BaseService
{
    
    public function main($user)
    {
        $userConfig = (new UserService())->getUserConfig($user['id']);
        if(!empty($userConfig['servant_shop']))$this->bugGoods($user, $userConfig['servant_shop']);
        $this->index($user, $userConfig);
    }
    
    /**
     * 家丁页面详情
     * @param unknown $user
     * @return boolean
     * @create_time 2018年1月18日
     */
    public function index($user, $userConfig) {
        //cmd=servant&uid=6084512&target_uid=0&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=4ebc6635a5410620d67ca76458097eb3&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'servant';
        $params['target_uid']     = 0;
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
                foreach ($data['servant_array'] as $val){
                    if($val['status'] == 3 && ($userConfig['servant_auto'] == 1)){  //有家丁在  或  家丁逃走
                        $workTime = (int)substr(floor($val['work_time'] / 60), -1);
                        if($workTime >= 0 && $workTime < 2){
                            $this->getCash($user, $val['idx']);
                        }
                    }
                    if($val['status'] == 4 && $userConfig['servant_auto'] == 1){  //家丁逃走
                        $this->getCash($user, $val['idx']);
                    }
                    if($val['status'] == 2 && $userConfig['servant_catch'] == 1){  //有坑，可以抓家丁
                        $this->catchMain($user);
                    }
                    if($userConfig['servant_rob'] == 1 && ($data['cash_rob_got'] < $data['cash_rob_max'] || $data['exp_rob_got'] < $data['exp_rob_max']) && $data['rob_times'] < $data['rob_max_times']){  //捣乱
                        $this->robMain($user);
                    }
                    if($userConfig['servant_train'] == 1 && $data['exp_train_got'] < $data['exp_train_max']){  //训练
                        $this->train($user);
                    }
                    if(isset($data['level']) && ($data['level'] - $val['level']) > 5 && $userConfig['servant_release'] == 1 ){
//                         $this->release($user, $val);
                    }
                }
                if($data['owner_uid'] > 0){
                    Log::dld($user['id'], "你被 ".urldecode($data['owner_name'])." 抓啦，对方等级：{$data['owner_level']}，战斗力：{$data['owner_attack_power']}");
                }
            }
            return true;
        }
    }
    
    /**
     * 收获家财
     * @param unknown $user
     * @param unknown $idx
     * @create_time 2018年1月18日
     */
    public function getCash($user, $idx)
    {
        //cmd=servant&uid=6084512&op=reward&idx=2&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=4ebc6635a5410620d67ca76458097eb3&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'servant';
        $params['op']             = 'reward';
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
                Log::dld($user['id'], urldecode($data['msg']));
            }
            return true;
        }
    }
    
    /**
     * 释放家丁
     * @param unknown $user
     * @param unknown $idx
     * @create_time 2018年1月25日
     */
    public function release($user, $servant) {
        ;
    }
    
    /**
     * 自动捕捉家丁
     * @param unknown $user
     * @return boolean
     * @create_time 2018年1月25日
     */
    public function catchMain($user)
    {
        $userInfo = (new UserInfo())->getByUserId($user['id']);
        $jiading = [];
        while (count($jiading) == 0){
            $jiading = $this->catchList($user, $userInfo);
            if($jiading === false)return false;
        }
        if(empty($jiading))return false;
        $this->catchServant($user, $jiading);
    }
    
    /**
     * 获取可捕捉家丁  家丁战斗力低于己方2000
     * @param unknown $user
     * @param unknown $userInfo
     * @return Ambigous <multitype:, unknown>|boolean
     * @create_time 2018年1月25日
     */
    public function catchList($user, $userInfo)
    {
        //cmd=servant&uid=6084512&op=teahouse&target_uid=0&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=d9d68fb445a806ad2f90b8dae426d4bf&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'servant';
        $params['op']             = 'teahouse';
        $params['target_uid']     = 0;
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
                $arr = [];
                foreach ($data['player_array'] as $key => $val){
                    $val['power'] = isset($val['owner_attack_power']) && $val['owner_attack_power'] > 0 ? $val['owner_attack_power'] : $val['attack_power'];
                    $val['idx'] = $key;
                    if(empty($arr) || $arr['power'] > $val['power'])$arr = $val;
                }
                return ($userInfo['attack_power'] - $arr['power'] > 2000) ? $arr : [];
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    
    /**
     * 捕捉家丁
     * @param unknown $user
     * @create_time 2018年1月25日
     */
    public function catchServant($user, $servant) {
        //cmd=servant&uid=6084512&op=arrest&idx=2&target_uid=915775&target_owner=342378&charge=0&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=d9d68fb445a806ad2f90b8dae426d4bf&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'servant';
        $params['op']             = 'arrest';
        $params['idx']            = $servant['idx'];
        $params['target_uid']     = $servant['uid'];
        $params['target_owner']   = $servant['owner_uid'];
        $params['charge']         = 0;
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
                if($data['win'] == 1){
                    Log::dld($user['id'], "抓到  ".urldecode($servant['name']).' 来当家丁');
                }else{
                    Log::dld($user['id'], "抓捕家丁 ".urldecode($servant['name']).' 失败');
                    $this->catchServant($user, $servant);
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    
    /**
     * 训练
     * @param unknown $user
     * @create_time 2018年1月25日
     */
    public function train($user)
    {
        //cmd=servant&uid=6084512&op=train&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=d9d68fb445a806ad2f90b8dae426d4bf&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'servant';
        $params['op']             = 'train';
        $params['charge']         = 0;
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
                Log::dld($user['id'], "训练   ".$data['msg']);
                if($data['exp_got'] < $data['exp_train_max'])$this->train($user);
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    
    public function robMain($user) {
        $userInfo = (new UserInfo())->getByUserId($user['id']);
        $jiading = [];
        while (count($jiading) == 0){
            $jiading = $this->robList($user, $userInfo);
            if($jiading === false)return false;
        }
        if(empty($jiading))return false;
        $this->rob($user, $jiading);
    }
    
    /**
     * 待捣乱列表
     * @param unknown $user
     * @param unknown $userInfo
     * @return Ambigous <multitype:, unknown>|boolean
     * @create_time 2018年1月25日
     */
    public function robList($user, $userInfo) {
        //cmd=servant&uid=636428&op=viewrob&target_uid=0&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=e0f01e77802711928e1eaf1d808fab1f&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'servant';
        $params['op']             = 'viewrob';
        $params['target_uid']     = 0;
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
                $arr = [];
                foreach ($data['player_array'] as $key => $val){
                    if($val['cash_rob'] < 150)continue;
                    if(empty($arr) || $arr['attack_power'] > $val['attack_power'])$arr = $val;
                }
                return !empty($arr) && ($userInfo['attack_power'] - $arr['attack_power'] > 5000) ? $arr : [];
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    
    /**
     * 捣乱
     * @param unknown $user
     * @param unknown $servant
     * @create_time 2018年1月25日
     */
    public function rob($user, $servant) {
        //cmd=servant&uid=636428&op=rob&target_uid=7441448&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=e0f01e77802711928e1eaf1d808fab1f&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'servant';
        $params['op']             = 'rob';
        $params['target_uid']     = $servant['uid'];
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
                if($data['win'] == 0 && ($data['rob_times'] +1) < $data['rob_max_times']){
                    $this->rob($user, $servant);
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    
    public function bugGoods($user, $goods) {
        $shop = (new GoodsService())->servant($user);
        $goods = explode(',', $goods);
        $prevNum = 0;
        foreach ($goods as $val){
            foreach($shop['goods'] as $v){
                if($val == $v['id']){
                    if($v['remain'] > 0 && $prevNum == 0){
                        $num = 0;
                        for ($i=1;$i<=$v['remain'];$i++){
                            if($v['price'] * $i <= $shop['servant_cash'])$num = $i;
                        }
                        if($num == 0)return false;
                        $res = $this->bug($user, $val, $num, $v['price']*$num);
                        $prevNum = $res ? $v['remain'] - $num : $v['remain'];
                    }
                }
            }
        }
    
    }
    
    /**
     * 购买
     * @param unknown $user
     * @param unknown $id
     * @param unknown $num
     * @param unknown $prize
     * @create_time 2018年1月26日
     */
    public function bug($user, $id, $num, $prize) {
        //cmd=shop&subtype=1&num=1&id=100023&price=20&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=dd63c541dc64fc41a05909a0466d753f&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'shop';
        $params['subtype']        = 1;
        $params['num']            = $num;
        $params['id']             = $id;
        $params['price']          = $prize;
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
                unset($data['changed']['attrs']);
                $awards = $this->getAwardsName($data['changed']);
                Log::dld($user['id'], "家财商店购买了 {$awards}");
                return true;
            }else{
                Log::dld($user['id'], $data['msg']);
                return false;
            }
        }else{
            return false;
        }
    }
}