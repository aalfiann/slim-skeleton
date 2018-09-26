<?php
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) return false;
}

// Set up template handler
if (!function_exists('get_template_handler')) {
    function get_template_handler($path,$filename){
        if(is_file($path.'/'.$filename)) return $path;
        return 'modules/core/view/handler';
    }
}

// Set up scanner files
if (!function_exists('glob_recursive')) {
    function glob_recursive($pattern, $flags = 0){
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir){
            $files = array_merge($files, glob_recursive($dir.'/'.basename($pattern), $flags));
        }
        return $files;
    }
}

// Load vendor
require dirname(__DIR__).'/vendor/autoload.php';
// Load config
require dirname(__DIR__).'/config.php';

// Load classes
spl_autoload_register(function ($classname) {
    require (realpath(__DIR__ . '/..'). '/'.str_replace('\\', DIRECTORY_SEPARATOR, $classname) . '.php');
});

// Set time zone
date_default_timezone_set($config['app']['timezone']);

session_start();

// Initialize Slim App
$app = new \Slim\App(["settings" => $config]);

require dirname(__DIR__).'/modules/core/dependencies.php';

// Load all modules router files before run
$modrouters = glob_recursive(dirname(__DIR__).'/modules/*.router.php',GLOB_NOSORT);
foreach ($modrouters as $modrouter) {
    require $modrouter;
}

// Release unecessary memory
unset($modrouters);

$app->run();

?>