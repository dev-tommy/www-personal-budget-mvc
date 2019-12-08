<?php

/**
 * Front controller v0.1
 *
 * PHP version 7.3
 *
 * Created with course PHP MVC framework by author Dave Hollingworth
 * e-mail: tomasz.frydrychowicz.programista@gmail.com
 */

// autoloader controllers
spl_autoload_register(function ($class) {
    $root = dirname(__DIR__);
    $file = $root . '/' . str_replace('\\', '/', $class) . '.php';
    if (is_readable($file)) {
        require $file;
    }
});


//routing

$router = new Core\Router();

$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('incomes', ['controller' => 'Incomes', 'action' => 'index']);
$router->add('{controller}/{action}');
$router->add('{controller}/{id:\d+}/{action}');

/*
$url = $_SERVER['QUERY_STRING'];

if ($router->match($url)) {
    echo '<pre>';
    var_dump($router->getParams());
    echo '</pre>';
} else {
    echo "No route found for URL '$url'";
}

*/

$router->dispatch($_SERVER['QUERY_STRING']);