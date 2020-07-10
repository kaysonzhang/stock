<?php


namespace App\HttpController;

class Login extends BaseController
{

    public function index()
    {
        $post = $this->request()->getParsedBody();
        $data = ['status' => true, 'token' => '11111'];
        $this->response()->write(json_encode($data));
    }
}