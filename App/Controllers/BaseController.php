<?php


namespace App\Controllers;

use App\Plugins\Di\Injectable;

class BaseController extends Injectable {

     public function __construct()
    {
        Global $di;
        $this->db = $di->getShared('db');
    }

    function sanitize($dirty) {
        return htmlspecialchars(strip_tags($dirty));
    }
}
