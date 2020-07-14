<?php


namespace App\HttpController\Manage;


use App\HttpController\BaseController;
use App\Model\stockModel;

class Stock extends BaseController
{
    public function getList()
    {
        $stock = new stockModel();
        $where = "1=1";
        $data  = $stock->getStock(1, 10, $where);
        $total = $stock->getTotal($where);
        $this->response()->write(json_encode(['data' => $data, 'total' => $total]));
    }
}
