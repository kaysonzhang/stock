<?php


namespace App\HttpController;


use App\Utility\JwtUtil;
use EasySwoole\Validate\Validate;

class Login extends BaseController
{
    public function index()
    {
        $post    = $this->request()->getParsedBody();
        $valitor = new Validate();
        $valitor->addColumn('phone', '手机号码')->required('不为空');
        $valitor->addColumn('pass', '密码')->required('不为空');
        $bool = $valitor->validate($post);
        if (!$bool) {
            $data = ['status' => false, 'msg' => $valitor->getError()->__toString()];
            $this->response()->write(json_encode($data));
        }

        $token_data = [
            'phone'     => $post['phone'],
            'loginTime' => time(),
            'Ip'        => $this->clientRealIP(),
        ];
        $token      = JwtUtil::getInstance()->getToken($token_data);
        $data       = ['status' => true, 'token' => $token];
        $this->response()->write(json_encode($data));
    }
}