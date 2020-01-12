<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Income;

/**
 * Incomes controller v0.1
 *
 * PHP version 7.3
 *
 * Created with course PHP MVC framework by author Dave Hollingworth
 * e-mail: tomasz.frydrychowicz.programista@gmail.com
 */

class Incomes extends Authenticated
{
    public function addAction()
    {
        View::renderTemplate('Incomes/add.html', [
            'alertshow' => 'true',
            'alertmessage' => 'Błąd logowania'
        ]);
    }

    public function createAction()
    {
        $income = new Income($_POST);
        if ($income->add()) {
            View::renderTemplate('Incomes/success.html');
        } else {
            View::renderTemplate('Incomes/add.html', [
                'isValid' => $income->isValid,
                'warnings' => $income->warnings,
                'oldValues' => $_POST
            ]);
        }
    }

    public function editAction()
    {
        echo "Edit income";
    }

    public function deleteAction()
    {
        echo "Delete income";
    }

    public function renameAction()
    {
        echo "Rename income";
    }

    public function viewAction()
    {
        echo "View income";
    }
}


?>