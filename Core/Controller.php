<?php

namespace Core;

/**
 * Base controller
 *
 * PHP version 7.3
 *
 * Created with course PHP MVC framework by author Dave Hollingworth
 * e-mail: tomasz.frydrychowicz.programista@gmail.com
 */

 abstract class Controller
 {
    protected $route_params = [];

    public function __construct($route_params)
    {
        $this->route_params = $route_params;
    }
 }