<?php


namespace App\Controllers;

use App\Plugins\Di\Injectable;

class BaseController extends Injectable {
    function sanitize($dirty) {
        return htmlspecialchars(strip_tags($dirty));
    }
}
