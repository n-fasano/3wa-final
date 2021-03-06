<?php

require_once 'vendor/autoload.php';
require_once 'config.php';

use App\Controller\Dto\Base\DtoArgumentValueResolver;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

$routes = require_once 'routes.php';

$request = Request::createFromGlobals();
$matcher = new UrlMatcher($routes, new RequestContext());

$dispatcher = new EventDispatcher();
$dispatcher->addSubscriber(new RouterListener($matcher, new RequestStack()));

$controllerResolver = new ControllerResolver();
$argumentResolver = new ArgumentResolver(
    null,
    array_merge(
        ArgumentResolver::getDefaultArgumentValueResolvers(),
        [new DtoArgumentValueResolver]
    )
);

$kernel = new HttpKernel($dispatcher, $controllerResolver, new RequestStack(), $argumentResolver);

try {
    $response = $kernel->handle($request);
} catch (BadRequestHttpException $exception) {
    $response = new Response(json_encode(['error' => $exception->getMessage()]), 400);
} catch (NotFoundHttpException|MethodNotAllowedHttpException $exception) {
    $response = new Response(null, 404);
    die(json_encode(['error' => $exception->getMessage()]));
} catch (Exception $exception) {
    die(json_encode(['error' => $exception->getMessage()]));
    $response = new Response(
        "Something is wrong. If this persists, please contact the site's administrator: fasano.nm@gmail.com", 
        500
    );
}

$response->send();

$kernel->terminate($request, $response);