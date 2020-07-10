<?php

namespace App\Service;

use App\Utility\DB;
use EasySwoole\Component\Singleton;


class BoardService
{
    use Singleton;

    /**
     * 获取板块数据
     * @return array
     * @throws \EasySwoole\HttpClient\Exception\InvalidUrl
     */
    public function getBorad()
    {
        $board_config = [
            //行业板块
            'industry_board' => 'http://24.push2.eastmoney.com/api/qt/clist/get?cb=jQuery1124080742427780561_1591501849175&pn=1&pz=1000&po=1&np=1&ut=bd1d9ddb04089700cf9c27f6f7426281&fltt=2&invt=2&fid=f3&fs=m:90+t:2+f:!50&fields=f1,f2,f3,f4,f5,f6,f7,f8,f9,f10,f12,f13,f14,f15,f16,f17,f18,f20,f21,f23,f24,f25,f26,f22,f33,f11,f62,f128,f136,f115,f152,f124,f107,f104,f105,f140,f141,f207,f222&_=1591501849190',
            //概念板块
            'concept_board'  => 'http://16.push2.eastmoney.com/api/qt/clist/get?cb=jQuery112409443398939235756_1591502067211&pn=1&pz=1000&po=1&np=1&ut=bd1d9ddb04089700cf9c27f6f7426281&fltt=2&invt=2&fid=f3&fs=m:90+t:3+f:!50&fields=f1,f2,f3,f4,f5,f6,f7,f8,f9,f10,f12,f13,f14,f15,f16,f17,f18,f20,f21,f23,f24,f25,f26,f22,f33,f11,f62,f128,f136,f115,f152,f124,f107,f104,f105,f140,f141,f207,f222&_=1591502067220',
        ];
        $boardData    = [];
        $wait         = new \Swoole\Coroutine\WaitGroup();
        foreach ($board_config as $key => $board) {
            $wait->add();
            go(function () use ($wait, &$boardData, $key, $board) {
                $client = new \EasySwoole\HttpClient\HttpClient();
                $client->setUrl($board);
                $response = $client->get();
                $result   = $response->getBody();
                if (!empty($result)) {
                    preg_match_all("/(?:\()(.*)(?:\))/i", $result, $m_result);
                    $data = json_decode($m_result[1][0], true);
                    $arr  = $data['data']['diff'] ?: [];
                    $i    = 1;
                    foreach ($arr as $k => $v) {
                        $boardData[$key][] = [
                            'rank'               => $i,
                            'board_name'         => $v['f14'],
                            'cur_price'          => $v['f2'],
                            'rise_and_fall'      => $v['f4'],
                            'rise_and_fall_rate' => $v['f3'],
                            'turnover_rate'      => $v['f8'],
                            'company_up'         => $v['f104'],
                            'company_down'       => $v['f105'],
                            'shares_name'        => $v['f128'],
                            'shares_code'        => $v['f140'],
                            'shares_rate'        => $v['f136'],
                        ];
                        $i++;
                    }

                }
                $wait->done();
            });
        }
        $wait->wait();

        return $boardData;
    }

    /**
     * 更新股票数据的板块
     * @return bool
     */
    public function updateBorad()
    {
        $sharesService = new SharesService();
        $s_data        = $sharesService->getShaersDataFormDb();
        $shareArray    = [];
        $wait          = new \Swoole\Coroutine\WaitGroup();
        foreach ($s_data as $key => $v) {
            if(in_array($v['sort_type'],['ETF','指数'])){
                continue;
            }
            $wait->add();
            go(function () use ($wait, &$shareArray, $key, $v) {
                $client = new \EasySwoole\HttpClient\HttpClient();
                //默认是普通板块
                $share  = strtolower($v['shares_type']) . $v['shares_code'];

                $client->setUrl('http://quote.eastmoney.com/' . $share . '.html');
                $response = $client->get();
                $result   = $response->getBody();
                if (!empty($result)) {
                    $regex4 = "/<td class=\"tit\".*?>.*?<\/td>/ism";
                    if (preg_match_all($regex4, $result, $matches)) {
                        $shareArray[$v['shares_code']] = trim(strip_tags($matches[0][0]));
                    }
                }
                $wait->done();
            });
        }
        $wait->wait();

        var_dump($shareArray);

        if ($shareArray) {
            $db     = new DB();
            $dbname = 'shares';
            //事务操作
            try {
                //开启事务
                $db->startTransaction();
                foreach ($shareArray as $k => $v) {
                    $db->name($dbname)->where('shares_code',$k)->update(['industry_board' => $v]);
                }

            } catch (\Throwable  $e) {
                //回滚事务
                $db->rollback();
            } finally {
                //提交事务
                $db->commit();
            }
        }

        return true;
    }
}
