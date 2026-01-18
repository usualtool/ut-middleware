<?php
// Swoole协程模式
use Swoole\Http\Request;
use Swoole\Http\Response;
use UsualTool\Middleware\Dispatcher;
use UsualTool\Middleware\FinalHandler;
$server = new Swoole\Http\Server('0.0.0.0', 9501);
$server->on('request', function (Request $req, Response $res) {
    // 模拟从 Cookie 或 Token 获取 uid（不能用 $_SESSION）
    $uid = null;
    if (isset($req->cookie['uid'])) {
        $uid = (int)$req->cookie['uid'];
    } elseif (isset($req->get['uid'])) {
        $uid = (int)$req->get['uid'];
    }
    $utRequest = [
        'uri' => $req->server['request_uri'] ?? '/',
        'uid' => $uid,
        'get' => $req->get,
        'post' => $req->post,
        'cookie' => $req->cookie
    ];
    // 显式使用 Swoole 模式
    $dispatcher = Dispatcher::create('swoole');
    // 认证中间件（不能 exit！必须 return）
    $authCheck = function ($request, $next) {
        if (empty($request['uid'])) {
            // 返回重定向指令（结构化数据）
            return [
                'type' => 'redirect',
                'location' => '?m=login'
            ];
        }
        return $next->handle($request);
    };
    // 最终处理器
    $finalHandler = new FinalHandler(function ($r) {
        return [
            'status' => 'success',
            'message' => 'Welcome to admin panel!',
            'uid' => $r['uid']
        ];
    });
    // 执行调度
    $result = $dispatcher->pipe($authCheck)->dispatch($utRequest, $finalHandler);
    // 处理响应类型
    if (is_array($result) && ($result['type'] ?? null) === 'redirect') {
        // 发送 302 重定向
        $res->status(302);
        $res->header('Location', $result['location']);
        $res->end();
    } else {
        // 发送 JSON 响应
        $res->header('Content-Type', 'application/json');
        $res->end(json_encode($result));
    }
});
echo "Swoole server started on http://127.0.0.1:9501\n";
$server->start();
