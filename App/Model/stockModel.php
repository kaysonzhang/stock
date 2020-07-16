<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 19-11-13
 * Time: 上午11:00
 */

namespace App\Model;

use App\Utility\DB;

class stockModel
{
    private $tablename = 'stock';
    public $db;

    public function __construct()
    {
        $this->db = (new DB())->name($this->tablename);
    }

    /**
     * 获取使用状态的股票
     * @return array|null
     */
    public function getStockByUsed(): ?array
    {
        return $this->db
            ->where('status', 1)
            ->order('stock_code', 'ASC')
            ->select();
    }

    public function getStock($page = 1, $pagesize = 10, $where = '')
    {
        if (!empty($where)) {
            $this->db->where($where);
        }
        $data = $this->db
            ->order('stock_code', 'ASC')
            ->limit($page, $pagesize)
            ->select();
        return $data;
    }

    public function getTotal($where = [])
    {
        if (!empty($where)) {
            $this->db->where($where);
        }
        return $this->db->count('stock_code');
    }

}