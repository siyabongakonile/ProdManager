<?php
declare(strict_types = 1);

namespace App\Controllers;

use App\Session;
use Twig\TwigFilter;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

abstract class BaseController{
    /** @property Environment $twig A twig instance. */
    protected Environment $twig;

    public function __construct(){
        $loader = new FilesystemLoader(VIEWS_PATH);
        $this->twig = new Environment($loader);
    }

    /**
     * Load and render a given view.
     *
     * @param string $view The view to render
     * @param array $args The arguments to pass to the view
     * @return void
     */
    protected function render(string $view, array $args = []){
        $session = Session::getInstance();
        $args['userlevel'] = @$session->get('userlevel');
        $args['isLoggedIn'] = @$session->isLoggedIn();
        $template = $this->twig->load($view . '.html.twig');
        echo $template->render($args);
    }

    /**
     * Converts a given page number or limit string to int or
     * returns false if the given string can not be converted to int or is less than 1.
     * 
     * @param string $val The given page or limit number
     * @return int|bool Returns an int if the conversion went through 
     *                  and the the number is not less than 1, false otherwise
     */
    protected function getPageOrLimitNum(string $val): int|bool{
        try{
            $num = (int) $val;
            if($num <= 0)
                return false;
            return $num;
        } catch(\Throwable){
            return false;
        }
    }

    public function addTwigFilter(string $name, callable $callable){
        $this->twig->addFilter(new TwigFilter($name, $callable));
    }
}