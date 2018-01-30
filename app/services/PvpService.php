<?php
namespace Services;

use Basic\BaseService;
use Library\Curl;
use Library\Log;
use Models\UserInfo;

class PvpService extends BaseService
{
    
    public function main($user)
    {
        (new BasicService())->getInfo($user);
        $userInfo = (new UserInfo())->getByUserId($user['id']);
        $userConfig = (new UserService())->getUserConfig($user['id']);
        
        if(!empty($userConfig['pvp_shop']))$this->bugGoods($user, $userConfig['pvp_shop']);
        
        //领取好友赠送体力
        if($userConfig['pvp_friend_vit'] == 1 && $userInfo['vit'] < 10){
            $result = $this->getFriendList($user, 0);
            if($result['getvit'] < $result['maxvit']){
                $this->sendFriendVit($user);
                $isSuccess = $this->getFriendVit($user);
                if($isSuccess === false)return false;
                (new BasicService())->getInfo($user);
                $userInfo = (new UserInfo())->getByUserId($user['id']);
            }
        }
        
        //自动使用体力药水
        if($userConfig['pvp_potion'] == 1 && $userInfo['vit'] < 10){
            $this->usedVitPotion($user);
            (new BasicService())->getInfo($user);
            $userInfo = (new UserInfo())->getByUserId($user['id']);
        }
        
        if($userConfig['pvp_potion'] == 0)return false;
        //优先好友战斗
        if($userInfo['vit'] < 10)return false;
        $type = 0;
        $result = $this->getFriendList($user, $type);
        if($result){
            foreach ($result['friendlist'] as $val) {
                if(abs($val['level']  - $userInfo['lvl']) > 5)continue;  //等级相差十五级，跳过
                if($userInfo['attack_power'] - $val['power'] < 4000)continue;  //战斗力不高于2000，则跳过
                if($val['can_fight'] != 1)continue;  //已战斗过，则跳过
            
                $res = $this->fight($user, $val, $type);
                if($res)$userInfo['vit'] = $userInfo['vit'] - 10;
                if($userInfo['vit'] < 10)return false;
            }
        }
        
        //再次帮友战斗
        if($userInfo['vit'] < 10)return false;
        $type = 1;
        $result = $this->getFriendList($user, $type);
        if($result){
            foreach ($result['friendlist'] as $val) {
                if(abs($val['level']  - $userInfo['lvl']) > 5)continue;  //等级相差十级，跳过
                if($userInfo['attack_power'] - $val['power'] < 4000)continue;  //战斗力不高于2000，则跳过
                if($val['can_fight'] != 1)continue;  //不能战斗，则跳过
            
                $res = $this->fight($user, $val, $type);
                if($res)$userInfo['vit'] = $userInfo['vit'] - 10;
                if($userInfo['vit'] < 10)return false;
            }
        }
        
        //最后斗友战斗
        if($userInfo['vit'] < 10)return false;
        $type = 2;
        $errorNum = 0;
        while ($userInfo['vit'] >= 10){
            $result = $this->getFriendList($user, $type);
            if($result){
                foreach ($result['friendlist'] as $val) {
                    if(abs($val['level']  - $userInfo['lvl']) > 5)continue;  //等级相差十级，跳过
                    if($userInfo['attack_power'] - $val['power'] < 4000)continue;  //战斗力不高于2000，则跳过
                    if($val['can_fight'] != 1)continue;  //不能战斗，则跳过
                
                    $res = $this->fight($user, $val, $type);
                    if($res)$userInfo['vit'] = $userInfo['vit'] - 10;
                }
            }else {
                if($errorNum >= 10)break;
                $errorNum++;
            }
        }
        
    }
    
