<?php

namespace App\Service;

use App\Database\Connection;
use App\Model\Comment;
use App\Model\Recipe;
use App\Model\User;
use PDO;
use PDOException;

class CommentModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::getConnection();
    }

    public function addComment(string $recipeId, string $comment): void
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO comments (recipe_id, comment) VALUES (:recipe_id, :comment)");
            $stmt->bindParam(':recipe_id', $recipeId, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new \Exception("Failed to add comment: " . $e->getMessage());
        }
    }

    public function getComments(string $recipeId): array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM comments WHERE recipe_id = :recipe_id");
            $stmt->bindParam(':recipe_id', $recipeId, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_CLASS, Comment::class);
        } catch (PDOException $e) {
            throw new \Exception("Failed to fetch comments: " . $e->getMessage());
        }
    }
    public function deleteComment(int $commentId): void
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM comments WHERE id = :comment_id");
            $stmt->bindParam(':comment_id', $commentId, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new \Exception("Failed to delete comment: " . $e->getMessage());
        }
    }
}