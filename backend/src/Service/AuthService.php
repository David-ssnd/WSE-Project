<?php

namespace App\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

use App\Service\ClientApplicationModel;
use App\Service\UserModel;
use App\Entity\User;

class AuthService
{
    private UserModel $userModel;
    private ClientApplicationModel $clientApplicationModel;
    private string $jwtSecret;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->clientApplicationModel = new ClientApplicationModel();
        $this->jwtSecret = getenv('JWT_SECRET') ?: 'default_secret';
    }

    public function login(string $username, string $password): string
    {
        $user = $this->userModel->getUserByUsername($username);

        if (password_verify($password, $user->getPasswordHash())) {
            return JWT::encode(
                [
                    'iss' => 'http://localhost:8080',
                    'sub' => $user->getId(),
                    'exp' => time() + 3600,
                    'iat' => time(),
                    'nbf' => time(),
                    'krilo' => [
                        'role' => 'user',
                        'username' => $user->getUsername(),
                    ],
                ],
                $this->jwtSecret,
                'HS256'
            );
        }

        throw new \Exception('Invalid username or password');
    }

    public function verifyJWT(string $token, string $requiredRole): bool
    {
        try {
            $decoded = JWT::decode($token, new Key($this->jwtSecret, 'HS256'));
            $payload = (array)$decoded;
            $krilo = (array)$payload['krilo'] ?? [];

            return isset($krilo['role']) && $krilo['role'] === $requiredRole;
        } catch (\Exception $e) {
            throw new \Exception('Invalid token or role');
        }
    }

    public function validateToken(): \stdClass
    {

        $token = $_COOKIE['token'] ?? null;

        if (!$token) {
            throw new \Exception('Authorization token not found');
        }

        return JWT::decode($token, new Key($this->jwtSecret, 'HS256'));
    }

    public function getUserFromToken(): User
    {
        $data = $this->validateToken();

        if (isset($data->krilo->username)) {
            return $this->userModel->getUserByUsername($data->krilo->username);
        }

        throw new \Exception('Invalid token or user not found');
    }

    public function updateUserByToken(array $body): User
    {
        $token = $this->validateToken();

        if (!isset($token->krilo->username)) {
            throw new \Exception('Invalid token or user not found');
        }

        $this->userModel->updateUserByUsername($token->krilo->username, $body);
        return $this->userModel->getUserByUsername($token->krilo->username);
    }

    public function register(string $username, string $password, string $email): bool
    {
        return $this->userModel->createUser($username, $email, $password);
    }

    public function clientIsAuthorized(string $clientId, string $clientSecret): bool
    {
        $client = $this->clientApplicationModel->getClientByClientId($clientId);
        return $client && $clientSecret === $client->getClientSecret();
    }
}
