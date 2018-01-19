<?php
namespace Services;

use Basic\BaseService;
use Library\Curl;
use Library\Log;

class MasterService extends BaseService
{
    /**
     * 主入口
     * @param unknown $user
     * @create_time 2018年1月18日
     */
    public function main($user) {
        $this->index($user);
    }
    
    /**
     * 师门首页信息获取
     * @param unknown $user
     * @create_time 2018年1月18日
     */
    public function index($user)
    {
        //cmd=mentor&uid=636428&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=bd69fcc8a839df769994e0bf963c05d4&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'mentor';
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
                $isMentor = ($data['mentor']['uid'] == $user['uid']) ? true : false;
                if(count($data['list']) > 0){
                    foreach ($data['list'] as $val) {
                        if($isMentor){  //是师傅
                            if($val['status'] == 1){  //已敬茶，去解惑
                                $this->disabuse($user, $val['uid']);
                            }
                        }else{  //是徒弟
                            if($val['uid'] == $user['uid'] && $val['status'] == 0){  //去敬茶
                                $this->tea($user);
                            }
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
     * 敬茶
     * @param unknown $user
     * @return boolean
     * @create_time 2018年1月18日
     */
    public function tea($user) {
        //cmd=mentor&op=tea&type=1&uid=636428&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=bd69fcc8a839df769994e0bf963c05d4&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'mentor';
        $params['op']             = 'tea';
        $params['type']           = 1;
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
                Log::dld($user['id'], '给师傅敬茶，'.$data['msg']);
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    
    /**
     * 给徒弟解惑
     * @param unknown $user
     * @param unknown $oppuid
     * @return boolean
     * @create_time 2018年1月18日
     */
    public function disabuse($user, $oppuid) {
        //cmd=mentor&op=disabuse&oppuid=1177730&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=d95ed3b9d33b8abd91cf8117733a66c4&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'mentor';
        $params['op']             = 'disabuse';
        $params['oppuid']         = $oppuid;
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
                Log::dld($user['id'], $data['msg']);
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}