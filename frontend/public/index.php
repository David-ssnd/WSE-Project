<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Router\Router;
use App\Controller\HomeController;

$router = new Router();

//routes
$router->get('/', HomeController::class, 'index');


$router->dispatch();