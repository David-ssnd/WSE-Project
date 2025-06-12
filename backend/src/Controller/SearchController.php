<?php

namespace App\Controller;

use App\View\JsonView;
use App\Service\SearchModel;

class SearchController
{
    private JsonView $jsonView;
    private SearchModel $searchModel;

    public function __construct()
    {
        $this->jsonView = new JsonView();
        $this->searchModel = new SearchModel();
    }

    public function searchRecipes(): void
    {
        // zabranit pristupu z vonku
        try {
            $query = $_GET['query'] ?? '';

            if (empty($query)) {
                $this->jsonView->render(['error' => 'Query parameter is required'], 400);
                return;
            }

            $results = $this->searchModel->searchRecipes($query);
            $this->jsonView->render($results, 200);
        } catch (\Exception $e) {
            $this->jsonView->render(['error' => $e->getMessage()], 500);
        }
    }
}
