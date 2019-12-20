<?php

namespace App\Models;

use Core\Model;
use PDO;
use PDOException;

class Income extends \Core\Model
{
    public static function getAll()
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