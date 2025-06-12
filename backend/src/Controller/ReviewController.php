<?php

namespace App\Controller;

use App\View\JsonView;
use App\Service\ReviewModel;
use App\Service\AuthService;

class ReviewController
{
    private JsonView $jsonView;
    private ReviewModel $reviewModel;
    private AuthService $authService;

    public function __construct()
    {
        $this->jsonView = new JsonView();
        $this->reviewModel = new ReviewModel();
        $this->authService = new AuthService();
    }

    // Odoslanie hodnotenia + komentára
    public function submitReview(string $recipeId): void
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);

        try {
            $userId = $this->authService->getUserFromToken()->getId();
        } catch (\Exception $e) {
            $this->jsonView->render(['error' => $e->getMessage()], 401);
            return;
        }

        $rating = $data['rating'] ?? null;
        $comment = $data['comment'] ?? null;

        if ($rating !== null && (!is_numeric($rating) || $rating < 1 || $rating > 5)) {
            $this->jsonView->render(['error' => 'Rating must be a number between 1 and 5'], 400);
            return;
        }

        try {
            if ($rating !== null) {
                $this->reviewModel->addRating($recipeId, $userId, (int)$rating);
            }
            if ($comment !== null && trim($comment) !== '') {
                $this->reviewModel->addComment($recipeId, $userId, $comment);
            }
            $this->jsonView->render(['message' => 'Review saved'], 201);
        } catch (\Exception $e) {
            $this->jsonView->render(['error' => $e->getMessage()], 500);
        }
    }

    // Získanie všetkých recenzií (rating + komentár)
    public function getReviews(string $recipeId): void
    {
        try {
            $reviews = $this->reviewModel->getReviewsForRecipe($recipeId);
            $this->jsonView->render($reviews, 200);
        } catch (\Exception $e) {
            $this->jsonView->render(['error' => 'Failed to get reviews'], 500);
        }
    }

    // Odstránenie komentára
    public function deleteComment(string $commentId): void
    {
        $userId = $this->authService->getUserFromToken()->getId();

        try {
            $this->reviewModel->deleteComment($commentId, $userId);
            $this->jsonView->render(['message' => 'Comment deleted'], 204);
        } catch (\Exception $e) {
            $this->jsonView->render(['error' => $e->getMessage()], 500);
        }
    }
}
