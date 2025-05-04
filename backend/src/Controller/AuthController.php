<?php

namespace App\Controller;

use \App\Service\UserModel;
use \App\Service\AuthService;
use \App\View\JsonView;

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
    private JsonView $jsonView;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->jsonView = new JsonView();
        $this->authService = new AuthService();
    }

    /**
     * @return void
     */
    public function register()
    {
        // TODO: Implement register() method.
        //extract json data from the request body
        $data = json_decode(file_get_contents('php://input'), true);
        if(!isset($data['username']) || !isset($data['password'])) {
            $this->jsonView->render(['error' => 'Username and password are required'], 400);
            return;
        }

        //send to model
        try {
            $user = $this->userModel->createUser($data['username'], $data['password']);
        }catch (\Exception $e) {
            $this->jsonView->render(['error' => 'User already exists'], 409);
            return;
        }

        $this->jsonView->render($user, 201);
    }

    /**
     * @return void
     */
    public function login()
    {
        // TODO: Implement login() method.
        // 1. Get the username and password from the request body
        // 2. Validate the input data (e.g., check if username and password are not empty)
        // 3. Check if the user exists in the database using UserModel
        // 4. Verify the password using AuthService
        // 5. If the password is correct, generate a JWT token using AuthService
        // 6. Return the token in the response using JsonView
        // 7. If the password is incorrect, return an error response using JsonView
        // 8. If the user does not exist, return an error response using JsonView
        // 9. Handle any exceptions that may occur during the process and return appropriate error responses
        // 10. Ensure that the response is in JSON format using JsonView
        // 11. Set the appropriate HTTP status code for success or failure
        // 12. Optionally, log the login attempt (success or failure) for auditing purposes
        // 13. Ensure that the JWT token is signed and has an expiration time
        // 14. Optionally, implement rate limiting to prevent brute-force attacks on the login endpoint
        // 15. Ensure that the password is hashed and stored securely in the database
        // 16. Optionally, implement account lockout after a certain number of failed login attempts

        if(!$validateData()){
            $this->jsonView->render(['error' => 'Invalid input data'], 400);
            return;
        }

        //get the request body
        $data = json_decode(file_get_contents('php://input'), true);

        if(!isset($data['username']) || !isset($data['password'])) {
            $this->jsonView->render(['error' => 'Username and password are required'], 400);
            return;
        }

        try {
            $token = $this->authService->login($data['username'], $data['password']);
        }catch (\Exception $e) {
            $this->jsonView->render(['error' => 'Invalid username or password'], 401);
            return;
        }

        $this->jsonView->render(['token' => $token], 200);
    }

    /**
     * @return void
     */
    public function logout()
    {
        if(!$this->validateData()){
            $this->jsonView->render(['error' => 'Invalid input data'], 400);
            return;
        }
        //get the request body
        $data = json_decode(file_get_contents('php://input'), true);

        if(!isset($data['token'])) {
            $this->jsonView->render(['error' => 'Token is required'], 400);
            return;
        }

        try {
            $this->authService->logout($data['token']);
        }catch (\Exception $e) {
            $this->jsonView->render(['error' => 'Invalid token'], 401);
            return;
        }

        $this->jsonView->render(['message' => 'Logged out successfully'], 200);
        return;
    }

    private function validateData() : bool
    {
        // Extract headers
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? null;

        if($authHeader === null) {
            $this->jsonView->render(['error' => 'Authorization header not found'], 401);
            return false;
        }

        // Extract client ID and secret from the Authorization header
        $authParts = explode(':', base64_decode(substr($authHeader, 6)));
        if (count($authParts) !== 2) {
            $this->jsonView->render(['error' => 'Invalid Authorization header format'], 401);
            return false;
        }
    
        if (!$this->authService->validateClient($clientId, $clientSecret)) {
            $this->jsonView->render(['error' => 'Invalid client credentials'], 401);
            return false;
        }


        return true;
    }
}