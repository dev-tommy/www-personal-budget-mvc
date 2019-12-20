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

    public function __call($name, $arguments)
    {
        $method = $name . 'Action';
        if (method_exists($this, $method)) {
            call_user_func_array([$this, $method], $arguments);
            $this->after();
        } else {
            throw new \Exception("Method $method not found in controller". get_class($this));
        }
    }

    protected function before()
    {

    }

    protected function after()
    {

    }
 }