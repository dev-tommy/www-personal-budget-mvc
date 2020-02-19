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
        $answer = 'Brak poprawnej pozycji do dodania';
        if (!isset($_POST['source'])) {
            exit($answer);
        } elseif ($_POST['source'] == 'income') {
            $income = new Income($_POST);
            $answer = $income->addCategory();
            //$answer = 'Usunięto element o id: ' . $_POST['id'] . ' z bazy: ' . $_POST['source'];
        }
        //elseif ($_POST['source'] == 'expense') $isIdExist = Expense::getAllCategory();
        //elseif ($_POST['source'] == 'payment') $isIdExist = Expense::getAllPayments();

        echo $answer;
    }

    public function deleteAction()
    {
        $answer = 'Wybrana pozycja nie istnieje';
        if (!isset($_POST['id']) || !isset($_POST['source'])) {
            exit($answer);
        }
        elseif ($_POST['source'] == 'income') {
            $income = new Income($_POST);
            $answer = $income->deleteCategory();
            //$answer = 'Usunięto element o id: ' . $_POST['id'] . ' z bazy: ' . $_POST['source'];
        }
        //elseif ($_POST['source'] == 'expense') $isIdExist = Expense::getAllCategory();
        //elseif ($_POST['source'] == 'payment') $isIdExist = Expense::getAllPayments();

        echo $answer;
    }

    public function editAction()
    {
        $answer = 'Wybrana pozycja nie istnieje';
        if (!isset($_POST['id']) || !isset($_POST['source']) || !isset($_POST['name'])) {
            exit($answer);
        } elseif ($_POST['source'] == 'income') {
            $income = new Income($_POST);
            $answer = $income->editCategory();
            //$answer = 'Usunięto element o id: ' . $_POST['id'] . ' z bazy: ' . $_POST['source'];
        }
        //elseif ($_POST['source'] == 'expense') $isIdExist = Expense::getAllCategory();
        //elseif ($_POST['source'] == 'payment') $isIdExist = Expense::getAllPayments();

        echo $answer;
    }
}

?>