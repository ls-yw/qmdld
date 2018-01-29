<?php
namespace Services;

use Basic\BaseService;
use Models\Shops;
use Library\Redis;
use Library\Curl;

class GoodsService extends BaseService
{
    public function updateGoods($user)
    {
        $shops = ['pvp', 'servant', 'qualifying'];
        foreach ($shops as $val){
            $info = $this->getShopInfo($val);
            if($info && $info['update_date'] == date('Y-m-d'))continue;
            $shop = $this->{$val}($user);
            
            $key = 'shop_goods_'.$val;
            $data = ['mark'=>$val, 'goods'=>json_encode($shop['goods'], JSON_UNESCAPED_UNICODE), 'update_date'=>date('Y-m-d')];
            if($info){
                (new Shops())->updateData($data, ['id'=>$info['id']]);
            }else{
                (new Shops())->insertData($data);
            }
            Redis::getInstance()->del($key);
        }
        
        
    }
    
    /**
     * 获取商店商品详情
     * @param unknown $mark
     * @create_time 2018年1月26日
     */
    public function getShopInfo($mark) {
        $key = 'shop_goods_'.$mark;
        if(!Redis::getInstance()->exists($key)){
            $info = (new Shops())->getByMark($mark);
            Redis::getInstance()->setex($key, 86400, json_encode($info));
        }
        $info = Redis::getInstance()->get($key);
        return $info ? json_decode($info, true) : '';
    }
    
    /**
     * 胜点商店
     * @param unknown $user
     * @create_time 2018年1月26日
     */
    public function pvp($user) {
        //cmd=shop&shoptype=3&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=e8e6b0524f5c11fae7ab855b15e0f5fe&pf=wx2
        return $this->shopCurl($user, 3, 'winpoint');
    }
    
    /**
     * 家财商店
     * @param unknown $user
     * @create_time 2018年1月29日
     */
    public function servant($user) {
        //cmd=shop&shoptype=4&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=f0ae0ab6764ca7fc701f33725f9a5661&pf=wx2
        return $this->shopCurl($user, 4, 'servant_cash');
    }
    
    /**
     * 王者商店
     * @param unknown $user
     * @create_time 2018年1月29日
     */
    public function qualifying($user)
    {
        //cmd=shop&shoptype=6&uid=6084512&uin=null&skey=null&h5openid=oKIwA0eHZyXEDaUICvhtyE8EJuts&h5token=f0ae0ab6764ca7fc701f33725f9a5661&pf=wx2
        return $this->shopCurl($user, 6, 'king_medal');
    }
    
    public function shopCurl($user, $shoptype, $currency) {
        $url = $this->_config->dldUrl->url;
        $params = [];
        $params['cmd']            = 'shop';
        $params['shoptype']       = $shoptype;
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
                return ['goods'=>$data['commodity_info'], $currency=>$data[$currency]];
            }
            return true;
        }
    }
}