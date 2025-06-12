<?php

namespace App\Service;

use PDO;

class SearchModel
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

    public function searchRecipes(string $query): array
    {
        
        $stmt = $this->pdo->prepare(
            "SELECT *, GREATEST(similarity(title, :query), similarity(description, :query)) AS sim
                FROM recipes
                WHERE title % :query OR description % :query
                ORDER BY sim DESC
                LIMIT 20"
        );

        $stmt->bindParam(':query', $query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
