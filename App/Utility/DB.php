<?php

namespace App\Utility;

use EasySwoole\EasySwoole\Config;
use EasySwoole\Mysqli\Client as MysqliClient;
use EasySwoole\Mysqli\Config as MysqliConfig;
use EasySwoole\Mysqli\Exception\Exception;
use EasySwoole\Mysqli\QueryBuilder;
use EasySwoole\ORM\Db\Result;

class DB
{
    private $client;
    public $tableName;

    public function __construct(array $config = [])
    {
        if (!$config) {
            $config = Config::getInstance()->getConf('MYSQL');
        }

        $this->client = new MysqliClient(new MysqliConfig($config));
    }

    /**
     * 设置表名，不带前缀
     * @param string $name
     */
    public function name(string $name)
    {
        $this->client->queryBuilder()->setPrefix(DBPREFIX);
        $this->tableName = $name;
        return $this;
    }

    /**
     * 获取表名称
     * @return mixed
     */
    public function getTable()
    {
        return $this->tableName;
    }

    /**
     * 设置表名，带前缀
     * @param string $name
     */
    public function table(string $name)
    {
        $this->tableName = DBPREFIX . $name;
        return $this;
    }

    /**
     * 获取生成的语句
     * @return string|null
     */
    public function getLastSql()
    {
        return $this->client->queryBuilder()->getLastQuery();
    }

    /**
     * 写入数据
     * @param $tableName
     * @param $insertData
     * @return QueryBuilder
     */
    public function insert($insertData)
    {
        $this->client->queryBuilder()->insert($this->tableName, $insertData);
        return $this->execBuilder()->getLastInsertId();
    }

    /**
     * 批量写入数据
     * @param $tableName
     * @param $insertData
     * @param array $option 'REPLACE' / 'INSERT'
     * @return QueryBuilder
     */
    public function insertAll($insertData, $replace = false)
    {
        $option = [];
        if ($replace) {
            $option = ['replace' => true];
        }
        $this->client->queryBuilder()->insertAll($this->tableName, $insertData, $option);
        return $this->execBuilder()->getResult();
    }

    /**
     * 写入替换数据
     * @param $tableName
     * @param $insertData
     * @return QueryBuilder
     */
    public function replace($insertData)
    {
        $this->client->queryBuilder()->replace($this->tableName, $insertData);
        return $this->execBuilder()->getResult();
    }

    /**
     * 更新数据
     * @param $tableName
     * @param $insertData
     * @return QueryBuilder|void
     */
    public function update($tableData, $numRows = null)
    {
        $this->client->queryBuilder()->update($this->tableName, $tableData, $numRows);
        return $this->execBuilder()->getResult();
    }

    /**
     * 删除数据
     * @param $tableName
     * @return QueryBuilder|void
     */
    public function delete($numRows = null)
    {
        $this->client->queryBuilder()->delete($this->tableName, $numRows);
        return $this->execBuilder()->getResult();
    }

    /**
     * 执行sql语句操作
     * @param string $sql
     * @param array $param
     * @return Result|null
     * @throws \Throwable
     */
    public function query(string $sql, $param = [])
    {
        $this->client->queryBuilder()->raw($sql, $param);
        return $this->execBuilder()->getResult();
    }

    /**
     * 获取数据集返回
     * @return mixed
     * @throws \Throwable
     */
    public function select()
    {
        $this->client->queryBuilder()->get($this->tableName);
        return $this->execBuilder()->getResult();
    }

    /**
     * 获取一条数据返回
     * @return array|null
     * @throws \Throwable
     */
    public function find()
    {
        $this->client->queryBuilder()->get($this->tableName);
        return $this->execBuilder()->getResultOne();
    }

    /**
     * 得到某个字段的值
     * @access public
     * @param string $field 字段名
     * @param mixed $default 默认值
     * @return mixed
     */
    public function value(?string $column = null)
    {
        if (!is_null($column)) {
            $this->field([$column]);
        }
        $data = $this->find();
        if (!$data) return $data;

        return $data[$column];
    }

    /**
     * @param string $column
     * @return array|null
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    public function column(?string $column = null): ?array
    {
        if (!is_null($column)) {
            $this->field([$column]);
        }
        $this->client->queryBuilder()->get($this->tableName);
        return $this->execBuilder()->getResultColumn($column);
    }

    /**
     * Where条件
     * TODO Where支持数组条件查询(索引数组和kv数组两种方式)
     * TODO Where支持字符串条件，支持字符串直接预绑定(在Where条件上直接绑定参数)
     * TODO Where支持快捷查询方法(whereLike/whereIn/whereNotIn等)
     * @param $whereProp
     * @param string $whereValue
     * @param string $operator
     * @param string $cond
     * @return \Extend\orm\Db
     */
    public function where($whereProp, $whereValue = 'DBNULL', $operator = '=', $cond = 'AND')
    {
        $this->client->queryBuilder()->where($whereProp, $whereValue, $operator, $cond);
        return $this;
    }

    /**
     * 获取表数据的字段
     * @param array $fields
     * @return $this
     */
    public function field(array $fields)
    {
        $this->client->queryBuilder()->fields($fields);
        return $this;
    }

