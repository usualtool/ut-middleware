<?php
namespace usualtool\Middleware;
class Dispatcher{
    /**
     * 创建调度器实例
     *
     * @param string $mode 'sync' (default), 'swoole', or 'workerman'
     * @return object 返回对应调度器实例
     * @throws \InvalidArgumentException
     */
    public static function create(string $mode = 'sync'): object{
        switch ($mode) {
            case 'sync':
                return new SyncDispatcher();
            case 'swoole':
                if (!extension_loaded('swoole')) {
                    throw new \InvalidArgumentException('必须安装Swoole扩展。');
                }
                return new SwooleDispatcher();
            case 'workerman':
                if (!class_exists(\Workerman\Worker::class, false)) {
                    throw new \InvalidArgumentException('必须安装Workerman扩展。');
                }
                return new WorkermanDispatcher();
            default:
                throw new \InvalidArgumentException('模式必须为sync/swoole/workerman中的一种。');
        }
    }
}
