<?php
use Basic\BaseTask;
use Models\User;
use Library\Redis;
use Services\ZyhxHomeService;

class KeepzyhxTask extends BaseTask
{
    
    public function mainAction($params)
    {
        $userId = $params[0];
        if(empty($userId))return false;
        $lockKey = 'keep_lock_'.$userId;
        if(Redis::getInstance()->exists($lockKey)){
            echo date('Y-m-d H:i:s')."\t{$lockKey} 程序已被锁住".PHP_EOL;
            exit;
        }
        Redis::getInstance()->setex($lockKey, 1800 , 'lock');
        
        $user = (new User())->getById($userId);
        
        //两分钟跑一次
        $key = 'keep_'.$user['id'];
        if(empty($user['h5token'])){
            Redis::getInstance()->del($lockKey);
            return false;
        }
        $prevTime = (int)Redis::getInstance()->get($key);
        if(time() - $prevTime <= 60){
            Redis::getInstance()->del($lockKey);
            return false;
        }
        Redis::getInstance()->setex($key, 86400, time());
        
        //家园
        $this->home($user);
        
        
        Redis::getInstance()->del($lockKey);
    }
    
    /**
     * 家园  2分钟执行一次
     * @param unknown $user
     * @create_time 2018年1月17日
     */
    public function home($user)
    {
        $limitTime = 120;
        echo 'running home '.$user['id'].PHP_EOL;
        $key = 'home_time_'.$user['id'];
        if(Redis::getInstance()->exists($key)){
            $prevTime = Redis::getInstance()->get($key);
            if(time() - $prevTime <= $limitTime){
                echo 'home时间未到 '.$user['id'].' 还差'.($limitTime - (time() - $prevTime)).'秒'.PHP_EOL;
                return false;
            }
        }
        
        (new ZyhxHomeService())->main($user);
        
        Redis::getInstance()->setex($key, 86400, time());
    }
    
}