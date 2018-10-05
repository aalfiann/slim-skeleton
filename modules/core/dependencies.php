<?php

// Create container
$container = $app->getContainer();
$settings = $container->get('settings');

// Add middleware http-cache for all routes
$app->add(new \Slim\HttpCache\Cache('public',$settings['app']['http']['max-age']));

/**
 * Activating routes in a subfolder
 * Note:
 * - This $container['environment'] doesn't work for NGINX, 
 *      so you still have to set path >> root /var/www/yourdomain.com/public;
 * - Better to comment or delete this $container['environment'] to prevent useless memory PHP-FPM in NGINX
 */
$container['environment'] = function () {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $_SERVER['SCRIPT_NAME'] = dirname(dirname($scriptName)) . '/' . basename($scriptName);
    return new Slim\Http\Environment($_SERVER);
};

// Register site url
$container['site_url'] = function ($container) {
    if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || 
        isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
        return 'https://'.$_SERVER['HTTP_HOST'];
    }
    return 'http://'.$_SERVER['HTTP_HOST'];
};

// Register site url canonical
$container['site_url_canonical'] = function ($container) {
    return $container['site_url'].$_SERVER['REQUEST_URI'];
};

// Register visitor ip
$container['visitor_ip'] = function(){
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
	$remote  = $_SERVER['REMOTE_ADDR'];
	if(filter_var($client, FILTER_VALIDATE_IP)) {
        $ip = $client;
	} elseif(filter_var($forward, FILTER_VALIDATE_IP)) {
	    $ip = $forward;
	} else {
        $ip = $remote;
	}
	return $ip;
};

// Register CSRF Guard 
$container['csrf'] = function ($container) {
    $guard = new \Slim\Csrf\Guard();
    $guard->setFailureCallable(function ($request, $response, $next) {
        $request = $request->withAttribute("csrf_status", false);
        if (false === $request->getAttribute('csrf_status')) {
            $data = [
                'status' => 'error',
                'code' => '400',
                'message' => $response->withStatus(400)->getReasonPhrase()
            ];
            return \modules\core\view\Renderer::view($this,'../modules/core/view/handler',
                $container['settings']['app']['template']['options'])
                    ->render($response->withStatus(400), "400.twig",$data);
        } else {
            return $next($request, $response);
        }
    });
    return $guard;
};

// Register component Http-cache
$container['cache'] = function () {
    return new \Slim\HttpCache\CacheProvider();
};

// Register component Monolog
$container['logger'] = function($container) {
    $logger = new \Monolog\Logger($container['settings']['app']['name']);
    $file_handler = new \Monolog\Handler\StreamHandler("../logs/app.log",$container['settings']['app']['log']['level']);
    $formatter = new \Monolog\Formatter\LineFormatter(null, null, false, true);
    $file_handler->setFormatter($formatter);
    $logger->pushHandler($file_handler);
    return $logger;
};

// Register component view to render page using twig
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig('../'.$container['settings']['app']['template']['folder'],
        $container['settings']['app']['template']['options']
    );
    $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new modules\core\twig\GlobalTwigVariable($container['settings']['app']['template']['variable']));
    $view->addExtension(new modules\core\twig\CsrfExtension($container['csrf']));
    $view->addExtension(new modules\core\twig\SlugifyExtension);
    $view->addExtension(new modules\core\twig\PHPFunctionExtension);
    $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));
    return $view;
};

// Override the default Not Found Handler
$container['notFoundHandler'] = function ($container) {
    return function ($request, $response) use ($container) {
        $data = [
            'status' => 'error',
            'code' => '404',
            'message' => $response->withStatus(404)->getReasonPhrase()
        ];
        return \modules\core\view\Renderer::view($container,get_template_handler('../'.$container['settings']['app']['template']['handler'],'404.twig'),
            $container['settings']['app']['template']['options'])
                ->render($response->withStatus(404), "404.twig",$data);
    };
};

// Override the default Not Allowed Handler
$container['notAllowedHandler'] = function ($container) {
    return function ($request, $response, $methods) use ($container) {
        $data = [
            'status' => 'error',
            'code' => '405',
            'message' => $response->withStatus(405)->getReasonPhrase().', method must be one of: ' . implode(', ', $methods)
        ];
        return \modules\core\view\Renderer::view($container,get_template_handler('../'.$container['settings']['app']['template']['handler'],'405.twig'),
            $container['settings']['app']['template']['options'])
                ->render($response->withStatus(405)->withHeader('Allow', implode(', ', $methods)), "405.twig",$data);
    };
};

// Override the slim error handler
$container['errorHandler'] = function ($container) {
    return function ($request, $response, $exception) use ($container) {
        $container->logger->addInfo('{ 
"code": '.json_encode($exception->getCode()).', 
"message": '.json_encode($exception->getMessage()).'}',['file'=>$exception->getFile(),'line'=>$exception->getLine()]);
        $response->getBody()->rewind();
        if($container['settings']['displayErrorDetails']){
            $data = [
                'status' => 'error',
                'code' => '500',
                'error_code' => $exception->getCode(),
                'message' => trim($exception->getMessage()),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => explode("\n", $exception->getTraceAsString())
            ];
        } else {
            $data = [
                'status' => 'error',
                'code' => '500',
                'message' => 'Something went wrong!',
            ];
        }
        return \modules\core\view\Renderer::view($container,get_template_handler('../'.$container['settings']['app']['template']['handler'],'500.twig'),
            $container['settings']['app']['template']['options'])
                ->render($response->withStatus(500), "500.twig",$data);
    };
};

// Override PHP 7 error handler
$container['phpErrorHandler'] = function ($container) {
    return $container['errorHandler'];
};

// PHP Notice Handler
set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return;
    }
    throw new \ErrorException($message, 0, $severity, $file, $line);
});