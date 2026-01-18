<?php
namespace usualtool\Middleware;
use usualtool\Middleware\RequestHandlerInterface;
//普通模式
class SyncDispatcher{
    private array $stack = [];
    public function pipe($middleware): self{
        $this->stack[] = $middleware;
        return $this;
    }
    public function dispatch($request, callable $finalHandler){
        $next = $finalHandler;
        foreach (array_reverse($this->stack) as $middleware) {
            $next = function ($req) use ($middleware, $next) {
                if (is_object($middleware) && method_exists($middleware, 'process')) {
                    return $middleware->process($req, new class($next) implements RequestHandlerInterface {
                        private $handler;
                        public function __construct(callable $handler){
                            $this->handler = $handler;
                        }
                        public function handle($request){
                            return ($this->handler)($request);
                        }
                    });
                }
                return $middleware($req, new class($next) implements RequestHandlerInterface {
                    private $handler;
                    public function __construct(callable $handler){
                        $this->handler = $handler;
                    }
                    public function handle($request){
                        return ($this->handler)($request);
                    }
                });
            };
        }
        return $next($request);
    }
}
