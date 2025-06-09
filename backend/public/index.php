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


//authentication routes
$router->post('/api/auth/register', AuthController::class, 'register');                 //✅
$router->post('/api/auth/login', AuthController::class, 'login');                       //✅
// Logout is useless in stateless JWT authentication
$router->get('/api/profile', ProfileController::class, 'getProfile');                   //✅
$router->patch('/api/profile', ProfileController::class, 'updateProfile');              //✅

// user public profile
$router->get('/api/user/{username}', UserController::class, 'getPublicProfile');        //✅
$router->post('/api/user/{username}/follow', UserController::class, 'follow');          //✅
$router->delete('/api/user/{username}/unfollow', UserController::class, 'unfollow');    //✅
$router->get('/api/user/{username}/followers', UserController::class, 'getFollowers');  //✅
$router->get('/api/user/{username}/following', UserController::class, 'getFollowing');  //✅

$router->get('/api/recipes', RecipeController::class, 'list');                          //✅
$router->get('/api/recipes/{id:uuid}', RecipeController::class, 'detail');              //✅
$router->post('/api/recipes', RecipeController::class, 'create');                       //✅
$router->patch('/api/recipes/{id:uuid}', RecipeController::class, 'update');            //✅
$router->delete('/api/recipes/{id:uuid}', RecipeController::class, 'delete');           //✅

// ingredients routes
$router->get('/api/ingredients', IngredientController::class, 'list');
$router->get('/api/ingredients/{id:uuid}', IngredientController::class, 'detail');
$router->post('/api/ingredients', IngredientController::class, 'create');               
$router->patch('/api/ingredients/{id:uuid}', IngredientController::class, 'update');
$router->delete('/api/ingredients/{id:uuid}', IngredientController::class, 'delete');

// ratings and comments routes
$router->post('/api/recipes/{id:uuid}/rate', RatingController::class, 'rate');          //✅
$router->get('/api/recipes/{id:uuid}/ratings', RatingController::class, 'getRatings');  //✅
$router->post('/api/recipes/{id:uuid}/comments', CommentController::class, 'add');      //✅
$router->get('/api/recipes/{id:uuid}/comments', CommentController::class, 'get');       //✅
$router->delete('/api/comments/{id:uuid}', CommentController::class, 'delete');         //✅ neimplementovane na FE

// favorites routes
$router->post('/api/recipes/{id:uuid}/favorite', FavoriteController::class, 'add');     //✅
$router->delete('/api/recipes/{id:uuid}/favorite', FavoriteController::class, 'remove');//✅
$router->get('/api/profile/favorites', FavoriteController::class, 'list');              //✅

// search routes
$router->get('/api/search/recipes', SearchController::class, 'searchRecipes');          //✅

$router->dispatch();