<?php
namespace usualtool\Middleware;
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
                    return $middleware->process($req, (object)['handle' => $next]);
                }
                return $middleware($req, (object)['handle' => $next]);
            };
        }
        return $next($request);
    }
}
