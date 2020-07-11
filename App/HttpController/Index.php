<?php


namespace App\HttpController;


use App\Service\BoardService;
use App\Service\StockService;
use App\Utility\DB;
use EasySwoole\EasySwoole\Core;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Trigger;
use EasySwoole\Http\AbstractInterface\Controller;

class Index extends Controller
{

    public function index()
    {
        $shareData = StockService::getInstance()->getShares();
        $this->response()->write(json_encode($shareData));
        //$this->response()->write($this->clientRealIP( 'x-real-ip'));
    }

    /**
     * 板块
     * @throws \EasySwoole\HttpClient\Exception\InvalidUrl
     */
    public function board()
    {
        $boardData = BoardService::getInstance()->getBorad();
        $this->response()->write(json_encode($boardData));
    }


    protected function actionNotFound(?string $action)
    {
        $this->response()->withStatus(404);
        $this->response()->write(404);
    }

    protected function onException(\Throwable $throwable): void
    {
        if ($throwable instanceof ParamAnnotationValidateError) {
            $msg = $throwable->getValidate()->getError()->getErrorRuleMsg();
            $this->writeJson(400, null, "{$msg}");
        } else {
            if (Core::getInstance()->isDev()) {
                $this->writeJson(500, null, $throwable->getMessage());
            } else {
                Trigger::getInstance()->throwable($throwable);
                $this->writeJson(500, null, '系统内部错误，请稍后重试');
            }
        }
    }

    /**
     * 获取用户的真实IP
     * @param string $headerName 代理服务器传递的标头名称
     * @return string
     */
    protected function clientRealIP(string $headerName = 'x-real-ip')
    {
        $server        = ServerManager::getInstance()->getSwooleServer();
        $client        = $server->getClientInfo($this->request()->getSwooleRequest()->fd);
        $clientAddress = $client['remote_ip'];
        $xri           = $this->request()->getHeader($headerName);
        $xff           = $this->request()->getHeader('x-forwarded-for');
        if ($clientAddress === '127.0.0.1') {
            if (!empty($xri)) {  // 如果有xri 则判定为前端有NGINX等代理
                $clientAddress = $xri[0];
            } elseif (!empty($xff)) {  // 如果不存在xri 则继续判断xff
                $list = explode(',', $xff[0]);
                if (isset($list[0])) $clientAddress = $list[0];
            }
        }
        return $clientAddress;
    }
}