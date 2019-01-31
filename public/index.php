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
        if(is_file($path.DIRECTORY_SEPARATOR.$filename)) return $path;
        return 'modules'.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'handler';
    }
}

// Load vendor
require dirname(__DIR__).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
// Load config
require dirname(__DIR__).DIRECTORY_SEPARATOR.'config.php';

// Load classes
spl_autoload_register(function ($classname) {
    require (realpath(__DIR__ . '/..').DIRECTORY_SEPARATOR.str_replace('\\', DIRECTORY_SEPARATOR, $classname) . '.php');
});

// Set time zone
date_default_timezone_set($config['app']['timezone']);

session_start();

// Initialize Slim App
$app = new \Slim\App(["settings" => $config]);

require dirname(__DIR__).DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.'dependencies.php';

// Load all modules router files before run
$modrouters = \modules\core\helper\Scanner::fileSearch(dirname(__DIR__).DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR,'router.php');
foreach ($modrouters as $modrouter) {
    require $modrouter;
}

// Release unecessary memory
unset($modrouters);

$app->run();
