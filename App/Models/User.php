<?php

namespace App\Models;

use PDO;

class User extends \Core\Model
{
    public $warnings = [];

    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    public function save()
    {
        $this->validate();

        if (empty($this->isValid)) {
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
        if (strlen($this->name) < 6) {
            $this->isValid['name'] = 'is-invalid';
            $this->warnings['name'] = 'Nazwa użytkownika jest wymagana i musi mieć min. 6 znków';
            //$this->warnings[] = 'Name is required';
        }

        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->isValid['email'] = 'is-invalid';
            $this->warnings['email'] = 'Niepoprawny adres email';
            //$this->warnings[] = 'Invalid emial';
        }

        if (static::emailExists($this->email)) {
            $this->isValid['email'] = 'is-invalid';
            $this->warnings['email'] = 'Adres email już wcześniej został wykorzystany';
            //$this->warnings[] = 'Email already taken';
        }

        if (preg_match('/.*[a-z]+.*/i', $this->password) == 0) {
            $this->isValid['password'] = 'is-invalid';
            $this->warnings['password'] = 'Hasło musi zawierać przynajmniej jedną literę';
            //$this->warnings[] = 'Password needs at least one letter';
        }

        if (preg_match('/.*\d+.*/i', $this->password) == 0) {
            $this->isValid['password'] = 'is-invalid';
            $this->warnings['password'] = 'Hasło musi zawierać przynajmniej jedną cyfrę';
            //$this->warnings[] = 'Password needs at least one number';
        }

        if (strlen($this->password) < 8) {
            $this->isValid['password'] = 'is-invalid';
            $this->warnings['password'] = 'Hasło musi mieć minimum 8 znaków długości';
            //$this->warnings[] = 'Please enter at least 8 chars for the password';
        }
    }

    public static function emailExists($email)
    {
        return static::findByEmail($email) !== false;
    }

    public static function findByEmail($email)
    {
        $sql = 'SELECT * FROM users WHERE email = :email';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    public static function findByID($id)
    {
        $sql = 'SELECT * FROM users WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    public function rememberLogin()
    {
        $token = new \App\Token();
        $hashed_token = $token->getHash();
        $this->remember_token = $token->getValue();
        $this->expiry_timestamp = time() + 60 * 60 * 24 * 30; // 30days

        $sql = 'INSERT INTO remembered_logins (token_hash, user_id, expires_at)
                VALUES (:token_hash, :user_id, :expires_at)';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $this->id, PDO::PARAM_INT);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $this->expiry_timestamp), PDO::PARAM_STR);

        return $stmt->execute();
    }

    public static function authenticate($email, $password)
    {
        $user = static::findByEmail($email);
        if ($user) {
            if (password_verify($password, $user->password_hash)) {
                return $user;
            }
        }
        return false;
    }
}
