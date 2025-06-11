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

    // Read and decode JSON body
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    // Basic validation
    if (!is_array($data)) {
        http_response_code(400);
        echo json_encode(['message' => 'Invalid JSON']);
        return;
    }

    // Extract and sanitize inputs
    $title = $data['title'] ?? '';
    $ingredients = $data['ingredients'] ?? [];
    $prepTime = $data['prep_time'] ?? 0;
    $cookTime = $data['cook_time'] ?? 0;
    $temperature = $data['temperature'] ?? 0;
    $servings = $data['servings'] ?? 0;
    $stepDescriptions = $data['step_descriptions'] ?? [];
    $stepImages = $data['step_images'] ?? [];
    $thumbnailImage = $data['thumbnail_image'] ?? null;

    if (trim($title) === '' || empty($ingredients)) {
        http_response_code(400);
        echo json_encode(['message' => 'Title and ingredients are required']);
        return;
    }

    // Combine step descriptions with corresponding images
    $steps = [];
    foreach ($stepDescriptions as $i => $desc) {
        $steps[] = [
            'description' => $desc,
            'image' => $stepImages[$i] ?? null
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
        'thumbnailImage' => $thumbnailImage,
        'ingredients' => $ingredients,
        'steps' => $steps
    ]);
}
}