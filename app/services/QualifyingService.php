<?php
namespace Services;

use Basic\BaseService;
use Library\Curl;
use Library\Log;
use Models\UserInfo;

class QualifyingService extends BaseService
{
    /**
     * 个人赛主入口
     * @param unknown $user
     * @create_time 2018年1月23日
     */
    public function main($user)
    {
        $this->getAllRank($user);
        $this->getRank($user);
        
        $userConfig = (new UserService())->getUserConfig($user['id']);
        if(!empty($userConfig['qualifying_shop']))$this->bugGoods($user, $userConfig['qualifying_shop']);
        if($userConfig['qualifying_person'] == 0)return false;
        $this->index($user);
    }
    
    /**
     * 团队赛主入口
     * @param unknown $user
     * @create_time 2018年1月23日
     */
    public function teamMain($user)
    {
        $this->getTeamAllRank($user);
        $this->getTeamRank($user);
        
        $userConfig = (new UserService())->getUserConfig($user['id']);
        if($userConfig['qualifying_team'] == 0)return false;
        $this->teamIndex($user);
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
                (new UserInfo())->updateData(['qualifying'=>$data['sname']." {$data['star']}星", 'qualifying_num'=>$data['free_times']], ['user_id'=>$user['id']]);
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
    
    public function getAllRank($user)
    {
        //cmd=qualifying&op=rank&type=0&start=1&end=100&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=46fa3da7cb5dc49fe6b822bc24f61e02&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'qualifying';
        $params['op']             = 'rank';
        $params['type']           = 0;
        $params['start']          = 1;
        $params['end']            = 100;
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
                $this->getRankAward($user, null, 4);
                if($data['reward_flag'] == 0){
                    $this->getRankAward($user, null, 3);
                }
            }
            return true;
        }
    }
    
    public function getTeamAllRank($user)
    {
        //cmd=qualifying&op=rank&type=0&start=1&end=100&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=46fa3da7cb5dc49fe6b822bc24f61e02&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'teamqua';
        $params['op']             = 'rank';
        $params['type']           = 0;
        $params['start']          = 1;
        $params['end']            = 10;
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
                $this->getTeamRankAward($user, null, 4);
                if($data['reward_flag'] == 0){
                    $this->getTeamRankAward($user, null, 3);
                }
            }
            return true;
        }
    }
    
    public function getTeamZanReward()
    {
        //cmd=teamqua&op=reward&idx=4&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=46fa3da7cb5dc49fe6b822bc24f61e02&pf=wx2
    }
    
    /**
     * 领取好友荣耀勋章
     * @param unknown $user
     * @param unknown $friuid
     * @return boolean
     * @create_time 2018年1月19日
     */
    public function getRankAward($user, $friuid=null, $idx=100) {
        //cmd=qualifying&op=reward&idx=100&friuid=6661917&uid=636428&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=2388ab625144e111dd364b4100149114&pf=wx2
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
        
        if($friuid !== null)$params['friuid']         = $friuid;
        
        $result = Curl::dld($url, $params);
        if($result['code'] == 0){
            $data = $result['data'];
            $this->dealResult($data, $user['id']);
            if($data['result'] == '0'){
                Log::dld($user['id'], ($idx == 3 ? '点赞奖励：' : '').$data['msg']);
                return true;
            }
            return false;
        }
        return false;
    }
    
    /**
     * 获取团队好友排行榜，并领取荣耀勋章
     * @param unknown $user
     * @return boolean
     * @create_time 2018年1月19日
     */
    public function getTeamRank($user) {
        //cmd=teamqua&op=rank&type=2&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=7bb2c60e84c12f5a49d9a57329be2abf&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'teamqua';
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
                            $res = $this->getTeamRankAward($user, $val['uid']);
                            if($res == true){
                                $data['award_num'] = $data['award_num'] + 15;
                                Log::dld($user['id'], '团队好友荣耀勋章：'.$data['award_num'].' / '.$data['max_award']);
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
    public function getTeamRankAward($user, $friuid=null, $idx=100) {
        //cmd=teamqua&op=reward&idx=100&friuid=3777413&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=7bb2c60e84c12f5a49d9a57329be2abf&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'teamqua';
        $params['op']             = 'reward';
        $params['idx']            = $idx;
        $params['uid']            = $user['uid'];
        $params['uin']            = null;
        $params['skey']           = null;
        $params['h5openid']       = $user['h5openid'];
        $params['h5token']        = $user['h5token'];
        $params['pf']             = 'wx2';
        
        if($friuid !== null)$params['friuid']         = $friuid;
    
        $result = Curl::dld($url, $params);
        if($result['code'] == 0){
            $data = $result['data'];
            $this->dealResult($data, $user['id']);
            if($data['result'] == '0'){
                Log::dld($user['id'], ($idx == 3 ? '点赞奖励：' : '').$data['msg']);
                return true;
            }
            return false;
        }
        return false;
    }
    
    /**
     * 团队王者页面详情
     * @param unknown $user
     * @return boolean
     * @create_time 2018年1月18日
     */
    public function teamIndex($user) {
        //cmd=teamqua&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=7bb2c60e84c12f5a49d9a57329be2abf&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'teamqua';
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
                if($data['team_id'] == 0){
                    Log::dld($user['id'], '王者争霸组队赛你还未组队');
                    return false;
                }
                Log::dld($user['id'], '王者争霸组队赛还有'.$data['free_times'].'次免费次数');
                Log::dld($user['id'], '当前排位'.$data['sname']." {$data['star']}星");
                (new UserInfo())->updateData(['teamqua'=>$data['sname']." {$data['star']}星", 'teamqua_num'=>$data['free_times']], ['user_id'=>$user['id']]);
                //领取胜利次数奖励
                foreach ($data['win_award'] as $key => $val){
                    if($data['win_times'] >= $val['times'] && $val['flag'] == 0){  //领取奖励
                        $this->getTeamReward($user, $key);
                    }
                }
                if(count($data['team_member']) != 3){
                    Log::dld($user['id'], "团队只有".count($data['team_member']).'位成员');
                    return false;
                }
                if($data['free_times'] > 0)$this->teamMatch($user, $data['team_member']);
            }
            return true;
        }
    }
    
    /**
     * 团队匹配，并战斗
     * @param unknown $user
     * @param unknown $team
     * @create_time 2018年1月23日
     */
    public function teamMatch($user, $team) {
        //cmd=teamqua&op=match&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=7bb2c60e84c12f5a49d9a57329be2abf&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'teamqua';
        $params['op']             = 'match';
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
                $teamOrder = $this->_getOrderByPower($team);
                $opTeamOrder = $this->_getOrderByPower($data['team_member']);
                $order = [];
                $order[$opTeamOrder[0]] = $teamOrder[2];
                $order[$opTeamOrder[1]] = $teamOrder[0];
                $order[$opTeamOrder[2]] = $teamOrder[1];
                $list = [];
                foreach ($data['team_member'] as $k => $v){
                    $list[] = $v['attack_power'].' VS '. $team[$order[$k]]['attack_power'];
                }
                Log::dld($user['id'], implode(' | ', $list));
                $this->teamFight($user, $order[0].'|'.$order[1].'|'.$order[2], $data['team_name']);
            }else{
                Log::dld($user['id'], $data['msg']);
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
    public function teamFight($user, $userlist, $teamName) {
        //cmd=teamqua&op=fight&userlist=2|1|0&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=7bb2c60e84c12f5a49d9a57329be2abf&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'teamqua';
        $params['op']             = 'fight';
        $params['userlist']       = $userlist;
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
                    Log::dld($user['id'], '战胜了 '.urldecode($teamName).'团队 排位：'.$data['sname']." {$data['star']}星");
                }else{
                    Log::dld($user['id'], '战败了 '.urldecode($teamName).'团队 排位：'.$data['sname']." {$data['star']}星");
                }
                $this->teamIndex($user);
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
    public function getTeamReward($user, $idx) {
        //cmd=teamqua&op=reward&idx=0&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=7bb2c60e84c12f5a49d9a57329be2abf&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'teamqua';
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
     * 返回团队排名 key  从高到低   [1,0,2]
     * @param unknown $team
     * @return multitype:
     * @create_time 2018年1月23日
     */
    private function _getOrderByPower($team) {
        $powerArr = [];
        foreach ($team as $key => $val){
            $powerArr[$key] = $val['attack_power'];
        }
        arsort($powerArr);
        return array_keys($powerArr);
    }
    
    public function bugGoods($user, $goods) {
        $shop = (new GoodsService())->qualifying($user);
        $goods = explode(',', $goods);
        $prevNum = 0;
        foreach ($goods as $val){
            foreach($shop['goods'] as $v){
                if($val == $v['id']){
                    if($v['remain'] > 0 && $prevNum == 0){
                        $num = 0;
                        for ($i=1;$i<=$v['remain'];$i++){
                            if($v['price'] * $i <= $shop['king_medal'])$num = $i;
                        }
                        if($num == 0)return false;
                        $res = $this->bug($user, $val, $num, $v['price']*$num);
                        $prevNum = $res ? $v['remain'] - $num : $v['remain'];
                    }
                }
            }
        }
    
    }
    
    public function bugDoushenGoods($user, $goods) {
        $shop = (new GoodsService())->doushen($user);
        $goods = explode(',', $goods);
        $prevNum = 0;
        foreach ($goods as $val){
            foreach($shop['goods'] as $v){
                if($val == $v['id']){
                    if($v['remain'] > 0 && $prevNum == 0){
                        $num = 0;
                        for ($i=1;$i<=$v['remain'];$i++){
                            if($v['price'] * $i <= $shop['doushen_medal'])$num = $i;
                        }
                        if($num == 0)return false;
                        $res = $this->bug($user, $val, $num, $v['price']*$num);
                        $prevNum = $res ? $v['remain'] - $num : $v['remain'];
                    }
                }
            }
        }
    }
    
    /**
     * 购买
     * @param unknown $user
     * @param unknown $id
     * @param unknown $num
     * @param unknown $prize
     * @create_time 2018年1月26日
     */
    public function bug($user, $id, $num, $prize) {
        //cmd=shop&subtype=1&num=1&id=100023&price=20&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=dd63c541dc64fc41a05909a0466d753f&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'shop';
        $params['subtype']        = 1;
        $params['num']            = $num;
        $params['id']             = $id;
        $params['price']          = $prize;
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
                unset($data['changed']['attrs']);
                $awards = $this->getAwardsName($data['changed']);
                Log::dld($user['id'], "商店购买了 {$awards}");
                return true;
            }else{
                Log::dld($user['id'], $data['msg']);
                return false;
            }
        }else{
            return false;
        }
    }
    
    public function doushenMain($user)
    {
        $this->getDoushenRank($user);
        
        $userConfig = (new UserService())->getUserConfig($user['id']);
        if($userConfig['qualifying_doushen'] == 1){
            $indexData = $this->doushenIndex($user);
            (new UserInfo())->updateData(['doushen'=>$indexData['self_rank']], ['user_id'=>$user['id']]);
            Log::dld($user['id'], '当前斗神排名：'.$indexData['self_rank'].' 还剩余 '.$indexData['free_times'].'次');
            foreach ($indexData['day_award'] as $v){
                if($v['flag'] != 1)$this->getDoushenMoney($user, null, $v['idx']);
            }
            if($indexData['free_times'] > 0){
                $userInfo = (new UserInfo())->getByUserId($user['id']);
                if($userConfig['qualifying_doushen_type'] == 1){
                    $hasFight = false;
                    while ($hasFight === false){
                        $result = (new PvpService())->getFriendList($user, 0);
                        $duishou = [];
                        foreach ($result['friendlist'] as $val) {
                            if($userInfo['attack_power'] - $val['power'] < 5000)continue;  //战斗力不高于4000，则跳过
                            $duishou = $val;
                        }
                        if(!empty($duishou)){
                            for ($i=0;$i<$indexData['free_times'];$i++){
                                $this->doushenFight($user, $duishou['uid']);
                            }
                            $hasFight = true;
                        }
                    }
                }elseif ($userConfig['qualifying_doushen_type'] == 0){
                    $isFight = false;
                    foreach ($indexData['oppinfo'] as $ulist) {
                        if($ulist['rank'] < $indexData['self_rank'] && ($userInfo['attack_power'] - $ulist['attack_power']) >= 2000 && $ulist['vip_level'] < 3){
                            $this->doushenFight($user, $ulist['uid']);
                            $isFight = true;
                        }
                    }
                    while ($isFight === false){
                        $nData = $this->doushenIndex($user);
                        foreach ($nData['oppinfo'] as $ulist) {
                            if($ulist['rank'] < $nData['self_rank'] && ($userInfo['attack_power'] - $ulist['attack_power']) >= 2000 && $ulist['vip_level'] < 3){
                                $this->doushenFight($user, $ulist['uid']);
                                $isFight = true;
                            }
                        }
                    }
                }
            }
        }
        
        if(!empty($userConfig['doushen_shop']))$this->bugDoushenGoods($user, $userConfig['doushen_shop']);
    }
    
    /**
     * 斗神详情
     * @param unknown $user
     * @return boolean
     * @create_time 2018年2月8日
     */
    public function doushenIndex($user)
    {
        //cmd=doushen&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=1faac839ebb280be5a9163e8ddc2de68&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'doushen';
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
                return $data;
            }else{
                Log::dld($user['id'], $data['msg']);
                return false;
            }
        }else{
            return false;
        }
    }
    
    public function getDoushenRank($user) {
        //cmd=doushen&op=rank&type=2&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=1faac839ebb280be5a9163e8ddc2de68&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'doushen';
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
                            $res = $this->getDoushenMoney($user, $val['uid']);
                            if($res == true){
                                $data['award_num'] = $data['award_num'] + 30;
                                Log::dld($user['id'], '好友斗神勋章：'.$data['award_num'].' / '.$data['max_award']);
                            }
                        }
                        if($data['award_num'] >= $data['max_award'])return true;
                    }
                }
            }
            return true;
        }
    }
    
    public function getDoushenMoney($user, $friuid=null, $idx=100) {
        //cmd=doushen&op=reward&idx=100&friuid=3469815&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=236efcd907692ceed4b7f05f423c19e7&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'doushen';
        $params['op']             = 'reward';
        $params['idx']            = $idx;
        $params['uid']            = $user['uid'];
        $params['uin']            = null;
        $params['skey']           = null;
        $params['h5openid']       = $user['h5openid'];
        $params['h5token']        = $user['h5token'];
        $params['pf']             = 'wx2';
        
        if($friuid !== null)$params['friuid']         = $friuid;
    
        $result = Curl::dld($url, $params);
        if($result['code'] == 0){
            $data = $result['data'];
            $this->dealResult($data, $user['id']);
            if($data['result'] == '0'){
                $name = ($idx == 0 ? '斗神点赞奖励：' : ($idx == 0 ? '斗神分享奖励：' : ''));
                Log::dld($user['id'], $name.$data['msg']);
                return true;
            }
            return false;
        }
        return false;
    }
    
    /**
     * 斗神挑战
     * @param unknown $user
     * @param unknown $friendId
     * @return boolean
     * @create_time 2018年2月8日
     */
    public function doushenFight($user, $friendId)
    {
        //cmd=doushen&op=fight&opp=5624410&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=a76c85d4ffde5477716324fbcd9added&pf=wx2
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'doushen';
        $params['op']             = 'fight';
        $params['opp']            = $friendId;
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
                $reward = $this->getAwardsName($data['changed']);
                Log::dld($user['id'], '斗神挑战 '.($data['win'] == 1 ? '成功 ' : '失败').$reward);
                if($data['win'] == 1){
                    $ddd = $this->doushenIndex($user);
                    (new UserInfo())->updateData(['doushen'=>$ddd['self_rank']], ['user_id'=>$user['id']]);
                    Log::dld($user['id'], '当前斗神排名：'.$ddd['self_rank'].' 还剩余 '.$ddd['free_times'].'次');
                }
                return true;
            }else{
                Log::dld($user['id'], $data['msg']);
                return true;
            }
            return false;
        }
        return false;
    }
}