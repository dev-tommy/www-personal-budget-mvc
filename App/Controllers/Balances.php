<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Balance;

/**
 * Incomes controller v0.1
 *
 * PHP version 7.3
 *
 * Created with course PHP MVC framework by author Dave Hollingworth
 * e-mail: tomasz.frydrychowicz.programista@gmail.com
 */

class Balances extends Authenticated
{
    public function showAction()
    {
        $incomes = Balance::getIncomes();
        $expenses = Balance::getExpenses();
        View::renderTemplate('Balances/show.html', [
            'incomes' => $incomes,
            'expenses' => $expenses,
            'periodBalanceMsg' => 'Bilans z bieżącego miesiąca',
            'totalIncomesAmount' => '5000,23',
            'totalExpensesAmount' => '3450,11'
            ]);
    }
}
