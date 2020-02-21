<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Expense;

class Expenses extends Authenticated
{
    public function addAction()
    {
        $categories = Expense::getAllCategory();
        $payments = Expense::getAllPayments();
        View::renderTemplate('Expenses/add.html', [
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
        $payments = Expense::getAllPayments();
        if ($expense->add()) {
            View::renderTemplate('Expenses/success.html');
        } else {
            View::renderTemplate('Expenses/add.html', [
                'alertshow' => 'true',
                'alertmessage' => 'Wydatek nie został dodany!',
                'isValid' => $expense->isValid,
                'warnings' => $expense->warnings,
                'oldValues' => $_POST,
                'categories' => $categories,
                'payments' => $payments
            ]);
        }
    }

    public function getAllCategoriesAction()
    {
        $expenses = Expense::getAllCategory();
        echo json_encode(array_values($expenses));
    }

    public function getAllMethodsAction()
    {
        $methods = Expense::getAllPayments();
        echo json_encode(array_values($methods));
    }

    public function getTotalMonthlyExpensesAction()
    {
        if (!isset($_POST['categoryId'])) exit(0);

        $categoryId = $_POST['categoryId'];
        $exepnsesSum = Expense::getTotalMonthlyExpenses($categoryId);
        echo json_encode(array_values($exepnsesSum));
    }
}
