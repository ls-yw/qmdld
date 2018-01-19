<?php
use Basic\BaseTask;
use Models\User;
use Services\BasicService;
use Library\Redis;
use Services\MeridianService;
use Services\ActivityService;
use Services\PvpService;
use Services\MasterService;
use Services\ServantService;
use Services\FactionService;
use Services\QualifyingService;

class KeepTask extends BaseTask
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
        if(time() - $prevTime <= 120){
            Redis::getInstance()->del($lockKey);
            return false;
        }
        Redis::getInstance()->setex($key, 86400, time());
        
        $isGo = (new BasicService())->reddot($user);
        if($isGo === false){
            Redis::getInstance()->del($lockKey);
            return false;
        }
        (new ActivityService())->getList($user);
        
        //经脉造访
        $this->meridian($user);
        
        //好友战斗
        $this->pvp($user);
        
        //师徒
        $this->master($user);
        
        //家丁
        $this->servant($user);
        
        //帮派
        $this->faction($user);
        
        //王者争霸
        $this->qualifying($user);
        
        
        Redis::getInstance()->del($lockKey);
    }
    
    /**
     * 经脉造访  2小时执行一次
     * @param unknown $user
     * @create_time 2018年1月17日
     */
    public function meridian($user)
    {
        $limitTime = 7200;
        echo 'running meridian '.$user['id'].PHP_EOL;
        $key = 'meridian_time_'.$user['id'];
        if(Redis::getInstance()->exists($key)){
            $prevTime = Redis::getInstance()->get($key);
            if(time() - $prevTime <= $limitTime){
                echo 'meridian时间未到 '.$user['id'].' 还差'.($limitTime - (time() - $prevTime)).'秒'.PHP_EOL;
                return false;
            }
        }
        
        (new MeridianService())->main($user);
        
        Redis::getInstance()->setex($key, 86400, time());
    }
    
    /**
     * 好友战斗  半小时执行一次
     * @param unknown $user
     * @return boolean
     * @create_time 2018年1月17日
     */
    public function pvp($user)
    {
        $limitTime = 1800;
        echo 'running pvp '.$user['id'].PHP_EOL;
        $key = 'pvp_time_'.$user['id'];
        if(Redis::getInstance()->exists($key)){
            $prevTime = Redis::getInstance()->get($key);
            if(time() - $prevTime <= $limitTime){
                echo 'pvp时间未到 '.$user['id'].' 还差'.($limitTime - (time() - $prevTime)).'秒'.PHP_EOL;
                return false;
            }
        }
    
        (new PvpService())->main($user);
    
        Redis::getInstance()->setex($key, 86400, time());
    }
    
    /**
     * 师徒    2小时执行一次
     * @param unknown $user
     * @return boolean
     * @create_time 2018年1月18日
     */
    public function master($user) 
    {
        $limitTime = 7200;
        echo 'running master '.$user['id'].PHP_EOL;
        $key = 'master_time_'.$user['id'];
        if(Redis::getInstance()->exists($key)){
            $prevTime = Redis::getInstance()->get($key);
            if(time() - $prevTime <= $limitTime){
                echo 'master时间未到 '.$user['id'].' 还差'.($limitTime - (time() - $prevTime)).'秒'.PHP_EOL;
                return false;
            }
        }
        
        (new MasterService())->main($user);
        
        Redis::getInstance()->setex($key, 86400, time());
    }
    
    /**
     * 家丁    三分钟执行一次
     * @param unknown $user
     * @return boolean
     * @create_time 2018年1月18日
     */
    public function servant($user) 
    {
        $limitTime = 180;
        echo 'running servant '.$user['id'].PHP_EOL;
        $key = 'servant_time_'.$user['id'];
        if(Redis::getInstance()->exists($key)){
            $prevTime = Redis::getInstance()->get($key);
            if(time() - $prevTime <= $limitTime){
                echo 'servant时间未到 '.$user['id'].' 还差'.($limitTime - (time() - $prevTime)).'秒'.PHP_EOL;
                return false;
            }
        }
    
        (new ServantService())->main($user);
    
        Redis::getInstance()->setex($key, 86400, time());
    }
    
    /**
     * 帮派    两小时执行一次
     * @param unknown $user
     * @return boolean
     * @create_time 2018年1月18日
     */
    public function faction($user) 
    {
        $limitTime = 7200;
        echo 'running faction'.$user['id'].PHP_EOL;
        $key = 'faction_time_'.$user['id'];
        if(Redis::getInstance()->exists($key)){
            $prevTime = Redis::getInstance()->get($key);
            if(time() - $prevTime <= $limitTime){
                echo 'faction 未到 '.$user['id'].' 还差'.($limitTime - (time() - $prevTime)).'秒'.PHP_EOL;
                return false;
            }
        }
    
        (new FactionService())->main($user);
    
        Redis::getInstance()->setex($key, 86400, time());
    }
    
    /**
     * 王者争霸    两小时执行一次
     * @param unknown $user
     * @return boolean
     * @create_time 2018年1月18日
     */
    public function qualifying($user) 
    {
        $limitTime = 7200;
        echo 'running qualifying'.$user['id'].PHP_EOL;
        $key = 'qualifying_time_'.$user['id'];
        if(Redis::getInstance()->exists($key)){
            $prevTime = Redis::getInstance()->get($key);
            if(time() - $prevTime <= $limitTime){
                echo 'qualifying未到 '.$user['id'].' 还差'.($limitTime - (time() - $prevTime)).'秒'.PHP_EOL;
                return false;
            }
        }
    
        (new QualifyingService())->main($user);
    
        Redis::getInstance()->setex($key, 86400, time());
    }
}