<?php

namespace App\Service;

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
            getenv('POSTGRES_HOST') ?: 'localhost',
            getenv('POSTGRES_PORT') ?: 5432,
            getenv('POSTGRES_DB') ?: 'your_database'
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $dbUser = getenv('POSTGRES_USER') ?: 'default_user';
        $dbPass = getenv('POSTGRES_PASSWORD') ?: 'default_password';

        $this->pdo = new PDO($dsn, $dbUser, $dbPass, $options);
    }

    public function getPaginatedRecipes(int $limit, int $offset): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM recipes ORDER BY created_at DESC LIMIT :limit OFFSET :offset');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $recipes = [];
        while ($row = $stmt->fetch()) {
            $recipes[] = new Recipe(
                $row['id'],
                $row['user_id'],
                $row['title'],
                $row['description'] ?? null,
                new \DateTime($row['created_at']),
                $row['cook_time'] ?? 0,
                $row['thumbnail_image'] ?? '../resources/noimage.png',
                $row['rating_count'] ?? 0,
                $row['average_rating'] ?? 0.0,
                $row['prep_time'] ?? 0,
                $row['temperature'] ?? 0,
                (array) json_decode($row['step_descriptions'], true),
                (array) json_decode($row['step_images'], true),
                $row['servings'] ?? 0,
                (array) json_decode($row['ingredients'], true)
            );
        }
        return $recipes;
    }

    public function getRecipeById(string $id): ?Recipe
    {
        $stmt = $this->pdo->prepare('SELECT * FROM recipes WHERE id = :id LIMIT 1');
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }

        return new Recipe(
            $row['id'],
            $row['user_id'],
            $row['title'],
            $row['description'] ?? null,
            new \DateTime($row['created_at']),
            $row['cook_time'] ?? 0,
            $row['instructions'] ?? null,
            $row['rating_count'] ?? 0,
            $row['average_rating'] ?? 0.0,
            $row['thumbnail_image'] ?? null,
            $row['prep_time'] ?? 0,
            $row['temperature'] ?? 0,
            $row['step_descriptions'] ? json_decode($row['step_descriptions'], true) : [],
            $row['step_images'] ? json_decode($row['step_images'], true) : [],
            $row['servings'] ?? 0,
            $row['ingredients'] ? json_decode($row['ingredients'], true) : []
        );
    }

    public function createRecipeFromData(array $data): void
{
    try {
        $id = $this->generateUniqueRamseyUUID();

        error_log("Generated UUID: " . $id);

        $recipe = new Recipe(
            $id,
            $data['user_id'],
            $data['title'],
            $data['description'] ?? null,
            new \DateTime(),
            $data['cook_time'] ?? 0,
            $data['thumbnail_image'] ?? '../resources/noimage.png',
            0, // rating_count
            0.0, // average_rating
            $data['prep_time'] ?? 0,
            $data['temperature'] ?? 0,
            $data['step_descriptions'] ?? [],
            $data['step_images'] ?? [],
            $data['servings'] ?? 0,
            $data['ingredients'] ?? []
        );

        error_log("Recipe object created successfully");

        $this->insertRecipe($recipe);

        error_log("Recipe inserted successfully");

    } catch (\Throwable $e) {
        error_log("Error in createRecipeFromData: " . $e->getMessage());
        error_log($e->getTraceAsString());
        throw $e;  // Optionally rethrow to see the error in your dev environment
    }
}

    function toPgTextArray(array $phpArray): string {
        return '{' . implode(',', array_map(function ($item) {
            if ($item === null) return 'NULL'; // or '""' if you prefer empty strings
            return '"' . addslashes($item) . '"';
        }, $phpArray)) . '}';
    }

    private function insertRecipe(Recipe $recipe): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO recipes (id, user_id, title, description, created_at, cook_time, rating_count, average_rating, thumbnail_image, prep_time, temperature, step_descriptions, step_images, servings, ingredients)
             VALUES (:id, :user_id, :title, :description, :created_at, :cook_time, :rating_count, :average_rating, :thumbnail_image, :prep_time, :temperature, :step_descriptions, :step_images, :servings, :ingredients)'
        );

        $stmt->execute([
            ':id' => $recipe->getId(),
            ':user_id' => $recipe->getUserId(),
            ':title' => $recipe->getTitle(),
            ':description' => $recipe->getDescription(),
            ':created_at' => $recipe->getCreatedAt()->format('Y-m-d H:i:s'),
            ':cook_time' => $recipe->getCookTime(),
            ':rating_count' => $recipe->getRatingCount(),
            ':average_rating' => $recipe->getAverageRating(),
            ':thumbnail_image' => $recipe->getThumbnailImage(),
            ':prep_time' => $recipe->getPrepTime(),
            ':temperature' => $recipe->getTemperature(),
            ':step_descriptions' => $this->toPgTextArray($recipe->getStepDescriptions()),
            ':step_images' => $this->toPgTextArray($recipe->getStepImages()),
            ':servings' => $recipe->getServings(),
            ':ingredients' => json_encode($recipe->getIngredients())
        ]);
    }

    public function updateRecipe(Recipe $recipe): void
{
    $stmt = $this->pdo->prepare(
        'UPDATE recipes 
         SET title = :title,
             description = :description,
             cook_time = :cook_time,
             instructions = :instructions,
             rating_count = :rating_count,
             average_rating = :average_rating,
             thumbnail_image = :thumbnail_image,
             prep_time = :prep_time,
             temperature = :temperature,
             step_descriptions = :step_descriptions,
             step_images = :step_images,
             servings = :servings,
             ingredients = :ingredients
         WHERE id = :id'
    );

    $stmt->execute([
        ':id' => $recipe->getId(),
        ':title' => $recipe->getTitle(),
        ':description' => $recipe->getDescription(),
        ':cook_time' => $recipe->getCookTime(),
        ':instructions' => $recipe->getInstructions(),
        ':rating_count' => $recipe->getRatingCount(),
        ':average_rating' => $recipe->getAverageRating(),
        ':thumbnail_image' => $recipe->getThumbnailImage(),
        ':prep_time' => $recipe->getPrepTime(),
        ':temperature' => $recipe->getTemperature(),
        ':step_descriptions' => json_encode($recipe->getStepDescriptions()),
        ':step_images' => json_encode($recipe->getStepImages()),
        ':servings' => $recipe->getServings(),
        ':ingredients' => json_encode($recipe->getIngredients())
    ]);
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
        $stmt->execute([':id' => $uuid]);
        $count = (int) $stmt->fetchColumn();
    } while ($count > 0);

    error_log("Generated UUID: " . $uuid); // log it
    return $uuid;
}
    

}
