<?php
declare(strict_types=1);
/*
 * Include this file to use the recipe without composer autoloader (e.g. when running deployer as phar)
 *
 * Implementation based on https://www.php-fig.org/psr/psr-4/examples/
 */

spl_autoload_register(function ($class) {

    $prefix = 'IntegerNet\\DeployerTimer\\';
    $base_dir = __DIR__ . '/src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});
