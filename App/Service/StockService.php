<?php

namespace App\Service;

use App\Utility\DB;
use App\Utility\DbConfig;
use EasySwoole\Component\Singleton;
use EasySwoole\EasySwoole\Config;
use EasySwoole\Redis\Redis;


class StockService
{
    use Singleton;

    /**
     * 异步并发获取股票数据
     * @return array
     */
    public function getShares()
    {
        $hm   = date('Hi');
        $week = date('w');
        if ($hm < 915 || $hm > 1501 || in_array($week, [0, 6])) {
            $ret = $this->readRedis();
            if (!empty($ret['data']) && !empty($ret['zhishu'])) {
                return $ret;
            }
        }

        $stock = $this->getShaersDataFormDb();
        $data  = $this->splitData($stock);
        $wait  = new \Swoole\Coroutine\WaitGroup();

        $result                = [];
        $result['data']['all'] = [];
        $result['sort']['all'] = '全部';
        foreach ($data as $k => $v) {
            $wait->add();
            go(function () use ($wait, &$result, $v) {
                $ret = $this->getSharesDataFromRemote($v);
                if ($ret) {
                    foreach ($ret as $k => $item) {
                        $sort_type = !empty($item['sort_type']) ? $item['sort_type'] : '';
                        if ($sort_type == '指数') {
                            $result['zhishu'][] = $item;
                        } else {
                            $result['data']['all'][] = $item;
                            if (!empty($sort_type)) {
                                $result['data'][md5($sort_type)][] = $item;
                                $result['sort'][md5($sort_type)]   = $sort_type;
                            }
                        }
                    }
                }
                $wait->done();
            });
        }
        $wait->wait();
        //挂起当前协程，等待所有任务完成后执行下面代码
        //对指数排序一下
        if (!empty($result['zhishu'])) {
            $shares_code = array_column($result['zhishu'], 'stock_code');
            array_multisort($shares_code, SORT_ASC, $result['zhishu']);
        }

        $result['all_total']    = count($stock);
        $result['data_total']   = count($result['data']['all']);
        $result['zhishu_total'] = count($result['zhishu']);

        $this->writeRedis($result);
        return $result;
    }

    private function splitData($data)
    {
        $j     = 0;
        $i     = 1;
        $group = [];
        if ($data) {
            foreach ($data as $key => $v) {
                $group[$j][] = $v;
                if ($i % 10 == 0) {
                    $j++;
                }
                $i++;
            }
        }
        return $group;
    }

    private function writeRedis($sharedata = [])
    {
        $redis_config = Config::getInstance()->getConf('REDIS');
        $redis_key    = Config::getInstance()->getConf('REDIS_KEY');
        $redis        = new Redis(new \EasySwoole\Redis\Config\RedisConfig($redis_config));
        $ret          = $redis->set($redis_key['share_data'], json_encode($sharedata));
        $redis->disconnect();
        return $ret;
    }

    private function readRedis()
    {
        $redis_config = Config::getInstance()->getConf('REDIS');
        $redis_key    = Config::getInstance()->getConf('REDIS_KEY');
        $redis        = new Redis(new \EasySwoole\Redis\Config\RedisConfig($redis_config));
        $ret          = $redis->get($redis_key['share_data']);
        $redis->disconnect();
        return json_decode($ret, true);
    }

    /**
     * 读取配置的数据库中的股票
     * @return array|null
     */
    public function getShaersDataFormDb(): ?array
    {
        return (new DB())->name('stock')->where('status', 1)->order('stock_code', 'ASC')->select();
    }


