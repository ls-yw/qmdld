<?php
use Phalcon\Di\FactoryDefault;

error_reporting(E_ALL);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('RUN_TYPE', 'cgi');
if( defined('RZ_ADMIN_ENCRYPT_KEY')  == false) define('RZ_ADMIN_ENCRYPT_KEY', 'rzjf_admin_password_key') ;
if( defined('LOGIN_ADMIN_USER_PREFIX')  == false) define('LOGIN_ADMIN_USER_PREFIX', 'user:') ;
date_default_timezone_set('Asia/Shanghai');

try {

    /**
     * The FactoryDefault Dependency Injector automatically registers
     * the services that provide a full stack framework.
     */
    $di = new FactoryDefault();

    /**
     * Read services
     */
    include APP_PATH . '/config/baseServices.php';
    include APP_PATH . '/config/webServices.php';

    /**
     * Get config service for use in inline setup below
     */
    $config = $di->getConfig();
    
    /**
     * Handle routes
     */
    include APP_PATH . '/config/router.php';

    /**
     * Include Autoloader
     */
    include APP_PATH . '/config/loader.php';
    
    Library\Log::setTriggerError();

    /**
     * Handle the request
     */
    $application = new \Phalcon\Mvc\Application($di);
    
    // 注册模块
    $application->registerModules($config->modules->toArray()); 
    echo str_replace(["\n","\r","\t"], '', $application->handle()->getContent());

} catch (\Exception $e) {
    if(class_exists('Library\Common\Log')){
        Library\Log::write('fatalError', $e->getMessage(), '', 'trigger_error');
        Library\Log::write('fatalError', $e->getTraceAsString(), '', 'trigger_error');
    }else{
        echo $e->getMessage() . '<br>';
//         echo '<pre>' . $e->getTraceAsString() . '</pre>';
    }
    
}
