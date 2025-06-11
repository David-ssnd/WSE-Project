<?php

namespace App\Controller;

/**
 *
 */
use App\View\JsonView;
use App\Service\RecipeModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class RecipeController
{
    /**
     *
     */
    private JsonView $jsonView;
    private RecipeModel $recipeModel;

    public function __construct()
    {
        $this->jsonView = new JsonView();
        $this->recipeModel = new RecipeModel();
    }

    // list with limit and offset

    /**
     * @param int $offset
     * @param int $limit
     * @return void
     */
    public function list(int $offset = 0, int $limit = 20): void
    {
        try {
            $recipes = $this->recipeModel->getPaginatedRecipes($limit, $offset);
            $this->jsonView->render($recipes, 200);
        } catch (\Exception $e) {
            $this->jsonView->render(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @param string $id
     * @return void
     */
    public function detail(string $id): void
    {
        try {
            $recipe = $this->recipeModel->getRecipeById($id);
            if ($recipe) {
                $this->jsonView->render($recipe, 200);
            } else {
                $this->jsonView->render(['error' => 'Recipe not found'], 404);
            }
        } catch (\Exception $e) {
            $this->jsonView->render(['error' => $e->getMessage()], 500);
        }
    }

    public function create(): void
{
    header('Content-Type: application/json');

    // Step 1: Get JSON input
    $data = json_decode(file_get_contents('php://input'), true);

    // Step 2: Get token from cookie
    if (!isset($_COOKIE['token'])) {
        $this->jsonView->render(['error' => 'Authentication token is missing'], 401);
        return;
    }

    $jwt = $_COOKIE['token'];
    $secret = getenv('JWT_SECRET');
    if (!$secret) {
        error_log("❌ JWT_SECRET is not set in the environment.");
        $this->jsonView->render(['error' => 'Server misconfiguration: JWT secret missing'], 500);
        return;
    }

    try {
        // Step 3: Decode the token
        $decoded = JWT::decode($jwt, new Key($secret, 'HS256'));
        $userId = $decoded->sub ?? null;

        if (!$userId) {
            $this->jsonView->render(['error' => 'Invalid token: missing subject'], 401);
            return;
        }

        // Step 4: Validate input data
        if (!isset($data['title'])) {
            $this->jsonView->render(['error' => 'Title is required'], 400);
            return;
        }

        // Step 5: Inject user ID into recipe data
        try {
            $data['user_id'] = $userId;
        } catch (\Throwable $e) {
            $this->jsonView->render(['error' => 'Invalid user ID format'], 400);
            return;
        }

        // Step 6: Call the model to insert recipe
        $this->recipeModel->createRecipeFromData($data);

        // Step 7: Respond with success
        $this->jsonView->render(['message' => 'Recipe created successfully'], 201);

    } catch (\Firebase\JWT\ExpiredException $e) {
        $this->jsonView->render(['error' => 'Token expired'], 401);
    } catch (\Exception $e) {
        error_log("JWT error: " . $e->getMessage());
        $this->jsonView->render(['error' => 'Authentication failed'], 401);
    }
}


public function update(string $id): void
{
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data || !isset($data['title'])) {
        $this->jsonView->render(['error' => 'Title is required'], 442);
        return;
    }

    try {
        $existingRecipe = $this->recipeModel->getRecipeById($id);
        if (!$existingRecipe) {
            $this->jsonView->render(['error' => 'Recipe not found'], 404);
            return;
        }

        // Update mutable fields
        $existingRecipe->setTitle($data['title']);
        $existingRecipe->setDescription($data['description'] ?? null);
        $existingRecipe->setCookTime($data['cook_time'] ?? 0);
        $existingRecipe->setInstructions($data['instructions'] ?? null);

        $this->recipeModel->updateRecipe($existingRecipe);

        $this->jsonView->render(['message' => 'Recipe updated successfully'], 200);
    } catch (\Exception $e) {
        $this->jsonView->render(['error' => $e->getMessage()], 500);
    }
}

public function delete(string $id): void
{
    try {
        $this->recipeModel->deleteRecipe($id);
        $this->jsonView->render(['message' => 'Recipe deleted successfully'], 200);
    } catch (\Exception $e) {
        $this->jsonView->render(['error' => $e->getMessage()], 500);
    }
}
}


