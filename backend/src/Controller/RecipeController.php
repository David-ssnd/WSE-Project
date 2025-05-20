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
}