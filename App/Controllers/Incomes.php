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
        $incomes = Income::getAllCategory();
        View::renderTemplate('Incomes/add.html', [
            'incomes' => $incomes
        ]);
    }

    public function createAction()
    {
        $income = new Income($_POST);
        $incomes = Income::getAllCategory();
        if ($income->add()) {
            View::renderTemplate('Incomes/success.html');
        } else {
            View::renderTemplate('Incomes/add.html', [
                'alertshow' => 'true',
                'alertmessage' => 'Przychód nie został dodany!',
                'isValid' => $income->isValid,
                'warnings' => $income->warnings,
                'oldValues' => $_POST,
                'incomes' => $incomes
            ]);
        }
    }
}


?>