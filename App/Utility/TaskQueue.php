<?php
namespace App\Utility;

use EasySwoole\Task\AbstractInterface\TaskQueueInterface;
use EasySwoole\Component\Singleton;
use EasySwoole\EasySwoole\Config;
use EasySwoole\Redis\Redis;
use EasySwoole\Task\Package;

class TaskQueue implements TaskQueueInterface
{
    use Singleton;

    private $redis;

    private $redisKey;

    private $isInit = false;

    public function init()
    {
        if ($this->isInit) {
            return ;
        }
        $redis_config   = Config::getInstance()->getConf('REDIS');
        $this->redis    = new Redis(new \EasySwoole\Redis\Config\RedisConfig($redis_config));
        $server_name    = Config::getInstance()->getConf("SERVER_NAME");
        $this->redisKey = $server_name.'_'.Config::getInstance()->getConf('Task_Redis_Queue');
        $this->isInit   = true;
    }

    public function clear(){
        $this->redis->del($this->redisKey);
    }

    function pop():?Package
    {
        $this->init();
        if ($this->redis->LLEN($this->redisKey) > 0) {
            $data = $this->redis->Rpop($this->redisKey);
            $package = \Opis\Closure\unserialize($data);
            $this->redis->disconnect();
            return $package;
        }
        return null;
    }

    function push(Package $package):bool
    {
        $this->init();
        $data = \Opis\Closure\serialize($package);
        $this->redis->Lpush($this->redisKey, $data);
        $this->redis->disconnect();
        return true;
    }
}