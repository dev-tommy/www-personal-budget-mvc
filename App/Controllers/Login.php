<?php

namespace App\Controllers;

use \App\Auth;
use App\Flash;
use \Core\View;
use \App\Models\User;

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
        if (isset($_POST['email']))
        {
            $email = $_POST['email'];
        }
        else
        {
            $email="";
        }

        if (isset($_POST['password'])) {
            $password = $_POST['password'];
        } else {
            $password = "";
        }
        $user = User::authenticate($email, $password);
        if ($user) {
            Auth::login($user, false);
            $this->redirect('/add-income');
            //$this->redirect(Auth::getReturnToPage());
        } else {
            View::renderTemplate('Signup/new.html', [
                'oldEmailValue' => $email,
                'alertshow' => 'true',
                'alertmessage' => 'Niepoprawny email lub hasło!',
                'isLoginValid' => 'is-invalid',
                'redNavbarToggler' => 'navbar-toggler-bg-red'
             ]);
        }
    }

    public function destroyAction()
    {
        Auth::logout();

        $this->redirect('/');
    }
}