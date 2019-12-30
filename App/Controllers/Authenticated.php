<?php

namespace App\Controllers;

/**
 * Authenticated base controller v0.1
 *
 * PHP version 7.3
 *
 * Created with course PHP MVC framework by author Dave Hollingworth
 * e-mail: tomasz.frydrychowicz.programista@gmail.com
 */

 abstract class Authenticated extends \Core\Controller
 {
    protected function before()
    {
        $this->requireLogin();
    }
 }