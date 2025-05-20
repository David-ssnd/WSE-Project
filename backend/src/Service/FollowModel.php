<?php

namespace App\Service;

use PDO;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 *
 */
class FollowModel
{
    /**
     * @var PDO
     */
    private PDO $pdo;
    
    /**
     *
     */
    public function __construct()
    {
        // setup DSN - Data Source Name
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

    /**
     * @param UuidInterface $userId
     * @param UuidInterface $followedUserId
     * @return bool
     */
    public function isFollowing(UuidInterface $followerId, UuidInterface $followedId): bool
    {
        $stmt = $this->pdo->prepare("SELECT * FROM follows WHERE follower_id = :followerId AND followed_id = :followedId");
        $stmt->bindParam(':followerId', $followerId, PDO::PARAM_STR);
        $stmt->bindParam(':followedId', $followedId, PDO::PARAM_STR);
        $stmt->execute();
        
        return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @param UuidInterface $followerId
     * @param UuidInterface $followedId
     * @return void
     */
    public function createFollow(UuidInterface $followerId, UuidInterface $followedId): void
    {
        $stmt = $this->pdo->prepare("INSERT INTO follows (follower_id, followed_id) VALUES (:followerId, :followedId)");
        $stmt->bindParam(':followerId', $followerId, PDO::PARAM_STR);
        $stmt->bindParam(':followedId', $followedId, PDO::PARAM_STR);
        $stmt->execute();
    }

    /**
     * @param UuidInterface $followerId
     * @param UuidInterface $followedId
     * @return void
     */
    public function deleteFollow(UuidInterface $followerId, UuidInterface $followedId): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM follows WHERE follower_id = :followerId AND followed_id = :followedId");
        $stmt->bindParam(':followerId', $followerId, PDO::PARAM_STR);
        $stmt->bindParam(':followedId', $followedId, PDO::PARAM_STR);
        $stmt->execute();
    }
}