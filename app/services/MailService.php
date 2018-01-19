<?php
namespace Services;

use Basic\BaseService;
use Library\Curl;
use Library\Log;

class MailService extends BaseService
{
    public function main($user)
    {
        $list = $this->getList($user, 2);
        if(!$list || count($list) <= 0)return false;
        foreach ($list as $val) {
            $this->getPrize($user, $val);
        }
    }
    
    /**
     * 获得活动列表，有小红点的对应去参加
     * @param unknown $user
     * @return boolean
     * @create_time 2018年1月16日
     */
    public function getList($user, $type)
    {
        //cmd=mail&op=mailist&type=2&needreload=1&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=d95ed3b9d33b8abd91cf8117733a66c4&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'mail';
        $params['op']             = 'mailist';
        $params['type']           = $type;
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
                $list = [];
                foreach ($data['maillist'] as $val)
                {
                    if(isset($val['reward']) && $val['reward'] == 1){
                        $list[] = $val;
                    }
                }
                return $list;
            }else{
                return false;
            }
        }else {
            return false;
        }
    }
    
    /**
     * 领取奖励
     * @param unknown $user
     * @param unknown $mail
     * @create_time 2018年1月18日
     */
    public function getPrize($user, $mail)
    {
        //cmd=mail&uid=6084512&op=getreward&id=16&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=e25925bc1a617639f67223e686b2167b&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'mail';
        $params['op']             = 'getreward';
        $params['id']             = $mail['id'];
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
                $awards = $this->getAwardsName($data['award']);
                Log::dld($user['id'], "领取邮件 {$mail['title']}奖励：".$awards);
            }else{
                return false;
            }
        }else {
            return false;
        }
    }
}