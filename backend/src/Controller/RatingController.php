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