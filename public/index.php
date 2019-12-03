<?php

/**
 * Front controller v0.1
 *
 * PHP version 7.3
 *
 * Author: Tomasz Frydrychowicz
 * e-mail: tomasz.frydrychowicz.programista@gmail.com
*/

//echo 'Requested URL = "' . $_SERVER['QUERY_STRING'] . '"';

/**
 * Routing
 */

require '../Core/Router.php';

$router = new Router();

echo get_class($router);