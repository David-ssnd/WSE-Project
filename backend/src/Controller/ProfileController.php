<?php

namespace App\Controller;

use App\Service\AuthService;
use App\View\JsonView;

/**
 *
 */
class ProfileController
{
    private AuthService $authService;
    private JsonView $view;

    /**
     *
     */
    public function __construct()
    {
        $this->view = new JsonView();
        $this->authService = new AuthService();
    }

    /**
     * @return void
     */
    public function getProfile()
    {
        // Validation is in getUserFromToken method

        try {
            $user = $this->authService->getUserFromToken();
        } catch (\Exception $e) {
            $this->view->render(['error' => $e->getMessage()], 401);
            return;
        }

        $this->view->render([
            'username'        => $user->getUsername(),
            'email'           => $user->getEmail(),
            'profile_picture' => $user->getProfilePicture(),
        ], 200);
    }

    /**
     * @return void
     */
    public function updateProfile()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        try {
            $user = $this->authService->getUserFromToken();
        } catch (\Exception $e) {
            $this->view->render(['error' => $e->getMessage()], 401);
            return;
        }
        
        if (!isset($data['profile_picture'])) {
            $this->view->render(['error' => 'Missing profile_picture'], 400);
            return;
        }

        $base64 = $data['profile_picture'];

        // Validate base64
        if (!preg_match('/^data:image\/(png|jpe?g);base64,/', $base64)) {
            $this->view->render(['error' => 'Invalid image data format.'], 400);
            return;
        }

        try {
            $this->authService->updateProfilePictureByToken($base64);
        } catch (\Exception $e) {
            $this->view->render(['error' => $e->getMessage()], 500);
            return;
        }

        $this->view->render(['message' => 'Profile picture updated'], 200);
    }
}