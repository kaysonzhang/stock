<?php


namespace App\HttpController\Manage;


use App\HttpController\BaseController;
use App\Model\stockModel;

class Stock extends BaseController
{
    public function getList()
    {
        $post = $this->request()->getParsedBody();
        $stock = new stockModel();
        $where = "1=1";
        if(!empty($post['stockCode'])){
            $where .= " and shares_code like '%{$post['stockCode']}%'";
        }
        if(!empty($post['stockName'])){
            $where .= " and shares_name like '%{$post['stockName']}%'";
        }
        $data  = $stock->getStock($post['page'], $post['pagesize'], $where);
        $total = $stock->getTotal($where);
        $this->response()->write(json_encode(['data' => $data, 'total' => $total]));
    }
}
