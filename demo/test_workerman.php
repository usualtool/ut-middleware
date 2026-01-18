<?php
// Workerman异步模式
use Workerman\Worker;
use UsualTool\Middleware\Dispatcher;
use UsualTool\Middleware\FinalHandler;
// 创建 HTTP Worker
$worker = new Worker('http://0.0.0.0:8787');
$worker->onMessage = function ($connection, $request) {
    // 从 GET 参数获取 uid
    $uri = $request->uri();
    parse_str(parse_url($uri, PHP_URL_QUERY) ?: '', $queryParams);
    $uid = $queryParams['uid'] ?? null;
    $utRequest = [
        'uri' => $uri,
        'uid' => $uid,
        'get' => $queryParams
    ];
    // 显式使用 Workerman 模式
    $dispatcher = Dispatcher::create('workerman');
    // 认证中间件
    $authCheck = function ($req, $next) {
        if (empty($req['uid'])) {
            // 构造 HTTP 302 响应字符串
            return "HTTP/1.1 302 Found\r\nLocation: ?m=login\r\nConnection: close\r\n\r\n";
        }
        return $next->handle($req);
    };
    // 最终处理器
    $finalHandler = new FinalHandler(function ($r) {
        return "HTTP/1.1 200 OK\r\nContent-Type: text/plain\r\nConnection: close\r\n\r\n" .
               "Hello UID {$r['uid']}! You are in Workerman.";
    });
    // 执行调度
    $response = $dispatcher->pipe($authCheck)->dispatch($utRequest, $finalHandler);
    // 发送响应（字符串）
    $connection->send($response);
};
echo "Workerman server started on http://127.0.0.1:8787\n";
Worker::runAll();
