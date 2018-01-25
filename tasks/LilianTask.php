<?php
use Basic\BaseTask;
use Models\User;
use Services\LilianService;
use Library\Redis;

class LilianTask extends BaseTask
{
    
    public function mainAction($params)
    {
       $userId = $params[0];
       if(empty($userId))return false;
       
       $lockKey = 'lilian_task_lock_'.$userId;
       if(Redis::getInstance()->exists($lockKey)){
           echo date('Y-m-d H:i:s')."\t{$lockKey} 程序已被锁住".PHP_EOL;
           exit;
       }
       Redis::getInstance()->setex($lockKey, 86400 , 'lock');
       
       $user = (new User())->getById($userId);
       
       (new LilianService())->main($user);
       
       Redis::getInstance()->del($lockKey);
    }
    
}