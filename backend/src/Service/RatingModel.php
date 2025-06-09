<?php

namespace App\Service;

use PDO;
use App\Service\AuthService;

class RatingModel
{
    private PDO $pdo;
    private AuthService $authService;

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
        $this->authService = new AuthService();
    }

    public function addRating(string $recipeId, string $user_id, int $rating): void
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO ratings (recipe_id, user_id, rating) VALUES (:recipe_id, :user_id, :rating)");
            $stmt->bindParam(':recipe_id', $recipeId);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':rating', $rating);
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new \Exception("Failed to add rating: " . $e->getMessage());
        }
    }

    public function getRatings(string $recipeId): array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT user_id, rating FROM ratings WHERE recipe_id = :recipe_id");
            $stmt->bindParam(':recipe_id', $recipeId);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("Failed to fetch ratings: " . $e->getMessage());
        }
    }

    public function deleteRating(string $recipeId): void
    {
        try {
            $userId = $this->authService->getUserFromToken()->getId();
            $stmt = $this->pdo->prepare("DELETE FROM ratings WHERE recipe_id = :recipe_id AND user_id = :user_id");
            $stmt->bindParam(':recipe_id', $recipeId);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new \Exception("Failed to delete rating: " . $e->getMessage());
        }
    }

    public function updateRating(int $ratingId, int $newRating): void
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE ratings SET rating = :rating WHERE id = :rating_id");
            $stmt->bindParam(':rating', $newRating, \PDO::PARAM_INT);
            $stmt->bindParam(':rating_id', $ratingId, \PDO::PARAM_INT);
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new \Exception("Failed to update rating: " . $e->getMessage());
        }
    }

    public function getAverageRating(string $recipeId): float
    {
        try {
            $stmt = $this->pdo->prepare("SELECT AVG(rating) as average_rating FROM ratings WHERE recipe_id = :recipe_id");
            $stmt->bindParam(':recipe_id', $recipeId);
            $stmt->execute();
            return (float)$stmt->fetchColumn();
        } catch (\PDOException $e) {
            throw new \Exception("Failed to fetch average rating: " . $e->getMessage());
        }
    }

    public function getRatingCount(string $recipeId): int
    {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM ratings WHERE recipe_id = :recipe_id");
            $stmt->bindParam(':recipe_id', $recipeId);
            $stmt->execute();
            return (int)$stmt->fetchColumn();
        } catch (\PDOException $e) {
            throw new \Exception("Failed to fetch rating count: " . $e->getMessage());
        }
    }

    public function getUserRating(string $recipeId, string $userId): ?int
    {
        try {
            $stmt = $this->pdo->prepare("SELECT rating FROM ratings WHERE recipe_id = :recipe_id AND user_id = :user_id");
            $stmt->bindParam(':recipe_id', $recipeId);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            return (int)$stmt->fetchColumn();
        } catch (\PDOException $e) {
            throw new \Exception("Failed to fetch user rating: " . $e->getMessage());
        }
    }
}