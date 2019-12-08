<?php

namespace App\Controllers;
/**
 * Incomes controller v0.1
 *
 * PHP version 7.3
 *
 * Created with course PHP MVC framework by author Dave Hollingworth
 * e-mail: tomasz.frydrychowicz.programista@gmail.com
 */

class Incomes extends \Core\Controller
{
    protected function before()
    {
        
    }

    protected function after()
    {
        
    }
    
    public function indexAction()
    {
        echo "index page";
    }

    public function addAction()
    {
        echo "Add new income";
    }

    public function editAction()
    {
        echo "Edit income";
    }

    public function deleteAction()
    {
        echo "Delete income";
    }

    public function renameAction()
    {
        echo "Rename income";
    }

    public function viewAction()
    {
        echo "View income";
    }

}


?>