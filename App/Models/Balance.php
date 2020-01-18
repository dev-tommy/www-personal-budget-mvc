<?php

namespace App\Models;

use Core\Model;
use PDO;
use PDOException;

class Balance extends \Core\Model
{
    public function setCorrectDates($get)
    {
        $months = array('styczeń', 'luty', 'marzec', 'kwiecień', 'maj', 'czerwiec', 'lipiec', 'sierpień', 'wrzesień', 'październik', 'listopad', 'grudzień');

        if (isset($get['periodBalance'])) {
            switch ($get['periodBalance']) {
                case 'currentMonth':
                    $startDate = strtotime(date("Y-m") . "-01");
                    $endDate = $endDate = strtotime("+1 month, -1 day", $startDate);
                    $currentMonthName =  $months[date("m") - 1];
                    $msg = 'Bilans z bieżącego miesiąca [' . $currentMonthName . ']:';
                    break;
                case 'previousMonth':
                    $startDate = strtotime(date("Y-m") . "-01");
                    $startDate = strtotime("-1 month", $startDate);
                    $endDate = strtotime("+1 month, -1 day", $startDate);
                    $previousMonthName =  $months[date("m", $startDate) - 1];
                    $msg = 'Bilans z poprzedniego miesiąca [' . $previousMonthName . ']:';
                    break;
                case 'currentYear':
                    $startDate = strtotime(date("Y") . "-01-01");
                    $endDate = strtotime(date("Y-m-d"));
                    $msg = 'Bilans z bieżącego roku [' . date("Y") . ']:';
                    break;
            }
        } else {
            if (isset($get['startDate']) && isset($get['endDate'])) {
                $startDate = strtotime($get["startDate"]);
                if ($startDate > strtotime(date("Y-m-d"))) {
                    $startDate = strtotime(date("Y-m-d"));
                    echo "Data początkowa była późniejsza od dzisiejszej!";
                    return;
                }

                $endDate = strtotime($_GET["endDate"]);
                if ($endDate > strtotime(date("Y-m-d"))) {
                    $endDate = strtotime(date("Y-m-d"));
                    echo "Data początkowa była późniejsza od dzisiejszej!";
                }

                if ($startDate > $endDate) {
                    $startDate = $endDate;
                }

                $msg = 'Bilans za okres:<br /> od ' . date("Y-m-d", $startDate) . ' do ' . date("Y-m-d", $endDate);
            } else {
                $startDate = strtotime(date("Y-m") . "-01");
                $endDate = strtotime("+1 month, -1 day", $startDate);
                $currentMonthName =  $months[date("m") - 1];
                $msg = 'Bilans z bieżącego miesiąca [' . $currentMonthName . ']:';
            }
        }

        $_SESSION["startDate"] =  $startDate;
        $_SESSION["endDate"] =  $endDate;
        $_SESSION["periodBalanceMsg"] = $msg;
    }
    public static function getIncomes()
    {
        $sql = "
        SELECT
            i_userid.name AS 'Category',
            SUM(i.amount) AS 'Sum_of_amounts'
        FROM
            incomes AS i,
            incomes_category_default AS i_userid
        WHERE
            i_userid.id = i.income_category_assigned_to_user_id AND
            i.date_of_income >= '2000-01-01' AND
            i.date_of_income <= '2020-12-31' AND
            i.user_id='4'
            GROUP BY i.income_category_assigned_to_user_id
        ";

        $db = static::getDB();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function getExpenses()
    {
        $sql = "
        SELECT
            e_userid.name AS 'Category',
            SUM(e.amount) AS 'Sum_of_amounts'
        FROM
            expenses AS e,
            expenses_category_default AS e_userid
        WHERE
            e_userid.id = e.expense_category_assigned_to_user_id AND
            e.date_of_expense >= '2000-01-01' AND
            e.date_of_expense <= '2020-12-31' AND
            e.user_id='4'
            GROUP BY e.expense_category_assigned_to_user_id
        ";

        $db = static::getDB();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
}
