<?php
namespace App\HttpController\Backend;

use App\Model\GroupModel;
use EasySwoole\Validate\Validate;
use App\HttpController\BaseController;


class Group extends BaseController
{
    private $groupModel;

    public function initialize()
    {
        $this->groupModel = new GroupModel();
    }

    /**
     * 获取分组列表
     * @return bool
     */
    public function getList()
    {
        $post  = $this->request()->getParsedBody();
        $where = "1=1";
        if (!empty($post['group_name'])) {
            $where .= " and group_name like '%{$post['group_name']}%'";
        }

        $data = ($this->groupModel->where($where)->paginate([
            'list_rows' => $post['pagesize'],
            'page'      => $post['page'],
            'var_page'  => 'page',
        ]))->toArray();

        return $this->responseJson(200, $data);
    }

    /**
     * 获取单个分组详细信息
     * @return bool
     */
    public function getGroup()
    {
        $post    = $this->request()->getParsedBody();
        $valitor = new Validate();
        $valitor->addColumn('id', 'id')->notEmpty();
        $valitor->validate($post);
        $data = $this->groupModel->where('id=' . $post['id'])->find();
        return $this->responseJson(200, $data);

    }

    /**
     * 保存分组信息
     * @return bool
     */
    public function save()
    {
        $post = $this->request()->getRequestParam();
        $this->validate($post);
        if (!empty($post['id'])) {
            $ret = $this->groupModel->where("id='{$post['id']}'")->update($post);
        } else {
            unset($post['id']);
            $ret = $this->groupModel->insertGetId($post);
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
        $valitor->addColumn('group_name', '分组名称')->notEmpty();
        $bool = $valitor->validate($post);
        if (!$bool) {
            return $this->responseJson(500, [], $valitor->getError()->__toString());
        }

    }

    /**
     * 删除分组
     * @return bool
     */
    public function del()
    {
        $post    = $this->request()->getParsedBody();
        $valitor = new Validate();
        $valitor->addColumn('id', 'id')->notEmpty();
        $bool = $valitor->validate($post);
        if (!$bool) {
            return $this->responseJson(500, [], $valitor->getError()->__toString());
        }

        $ret = $this->groupModel->where('id=' . $post['id'])->delete();
        if ($ret) {
            return $this->responseJson(200, $post);
        }
        return $this->responseJson(500, [], '失败');
    }
}
