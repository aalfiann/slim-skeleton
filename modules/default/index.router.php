<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \modules\core\helper\EtagHelper;

    // Show index page
    $app->get('/', function (Request $request, Response $response) {
        $body = $response->getBody();
        $response = $this->cache->withEtag($response, EtagHelper::updateByMinute());
        return $this->view->render($response, "index.twig", [
            'welcome' => 'Hello World, here is the default index page.',
            'message' => 'This is a very simple, fast and secure of slim-skeleton.',
            'author' => [
                'name' => 'M ABD AZIZ ALFIAN',
                'email' => 'aalfiann@gmail.com',
                'github' => 'https://github.com/aalfiann',
                'linkedin' => 'https://www.linkedin.com/in/azizalfian'
            ]
        ]);
    })->setName("/");

    // Show detail info about routes
    $app->map(['GET','POST'],'/route/info',function(Request $request, Response $response) use($container) {
        $body = $request->getBody();
        $routes = $container->get('router')->getRoutes();
        foreach($routes as $route){
            $data[] = [
                'identifier' => $route->getIdentifier(),
                'name' => $route->getName(),
                'pattern' => $route->getPattern(),
                'methods' => $route->getMethods(),
                'middleware' => count($route->getMiddleware())
            ];
        }
        $body->write(json_encode($data,JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT));
        return $response
            ->withStatus(200)
            ->withHeader('Content-Type','application/json; charset=utf-8')
            ->withBody($body);
    });