<?php

namespace App\Controllers;

use App\Plugins\Http\Response as Status;
use App\Plugins\Http\Exceptions;
use App\Plugins\Db\Db;

class FacilityController extends BaseController {

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
        (new Status\Ok(['message' => 'testing facility controller!']))->send();
    }

    /**
     * Controler function to create a row in the database table with user input
     */
    public function create() {
        if($_SERVER['REQUEST_METHOD'] != 'POST'){
            throw new Exceptions\BadRequest('Only POST method is allowed');
        }

        // Sanitize POST data
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $data = [
            'name' => trim($_POST['name']),
        ];

        // validation data
        $errors = [];
        foreach($data as $key => $value){
            if(empty($value) || $value == ''){
                $errors[$key] = 'please enter ' . $key . '<br>';
            }            
        }
        if(!empty($errors)) throw new Exceptions\BadRequest($errors);

        // $this->db->executeQuery($query, $bind);
    }

}
