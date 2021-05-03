<?php

use Framework\Framework;
use Symfony\Component\DependencyInjection;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher;
use Symfony\Component\HttpFoundation;
use Symfony\Component\HttpKernel;
use Symfony\Component\Routing;
use Framework\StringResponseListener;

$containerBuilder = new DependencyInjection\ContainerBuilder();

$containerBuilder->setParameter('charset', 'UTF-8');
$containerBuilder->setParameter('debug', true);
$containerBuilder->setParameter('routes', include __DIR__.'/../src/routes.php');

$containerBuilder->register('context', Routing\RequestContext::class);
$containerBuilder->register('matcher', Routing\Matcher\UrlMatcher::class)
    ->setArguments([$routes, new Reference('context')])
;
$containerBuilder->register('request_stack', HttpFoundation\RequestStack::class);
$containerBuilder->register('controller_resolver', HttpKernel\Controller\ControllerResolver::class);
$containerBuilder->register('argument_resolver', HttpKernel\Controller\ArgumentResolver::class);

$containerBuilder->register('listener.router', HttpKernel\EventListener\RouterListener::class)
    ->setArguments([new Reference('matcher'), new Reference('request_stack')])
;
$containerBuilder->register('listener.response', HttpKernel\EventListener\ResponseListener::class)
    ->setArguments(['%charset%'])
;
$containerBuilder->register('listener.exception', HttpKernel\EventListener\ErrorListener::class)
    ->setArguments(['Exception\Controller\ExceptionController::exception'])
;
$containerBuilder->register('dispatcher', EventDispatcher\EventDispatcher::class)
    ->addMethodCall('addSubscriber', [new Reference('listener.router')])
    ->addMethodCall('addSubscriber', [new Reference('listener.response')])
    ->addMethodCall('addSubscriber', [new Reference('listener.exception')])
;
$containerBuilder->register('framework', Framework::class)
    ->setArguments([
        new Reference('dispatcher'),
        new Reference('controller_resolver'),
        new Reference('request_stack'),
        new Reference('argument_resolver'),
    ])
;

$containerBuilder->register('listener.string_response', StringResponseListener::class);
$containerBuilder->getDefinition('dispatcher')
    ->addMethodCall('addSubscriber', [new Reference('listener.string_response')])
;

$containerBuilder->register('matcher', Routing\Matcher\UrlMatcher::class)
    ->setArguments(['%routes%', new Reference('context')])
;

return $containerBuilder;
