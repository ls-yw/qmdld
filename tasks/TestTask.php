<?php
use Basic\BaseTask;
use Library\Redis;

class TestTask extends BaseTask
{
    
    public function mainAction($params)
    {
        print_r($params);
//         Redis::getInstance()->del('dld_cli_keep_main');
//         system('php /data/html/www/qmdld/tasks/cli.php test aa');
    }
    
    public function aaAction() {
        echo '执行啦啦';
    }
}