<?php

namespace App\Entity;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Recipe implements \JsonSerializable
{
    private UuidInterface $id;
    private UuidInterface $user_id;
    private string $title;
    private ?string $description;
    private \DateTime $created_at;
    private int $cook_time;
    private ?string $thumbnail_image = null;
    private int $rating_count = 0;
    private float $average_rating = 0.0;
    private float $prep_time = 0;
    private float $temperature = 0;
    private array $step_descriptions = [];
    private array $step_images = [];
    private int $servings = 0;
    private array $ingredients = [];

    public function __construct(
        $id,
        $user_id,
        string $title,
        ?string $description,
        \DateTime $created_at,
        int $cook_time,
        ?string $thumbnail_image = null,
        int $rating_count = 0,
        float $average_rating = 0.0,
        float $prep_time = 0,
        float $temperature = 0,
        array $step_descriptions = [],
        array $step_images = [],
        int $servings = 0,
        array $ingredients = []
    ) {
        $this->id = is_string($id) ? Uuid::fromString($id) : $id;
        $this->user_id = is_string($user_id) ? Uuid::fromString($user_id) : $user_id;
        $this->title = $title;
        $this->description = $description;
        $this->created_at = $created_at;
        $this->cook_time = $cook_time;
        $this->thumbnail_image = $thumbnail_image;
        $this->rating_count = $rating_count;
        $this->average_rating = $average_rating;
        $this->prep_time = $prep_time;
        $this->temperature = $temperature;
        $this->step_descriptions = $step_descriptions;
        $this->step_images = $step_images;
        $this->servings = $servings;
        $this->ingredients = $ingredients;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function setId(UuidInterface $id): void
    {
        $this->id = $id;
    }

    public function getUserId(): UuidInterface
    {
        return $this->user_id;
    }

    public function setUserId($user_id): void
    {
        $this->user_id = is_string($user_id) ? Uuid::fromString($user_id) : $user_id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTime $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getCookTime(): int
    {
        return $this->cook_time;
    }

    public function setCookTime(int $cook_time): void
    {
        $this->cook_time = $cook_time;
    }

    public function getRatingCount(): int
    {
        return $this->rating_count;
    }

    public function setRatingCount(int $rating_count): void
    {
        $this->rating_count = $rating_count;
    }

    public function getAverageRating(): float
    {
        return $this->average_rating;
    }

    public function setAverageRating(float $average_rating): void
    {
        $this->average_rating = $average_rating;
    }

    public function getThumbnailImage(): ?string
    {
        return $this->thumbnail_image;
    }

    public function setThumbnailImage(?string $thumbnail_image): void
    {
        $this->thumbnail_image = $thumbnail_image;
    }

    public function getPrepTime(): float
    {
        return $this->prep_time;
    }

    public function setPrepTime(float $prep_time): void
    {
        $this->prep_time = $prep_time;
    }

    public function getTemperature(): float
    {
        return $this->temperature;
    }

    public function setTemperature(float $temperature): void
    {
        $this->temperature = $temperature;
    }

    public function getStepDescriptions(): array
    {
        return $this->step_descriptions;
    }

    public function setStepDescriptions(array $step_descriptions): void
    {
        $this->step_descriptions = $step_descriptions;
    }

    public function getStepImages(): array
    {
        return $this->step_images;
    }

    public function setStepImages(array $step_images): void
    {
        $this->step_images = $step_images;
    }

    public function getServings(): int
    {
        return $this->servings;
    }

    public function setServings(int $servings): void
    {
        $this->servings = $servings;
    }

    public function getIngredients(): array
    {
        return $this->ingredients;
    }

    public function setIngredients(array $ingredients): void
    {
        $this->ingredients = $ingredients;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->toString(),
            'user_id' => $this->user_id->toString(),
            'title' => $this->title,
            'description' => $this->description,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'cook_time' => $this->cook_time,
            'rating_count' => $this->rating_count,
            'average_rating' => $this->average_rating,
            'thumbnail_image' => $this->thumbnail_image,
            'prep_time' => $this->prep_time,
            'temperature' => $this->temperature,
            'step_descriptions' => $this->step_descriptions,
            'step_images' => $this->step_images,
            'servings' => $this->servings,
            'ingredients' => $this->ingredients
        ];
    }
}
