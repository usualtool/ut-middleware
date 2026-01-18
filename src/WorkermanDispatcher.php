<?php
namespace usualtool\Middleware;
/**
 * Workerman 中间件调度器
 *
 * Workerman 默认是多进程/多线程模型，非协程。
 * 本调度器预留了集成 Workerman 异步组件的能力。
 */
class WorkermanDispatcher{
    /**
     * @var array 中间件栈
     */
    private array $stack = [];
    /**
     * 添加中间件
     * @param callable|object $middleware
     * @return self
     */
    public function pipe($middleware): self{
        $this->stack[] = $middleware;
        return $this;
    }
    /**
     * 调度执行中间件链
     *
     * @param array|object $request 请求上下文
     * @param callable $finalHandler 最终处理器
     * @return mixed 最终响应
     */
    public function dispatch($request, callable $finalHandler){
        $next = $finalHandler;
        foreach (array_reverse($this->stack) as $middleware) {
            $next = function ($req) use ($middleware, $next) {
                if (is_object($middleware) && method_exists($middleware, 'process')) {
                    return $middleware->process($req, (object)['handle' => $next]);
                } else {
                    return $middleware($req, (object)['handle' => $next]);
                }
            }
        }
        return $next($request);
    }
}
