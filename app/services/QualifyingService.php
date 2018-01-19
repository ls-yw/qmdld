<?php
namespace Services;

use Basic\BaseService;
use Library\Curl;
use Library\Log;

class QualifyingService extends BaseService
{
    
    public function main($user)
    {
        $this->index($user);
        $this->getRank($user);
    }
    
    /**
     * 个人王者页面详情
     * @param unknown $user
     * @return boolean
     * @create_time 2018年1月18日
     */
    public function index($user) {
        //cmd=qualifying&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=7fd1de6a8e520814b9a3e96628a6cef6&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'qualifying';
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
                Log::dld($user['id'], '王者争霸个人赛还有'.$data['free_times'].'次免费次数');
                Log::dld($user['id'], '当前排位'.$data['sname']." {$data['star']}星");
                //领取胜利次数奖励
                foreach ($data['win_award'] as $key => $val){
                    if($data['win_times'] >= $val['times'] && $val['flag'] == 0){  //领取奖励
                        $this->getReward($user, $key);
                    }
                }
                if($data['free_times'] > 0)$this->fight($user, $data['win_times']);
            }
            return true;
        }
    }
    
    /**
     * 战斗
     * @param unknown $user
     * @param unknown $winNum
     * @create_time 2018年1月18日
     */
    public function fight($user, $winNum) {
        //cmd=qualifying&op=fight&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=35c44646c00cbdf99a168155d6a4a438&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'qualifying';
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
                if($data['win'] == 1){
                    Log::dld($user['id'], '战胜了 '.urldecode($data['oppinfo']['name']).' 排位：'.$data['sname']." {$data['star']}星");
                    if($winNum == 0){  //首胜分享
                        $this->firstWinShare($user);
                    }
                }else{
                    Log::dld($user['id'], '战败了 '.urldecode($data['oppinfo']['name']).' 排位：'.$data['sname']." {$data['star']}星");
                }
                $this->index($user);
            }
            return true;
        }
    }
    
    /**
     * 首胜分享
     * @param unknown $user
     * @return boolean
     * @create_time 2018年1月18日
     */
    public function firstWinShare($user) {
        //cmd=qualifying&op=reward&idx=4&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=7fd1de6a8e520814b9a3e96628a6cef6&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'qualifying';
        $params['op']             = 'reward';
        $params['idx']            = 4;
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
                Log::dld($user['id'], "首胜分享成功，获得{$data['msg']}");
            }
            return true;
        }
    }
    
    /**
     * 领取奖励
     * @param unknown $user
     * @param unknown $idx
     * @return boolean
     * @create_time 2018年1月18日
     */
    public function getReward($user, $idx) {
        //cmd=qualifying&op=reward&idx=0&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=35c44646c00cbdf99a168155d6a4a438&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'qualifying';
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
                Log::dld($user['id'], "领取额外奖励，获得{$data['msg']}");
            }
            return true;
        }
    }
    
    /**
     * 获取好友排行榜，并领取荣耀勋章
     * @param unknown $user
     * @return boolean
     * @create_time 2018年1月19日
     */
    public function getRank($user) {
        //cmd=qualifying&op=rank&type=2&uid=636428&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=2388ab625144e111dd364b4100149114&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'qualifying';
        $params['op']             = 'rank';
        $params['type']           = 2;
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
                if($data['award_num'] < $data['max_award']){
                    foreach ($data['friend_array'] as $val){
                        if($val['win'] == 1 && $val['flag'] == 0){
                            $res = $this->getRankAward($user, $val['uid']);
                            if($res == true){
                                $data['award_num'] = $data['award_num'] + 15;
                                Log::dld($user['id'], '好友荣耀勋章：'.$data['award_num'].' / '.$data['max_award']);
                            }
                        }
                        if($data['award_num'] >= $data['max_award'])return true;
                    }
                }
            }
            return true;
        }
    }
    
    /**
     * 领取好友荣耀勋章
     * @param unknown $user
     * @param unknown $friuid
     * @return boolean
     * @create_time 2018年1月19日
     */
    public function getRankAward($user, $friuid) {
        //cmd=qualifying&op=reward&idx=100&friuid=6661917&uid=636428&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=2388ab625144e111dd364b4100149114&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'qualifying';
        $params['op']             = 'reward';
        $params['idx']            = 100;
        $params['friuid']         = $friuid;
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
                return true;
            }
            return false;
        }
        return false;
    }
}