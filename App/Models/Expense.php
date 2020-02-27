<?php

namespace App\Models;

use App\Date;
use Core\Model;
use DateTime;
use PDO;
use PDOException;

class Expense extends \Core\Model
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
            $sql = 'INSERT INTO expenses (user_id, expense_category_assigned_to_user_id, payment_method_assigned_to_user_id, amount, date_of_expense, expense_comment) VALUES (:user_id, :expense_category_assigned_to_user_id, :payment_method_assigned_to_user_id, :amount, :date_of_expense, :expense_comment)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindValue(':expense_category_assigned_to_user_id', $this->category, PDO::PARAM_INT);
            $stmt->bindValue(':payment_method_assigned_to_user_id', $this->payment, PDO::PARAM_INT);
            $stmt->bindValue(':amount', $this->amount, PDO::PARAM_STR);
            $stmt->bindValue(':date_of_expense', $this->date, PDO::PARAM_STR);
            $stmt->bindValue(':expense_comment', $this->comment, PDO::PARAM_STR);

            return $stmt->execute();
        }
        return false;
    }

    public function addCategory()
    {
        if (strlen($this->name) < 3) return "Nazwa kategorii musi zawierać minimum 3 znaki";

        if ($this->existNameCategory() == 'false') {
            $userId = $_SESSION['user_id'];

            if ($this->limit > 0)
            {
                $sql = "INSERT INTO expenses_category_assigned_to_userid_$userId (name, expense_limit) VALUES (:categoryName, :limitValue)";
                $db = static::getDB();
                $stmt = $db->prepare($sql);
                $stmt->bindValue(':limitValue', strval($this->limit), PDO::PARAM_STR);
            } else {
                $sql = "INSERT INTO expenses_category_assigned_to_userid_$userId (name) VALUES (:categoryName)";
                $db = static::getDB();
                $stmt = $db->prepare($sql);
            }

            $stmt->bindValue(':categoryName', $this->name, PDO::PARAM_STR);
            $stmt->execute();

            return "Kategoria została dodana ";
        } else {
            return "Kategoria już istnieje";
        }
    }

    public function addMethod()
    {
        if (strlen($this->name) < 3) return "Nazwa rodzaju platnosci musi zawierać minimum 3 znaki";

        if ($this->existNameMethod() == 'false') {
            $userId = $_SESSION['user_id'];

            $sql = "INSERT INTO payment_methods_assigned_to_userid_$userId (name) VALUES (:methodName)";

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':methodName', $this->name, PDO::PARAM_STR);
            $stmt->execute();

            return "Rodzaj platnosci został dodany ";
        } else {
            return "Rodzaj platnosci już istnieje";
        }
    }

    public function deleteCategory()
    {
        if ($this->validateCategoryId() == 'true') {

            if (empty($this->isEmptyCategory())) {

                $userId = $_SESSION['user_id'];

                $sql = "DELETE FROM expenses_category_assigned_to_userid_$userId WHERE id = :categoryId";

                $db = static::getDB();
                $stmt = $db->prepare($sql);
                $stmt->bindValue(':categoryId', $this->id, PDO::PARAM_INT);
                $stmt->execute();

                return "Kategoria została usunięta";
            } else {
                $this->moveCategoryItems();

                $userId = $_SESSION['user_id'];

                $sql = "DELETE FROM expenses_category_assigned_to_userid_$userId WHERE id = :categoryId";

                $db = static::getDB();
                $stmt = $db->prepare($sql);
                $stmt->bindValue(':categoryId', $this->id, PDO::PARAM_INT);
                $stmt->execute();
                return "Kategoria zawierała wydatki! <br />Zostały one przeniesione do kategorii 'Inne' ";
            }
        } else {
            return "Nie znaleziono kategorii";
        }
    }

    public function moveCategoryItems()
    {
        $userId = $_SESSION['user_id'];
        $otherId = DB::getOtherExpensesCategoryId();
        $sql = "UPDATE expenses SET expense_category_assigned_to_user_id = $otherId WHERE user_id = :userId AND expense_category_assigned_to_user_id = :categoryId";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':categoryId', $this->id, PDO::PARAM_INT);

        $stmt->execute();
    }

    public function moveMethodsItems()
    {
        $userId = $_SESSION['user_id'];
        $otherId = DB::getOtherPaymentMethodId();
        $sql = "UPDATE expenses SET payment_method_assigned_to_user_id = $otherId WHERE user_id = :userId AND payment_method_assigned_to_user_id = :categoryId";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':categoryId', $this->id, PDO::PARAM_INT);

        $stmt->execute();
    }

    public function deleteMethod()
    {
        if ($this->validateMethodId() == 'true') {

            if (empty($this->isEmptyMethod())) {

                $userId = $_SESSION['user_id'];

                $sql = "DELETE FROM payment_methods_assigned_to_userid_$userId WHERE id = :methodId";

                $db = static::getDB();
                $stmt = $db->prepare($sql);
                $stmt->bindValue(':methodId', $this->id, PDO::PARAM_INT);
                $stmt->execute();

                return "Metoda platnosci została usunięta";
            } else {
                $this->moveMethodsItems();

                $userId = $_SESSION['user_id'];

                $sql = "DELETE FROM payment_methods_assigned_to_userid_$userId WHERE id = :methodId";

                $db = static::getDB();
                $stmt = $db->prepare($sql);
                $stmt->bindValue(':methodId', $this->id, PDO::PARAM_INT);
                $stmt->execute();
                return "Zmieniono wybrany sposób płatności wydatków na 'Inne' ";
            }
        } else {
            return "Nie znaleziono metody platnosci!";
        }
    }

    public function editCategory()
    {
        if (strlen($this->name) < 3) return "Nazwa kategorii musi zawierać minimum 3 znaki";
        $userId = $_SESSION['user_id'];
        $answer = "Błąd";
        if ($this->validateCategoryId() == 'true') {
            if ($this->existNameCategory() == 'false') {
                $sql = "UPDATE expenses_category_assigned_to_userid_:userId SET name = :categoryName WHERE id = :categoryId";

                $db = static::getDB();
                $stmt = $db->prepare($sql);
                $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
                $stmt->bindValue(':categoryId', $this->id, PDO::PARAM_INT);
                $stmt->bindValue(':categoryName', $this->name, PDO::PARAM_STR);
                $stmt->execute();

                $answer = "Kategoria została zmieniona";
            } else {
                $answer = "Kategoria o tej nazwie już istnieje";
            }

            if ($this->limitCheckbox === "true") {
                if (empty($this->limit)) {
                    $this->limit = NULL;
                }
                $sql = "UPDATE expenses_category_assigned_to_userid_:userId SET expense_limit = :categoryLimit WHERE id = :categoryId";

                $db = static::getDB();
                $stmt = $db->prepare($sql);
                $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
                $stmt->bindValue(':categoryId', $this->id, PDO::PARAM_INT);
                $stmt->bindValue(':categoryLimit', $this->limit, PDO::PARAM_STR);
                $stmt->execute();
                $answer = "Kategoria została zmieniona";
            }
        } else {
            return "Nie znaleziono kategorii lub jest zabezpieczona przed modyfikacją";
        }
        return $answer;
    }

    public function editMethod()
    {
        if (strlen($this->name) < 3) return "Nazwa rodzaju platnosci musi zawierać minimum 3 znaki";

        if ($this->validateMethodId() == 'true') {

            if ($this->existNameMethod() == 'false') {

                $userId = $_SESSION['user_id'];

                $sql = "UPDATE payment_methods_assigned_to_userid_$userId SET name = :methodName WHERE id = :methodId";

                $db = static::getDB();
                $stmt = $db->prepare($sql);
                $stmt->bindValue(':methodId', $this->id, PDO::PARAM_INT);
                $stmt->bindValue(':methodName', $this->name, PDO::PARAM_STR);
                $stmt->execute();

                return "Nazwa rodzaju platnosci została zmieniona";
            } else {
                return "Rodzaj platnosci o tej nazwie już istnieje";
            }
        } else {
            return "Nie znaleziono sposobu płatności lub jest zabezpieczony przed modyfikacją";
        }
    }

    public function isEmptyCategory()
    {
        $userId = $_SESSION['user_id'];

        $sql = 'SELECT * FROM expenses WHERE user_id = :userId AND expense_category_assigned_to_user_id = :categoryId';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':categoryId', $this->id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    public function isEmptyMethod()
    {
        $userId = $_SESSION['user_id'];

        $sql = 'SELECT * FROM expenses WHERE user_id = :userId AND payment_method_assigned_to_user_id = :methodId';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':methodId', $this->id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
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

        if (!isset($this->payment)) {
            $this->isValid['payment'] = 'is-invalid';
            $this->warnings['payment'] = 'Brak wybranej metody płatności';
        } else {
            $payments = static::getAllPayments();
            $this->isValid['payment'] = 'is-invalid';
            $this->warnings['payment'] = 'Wybrana metoda płatności nie istnieje';
            foreach ($payments as $payment) {
                if ($this->payment == $payment['id']) {
                    unset($this->isValid['payment']);
                }
            }
        }

        if (!isset($this->date) || ($this->date == '')) {
            $this->isValid['date'] = 'is-invalid';
            $this->warnings['date'] = 'Brak daty przychodu';
        } else {
            if (!$this->validateDate($this->date)) {
                $this->isValid['date'] = 'is-invalid';
                $this->warnings['date'] = 'Prawidłowy format daty to: RRRR-MM-DD, np.: 2019-12-31';
            } else {
                if ($this->date > date('Y-m-d')) {
                    $this->isValid['date'] = 'is-invalid';
                    $this->warnings['date'] = 'Maksymalna data to ' . date('Y-m-d');
                }
            }
        }

        if (!isset($this->comment)) {
            $this->comment = '';
        } else {
            if (!preg_match('/^[a-zA-Z0-9 .,!]*$/', $this->comment)) {
                $this->isValid['comment'] = 'is-invalid';
                $this->warnings['comment'] = 'Dozwolone znaki to: a-z, A-Z, 0-9, spacja, kropka, przecinek, wykrzyknik';
            } else if (strlen($this->comment) > 180) {
                $this->isValid['comment'] = 'is-invalid';
                $this->warnings['comment'] = 'Maksymalna długość komentarza to 180 znaków';
            }
        }
    }

    private function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    private function validateCategoryId()
    {
        $isExist = 'false';
        if (DB::getOtherExpensesCategoryId() == $this->id) return $isExist;
        $elements = static::getAllCategory();
        foreach ($elements as $element) {
            if ($this->id == $element['id']) {
                $isExist = 'true';
            }
        }
        return $isExist;
    }

    private function validateMethodId()
    {
        $isExist = 'false';
        if (DB::getOtherPaymentMethodId() == $this->id) return $isExist;
        $elements = static::getAllPayments();
        foreach ($elements as $element) {
            if ($this->id == $element['id']) {
                $isExist = 'true';
            }
        }
        return $isExist;
    }

    private function existNameCategory()
    {
        $isExist = 'false';
        $elements = static::getAllCategory();
        foreach ($elements as $element) {
            if (strtolower($this->name) == strtolower($element['name'])) {
                $isExist = 'true';
            }
        }
        return $isExist;
    }

    private function existNameMethod()
    {
        $isExist = 'false';
        $elements = static::getAllPayments();
        foreach ($elements as $element) {
            if (strtolower($this->name) == strtolower($element['name'])) {
                $isExist = 'true';
            }
        }
        return $isExist;
    }

    public static function getAllCategory()
    {
        $userId = $_SESSION['user_id'];
        try {
            $db = static::getDB();
            $sql = "SELECT id, name, expense_limit FROM expenses_category_assigned_to_userid_$userId";
            $stmt = $db->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function getAllPayments()
    {
        $userId = $_SESSION['user_id'];
        try {
            $db = static::getDB();
            $sql = "SELECT id, name FROM payment_methods_assigned_to_userid_$userId";
            $stmt = $db->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function getTotalMonthlyExpenses($categoryId)
    {

        $startDate = Date::getFirstDayOfCurrentMonth();
        $endDate = Date::getLastDayOfCurrentMonth();
        $id = $_SESSION['user_id'];
        $sql = "
        SELECT
            e_userid.name AS 'Category',
            e_userid.expense_limit AS 'Limit',
            SUM(e.amount) AS 'Sum_of_amounts'
        FROM
            expenses AS e,
            expenses_category_assigned_to_userid_$id AS e_userid
        WHERE
            e_userid.id = e.expense_category_assigned_to_user_id AND
            e.date_of_expense >= '$startDate' AND
            e.date_of_expense <= '$endDate' AND
            e.user_id='$id' AND
            e.expense_category_assigned_to_user_id='$categoryId'
        ";

        $db = static::getDB();
        $stmt = $db->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function getCategoryLimit($categoryId)
    {
        $userId = $_SESSION['user_id'];

        $sql = 'SELECT expense_limit FROM expenses_category_assigned_to_userid_:userId WHERE id = :categoryId';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn(0);
    }

    public static function getCategoryNameLike($containingName)
    {
        $userId = $_SESSION['user_id'];
        $name = "%" . $containingName . "%";
        $sql = 'SELECT name FROM expenses_category_assigned_to_userid_? WHERE LOWER(name) LIKE LOWER(?) LIMIT 5';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $name, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getMethodNameLike($containingName)
    {
        $userId = $_SESSION['user_id'];
        $name = "%" . $containingName . "%";
        $sql = 'SELECT name FROM payment_methods_assigned_to_userid_? WHERE LOWER(name) LIKE LOWER(?) LIMIT 5';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $name, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