    /**
     * 分页获取数据
     * @param int $page
     * @param int|null $limit
     * @return $this
     */
    public function page(int $page, ?int $limit = null)
    {
        if ($page < 1) {
            $page = 1;
        }
        $this->client->queryBuilder()->limit(($page - 1) * $limit, $limit);
        return $this;
    }

    /**
     * OrderBy条件
     * @param $orderByField
     * @param string $orderbyDirection
     * @param null $customFieldsOrRegExp
     * @return $this
     * @throws Exception
     */
    public function order($orderByField, $orderbyDirection = "DESC"): Db
    {
        $this->client->queryBuilder()->orderBy($orderByField, $orderbyDirection);
        return $this;
    }

    /**
     * GroupBy条件
     * TODO GroupBy数组条件支持
     * @param $groupByField
     * @return $this
     */
    public function group($groupByField)
    {
        $this->client->queryBuilder()->groupBy($groupByField);
        return $this;
    }

    /**
     * Having条件
     * TODO Having支持字符串原语查询
     * @param $havingProp
     * @param string $havingValue
     * @param string $operator
     * @param string $cond
     * @return $this
     */
    public function having($havingProp, $havingValue = 'DBNULL', $operator = '=', $cond = 'AND')
    {
        $this->client->queryBuilder()->having($havingProp, $havingValue, $operator, $cond);
        return $this;
    }

    public function limit(int $page, ?int $limit = null)
    {
        $this->client->queryBuilder()->limit($page, $limit);
        return $this;
    }

    /**
     * 事务开始
     */
    public function startTransaction()
    {
        $this->client->queryBuilder()->startTransaction();
        return $this->execBuilder()->getResult();
    }

    /**
     * 事务提交
     */
    public function commit()
    {
        $this->client->queryBuilder()->commit();
        return $this->execBuilder()->getResult();
    }

    /**
     * 事务回滚
     */
    public function rollback()
    {
        $this->client->queryBuilder()->rollback();
        return $this->execBuilder()->getResult();
    }

    /*  ==============    聚合查询    ==================   */

    /**
     * @param $field
     * @return null
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    public function max($field)
    {
        return $this->queryPolymerization('max', $field);
    }

    /**
     * @param $field
     * @return null
     * @throws Exception
     * @throws \Throwable
     */
    public function min($field)
    {
        return $this->queryPolymerization('min', $field);
    }

    /**
     * @param null $field
     * @return null
     * @throws Exception
     * @throws \Throwable
     */
    public function count($field = null)
    {
        return (int)$this->queryPolymerization('count', $field);
    }

    /**
     * @param $field
     * @return null
     * @throws Exception
     * @throws \Throwable
     */
    public function avg($field)
    {
        return $this->queryPolymerization('avg', $field);
    }

    /**
     * @param $field
     * @return null
     * @throws Exception
     * @throws \Throwable
     */
    public function sum($field)
    {
        return $this->queryPolymerization('sum', $field);
    }

    /**
     * 快捷查询 统一执行
     * @param $type
     * @param null $field
     * @return null|mixed
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    private function queryPolymerization($type, $field = null)
    {
        if ($field === null) {
            $field = $this->getPkFiledName();
        }
        // 判断字段中是否带了表名，是否有`
        if (strstr($field, '`') == false) {
            // 有表名
            if (strstr($field, '.') !== false) {
                $temArray = explode(".", $field);
                $field    = "`{$temArray[0]}`.`{$temArray[1]}`";
            } else {
                if (!is_numeric($field)) {
                    $field = "`{$field}`";
                }
            }
        }

        $fields = "$type({$field}) as total";
        $res    = $this->field([$fields])->limit(0, 1)->select();
        if (isset($res[0]['total'])) {
            return $res[0]['total'];
        }

        return null;
    }

    /**
     * 当前表的索引字段
     * @return mixed|null
     */
    private function getPkFiledName()
    {
        $indexes = $this->query("SHOW KEYS FROM {$this->tableName} WHERE Key_name = 'PRIMARY'");
        // 直接返回PrimaryKey第一个索引
        foreach ($indexes as $indexName => $index) {
            if ($index['Column_name']) {
                return $index['Column_name'];
            }
        }

        return null;
    }

    public function execBuilder()
    {
        $result = new Result();
        $ret    = null;
        $errno  = 0;
        $error  = '';

        try {
            $ret           = $this->client->rawQuery($this->client->queryBuilder()->getLastQuery());
            $errno         = $this->client->mysqlClient()->errno;
            $error         = $this->client->mysqlClient()->error;
            $insert_id     = $this->client->mysqlClient()->insert_id;
            $affected_rows = $this->client->mysqlClient()->affected_rows;

            $result->setResult($ret);
            $result->setLastError($error);
            $result->setLastErrorNo($errno);
            $result->setLastInsertId($insert_id);
            $result->setAffectedRows($affected_rows);
        } catch (\Throwable $throwable) {
            throw $throwable;
        }
        return $result;
    }

    public function __call($method, $args)
    {
        call_user_func_array([$this->client->queryBuilder(), $method], $args);
        return $this;
    }

    public function __destruct()
    {
        $this->client->close();
    }
}