<?php

namespace App\Models;

use Core\Model;
use PDO;
use PDOException;

class Income extends \Core\Model
{
    public $warnings = [];

    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
}
