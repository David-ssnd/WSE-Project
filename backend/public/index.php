<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Router\Router;
use App\Controller\AuthController;
use App\Controller\ProfileController;
use App\Controller\UserController;
use App\Controller\RecipeController;
use App\Controller\IngredientController;
use App\Controller\RatingController;
use App\Controller\CommentController;
use App\Controller\FavoriteController;
use App\Controller\SearchController;

$router = new Router();

$router->post('/api/auth/register', AuthController::class, 'register');
$router->post('/api/auth/login', AuthController::class, 'login');
$router->post('/api/auth/logout', AuthController::class, 'logout');
$router->get('/api/profile', ProfileController::class, 'getProfile');
$router->patch('/api/profile', ProfileController::class, 'updateProfile');

$router->get('/api/user/{id:uuid}', UserController::class, 'getPublicProfile');
$router->post('/api/user/{id:uuid}/follow', UserController::class, 'follow');
$router->delete('/api/user/{id:uuid}/unfollow', UserController::class, 'unfollow');
$router->get('/api/user/{id:uuid}/followers', UserController::class, 'getFollowers');
$router->get('/api/user/{id:uuid}/following', UserController::class, 'getFollowing');

$router->get('/api/recipes', RecipeController::class, 'list');
$router->get('/api/recipes/{id:uuid}', RecipeController::class, 'detail');
$router->post('/api/recipes', RecipeController::class, 'create');
$router->patch('/api/recipes/{id:uuid}', RecipeController::class, 'update');
$router->delete('/api/recipes/{id:uuid}', RecipeController::class, 'delete');

$router->get('/api/ingredients', IngredientController::class, 'list');
$router->get('/api/ingredients/{id:uuid}', IngredientController::class, 'detail');
$router->post('/api/ingredients', IngredientController::class, 'create');
$router->patch('/api/ingredients/{id:uuid}', IngredientController::class, 'update');
$router->delete('/api/ingredients/{id:uuid}', IngredientController::class, 'delete');

$router->post('/api/recipes/{id:uuid}/rate', RatingController::class, 'rate');
$router->get('/api/recipes/{id:uuid}/ratings', RatingController::class, 'getRatings');
$router->post('/api/recipes/{id:uuid}/comments', CommentController::class, 'add');
$router->get('/api/recipes/{id:uuid}/comments', CommentController::class, 'get');
$router->delete('/api/comments/{id:uuid}', CommentController::class, 'delete');

$router->post('/api/recipes/{id:uuid}/favorite', FavoriteController::class, 'add');
$router->delete('/api/recipes/{id:uuid}/favorite', FavoriteController::class, 'remove');
$router->get('/api/user/favorites', FavoriteController::class, 'list');

$router->get('/api/search', SearchController::class, 'search');
$router->get('/api/recipes/filter', RecipeController::class, 'filter');

$router->dispatch();
