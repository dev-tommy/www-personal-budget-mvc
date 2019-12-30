<?php

namespace App\Controllers;

use \App\Models\User;

/**
 * Account controller v0.1
 *
 * PHP version 7.3
 *
 * Created with course PHP MVC framework by author Dave Hollingworth
 * e-mail: tomasz.frydrychowicz.programista@gmail.com
 */

class Account extends \Core\Controller
{
    public function validateEmailAction()
    {
        $is_valid = !User::emailExists($_GET['email']);
        echo json_encode($is_valid);
    }
}