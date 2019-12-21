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

}