<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Recipe;
use PDO;

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

    public function getAllRecipes(): array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM recipes");
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_CLASS, Recipe::class);
        } catch (\PDOException $e) {
            throw new \Exception("Failed to fetch recipes: " . $e->getMessage());
        }
    }

    public function getRecipeById(string $id): ?Recipe
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM recipes WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetchObject(Recipe::class) ?: null;
        } catch (\PDOException $e) {
            throw new \Exception("Failed to fetch recipe: " . $e->getMessage());
        }
    }

    public function getRecipesByUser(User $user, int $offset, int $limit): array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM recipes WHERE user_id = :user_id LIMIT :limit OFFSET :offset ORDER BY created_at DESC");
            $stmt->bindValue(':user_id', $user->getId(), PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_CLASS, Recipe::class);
        } catch (\PDOException $e) {
            throw new \Exception("Failed to fetch user's recipes: " . $e->getMessage());
        }
    }

    public function createRecipe(Recipe $recipe): void
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO recipes (name, description, instructions) VALUES (:name, :description, :instructions)");
            $stmt->bindParam(':name', $recipe->getName());
            $stmt->bindParam(':description', $recipe->getDescription());
            $stmt->bindParam(':instructions', $recipe->getInstructions());
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new \Exception("Failed to create recipe: " . $e->getMessage());
        }
    }

    public function updateRecipe(Recipe $recipe): void
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE recipes SET name = :name, description = :description, instructions = :instructions WHERE id = :id");
            $stmt->bindParam(':name', $recipe->getName());
            $stmt->bindParam(':description', $recipe->getDescription());
            $stmt->bindParam(':instructions', $recipe->getInstructions());
            $stmt->bindParam(':id', $recipe->getId());
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new \Exception("Failed to update recipe: " . $e->getMessage());
        }
    }

    public function deleteRecipe(string $id): void
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM recipes WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new \Exception("Failed to delete recipe: " . $e->getMessage());
        }
    }

}