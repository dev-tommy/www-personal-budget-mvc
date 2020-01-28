<?php

namespace Core;

use App\Config;
use PDO;
use PDOException;

abstract class Model
{
    protected static function getDB()
    {
        static $db = null;
        if ($db === null) {

            try {
                $dsn = 'mysql:host='.Config::DB_HOST.'; dbname='.Config::DB_NAME.'; charset=utf8';
                $db = new PDO(
                    $dsn,
                    Config::DB_USER,
                    Config::DB_PASSWORD
                );
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                Log::addException($e);
                //echo $e->getMessage();
            }
        }
        return $db;
    }
}