<?php

namespace App\Controllers;

use App\Models\Expense;
use App\Models\Income;
use App\Models\User;
use \Core\View;

class Settings extends Authenticated
{
    //get incomes, expenses, payment , user data
    public function showAction()
    {
        $incomeCategories = Income::getAllCategory();
        $expenseCategories = Expense::getAllCategory();
        $paymentMethods = Expense::getAllPayments();
        $user = User::findByID($_SESSION['user_id']);

        View::renderTemplate('Settings/show.html', [
            'incomeCategories' => $incomeCategories,
            'expenseCategories' => $expenseCategories,
            'paymentMethods' => $paymentMethods,
            'user' => $user
        ]);
    }

    public function deleteAction()
    {

    }
}

?>