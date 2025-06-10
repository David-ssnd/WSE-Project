<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Router\Router;
use App\Controller\HomeController;
use App\Controller\RecipeController;
use App\Controller\AccountController;

$router = new Router();

//routes
$router->get('/', HomeController::class, 'index');
$router->get('/recipe-page/', RecipeController::class, 'index');
$router->get('/account/', AccountController::class, 'index');
$router->get('/create_recipe/', RecipeController::class, 'index');


$router->dispatch();