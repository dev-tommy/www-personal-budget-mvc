<?php

namespace Core;

/**
 * Logger
 *
 * PHP version 7.3
 *
 * e-mail: tomasz.frydrychowicz.programista@gmail.com
 */

class Log
{
    public static function addException($exception)
    {
        $log = dirname(__DIR__) . '/logs/' . date('Y-m-d') . '.txt';
        ini_set('error_log', $log);

        $message = "\n ^---Uncaught exception: '" . get_class($exception) . "'";
        $message .= " with message: '" . $exception->getMessage() . "'";
        $message .= "\nStack trace: " . $exception->getTraceAsString();
        $message .= "\nThrown in '" . $exception->getFile() . "' on line " . $exception->getLine();

        error_log($message);
    }
}