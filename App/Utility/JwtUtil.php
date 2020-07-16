<?php

namespace App\Utility;

use EasySwoole\Component\Singleton;
use EasySwoole\Jwt\Jwt;

class JwtUtil
{
    use Singleton;

    public function getToken(array $data = []): string
    {
        $jwtObject = Jwt::getInstance()
            ->setSecretKey('kaysonzhang@gmail.com') // 秘钥
            ->publish();
        // 加密方式
        $jwtObject->setAlg('HMACSHA256');
        // 用户
        $jwtObject->setAud('user');
        // 过期时间
        $jwtObject->setExp(time() + 3600 * 5);
        // 发布时间
        $jwtObject->setIat(time());
        // 发行人
        $jwtObject->setIss('stock');
        // jwt id 用于标识该jwt
        $jwtObject->setJti(md5(time()));
        // 在此之前不可用
        $jwtObject->setNbf(time() + 60 * 5);
        // 主题
        $jwtObject->setSub('主题');
        // 自定义数据
        $jwtObject->setData($data);
        // 最终生成的token
        return $jwtObject->__toString();
    }

    public function decodeToken(string $token = ''): array
    {
        try {
            // 如果encode设置了秘钥,decode 的时候要指定
            $jwtObject = Jwt::getInstance()->setSecretKey('kaysonzhang@gmail.com')->decode($token);
            $status    = $jwtObject->getStatus();
            $data      = [];
            switch ($status) {
                case  1:
                    echo '验证通过';
                    $data = $jwtObject->getData();
                    break;
                case  -1:
                    echo '无效';
                    break;
                case  -2:
                    echo 'token过期';
                    break;
            }
        } catch (\EasySwoole\Jwt\Exception $e) {

        }
        return $data;
    }
}