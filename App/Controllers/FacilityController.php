<?php

namespace App\Controllers;

use App\Plugins\Http\Response as Status;
use App\Plugins\Http\Exceptions;
use PDO;
use App\Plugins\Db\Db;

class FacilityController extends BaseController {

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
     * Controller function to create a row in the database table with user input
     */
    public function create() {
        if($_SERVER['REQUEST_METHOD'] != 'POST') throw new Exceptions\BadRequest('Only POST method is allowed');

        // sanitize input
        $sanitized_data = array_map(function($value) {
            return $this->sanitize($value);
        }, $_POST);

        $data = [
            'name' => trim($sanitized_data['name']),
            'location_id' => trim($sanitized_data['location_id']),
            'tag_id' => trim($sanitized_data['tag_id']),
        ];

        // validation data
        $errors = [];
        foreach($data as $key => $value){
            if($key == 'tag_id' && $value == ''){ 
                $data['tag_id'] = null;
                continue;
            };

            if($key != 'tag_id' && empty($value)){
                $errors[$key] = 'please enter ' . $key;
            }            
        }
        if(!empty($errors)){
            (new Status\BadRequest($errors))->send();
            exit();
        };

        $query = 'INSERT INTO facilities (name, location_id, tag_id) VALUES(:name, :location_id, :tag_id)';

        $bind = [
            ':name' => $data['name'],
            ':location_id' => $data['location_id'],
            ':tag_id' => $data['tag_id']
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

    public function getAllFacilities() {
        try {
            $this->db->executeQuery('SELECT facilities.id, facilities.location_id, facilities.created, facilities_tags.tag_id, facilities_tags.facility_tag_id, facilities.name as facility_name, tags.name as tag_name, locations.city, locations.address, locations.zip_code, locations.country_code, locations.phone_number
                FROM facilities 
                LEFT JOIN facilities_tags ON facilities.tag_id = facilities_tags.facility_tag_id
                LEFT JOIN tags ON facilities_tags.tag_id = tags.id
                LEFT JOIN locations ON facilities.location_id = locations.id
            ');
             $stmt = $this->db->getStatement();
             $result = $stmt->fetch(PDO::FETCH_ASSOC);
            (new Status\Ok($result))->send();
            exit();
        } catch (\Exception $e) {
            (new Status\InternalServerError($e->getMessage()))->send();
            exit();
        }
    }

}
