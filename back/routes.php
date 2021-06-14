<?php

use App\Controller\AuthController;
use App\Controller\HomeController;
use App\Controller\ThreadController;
use App\Controller\UserController;
use App\Service\Route;
use App\Service\RouteCollectionBuilder;

$routes = new RouteCollectionBuilder('/api');

$routes->add('welcome', Route::get('', [HomeController::class, 'welcome']));

$routes->add('register', Route::post('/register', [AuthController::class, 'register']));
$routes->add('login', Route::post('/login', [AuthController::class, 'login']));
$routes->add('logout', Route::get('/logout', [AuthController::class, 'logout']));
$routes->add('logged', Route::get('/logged', [AuthController::class, 'logged']));

$routes->add('threads', Route::get('/threads', [ThreadController::class, 'list']));
$routes->add('threads_new', Route::post('/threads', [ThreadController::class, 'create']));

$routes->add('users', Route::get('/users', [UserController::class, 'list']));

return $routes->build();