<?php

class Autoloader
{
    public static function register(): void
    {
        spl_autoload_register(function (string $class): void {
            $prefix = 'src\\';
            if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
                return;
            }

            $relativeClass = substr($class, strlen($prefix));
            $file = __DIR__ . DIRECTORY_SEPARATOR
                . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';

            if (is_file($file)) {
                require_once $file;
            }
        });
    }
}

Autoloader::register();