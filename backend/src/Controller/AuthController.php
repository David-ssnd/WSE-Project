<?php

namespace App\Controller;

use \App\Service\UserModel;
use \App\Service\AuthService;
use \App\View\JsonView;
use Ramsey\Uuid\Uuid;

/**
 *
 */
class AuthController
{
    private UserModel $userModel;
    private AuthService $authService;
    private JsonView $view;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->view = new JsonView();
        $this->authService = new AuthService();
    }

    public function register()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['username'], $data['password'], $data['email'])) {
            $this->view->render(['error' => 'Invalid request'], 400);
            return;
        }

        try {
            $user = $this->userModel->createUser($data['username'], $data['password'], $data['email']);
        } catch (\Exception $e) {
            $this->view->render(['error' => 'User already exists'], 409);
            return;
        }

        // Auto-login: generate JWT and set it in httpOnly cookie
        $token = $this->authService->login($data['username'], $data['password']);
        $this->setAuthCookie($token);

        $this->view->render(['message' => 'User registered and authenticated'], 201);
    }

    public function login()
    {
        //if (!$this->validateClientApplication()) return;

        $body = json_decode(file_get_contents('php://input'), true);
        if (!isset($body['username'], $body['password'])) {
            $this->view->render(['error' => 'Invalid request'], 400);
            return;
        }

        try {
            $token = $this->authService->login($body['username'], $body['password']);
        } catch (\Exception $e) {
            $this->view->render(['error' => 'Invalid username or password'], 401);
            return;
        }

        $this->setAuthCookie($token);
        $this->view->render(['message' => 'Login successful'], 200);
    }

    public function delete(string $userId): void
    {
        if (!$this->validateClientApplication()) return;

        try {
            $this->authService->validateToken(); // token validation
        } catch (\Exception $e) {
            $this->view->render(['error' => 'Unauthorized'], 401);
            return;
        }

        if (!Uuid::isValid($userId)) {
            $this->view->render(['error' => 'Invalid request'], 400);
            return;
        }

        try {
            $this->userModel->deleteUserById(Uuid::fromString($userId));
        } catch (\Exception $e) {
            $this->view->render(['message' => 'User not found'], 404);
            return;
        }

        $this->view->render([], 204);
    }

    private function validateClientApplication(): bool
    {
        $headers = getallheaders();
        $authorizationHeader = $headers['Authorization'] ?? null;

        if ($authorizationHeader === null) {
            $this->view->render(['error' => 'Not allowed'], 403);
            return false;
        }

        if (preg_match('/Basic\s(\S+)/', $authorizationHeader, $matches)) {
            $decodedAuth = base64_decode($matches[1]);
            [$clientId, $clientSecret] = explode(':', $decodedAuth, 2);
        } else {
            $this->view->render(['error' => 'Invalid request'], 400);
            return false;
        }

        if (!$this->authService->clientIsAuthorized($clientId, $clientSecret)) {
            $this->view->render(['error' => 'Invalid client credentials'], 401);
            return false;
        }

        return true;
    }

    private function setAuthCookie(string $token): void
    {
        setcookie("token", $token, [
            'expires' => time() + 3600,
            'path' => '/',
            'secure' => false,           // <-- allow HTTP
            'httponly' => false,         // <-- allow JS access
            'samesite' => 'None'          // safer for localhost
        ]);
    }
}
