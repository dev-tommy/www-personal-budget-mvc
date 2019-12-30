<?php

namespace Core;

/**
* View
*
* PHP version 7.3
*
* Created with course PHP MVC framework by author Dave Hollingworth
* e-mail: tomasz.frydrychowicz.programista@gmail.com
*/

class View
{
    public static function render($view, $args = [])
    {
        extract($args, EXTR_SKIP);

        $file = "../App/Views/$view";

        if  (is_readable($file)) {
            require $file;
        } else {
            throw new \Exception("$file not found");
        }
    }

    public static function renderTemplate($template, $args = [])
    {
        static $twig = null;

        if ($twig === null) {
            $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__).'/App/Views');
            $twig = new \Twig\Environment($loader);
            $twig->addGlobal('is_logged_in', \App\Auth::isLoggedIn());
            $twig->addGlobal('current_user', \App\Auth::getUser());
            $twig->addGlobal('flash_messages', \App\Flash::getMessage());
        }

        echo $twig->render($template, $args);
    }
}