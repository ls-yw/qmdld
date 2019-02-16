<?php
namespace Library;

use Phalcon\DI;
class Log
{
    
    private $_logPath = '';
    private static $_isSetTrigger = false;
    
    public  function __construct()
    {
        $this->_logPath = dirname(__DIR__);
    }
    
    /**
     * 手动埋点写业务日志
     * @param string $mark      业务标记
     * @param string|array $message
     * @param string $title
     * @param string $fileName
     * @create_time 2017年11月23日
     */
    public static function write(string $mark, $message, string $title='', string $fileName='') {
        if(is_array($message) || is_object($message))$message = json_encode($message, JSON_UNESCAPED_UNICODE);
        
        $logPath = DI::getDefault()->get('config')->application->logsDir.date('Y-m').'/';
        self::directory($logPath);
        
        //文件名
        $fileName = $fileName ? date('d').'-'.$fileName : date('d').'-'.$mark;
        if(explode('.', $fileName)[count(explode('.', $fileName))-1] != 'log')$fileName .= '-'.RUN_TYPE.'.log';
        $file = $logPath.$fileName;
        
        $message = date('Y-m-d H:i:s')." 【{$mark}】".($title ? "【{$title}】" : '')." {$message}\r\n";
        
        error_log($message, 3,$file );
    }
    
    /**
     * 记录操作日志
     * @param string $mark      业务标记
     * @param string|array $message
     * @param string $title
     * @param string $fileName
     * @create_time 2017年11月23日
     */
    public static function dld(int $uid, $message, $mark='') {
        if(is_array($message) || is_object($message))$message = json_encode($message, JSON_UNESCAPED_UNICODE);
    
        $logPath = DI::getDefault()->get('config')->application->actionDir.$uid.'/';
        self::directory($logPath);
    
        //文件名
        $fileName = 'dld'.date('Ymd').'.log';
        $file = $logPath.$fileName;
    
        $message = date('Y-m-d H:i:s').(!empty($mark) ? " 【{$mark}】" : '')." {$message}\r\n";
    
        error_log($message, 3,$file );
    }
    
    /**
     * 记录操作日志
     * @param string $mark      业务标记
     * @param string|array $message
     * @param string $title
     * @param string $fileName
     * @create_time 2017年11月23日
     */
    public static function zyhx(int $uid, $message, $mark='') {
        if(is_array($message) || is_object($message))$message = json_encode($message, JSON_UNESCAPED_UNICODE);
    
        $logPath = DI::getDefault()->get('config')->application->actionDir.$uid.'/';
        self::directory($logPath);
    
        //文件名
        $fileName = 'zyhx'.date('Ymd').'.log';
        $file = $logPath.$fileName;
    
        $message = date('Y-m-d H:i:s').(!empty($mark) ? " 【{$mark}】" : '')." {$message}\r\n";
    
        error_log($message, 3,$file );
    }
    
    /**
     * 捕捉错误并记入日志
     * @param string $errno
     * @param string $errstr
     * @param string $errfile
     * @param string $errline
     * @create_time 2017年12月18日
     */
    public static function setTriggerError($errno='', $errstr='', $errfile='', $errline='')
    {
        if (self::$_isSetTrigger == false){
            //设置错误日志
            set_error_handler(__CLASS__.'::setTriggerError');
            self::$_isSetTrigger = true;
            return;
        }
        $level = '';
        switch ($errno) {
            case E_USER_ERROR:
                $level = 'error';
                break;
            case E_USER_WARNING:
                $level = 'warning';
                break;
            case E_USER_NOTICE:
                $level = 'notice';
                break;
            default:
                $level = 'otherError';
                break;
        }
        self::write($level, $errstr."\r\n保存定位：".$errfile.' '.$errline, '', 'trigger_error');
    }
    
    /**
     * 自动创建目录
     * @param string $dir
     * @return boolean
     * @create_time 2017年11月21日
     */
    public static function directory( $dir ){  
       return  is_dir ( $dir ) or self::directory(dirname( $dir )) and  @mkdir ( $dir , 0777);
    }
    
}