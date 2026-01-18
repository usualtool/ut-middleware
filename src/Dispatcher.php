<?php
namespace usualtool\Middleware;
use InvalidArgumentException;
/**
 * 中间件调度器
 * 负责按顺序执行中间件链
 */
class Dispatcher{
    /**
     * @var array 中间件栈（支持 callable 或 MiddlewareInterface）
     */
    private array $stack = [];

    /**
     * 添加中间件
     *
     * @param callable|MiddlewareInterface $middleware
     * @return self
     */
    public function pipe($middleware): self{
        if (!is_callable($middleware) && !$middleware instanceof MiddlewareInterface) {
            throw new InvalidArgumentException(
                'Middleware must be a callable or implement MiddlewareInterface.'
            );
        }
        $this->stack[] = $middleware;
        return $this;
    }

    /**
     * 执行中间件管道
     *
     * @param array|object $request 请求上下文
     * @param RequestHandlerInterface $finalHandler 最终处理器
     * @return mixed 响应
     */
    public function dispatch($request, RequestHandlerInterface $finalHandler){
        $next = $finalHandler;
        foreach (array_reverse($this->stack) as $middleware) {
            $next = new class($middleware, $next) implements RequestHandlerInterface {
                private $middleware;
                private $next;

                public function __construct($middleware, RequestHandlerInterface $next)
                {
                    $this->middleware = $middleware;
                    $this->next = $next;
                }

                public function handle($request)
                {
                    if ($this->middleware instanceof MiddlewareInterface) {
                        return $this->middleware->process($request, $this->next);
                    } else {
                        return ($this->middleware)($request, $this->next);
                    }
                }
            };
        }
        return $next->handle($request);
    }
}