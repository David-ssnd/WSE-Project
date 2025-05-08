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
            'username' => $user->getUsername(),
            'fullname' => $user->getFullname(),
            'email'    => $user->getEmail(),
            'phone'    => $user->getPhone()
        ], 200);
    }

    /**
     * @return void
     */
    public function updateProfile()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        try {
            $user = $this->authService->updateUserByToken($data);
        } catch (\Exception $e) {
            $this->view->render(['error' => $e->getMessage()], 401);
            return;
        }

        $this->view->render($user, 200); // 200 - OK
    }
}