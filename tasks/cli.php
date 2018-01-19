<?php

use Phalcon\Di\FactoryDefault\Cli as CliDI;
use Phalcon\Cli\Console as ConsoleApp;
use Phalcon\Loader;

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH.'/app');
date_default_timezone_set('Asia/Shanghai');
define('RUN_TYPE', 'cli');

// 使用CLI工厂类作为默认的服务容器
$di = new CliDI();
try {

    /**
     * 注册类自动加载器
     */
    $loader = new Loader();
    
    $loader->registerDirs(
        [
            __DIR__ ,
        ]
    );
    
    $loader->register();
    
    /**
     * Read services
     */
    include APP_PATH . '/config/baseServices.php';
    
    /**
     * Get config service for use in inline setup below
     */
    $config = $di->getConfig();
    
    /**
     * Include Autoloader
     */
    include APP_PATH . '/config/loader.php';
    
    // 创建console应用
    $console = new ConsoleApp();
    
    $console->setDI($di);
    
    
    
    /**
     * 处理console应用参数
     */
    $arguments = [];
    
    foreach ($argv as $k => $arg) {
        if ($k == 1) {
            $arguments["task"] = $arg;
        } elseif ($k == 2) {
            $arguments["action"] = $arg;
        } elseif ($k >= 3) {
            $arguments["params"][] = $arg;
        }
    }
    
    Library\Log::setTriggerError();

    // 处理参数
    $console->handle($arguments);
} catch (\Phalcon\Exception $e) {
    echo $e->getMessage();

    exit(255);
}