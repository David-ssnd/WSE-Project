<?php


namespace App\Service;


class RatingModel
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::getConnection();
    }

    public function addRating(string $recipeId, int $rating): void
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO ratings (recipe_id, rating) VALUES (:recipe_id, :rating)");
            $stmt->bindParam(':recipe_id', $recipeId);
            $stmt->bindParam(':rating', $rating);
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new \Exception("Failed to add rating: " . $e->getMessage());
        }
    }

    public function getRatings(string $recipeId): array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM ratings WHERE recipe_id = :recipe_id");
            $stmt->bindParam(':recipe_id', $recipeId);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("Failed to fetch ratings: " . $e->getMessage());
        }
    }

    public function deleteRating(int $ratingId): void
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM ratings WHERE id = :rating_id");
            $stmt->bindParam(':rating_id', $ratingId, \PDO::PARAM_INT);
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