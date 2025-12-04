<?php

namespace App\Controllers;

use App\Plugins\Http\Response as Status;
use App\Plugins\Http\Exceptions;
use App\Plugins\Db\Db;
use App\Plugins\Db\Connection\Mysql;

class LocationController extends BaseController {

    // /**
    //  * this property contains the database instance
    //  * @var Db
    //  */
    // private DB $db;

     public function __construct()
    {
        Global $di;
        $this->db = $di->getShared('db');
    }

    /**
     * Controller function used to test whether the project was set up properly.
     * @return void
     */
    public function test() {
        // Respond with 200 (OK):
        // $this->view('pages/index', ['title' => 'Boss']);
        (new Status\Ok(['message' => 'Hello world!']))->send();
    }

    /**
     * Controler function to create a row in the database table with user input
     * @return void
     */
    public function create() {
        if($_SERVER['REQUEST_METHOD'] != 'POST'){
            throw new Exceptions\BadRequest('Only POST method is allowed');
        }

        // print_r($_POST);
        // die();

        $_POST = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);

        $data = [
            'city' => trim($_POST['city']),
            'address' => trim($_POST['address']),
            'zip_code' => trim($_POST['zip_code']),
            'country_code' => trim($_POST['country_code']),
            'phone_number' => trim($_POST['phone_number']),
        ];

        // validation data
        $errors = [];
        foreach($data as $key => $value){
            if(empty($value) || $value == ''){
                $errors[$key] = 'please enter ' . $key . ' ';
            }            
        }
        if(!empty($errors)) throw new Exceptions\BadRequest($errors);

        // sanitize input
        // foreach($data as $key => $value){
        // }

        // foreach($_POST as $key => $value){
        //     echo $value . '<br>';
        // }
        // die();

        $query = 'INSERT INTO locations (city, address, zip_code, country_code, phone_number) VALUES(:city, :address, :zip_code, :country_code, :phone_number)';

        $bind = [
            ':city' => $data['city'],
            ':address' => $data['address'],
            ':zip_code' => $data['zip_code'],
            ':country_code' => $data['country_code'],
            ':phone_number' => $data['phone_number'],
        ];


        // print_r($this->db);
        // die();
        if($this->db->executeQuery($query, $bind)) {
            (new Status\Created(['message' => 'Location created successfully']))->send();
        } else {
            throw new Exceptions\InternalServerError('Something went wrong, please try again later');
        }
    }

}
