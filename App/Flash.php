<?php

namespace App;

/**
 * Flash notifiaction messages
 *
 * PHP version 7.3
 *
 * Created with course PHP MVC framework by author Dave Hollingworth
 * e-mail: tomasz.frydrychowicz.programista@gmail.com
 */

class Flash
{
    public static function addMessage($message)
    {
        if (! isset($_SESSION['flash_notifocations'])) {
            $_SESSION['flash_notifications'] = [];
        }
        $_SESSION['flash_notifications'][] = $message;
    }
}