<?php

namespace Core;

use Core\Log;
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
        View::renderTemplate('error.html',
        [
            'className' => get_class($exception),
            'message' => $exception->getMessage(),
            'stackTrace' => $exception->getTraceAsString(),
            'fileName' => $exception->getFile(),
            'lineNumber' => $exception->getLine()
        ]
        );
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
            Log::addException($exception);
            View::renderTemplate("$code.html");
        }
    }
}
