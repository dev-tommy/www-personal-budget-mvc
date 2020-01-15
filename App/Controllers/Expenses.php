<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Expense;
/**
 * Expenses controller v0.1
 *
 * PHP version 7.3
 *
 * Created with course PHP MVC framework by author Dave Hollingworth
 * e-mail: tomasz.frydrychowicz.programista@gmail.com
 */

class Expenses extends Authenticated
{
    public function addAction()
    {
        $categories = Expense::getAllCategory();
        $payments = Expense::getAllPayments();
        View::renderTemplate('Incomes/add.html', [
            'categories' => $categories,
            'payments' => $payments,
            'default_date' => 'true',
            'current_date' => date("Y-m-d")
        ]);
    }

    public function createAction()
    {
        if (isset($_POST['button_reset'])) {
            $this->addAction();
            exit();
        }

        $expense = new Expense($_POST);
        $categories = Expense::getAllCategory();
        if ($expense->add()) {
            View::renderTemplate('Expenses/success.html');
        } else {
            View::renderTemplate('Expenses/add.html', [
                'alertshow' => 'true',
                'alertmessage' => 'Przychód nie został dodany!',
                'isValid' => $income->isValid,
                'warnings' => $income->warnings,
                'oldValues' => $_POST,
                'categories' => $categories
            ]);
        }
    }
}
