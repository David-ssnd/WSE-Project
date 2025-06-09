<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Recipe;
use PDO;
use Ramsey\Uuid\Uuid;

class RecipeModel
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

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $dbUser = getenv('POSTGRES_USER') ?? 'default_user';
        $dbPass = getenv('POSTGRES_PASSWORD') ?? 'default_password';

        $this->pdo = new PDO($dsn, $dbUser, $dbPass, $options);
    }

    public function getPaginatedRecipes(int $limit, int $offset): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM recipes ORDER BY created_at DESC LIMIT :limit OFFSET :offset');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $recipes = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $recipe = new Recipe(
                $row['id'],
                $row['user_id'],
                $row['title'],
                $row['description'] ?? null,
                new \DateTime($row['created_at']) ?? new \DateTime(),
                $row['cook_time'] ?? 0,
                $row['instructions'] ?? null
            );
            $recipes[] = $recipe;
        }
        return $recipes;
    }

    public function getRecipeById(string $id): ?Recipe
    {
        $stmt = $this->pdo->prepare('SELECT * FROM recipes WHERE id = :id LIMIT 1');
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return new Recipe(
            $row['id'],
            $row['user_id'],
            $row['title'],
            $row['description'] ?? null,
            new \DateTime($row['created_at']) ?? new \DateTime(),
            $row['cook_time'] ?? 0,
            $row['instructions'] ?? null
        );
    }

    public function createRecipe(Recipe $recipe): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO recipes (id, user_id, title, description, created_at, cook_time, instructions) VALUES (:id, :user_id, :title, :description, :created_at, :cook_time, :instructions)');
        $stmt->bindParam(':id', $recipe->getId(), PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $recipe->getUserId(), PDO::PARAM_STR);
        $stmt->bindParam(':title', $recipe->getTitle(), PDO::PARAM_STR);
        $stmt->bindParam(':description', $recipe->getDescription(), PDO::PARAM_STR);
        $stmt->bindValue(':created_at', $recipe->getCreatedAt()->format('Y-m-d H:i:s'));
        $stmt->bindValue(':cook_time', $recipe->getCookTime(), PDO::PARAM_INT);
        $stmt->bindParam(':instructions', $recipe->getInstructions(), PDO::PARAM_STR);

        if (!$stmt->execute()) {
            throw new \Exception('Failed to create recipe');
        }
    }

    public function createRecipeFromData(array $data): void
    {
        $id = $this->generateUniqueRamseyUUID();
        $createdAt = new \DateTime();

        $recipe = new Recipe(
            $id,
            $data['user_id'],
            $data['title'],
            $data['description'] ?? null,
            $createdAt,
            $data['cook_time'] ?? 0,
            $data['instructions'] ?? null
        );

        $this->createRecipe($recipe);
    }

    public function updateRecipe(Recipe $recipe): void
    {
        $stmt = $this->pdo->prepare('UPDATE recipes SET title = :title, description = :description, cook_time = :cook_time, instructions = :instructions WHERE id = :id');
        $stmt->bindParam(':id', $recipe->getId(), PDO::PARAM_STR);
        $stmt->bindParam(':title', $recipe->getTitle(), PDO::PARAM_STR);
        $stmt->bindParam(':description', $recipe->getDescription(), PDO::PARAM_STR);
        $stmt->bindValue(':cook_time', $recipe->getCookTime(), PDO::PARAM_INT);
        $stmt->bindParam(':instructions', $recipe->getInstructions(), PDO::PARAM_STR);

        if (!$stmt->execute()) {
            throw new \Exception('Failed to update recipe');
        }
    }

    public function deleteRecipe(string $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM recipes WHERE id = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);

        if (!$stmt->execute()) {
            throw new \Exception('Failed to delete recipe');
        }
    }

    public function getTotalRecipesCount(): int
    {
        $stmt = $this->pdo->query('SELECT COUNT(*) FROM recipes');
        return (int) $stmt->fetchColumn();
    }

    public function deleteRecipesByUser(string $userId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM recipes WHERE user_id = :user_id');
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_STR);

        if (!$stmt->execute()) {
            throw new \Exception('Failed to delete recipes for user');
        }
    }

    private function generateUniqueRamseyUUID(): string
    {
        do {
            $uuid = Uuid::uuid4()->toString();
            $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM recipes WHERE id = :id');
            $stmt->bindParam(':id', $uuid, PDO::PARAM_STR);
            $stmt->execute();
            $count = (int) $stmt->fetchColumn();
        } while ($count > 0);

        return $uuid;
    }
}
