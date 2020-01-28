<?php

namespace App;

class Flash
{
    public static function addMessage($message)
    {
        if (! isset($_SESSION['flash_notifications'])) {
            $_SESSION['flash_notifications'] = [];
        }
        $_SESSION['flash_notifications'][] = $message;
    }

    public static function getMessage()
    {
        if (isset($_SESSION['flash_notifications'])) {
            $message = $_SESSION['flash_notifications'];
            unset($_SESSION['flash_notifications']);
            return $message;
        }
    }
}