<?php

namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\AbstractRouter;
use FastRoute\RouteCollector;

class Router extends AbstractRouter
{
    function initialize(RouteCollector $routeCollector)
    {
        // 拦截多个方法
        $routeCollector->addRoute(['GET', 'POST'], '/api/Infosync/SyncServerinfo/{product}/', '/Infosync/SyncServerinfo');
 
        $routeCollector->addRoute(['GET', 'POST'], '/api/TaskCallback/setTask', '/TaskCallback/setTask');

        $routeCollector->addRoute(['GET', 'POST'], '/task/tool', '/TaskCallback/tool');

        $routeCollector->addRoute(['GET', 'POST'], '/task/add', '/TaskCallback/addTask');

        $routeCollector->addRoute(['GET', 'POST'], '/task/check', '/TaskCallback/checkSql');
    }
}