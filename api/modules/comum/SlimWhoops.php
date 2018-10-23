<?php
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;

class SlimWhoops {
    protected static $whoops;

    public static function init() {
        if (!self::$whoops) {
            $run = new Run;
            $handler = new PrettyPageHandler;
            $run->pushHandler($handler);
            $run->register();

            self::$whoops = $run;
        }
    }

    public static function integrate($exception) {
        $whoops = self::$whoops;
        $handler = Run::EXCEPTION_HANDLER;
        $whoops->$handler($exception);
    }
}
?>