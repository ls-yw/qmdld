<?php
use Basic\BaseTask;
use Models\User;
use Services\ZyhxHomeService;
use Library\Redis;

class ZyhxTask extends BaseTask
{
    
    public function mainAction()
    {
        $users = (new User())->getList(['auto'=>1, 'project'=>'zyhx']);
        foreach ($users as $user) {
            if(empty($user['h5token']))continue;
            system('/usr/bin/php /data/html/my/qmdld/tasks/cli.php zyhx home '.$user['id'].'  >> /data/logs/task-dld-zyhx.log');
        }
    }
    
    
    public function homeAction($params)
    {
        $users = (new User())->getList(['auto'=>1, 'project'=>'zyhx', 'h5token'=>['!=', '']]);
        
        $userId = $params[0];
        if(empty($userId))return false;
         
        $lockKey = 'home_task_lock_'.$userId;
//         if(Redis::getInstance()->exists($lockKey)){
//             echo date('Y-m-d H:i:s')."\t{$lockKey} 程序已被锁住".PHP_EOL;
//             exit;
//         }
        Redis::getInstance()->setex($lockKey, 86400 , 'lock');
         
        $user = (new User())->getById($userId);
        
        (new ZyhxHomeService())->main($user);
        Redis::getInstance()->del($lockKey);
    }
    
}