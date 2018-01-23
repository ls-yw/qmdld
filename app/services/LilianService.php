<?php
namespace Services;

use Basic\BaseService;
use Library\Curl;
use Library\Log;

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
    
    public function index($user) {
        //cmd=mappush&subcmd=GetUser&uid=6084512&dup=0&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=0e0ee562b40bccb84818c817816ed7ba&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'mappush';
        $params['subcmd']         = 'GetUser';
        $params['dup']            = 0;
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
                if($data['energy'] > 0){
                    $curdup = $data['userinfo']['curdup'];
                    $curlevel = $data['userinfo']['info']['curlevel'];
                    $this->fight($user, $curdup, $curlevel);
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
    public function fight($user, $dup, $level)
    {
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
                Log::dld($user['id'], "{$dup}-{$level} {$data['msg']}".(!empty($reward) ? '获得'.$reward : ''));
                $this->index($user);
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}