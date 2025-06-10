<?php


namespace App\Controller;
use App\View\HtmlView;


class CreateRecipeController
{
    /**
     * @var HtmlView
     */
    private HtmlView $view;
    
    /**
     * HomeController constructor.
     */
    public function __construct()
    {
        $this->view = new HtmlView();
    }
    
    /**
     * Index action
     *
     * @return void
     */
    public function index(): void
    {
        $this->view->render('/create_recipe/index.html');
    }

    public function create(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo "Method Not Allowed";
        return;
    }

    $title = $_POST['recipe-title'] ?? '';
    $ingredients = $_POST['ingredients'] ?? [];
    $amounts = $_POST['amounts'] ?? [];
    $prepTime = $_POST['prep-time'] ?? 0;
    $cookTime = $_POST['cook-time'] ?? 0;
    $temperature = $_POST['temperature'] ?? 0;
    $servings = $_POST['servings'] ?? 0;
    $stepDescriptions = $_POST['step-description'] ?? [];

    $mainImage = $_FILES['recipe-image'] ?? null;
    $stepImages = $_FILES['step-image'] ?? null;

    // Example validation
    if ($title === '' || empty($ingredients)) {
        http_response_code(400);
        echo "Invalid input";
        return;
    }

    // Example: iterate ingredients
    $steps = [];
    foreach ($stepDescriptions as $i => $desc) {
        $image = [
            'name' => $stepImages['name'][$i] ?? null,
            'type' => $stepImages['type'][$i] ?? null,
            'tmp_name' => $stepImages['tmp_name'][$i] ?? null,
            'error' => $stepImages['error'][$i] ?? null,
            'size' => $stepImages['size'][$i] ?? null,
        ];
        $steps[] = [
            'description' => $desc,
            'image' => $image
        ];
    }

    // Debug / output
    header('Content-Type: application/json');
    echo json_encode([
        'title' => $title,
        'prepTime' => $prepTime,
        'cookTime' => $cookTime,
        'temperature' => $temperature,
        'servings' => $servings,
        'ingredients' => array_map(null, $ingredients, $amounts),
        'mainImage' => $mainImage,
        'steps' => $steps
    ]);
}
}