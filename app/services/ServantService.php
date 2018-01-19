<?php
namespace Services;

use Basic\BaseService;
use Library\Curl;
use Library\Log;

class ServantService extends BaseService
{
    
    public function main($user)
    {
        $this->index($user);
    }
    
    /**
     * 家丁页面详情
     * @param unknown $user
     * @return boolean
     * @create_time 2018年1月18日
     */
    public function index($user) {
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
                    if($val['status'] == 3){  //有家丁在
                        $workTime = (int)substr(floor($val['work_time'] / 60), -1);
                        if($workTime >= 0 && $workTime < 3){
                            $this->getCash($user, $val['idx']);
                        }
                    }
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
}