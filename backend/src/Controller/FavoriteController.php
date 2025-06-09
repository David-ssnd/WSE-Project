<?php

namespace App\Controller;

use App\View\JsonView;
use App\Service\AuthService;
use App\Service\FavoriteModel;

class FavoriteController
{
    private JsonView $jsonView;
    private FavoriteModel $favoriteModel;
    private AuthService $authService;

    public function __construct()
    {
        $this->jsonView = new JsonView();
        $this->favoriteModel = new FavoriteModel();
        $this->authService = new AuthService();
    }

    public function add(string $id): void
    {
        $user = $this->authService->getUserFromToken();
        try {
            $this->favoriteModel->addFavorite($user->getId(), $id);
            $this->jsonView->render(['message' => 'Recipe added to favorites'], 201);
        } catch (\Exception $e) {
            $this->jsonView->render(['error' => $e->getMessage()], 500);
        }
    }

    public function remove(string $id): void
    {
        $user = $this->authService->getUserFromToken();
        try {
            $this->favoriteModel->removeFavorite($user->getId(), $id);
            $this->jsonView->render(['message' => 'Recipe removed from favorites'], 200);
        } catch (\Exception $e) {
            $this->jsonView->render(['error' => $e->getMessage()], 500);
        }
    }

    public function list(): void
    {
        $user = $this->authService->getUserFromToken();
        try {
            $favorites = $this->favoriteModel->getUserFavorites($user->getId());
            $this->jsonView->render($favorites, 200);
        } catch (\Exception $e) {
            $this->jsonView->render(['error' => $e->getMessage()], 500);
        }
    }
}
