<?php

namespace App\Models;

use PDO;

class DB extends \Core\Model
{
    public static function editTable($table, $column, $value, $datatype) {
        $userId = $_SESSION['user_id'];
        switch($datatype) {
            case "str":
                $param = PDO::PARAM_STR;
            break;
            case "int":
                $param = PDO::PARAM_INT;
            break;
            default:
                $param = PDO::PARAM_NULL;
        }

        $sql = "UPDATE ".$table." SET ".$column." = ? WHERE id = ?";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, $value, $param);
        $stmt->bindValue(2, $userId, PDO::PARAM_INT);

        return $stmt->execute();
    }
}