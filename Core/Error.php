<?php

namespace Core;

/**
 * Base model
 *
 * PHP version 7.3
 *
 * Created with course PHP MVC framework by author Dave Hollingworth
 * e-mail: tomasz.frydrychowicz.programista@gmail.com
 */

class Error
{
    public static function errorHandler($level, $message, $file, $line)
    {
        if (error_reporting() != 0) {
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }

    public static function exceptionHandler($exception)
    {
        if (\App\Config::SHOW_ERRORS) {


        echo "<h1>Fatal error</h1>";
        echo "<p>Uncaught exception: '".get_class($exception)."'</p>";
        echo "<p>Message: '".$exception->getMessage()."'</p>";
        echo "<p>Stack trace:<pre>".$exception->getTraceAsString()."</pre></p>";
        echo "<p>Thrown in '".$exception->getFile()."' on line ".$exception->getLine()."</p>";
        } else {
            $log = dirname(__DIR__) . '/logs/' . date('Y-m-d') . '.txt';
            ini_set('error_log', $log);

            $message = "Uncaught exception: '" . get_class($exception) . "'";
            $message .= " with message: '" . $exception->getMessage() . "'";
            $message .= "\nStack trace: " . $exception->getTraceAsString();
            $message .= "\nThrown in '" . $exception->getFile() . "' on line " . $exception->getLine();

            error_log($message);
            echo "<h1>An error occured</h1>";
        }

    }
}