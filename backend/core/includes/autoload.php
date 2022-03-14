<?php

define('ROOT_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);

spl_autoload_register(function($className) {

    $file = ROOT_PATH . str_replace('\\', '/', $className) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});


