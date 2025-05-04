<?php

namespace App\Controller;

/**
 *
 */
use App\View\JsonView;
use App\Service\FavoriteModel;

class FavoriteController
{
    /**
     *
     */
    private JsonView $jsonView;
    private FavoriteModel $favoriteModel;


    public function __construct()
    {
        // TODO: Implement constructor
        $this->jsonView = new JsonView();
        $this->favoriteModel = new FavoriteModel();
    
    }

    /**
     * Add a recipe to the user's favorites.
     *
     * @param string $id The ID of the recipe to add to favorites.
     * @return void
     */
    public function add(string $id): void
    {
        //extract json data from the request body
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['user_id'])) {
            $this->jsonView->render(['error' => 'User ID is required'], 400);
            return;
        }

        //send to model
        try {
            $this->favoriteModel->addFavorite($id, $data['user_id']);
        } catch (\Exception $e) {
            $this->jsonView->render(['error' => 'Failed to add favorite'], 500);
            return;
        }

        $this->jsonView->render(['message' => 'Recipe added to favorites'], 201);
    }

    /**
     * Remove a recipe from the user's favorites.
     *
     * @param string $id The ID of the recipe to remove from favorites.
     * @return void
     */
    public function remove(string $id): void
    {
        //extract json data from the request body
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['user_id'])) {
            $this->jsonView->render(['error' => 'User ID is required'], 400);
            return;
        }

        //send to model
        try {
            $this->favoriteModel->removeFavorite($id, $data['user_id']);
        } catch (\Exception $e) {
            $this->jsonView->render(['error' => 'Failed to remove favorite'], 500);
            return;
        }

        $this->jsonView->render(['message' => 'Recipe removed from favorites'], 200);
    }

    /**
     * List all favorite recipes of the user.
     *
     * @return void
     */
    public function list(): void
    {
        //extract json data from the request body
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['user_id'])) {
            $this->jsonView->render(['error' => 'User ID is required'], 400);
            return;
        }

        //send to model
        try {
            $favorites = $this->favoriteModel->listFavorites($data['user_id']);
            $this->jsonView->render($favorites, 200);
        } catch (\Exception $e) {
            $this->jsonView->render(['error' => 'Failed to fetch favorites'], 500);
            return;
        }
    }

    /**
     * Check if a recipe is in the user's favorites.
     *
     * @param string $id The ID of the recipe to check.
     * @return void
     */
    public function isFavorite(string $id): void
    {
        //extract json data from the request body
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['user_id'])) {
            $this->jsonView->render(['error' => 'User ID is required'], 400);
            return;
        }

        //send to model
        try {
            $isFavorite = $this->favoriteModel->isFavorite($id, $data['user_id']);
            $this->jsonView->render(['is_favorite' => $isFavorite], 200);
        } catch (\Exception $e) {
            $this->jsonView->render(['error' => 'Failed to check favorite'], 500);
            return;
        }
    }
    /**
     * Get the count of favorite recipes for a user.
     *
     * @return void
     */
    public function count(): void
    {
        //extract json data from the request body
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['user_id'])) {
            $this->jsonView->render(['error' => 'User ID is required'], 400);
            return;
        }

        //send to model
        try {
            $count = $this->favoriteModel->getFavoriteCount($data['user_id']);
            $this->jsonView->render(['favorite_count' => $count], 200);
        } catch (\Exception $e) {
            $this->jsonView->render(['error' => 'Failed to fetch favorite count'], 500);
            return;
        }
    }
    /**
     * Get all favorite recipes for a user.
     *
     * @return void
     */
    public function getFavoritesByUser(): void
    {
        //extract json data from the request body
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['user_id'])) {
            $this->jsonView->render(['error' => 'User ID is required'], 400);
            return;
        }

        //send to model
        try {
            $favorites = $this->favoriteModel->getFavoriteByUser($data['user_id']);
            $this->jsonView->render($favorites, 200);
        } catch (\Exception $e) {
            $this->jsonView->render(['error' => 'Failed to fetch favorites'], 500);
            return;
        }
    }
}