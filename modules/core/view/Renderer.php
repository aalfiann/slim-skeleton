<?php
namespace modules\core\view;
    class Renderer {
        public static function view($app,$path,$options){
            $view = new \Slim\Views\Twig($path,$options);
            $basePath = rtrim(str_ireplace('index.php', '', $app->get('request')->getUri()->getBasePath()), '/');
            $view->addExtension(new \modules\core\twig\GlobalTwigVariable($app->get('settings')['app']['template']['variable']));
            $view->addExtension(new \modules\core\twig\CsrfExtension($app->get('csrf')));
            $view->addExtension(new \Slim\Views\TwigExtension($app->get('router'), $basePath));
            return $view;
        }
    }