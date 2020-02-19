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

    public function addAction()
    {
        $answer = 'Nie znaleziono opcji!';
        if (!isset($_POST['source'])) {
            exit($answer);
        }
        elseif ($_POST['source'] == 'income') {
            $income = new Income($_POST);
            $answer = $income->addCategory();
        }
        elseif ($_POST['source'] == 'expense') {
            $expense = new Expense($_POST);
            $answer = $expense->addCategory();
        }
        elseif ($_POST['source'] == 'payment') {
            $expense = new Expense($_POST);
            $answer = $expense->addMethod();
        }
        echo $answer;
    }

    public function deleteAction()
    {
        $answer = 'Nie znaleziono opcji!';
        if (!isset($_POST['id']) || !isset($_POST['source'])) {
            exit($answer);
        }
        elseif ($_POST['source'] == 'income') {
            $income = new Income($_POST);
            $answer = $income->deleteCategory();
        }
        elseif ($_POST['source'] == 'expense') {
            $expense = new Expense($_POST);
            $answer = $expense->deleteCategory();
        }
        elseif ($_POST['source'] == 'payment') {
            $expense = new Expense($_POST);
            $answer = $expense->deleteMethod();
        }
        echo $answer;
    }

    public function editAction()
    {
        $answer = 'Wybrana pozycja nie istnieje';
        if (!isset($_POST['id']) || !isset($_POST['source']) || !isset($_POST['name'])) {
            exit($answer);
        }
        elseif ($_POST['source'] == 'income') {
            $income = new Income($_POST);
            $answer = $income->editCategory();
        }
        elseif ($_POST['source'] == 'expense') {
            $expense = new Expense($_POST);
            $answer = $expense->editCategory();
        }
        elseif ($_POST['source'] == 'payment') {
            $expense = new Expense($_POST);
            $answer = $expense->editMethod();
        } elseif ($_POST['source'] == 'user') {
            $user = new User($_POST);
            $answer = $user->editUser();
        }
        echo $answer;
    }
}

?>