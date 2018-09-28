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