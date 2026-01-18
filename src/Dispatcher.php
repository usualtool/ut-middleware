<?php
namespace usualtool\Middleware;
class Dispatcher{
    /**
     * 创建指定模式的调度器
     *
     * @param string|null $mode 'sync'|'swoole'|'workerman'，默认为 'sync'
     * @return SyncDispatcher|SwooleDispatcher|WorkermanDispatcher
     * @throws \InvalidArgumentException
     */
    public static function create(?string $mode = null): object{
        // 默认模式为 'sync'
        $mode = $mode ?? 'sync';
        switch ($mode) {
            case 'sync':
                return new SyncDispatcher();

            case 'swoole':
                if (!extension_loaded('swoole')) {
                    throw new \InvalidArgumentException("请安装Swoole和ut-swoole扩展。");
                }
                return new SwooleDispatcher();
            case 'workerman':
                if (!class_exists(\Workerman\Worker::class, false)) {
                    throw new \InvalidArgumentException("请安装ut-Workerman扩展。");
                }
                return new WorkermanDispatcher();
            default:
                throw new \InvalidArgumentException("请指定模式。");
        }
    }
}
