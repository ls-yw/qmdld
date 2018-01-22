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
                if(count($data['faction_reddot']) <= 0)return false;
                foreach ($data['faction_reddot'] as $val) {
                    if($val['status'] != 1)continue;
                    if($val['id'] == 1){  //捐献
                        $this->donation($user);
                    }elseif($val['id'] == 2){  //武馆
                        $this->getclub($user);
                    }elseif($val['id'] == 3){  //洞穴
                        
                    }
                }
                (new UserInfo())->updateData(['fac_name'=>$data['faction_base']['name']], ['user_id'=>$user['id']]);
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
                $num = $data['fight_maxcount'] - $data['fight_count'];
                if($num <= 0)return false;
                $exp = 0;
                $club_type = 0;
                $j = 1;
                foreach ($data['club'] as $val) {
                    if($exp == 0 || $exp >= $val['exp'])$toId = $j;
                    $j++;
                }
                for($i=0;$i<$num;$i++){
                    $this->clubfight($user, 1);
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
}