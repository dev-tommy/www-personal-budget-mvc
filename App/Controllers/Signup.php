<?php

namespace App\Controllers;

use App\Models\User;
use \Core\View;

/**
 * Sing up controller v0.1
 *
 * PHP version 7.3
 *
 * Created with course PHP MVC framework by author Dave Hollingworth
 * e-mail: tomasz.frydrychowicz.programista@gmail.com
 */

class Signup extends \Core\Controller
{
    protected function before()
    {
    }

    protected function after()
    {
    }

    public function createAction()
    {
        $user = new User($_POST);
        if ($user->save()) {
            View::renderTemplate('Signup/success.html');
        } else {
            View::renderTemplate('Signup/new.html',
            [
                'user' => $user
            ]
        );
        }
    }

    public function newAction()
    {
        View::renderTemplate('Signup/new.html');
    }
}
