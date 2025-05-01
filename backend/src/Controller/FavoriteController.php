<?php

namespace App\Controller;

/**
 *
 */
class FavoriteController
{
    /**
     *
     */
    public function __construct()
    {
        // TODO: Implement constructor
        $this->jsonView = new \App\View\JsonView();
        $this->favoriteModel = new \App\Service\FavoriteModel();
    
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
            http_response_code(400);
            echo json_encode(['error' => 'User ID is required']);
            return;
        }

        //send to model
        try {
            $this->favoriteModel->addFavorite($id, $data['user_id']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add favorite']);
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
            http_response_code(400);
            echo json_encode(['error' => 'User ID is required']);
            return;
        }

        //send to model
        try {
            $this->favoriteModel->removeFavorite($id, $data['user_id']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to remove favorite']);
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
            http_response_code(400);
            echo json_encode(['error' => 'User ID is required']);
            return;
        }

        //send to model
        try {
            $favorites = $this->favoriteModel->listFavorites($data['user_id']);
            $this->jsonView->render($favorites, 200);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch favorites']);
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
            http_response_code(400);
            echo json_encode(['error' => 'User ID is required']);
            return;
        }

        //send to model
        try {
            $isFavorite = $this->favoriteModel->isFavorite($id, $data['user_id']);
            $this->jsonView->render(['is_favorite' => $isFavorite], 200);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to check favorite status']);
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
            http_response_code(400);
            echo json_encode(['error' => 'User ID is required']);
            return;
        }

        //send to model
        try {
            $count = $this->favoriteModel->getFavoriteCount($data['user_id']);
            $this->jsonView->render(['favorite_count' => $count], 200);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch favorite count']);
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
            http_response_code(400);
            echo json_encode(['error' => 'User ID is required']);
            return;
        }

        //send to model
        try {
            $favorites = $this->favoriteModel->getFavoriteByUser($data['user_id']);
            $this->jsonView->render($favorites, 200);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch favorite recipes']);
            return;
        }
    }
}