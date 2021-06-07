<?php

use App\Controller\AuthController;
use App\Controller\HomeController;
use App\Controller\ThreadController;
use App\Service\Route;
use App\Service\RouteCollectionBuilder;

$routes = new RouteCollectionBuilder('/api');

$routes->add('welcome', Route::get('', [HomeController::class, 'welcome']));

$routes->add('show_thread', Route::get('/thread/{id}', [ThreadController::class, 'show']));

$routes->add('logged', Route::get('/logged', [AuthController::class, 'logged']));
$routes->add('login', Route::post('/login', [AuthController::class, 'login']));
$routes->add('logout', Route::get('/logout', [AuthController::class, 'logout']));
$routes->add('register', Route::post('/register', [AuthController::class, 'register']));

return $routes->build();