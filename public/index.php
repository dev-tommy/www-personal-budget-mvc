<?php

/**
 * PHP version 7.3
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
$router->add('add-income', ['controller' => 'Incomes', 'action' => 'add']);
$router->add('save-income', ['controller' => 'Incomes', 'action' => 'create']);
$router->add('add-expense', ['controller' => 'Expenses', 'action' => 'add']);
$router->add('save-expense', ['controller' => 'Expenses', 'action' => 'create']);
$router->add('show-balance', ['controller' => 'Balances', 'action' => 'showCurrentMonth']);
$router->add('the-balance-of-the-current-month', ['controller' => 'Balances', 'action' => 'showCurrentMonth']);
$router->add('the-balance-of-the-current-year', ['controller' => 'Balances', 'action' => 'showCurrentYear']);
$router->add('the-balance-of-the-previous-month', ['controller' => 'Balances', 'action' => 'showPreviousMonth']);
$router->add('the-balance-of-period', ['controller' => 'Balances', 'action' => 'showForPeriod']);
$router->add('new-user-registration', ['controller' => 'Signup', 'action' => 'new']);
$router->add('add-new-user', ['controller' => 'Signup', 'action' => 'create']);
$router->add('sign-up-success', ['controller' => 'Signup', 'action' => 'success']);
$router->add('attempt-unautorized-entry', ['controller' => 'Login', 'action' => 'new']);
$router->add('user-login', ['controller' => 'Login', 'action' => 'create']);
$router->add('log-out-user', ['controller' => 'Login', 'action' => 'destroy']);
$router->add('show-settings', ['controller' => 'Settings', 'action' => 'show']);
$router->add('element-add', ['controller' => 'Settings', 'action' => 'add']);
$router->add('element-remove', ['controller' => 'Settings', 'action' => 'delete']);
$router->add('element-edit', ['controller' => 'Settings', 'action' => 'edit']);
$router->add('get-all-incomes-categories', ['controller' => 'Incomes', 'action' => 'getAllCategories']);
//$router->add('{controller}/{action}');
//$router->add('{controller}/{id:\d+}/{action}');
//$router->add('admin/{controller}/{action}', ['namespace' => 'Admin']);

$router->dispatch($_SERVER['QUERY_STRING']);