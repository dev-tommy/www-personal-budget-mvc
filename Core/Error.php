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
    const PAGE_NOT_FOUND = 404;
    const SERVER_ERROR = 500;

    private static function showDescription($exception)
    {
        echo "<h1>Fatal error</h1>";
        echo "<p>Uncaught exception: '" . get_class($exception) . "'</p>";
        echo "<p>Message: '" . $exception->getMessage() . "'</p>";
        echo "<p>Stack trace:<pre>" . $exception->getTraceAsString() . "</pre></p>";
        echo "<p>Thrown in '" . $exception->getFile() . "' on line " . $exception->getLine() . "</p>";
    }

    private static function addExceptionToLog($exception)
    {
        $log = dirname(__DIR__) . '/logs/' . date('Y-m-d') . '.txt';
        ini_set('error_log', $log);

        $message = "Uncaught exception: '" . get_class($exception) . "'";
        $message .= " with message: '" . $exception->getMessage() . "'";
        $message .= "\nStack trace: " . $exception->getTraceAsString();
        $message .= "\nThrown in '" . $exception->getFile() . "' on line " . $exception->getLine();

        error_log($message);
    }

    private static function getCorrectHttpResponseCode($exception)
    {
        $code = $exception->getCode();
        if ($code != Error::PAGE_NOT_FOUND) {
            $code = Error::SERVER_ERROR;
        }
        http_response_code($code);
        return $code;
    }

    public static function errorHandler($level, $message, $file, $line)
    {
        if (error_reporting() != 0) {
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }

    public static function exceptionHandler($exception)
    {
        $code = Error::getCorrectHttpResponseCode($exception);

        if (\App\Config::SHOW_ERRORS) {
            Error::showDescription($exception);
        } else {
            //Error::addExceptionToLog($exception);
            View::renderTemplate("$code.html");
        }
    }
}
