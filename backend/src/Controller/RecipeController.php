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
        // TODO: Implement constructor
        $this->jsonView = new JsonView();
        $this->recipeModel = new RecipeModel();
    }

    /**
     * @return void
     * List all recipes
     */
    public function list()
    {
        $recipes = $this->recipeModel->getAllRecipes();
        if ($recipes) {
            $this->jsonView->render($recipes, 200);
        } else {
            $this->jsonView->render(['error' => 'No recipes found'], 404);
        }
    }

    /**
     * @param string $id - UUID
     * @return void
     * Get recipe details
     */
    public function detail($id)
    {
        $recipe = $this->recipeModel->getRecipeById($id);
        if ($recipe) {
            $this->jsonView->render($recipe, 200);
        } else {
            $this->jsonView->render(['error' => 'Recipe not found'], 404);
        }
    }

    /**
     * @return void
     * Create a new recipe
     */
    public function create()
    {
        //extract json data from the request body
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['name']) || !isset($data['ingredients']) || !isset($data['instructions'])) {
            $this->jsonView->render(['error' => 'Name, ingredients, and instructions are required'], 400);
            return;
        }

        //send to model
        try {
            $this->recipeModel->createRecipe($data);
            $this->jsonView->render(['message' => 'Recipe created successfully'], 201);
        } catch (\Exception $e) {
            $this->jsonView->render(['error' => 'Failed to create recipe'], 500);
            return;
        }
    }

    /**
     * @param string $id - UUID
     * @return void
     * Update a recipe
     */
    public function update($id)
    {
        //extract json data from the request body
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['name']) || !isset($data['ingredients']) || !isset($data['instructions'])) {
            $this->jsonView->render(['error' => 'Name, ingredients, and instructions are required'], 400);
            return;
        }

        //send to model
        try {
            $this->recipeModel->updateRecipe($id, $data);
            $this->jsonView->render(['message' => 'Recipe updated successfully'], 200);
        } catch (\Exception $e) {
            $this->jsonView->render(['error' => 'Failed to update recipe'], 500);
            return;
        }
    }

    /**
     * @param string $id - UUID
     * @return void
     * Delete a recipe
     */
    public function delete($id)
    {
        //send to model
        try {
            $this->recipeModel->deleteRecipe($id);
            $this->jsonView->render(['message' => 'Recipe deleted successfully'], 200);
        } catch (\Exception $e) {
            $this->jsonView->render(['error' => 'Failed to delete recipe'], 500);
            return;
        }
    }
}