<?php

namespace App\Service;

use PDO;

class CommentModel
{
    private PDO $pdo;

    public function __construct()
    {
        $dsn = sprintf(
            'pgsql:host=%s;port=%s;dbname=%s',
            getenv('POSTGRES_HOST') ?: 'localhost',
            getenv('POSTGRES_PORT') ?: 5432,
            getenv('POSTGRES_DB') ?: 'your_database'
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $dbUser = getenv('POSTGRES_USER') ?? 'default_user';
        $dbPass = getenv('POSTGRES_PASSWORD') ?? 'default_password';

        $this->pdo = new PDO($dsn, $dbUser, $dbPass, $options);
    }

    public function addComment(string $recipeId, string $userId, string $comment): void
    {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO comments (recipe_id, user_id, comment, created_at) VALUES (:recipe_id, :user_id, :comment, NOW())"
            );
            $stmt->bindParam(':recipe_id', $recipeId);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':comment', $comment);
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new \Exception("Failed to add comment: " . $e->getMessage());
        }
    }

    public function getComments(string $recipeId): array
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT id, user_id, comment, created_at FROM comments WHERE recipe_id = :recipe_id ORDER BY created_at DESC"
            );
            $stmt->bindParam(':recipe_id', $recipeId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("Failed to fetch comments: " . $e->getMessage());
        }
    }

    public function deleteComment(string $commentId, string $userId): void
    {
        try {
            $stmt = $this->pdo->prepare(
                "DELETE FROM comments WHERE id = :id AND user_id = :user_id"
            );
            $stmt->bindParam(':id', $commentId);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                throw new \Exception("Comment not found or permission denied");
            }
        } catch (\PDOException $e) {
            throw new \Exception("Failed to delete comment: " . $e->getMessage());
        }
    }
}
