<?php
use Basic\BaseTask;
use Models\User;
use Models\UserConfig;

class AutoTask extends BaseTask
{
    
    public function mainAction()
    {
        $users = (new User())->getList(['auto'=>1]);
        foreach ($users as $user) {
            if(empty($user['h5token']))continue;
            system('/usr/bin/php /data/html/www/qmdld/tasks/cli.php keep main '.$user['id'].'  >> /data/logs/task-dld.log');
        }
    }
    
    
    public function lilianAction()
    {
        $users = (new User())->getList(['auto'=>1, 'h5token'=>['!=', '']]);
        if(!$users)return false;
        foreach ($users as $user) {
            if(empty($user['h5token']))continue;
            $config = (new UserConfig())->getByUserId($user['id']);
            if(!$config)continue;
            system('/usr/bin/php /data/html/www/qmdld/tasks/cli.php lilian main '.$user['id'].'  >> /data/logs/task-dld.log');
        }
    }
    
}