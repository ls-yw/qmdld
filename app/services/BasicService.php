<?php
namespace Services;

use Basic\BaseService;
use Library\Curl;
use Library\Log;
use Models\UserInfo;
use Library\Redis;
use Models\Goods;

class BasicService extends BaseService
{
    public function auth($openid) {
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'index';
        $params['uid']            = '';
        $params['inviter']        = 0;
        $params['wxcode']         = '';
        $params['from']           = 0;
        $params['inviter_openid'] = '';
        $params['recaller']       = '';
        $params['h5channel']      = 10027566;
        $params['uin']            = null;
        $params['skey']           = null;
        $params['h5openid']       = $openid;
        $params['h5token']        = 12345;
        $params['pf']             = 'wx2';
        
        $result = Curl::dld($url, $params);
        if($result['code'] == 0){
            $data = $result['data'];
            $this->dealResult($data);
            if($data['result'] == '0'){
                Log::dld($this->_user, '鉴权', '鉴权成功');
                return false;
            }
        }
        print_r($result);
    }
    
    public function updateAll($user)
    {
        $this->getInfo($user);
        (new OtherService())->getUnlockPage($user);
        (new OtherService())->handbook($user);
    }
    
    /**
     * 更新用户数据
     * 
     * @create_time 2018年1月11日
     */
    public function getInfo($user) {
        if(empty($user['h5token']))return false;
        //cmd=index&lvlup=1&uid=636428&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=bd69fcc8a839df769994e0bf963c05d4&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'index';
        $params['lvlup']          = 0;
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
                $userInfoData = [];
                $userInfoData['name']         = urldecode($data['name']);
                $userInfoData['lvl']          = $data['lvl'];
                $userInfoData['headimgurl']   = $data['headimgurl'];
                $userInfoData['exp']          = $data['exp'];
                $userInfoData['max_exp']      = $data['max_exp'];
                $userInfoData['vip_lvl']      = $data['vip_lvl'];
                $userInfoData['vit']          = $data['vit'];
                $userInfoData['sex']          = $data['sex'];
                $userInfoData['fac_id']       = $data['facid'];
                $userInfoData['invite_num']   = $data['invite_num'];
                $userInfoData['attack_power'] = $data['attack_power'];
                $userInfoData['marry_status'] = $data['marry_status'];
                $userInfoData['invite_num']   = $data['invite_num'];
                $userInfoData['douyu_num']    = $data['douyu_num'];
                $userInfoData['doubi_num']    = $data['doubi_num'];
                $userInfoData['servant_cash'] = $data['servant_cash'];
                $userInfoData['king_medal']   = $data['king_medal'];
                $userInfoData['prestige']     = $data['prestige'];
                $userInfoData['spirit']       = $data['spirit'];
                $userInfoData['winpoint']     = $data['winpoint'];
                $userInfoData['login_days']   = $data['login_days'];
                $userInfoData['mentor_uid']   = $data['mentor_uid'];
                $userInfoData['uid']          = $user['uid'];
                
                $userInfo = (new UserInfo())->getByUserId($user['id']);
                if($userInfo){
                    $row = (new UserInfo())->updateData($userInfoData, ['user_id'=>$user['id']]);
                }else{
                    $userInfoData['user_id']      = $user['id'];
                    $row = (new UserInfo())->insertData($userInfoData);
                }
                (new GoodsService())->updateShops($user);
                $row ? Log::dld($user['id'], '更新用户信息成功') : Log::dld($user['id'], '更新用户信息失败');
                return true;
            }
        }
    }
    
    /**
     * 获取任务列表，并去完成
     * @param unknown $user
     * @create_time 2018年1月16日
     */
    public function getTasks($user)
    {
        //cmd=task&needreload=1&uid=6084512&subcmd=GetUser&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=dd8849d86713620b3574ac241e245367&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'task';
        $params['subcmd']         = 'GetUser';
        $params['needreload']     = 1;
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
                $userInfoTasks = $data['userinfo'];
                
                while ($userInfoTasks && ($userInfoTasks['exchange_reddot'] == 1 || $userInfoTasks['main_reddot'] == 1)){
                    if($userInfoTasks['exchange_reddot'] == 1){  //神器
                        foreach ($userInfoTasks['exchangetask'] as $val2){
                            if($val2['status'] == 1){
                                $userInfoTasks = $this->finshedTask($user, $val2);
                            }
                        }
                    }elseif($userInfoTasks['main_reddot'] == 1){  //主线任务
                        foreach ($userInfoTasks['maintask'] as $val3){
                            if($val3['status'] == 1){
                                $userInfoTasks = $this->finshedTask($user, $val3);
                            }
                        }
                    }
                }
                
                //领取活跃值宝箱
                if($userInfoTasks['daily_reddot'] == 1){
                    foreach ($userInfoTasks['activegift'] as $v){
                        if($v['status'] == 1){
                            $this->finshedTask($user, $v);
                        }
                    }
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    
    /**
     * 完成任务
     * @param unknown $user
     * @param unknown $task
     * @return boolean|Ambigous <number, string, number, mixed, number, NULL>
     * @create_time 2018年1月16日
     */
    public function finshedTask($user, $task)
    {
        if(empty($task))return false;
        //cmd=task&uid=6084512&subcmd=GetPrize&id=1967&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=dd8849d86713620b3574ac241e245367&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'task';
        $params['subcmd']         = 'GetPrize';
        $params['id']             = $task['id'];
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
                Log::dld($user['id'], "完成任务：{$task['name']}");
                Log::dld($user['id'], $data['msg']);
                return $data['userinfo'];
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    
    /**
     * 获取小红点
     * @param unknown $user
     * @create_time 2018年1月16日
     */
    public function reddot($user) {
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'reddot';
        $params['subcmd']         = 'Get';
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
                $rodinfo = [];
                if(!empty($data['rodinfo']))$rodinfo = $data['rodinfo'];
                if(!empty($data['rodinfo2']))$rodinfo = (empty($rodinfo) ? $data['rodinfo2'] : array_merge($rodinfo, $data['rodinfo2']));
                if(!empty($rodinfo)){
                    foreach ($rodinfo as $val){
                        if($val['name'] == 'kRedDotTask'){  //有可领任务
                            $this->getTasks($user);
                        }
                        if ($val['name'] == 'kRedDotAddDesk'){  //闹钟 ? 便当？
                            
                        }
                        if ($val['name'] == 'kRedDotMail'){  //邮件
                            (new MailService())->main($user);
                        }
                        if ($val['name'] == 'kRedDotFaction'){  //群英会
                            $this->wulin($user);
                        }
                    }
                }
            }
            if($data['result'] == '110'){
                return false;
            }
            return true;
        }
    }
    
    /**
     * 获取上一级上级得的奖励
     * @param unknown $user
     * @return boolean
     * @create_time 2018年1月19日
     */
    public function getUpgradeInfo($user) {
        //cmd=detail&op=levelup&uid=636428&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=bd69fcc8a839df769994e0bf963c05d4&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'detail';
        $params['op']             = 'levelup';
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
                $name = isset($data['levelup'][0]['status']) ? $data['levelup'][0]['status']['name'] : (isset($data['levelup'][0]['weapon']) ? $data['levelup'][0]['weapon']['name'] : $data['levelup'][0]['skill']['name']);
                Log::dld($user['id'], "获得 ".$name);
            }
            return true;
        }
    }
    
    /**
     * 查看武林大会
     * @param unknown $user
     * @create_time 2018年1月19日
     */
    public function wulin($user) {
        //cmd=wulin&op=query&sec_id=0&needreload=1&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=3b13133d248b664ef35e1033d19bcfda&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'wulin';
        $params['op']             = 'query';
        $params['sec_id']         = 0;
        $params['needreload']     = 1;
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
                if($data['arena_id'] == 0 && $data['goodsnum'] > 0)$this->wulinEntered($user);
            }
            return true;
        }
    }
    
    /**
     * 报名武林大会
     * @param unknown $user
     * @return boolean
     * @create_time 2018年1月19日
     */
    public function wulinEntered($user) {
        //cmd=wulin&op=signup&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=3b13133d248b664ef35e1033d19bcfda&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'wulin';
        $params['op']             = 'signup';
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
                $key = 'wulin_sign_'.date('Ymd').'_'.$user['id'];
                Redis::getInstance()->setex($key, 86400, 1);
                Log::dld($user['id'], "武林大会 ".$data['msg']);
            }
            return true;
        }
    }
    
    /**
     * 获取状态详情
     * @param unknown $user
     * @create_time 2018年1月31日
     */
    public function getStatus($user) {
        //cmd=detail&op=status&needreload=1&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=3480bb77a97af44c7e0f8a6f0c579530&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'detail';
        $params['op']             = 'status';
        $params['needreload']     = 1;
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
                return $data['status_info'];
            }
            return false;
        }
    }
    
    /**
     * 增加状态
     * @param unknown $user
     * @param unknown $id
     * @create_time 2018年1月31日
     */
    public function addStatus($user) {
        $status = $this->getStatus($user);
        foreach ($status as $val){
            if($val['shop_id'] == 0)continue;
            if(in_array($val['shop_id'], [100011, 100012, 100013, 100014]) && $val['has'] == 0){
                $this->useGood($user, $val['shop_id']);
            }
        }
    }
    
    /**
     * 使用商品
     * @param unknown $user
     * @param unknown $id
     * @create_time 2018年1月31日
     */
    public function useGood($user, $id) {
        //cmd=storage&op=use&id=100021&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=3480bb77a97af44c7e0f8a6f0c579530&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'storage';
        $params['op']             = 'use';
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
                $reward = $this->getAwardsName($data['award']);
                $good = (new Goods())->getById($id);
                $msg = '使用了一个';
                $msg .= $good ? $good['name'] : '';
                $msg .= '获得'.$reward;
                Log::dld($user['id'], $msg);
            }
            return false;
        }
    }
}