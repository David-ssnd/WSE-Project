<?php

namespace App\Controller;

/**
 *
 */
class CommentController
{
    /**
     *
     */
    public function __construct()
    {
        // TODO: Implement constructor
        $this->jsonView = new \App\View\JsonView();
        $this->commentModel = new \App\Service\CommentModel();
    }

    /**
     * Add a comment to a recipe
     *
     * @param string $recipeId
     * @return void
     */
    public function add(string $recipeId): void
    {
        //extract json data from the request body
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['comment'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Comment is required']);
            return;
        }

        //send to model
        try {
            $commentModel = new \App\Service\CommentModel();
            $commentModel->addComment($recipeId, $data['comment']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add comment']);
            return;
        }

        $this->jsonView->render($user, 201);
    }

    /**
     * Get comments for a recipe
     *
     * @param string $recipeId
     * @return void
     */
    public function get(string $recipeId): void
    {
        // TODO: Implement get method
        try {
            $comments = $this->commentModel->getComments($recipeId);
            $this->jsonView->render($comments, 200);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch comments']);
            return;
        }

        $this->jsonView->render($comments, 200);
    }

    /**
     * Delete a comment
     *
     * @param string $commentId
     * @return void
     */
    public function delete(string $commentId): void
    {
        try {
            $this->commentModel->deleteComment($commentId);
            http_response_code(204); // No Content
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete comment']);
            return;
        }
        $this->jsonView->render(['message' => 'Comment deleted successfully'], 200);
    }
}