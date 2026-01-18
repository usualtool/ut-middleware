<?php
namespace usualtool\Middleware;
/**
 * 请求处理器接口
 */
interface RequestHandlerInterface{
    /**
     * 处理请求并返回响应
     * @param array|object $request 请求上下文
     * @return mixed 响应
     */
    public function handle($request);

}
