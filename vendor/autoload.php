<?php
// vendor/autoload.php - minimal stub for demo (if composer not run)
spl_autoload_register(function($class){
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../src/';
    if (str_starts_with($class, $prefix)) {
        $relative = substr($class, strlen($prefix));
        $path = $base_dir . str_replace('\\', '/', $relative) . '.php';
        if (file_exists($path)) require $path;
    }
});
