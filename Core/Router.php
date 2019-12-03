<?php

/**
 * Router v0.1
 *
 * PHP version 7.3
 *
 * Author: Tomasz Frydrychowicz
 * e-mail: tomasz.frydrychowicz.programista@gmail.com
 */

 class Router
 {
    protected $routes = [];
    protected $params = [];

    public function add($route, $params)
    {
        $this->routes[$route] = $params;
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    public function match($url)
    {
        $reg_exp = "/^(?<controller>[a-z-]+)\/(?<action>[a-z-]+)$/i";
        if (preg_match($reg_exp, $url, $matches))
        {
            $params = [];

            foreach ($matches as $key => $match) {
                if (is_string($key)) {
                    $params[$key] = $match;
                }
            }
            $this->params = $params;
            return true;
        }
        return false;
    }

    public function getParams()
    {
        return $this->params;
    }
}

?>

