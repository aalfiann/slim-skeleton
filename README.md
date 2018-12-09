# Slim-Skeleton

[![Version](https://img.shields.io/badge/stable-1.5.2-green.svg)](https://github.com/aalfiann/slim-skeleton)
[![Total Downloads](https://poser.pugx.org/aalfiann/slim-skeleton/downloads)](https://packagist.org/packages/aalfiann/slim-skeleton)
[![License](https://poser.pugx.org/aalfiann/slim-skeleton/license)](https://github.com/aalfiann/slim-skeleton/blob/HEAD/LICENSE.md)

This is a very simple, fast and secure of slim-skeleton.  
This skeleton is secured with CSRF, fast, simple and modular architecture.  
The architecture concept is **SPA** (Single Page Application).

## Dependencies
- CSRF Guard >> slim/csrf
- TWIG Template >> slim/twig-view
- HTTP Cache >> slim/http-cache
- Logger >> monolog/monolog

## Installation

Install this package via [Composer](https://getcomposer.org/).
```
composer create-project aalfiann/slim-skeleton [my-app-name]
```

## Getting Started

### How to create new application
- Go to modules directory
- Create new folder `my-app`
- To create routes, you should follow this pattern >> `*.router.php`
- Put the view template to `templates/default` directory
- Done

### How to activate CSRF
CSRF is already integrated in this skeleton :  
1. Create same two routes, GET and POST  
```
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// load contact page
$app->get('/contact', function (Request $request, Response $response) {
    $body = $response->getBody();
    return $this->view->render($response, "contact.twig", []);
})->setName("/contact")->add($container->get('csrf'));

// send message
$app->post('/contact', function (Request $request, Response $response) {
    $body = $response->getBody();
    return $this->view->render($response, "contact.twig", []);
})->add($container->get('csrf'));
```  
2. Put hidden input value in contact form HTML  
```
<input type="hidden" name="{{csrf.keys.name}}" value="{{csrf.name}}">
<input type="hidden" name="{{csrf.keys.value}}" value="{{csrf.value}}">
```  
3. Done

**Note:**  
- Documentation about `Slim` is available on [slimframework.com](http://slimframework.com).
- This is a forked version from the original [slimphp/Slim-Skeleton](https://github.com/slimphp/Slim-Skeleton).