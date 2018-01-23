<?php
namespace Services;

use Basic\BaseService;
use Library\Curl;
use Library\Log;

class MarryHangupService extends BaseService
{
    
    public function main($user)
    {
        $this->index($user, true);
    }
    
    public function index($user, $autoFight=false) {
        //cmd=marry_hangup&op=query&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=74c930322d4f66819dd0800940743c24&pf=wx2;
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'marry_hangup';
        $params['op']             = 'query';
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
                Log::dld($user['id'], "获得仙缘 x {$data['account']['point_award']}，装备 x ".count($data['account']['equip_award'])."，宝箱 x ".count($data['account']['box_award']));
                if(count($data['slots']) > 0){  //背包
                    $this->getBag($user);
                }
                if(count($data['selfbox']) > 0){  //我的宝箱
                    foreach ($data['selfbox'] as $val){
                        if($val['locked'] == 0)$this->getBox($user, $val['idx'], 0);
                    }
                }
                if(count($data['oppbox']) > 0){  //对方的宝箱
                    if($val['locked'] == 1)$this->getBox($user, $val['idx'], 1);
                }
                if($data['encourage'] == 0){
                    
                }
                if($data['fight'] < $data['maxfight'] && $autoFight){
                    Log::dld($user['id'], '开始挑战仙缘历练');
                    for($i=$data['fight'];$i<$data['maxfight'];$i++){
                        $this->fight($user);
                    }
                }
            }
            return true;
        }
    }
    
    /**
     * 获取背包详情
     * @param unknown $user
     * @create_time 2018年1月23日
     */
    public function getBag($user)
    {
        //cmd=marry_hangup&op=bag_query&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=74c930322d4f66819dd0800940743c24&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'marry_hangup';
        $params['op']             = 'bag_query';
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
                $i = 0;
                foreach ($data['bag'] as $val){
                    if($val['cansend'] == 0 && $val['canequip'] == 0){
                        $i = $val['grid_id'];
                    }
                }
                while ($i && $i >0){
                    $bags = $this->ronglian($user, $i);
                    if($bags){
                        foreach ($bags as $v){
                            if($v['cansend'] == 0 && $v['canequip'] == 0){
                                $i = $v['grid_id'];
                                break;
                            }
                        }
                    }else{
                        $i = 0;
                    }
                }
            }
            return true;
        }
    }
    
    /**
     * 熔炼
     * @param unknown $user
     * @param unknown $idx
     * @create_time 2018年1月23日
     */
    public function ronglian($user, $idx)
    {
        //cmd=marry_hangup&op=ronglian&grid_id=2&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=74c930322d4f66819dd0800940743c24&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'marry_hangup';
        $params['op']             = 'ronglian';
        $params['grid_id']        = $idx;
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
                return $data['bag'];
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    
    /**
     * 打开宝箱
     * @param unknown $user
     * @param unknown $idx
     * @create_time 2018年1月23日
     */
    public function getBox($user, $idx, $type) {
        //cmd=marry_hangup&op=unlock&type=0&idx=1&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=74c930322d4f66819dd0800940743c24&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'marry_hangup';
        $params['op']             = 'unlock';
        $params['type']           = $type;
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
                Log::dld($user['id'], ($type == 0 ? '领取' : '解锁')."宝箱，获得：仙缘 x {$data['point_award']}");
            }
            return true;
        }
    }
    
    /**
     * 互动
     * @param unknown $user
     * @return boolean
     * @create_time 2018年1月23日
     */
    public function encourage($user)
    {
        //cmd=marry_hangup&op=encourage&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=74c930322d4f66819dd0800940743c24&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'marry_hangup';
        $params['op']             = 'encourage';
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
                Log::dld($user['id'], '夫妻互动成功');
            }
            return true;
        }
    }
    
    /**
     * 战斗
     * @param unknown $user
     * @create_time 2018年1月23日
     */
    public function fight($user) {
        //cmd=marry_hangup&op=fight&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=74c930322d4f66819dd0800940743c24&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'marry_hangup';
        $params['op']             = 'fight';
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
            }
            return true;
        }
    }
}