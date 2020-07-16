<?php


namespace App\HttpController\Manage;

use EasySwoole\Validate\Validate;
use App\HttpController\BaseController;
use App\Model\stockModel;

class Stock extends BaseController
{
    private $stockModel;

    public function initialize(){
        $this->stockModel = new stockModel();
    }

    public function getList()
    {
        $post = $this->request()->getParsedBody();

        $where = "1=1";
        if(!empty($post['stockCode'])){
            $where .= " and shares_code like '%{$post['stockCode']}%'";
        }
        if(!empty($post['stockName'])){
            $where .= " and shares_name like '%{$post['stockName']}%'";
        }
        $data  = $this->stockModel->getStock($post['page'], $post['pagesize'], $where);
        $total = $this->stockModel->getTotal($where);
        $this->response()->write(json_encode(['data' => $data, 'total' => $total]));
    }

    public function add(){
        $post = $this->request()->getParsedBody();
        $insertData = [];
        $this->stockModel->insert($insertData);
    }

    public function eidt(){
        $post = $this->request()->getParsedBody();
        $insertData = [];
        $this->stockModel->where()->update($insertData);
    }
}
