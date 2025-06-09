<?php

namespace App\Controller;

use App\Service\AuthService;
use App\View\JsonView;
use App\Service\RatingModel;

/**
 *
 */
class RatingController
{
    /**
     *
     */
    private JsonView $jsonView; 
    private RatingModel $ratingModel;
    private AuthService $authService;
    
    public function __construct()
    {
        // TODO: Implement constructor
        $this->jsonView = new \App\View\JsonView();
        $this->ratingModel = new \App\Service\RatingModel();
        $this->authService = new \App\Service\AuthService();
    }

    /**
     * Rate a recipe
     *
     * @param string $id Recipe ID
     * @return void
     */
    public function rate(string $recipe_id): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['rating']) || !is_numeric($data['rating']) || $data['rating'] < 1 || $data['rating'] > 5) {
            $this->jsonView->render(['error' => 'Rating must be a number between 1 and 5'], 400);
            return;
        }

        $user_id = $this->authService->getUserFromToken()->getId();

        try {
            $this->ratingModel->addRating($recipe_id, $user_id, (int) $data['rating']);
        } catch (\Exception $e) {
            $this->jsonView->render(['error' => $e->getMessage()], 500);
            return;
        }

        $this->jsonView->render(['message' => 'Rating saved successfully'], 201);
    }

    /**
     * Get ratings for a recipe
     *
     * @param string $id Recipe ID
     * @return void
     */
    public function getRatings(string $id): void
    {
        try {
            $ratings = $this->ratingModel->getRatings($id);
            $this->jsonView->render($ratings, 200);
        } catch (\Exception $e) {
            $this->jsonView->render(['error' => 'Failed to get ratings'], 500);
            return;
        }
    }

    /**
     * Get average rating for a recipe
     *
     * @param string $id Recipe ID
     * @return void
     */
    public function getAverageRating(string $id): void
    {
        try {
            $averageRating = $this->ratingModel->getAverageRating($id);
            $this->jsonView->render(['average_rating' => $averageRating], 200);
        } catch (\Exception $e) {
            $this->jsonView->render(['error' => 'Failed to get average rating'], 500);
            return;
        }
    }

    /**
     * Delete a rating
     *
     * @param string $id Rating ID
     * @return void
     */

    public function delete(string $id): void
    {
        try {
            $this->ratingModel->deleteRating($id);
        } catch (\Exception $e) {
            $this->jsonView->render(['error' => 'Failed to delete rating'], 500);
            return;
        }

        $this->jsonView->render(204);
    }

    /**
     * Update a rating
     *
     * @param string $id Rating ID
     * @return void
     */

    public function update(string $id): void
    {
        //extract json data from the request body
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['rating'])) {
            $this->jsonView->render(['error' => 'Rating is required'], 400);
            return;
        }

        //send to model
        try {
            $this->ratingModel->updateRating($id, $data['rating']);
        } catch (\Exception $e) {
            $this->jsonView->render(['error' => 'Failed to update rating'], 500);
            return;
        }

        $this->jsonView->render(['message' => 'Rating updated successfully'], 200);
    }

    /**
     * Get the count of ratings for a recipe
     *
     * @param string $id Recipe ID
     * @return void
     */
    public function getRatingCount(string $id): void
    {
        try {
            $count = $this->ratingModel->getRatingCount($id);
            $this->jsonView->render(['rating_count' => $count], 200);
        } catch (\Exception $e) {
            $this->jsonView->render(['error' => 'Failed to get rating count'], 500);
            return;
        }

        $this->jsonView->render(['rating_count' => $count], 200);
    }

    /**
     * Get the user's rating for a recipe
     *
     * @param string $recipeId Recipe ID
     * @param string $userId User ID
     * @return void
     */
    public function getUserRating(string $recipeId, string $userId): void
    {
        try {
            $rating = $this->ratingModel->getUserRating($recipeId, $userId);
            $this->jsonView->render(['user_rating' => $rating], 200);
        } catch (\Exception $e) {
            $this->jsonView->render(['error' => 'Failed to get user rating'], 500);
            return;
        }

        $this->jsonView->render(['user_rating' => $rating], 200);
    }
}