<?php

namespace App\Controller;

/**
 *
 */
class SearchController
{
    /**
     *
     */
    public function __construct()
    {
        // TODO: Implement constructor
        $this->jsonView = new \App\View\JsonView();
        $this->searchModel = new \App\Service\SearchModel();

    }

    /**
     * Search for recipes, users, or ingredients based on the query parameter.
     *
     * @param string $query The search query.
     * @return void
     */
    public function search(string $query): void
    {
        // TODO: Implement search logic for recipes, users, and ingredients
        // I am not sure about params for this function
        $recipes = $this->searchModel->searchRecipes($query);
        $users = $this->searchModel->searchUsers($query);
        $ingredients = $this->searchModel->searchIngredients($query);

        if ($recipes || $users || $ingredients) {
            $this->jsonView->render(['recipes' => $recipes, 'users' => $users, 'ingredients' => $ingredients], 200);
        } else {
            $this->jsonView->sendResponse(['error' => 'No results found'], 404);
        }
    }

    /**
     * Search for recipes based on the query parameter.
     *
     * @param string $query The search query.
     * @return void
     */
    public function searchRecipes(string $query): void
    {
        // TODO: Implement search logic for recipes
        // I am not sure about params for this function
    }
}