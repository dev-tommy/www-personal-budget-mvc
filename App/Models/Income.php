<?php

namespace App\Models;

use App\Auth;
use Core\Model;
use PDO;
use PDOException;

class Income extends \Core\Model
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
            //$sql = 'INSERT INTO users (username, password_hash, email)
            //    VALUES(:username, :password_hash, :email)';
            $sql = 'INSERT INTO incomes (user_id, income_category_assigned_to_user_id, amount, date_of_income, income_comment) VALUES (:user_id, :income_category_assigned_to_user_id, :amount, :date_of_income, :income_comment)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':income_category_assigned_to_user_id', $this->category, PDO::PARAM_INT);
            $stmt->bindValue(':amount', $this->amount, PDO::PARAM_STR);
            $stmt->bindValue(':date_of_income', $this->date, PDO::PARAM_STR);
            $stmt->bindValue(':income_comment', $this->comment, PDO::PARAM_STR);

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


    }

    public static function getAllCategory()
    {
        try {
            $db = static::getDB();
            $sql = 'SELECT id, name FROM incomes_category_default';
            $stmt = $db->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;

        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}