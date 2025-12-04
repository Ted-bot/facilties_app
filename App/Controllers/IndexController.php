<?php

namespace App\Controllers;

use App\Plugins\Http\Response as Status;
use App\Plugins\Http\Exceptions;
use App\Plugins\Db\Db;

class IndexController extends BaseController {

    /**
     * this property contains the database instance
     * @var Db
     */
    private Db $db;

    /**
     * Controller function used to test whether the project was set up properly.
     * @return void
     */
    public function test() {
        // Respond with 200 (OK):
        // $this->view('pages/index', ['title' => 'Boss']);
        (new Status\Ok(['message' => 'Hello world!']))->send();
    }

}
