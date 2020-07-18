<?php

namespace App\Model;

use think\Model;

class StockModel extends Model
{
    protected $name = 'stock';

    public function getStock(int $status = 1)
    {
        return ($this->where('status', $status)->order('stock_code', 'ASC')->select())->toArray();
    }
}