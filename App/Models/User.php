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

    public function editUser()
    {
        switch ($this->id)
        {
            case 1:
                return $this->editUserName();
            case 2:
                return $this->editUserEmail();
            case 3:
                return $this->editUserPassword();
            default:
                return "Błędne ID edytowanego pola!";
        }
    }

    private function editUserName()
    {
        if (Validator::checkLength($this->name,6)) {
            return "Nazwa uzytkownika musi skladać się z minimum 6 znaków";
        }
        if (DB::editTable("users", "username", $this->name, "str")) {
            return "Nazwa użytkownika została zmieniona";
        } else {
            return "Nazwa użytkownika <B>nie została</B> zmieniona";
        }
    }

    private function editUserEmail()
    {
        if (!Validator::checkEmailFormat($this->name)) {
            return 'Niepoprawny adres email';
        }
        if (static::emailExists($this->name)) {
            return "Adres email już zajęty. Proszę wybrać inny.";
        }
        if (DB::editTable("users", "email", $this->name, "str")) {
            return "Adres email został zmieniony";
        } else {
            return "Adres email <B>nie został</B> zmieniony";
        }
    }

    private function editUserPassword()
    {
        if (!Validator::containLetter($this->name)) {
            return 'Hasło musi zawierać przynajmniej jedną literę';
        }

        if (!Validator::containNumber($this->name)) {
            return 'Hasło musi zawierać przynajmniej jedną cyfrę';
        }

        if (Validator::checkLength($this->name, 8)) {
            return 'Hasło musi mieć minimum 8 znaków długości';
        }

        $password_hash = password_hash($this->name, PASSWORD_DEFAULT);

        if (DB::editTable("users", "password_hash", $password_hash, "str")) {
            return "Hasło zostało zmienione";
        } else {
            return "Hasło <B>nie zostało</B> zmienione";
        }
    }

    public function save()
    {
        $this->validateSaveData();

        if (empty($this->isValid)) {
            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

            $sql = 'INSERT INTO users (username, password_hash, email)
                VALUES(:username, :password_hash, :email)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':username', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);

            $isOk = $stmt->execute();
            $this->id = 0;
            if ($isOk) {
                $isOk = $this->addNewUserTables($db->lastInsertId());
            }
            return $isOk;
        }

        return false;
    }

    public function validateSaveData()
    {
        if (Validator::checkLength($this->name, 6)) {
            $this->isValid['name'] = 'is-invalid';
            $this->warnings['name'] = 'Nazwa użytkownika jest wymagana i musi mieć min. 6 znków';
        }

        if (!Validator::checkEmailFormat($this->email)) {
            $this->isValid['email'] = 'is-invalid';
            $this->warnings['email'] = 'Niepoprawny adres email';
        }

        if (static::emailExists($this->email)) {
            $this->isValid['email'] = 'is-invalid';
            $this->warnings['email'] = 'Adres email już wcześniej został wykorzystany';
        }

        if (!Validator::containLetter($this->password)) {
            $this->isValid['password'] = 'is-invalid';
            $this->warnings['password'] = 'Hasło musi zawierać przynajmniej jedną literę';
        }

        if (!Validator::containNumber($this->password)) {
            $this->isValid['password'] = 'is-invalid';
            $this->warnings['password'] = 'Hasło musi zawierać przynajmniej jedną cyfrę';
        }

        if (Validator::checkLength($this->password, 8)) {
            $this->isValid['password'] = 'is-invalid';
            $this->warnings['password'] = 'Hasło musi mieć minimum 8 znaków długości';
        }
    }

    public static function emailExists($email)
    {
        return static::findByEmail($email) !== false;
    }

    public static function findByEmail($email)
    {
        return DB::findInTable('users', 'email', $email, 'str');
    }

    public static function findByID($id)
    {
        return DB::findInTable('users', 'id', $id, 'int');
    }

    public function addNewUserTables($userId)
    {
        $db = static::getDB();
        $isOk = true;

        if (!$this->createIncomesTable($db, $userId)) $isOk = false;
        if (!$this->cloneIncomesTable($db, $userId)) $isOk = false;

        if (!$this->createExpensesTable($db, $userId)) $isOk = false;
        if (!$this->cloneExpensesTable($db, $userId)) $isOk = false;

        if (!$this->createPaymentTable($db, $userId)) $isOk = false;
        if (!$this->clonePaymentTable($db, $userId)) $isOk = false;

        return $isOk;
    }

    public function createIncomesTable($db, $userId)
    {
        $sqlCreateIncomesTable = "CREATE TABLE incomes_category_assigned_to_userid_$userId LIKE incomes_category_default";
        $stmt = $db->prepare($sqlCreateIncomesTable);
        return $stmt->execute();
    }

    public function cloneIncomesTable($db, $userId)
    {
        $sqlCloneIncomesTable = "INSERT INTO incomes_category_assigned_to_userid_$userId SELECT * FROM incomes_category_default";
        $stmt = $db->prepare($sqlCloneIncomesTable);
        return $stmt->execute();
    }

    public function createExpensesTable($db, $userId)
    {
        $sqlCreateExpensesTable = "CREATE TABLE expenses_category_assigned_to_userid_$userId LIKE expenses_category_default";
        $stmt = $db->prepare($sqlCreateExpensesTable);
        return $stmt->execute();
    }

    public function cloneExpensesTable($db, $userId)
    {
        $sqlCloneExpensesTable = "INSERT INTO expenses_category_assigned_to_userid_$userId SELECT * FROM expenses_category_default";
        $stmt = $db->prepare($sqlCloneExpensesTable);
        return $stmt->execute();
    }

    public function createPaymentTable($db, $userId)
    {
        $sqlCreatePaymentTable = "CREATE TABLE payment_methods_assigned_to_userid_$userId LIKE payment_methods_default";
        $stmt = $db->prepare($sqlCreatePaymentTable);
        return $stmt->execute();
    }

    public function clonePaymentTable($db, $userId)
    {
        $sqlClonePaymentTable = "INSERT INTO payment_methods_assigned_to_userid_$userId SELECT * FROM payment_methods_default";
        $stmt = $db->prepare($sqlClonePaymentTable);
        return $stmt->execute();
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
