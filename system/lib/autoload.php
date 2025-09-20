<?php

spl_autoload_register(function ($class) {
    $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.class.php';
    $baseDir = __DIR__ . '/../../';
    $file = $baseDir . $classPath;
    if (file_exists($file)) {
        require_once $file;
    } else {
        echo "Autoloader: file not found: $file";
    }
});
