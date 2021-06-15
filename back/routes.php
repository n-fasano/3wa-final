<?php

use App\Controller\AuthController;
use App\Controller\HomeController;
use App\Controller\MessageController;
use App\Controller\ThreadController;
use App\Controller\UserController;
use App\Service\Route;
use App\Service\RouteCollectionBuilder;

$routes = new RouteCollectionBuilder('/api');

$routes->add('welcome', Route::get('', [HomeController::class, 'welcome']));

$routes->add('register', Route::post('/register', [AuthController::class, 'register']));
$routes->add('login', Route::post('/login', [AuthController::class, 'login']));
$routes->add('logout', Route::get('/logout', [AuthController::class, 'logout']));
$routes->add('credentials', Route::get('/credentials', [AuthController::class, 'credentials']));

$routes->add('threads', Route::get('/threads', [ThreadController::class, 'list']));
$routes->add('threads_new', Route::post('/threads', [ThreadController::class, 'create']));
$routes->add('thread', Route::get('/threads/{id}', [ThreadController::class, 'show']));

$routes->add('thread_messages', Route::get('/threads/{id}/messages', [MessageController::class, 'list']));
$routes->add('thread_message_new', Route::post('/threads/{id}/messages', [MessageController::class, 'create']));

$routes->add('users', Route::get('/users', [UserController::class, 'list']));

return $routes->build();