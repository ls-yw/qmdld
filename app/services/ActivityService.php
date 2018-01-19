<?php
namespace Services;

use Basic\BaseService;
use Library\Curl;
use Library\Log;

class ActivityService extends BaseService
{
    /**
     * 获得活动列表，有小红点的对应去参加
     * @param unknown $user
     * @return boolean
     * @create_time 2018年1月16日
     */
    public function getList($user)
    {
        //cmd=acthall&subcmd=Get&needreload=0&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=ea6a09caca7fcf35ca0448458a62498a&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'acthall';
        $params['subcmd']         = 'Get';
        $params['needreload']     = 0;
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
                foreach ($data['acts'] as $val)
                {
                    if($val['reddot'] == 1){
                        if($val['id'] == 7)$this->getClock($user);
                        if($val['id'] == 19)$this->getLunch($user);
                        if($val['id'] == 24)$this->getGoldTurntable($user);
                        if($val['id'] == 9)$this->drawLotIndex($user);
                        if($val['id'] == 10)$this->farmIndex($user);
                        if($val['id'] == 8)$this->startGame($user);
                    }
                }
            }else{
                return false;
            }
        }else {
            return false;
        }
    }
    
    /**
     * 领取闹钟奖励
     * @param unknown $user
     * @create_time 2018年1月16日
     */
    public function getClock($user) {
        //cmd=activity&aid=7&subcmd=GetGift&idx=0&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=ea6a09caca7fcf35ca0448458a62498a&pf=wx2
        $time1 = '11:00 -13:00   16:00-18:00 19:00-21:00';
        $startTime1 = 1100;
        $endTime1   = 1300;
        $startTime2 = 1600;
        $endTime2   = 1800;
        $startTime3 = 1900;
        $endTime3   = 2100;
        $nowTime = date('Hi');
        
        if($startTime1 <= $nowTime && $nowTime <= $endTime1){
            $idx = 0;
        }elseif($startTime2 <= $nowTime && $nowTime <= $endTime2){
            $idx = 1;
        }elseif($startTime3 <= $nowTime && $nowTime <= $endTime3){
            $idx = 2;
        }else{
            return false;
        }
        
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'activity';
        $params['aid']            = 7;
        $params['subcmd']         = 'GetGift';
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
                Log::dld($user['id'], '开始领取企鹅小闹钟奖励');
                foreach ($data['award']['items'] as $val)
                {
                    Log::dld($user['id'], "获得{$val['name']}，数量 {$val['num']}");
                }
            }else{
                return false;
            }
        }else {
            return false;
        }
    }
    
    /**
     * 领取便当
     * @param unknown $user
     * @create_time 2018年1月16日
     */
    public function getLunch($user) {
        //cmd=activity&aid=19&subcmd=GetGift&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=ea6a09caca7fcf35ca0448458a62498a&pf=wx2
        //{"result":0,"msg":"ok","award_vit":50,"award_energy":5,"award_king":5,"changed":{"attrs":[{"id":"kVit","num":50},{"id":"kEnergy","num":5}]},"rodinfo":[{"name":"kRedDotTask","flag":1}]}
        $time1 = '11:00 -13:00   16:00-18:00 19:00-21:00';
//         $startTime1 = 1100;
//         $endTime1   = 1300;
//         $startTime2 = 1800;
//         $endTime2   = 2100;
//         $nowTime = date('Hi');
        
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'activity';
        $params['aid']            = 19;
        $params['subcmd']         = 'GetGift';
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
                Log::dld($user['id'], "领取便当：获得".(isset($data['award_vit']) ? " {$data['award_vit']} 体力" : '').(isset($data['award_energy']) ? " {$data['award_energy']} 个江湖令" : '').(isset($data['award_king']) ? " {$data['award_king']} 次王者争霸次数" : ''));
            }else{
                return false;
            }
        }else {
            return false;
        }
    }
    
    /**
     * 黄金转盘
     * @param unknown $user
     * @create_time 2018年1月17日
     */
    public function getGoldTurntable($user) {
        //cmd=activity&aid=24&sub=0&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=87fb9342d130b34ad95d2218fee8e50a&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'activity';
        $params['aid']            = 24;
        $params['sub']            = 0;
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
                if($data['keynum'] > 0){
                    //cmd=activity&aid=24&sub=1&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=87fb9342d130b34ad95d2218fee8e50a&pf=wx2
                    $params['sub']            = 1;
                    for ($i=0;$i<$data['keynum'];$i++){
                        $res = Curl::dld($url, $params);
                        $resData = $res['data'];
                        $this->dealResult($resData, $user['id']);
                        if($resData['result'] == '0'){
                            $awards = '';
                            foreach ($resData['award']['items'] as $val){
                                $awards .= $val['num'].'个'.$val['name'].' ';
                            }
                            Log::dld($user['id'], '黄金转盘：获得'.$awards);
                        }
                    }
                }
            }else{
                return false;
            }
        }else {
            return false;
        }
    }
    
    /**
     * 农场首页
     * @param unknown $user
     * @return boolean
     * @create_time 2018年1月18日
     */
    public function farmIndex($user)
    {
        //cmd=activity&aid=10&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=4ebc6635a5410620d67ca76458097eb3&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'activity';
        $params['aid']            = 10;
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
                if($data['status'] == 2 && $data['seconds'] == 0){  //TODO   收获
                    $this->farmHarvest($user);
                }elseif($data['fertilize'] == 0){   //可施肥
                    $this->farmFertilizer($user);
                }
            }else{
                return false;
            }
        }else {
            return false;
        }
    }
    
    /**
     * 农场施肥
     * @param unknown $user
     * @create_time 2018年1月18日
     */
    public function farmFertilizer($user)
    {
        //cmd=activity&aid=10&sub=2&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=4ebc6635a5410620d67ca76458097eb3&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'activity';
        $params['aid']            = 10;
        $params['sub']            = 2;
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
                Log::dld($user['id'], '农场施肥成功，获得：'.$awards);
            }else{
                return false;
            }
        }else {
            return false;
        }
    }
    
    /**
     * 农场收获
     * @param unknown $user
     * @create_time 2018年1月19日
     */
    public function farmHarvest($user)
    {
        //cmd=activity&aid=10&sub=3&uid=636428&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=2388ab625144e111dd364b4100149114&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'activity';
        $params['aid']            = 10;
        $params['sub']            = 3;
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
                Log::dld($user['id'], '乐斗农庄：'.$data['msg']);
                $this->farmFertilizer($user);
            }else{
                return false;
            }
        }else {
            return false;
        }
    }
    
    /**
     * 抽签活动
     * @param unknown $user
     * @create_time 2018年1月18日
     */
    public function drawLotIndex($user) {
        //cmd=activity&aid=9&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=4ebc6635a5410620d67ca76458097eb3&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'activity';
        $params['aid']            = 9;
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
                if($data['status'] == 0){  //未选择道具
                    $this->drawLotSetPrize($user);
                }elseif ($data['status'] == 1 && $data['seconds'] == 0){  //可抽签
                    $this->drawLotToDraw($user);
                }elseif ($data['status'] == 2 && $data['seconds'] == 0){  //已抽，但未领取
                    $this->drawLotGetPrize($user);
                }
            }else{
                return false;
            }
        }else {
            return false;
        }
    }
    
    /**
     * 抽签
     * @param unknown $user
     * @return boolean
     * @create_time 2018年1月18日
     */
    public function drawLotToDraw($user) {
        //cmd=activity&aid=9&sub=2&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=4ebc6635a5410620d67ca76458097eb3&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'activity';
        $params['aid']            = 9;
        $params['sub']            = 2;
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
                Log::dld($user['id'], '抽得'.$data['multiple'].'倍签');
                $this->drawLotIndex($user);
            }else{
                return false;
            }
        }else {
            return false;
        }
    }
    
    /**
     * 领取抽签奖品
     * @param unknown $user
     * @create_time 2018年1月18日
     */
    public function drawLotGetPrize($user) {
        //cmd=activity&aid=9&sub=3&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=4ebc6635a5410620d67ca76458097eb3&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'activity';
        $params['aid']            = 9;
        $params['sub']            = 3;
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
                $this->drawLotIndex($user);
            }else{
                return false;
            }
        }else {
            return false;
        }
    }
    
    /**
     * 设置抽签奖品   默认设置抽黄金卷轴
     * @param unknown $user
     * @create_time 2018年1月18日
     */
    public function drawLotSetPrize($user) {
        //cmd=activity&aid=9&sub=1&idx=1&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=4ebc6635a5410620d67ca76458097eb3&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'activity';
        $params['aid']            = 9;
        $params['sub']            = 1;
        $params['idx']            = 1;
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
                Log::dld($user['id'], '乐斗上上签活动，设置抽黄金卷轴成功');
            }else{
                return false;
            }
        }else {
            return false;
        }
    }
    
    /**
     * 开工福利详情
     * @param unknown $user
     * @create_time 2018年1月19日
     */
    public function startGame($user) {
        //cmd=activity&aid=8&subcmd=Get&uid=636428&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=2388ab625144e111dd364b4100149114&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'activity';
        $params['aid']            = 8;
        $params['subcmd']         = 'Get';
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
                foreach ($data['infos'] as $val){
                    if($val['flag'] == 1)$this->startGameGetPrize($user, $val['idx']);
                }
            }else{
                return false;
            }
        }else {
            return false;
        }
    }
    
    /**
     * 领取开工福利
     * @param unknown $user
     * @param unknown $idx
     * @return boolean
     * @create_time 2018年1月19日
     */
    public function startGameGetPrize($user, $idx) {
        //cmd=activity&aid=8&subcmd=GetGift&idx=4&uid=636428&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=2388ab625144e111dd364b4100149114&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'activity';
        $params['aid']            = 8;
        $params['subcmd']         = 'GetGift';
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
                $arawds = $this->getAwardsName($data['award']);
                Log::dld($user['id'], "领取开工福利：{$arawds}");
            }else{
                return false;
            }
        }else {
            return false;
        }
    }
}