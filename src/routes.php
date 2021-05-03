<?php

use Symfony\Component\Routing;

$routes = new Routing\RouteCollection();
$routes->add('hello', new Routing\Route('/hello/{name}', [
    'name' => null,
    '_controller' => 'Example\Controller\ExampleController::index',
]));

return $routes;