    /**
     * 从股票api中获取数据
     * @param $data
     * @return array
     * @throws \EasySwoole\HttpClient\Exception\InvalidUrl
     */
    private function getSharesDataFromRemote($data)
    {
        //睡眠1-2毫秒，防止一起并发达到请求并发限制
        //\Co::sleep(rand(1, 10));
        $sc = [];
        $sv = [];
        foreach ($data as $key => $v) {
            $sv[$v['stock_code']] = $v;

            $sc[] = strtolower($v['stock_type']) . $v['stock_code'];
        }

        $shareData = [];
        $client    = new \EasySwoole\HttpClient\HttpClient();
        $client->setUrl('http://qt.gtimg.cn/q=' . implode(',', $sc));
        $response = $client->get();
        $shareStr = $response->getBody();
        if (!empty($shareStr)) {
            preg_match_all('#"(.*?)"#i', $shareStr, $matches);
            foreach ($matches[1] as $val) {
                $share_arr = explode('~', $val);
                if ($share_arr) {
                    $turnover = $share_arr[37];
                    if ($turnover > 10000) {
                        $turnover = round($turnover / 10000, 2) . "亿";
                    } else {
                        $turnover .= "万";
                    }
                    $sdata       = $sv[$share_arr[2]];
                    $shareData[] = [
                        'stock_code'            => $sdata['stock_code'],
                        'stock_name'            => $sdata['stock_name'],
                        'stock_type'            => $sdata['stock_type'],
                        'industry_board'        => $sdata['industry_board'],
                        'concept_board'         => $sdata['concept_board'],
                        'description'           => $sdata['description'],
                        'best_price'            => $sdata['best_price'],
                        'sort_type'             => $sdata['sort_type'],
                        'date'                  => $share_arr[30],
                        'cur_price'             => $share_arr[3],//当前价格
                        'yesterday_close_price' => $share_arr[4],//昨收
                        'today_open_price'      => $share_arr[5],//今开
                        'rise_and_fall'         => $share_arr[31],//涨跌
                        'rise_and_fall_rate'    => $share_arr[32],//涨跌
                        'hightest_price'        => $share_arr[32],//最高
                        'lowest_price'          => $share_arr[33],//最低
                        'volume'                => $share_arr[36],//成交量（手）
                        'turnover'              => $turnover,//成交额（万）
                        'turnover_rate'         => $share_arr[38],//换手率
                        'famc'                  => $share_arr[44],//流通市值
                        'eur'                   => $share_arr[45],//总市值
                        'pe'                    => $share_arr[39],//市盈率
                        'pb'                    => $share_arr[46],//市净率
                    ];
                }
            }
        }
        return $shareData;
    }

    /**
     * 估值
     */
    public function valuation()
    {
        $data = $this->getShares();
        $ttm  = [];
        foreach ($data['data']['all'] as $k => $v) {
            if ($v['industry_board'] != 'ETF') {
                $ret                    = $this->getTTM($v);
                $ttm[$v['stock_code']] = ['ttm' => $ret, 'cur_price' => $v['cur_price'], 'pe' => $v['pe']];
            }
        }

        $config = DbConfig::getInstance()->read();
        if (!empty($config['bond_yields'])) {
            foreach ($ttm as $c => $val) {
                if ($val['ttm'] == '--') {
                    $val['ttm'] = 0;
                }
                $ttm_rate = $config['bond_yields'] > 0 ? round($val['ttm'] / $config['bond_yields'], 2) : 0;
                $price    = $val['pe'] > 0 ? round(($val['cur_price'] / $val['pe']) * 15, 2) : 0;

                if ($ttm_rate >= 0 && $price == 0) {
                    $ttm_price = $ttm_rate;
                } else if ($ttm_rate == 0 && $price > 0) {
                    $ttm_price = $price;
                } else if ($ttm_rate > 0 && $price > 0) {
                    $ttm_price = $ttm_rate < $price ? $ttm_rate : $price;
                }

                // $ret = $c.'-('.$ttm_rate.')--('.$price.')-'.$ttm_price;
                // var_dump($ret);
                (new DB())->name('shares')
                    ->where('stock_code', $c)
                    ->update(['best_price' => $ttm_price, 'ttm' => $val['ttm']]);

            }
        }
        return true;
    }

    public function getTTM(array $data)
    {
        $client = new \EasySwoole\HttpClient\HttpClient();
        $client->setUrl('https://xueqiu.com/S/' . strtoupper($data['stock_type']) . $data['stock_code']);
        $response = $client->get();
        $shareStr = $response->getBody();
        preg_match_all('#"tableHtml":"(.*?)","isMF#i', $shareStr, $matches);
        $guxi = 0;
        if (!empty($matches[1][0])) {
            preg_match_all('/<td>(.*?)<\/td>/is', $matches[1][0], $matches_b);
            foreach ($matches_b[1] as $v) {
                $str           = strip_tags($v);
                $arr           = explode('：', $str);
                $data[$arr[0]] = $arr[1];
            }

            $guxi = $data['股息(TTM)'] ?? 0;
        }
        return $guxi;
    }

}