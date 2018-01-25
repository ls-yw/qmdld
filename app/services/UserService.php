<?php 
namespace Services;

use Basic\BaseService;
use Models\UserConfig;
use Library\Redis;

class UserService extends BaseService
{
    /**
     * 获取用户配置
     * @param unknown $userId
     * @create_time 2018年1月24日
     */
    public function getUserConfig($userId) {
        $key = 'user_config_'.$userId;
        if(!Redis::getInstance()->exists($key)){
            $userConfig = (new UserConfig())->getByUserId($userId);
            Redis::getInstance()->setex($key, 1800, json_encode($userConfig));
        }
        $userConfig = Redis::getInstance()->get($key);
        return json_decode($userConfig, true);
    }
}