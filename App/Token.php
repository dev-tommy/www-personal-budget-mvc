<?php

namespace App;

/**
 * Token generator
 *
 * PHP version 7.3
 *
 * Created with course PHP MVC framework by author Dave Hollingworth
 * e-mail: tomasz.frydrychowicz.programista@gmail.com
 */
class Token
{
    protected $token;

    public function __construct()
    {
        $this->token = bin2hex(random_bytes(16));
    }

    public function getValue()
    {
        return $this->token;
    }

}