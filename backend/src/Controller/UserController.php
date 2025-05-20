<?php

namespace App\Controller;

use App\View\JsonView;
use App\Service\UserModel;
use App\Service\AuthService;

/**
 *
 */
class UserController
{
    private JsonView $view;
    private UserModel $userModel;
    private AuthService $authService;

    /**
     *
     */
    public function __construct()
    {
        $this->view = new JsonView();
        $this->userModel = new UserModel();
        $this->authService = new AuthService();
    }

    /**
     * @param string $username
     * @return void
     */
    public function getPublicProfile(string $username): void
    {
        // Validation is in getUserFromToken method

        try {
            $follower = $this->authService->getUserFromToken();
        } catch (\Exception $e) {
            $this->view->render(['error' => $e->getMessage()], 401);
            return;
        }

        try {
            $followed = $this->userModel->getUserByUsername($username);
        } catch (\Exception $e) {
            $this->view->render(['error' => $e->getMessage()], 401);
            return;
        }

        $this->view->render(204);
    }

    /**
     * @param string $username
     * @return void
     */
    public function follow(string $username): void
    {
        try {
            $user = $this->userModel->getUserByUsername($username);
        } catch (\Exception $e) {
            $this->view->render(['error' => $e->getMessage()], 401);
            return;
        }
    }

    /**
     * @param string $id
     * @return void
     */
    public function unfollow(string $id): void
    {
        // TODO: Implement unfollow() method.
    }

    /**
     * @param string $id
     * @return void
     */
    public function getFollowers(string $id): void
    {
        // TODO: Implement getFollowers() method.
    }

    /**
     * @param string $id
     * @return void
     */
    public function getFollowing(string $id): void
    {
        // TODO: Implement getFollowing() methods.
    }
}