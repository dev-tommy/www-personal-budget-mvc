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
        if (strlen($this->name) < 6)
            return "Nazwa uzytkownika musi skladać się z minimum 6 znaków";

        if (DB::editTable("users", "username", $this->name, "str")) {
            return "Nazwa użytkownika została zmieniona";
        } else {
            return "Nazwa użytkownika <B>nie została</B> zmieniona";
        }
    }

    private function editUserEmail()
    {
        if (filter_var($this->name, FILTER_VALIDATE_EMAIL) === false)
            return 'Niepoprawny adres email';
        if (static::emailExists($this->name))
            return "Adres email już zajęty. Proszę wybrać inny.";

        if (DB::editTable("users", "email", $this->name, "str")) {
            return "Adres email został zmieniony";
        } else {
            return "Adres email <B>nie został</B> zmieniony";
        }
    }

    private function editUserPassword()
    {
        $userId = $_SESSION['user_id'];

        if (preg_match('/.*[a-z]+.*/i', $this->name) == 0) {
            return 'Hasło musi zawierać przynajmniej jedną literę';
        }

        if (preg_match('/.*\d+.*/i', $this->name) == 0) {
            return 'Hasło musi zawierać przynajmniej jedną cyfrę';
        }

        if (strlen($this->name) < 8) {
            return 'Hasło musi mieć minimum 8 znaków długości';
        }

        $password_hash = password_hash($this->name, PASSWORD_DEFAULT);

        $sql = "UPDATE users SET password_hash = :userPassword WHERE id = $userId";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':userPassword', $password_hash, PDO::PARAM_STR);
        $stmt->execute();

        return "Hasło zostało zmienione";
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

            $isOk = $stmt->execute();
            $this->id = 0;
            if ($isOk) {
                $isOk = $this->addNewUserTables($db->lastInsertId());
            }
            return $isOk;
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
