<?php

namespace marvin255\fias;

/**
 * Класс для автозагрузчика, который используется в случае, если composer не доступен.
 */
class Autoloader
{
    /**
     * @param string
     */
    protected static $path = null;

    /**
     * @param string $path
     *
     * @return bool
     */
    public static function register($path = null)
    {
        self::$path = $path ? $path : dirname(__FILE__);
        return spl_autoload_register([__CLASS__, 'load'], true, true);
    }

    /**
     * @param string $class
     */
    public static function load($class)
    {
        $prefix = __NAMESPACE__.'\\';
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }
        $relativeClass = substr($class, $len);
        $file = self::$path.'/'.str_replace('\\', '/', $relativeClass).'.php';
        if (file_exists($file)) {
            require $file;
        }
    }
}

Autoloader::register(dirname(__FILE__));
