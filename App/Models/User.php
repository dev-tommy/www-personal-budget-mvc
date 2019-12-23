<?php

namespace App\Models;

use PDO;

class User extends \Core\Model
{
    public $errors = [];

    public function __construct($data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    public function save()
    {
        $this->validate();

        if (empty($this->errors)) {
            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

            $sql = 'INSERT INTO users (username, password_hash, email)
                VALUES(:username, :password_hash, :email)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':username', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }

    public function validate()
    {
        if ($this->name == '') {
            $this->errors[] = 'Name is required';
        }

        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors[] = 'Invalid emial';
        }

        if ($this->static::emailExists($this->email)) {
            $this->errors[] = 'Email already taken';
        }

        if ($this->password != $this->password_confirmation) {
            $this->errors[] = 'Password must match confirmation';
        }

        if (strlen($this->password) < 8) {
            $this->errors[] = 'Please enter at least 8 chars for the password';
        }

        if (preg_match('/.*[a-z]+.*/i', $this->password) == 0) {
            $this->errors[] = 'Password needs at least one letter';
        }

        if (preg_match('/.*\d+.*/i', $this->password) == 0) {
            $this->errors[] = 'Password needs at least one number';
        }
    }

    public static function emailExists($email)
    {
        $sql = 'SELECT * FROM users WHERE email = :email';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':email', $email, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch() !== false;
    }
}
