<?php

namespace App\Timer;

use App\Logger\Logger;
use EasySwoole\EasySwoole\Crontab\AbstractCronTask;

/**
 *  定时任务加载数据库中配置的任务
 */
class ShareClient extends AbstractCronTask
{

    private $logFile = "ShareClient.log";

    public static function getRule(): string
    {
        return '* * * * *';
    }

    public static function getTaskName(): string
    {
        return 'ShareClient';
    }

    function run(int $taskId, int $workerIndex)
    {
    }

    function onException(\Throwable $throwable, int $taskId, int $workerIndex)
    {
        Logger::getInstance()->log($throwable->getMessage(), $this->logFile, "classException");
    }
}