<?php

namespace Core;

/**
* View
*
* PHP version 7.3
*
* Created with course PHP MVC framework by author Dave Hollingworth
* e-mail: tomasz.frydrychowicz.programista@gmail.com
*/

class View
{
    public static function render($view)
    {
        $file = "../App/Views/$view";

        if  (is_readable($file)) {
            require $file;
        } else {
            echo "$file not found";
        }
    }
}