<?php

namespace App\Service;

use PDO;

class FavoriteModel
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

        $user = getenv('POSTGRES_USER') ?: 'default_user';
        $pass = getenv('POSTGRES_PASSWORD') ?: 'default_password';

        $this->pdo = new PDO($dsn, $user, $pass, $options);
    }

    public function addFavorite(string $userId, string $recipeId): void
    {
        $stmt = $this->pdo->prepare("INSERT INTO favorites (user_id, recipe_id) VALUES (:user_id, :recipe_id) ON CONFLICT DO NOTHING");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':recipe_id', $recipeId);
        $stmt->execute();
    }

    public function removeFavorite(string $userId, string $recipeId): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM favorites WHERE user_id = :user_id AND recipe_id = :recipe_id");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':recipe_id', $recipeId);
        $stmt->execute();
    }

    public function getUserFavorites(string $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT r.id, r.title
            FROM favorites f
            JOIN recipes r ON r.id = f.recipe_id
            WHERE f.user_id = :user_id
        ");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
