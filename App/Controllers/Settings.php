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
        if (!isset($_POST['id']) || !isset($_POST['source'])) exit();

        $isIdExist = 'false';

        if ($_POST['source'] == 'income') $elements = Income::getAllCategory();
        elseif ($_POST['source'] == 'expense') $elements = Expense::getAllCategory();
        elseif ($_POST['source'] == 'payment') $elements = Expense::getAllPayments();

        foreach ($elements as $element) {
            if ($_POST['id'] == $element['id']) {
                $isIdExist = 'true';
            }
        }

        if ($isIdExist == 'false') echo 'Wybrana kategoria nie istnieje';
        else {


            echo 'Usunięto element o id: '.$_POST['id']. ' z bazy: ' . $_POST['source'];
        }
    }
}

?>