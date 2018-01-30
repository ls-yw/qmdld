<?php
namespace Services;

use Basic\BaseService;
use Library\Curl;
use Library\Log;
use Models\LilianReward;
use Models\UserInfo;

class LilianService extends BaseService
{
    public function updateLilianConfig() {
        $url = $this->_config->dldUrl->staticUrl.$this->_config->dldUrl->lilian->config;
        $result = Curl::dld($url, '', 'get');
        if($result['code'] == 0){
            $data = json_decode($result['data'], true);
            print_r($result['data']);
        }else {
            return false;
        }
    }
    
    /**
     * 更新历练获取物品
     * @param unknown $user
     * @param unknown $dup
     * @return boolean
     * @create_time 2018年1月30日
     */
    public function updateLilianReward($user, $dup)
    {
        $initDup = $dup;
        for ($i=0;$i<3;$i++){
            for ($j=3;$j<=15;$j=$j+3){
                $info = $this->getLevelInfo($user, $dup, $j);
                if($info === false)return false;
                foreach ($info['goods'] as $v){
                    $where = ['good_id'=>$v['id'], 'dup'=>$initDup];
                    $row = (new LilianReward())->getOne($where);
                    if(!$row){
                        $data = $where;
                        $data['name'] = $v['name'];
                        (new LilianReward())->insertData($data);
                    }
                }
            }
            $dup++;
        }
    }
    
