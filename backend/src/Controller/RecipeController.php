<?php

namespace App\Controller;

/**
 *
 */
use App\View\JsonView;
use App\Service\RecipeModel;

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
    header('Content-Type: application/json'); // ← ochrana pred mimo výstupom

    $data = json_decode(file_get_contents('php://input'), true);
    error_log("Raw POST: " . print_r($data, true));

    if (!isset($data['title']) || !isset($data['user_id'])) {
        $this->jsonView->render(['error' => 'Title and User ID are required'], 400);
        return;
    }

    try {
        $this->recipeModel->createRecipeFromData($data);
        $this->jsonView->render(['message' => 'Recipe created successfully'], 201);
    } catch (\Exception $e) {
        error_log("Exception: " . $e->getMessage());
        $this->jsonView->render(['error' => $e->getMessage()], 500);
    }
}

public function update(string $id): void
{
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data || !isset($data['title'])) {
        $this->jsonView->render(['error' => 'Title is required'], 400);
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


