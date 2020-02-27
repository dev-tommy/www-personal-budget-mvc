<?php

namespace App\Models;

use PDO;

class Validator extends \Core\Model
{
    public static function checkLength($str,$length) {
        return strlen($str) < $length;
    }

    public static function checkEmailFormat($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function containLetter($str)
    {
        return preg_match('/.*[a-z]+.*/i', $str);
    }

    public static function containNumber($str)
    {
        return preg_match('/.*\d+.*/i', $str);
    }
}
