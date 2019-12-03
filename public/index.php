<?php

/**
 * Front controller v0.1
 *
 * PHP version 7.3
 *
 * Author: Tomasz Frydrychowicz
 * e-mail: tomasz.frydrychowicz.programista@gmail.com
*/

/**
 * Routing
 */

require '../Core/Router.php';

$router = new Router();

$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('posts', ['controller' => 'Posts', 'action' => 'index']);
$router->add('{controller}/{action}');
$router->add('admin/{action}/{controller}');


$url = $_SERVER['QUERY_STRING'];

if ($router->match($url)) {
    echo '<pre>';
    var_dump($router->getParams());
    echo '</pre>';
} else {
    echo "No route found for URL '$url'";
}