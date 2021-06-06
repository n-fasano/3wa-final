<?php

use App\Controller\HomeController;
use App\Controller\ThreadController;
use App\Service\Route;
use Symfony\Component\Routing\RouteCollection;

$routes = new RouteCollection();

$routes->add('welcome', Route::get('/api', [HomeController::class, 'welcome']));

$routes->add('show_thread', Route::get('/api/thread/{id}', [ThreadController::class, 'show']));

// $routes->add('view_user', Route::get('/users/{username}', [
//     'username' => null,
//     '_controller' => [UserController::class, 'view']
// ]));

// $routes->add('login', Route::post('/login', [
//     '_controller' => [AuthController::class, 'login']
// ]));

// $routes->add('logout', Route::post('/logout', [
//     '_controller' => [AuthController::class, 'logout']
// ]));

// $routes->add('register_form', Route::get('/register', [
//     '_controller' => [AuthController::class, 'register_form']
// ]));

// $routes->add('register', Route::post('/register', [
//     'username' => '',
//     'password' => '',
//     '_controller' => [AuthController::class, 'register']
// ]));

return $routes;