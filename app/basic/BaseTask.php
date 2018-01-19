<?php
namespace Basic;

use Phalcon\Cli\Task;
use Library\Redis;

class BaseTask extends Task
{
    private $_lockKey = '';
    
    /**
     * 脚本执行前执行
     *
     * @create_time 2017年12月1日
     */
    public function beforeExecuteRoute()
    {
        $taskName = $this->di->get('router')->getTaskName();
        $actionName = $this->di->get('router')->getActionName() ? $this->di->get('router')->getActionName() : 'main';
        $this->_lockKey = 'dld_cli_'.$taskName.'_'.$actionName;
        
        echo date('Y-m-d H:i:s')."\t{$taskName} {$actionName} 被执行\n";
    }
    
    /**
     * task防止重复执行锁
     * 
     * @create_time 2017年12月12日
     */
    protected function _taskLock()
    {
        if(Redis::getInstance()->exists($this->_lockKey)){
            echo date('Y-m-d H:i:s')."\t程序已被锁住".PHP_EOL;
            exit;
        }
        //redis锁
        Redis::getInstance()->setex($this->_lockKey, 1800 , 'lock');
    }
    
    
    /**
     * 脚本执行后执行
     * @param unknown $dispatcher
     * @create_time 2017年12月1日
     */
    public function afterExecuteRoute($dispatcher)
    {
        //删除锁
        Redis::getInstance()->del($this->_lockKey);
    }
}