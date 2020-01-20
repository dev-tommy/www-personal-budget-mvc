<?php

namespace App\Models;

use Core\Model;
use PDO;
use PDOException;

class Balance extends \Core\Model
{
    public static function getIncomes($startDate, $endDate)
    {
        $id = $_SESSION['user_id'];
        $sql = "
        SELECT
            i_userid.name AS 'Category',
            SUM(i.amount) AS 'Sum_of_amounts'
        FROM
            incomes AS i,
            incomes_category_default AS i_userid
        WHERE
            i_userid.id = i.income_category_assigned_to_user_id AND
            i.date_of_income >= '$startDate' AND
            i.date_of_income <= '$endDate' AND
            i.user_id='$id'
            GROUP BY i.income_category_assigned_to_user_id
        ";

        $db = static::getDB();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    public static function getTotalIncomesAmount()
    {
        $sql = "
        SELECT
            SUM(i.amount) AS 'Total'
        FROM
            incomes AS i,
            incomes_category_default AS i_userid
        WHERE
            i_userid.id = i.income_category_assigned_to_user_id AND
            i.date_of_income >= '2000-01-01' AND
            i.date_of_income <= '2020-12-31' AND
            i.user_id='4'
        ";

        $db = static::getDB();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result[0]['Total'];
    }

    public static function getTotalExpensesAmount()
    {
        $sql = "
        SELECT
            SUM(e.amount) AS 'Total'
        FROM
            expenses AS e,
            expenses_category_default AS e_userid
        WHERE
            e_userid.id = e.expense_category_assigned_to_user_id AND
            e.date_of_expense >= '2000-01-01' AND
            e.date_of_expense <= '2020-12-31' AND
            e.user_id='4'
        ";

        $db = static::getDB();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result[0]['Total'];
    }

    public static function getExpenses($startDate, $endDate)
    {
        $id = $_SESSION['user_id'];
        $sql = "
        SELECT
            e_userid.name AS 'Category',
            SUM(e.amount) AS 'Sum_of_amounts'
        FROM
            expenses AS e,
            expenses_category_default AS e_userid
        WHERE
            e_userid.id = e.expense_category_assigned_to_user_id AND
            e.date_of_expense >= '$startDate' AND
            e.date_of_expense <= '$endDate' AND
            e.user_id='$id'
            GROUP BY e.expense_category_assigned_to_user_id
        ";

        $db = static::getDB();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
}
