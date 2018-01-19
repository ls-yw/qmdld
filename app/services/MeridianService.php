<?php
namespace Services;

use Basic\BaseService;
use Library\Curl;
use Library\Log;

class MeridianService extends BaseService
{
    
    /**
     * 获取未使用威望旗列表
     * 
     * @create_time 2018年1月11日
     */
    public function getNotUsedFlag($user) {
        //cmd=limit&goodslist=100012|100013|100014&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=71649f42d57e00887d61d8a053b3ca9a&pf=wx2
        //{"limit_info":[{"Goods":100012,"Num":5,"Name":"小威望旗","limit":"unlimited","IconId":100012,"ShopId":100006,"Price":20,"GoodsDesc":"小门派的旗子，有一定的号召力…","EffectDesc":"使用获得600点威望。（每日最多可购买40个）"},{"Goods":100013,"Num":12,"Name":"中威望旗","limit":"unlimited","IconId":100013,"ShopId":100007,"Price":100,"GoodsDesc":"名门望派的信物，能威震一方…","EffectDesc":"使用获得3000点威望。（每日最多可购买40个）"},{"Goods":100014,"Num":0,"Name":"大威望旗","limit":"unlimited","IconId":100014,"ShopId":100008,"Price":600,"GoodsDesc":"武林盟主的令旗，执之号令江湖…","EffectDesc":"使用获得18000点威望。（每日最多可购买40个）"}],"result":0,"msg":"ok","rodinfo":[{"name":"kRedDotTask","flag":1}]}
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'limit';
        $params['goodslist']      = '100012|100013|100014';
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
                    if($val['Num'] > 0 && $val['Num'])$res[] = $val;
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
     * 使用威望旗帜
     * @param unknown $user
     * @create_time 2018年1月16日
     */
    public function usedFlag($user) {
        //cmd=storage&op=use&uid=6084512&id=100013&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=71649f42d57e00887d61d8a053b3ca9a&pf=wx2
        //{"result":0,"changed":{"attrs":[{"id":"kPrestige","num":3000}],"items":[{"id":100013,"num":-1}]},"rodinfo":[{"name":"kRedDotTask","flag":1}]}
        $flags = $this->getNotUsedFlag($user);
        if($flags && count($flags) > 0){
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
            foreach ($flags as $val) {
                $params['id']             = $val['Goods'];
                for($i=0;$i<$val['Num'];$i++) {
                    $result = Curl::dld($url, $params);
                    if($result['code'] == 0){
                        $data = $result['data'];
                        $this->dealResult($data, $user['id']);
                        if($data['result'] == '0'){
                            Log::dld($user['id'], "使用一个{$val['Name']}，获得 {$data['changed']['attrs'][0]['num']} 威望");
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
     * 获得经脉首页信息
     * @param unknown $user
     * @return boolean
     * @create_time 2018年1月16日
     */
    public function getIndex($user) {
        //cmd=meridian&op=visitpage&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=71649f42d57e00887d61d8a053b3ca9a&pf=wx2
        //{"act_npc":4,"levels":[{"spot_id":1,"spot_level":2},{"spot_id":2,"spot_level":2},{"spot_id":3,"spot_level":10},{"spot_id":4,"spot_level":10},{"spot_id":5,"spot_level":15},{"spot_id":6,"spot_level":15},{"spot_id":7,"spot_level":20},{"spot_id":8,"spot_level":20},{"spot_id":9,"spot_level":25},{"spot_id":10,"spot_level":25},{"spot_id":11,"spot_level":30},{"spot_id":12,"spot_level":30}],"spirit":1870,"prestige":3937,"souls":[{"id":59,"level":23,"quality":4,"spot_id":12,"spirit":115200,"atk":413,"name":"太极劲","desc":"闪避:4.1%|","next_desc":"闪避:4.3%|","next_spirit":14400,"next_atk":432},{"id":4,"level":26,"quality":4,"spot_id":1,"spirit":168000,"atk":480,"name":"伏魔神通","desc":"小型攻击:800|","next_desc":"小型攻击:840|","next_spirit":24000,"next_atk":504},{"id":9,"level":18,"quality":4,"spot_id":2,"spirit":57600,"atk":317,"name":"紫薇心法","desc":"小型防御:528|","next_desc":"小型防御:560|","next_spirit":9600,"next_atk":336},{"id":20,"level":19,"quality":5,"spot_id":4,"spirit":67200,"atk":480,"name":"洗髓经","desc":"中型防御:800|","next_desc":"中型防御:840|","next_spirit":9600,"next_atk":504},{"id":54,"level":24,"quality":4,"spot_id":11,"spirit":129600,"atk":432,"name":"泥偶神功","desc":"命中:4.3%|","next_desc":"命中:4.6%|","next_spirit":19200,"next_atk":456},{"id":49,"level":16,"quality":4,"spot_id":10,"spirit":43200,"atk":288,"name":"两仪心法","desc":"抗暴击:5.8%|","next_desc":"抗暴击:6%|","next_spirit":7200,"next_atk":302},{"id":44,"level":23,"quality":4,"spot_id":9,"spirit":115200,"atk":413,"name":"混元功","desc":"暴击:8.2%|","next_desc":"暴击:8.6%|","next_spirit":14400,"next_atk":432},{"id":14,"level":25,"quality":4,"spot_id":3,"spirit":148800,"atk":456,"name":"达摩神功","desc":"中型攻击:760|","next_desc":"中型攻击:800|","next_spirit":19200,"next_atk":480},{"id":34,"level":26,"quality":4,"spot_id":7,"spirit":168000,"atk":480,"name":"龙象般若功","desc":"技能伤害:400|","next_desc":"技能伤害:420|","next_spirit":24000,"next_atk":504},{"id":24,"level":29,"quality":4,"spot_id":5,"spirit":252000,"atk":552,"name":"密宗心法","desc":"大型攻击:920|","next_desc":"大型攻击:960|","next_spirit":36000,"next_atk":576},{"id":29,"level":16,"quality":4,"spot_id":6,"spirit":43200,"atk":288,"name":"小无相功","desc":"大型防御:480|","next_desc":"大型防御:504|","next_spirit":7200,"next_atk":302},{"id":39,"level":18,"quality":4,"spot_id":8,"spirit":57600,"atk":317,"name":"北冥真气","desc":"技能减免:264|","next_desc":"技能减免:280|","next_spirit":9600,"next_atk":336}],"awards":[],"auto":0,"result":0,"msg":"ok","rodinfo":[{"name":"kRedDotTask","flag":1}]}
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'meridian';
        $params['op']             = 'visitpage';
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
                Log::dld($user['id'], "当前威望：{$data['prestige']}");
                $res = ['prestige'=>$data['prestige'], 'act_npc'=>$data['act_npc']];
                return $res;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    
    /**
     * 触发经脉造访主入口
     * @param unknown $user
     * @return boolean
     * @create_time 2018年1月16日
     */
    public function main($user) {
        $this->usedFlag($user);
        $info = $this->getIndex($user);
        if($info['prestige'] < 300){
            Log::dld($user['id'], $info['prestige']." 威望，小于300，不造访");
            return false;
        }
        
        while ($info && $info['prestige'] >= 300) {
            $info = $this->meridian($user, $info['act_npc']);
        }
        Log::dld($user['id'], '剩余'. $info['prestige']." 威望，小于300，不造访");
        return false;
    }
    
    /**
     * 造访，并自动使用奖励
     * @param unknown $user
     * @param unknown $id
     * @create_time 2018年1月16日
     */
    public function meridian($user, $id)
    {
        //cmd=meridian&op=visit&id=4&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=71649f42d57e00887d61d8a053b3ca9a&pf=wx2
        //{"act_npc":1,"spirit":2170,"prestige":3757,"awards":[],"npcid":4,"auto":0,"line1":"(10倍)修为+300","line2":"","msg":"","strong":0,"award_max":143,"result":0,"changed":{"attrs":[{"id":"kPrestige","num":-180},{"id":"kSpirit","num":300}]},"rodinfo":[{"name":"kRedDotTask","flag":1}]}
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'meridian';
        $params['op']             = 'visit';
        $params['uid']            = $user['uid'];
        $params['id']             = $id;
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
                Log::dld($user['id'], $data['line1'].' '.$data['line2']." 剩余威望：{$data['prestige']}");
                if(count($data['awards']) > 0)$this->usedAwards($user, $data['awards']);
                $res = ['prestige'=>$data['prestige'], 'act_npc'=>$data['act_npc']];
                return $res;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    
    /**
     * 使用奖品
     * @param unknown $user
     * @param unknown $awards
     * @return boolean
     * @create_time 2018年1月16日
     */
    public function usedAwards($user, $awards)
    {
        //cmd=meridian&op=award&index=94&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=e3b6be64a3c6a7908f710940427660d1&pf=wx2
        //result：{"auto":0,"result":0,"msg":"ok","souls":[{"id":4,"level":22,"quality":4,"spot_id":1,"spirit":100800,"atk":394,"name":"伏魔神通","desc":"小型攻击:656|","next_desc":"小型攻击:688|","next_spirit":14400,"next_atk":413},{"id":49,"level":16,"quality":4,"spot_id":10,"spirit":43200,"atk":288,"name":"两仪心法","desc":"抗暴击:5.8%|","next_desc":"抗暴击:6%|","next_spirit":7200,"next_atk":302},{"id":53,"level":20,"quality":3,"spot_id":11,"spirit":76800,"atk":281,"name":"泥偶神功","desc":"命中:2.8%|","next_desc":"命中:3%|","next_spirit":12000,"next_atk":298},{"id":9,"level":16,"quality":4,"spot_id":2,"spirit":43200,"atk":288,"name":"紫薇心法","desc":"小型防御:480|","next_desc":"小型防御:504|","next_spirit":7200,"next_atk":302},{"id":19,"level":16,"quality":4,"spot_id":4,"spirit":43200,"atk":288,"name":"洗髓经","desc":"中型防御:480|","next_desc":"中型防御:504|","next_spirit":7200,"next_atk":302},{"id":44,"level":20,"quality":4,"spot_id":9,"spirit":76800,"atk":355,"name":"混元功","desc":"暴击:7.2%|","next_desc":"暴击:7.4%|","next_spirit":12000,"next_atk":374},{"id":14,"level":21,"quality":4,"spot_id":3,"spirit":88800,"atk":374,"name":"达摩神功","desc":"中型攻击:624|","next_desc":"中型攻击:656|","next_spirit":12000,"next_atk":394},{"id":58,"level":20,"quality":3,"spot_id":12,"spirit":76800,"atk":281,"name":"太极劲","desc":"闪避:2.8%|","next_desc":"闪避:3%|","next_spirit":12000,"next_atk":298},{"id":24,"level":21,"quality":4,"spot_id":5,"spirit":88800,"atk":374,"name":"密宗心法","desc":"大型攻击:624|","next_desc":"大型攻击:656|","next_spirit":12000,"next_atk":394},{"id":34,"level":21,"quality":4,"spot_id":7,"spirit":88800,"atk":374,"name":"龙象般若功","desc":"技能伤害:312|","next_desc":"技能伤害:328|","next_spirit":12000,"next_atk":394},{"id":39,"level":16,"quality":4,"spot_id":8,"spirit":43200,"atk":288,"name":"北冥真气","desc":"技能减免:240|","next_desc":"技能减免:252|","next_spirit":7200,"next_atk":302},{"id":28,"level":16,"quality":3,"spot_id":6,"spirit":43200,"atk":222,"name":"小无相功","desc":"大型防御:370|","next_desc":"大型防御:391|","next_spirit":7200,"next_atk":235}],"awards":[],"spirit":9760,"changed":{"attrs":[{"id":"kSpirit","num":1200}]},"rodinfo":[{"name":"kRedDotTask","flag":1},{"name":"kRedDotFaction","flag":1}]}
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'meridian';
        $params['op']             = 'award';
        $params['uid']            = $user['uid'];
        $params['uin']            = null;
        $params['skey']           = null;
        $params['h5openid']       = $user['h5openid'];
        $params['h5token']        = $user['h5token'];
        $params['pf']             = 'wx2';
        
        if(empty($awards))return false;
        foreach ($awards as $val){
            $params['index']    = $val['index'];
            $result = Curl::dld($url, $params);
            
            if($result['code'] == 0){
                $data = $result['data'];
                $this->dealResult($data, $user['id']);
                if($data['result'] == '110')break;
                if($data['result'] == '0'){
                    Log::dld($user['id'], "使用{$val['name']}");
                    foreach ($data['changed']['attrs'] as $v){
                        if($v['id'] == 'kFightPower'){
                            Log::dld($user['id'], "战斗力增加{$v['num']}");
                        }elseif($v['id'] == 'kSpirit'){
                            Log::dld($user['id'], "修为增加{$v['num']}");
                        }
                    }
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }
        
    }
}