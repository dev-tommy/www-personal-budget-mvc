<?php

namespace App\Models;

use PDO;
use PDOException;

class Income
{
    public static function getAll()
    {
        $host = 'localhost';
        $dbname = 'personalBudget';
        $username = 'root';
        $password = 'mysql';

        try {
            $db = new PDO("mysql:host=$host; dbname=$dbname; charset=utf8",
                          $username, $password);
            $sql = 'SELECT id, name FROM incomes_category_default';
            $stmt = $db->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;

        } catch (PDOException $e) {
            echo $e->getMessage();
        }

    }
}