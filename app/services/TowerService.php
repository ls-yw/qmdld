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
                $info['status']['qm'] = $data['tech']['tech_list']['3'];
                $info['status']['yy'] = $data['tech']['tech_list']['2'];
                $info['status']['jg'] = $data['tech']['tech_list']['1'];
                $info['status']['js'] = $data['tech']['tech_list']['0'];
                $info['revive'] = $data['baseInfo']['revive'];
                return $info;
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
    
}