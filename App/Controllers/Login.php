<?php

namespace App\Controllers;

use \App\Auth;
use App\Flash;
use \Core\View;
use \App\Models\User;

/**
 * Login controller v0.1
 *
 * PHP version 7.3
 *
 * Created with course PHP MVC framework by author Dave Hollingworth
 * e-mail: tomasz.frydrychowicz.programista@gmail.com
 */

class Login extends \Core\Controller
{
    protected function before()
    {
    }

    protected function after()
    {
    }

    public function newAction()
    {
        View::renderTemplate('Login/new.html');
    }

    public function createAction()
    {
        $user = User::authenticate($_POST['email'], $_POST['password']);
        $remember_me = isset($_POST['remember_me']);
        if ($user) {
            Auth::login($user, $remember_me);
            Flash::addMessage('Login successful');
            $this->redirect(Auth::getReturnToPage());
        } else {
            Flash::addMessage('Login unsuccessful');
            View::renderTemplate('Login/new.html', [
                'email' => $_POST['email'],
                'remember_me' => $remember_me
            ]);
        }
    }

    public function destroyAction()
    {
        Auth::logout();

        $this->redirect('/');
    }
}