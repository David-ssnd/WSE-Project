<?php


namespace App\Service;

use App\Database\Connection;
use App\Model\Comment;
use App\Model\Recipe;

class FavoriteModel
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::getConnection();
    }

    public function addFavorite(string $recipeId, string $userId): void
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO favorites (recipe_id, user_id) VALUES (:recipe_id, :user_id)");
            $stmt->bindParam(':recipe_id', $recipeId);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new \Exception("Failed to add favorite: " . $e->getMessage());
        }
    }

    
    public function removeFavorite(string $recipeId, string $userId): void
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM favorites WHERE recipe_id = :recipe_id AND user_id = :user_id");
            $stmt->bindParam(':recipe_id', $recipeId);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new \Exception("Failed to remove favorite: " . $e->getMessage());
        }
    }

    public function listFavorites(string $userId): array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM favorites WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_CLASS, Recipe::class);
        } catch (\PDOException $e) {
            throw new \Exception("Failed to fetch favorites: " . $e->getMessage());
        }
    }

    public function isFavorite(string $recipeId, string $userId): bool
    {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM favorites WHERE recipe_id = :recipe_id AND user_id = :user_id");
            $stmt->bindParam(':recipe_id', $recipeId);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (\PDOException $e) {
            throw new \Exception("Failed to check favorite: " . $e->getMessage());
        }
    }

    public function getFavoriteCount(string $userId): int
    {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM favorites WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            return (int)$stmt->fetchColumn();
        } catch (\PDOException $e) {
            throw new \Exception("Failed to fetch favorite count: " . $e->getMessage());
        }
    }

    public function getFavoriteByUser(string $userId): array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM favorites WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_CLASS, Recipe::class);
        } catch (\PDOException $e) {
            throw new \Exception("Failed to fetch favorites by user: " . $e->getMessage());
        }
    }
}
