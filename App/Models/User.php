<?php

namespace App\Models;

use PDO;

class User extends \Core\Model
{
    public function __construct($data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    public function save()
    {
        $sql = 'INSERT INTO users (name, email, password_hash
                VALUES (:name, :email, :password_hash)';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
        $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
        $stmt->bindValue(':password_hash', $this->password, PDO::PARAM_STR);

        $stmt->execute();
    }
}