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
        $stmt = $this->pdo->prepare("
            UPDATE users
            SET favorites = array_append(favorites, :recipe_id)
            WHERE id = :user_id AND NOT (:recipe_id = ANY(favorites))
        ");
        $stmt->execute([
            ':user_id'   => $userId,
            ':recipe_id' => $recipeId
        ]);
    }

    public function removeFavorite(string $userId, string $recipeId): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE users
            SET favorites = array_remove(favorites, :recipe_id)
            WHERE id = :user_id
        ");
        $stmt->execute([
            ':user_id'   => $userId,
            ':recipe_id' => $recipeId
        ]);
    }

    public function getUserFavorites(string $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT r.id, r.title, r.thumbnail_image
            FROM users u
            JOIN recipes r ON r.id = ANY(u.favorites)
            WHERE u.id = :user_id
        ");
        $stmt->execute([':user_id' => $userId]);

        return $stmt->fetchAll();
    }

    public function isFavorite(string $userId, string $recipeId): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT :recipe_id = ANY(favorites) AS is_fav
            FROM users
            WHERE id = :user_id
        ");
        $stmt->execute([
            ':user_id'   => $userId,
            ':recipe_id' => $recipeId
        ]);
        $result = $stmt->fetch();

        return $result['is_fav'] ?? false;
    }
}
