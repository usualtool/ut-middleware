<?php
namespace usualtool\Middleware;
//最终处理器
class FinalHandler implements RequestHandlerInterface{
    private $callback;
    public function __construct(callable $callback){
        $this->callback = $callback;
    }
    public function handle($request){
        return ($this->callback)($request);
    }
}

