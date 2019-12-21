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
        $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

        $sql = 'INSERT INTO users (username, email, password
                VALUES (:username, :email, :password)';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':username', $this->name, PDO::PARAM_STR);
        $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
        $stmt->bindValue(':password', $this->password_hash, PDO::PARAM_STR);

        $stmt->execute();
    }
}