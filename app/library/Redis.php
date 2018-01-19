<?php
namespace Library;

use Phalcon\DI;

class Redis
{
    public $obj;
    private static $_instance = null;

    /**
     * @return \Redis|null
     */
    public static function getInstance($config=[])
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance->init($config);
    }

    /**
     * Redis constructor.
     */
    public function init($config)
    {
        $redisConfig = empty($config) ? DI::getDefault()->get('config')->redis->toArray() : $config;
        try {
            $this->obj = new \Redis();
            $this->obj->connect($redisConfig['default']['host'], $redisConfig['default']['port']);
            if ($this->obj) {
                $connect = true;
            } else {
                trigger_error('redis|redis连接失败，host：'.json_encode($redisConfig['default']['host'], JSON_UNESCAPED_UNICODE));
                return false;
                $connect = false;
            }
        } catch(\Exception $e){
            trigger_error('redis|'.$e->getMessage());
            return false;
        }
        
        //设置前缀
        $this->obj->setOption(\Redis::OPT_PREFIX, $redisConfig['default']['prefix']);
//         $this->obj->set('aa', 'bb');
//         echo $this->obj->get('aa');
        return $this->obj;
    }
    
    
    
}