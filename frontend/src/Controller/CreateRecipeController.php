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
}