    /**
     * 获得好友列表
     * @param unknown $user
     * @param unknown $type   2:斗友  0：好友  1：帮友
     * @create_time 2018年1月17日
     */
    public function getFriendList($user, $type) {
        //cmd=sns&op=query&needreload=1&type=2&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=87fb9342d130b34ad95d2218fee8e50a&pf=wx2
        //$type 2:斗友  0：好友  1：帮友
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'sns';
        $params['op']             = 'query';
        $params['needreload']     = 1;
        $params['type']           = $type;
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
                return $data;
            }else{
                return false;
            }
        }else {
            return false;
        }
    }
    
    /**
     * 战斗
     * @param unknown $user
     * @param unknown $friend
     * @param unknown $type
     * @create_time 2018年1月17日
     */
    public function fight($user, $friend, $type) {
        //cmd=sns&op=fight&target_uid=266328&type=0&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=4a343cb2a94f5da14434e05ee8fc2b4a&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'sns';
        $params['op']             = 'fight';
        $params['target_uid']     = $friend['uid'];
        $params['type']           = $type;
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
                if($data['first_win'] == 1)$this->shareFirstWin($user);
                
                foreach ($data['changed']['attrs'] as $val){
                    if($val['id'] == 'kFightPower'){  //TODO  升级增加战斗力
                        Log::dld($user['id'], "升级，增加{$val['num']}点战斗力");
                        (new BasicService())->getUpgradeInfo($user);
                    }
                }
                
                return true;
            }else{
                return false;
            }
        }else {
            return false;
        }
    }
    
    /**
     * 首胜分享
     * @param unknown $user
     * @create_time 2018年1月17日
     */
    public function shareFirstWin($user) {
        //uid=6084512&cmd=task&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=4a343cb2a94f5da14434e05ee8fc2b4a&uin=null&skey=null&pf=wx2&subcmd=Report&id=252&otherOpenid=&share_from=2&share_type=101
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'task';
        $params['subcmd']         = 'Report';
        $params['id']             = 252;
        $params['otherOpenid']    = '';
        $params['share_from']     = 2;
        $params['share_type']     = 101;
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
                $awards = '';
                foreach ($data['award']['items'] as $val){
                    $awards .= $val['num'].'个'.$val['name'].' ';
                }
                Log::dld($user['id'], '分享首胜，获得：'.$awards);
                return true;
            }else{
                Log::dld($user['id'], '首胜分享失败');
                return false;
            }
        }else {
            Log::dld($user['id'], '首胜分享失败');
            return false;
        }
    }
    
    /**
     * 使用能量药水
     * @param unknown $user
     * @create_time 2018年1月17日
     */
    public function usedVitPotion($user) {
        $potions = $this->getVitPotion($user);
        if($potions && count($potions) > 0){
            //cmd=storage&op=use&uid=6084512&id=100002&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=87fb9342d130b34ad95d2218fee8e50a&pf=wx2
            //{"result":0,"changed":{"attrs":[{"id":"kVit","num":30}],"items":[{"id":100002,"num":-1}]},"rodinfo":[{"name":"kRedDotFaction","flag":1},{"name":"kRedDotBagGlodNum","flag":1}]}
            $url = $this->_config->dldUrl->url;
            $params = [];
            $params['cmd']            = 'storage';
            $params['op']             = 'use';
            $params['uid']            = $user['uid'];
            $params['uin']            = null;
            $params['skey']           = null;
            $params['h5openid']       = $user['h5openid'];
            $params['h5token']        = $user['h5token'];
            $params['pf']             = 'wx2';
            foreach ($potions as $val) {
                $params['id']             = $val['Goods'];
                $num = ($val['limit'] > $val['Num']) ? $val['Num'] : $val['limit'];
                for($i=0;$i<$num;$i++) {
                    $result = Curl::dld($url, $params);
                    if($result['code'] == 0){
                        $data = $result['data'];
                        $this->dealResult($data, $user['id']);
                        if($data['result'] == '0'){
                            Log::dld($user['id'], "使用一个{$val['Name']}，获得 {$data['changed']['attrs'][0]['num']} 能量");
                        }else{
                            break;
                        }
                    }else{
                        break;
                    }
                }
            }
        }
    }
    
    /**
     * 获得今日可使用药水数量
     * @param unknown $user
     * @create_time 2018年1月17日
     */
    public function getVitPotion($user) {
        //cmd=limit&goodslist=100001|100002|100003&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=87fb9342d130b34ad95d2218fee8e50a&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'limit';
        $params['goodslist']      = '100001|100002|100003';
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
                $res = [];
                foreach ($data['limit_info'] as $val) {
                    if($val['Num'] > 0 && $val['limit'])$res[] = $val;
                }
                return $res;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    
    /**
     * 一键赠送体力
     * @param unknown $user
     * @create_time 2018年1月17日
     */
    public function sendFriendVit($user) {
        //cmd=sns&op=sendvit&target_uid=0&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=87fb9342d130b34ad95d2218fee8e50a&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'sns';
        $params['op']             = 'sendvit';
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
            Log::dld($user['id'], $data['msg']);
        }else{
            return false;
        }
    }
    
    /**
     * 一键领取体力
     * @param unknown $user
     * @return boolean
     * @create_time 2018年1月17日
     */
    public function getFriendVit($user) {
        //cmd=sns&op=getvit&target_uid=0&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=87fb9342d130b34ad95d2218fee8e50a&pf=wx2
        //{"result":0,"msg":"体力+130","vit":130,"changed":{"attrs":[{"id":"kVit","num":130}]},"rodinfo":[{"name":"kRedDotTask","flag":1},{"name":"kRedDotFaction","flag":1},{"name":"kRedDotBagGlodNum","flag":1}]}
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'sns';
        $params['op']             = 'getvit';
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
                Log::dld($user['id'], "一键领取体力成功：{$data['msg']}");
                return true;
            }else{
                Log::dld($user['id'], "一键领取体力失败：{$data['msg']}");
                return false;
            }
        }else{
            return false;
        }
    }
    
    public function bugGoods($user, $goods) {
        $shop = (new GoodsService())->pvp($user);
        $goods = explode(',', $goods);
        $prevNum = 0;
        foreach ($goods as $val){
            foreach($shop['goods'] as $v){
                if($val == $v['id']){
                    if($v['remain'] > 0 && $prevNum == 0){
                        $num = 0;
                        for ($i=1;$i<=$v['remain'];$i++){
                            if($v['price'] * $i <= $shop['winpoint'])$num = $i;
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
                Log::dld($user['id'], "胜点商店购买了 {$awards}");
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