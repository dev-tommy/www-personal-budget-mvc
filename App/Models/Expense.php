<?php

namespace App\Models;

use Core\Model;
use DateTime;
use PDO;
use PDOException;

class Expense extends \Core\Model
{
    public $warnings = [];

    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    public function add()
    {
        $this->validate();
        if (empty($this->isValid)) {
            $sql = 'INSERT INTO expenses (user_id, expense_category_assigned_to_user_id, payment_method_assigned_to_user_id, amount, date_of_expense, expense_comment) VALUES (:user_id, :expense_category_assigned_to_user_id, :payment_method_assigned_to_user_id, :amount, :date_of_expense, :expense_comment)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':expense_category_assigned_to_user_id', $this->category, PDO::PARAM_INT);
            $stmt->bindValue(':payment_method_assigned_to_user_id', $this->payment, PDO::PARAM_INT);
            $stmt->bindValue(':amount', $this->amount, PDO::PARAM_STR);
            $stmt->bindValue(':date_of_expense', $this->date, PDO::PARAM_STR);
            $stmt->bindValue(':expense_comment', $this->comment, PDO::PARAM_STR);

            return $stmt->execute();
        }
        return false;
    }

    public function addCategory()
    {
        if (strlen($this->name) < 3) return "Nazwa kategorii musi zawierać minimum 3 znaki";

        if ($this->existNameCategory() == 'false') {
            $userId = $_SESSION['user_id'];

            $sql = "INSERT INTO expenses_category_assigned_to_userid_$userId (name) VALUES (:categoryName)";

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':categoryName', $this->name, PDO::PARAM_STR);
            $stmt->execute();

            return "Kategoria została dodana ";
        } else {
            return "Kategoria już istnieje";
        }
    }

    public function deleteCategory()
    {
        if ($this->validateCategoryId() == 'true') {

            if (empty($this->isEmptyCategory())) {

                $userId = $_SESSION['user_id'];

                $sql = "DELETE FROM expenses_category_assigned_to_userid_$userId WHERE id = :categoryId";

                $db = static::getDB();
                $stmt = $db->prepare($sql);
                $stmt->bindValue(':categoryId', $this->id, PDO::PARAM_INT);
                $stmt->execute();

                $numberOfDeletedRows = $stmt->rowCount();
                return "Kategoria została usunięta. Usunięto " . $numberOfDeletedRows;
            } else {
                return "Kategoria zawiera przychody. Czy chcesz ją usunąć? ";
            }
        } else {
            return "Nie znaleziono kategorii";
        }
    }

    public function editCategory()
    {
        if (strlen($this->name) < 3) return "Nazwa kategorii musi zawierać minimum 3 znaki";

        if ($this->validateCategoryId() == 'true') {

            if ($this->existNameCategory() == 'false') {

                $userId = $_SESSION['user_id'];

                $sql = "UPDATE expenses_category_assigned_to_userid_$userId SET name = :categoryName WHERE id = :categoryId";

                $db = static::getDB();
                $stmt = $db->prepare($sql);
                $stmt->bindValue(':categoryId', $this->id, PDO::PARAM_INT);
                $stmt->bindValue(':categoryName', $this->name, PDO::PARAM_STR);
                $stmt->execute();

                return "Nazwa kategorii została zmieniona";
            } else {
                return "Kategoria o tej nazwie już istnieje";
            }
        } else {
            return "Nie znaleziono kategorii";
        }
    }

    public function isEmptyCategory()
    {
        $userId = $_SESSION['user_id'];

        $sql = 'SELECT * FROM expenses WHERE user_id = :userId AND expense_category_assigned_to_user_id = :categoryId';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':categoryId', $this->id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    public function validate()
    {
        if (!isset($this->amount) || ($this->amount == '')) {
            $this->isValid['amount'] = 'is-invalid';
            $this->warnings['amount'] = 'Brak kwoty przychodu';
        } else {
            $this->amount = str_replace(",", ".", $this->amount);
            if (!is_numeric($this->amount)) {
                $this->isValid['amount'] = 'is-invalid';
                $this->warnings['amount'] = 'Zły format kwoty';
            }
        }

        if (!isset($this->category)) {
            $this->isValid['category'] = 'is-invalid';
            $this->warnings['category'] = 'Brak wybranej kategorii';
        } else {
            $categories = static::getAllCategory();
            $this->isValid['category'] = 'is-invalid';
            $this->warnings['category'] = 'Wybrana kategoria nie istnieje';
            foreach ($categories as $category) {
                if ($this->category == $category['id']) {
                    unset($this->isValid['category']);
                }
            }
        }

        if (!isset($this->payment)) {
            $this->isValid['payment'] = 'is-invalid';
            $this->warnings['payment'] = 'Brak wybranej metody płatności';
        } else {
            $payments = static::getAllPayments();
            $this->isValid['payment'] = 'is-invalid';
            $this->warnings['payment'] = 'Wybrana metoda płatności nie istnieje';
            foreach ($payments as $payment) {
                if ($this->payment == $payment['id']) {
                    unset($this->isValid['payment']);
                }
            }
        }

        if (!isset($this->date) || ($this->date == '')) {
            $this->isValid['date'] = 'is-invalid';
            $this->warnings['date'] = 'Brak daty przychodu';
        } else {
            if (!$this->validateDate($this->date)) {
                $this->isValid['date'] = 'is-invalid';
                $this->warnings['date'] = 'Prawidłowy format daty to: RRRR-MM-DD, np.: 2019-12-31';
            } else {
                if ($this->date > date('Y-m-d')) {
                    $this->isValid['date'] = 'is-invalid';
                    $this->warnings['date'] = 'Maksymalna data to ' . date('Y-m-d');
                }
            }
        }

        if (!isset($this->comment)) {
            $this->comment = '';
        } else {
            if (!preg_match('/^[a-zA-Z0-9 .,!]*$/', $this->comment)) {
                $this->isValid['comment'] = 'is-invalid';
                $this->warnings['comment'] = 'Dozwolone znaki to: a-z, A-Z, 0-9, spacja, kropka, przecinek, wykrzyknik';
            } else if (strlen($this->comment) > 180) {
                $this->isValid['comment'] = 'is-invalid';
                $this->warnings['comment'] = 'Maksymalna długość komentarza to 180 znaków';
            }
        }
    }

    private function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    private function validateCategoryId()
    {
        $isExist = 'false';
        $elements = static::getAllCategory();
        foreach ($elements as $element) {
            if ($this->id == $element['id']) {
                $isExist = 'true';
            }
        }
        return $isExist;
    }

    private function existNameCategory()
    {
        $isExist = 'false';
        $elements = static::getAllCategory();
        foreach ($elements as $element) {
            if ($this->name == $element['name']) {
                $isExist = 'true';
            }
        }
        return $isExist;
    }

    public static function getAllCategory()
    {
        $userId = $_SESSION['user_id'];
        try {
            $db = static::getDB();
            $sql = "SELECT id, name, expense_limit FROM expenses_category_assigned_to_userid_$userId";
            $stmt = $db->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function getAllPayments()
    {
        $userId = $_SESSION['user_id'];
        try {
            $db = static::getDB();
            $sql = "SELECT id, name FROM payment_methods_assigned_to_userid_$userId";
            $stmt = $db->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}
