<?php
namespace usualtool\Middleware;
/**
 * 中间件接口
 */
interface MiddlewareInterface{
    /**
     * 处理请求
     *
     * @param array|object $request 请求上下文
     * @param RequestHandlerListener $next 下一个处理器
     * @return mixed 响应
     */
    public function process($request, RequestHandlerInterface $next);
}