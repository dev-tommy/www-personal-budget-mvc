<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Income;

class Incomes extends Authenticated
{
    public function addAction()
    {
        $incomes = Income::getAllCategory();
        View::renderTemplate('Incomes/add.html', [
            'incomes' => $incomes,
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

    public function getAllCategoriesAction()
    {
        $incomes = Income::getAllCategory();
        echo json_encode(array_values($incomes));
    }

    public function getCategoryNameLikeAction()
    {
        if (!isset($_POST['categoryName'])) {
            $_POST['categoryName'] = 'wyy';
        }
        $incomesCategory = Income::getCategoryNameLike($_POST['categoryName']);
        if ($incomesCategory) {
            echo json_encode(array_values($incomesCategory));
        } else {
            $answer[0]["name"] = '';
            echo json_encode($answer);
        }
    }
}


?>