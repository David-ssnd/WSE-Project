<?php


namespace App\Controller;
use App\View\HtmlView;


class RecipeController
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
        $this->view->render('/recipe-page/index.html');
    }
}