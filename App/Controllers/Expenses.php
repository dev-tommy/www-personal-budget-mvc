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
        $expenses = Expense::getAllCategory();
        $payments = Expense::getAllPayments();
        View::renderTemplate('Incomes/add.html', [
            'expenses' => $expenses,
            'payments' => $payments,
            'default_date' => 'true',
            'current_date' => date("Y-m-d")
        ]);
    }
}
