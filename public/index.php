<?php

/**
 * Front controller v0.1
 *
 * PHP version 7.3
 *
 * Created with course PHP MVC framework by author Dave Hollingworth
 * e-mail: tomasz.frydrychowicz.programista@gmail.com
 */
ini_set('session.cookie_lifetime', '864000');

 require '../vendor/autoload.php';

//error and exception handling
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

//session start
session_start();

//routing

$router = new Core\Router();

$router->add('', ['controller' => 'Signup', 'action' => 'new']);
$router->add('incomes', ['controller' => 'Incomes', 'action' => 'add']);
$router->add('expenses', ['controller' => 'Expenses', 'action' => 'add']);
$router->add('balances', ['controller' => 'Balances', 'action' => 'show']);
$router->add('the-balance-of-the-current-month', ['controller' => 'Balances', 'action' => 'showCurrentMonth']);
$router->add('the-balance-of-the-current-year', ['controller' => 'Balances', 'action' => 'showCurrentYear']);
$router->add('the-balance-of-the-previous-month', ['controller' => 'Balances', 'action' => 'showPreviousMonth']);
$router->add('signup', ['controller' => 'Signup', 'action' => 'new']);
$router->add('login', ['controller' => 'Login', 'action' => 'new']);
$router->add('logout', ['controller' => 'Login', 'action' => 'destroy']);
$router->add('{controller}/{action}');
//$router->add('{controller}/{id:\d+}/{action}');
//$router->add('admin/{controller}/{action}', ['namespace' => 'Admin']);

$router->dispatch($_SERVER['QUERY_STRING']);