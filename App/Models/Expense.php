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

    public static function getAllCategory()
    {
        try {
            $db = static::getDB();
            $sql = 'SELECT id, name FROM expenses_category_default';
            $stmt = $db->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function getAllPayments()
    {
        try {
            $db = static::getDB();
            $sql = 'SELECT id, name FROM payment_methods_default';
            $stmt = $db->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}
