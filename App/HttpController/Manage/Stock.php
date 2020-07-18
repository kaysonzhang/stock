<?php


namespace App\HttpController\Manage;

use App\Service\BoardService;
use EasySwoole\Validate\Validate;
use App\HttpController\BaseController;
use App\Model\StockModel;

class Stock extends BaseController
{
    private $stockModel;

    public function initialize()
    {
        $this->stockModel = new stockModel();
    }

    /**
     * 获取股票列表
     * @return bool
     */
    public function getList()
    {
        $post  = $this->request()->getParsedBody();
        $where = "1=1";
        if (!empty($post['stock_code'])) {
            $where .= " and stock_code like '%{$post['stock_code']}%'";
        }
        if (!empty($post['stock_name'])) {
            $where .= " and stock_name like '%{$post['stock_name']}%'";
        }
        $data = ($this->stockModel->where($where)->paginate([
            'list_rows' => $post['pagesize'],
            'page'      => $post['page'],
            'var_page'  => 'page',
        ]))->toArray();

        return $this->responseJson(200, $data);
    }

    /**
     * 获取单个股票详细信息
     * @return bool
     */
    public function getStock()
    {
        $post    = $this->request()->getParsedBody();
        $valitor = new Validate();
        $valitor->addColumn('stock_code', '股票代号')->notEmpty();
        $valitor->validate($post);
        $data = $this->stockModel->where('stock_code=' . $post['stock_code'])->find();
        return $this->responseJson(200, $data);

    }

    /**
     * 保存股票信息
     * @return bool
     */
    public function save()
    {
        $post = $this->request()->getRequestParam();
        $this->validate($post);
        if (!empty($post['id'])) {
            $post['stock_code'] = sprintf("%06d", $post['stock_code']);
            $id = sprintf("%06d", $post['id']);
            $stock_code         = $post['id'];
            unset($post['id']);
            $ret = $this->stockModel->where("stock_code='{$id}'")->update($post);
        } else {
            $post['stock_code'] = sprintf("%06d", $post['stock_code']);
            unset($post['id']);
            $ret = $this->stockModel->insertGetId($post);
        }
        if ($ret) {
            return $this->responseJson(200, $post);
        }
        return $this->responseJson(500, [], '失败');
    }

    /**
     * 验证器
     * @param $post
     * @return bool
     */
    private function validate($post)
    {
        $valitor = new Validate();
        $valitor->addColumn('stock_code', '股票代号')->notEmpty();
        $valitor->addColumn('stock_name', '股票名称')->notEmpty();
        $valitor->addColumn('stock_type', '股票地域')->notEmpty();
        $bool = $valitor->validate($post);
        if (!$bool) {
            return $this->responseJson(500, [], $valitor->getError()->__toString());
        }

    }

    /**
     * 删除股票
     * @return bool
     */
    public function del()
    {
        $post    = $this->request()->getParsedBody();
        $valitor = new Validate();
        $valitor->addColumn('stock_code', '股票代号')->notEmpty();
        $bool = $valitor->validate($post);
        if (!$bool) {
            return $this->responseJson(500, [], $valitor->getError()->__toString());
        }

        $ret = $this->stockModel->where('stock_code=' . $post['stock_code'])->delete();
        if ($ret) {
            return $this->responseJson(200, $post);
        }
        return $this->responseJson(500, [], '失败');
    }

    /**
     * 更新股票的板块信息
     * @return bool
     */
    public function updateBorad()
    {
        BoardService::getInstance()->updateBorad();
        return $this->responseJson(200);
    }
}
