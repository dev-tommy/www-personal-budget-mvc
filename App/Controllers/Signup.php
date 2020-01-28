<?php

namespace App\Controllers;

use App\Models\User;
use \Core\View;

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
        if (!isset($_POST['name'])) {
            $_POST['name']='';
        }
        if (!isset($_POST['email'])) {
            $_POST['email'] = '';
        }
        if (!isset($_POST['password'])) {
            $_POST['password'] = '';
        }
        $user = new User($_POST);
        if ($user->save()) {
            $this->redirect('/sign-up-success');
        } else {
            View::renderTemplate('Signup/new.html',
            [
                'alertshow' => 'true',
                'alertmessage' => 'Użytkownik nie został zarejestrowany!',
                'isValid' => $user->isValid,
                'warnings' => $user->warnings,
                'oldValues' => $_POST
            ]
        );
        }
    }

    public function newAction()
    {
        View::renderTemplate('Signup/new.html');
    }

    public function successAction()
    {
        View::renderTemplate('Signup/success.html');
    }
}
