<?php


namespace App\HttpController\Manage;

use EasySwoole\Validate\Validate;
use App\HttpController\BaseController;
use App\Model\stockModel;

class Stock extends BaseController
{
    private $stockModel;

    public function initialize()
    {
        $this->stockModel = new stockModel();
    }

    public function getList()
    {
        $post = $this->request()->getParsedBody();

        $where = "1=1";
        if (!empty($post['stock_code'])) {
            $where .= " and stock_code like '%{$post['stock_code']}%'";
        }
        if (!empty($post['stock_name'])) {
            $where .= " and stock_name like '%{$post['stock_name']}%'";
        }
        $data  = $this->stockModel->getStock($post['page'], $post['pagesize'], $where);
        $total = $this->stockModel->getTotal($where);
        $this->response()->write(json_encode(['data' => $data, 'total' => $total]));
    }

    public function add()
    {
        $post    = $this->request()->getParsedBody();
        $valitor = new Validate();
        $valitor->addColumn('stock_code', '股票代号')->required('不为空');
        $valitor->addColumn('stock_name', '股票名称')->required('不为空');
        $valitor->addColumn('stock_type', '股票地域')->required('不为空');
        $bool = $valitor->validate($post);
        if (!$bool) {
            $data = ['status' => false, 'msg' => $valitor->getError()->__toString()];
            $this->response()->write(json_encode($data));
        }
        $ret = $this->stockModel->add($post);
        $this->response()->write(json_encode(['status' => $ret, 'msg' => $ret ? '成功' : '失败']));
    }

    public function eidt()
    {
        $post       = $this->request()->getParsedBody();
        $insertData = [];
        $this->stockModel->update($insertData,'id=1');
    }
}
