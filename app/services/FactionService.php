<?php
namespace Services;

use Basic\BaseService;
use Library\Curl;
use Library\Log;
use Models\UserInfo;

class FactionService extends BaseService
{
    
    public function main($user)
    {
        $userConfig = (new UserService())->getUserConfig($user['id']);
        if($userConfig['faction_club'] == 1)$this->getclub($user);
        $this->index($user);
    }
    
    /**
     * 帮派页面详情
     * @param unknown $user
     * @return boolean
     * @create_time 2018年1月18日
     */
    public function index($user) {
        //cmd=faction&op=query&need_member=1&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=4ebc6635a5410620d67ca76458097eb3&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'faction';
        $params['op']             = 'query';
        $params['need_member']    = 1;
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
                (new UserInfo())->updateData(['fac_name'=>$data['faction_base']['name'], 'fac_cave'=>(3-$data['user_faction']['fight_cave_times']).'/3'], ['user_id'=>$user['id']]);
                if(count($data['faction_reddot']) <= 0)return false;
                foreach ($data['faction_reddot'] as $val) {
                    if($val['status'] != 1)continue;
                    if($val['id'] == 1){  //捐献
                        $this->donation($user);
                    }elseif($val['id'] == 2){  //武馆
//                         $this->getclub($user);
                    }elseif($val['id'] == 3){  //洞穴
                        
                    }
                }
            }
            return true;
        }
    }
    
    /**
     * 武馆详情
     * @param unknown $user
     * @create_time 2018年1月18日
     */
    public function getclub($user) {
        //cmd=faction&op=getclub&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=4ebc6635a5410620d67ca76458097eb3&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'faction';
        $params['op']             = 'getclub';
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
                $exp = 0;
                $club_type = 0;
                $j = 0;
                foreach ($data['club'] as $val) {
                    if($exp == 0 || $exp >= $val['exp'])$club_type = $j;
                    if($val['lvl'] > $val['skill']['level'])$this->clubupskill($user, $club_type);
                    $j++;
                }
                $num = $data['fight_maxcount'] - $data['fight_count'];
                if($num <= 0)return false;
                for($i=0;$i<$num;$i++){
                    $this->clubfight($user, $club_type);
                }
            }
            return true;
        }
    }
    
    /**
     * 武馆战斗
     * @param unknown $user
     * @param unknown $club_type
     * @create_time 2018年1月18日
     */
    public function clubfight($user, $club_type) {
        //cmd=faction&op=clubfight&club_type=1&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=4ebc6635a5410620d67ca76458097eb3&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'faction';
        $params['op']             = 'clubfight';
        $params['club_type']      = $club_type;
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
                foreach ($data['award']['attrs'] as $val){
                    if($val['id'] == 'kContrib'){
                        $awards .= '帮贡 +'.$val['num'];
                    }elseif($val['id'] == 'kClubExp'){
                        $awards .= '经验 +'.$val['num'];
                    }
                }
                Log::dld($user['id'], '帮派武馆战斗，获得'.$awards);
            }
            return true;
        }
    }
    
    /**
     * 帮派捐赠
     * @param unknown $user
     * @create_time 2018年1月18日
     */
    public function donation($user) {
        //cmd=faction&op=donation&type=0&contrib=3000&uid=636428&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=f3ef24975cfb6598f7e8136ad0360948&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'faction';
        $params['op']             = 'donation';
        $params['type']           = 0;
        $params['contrib']        = 3000;
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
                foreach ($data['changed']['attrs'] as $val){
                    if($val['id'] == 'kContrib'){
                        $awards .= '帮贡 +'.$val['num'];
                    }
                }
                Log::dld($user['id'], '帮派普通捐赠：'.$awards.' 繁荣度 +'.$data['glory']);
            }
            return true;
        }
    }
    
    /**
     * 学习技能
     * @param unknown $user
     * @param unknown $idx
     * @create_time 2018年1月26日
     */
    public function clubupskill($user, $idx) {
        //cmd=faction&op=clubupskill&club_type=2&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=98a6a4dd4d405538b55da1156dedc53d&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'faction';
        $params['op']             = 'clubupskill';
        $params['club_type']      = $idx;
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
                Log::dld($user['id'], '学习技能成功');
            }
            return true;
        }
    }
    
    /**
     * 获取洞穴详情
     * @param unknown $user
     * @create_time 2018年1月31日
     */
    public function getCaveInfo($user) {
        //cmd=faction&op=cave_query&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=71fd4a590cad8b4da72fafaa76f0d058&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'faction';
        $params['op']             = 'cave_query';
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
                $list = ['sep'=>$data['cave']['seq']];
                
                $userInfo = (new UserInfo())->getByUserId($user['id']);
                $list['canFight'] = $userInfo['fac_cave'] == '0/3' ? false : true;
                
                $ordinary_status = false;
                foreach ($data['cave']['monster'] as $key => $val){
                    $info = false;
                    if($val['hp'] > 0)$info = $this->getCaveMonsterInfo($user, $data['cave']['seq'], $val['id']);
                    $list['ordinary'][$key]['id']           = $val['id'];
                    $list['ordinary'][$key]['name']         = $val['name'];
                    $list['ordinary'][$key]['hp']           = $val['hp'].'/'.$val['maxhp'];
                    $list['ordinary'][$key]['des_stauts']   = $info ? $info['des_stauts'] : '';
                    $list['ordinary'][$key]['des_weak']     = $info ? $info['des_weak'] : '';
                    
                    if($val['hp'] == 0){
                        $list['ordinary'][$key]['status'] = 0;
                    }else {
                        if(!$ordinary_status){
                            $list['ordinary'][$key]['status'] = 1;
                            $ordinary_status = true;
                        }else{
                            $list['ordinary'][$key]['status'] = 2;
                        }
                    }
                }
                
                foreach ($data['cave']['boss']['monster'] as $key => $val){
                    $info = false;
                    if($val['hp'] > 0)$this->getCaveMonsterInfo($user, $data['cave']['seq'], $val['id']);
                    $list['boss'][$key]['id']           = $val['id'];
                    $list['boss'][$key]['name']         = $val['name'];
                    $list['boss'][$key]['hp']           = $val['hp'].'/'.$val['maxhp'];
                    $list['boss'][$key]['des_stauts']   = $info ? $info['des_stauts'] : '';
                    $list['boss'][$key]['des_weak']     = $info ? $info['des_weak'] : '';
                    if($ordinary_status){
                        $list['boss'][$key]['status'] = 2;
                    }else {
                        if($val['hp'] == 0){
                            $list['boss'][$key]['status'] = 0;
                        }else{
                            $list['boss'][$key]['status'] = 1;
                        }
                    }
                }
                return $list;
            }
            return false;
        }
        return false;
    }
    
    /**
     * 获取洞穴怪物详情
     * @param unknown $user
     * @param unknown $sep
     * @param unknown $monster
     * @create_time 2018年1月31日
     */
    public function getCaveMonsterInfo($user, $sep, $monster) {
        //cmd=faction&op=cave_monster&seq=26&monster=2&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=834f8fbe7c559c6a296825114052f9bb&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'faction';
        $params['op']             = 'cave_monster';
        $params['seq']            = $sep;
        $params['monster']        = $monster;
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
                return $data['monster'];
            }
            return false;
        }
        return false;
    }
    
    /**
     * 洞穴战斗
     * @param unknown $user
     * @param unknown $sep
     * @param unknown $monster
     * @create_time 2018年1月31日
     */
    public function caveFight($user, $sep, $monster) {
        //params：cmd=faction&op=cave_fight&seq=5&monster=10&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=95a060c5665049fb29057c505c2376a0&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'faction';
        $params['op']             = 'cave_fight';
        $params['seq']            = $sep;
        $params['monster']        = $monster;
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
                $rewards = $this->getAwardsName($data['award']);
                $msg = '挑战帮派洞穴怪物成功！造成伤害：'.$data['deduct_hp'].' 剩下血量：'.$data['remain_hp'].' 获得 '.$rewards;
                Log::dld($user['id'], $msg);
                $this->index($user);
                return $msg;
            }else{
                $msg = '挑战帮派洞穴怪物：'.$data['msg'];
                Log::dld($user['id'], $msg);
                return $msg;
            }
            return false;
        }
        return false;
    }
}