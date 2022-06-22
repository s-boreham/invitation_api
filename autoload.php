<?php

spl_autoload_register(function ($class) {

    $prefix = 'App\\';
    
    $base_dir = __DIR__ . '/src/';
    
    $len = strlen($prefix);
    # compares the first $len characters of $prefix to $class
    # strncmp returns 0 only if both strings match (case senstitive)
    if (strncmp($prefix, $class, $len) !== 0) {
    
        return;
    }
    
    $relative_class = substr($class, $len);
    
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }

});