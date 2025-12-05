<?php


namespace App\Controllers;

use App\Plugins\Di\Injectable;

class BaseController extends Injectable {

     /**
      * initialize db connection
      */
     public function __construct()
    {
        Global $di;
        $this->db = $di->getShared('db');
    }

    /**
     * sanitize user input
     * @param mixed $dirty
     * @return string
     */
    function sanitize($dirty) {
        return htmlspecialchars(strip_tags($dirty));
    }
}
