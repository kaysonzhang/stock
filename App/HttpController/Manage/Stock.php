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

    public function getStock()
    {
        $post    = $this->request()->getParsedBody();
        $valitor = new Validate();
        $valitor->addColumn('stock_code', '股票代号')->notEmpty();
        $bool = $valitor->validate($post);
        if (!$bool) {
            $data = ['status' => false, 'msg' => $valitor->getError()->__toString()];
            $this->response()->write(json_encode($data));
        }

        $ret = $this->stockModel->where('stock_code=' . $post['stock_code'])->find();
        $this->response()->write(json_encode(['status' => $ret, 'data' => $ret]));
    }

    public function add()
    {
        $post = $this->request()->getRequestParam();
        $ret  = $this->validate($post);
        if (!$ret['status']) {
            $data = ['status' => false, 'msg' => $ret['msg']];
            $this->response()->write(json_encode($data));
        }
        $ret = $this->stockModel->replace()->insertGetId($post);
        $this->response()->write(json_encode(['status' => $ret, 'msg' => $ret ? '成功' : '失败']));
    }

    private function validate($post)
    {
        $valitor = new Validate();
        $valitor->addColumn('stock_code', '股票代号')->notEmpty();
        $valitor->addColumn('stock_name', '股票名称')->notEmpty();
        $valitor->addColumn('stock_type', '股票地域')->notEmpty();
        $bool = $valitor->validate($post);
        return ['status' => $bool, 'msg' => $valitor->getError()->__toString()];
    }

    public function eidt()
    {
        $post = $this->request()->getParsedBody();
        $ret  = $this->validate($post);
        if (!$ret['status']) {
            $data = ['status' => false, 'msg' => $ret['msg']];
            $this->response()->write(json_encode($data));
        }

        $ret = $this->stockModel->where('stock_code=' . $post['id'])->update($post);
        $this->response()->write(json_encode(['status' => $ret, 'msg' => $ret ? '成功' : '失败']));
    }

    public function del()
    {
        $post    = $this->request()->getParsedBody();
        $valitor = new Validate();
        $valitor->addColumn('stock_code', '股票代号')->notEmpty();
        $bool = $valitor->validate($post);
        if (!$bool) {
            $data = ['status' => false, 'msg' => $valitor->getError()->__toString()];
            $this->response()->write(json_encode($data));
        }

        $ret = $this->stockModel->where('stock_code=' . $post['stock_code'])->delete();
        $this->response()->write(json_encode(['status' => $ret, 'msg' => $ret ? '成功' : '失败']));
    }
}
