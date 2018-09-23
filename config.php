<?php

/** 
 * Configuration Slim
 *
 * @var $config['displayErrorDetails'] to display error details on slim
 * @var $config['addContentLengthHeader'] should be set to false. This will allows the web server to set the Content-Length header which makes Slim behave more predictably
 * @var $config['httpVersion'] The protocol version used by the Response object. Default is '1.1'. 
 * @var $config['responseChunkSize'] Size of each chunk read from the Response body when sending to the browser. Default is 4096
 * @var $config['outputBuffering'] If false, then no output buffering is enabled. If 'append' or 'prepend', then any echo or print statements are captured and are either appended or prepended to the Response returned from the route callable. Default is 'append'
 * @var $config['determineRouteBeforeAppMiddleware'] When true, the route is calculated before any middleware is executed. This means that you can inspect route parameters in middleware if you need to. Default is false.
 * @var $config['routerCacheFile'] This will make performance faster because php will not always recompile router in each request.
 */
$config['displayErrorDetails']                  = true;
$config['addContentLengthHeader']               = false;
$config['httpVersion']                          = '1.1';
$config['responseChunkSize']                    = 4096;
$config['outputBuffering']                      = 'append';
$config['determineRouteBeforeAppMiddleware']    = false;
//$config['routerCacheFile']                    = 'cache/routes.cache.php'; //Just uncomment if you are in production.

/**
 * Configuration App
 * 
 * @var $config['app']['name'] is the name of your application.
 * @var $config['app']['language'] is language that we use. Default is en means english.
 * @var $config['app']['timezone'] is your default php timezone.
 * @var $config['app']['log']['level'] is the level of logger.
 * @var $config['app']['http']['max-age'] is the lifetime of http cache.
 * @var $config['app']['template']['folder'] is the folder name of your current template.
 * @var $config['app']['template']['options'] is the options of twig template.
 */
$config['app']['name']                  = 'slim-skeleton';
$config['app']['language']              = 'en';
$config['app']['timezone']              = 'Asia/Jakarta';
$config['app']['log']['level']          = \Monolog\Logger::DEBUG;
$config['app']['http']['max-age']       = 604800;
$config['app']['template']['folder']    = 'default';
$config['app']['template']['options']   = [];