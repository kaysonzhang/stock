<?php

namespace App\Utility;

use EasySwoole\Component\Singleton;

class DbConfig
{
    use Singleton;

    public function read(): ?array
    {
        $config = [];
        $data =  (new DB())->name('stock_config')->select();
        foreach($data as $key=>$v){
            $config[$v['name']] = $v['value'];
        }
        return $config;
    }
}
