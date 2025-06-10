<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Router\Router;
use App\Controller\HomeController;
use App\Controller\RecipeController;

$router = new Router();

//routes
$router->get('/', HomeController::class, 'index');
$router->get('/recipe-page/', RecipeController::class, 'index');


$router->dispatch();