<?php
namespace Services;

use Basic\BaseService;
use Library\Curl;
use Library\Log;
use Models\UserInfo;

class OtherService extends BaseService
{
    /**
     * 获取72变
     * @param unknown $user
     * @create_time 2018年1月20日
     */
    public function getUnlockPage($user)
    {
        //cmd=unlock&op=page&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=631a1a5bfade688836a76af55e546566&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'unlock';
        $params['op']             = 'page';
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
                (new UserInfo())->updateData(['unlock_page'=>count($data['pet_actions']['action_ids']).'/120'], ['user_id'=>$user['id']]);
                foreach ($data['pet_actions']['gifts'] as $key => $val){
                    if($val == 1)$this->unlockPagePrize($user, $key);
                }
                return $data['pet_actions']['action_ids'];
            }
            return true;
        }
    }
    
    /**
     * 解锁72变
     * @param unknown $user
     * @return boolean
     * @create_time 2018年1月20日
     */
    public function goUnlockPage($user) {
        $actions = $this->getUnlockPage($user);
        $unlocked = [];
        if(count($actions) == 120){
            Log::dld($user['id'], '已全部解锁');
            return true;
        }
        if(count($actions) > 0){
            foreach ($actions as $val){
                if($val['status'] == 1){
                    $unlocked[] = $val['id'];
                }else if($val['status'] == 0){
                    $this->activeUnlockPage($user, $val['id']);
                }
            }
        }
        //cmd=unlock&uid=6084512&op=unlock&action_id=1&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=631a1a5bfade688836a76af55e546566&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'unlock';
        $params['op']             = 'unlock';
        $params['uid']            = $user['uid'];
        $params['uin']            = null;
        $params['skey']           = null;
        $params['h5openid']       = $user['h5openid'];
        $params['h5token']        = $user['h5token'];
        $params['pf']             = 'wx2';
        
        for($i=1;$i<=120;$i++){
            if(!in_array($i, $unlocked)){
                $params['action_id']  = $i;
                $result = Curl::dld($url, $params);
                
                if($result['code'] == 0){
                    $data = $result['data'];
                    $this->dealResult($data, $user['id']);
                    if($data['result'] == '0'){
                        $this->activeUnlockPage($user, $i);
                        Log::dld($user['id'], "动作{$i}解锁成功");
                    }else{
                        Log::dld($user['id'], "动作{$i}解锁失败：".$data['msg']);
                    }
                }
            }
        }
        $this->getUnlockPage($user);
    }
    
    /**
     * 激活72变
     * @param unknown $user
     * @param unknown $actionId
     * @create_time 2018年1月20日
     */
    public function activeUnlockPage($user, $actionId) {
        //cmd=unlock&op=active&action_id=74&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=922658a1ba88ed760e809cf5385d6f07&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'unlock';
        $params['op']             = 'active';
        $params['action_id']      = $actionId;
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
                
            }
            return true;
        }
    }
    
    /**
     * 领取72变动作解锁礼包
     * @param unknown $user
     * @param unknown $idx
     * @return boolean
     * @create_time 2018年1月20日
     */
    public function unlockPagePrize($user, $idx) {
        //cmd=unlock&op=award&gift_id=13&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=922658a1ba88ed760e809cf5385d6f07&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'unlock';
        $params['op']             = 'award';
        $params['gift_id']        = $idx;
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
                $award = $this->getAwardsName($data['changed']);
                Log::dld($user['id'], "领取72变动作解锁礼包：".$award);
            }
            return true;
        }
    }
    
    /**
     * 手册列表
     * @param unknown $user
     * @return Ambigous <multitype:, unknown>|boolean
     * @create_time 2018年1月20日
     */
    public function handbook($user) {
        //cmd=handbook&op=page&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=922658a1ba88ed760e809cf5385d6f07&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'handbook';
        $params['op']             = 'page';
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
                $lists = [];
                $attr = [];
                foreach ($data['lists'] as $val){
                    if($val['type'] == 1){  //场景
                        $attr['scene']['total'] = count($val['attrs']);
                        $attr['scene']['unlock'] = 0;
                        foreach ($val['attrs'] as $v){
                            if($v['status'] != 0){
                                $attr['scene']['unlock']++;
                                $lists['scene'][] = $v;
                            }
                        }
                        (new UserInfo())->updateData(['unlock_scene'=>$attr['scene']['unlock'].'/'.$attr['scene']['total']], ['user_id'=>$user['id']]);
                    }
                    if($val['type'] == 4){  //武器
                        $attr['weapon']['total']  = count($val['attrs']);
                        $attr['weapon']['unlock'] = 0;
                        $attr['weapon']['get']    = 0;
                        foreach ($val['attrs'] as $v){
                            if($v['status'] != 0){
                                $attr['weapon']['get']++;
                            }
                            if($v['status'] == 3){
                                $attr['weapon']['unlock']++;
                            }
                            $lists['weapon'][] = $v;
                        }
                        (new UserInfo())->updateData(['unlock_weapon'=>$attr['weapon']['unlock'].'/'.$attr['weapon']['get'].'/'.$attr['weapon']['total']], ['user_id'=>$user['id']]);
                    }
                    if($val['type'] == 5){  //技能
                        $attr['skill']['total']  = count($val['attrs']);
                        $attr['skill']['unlock'] = 0;
                        $attr['skill']['get']   = 0;
                        foreach ($val['attrs'] as $v){
                            if($v['status'] != 0){
                                $attr['skill']['get']++;
                            }
                            if($v['status'] == 3){
                                $attr['skill']['unlock']++;
                            }
                            $lists['skill'][] = $v;
                        }
                        (new UserInfo())->updateData(['unlock_skill'=>$attr['skill']['unlock'].'/'.$attr['skill']['get'].'/'.$attr['skill']['total']], ['user_id'=>$user['id']]);
                    }
                }
                return $lists;
            }
            return true;
        }
    }
    
    /**
     * 一键解锁场景
     * @param unknown $user
     * @create_time 2018年1月20日
     */
    public function onekeyScene($user) {
        $lists = $this->handbook($user);
        if(count($lists['scene']) <= 0){
            Log::dld($user['id'], "场景已全部解锁");
            return true;
        }
        foreach ($lists['scene'] as $val){
            if($val['status'] == 0){
                $res = $this->addHandbook($user, $val['type'], $val['id']);
                if($res)$val['status'] == 1;
            }
            if($val['status'] == 0){
                $this->unlockHandbook($user, $val['type'], $val['id']);
            }
        }
        $this->handbook($user);
    }
    
    /**
     * 激活手册
     * @param unknown $user
     * @param unknown $type
     * @param unknown $id
     * @create_time 2018年1月20日
     */
    public function addHandbook($user, $type, $id)
    {
        //cmd=handbook&op=add&type=1&id=30001&uid=636428&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=0fd0210e888953875f087c246b537c0a&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'handbook';
        $params['op']             = 'add';
        $params['type']           = $type;
        $params['id']             = $id;
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
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    
    /**
     * 解锁手册
     * @param unknown $user
     * @param unknown $type
     * @param unknown $id
     * @return boolean
     * @create_time 2018年1月20日
     */
    public function unlockHandbook($user, $type, $id) {
        //cmd=handbook&op=unlock&type=1&id=30001&uid=636428&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=0fd0210e888953875f087c246b537c0a&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'handbook';
        $params['op']             = 'unlock';
        $params['type']           = $type;
        $params['id']             = $id;
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
                Log::dld($user['id'], "场景{$id}解锁成功");
                $this->getHandbookPrize($user, $type, $id);
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    
    /**
     * 领取解锁场景礼包
     * @param unknown $user
     * @param unknown $type
     * @param unknown $id
     * @return boolean
     * @create_time 2018年1月20日
     */
    public function getHandbookPrize($user, $type, $id) {
        //cmd=handbook&op=award&type=1&id=30001&uid=636428&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=0fd0210e888953875f087c246b537c0a&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'handbook';
        $params['op']             = 'award';
        $params['type']           = $type;
        $params['id']             = $id;
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
                $award = $this->getAwardsName($data['award']);
                Log::dld($user['id'], "领取解锁{$id}场景礼包：".$award);
            }
            return true;
        }
    }
    
    /**
     * 一键分享武器
     * @param unknown $user
     * @create_time 2018年1月20日
     */
    public function onekeyWeapon($user) {
        $lists = $this->handbook($user);
        if(count($lists['weapon']) <= 0){
            Log::dld($user['id'], "武器已全部分享");
            return true;
        }
        foreach ($lists['weapon'] as $val){
            if(!is_array($val) || $val['status'] != 2)continue;
            if(substr($val['id'], -2) == '00')$val['id'] = substr($val['id'], 0, -2);
            $this->getWeaponReward($user, 4, $val['id'], $val['name']);
        }
        $this->handbook($user);
    }
    
    public function getWeaponReward($user, $type, $id, $name) {
        //uid=6084512&cmd=task&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=510617694df07b1a820f8b7a052830e4&uin=null&skey=null&pf=wx2&subcmd=Report&id=252&otherOpenid=&share_from=20&share_type=4&share_id=200033
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'task';
        $params['subcmd']         = 'Report';
        $params['otherOpenid']    = '';
        $params['share_from']     = '20';
        $params['share_type']     = $type;
        $params['share_id']       = $id;
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
                $award = $this->getAwardsName($data['award']);
                Log::dld($user['id'], "领取武器{$name}奖励：".$award);
            }
            return true;
        }
    }
    
    /**
     * 兑换码兑换
     * @param unknown $user
     * @param unknown $code
     * @return boolean
     * @create_time 2018年2月2日
     */
    public function exchangeCode($user, $code) {
        //cmd=secret&op=award&token=Ui68&uid=769448&uin=null&skey=null&h5openid=oKIwA0aGacUIRZjEHNXgzQvT65CA&h5token=85f193a3111a3df11b9c0c8e39cacabe&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'secret';
        $params['op']             = 'award';
        $params['token']          = $code;
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
                $award = $this->getAwardsName($data['award']);
                Log::dld($user['id'], "兑换成功：".$award);
                return true;
            }
        }
        return false;
    }
}