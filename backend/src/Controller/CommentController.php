<?php

namespace App\Controller;

use App\Service\AuthService;
use App\View\JsonView;
use App\Service\CommentModel;

class CommentController
{
    private JsonView $jsonView;
    private CommentModel $commentModel;
    private AuthService $authService;

    public function __construct()
    {
        $this->jsonView = new JsonView();
        $this->commentModel = new CommentModel();
        $this->authService = new AuthService();
    }

    public function add(string $id): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['comment']) || trim($data['comment']) === '') {
            $this->jsonView->render(['error' => 'Comment text is required'], 400);
            return;
        }

        $userId = $this->authService->getUserFromToken()->getId();

        try {
            $this->commentModel->addComment($id, $userId, $data['comment']);
            $this->jsonView->render(['message' => 'Comment added successfully'], 201);
        } catch (\Exception $e) {
            $this->jsonView->render(['error' => $e->getMessage()], 500);
        }
    }

    public function get(string $id): void
    {
        try {
            $comments = $this->commentModel->getComments($id);
            $this->jsonView->render($comments, 200);
        } catch (\Exception $e) {
            $this->jsonView->render(['error' => 'Failed to get comments'], 500);
        }
    }

    public function delete(string $id): void
    {
        $userId = $this->authService->getUserFromToken()->getId();

        try {
            $this->commentModel->deleteComment($id, $userId);
            $this->jsonView->render(['message' => 'Comment deleted'], 204);
        } catch (\Exception $e) {
            $this->jsonView->render(['error' => $e->getMessage()], 500);
        }
    }
}
