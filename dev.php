<?php
return [
    'SERVER_NAME' => "StockSwoole",
    'MAIN_SERVER' => [
        'LISTEN_ADDRESS' => '0.0.0.0',
        'PORT'           => 9601,
        'SERVER_TYPE'    => EASYSWOOLE_WEB_SERVER, //可选为 EASYSWOOLE_SERVER  EASYSWOOLE_WEB_SERVER EASYSWOOLE_WEB_SOCKET_SERVER,EASYSWOOLE_REDIS_SERVER
        'SOCK_TYPE'      => SWOOLE_TCP,
        'RUN_MODEL'      => SWOOLE_PROCESS,
        'SETTING'        => [
            'worker_num'    => 8,
            'reload_async'  => true,
            'max_wait_time' => 3,

        ],
        'TASK'           => [
            'workerNum'     => 4,
            'maxRunningNum' => 128,
            'timeout'       => 15,
            'taskQueue'     => App\Utility\TaskQueue::getInstance()
        ]
    ],
    //目录请根据对应项目名称小写区分,请以绝对路径，不然守护模式运行会有问题
    'TEMP_DIR'    => EASYSWOOLE_ROOT . '/Temp',
    'LOG_DIR'     => EASYSWOOLE_ROOT . '/Log',
    //配置使用那个调度者
    'TASKER'      => 'center',

    'database' => [
        'default'     => 'mysql',
        'connections' => [
            'mysql' => [    // 数据库类型
                'type'            => 'mysql',
                // 服务器地址
                'hostname'        => '127.0.0.1',
                // 数据库名
                'database'        => 'stock',
                // 用户名
                'username'        => 'root',
                // 密码
                'password'        => 'Kayson@398635',
                // 端口
                'hostport'        => '3306',

                // 数据库编码默认采用utf8
                'charset'         => 'utf8mb4',
                // 数据库表前缀
                'prefix'          => 'k_',
                // 是否需要断线重连
                'break_reconnect' => true,
            ]
        ]
    ],

    'REDIS_KEY' => [
        'share_data' => 'S_S_D'
    ],

    'TIMER' => [
        'jobs' => [
            //定时任务列表
            //App\Crontab\StockClient::class,
        ]
    ],

    'REDIS' => [
        'host'      => '127.0.0.1',
        'port'      => '6379',
        //'auth'   => '',
        'db'        => 5,
        'serialize' => \EasySwoole\Redis\Config\RedisConfig::SERIALIZE_NONE
    ],
];


