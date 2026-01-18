<?php
// UsualTool 模块控制器
use usualtool\Middleware\Dispatcher;
use usualtool\Middleware\FinalHandler;
// 创建同步调度器（默认）
$dispatcher = Dispatcher::create();
// 登录检查中间件（直接使用你的代码）
$authCheck = function ($request, $next) {
    if (!isset($_SESSION['uid'])) {
        header('Location: ?m=login');
        exit;
    }
    return $next->handle($request);
};
// 执行中间件链
$dispatcher
    ->pipe($authCheck)
    ->dispatch($_REQUEST, new FinalHandler(function () use ($app) {
        // 渲染页面
        $app->Open('index.cms');
    }));
