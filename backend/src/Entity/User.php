<?php

namespace App\Entity;

use Ramsey\Uuid\UuidInterface;

/**
 *
 */
class User implements \JsonSerializable
{
    /**
     * @var UuidInterface
     */
    private UuidInterface $id;
    /**
     * @var string
     */
    private string $username;
    /**
     * @var string
     */
    private string $passwordHash;
    /**
     * @var ?string
     */
    private ?string $email;
    
    /**
     * @param UuidInterface $id
     * @param string $username
     * @param string $passwordHash
     */
    public function __construct(UuidInterface $id, string $username, string $passwordHash, string $email = null)
    {
        $this->id = $id;
        $this->username = $username;
        $this->passwordHash = $passwordHash;
        $this->email = $email;
    }
    
    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }
    
    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    /**
     * @param string $passwordHash
     */
    public function setPasswordHash(string $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
    }

    /**
     * @return ?string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function jsonSerialize(): mixed
    {
        return [
            // 'id'       => $this->id->toString(),
            'username' => $this->username,
            'email'    => $this->email
        ];
    }
}