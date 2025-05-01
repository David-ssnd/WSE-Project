<?php


namespace App\Service;

use App\Database\Connection;
use App\Model\Recipe;
use App\Model\Ingredient;
use App\Model\Comment;
use App\Model\User;

class RecipeModel
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Connection::getConnection();
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