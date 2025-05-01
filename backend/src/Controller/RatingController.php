<?php

namespace App\Controller;

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
    public function __construct()
    {
        // TODO: Implement constructor
        $this->jsonView = new \App\View\JsonView();
        $this->ratingModel = new \App\Service\RatingModel();
    }

    /**
     * Rate a recipe
     *
     * @param string $id Recipe ID
     * @return void
     */
    public function rate(string $id): void
    {
        // TODO: Implement rate method
        //extract json data from the request body
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['user_id']) || !isset($data['rating'])) {
            http_response_code(400);
            echo json_encode(['error' => 'User ID and rating are required']);
            return;
        }

        //send to model
        try {
            $this->ratingModel->addRating($id, $data['user_id'], $data['rating']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add rating']);
            return;
        }

        $this->jsonView->render(['message' => 'Recipe rated successfully'], 201);
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
            http_response_code(500);
            echo json_encode(['error' => 'Failed to get ratings']);
            return;
        }

        $this->jsonView->render($ratings, 200);
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
            http_response_code(500);
            echo json_encode(['error' => 'Failed to get average rating']);
            return;
        }

        $this->jsonView->render(['average_rating' => $averageRating], 200);
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
            http_response_code(204); // No Content
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete rating']);
            return;
        }

        $this->jsonView->render(['message' => 'Rating deleted successfully'], 200);
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
            http_response_code(400);
            echo json_encode(['error' => 'Rating is required']);
            return;
        }

        //send to model
        try {
            $this->ratingModel->updateRating($id, $data['rating']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update rating']);
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
            http_response_code(500);
            echo json_encode(['error' => 'Failed to get rating count']);
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
            http_response_code(500);
            echo json_encode(['error' => 'Failed to get user rating']);
            return;
        }

        $this->jsonView->render(['user_rating' => $rating], 200);
    }
}