    /**
     * 获取关卡详情
     * @param unknown $user
     * @param unknown $dup
     * @param unknown $level
     * @create_time 2018年1月30日
     */
    public function getLevelInfo($user, $dup, $level)
    {
        //cmd=mappush&subcmd=EnterLevel&uid=6084512&dup=10005&level=3&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=135e507aa5f7819d3686ebcce8afe9f1&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'mappush';
        $params['subcmd']         = 'EnterLevel';
        $params['dup']            = $dup;
        $params['level']          = $level;
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
                return ['goods'=>$data['gift']['items'], 'times'=>$data['left_times']];
            }elseif($data['result'] == '110'){
                return false;
            }
        }else{
            return false;
        }
    }
    
    public function main($user)
    {
        $userConfig = (new UserService())->getUserConfig($user['id']);
        if($userConfig['lilian_used'] == 1)$this->useEnergy($user);
        if($userConfig['lilian_ordinary'] == 1){
            if($userConfig['lilian_ordinary_type'] == 3){
                $this->index($user, $userConfig, 1);
            }else{
                $this->index($user, $userConfig);
            }
        }
    }
    
    public function index($user, $userConfig, $dup=0) {
        //cmd=mappush&subcmd=GetUser&uid=6084512&dup=0&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=0e0ee562b40bccb84818c817816ed7ba&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'mappush';
        $params['subcmd']         = 'GetUser';
        $params['dup']            = $dup;
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
                foreach ($data['userinfo']['info']['giftstatus'] as $k => $v){
                    if($v == 1)$this->getFullStarReward($user, $data['userinfo']['thisdup'], ($k+1));
                }
                (new UserInfo())->updateData(['lilian_num'=>$data['energy']], ['user_id'=>$user['id']]);
                if($data['energy'] > 0){
                    $curdup = $data['userinfo']['curdup'];
                    $curlevel = $data['userinfo']['info']['curlevel'];
                    
                    if($userConfig['lilian_ordinary_type'] == 3){
                        foreach ($data['userinfo']['info']['levelmap'] as $key => $val){
                            if($val['star'] > 0 && $val['star'] < 3){
                                $this->fight($user, $data['userinfo']['thisdup'], $key);
                                return true;
                            }
                        }
                        if($data['userinfo']['thisdup'] < $data['userinfo']['curdup'])$this->index($user, $userConfig, ($dup+1));
                    }else{
                        $this->fight($user, $curdup, $curlevel);
                    }
                }
            }
            return true;
        }
    }
    
    /**
     * 自动使用江湖令礼包
     * @param unknown $user
     * @create_time 2018年1月23日
     */
    public function useEnergy($user) {
        $canUseEnergy = $this->getCanUseEnergy($user);
        if($canUseEnergy && count($canUseEnergy) > 0){
            //cmd=storage&op=use&uid=6084512&id=100015&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=0e0ee562b40bccb84818c817816ed7ba&pf=wx2
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
            foreach ($canUseEnergy as $val) {
                $params['id']             = $val['Goods'];
                $num = ($val['limit'] > $val['Num']) ? $val['Num'] : $val['limit'];
                for($i=0;$i<$num;$i++) {
                    $result = Curl::dld($url, $params);
                    if($result['code'] == 0){
                        $data = $result['data'];
                        $this->dealResult($data, $user['id']);
                        if($data['result'] == '0'){
                            Log::dld($user['id'], "使用一个{$val['Name']}，获得 {$data['changed']['attrs'][0]['num']} {$data['changed']['attrs'][0]['name']}");
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
     * 获取可使用的江湖令礼包
     * @param unknown $user
     * @create_time 2018年1月23日
     */
    public function getCanUseEnergy($user) {
        //cmd=limit&goodslist=100015&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=0e0ee562b40bccb84818c817816ed7ba&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'limit';
        $params['goodslist']      = 100015;
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
     * 历练
     * @param unknown $user
     * @param unknown $dup
     * @param unknown $level
     * @create_time 2018年1月23日
     */
    public function fight($user, $curdup, $curlevel, $type='pt')
    {
        $userConfig = (new UserService())->getUserConfig($user['id']);
        $dup   = $curdup;
        $level = $curlevel;
        if($type == 'pt' && $userConfig['lilian_ordinary_type'] == 2){
            $dup   = ($curlevel == 1) ? $curdup -1 : $curdup;
            $level = ($curlevel == 1) ? 15 : $curlevel - 1;
        }
        
        //cmd=mappush&subcmd=DoPk&uid=6084512&dup=13&level=14&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=0e0ee562b40bccb84818c817816ed7ba&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'mappush';
        $params['subcmd']         = 'DoPk';
        $params['dup']            = $dup;
        $params['level']          = $level;
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
                $numKey = $type == 'pt' ? 'energy' : 'high_energy';
                Log::dld($user['id'], "{$dup}-{$level} {$data['msg']}".(!empty($data['star']) ? "{$data['star']}星。" : '').(!empty($reward) ? '获得'.$reward.' 还剩'.$data[$numKey].'次机会' : ''));
                
                $fun = $type == 'pt' ? 'index' : 'heroIndex';
                if($data['win'] == 0){
                    $this->fight($user, $curdup, $curlevel, $type);
                }else{
                    if($type == 'yx' && $userConfig['lilian_hero_ordinary_type'] == 2){
                        $this->fight($user, $curdup, $curlevel, $type);
                        return true;
                    }
                    $this->{$fun}($user,$userConfig, $dup);
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    
    /**
     * 领取满星奖励
     * @param unknown $user
     * @param unknown $dup
     * @param unknown $star
     * @return boolean
     * @create_time 2018年1月29日
     */
    public function getFullStarReward($user, $dup, $star) {
        //cmd=mappush&subcmd=GetPrize&uid=636428&star=1&dup=11&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=9324e3cfa02b162af01ad1a4de3c5c8f&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'mappush';
        $params['subcmd']         = 'GetPrize';
        $params['dup']            = $dup;
        $params['star']           = $star;
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
                Log::dld($user['id'], "历练领取 {$dup}关第{$star}个礼包： {$data['msg']}");
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    
    public function heroMain($user) {
        $userConfig = (new UserService())->getUserConfig($user['id']);
        if($userConfig['lilian_hero_ordinary'] == 1){
            if($userConfig['lilian_hero_ordinary_type'] == 3){
                $this->index($user, $userConfig, 10001);
            }else{
                $this->heroIndex($user, $userConfig);
            }
        }
    }
    
    public function heroIndex($user, $userConfig, $dup=10000) {
        //cmd=mappush&subcmd=GetUser&uid=6084512&dup=10000&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=702945682f01b9a2043ec4cd70bdcd0c&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'mappush';
        $params['subcmd']         = 'GetUser';
        $params['dup']            = $dup;
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
                foreach ($data['userinfo']['info']['giftstatus'] as $k => $v){
                    if($v == 1)$this->getFullStarReward($user, $data['userinfo']['thisdup'], ($k+1));
                }
                
                (new UserInfo())->updateData(['hero_lilian_num'=>$data['high_energy']], ['user_id'=>$user['id']]);
                if($data['high_energy'] > 0){
                    $curdup = $data['userinfo']['curdup'];
                    $curlevel = $data['userinfo']['info']['curlevel'];
    
                    if($userConfig['lilian_hero_ordinary_type'] == 3){
                        foreach ($data['userinfo']['info']['levelmap'] as $val){
                            if($val['star'] > 0 && $val['star'] < 3){
                                $this->fight($user, $curdup, $curlevel, 'yx');
                                return true;
                            }
                        }
                        if($data['userinfo']['thisdup'] < $data['userinfo']['curdup'])$this->heroIndex($user, $userConfig, ($dup+1));
                    }elseif($userConfig['lilian_hero_ordinary_type'] == 2){
                        if(empty($userConfig['lilian_hero_ordinary_goods'])){
                            Log::dld($user['id'], "未设置英雄历练物品");
                            return false;
                        }
                        $dup   = ($curlevel == 3) ? $curdup -1 : $curdup;
                        $level = ($curlevel == 3) ? 15 : $curlevel - 3;
                        $wantRewards = explode(',', $userConfig['lilian_hero_ordinary_goods']);
                        for($i=$dup;$i>10000;$i--){
                            if($i != $dup)$level = 15;
                            for ($j=$level;$j>=3;$j=$j-3){
                                $info = $this->getLevelInfo($user, $i, $j);
                                if($info === false)return false;
                                if(empty($info['goods']) || $info['times'] <= 0)continue;
                                foreach ($info['goods'] as $vv){
                                    if(in_array($vv['id'], $wantRewards)){
                                        $this->fight($user, $i, $j, 'yx');
                                        break;
                                    }
                                }
                            }
                        }
                    }else{
                        $this->fight($user, $curdup, $curlevel, 'yx');
                    }
                }
            }
            return true;
        }
    }
}