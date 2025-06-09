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
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate required fields
    if (!isset($data['title']) || !isset($data['user_id'])) {
        $this->jsonView->render(['error' => 'Title and User ID are required'], 400);
        return;
    }

    try {
        // Create a Recipe object
        $this->recipeModel->createRecipeFromData($data);

        // Respond with success
        $this->jsonView->render(['message' => 'Recipe created successfully'], 201);
    } catch (\Exception $e) {
        // Handle errors
        $this->jsonView->render(['error' => $e->getMessage()], 500);
    }
}

    public function update(string $id): void
    {
        // Implementation for updating a recipe
        // This would typically involve parsing the request body for updated recipe data,
        // validating it, and then calling the model to update it in the database.
    }

    public function delete(string $id): void
    {
        // Implementation for deleting a recipe
        // This would typically involve calling the model to delete the recipe from the database.
    }
}


