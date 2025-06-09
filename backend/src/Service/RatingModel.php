<?php

namespace App\Service;

use PDO;

class RatingModel
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

    public function addRating(string $recipeId, string $userId, int $rating): void
    {
        if ($rating < 1 || $rating > 5) {
            throw new \InvalidArgumentException("Rating must be between 1 and 5.");
        }

        $existing = $this->getUserRating($recipeId, $userId);

        if ($existing !== null) {
            $this->updateRating($recipeId, $userId, $rating);
            return;
        }

        try {
            $stmt = $this->pdo->prepare("INSERT INTO ratings (recipe_id, user_id, rating) VALUES (:recipe_id, :user_id, :rating)");
            $stmt->bindParam(':recipe_id', $recipeId);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':rating', $rating);
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new \Exception("Failed to add rating: " . $e->getMessage());
        }

        $this->recalculateSummary($recipeId);
    }

    public function updateRating(string $recipeId, string $userId, int $newRating): void
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE ratings SET rating = :rating WHERE recipe_id = :recipe_id AND user_id = :user_id");
            $stmt->bindParam(':rating', $newRating, PDO::PARAM_INT);
            $stmt->bindParam(':recipe_id', $recipeId);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new \Exception("Failed to update rating: " . $e->getMessage());
        }

        $this->recalculateSummary($recipeId);
    }

    public function getUserRating(string $recipeId, string $userId): ?int
    {
        try {
            $stmt = $this->pdo->prepare("SELECT rating FROM ratings WHERE recipe_id = :recipe_id AND user_id = :user_id");
            $stmt->bindParam(':recipe_id', $recipeId);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            $result = $stmt->fetchColumn();
            return $result !== false ? (int)$result : null;
        } catch (\PDOException $e) {
            throw new \Exception("Failed to fetch user rating: " . $e->getMessage());
        }
    }

    public function getRatings(string $recipeId): array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT user_id, rating FROM ratings WHERE recipe_id = :recipe_id");
            $stmt->bindParam(':recipe_id', $recipeId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("Failed to fetch ratings: " . $e->getMessage());
        }
    }

    private function recalculateSummary(string $recipeId): void
    {
        try {
            $stmt = $this->pdo->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as rating_count FROM ratings WHERE recipe_id = :recipe_id");
            $stmt->bindParam(':recipe_id', $recipeId);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $avgRating = $result['avg_rating'];
                $ratingCount = $result['rating_count'];

                $updateStmt = $this->pdo->prepare("UPDATE recipes SET average_rating = :average_rating, rating_count = :rating_count WHERE id = :recipe_id");
                $updateStmt->bindParam(':average_rating', $avgRating);
                $updateStmt->bindParam(':rating_count', $ratingCount);
                $updateStmt->bindParam(':recipe_id', $recipeId);
                $updateStmt->execute();
            }
        } catch (\PDOException $e) {
            throw new \Exception("Failed to recalculate recipe rating: " . $e->getMessage());
        }
    }
}
