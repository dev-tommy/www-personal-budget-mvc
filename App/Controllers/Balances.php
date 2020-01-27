<?php

namespace App\Controllers;

use App\Date;
use \Core\View;
use \App\Models\Balance;

/**
 * Incomes controller v0.1
 *
 * PHP version 7.3
 *
 * e-mail: tomasz.frydrychowicz.programista@gmail.com
 */

class Balances extends Authenticated
{
    private function showTemplate($startDate, $endDate, $msg, $alertMsg=null)
    {
        $a['periodBalanceMsg'] = $msg;
        $a['incomes'] = Balance::getIncomes($startDate, $endDate);
        $a['expenses'] = Balance::getExpenses($startDate, $endDate);
        $a['totalIncomesAmount'] = $this->calcSum($a['incomes']);
        $a['totalExpensesAmount'] = $this->calcSum($a['expenses']);
        $a['default_date'] = 'true';
        $a['current_fromDate'] = Date::getFirstDayOfCurrentMonth();
        $a['current_toDate'] = Date::getTodayDate();
        $a['chartElements'] = $this->getChartElements($a['expenses']);

        if (!empty($alertMsg)) {
            $a['alertshow'] = 'true';
            $a['alertmessage'] = $alertMsg;
        }

        View::renderTemplate('Balances/show.html', $a);
    }

    public function showForPeriodAction()
    {
        $alert = '';
        if (!isset($_GET["startDate"])) {
            $_GET["startDate"] = Date::getFirstDayOfCurrentMonth();
        }
        if (!isset($_GET["endDate"])) {
            $_GET["endDate"] = Date::getLastDayOfCurrentMonth();
        }

        $startDate = strtotime($_GET["startDate"]);
        if ($startDate > Date::rawDate(Date::getLastDayOfCurrentMonth())) {
            $startDate = Date::rawDate(Date::getLastDayOfCurrentMonth());
            $alert = "Data początkowa była późniejsza od ostatniego dnia bieżącego miesiąca! Skorygowano!";
        }

        $endDate = strtotime($_GET["endDate"]);
        if ($endDate > Date::rawDate(Date::getLastDayOfCurrentMonth())) {
            $endDate = Date::rawDate(Date::getLastDayOfCurrentMonth());
            $alert = "Data końcowa była późniejsza od ostatniego dnia bieżącego miesiąca! Skorygowano!";
        }

        if ($startDate > $endDate) {
            $startDate = $endDate;
            $alert = "Data początkowa była późniejsza od końcowej! Skorygowano!";
        }

        $startDate = Date::convertDate($startDate);
        $endDate = Date::convertDate($endDate);
        $msg = "Bilans za okres:\n od " . $startDate . " do " . $endDate;

        $this->showTemplate($startDate, $endDate, $msg, $alert);
    }

    public function showPreviousMonthAction()
    {
        $msg =  'Bilans z poprzedniego miesiąca [' . Date::getPreviousMonthName() . ']:';
        $startDate = Date::getFirstDayOfPreviousMonth();
        $endDate = Date::getLastDayOfPreviousMonth();

        $this->showTemplate($startDate, $endDate, $msg);
    }

    public function showCurrentMonthAction()
    {
        $msg =  'Bilans z bieżącego miesiąca [' . Date::getCurrentMonthName() . ']:';
        $startDate = Date::getFirstDayOfCurrentMonth();
        $endDate = Date::getLastDayOfCurrentMonth();

        $this->showTemplate($startDate, $endDate, $msg);
    }

    public function showCurrentYearAction()
    {
        $msg =  'Bilans z bieżącego roku [' . Date::getCurrentYear() . ']:';
        $startDate = Date::getFirstDayOfCurrentYear();
        $endDate = Date::getLastDayOfCurrentMonth();

        $this->showTemplate($startDate, $endDate, $msg);
    }

    private function calcSum($sqlArray)
    {
        $sum = 0.0;
        foreach ($sqlArray as $values) {
            $sum += floatval($values['Sum_of_amounts']);
        }
        return $sum;
    }

    private function getChartElements($expenses)
    {
        $chartElements = array();
        foreach ($expenses as $expense) {
            $chartElements[$expense['Category']] = $expense['Sum_of_amounts'];
        }
        return $chartElements;
    }
}
