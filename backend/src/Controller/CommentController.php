<?php

namespace App\Controller;

/**
 *
 */

use App\View\JsonView;
use App\Service\CommentModel;

class CommentController
{
    /**
     *
     */
    private JsonView $jsonView;
    private CommentModel $commentModel;

    public function __construct()
    {
        // TODO: Implement constructor
        $this->jsonView = new JsonView();
        $this->commentModel = new CommentModel();
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
            $this->jsonView->render(['error' => 'Comment is required'], 400);
            return;
        }

        //send to model
        try {
            $commentModel = new \App\Service\CommentModel();
            $commentModel->addComment($recipeId, $data['comment']);
        } catch (\Exception $e) {
            $this->jsonView->render(['error' => 'Failed to add comment'], 500);
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
        } catch (\Exception $e) {
            $this->jsonView->render(['error' => 'Failed to retrieve comments'], 500);
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
        } catch (\Exception $e) {
            $this->jsonView->render(['error' => 'Failed to delete comment'], 500);
            return;
        }
        $this->jsonView->render(['message' => 'Comment deleted successfully'], 200);
    }
}