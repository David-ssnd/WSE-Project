<?php

namespace App\Service;

use PDO;
use PDOException;
use App\Entity\Comment;

class CommentModel
{
    private PDO $pdo;

    public function __construct()
    {
        $dsn = sprintf(
            'pgsql:host=%s;port=%s;dbname=%s',
            getenv('POSTGRES_HOST') ? getenv('POSTGRES_HOST') : 'localhost',
            getenv('POSTGRES_PORT') ? getenv('POSTGRES_PORT') : 5432,
            getenv('POSTGRES_DB') ? getenv('POSTGRES_DB') : 'your_database'
        );
        
        // setup DB connection options
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        
        // get username and password from the environment
        $dbUser = getenv('POSTGRES_USER') ?? 'default_user';
        $dbPass = getenv('POSTGRES_PASSWORD') ?? 'default_password';
        
        // create a new PDO - PHP Data Objects instance
        $this->pdo = new PDO($dsn, $dbUser, $dbPass, $options);
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