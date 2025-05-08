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
    /*
     *
     *
     */
    private UserModel $userModel;
    private AuthService $authService;
    private JsonView $view;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->view = new JsonView();
        $this->authService = new AuthService();
    }

    /**
     * @return void
     */
    public function register()
    {
        if (!$this->validateClientApplication()) return;

        //extract json data from the request body
        $data = json_decode(file_get_contents('php://input'), true);
        if(!isset($data['username']) || !isset($data['password'])) {
            $this->view->render(['error' => 'Invalid request'], 400);
            return;
        }

        //send to model
        try {
            $user = $this->userModel->createUser($data['username'], $data['password']);
        }catch (\Exception $e) {
            $this->view->render(['error' => 'User already exists'], 409);
            return;
        }

        $this->view->render($user, 201); // 201 - Created, returns username
    }

    /**
     * @return void
     */
    public function login()
    {
        if (!$this->validateClientApplication()) return;

        // Get the request body
        $body = json_decode(file_get_contents('php://input'), true);
        
        // Check if the request body contains the required fields
        if (!isset($body['username']) || !isset($body['password'])) {
            $this->view->render(['error' => 'Invalid request'], 400);
            return;
        }

        try {
            $token = $this->authService->login($body['username'], $body['password']);
        }catch (\Exception $e) {
            $this->view->render(['error' => 'Invalid username or password'], 401);
            return;
        }

        $this->view->render(['token' => $token], 200);
    }

    /**
     * @param string $userId
     * @return void
     */
    public function delete(string $userId): void
    {
        if (!$this->validateClientApplication()) return;

        if(!Uuid::isValid($userId)){
            $this->view->render(['error' => 'Invalid request'], 400);
            return;
        }

        try {
            $this->userModel->deleteUserById(Uuid::fromString($userId));
        } catch (\Exception $e) {
            $this->view->render(['message' => 'User not found'], 404);
            return;
        }
        
        $this->view->render([],204);
    }

    /**
     * Validate the Authorization header $clientId and $clientSecret
     * that date is base64 encoded and separated by a colon.
     * If the client is not authorized, return a 401 Unauthorized response.
     *
     * @return void
     */
    private function validateClientApplication(): bool
    {
        $headers = getallheaders();
        $authorizationHeader = $headers['Authorization'] ?? null;
        
        if ($authorizationHeader === null) {
            $this->view->render(['error' => 'Not allowed'], 403);
            return false;
        }
        
        // Extract clientId and clientSecret from the Authorization header
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
}