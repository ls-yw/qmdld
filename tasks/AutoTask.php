<?php
use Basic\BaseTask;
use Models\User;

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
    
}