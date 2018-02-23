<?php
namespace Services;

use Basic\BaseService;
use Library\Curl;
use Library\Log;
use Models\UserInfo;
use Phalcon\Mvc\Model;

class TowerService extends BaseService
{
    
    public function main($user) {
        $this->index($user);
    }
    
    public function index($user) {
        //cmd=tower&op=mainpage&needreload=1&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=9bec61e9874acfa4e43047ace8c407c3&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'tower';
        $params['op']             = 'mainpage';
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
                (new UserInfo())->updateData(['tower'=>$data['baseInfo']['layer'].'-'.$data['baseInfo']['barrier']], ['user_id'=>$user['id']]);
                if($data['giftInfo']['gift_status'] == 1)$this->getAward($user);
                $info = [];
                $info['status'][$data['tech']['tech_list']['0']['id']] = $data['tech']['tech_list']['0'];
                $info['status'][$data['tech']['tech_list']['1']['id']] = $data['tech']['tech_list']['1'];
                $info['status'][$data['tech']['tech_list']['2']['id']] = $data['tech']['tech_list']['2'];
                $info['status'][$data['tech']['tech_list']['3']['id']] = $data['tech']['tech_list']['3'];
                $info['status'][0]    = $data['tech']['point'];
                $info['maxStatus']    = $data['tech']['point'] + $data['tech']['tech_list']['0']['num'] + $data['tech']['tech_list']['1']['num'] + $data['tech']['tech_list']['2']['num'] + $data['tech']['tech_list']['3']['num'];
                $info['revive']       = $data['baseInfo']['revive'];
                $info['monster']      = $data['monsterInfo'];
                $info['attrStatus']   = (new BasicService())->getBaseStatus($user);
                $info['floor']        = $data['baseInfo']['layer'].'-'.$data['baseInfo']['barrier'];
                $info['alive']        = $data['baseInfo']['alive'];
                return $info;
            }else{
                return false;
            }
        }else {
            return false;
        }
    }
    
    /**
     * 挑战
     * @param unknown $user
     * @param unknown $index
     * @param unknown $floor
     * @return boolean
     * @create_time 2018年2月11日
     */
    public function fight($user, $index, $floor)
    {
        //cmd=tower&op=battle&needreload=1&index=0&uid=636428&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=a19e46f1c89d598a1d426e2901e7fbe2&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'tower';
        $params['op']             = 'battle';
        $params['needreload']     = 1;
        $params['index']          = $index;
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
                $index++;
                $rewards = $this->getAwardsName($data['award']);
                Log::dld($user['id'], "千层塔{$floor}，第{$index}个怪{$data['msg']} {$rewards}");
                return true;
            }else{
                return false;
            }
        }else {
            return false;
        }
    }
    
    public function getAward($user) {
        //cmd=tower&op=award&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=9bec61e9874acfa4e43047ace8c407c3&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'tower';
        $params['op']             = 'award';
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
                Log::dld($user['id'], "领取千层塔奖励：{$rewards}");
            }else{
                return false;
            }
        }else {
            return false;
        }
    }
    
    /**
     * 设置属性
     * @param unknown $user
     * @return boolean
     * @create_time 2018年2月11日
     */
    public function setTechtree($user, $value)
    {
        //cmd=tower&op=set_techtree&value=300030:0|300031:37|300032:0|300033:0&uid=636428&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=a19e46f1c89d598a1d426e2901e7fbe2&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'tower';
        $params['op']             = 'set_techtree';
        $params['value']          = $value;
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
                $content = '';
                foreach ($data['tech']['tech_list'] as $val){
                    $content .= "{$val['name']} {$val['num']} ";
                }
                $content .= "剩余 {$data['tech']['point']}";
                Log::dld($user['id'], "千层塔属性配置成功：{$content}");
                return true;
            }else{
                return false;
            }
        }else {
            return false;
        }
    }
    
    /**
     * 复活
     * @param unknown $user
     * @return boolean
     * @create_time 2018年2月12日
     */
    public function buylife($user)
    {
        //cmd=tower&op=buylife&type=free&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=f64aa3e87b8149ee1204598bbc7f5701&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'tower';
        $params['op']             = 'buylife';
        $params['type']           = 'free';
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
                if($data['baseInfo']['alive'] == 1){
                    Log::dld($user['id'], "复活成功");
                    return true;
                }
                return false;
            }else{
                return false;
            }
        }else {
            return false;
        }
    }
}