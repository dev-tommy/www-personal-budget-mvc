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
    public $months = array('styczeń', 'luty', 'marzec', 'kwiecień', 'maj', 'czerwiec', 'lipiec', 'sierpień', 'wrzesień', 'październik', 'listopad', 'grudzień');

    private function calcSum($sqlArray)
    {
        $sum = 0.0;
        foreach ($sqlArray as $values) {
           $sum += floatval($values['Sum_of_amounts']);
        }
        return $sum;
    }

    function rand_color()
    {
        return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    }

    private function getChartElements($expenses)
    {

    }

    public function showForPeriodAction()
    {
        $alertShow = 'false';
        $alert = '';

        $startDate = strtotime($_GET["startDate"]);
        if ($startDate > strtotime(date("Y-m-d"))) {
            $startDate = strtotime(date("Y-m-d"));
            $alertShow = 'true';
            $alert = "Data początkowa była późniejsza od dzisiejszej! Skorygowano!";
        }

        $endDate = strtotime($_GET["endDate"]);
        if ($endDate > strtotime(date("Y-m-d"))) {
            $endDate = strtotime(date("Y-m-d"));
            $alertShow = 'true';
            $alert = "Data końcowa była późniejsza od dzisiejszej! Skorygowano!";
        }

        if ($startDate > $endDate) {
            $startDate = $endDate;
            $alertShow = 'true';
            $alert = "Data początkowa była późniejsza od końcowej! Skorygowano!";
        }

        $startDate = date("Y-m-d", $startDate);
        $endDate = date("Y-m-d", $endDate);
        $msg = "Bilans za okres:\n od " . $startDate . " do " . $endDate;

        $incomes = Balance::getIncomes($startDate, $endDate);
        $expenses = Balance::getExpenses($startDate, $endDate);
        View::renderTemplate('Balances/show.html', [
            'incomes' => $incomes,
            'expenses' => $expenses,
            'periodBalanceMsg' => $msg,
            'totalIncomesAmount' => $this->calcSum($incomes),
            'totalExpensesAmount' => $this->calcSum($expenses),
            'default_date' => 'true',
            'current_fromDate' => date("Y-m")."-01",
            'current_toDate' => date("Y-m-d"),
            'alertshow' => $alertShow,
            'alertmessage' => $alert
        ]);
    }

    public function showPreviousMonthAction()
    {
        $startDate = strtotime(date("Y-m") . "-01");
        $startDate = strtotime("-1 month", $startDate);
        $endDate = strtotime("+1 month, -1 day", $startDate);
        $previousMonthName =  $this->months[date("m", $startDate) - 1];

        $startDate = date("Y-m-d", $startDate);
        $endDate = date("Y-m-d", $endDate);
        $msg = 'Bilans z poprzedniego miesiąca [' . $previousMonthName . ']:';

        $incomes = Balance::getIncomes($startDate, $endDate);
        $expenses = Balance::getExpenses($startDate, $endDate);
        View::renderTemplate('Balances/show.html', [
            'incomes' => $incomes,
            'expenses' => $expenses,
            'periodBalanceMsg' => $msg,
            'totalIncomesAmount' => $this->calcSum($incomes),
            'totalExpensesAmount' => $this->calcSum($expenses),
            'default_date' => 'true',
            'current_fromDate' => date("Y-m") . "-01",
            'current_toDate' => date("Y-m-d")
        ]);
    }

    public function showCurrentMonthAction()
    {
        $startDate = strtotime(date("Y-m") . "-01");
        $endDate = strtotime("+1 month, -1 day", $startDate);
        $currentMonthName =  $this->months[date("m") - 1];

        $startDate = date("Y-m-d", $startDate);
        $endDate = date("Y-m-d", $endDate);
        $msg = 'Bilans z bieżącego miesiąca [' . $currentMonthName . ']:';

        $incomes = Balance::getIncomes($startDate, $endDate);
        $expenses = Balance::getExpenses($startDate, $endDate);
        View::renderTemplate('Balances/show.html', [
            'incomes' => $incomes,
            'expenses' => $expenses,
            'periodBalanceMsg' => $msg,
            'totalIncomesAmount' => $this->calcSum($incomes),
            'totalExpensesAmount' => $this->calcSum($expenses),
            'default_date' => 'true',
            'current_fromDate' => date("Y-m") . "-01",
            'current_toDate' => date("Y-m-d")
        ]);
    }

    public function showCurrentYearAction()
    {
        $startDate = strtotime(date("Y") . "-01-01");
        $endDate = strtotime(date("Y-m-d"));

        $startDate = date("Y-m-d", $startDate);
        $endDate = date("Y-m-d", $endDate);
        $msg = 'Bilans z bieżącego roku [' . date("Y") . ']:';

        $incomes = Balance::getIncomes($startDate, $endDate);
        $expenses = Balance::getExpenses($startDate, $endDate);
        View::renderTemplate('Balances/show.html', [
            'incomes' => $incomes,
            'expenses' => $expenses,
            'periodBalanceMsg' => $msg,
            'totalIncomesAmount' => $this->calcSum($incomes),
            'totalExpensesAmount' => $this->calcSum($expenses),
            'default_date' => 'true',
            'current_fromDate' => date("Y-m") . "-01",
            'current_toDate' => date("Y-m-d")
        ]);
    }
}
