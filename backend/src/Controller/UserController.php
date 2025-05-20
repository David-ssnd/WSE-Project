<?php

namespace App\Controller;

use App\View\JsonView;
use App\Service\UserModel;
use App\Service\FollowModel;
use App\Service\AuthService;

/**
 *
 */
class UserController
{
    private JsonView $view;
    private UserModel $userModel;
    private FollowModel $followModel;
    private AuthService $authService;

    /**
     *
     */
    public function __construct()
    {
        $this->view = new JsonView();
        $this->userModel = new UserModel();
        $this->followModel = new FollowModel();
        $this->authService = new AuthService();
    }

    /**
     * @param string $username
     * @return void
     */
    public function getPublicProfile(string $username): void
    {
        try {
            $user = $this->userModel->getUserByUsername($username);
        } catch (\Exception $e) {
            $this->view->render(['error' => $e->getMessage()], 401);
            return;
        }

        $this->view->render([
            'username' => $username,
            'fullname' => $user->getFullname(),
            'email'    => $user->getEmail(),
            'phone'    => $user->getPhone()
        ], 200);
    }

    /**
     * @param string $username
     * @return void
     */
    public function follow(string $username): void
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

        // Check if the user is already following
        if ($this->followModel->isFollowing($follower->getId(), $followed->getId())) {
            $this->view->render(['error' => 'Already following'], 400);
            return;
        }

        // Check if not following self
        if ($follower->getId() == $followed->getId()) {
            $this->view->render(['error' => 'Cannot follow yourself'], 400);
            return;
        }

        // Create a new follow relationship
        $this->followModel->createFollow($follower->getId(), $followed->getId());
        $this->view->render([
            'follower' => $follower->getUsername(),
            'followed' => $followed->getUsername()
        ], 201);
    }

    /**
     * @param string $username
     * @return void
     */
    public function unfollow(string $username): void
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

        // Check if the user is not following
        if (!$this->followModel->isFollowing($follower->getId(), $followed->getId())) {
            $this->view->render(['error' => 'Not following'], 400);
            return;
        }

        // Delete the follow relationship
        $this->followModel->deleteFollow($follower->getId(), $followed->getId());
        $this->view->render([
            'follower' => $follower->getUsername(),
            'followed' => $followed->getUsername()
        ], 200);
    }

    /**
     * @param string $username
     * @return void
     */
    public function getFollowers(string $username): void
    {
        // Validation is in getUserFromToken method

        try {
            $user = $this->userModel->getUserByUsername($username);
        } catch (\Exception $e) {
            $this->view->render(['error' => $e->getMessage()], 401);
            return;
        }

        // Get followers
        $followers = $this->followModel->getFollowers($user->getId());
        $this->view->render($followers, 200);
    }

    /**
     * @param string $username
     * @return void
     */
    public function getFollowing(string $username): void
    {
        // Validation is in getUserFromToken method

        try {
            $user = $this->userModel->getUserByUsername($username);
        } catch (\Exception $e) {
            $this->view->render(['error' => $e->getMessage()], 401);
            return;
        }

        // Get following
        $following = $this->followModel->getFollowing($user->getId());
        $this->view->render($following, 200);
    }
}