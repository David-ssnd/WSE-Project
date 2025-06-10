<?php

// Enable CORS headers
header("Access-Control-Allow-Origin: *"); // Allow all origins (use specific origins in production, e.g., "http://your-frontend.com")
header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS"); // Allowed methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allowed headers
header("Access-Control-Max-Age: 86400"); // Cache preflight response for 1 day

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Max-Age: 86400");
    http_response_code(200);
    exit();
}

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

// Authentication routes
$router->post('/api/auth/register', AuthController::class, 'register');
$router->post('/api/auth/login', AuthController::class, 'login');
$router->get('/api/profile', ProfileController::class, 'getProfile');
$router->patch('/api/profile', ProfileController::class, 'updateProfile');

// User public profile
$router->get('/api/user/{username}', UserController::class, 'getPublicProfile');
$router->post('/api/user/{username}/follow', UserController::class, 'follow');
$router->delete('/api/user/{username}/unfollow', UserController::class, 'unfollow');
$router->get('/api/user/{username}/followers', UserController::class, 'getFollowers');
$router->get('/api/user/{username}/following', UserController::class, 'getFollowing');

$router->get('/api/recipes', RecipeController::class, 'list');                          //✅
$router->get('/api/recipes/{id:uuid}', RecipeController::class, 'detail');              //✅
$router->post('/api/recipes', RecipeController::class, 'create');                       //✅
$router->patch('/api/recipes/{id:uuid}', RecipeController::class, 'update');            //✅
$router->delete('/api/recipes/{id:uuid}', RecipeController::class, 'delete');           //✅

// Ingredients routes
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