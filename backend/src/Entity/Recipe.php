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
    private ?string $instructions = null;

    public function __construct(
        $id,
        $user_id,
        string $title,
        ?string $description,
        \DateTime $created_at,
        int $cook_time,
        ?string $instructions = null
    ) {
        $this->id = is_string($id) ? Uuid::fromString($id) : $id;
        $this->user_id = is_string($user_id) ? Uuid::fromString($user_id) : $user_id;
        $this->title = $title;
        $this->description = $description;
        $this->created_at = $created_at;
        $this->cook_time = $cook_time;
        $this->instructions = $instructions;
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

    public function getInstructions(): ?string
    {
        return $this->instructions;
    }

    public function setInstructions(?string $instructions): void
    {
        $this->instructions = $instructions;
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
            'instructions' => $this->instructions,
        ];
    }
}
