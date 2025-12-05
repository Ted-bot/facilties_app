<?php

namespace App\Controllers;

use App\Plugins\Http\Response as Status;
use App\Plugins\Http\Exceptions;

class LocationController extends BaseController {

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

        // check request method
        if($_SERVER['REQUEST_METHOD'] != 'POST') throw new Exceptions\BadRequest('Only POST method is allowed');

        // sanitize input
        $sanitized_data = array_map(function($value) {
            return $this->sanitize($value);
        }, $_POST);

        $data = [
            'city' => trim($sanitized_data['city']),
            'address' => trim($sanitized_data['address']),
            'zip_code' => trim($sanitized_data['zip_code']),
            'country_code' => trim($sanitized_data['country_code']),
            'phone_number' => trim($sanitized_data['phone_number']),
        ];

        // validation data
        $errors = [];
        foreach($data as $key => $value){
            if(empty($value)){
                $errors[$key] = 'please enter ' . $key;
            }            
        }
        if(!empty($errors)) throw new Exceptions\BadRequest($errors);

        $query = 'INSERT INTO locations (city, address, zip_code, country_code, phone_number) VALUES(:city, :address, :zip_code, :country_code, :phone_number)';

        $bind = [
            ':city' => $data['city'],
            ':address' => $data['address'],
            ':zip_code' => $data['zip_code'],
            ':country_code' => $data['country_code'],
            ':phone_number' => $data['phone_number'],
        ];

        try {
            if($this->db->executeQuery($query, $bind)) {
                (new Status\Created($data))->send();
                exit();
            }
        } catch (\Exception $e) {
            (new Status\InternalServerError($e->getMessage()))->send();
            exit();
        }
    }
}
