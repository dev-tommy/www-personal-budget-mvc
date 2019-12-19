<?php

namespace App\Controllers;

use \Core\View;
/**
 * Incomes controller v0.1
 *
 * PHP version 7.3
 *
 * Created with course PHP MVC framework by author Dave Hollingworth
 * e-mail: tomasz.frydrychowicz.programista@gmail.com
 */

class Home extends \Core\Controller
{
    protected function before()
    {

    }

    protected function after()
    {

    }

    public function indexAction()
    {
        View::renderTemplate('Home/index.php',
            [
                'name' => 'Tom',
                'colours' => ['red', 'green' , 'blue']
            ]
        );
    }
}
