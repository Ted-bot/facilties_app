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
     * this method creates a row in the database table with user input
     * @throws Exceptions\BadRequest
     * @return void
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

    /**
     * getOneFacility - MUST have a (int) param for query input e.g. localhost/facility/1
     * this method gets one facility from the database with related tags and location info
     * @param mixed $id
     * @return never
     */
    public function getOneFacility($id) {
        if($_SERVER['REQUEST_METHOD'] != 'GET') throw new Exceptions\BadRequest('Only GET method is allowed');

        try {
            // $this->db->executeQuery('SELECT * FROM facilities WHERE id = :id ', [':id' => $id]);
            $this->db->executeQuery('SELECT facilities.id AS id,facilities.name as facility_name,  facilities_tags.facility_tag_id, facilities.location_id, facilities.created, GROUP_CONCAT(facilities_tags.tag_id) AS tag_ids, GROUP_CONCAT(tags.name) as tag_names, locations.city, locations.address, locations.zip_code, locations.country_code, locations.phone_number
                FROM facilities 
                LEFT JOIN facilities_tags ON facilities.tag_id = facilities_tags.facility_tag_id
                LEFT JOIN tags ON facilities_tags.tag_id = tags.id
                LEFT JOIN locations ON facilities.location_id = locations.id
                WHERE facilities.id = :id
            GROUP BY facilities.id, facilities_tags.facility_tag_id', [':id' => $id]);
             $stmt = $this->db->getStatement();
             $result = $stmt->fetch(PDO::FETCH_ASSOC);
            (new Status\Ok($result))->send();
            exit();
        } catch (\Exception $e) {
            (new Status\InternalServerError($e->getMessage()))->send();
            exit();
        }
    }

    /**
     * update one facility with given facility_id and tag_id params e.g. /facilities/update/1/2
     * @param mixed $facility_id
     * @param mixed $tag_id
     * @throws Exceptions\BadRequest
     * @return never
     */
    public function updateOneFacility($facility_id, $tag_id = null) {
        if($_SERVER['REQUEST_METHOD'] != 'POST') throw new Exceptions\BadRequest('Only POST method is allowed');

        // sanitize input
        $sanitized_data = array_map(function($value) {
            return $this->sanitize($value);
        }, $_POST);

        $data = [
            'facility_name' => trim($sanitized_data['facility_name']),
            'tag_name' => trim($sanitized_data['tag_name']),
        ];

        try {
            $this->db->executeQuery('UPDATE facilities , tags
                SET
                    facilities.name = :facility_name,
                    tags.name = :tag_name
                WHERE facilities.id = :facility_id AND tags.id = :tag_id;', [
                ':facility_id' => $facility_id,
                ':facility_name' => $data['facility_name'],
                ':tag_id' => $tag_id,
                ':tag_name' => $data['tag_name'],
            ]);

            $this->db->executeQuery('SELECT facilities.id AS id,facilities.name as facility_name,  facilities_tags.facility_tag_id, facilities.location_id, facilities.created, GROUP_CONCAT(facilities_tags.tag_id) AS tag_ids, GROUP_CONCAT(tags.name) as tag_names, locations.city, locations.address, locations.zip_code, locations.country_code, locations.phone_number
                FROM facilities 
                LEFT JOIN facilities_tags ON facilities.tag_id = facilities_tags.facility_tag_id
                LEFT JOIN tags ON facilities_tags.tag_id = tags.id
                LEFT JOIN locations ON facilities.location_id = locations.id
                WHERE facilities.id = :id
            GROUP BY facilities.id, facilities_tags.facility_tag_id', [':id' => $facility_id]);

             $stmt = $this->db->getStatement();
             $result = $stmt->fetch(PDO::FETCH_ASSOC);
            (new Status\Ok($result))->send();
            exit();
        } catch (\Exception $e) {
            (new Status\InternalServerError($e->getMessage()))->send();
            exit();
        }
    }

    /**
     * this method deletes rows related to specified facility
     * @param mixed $id
     * @throws Exceptions\BadRequest
     * @return never
     */
    public function deleteOneFacility($id) {
        if($_SERVER['REQUEST_METHOD'] != 'DELETE') throw new Exceptions\BadRequest('Only DELETE method is allowed');

        try {
            $this->db->executeQuery('DELETE facilities, tags, facilities_tags FROM facilities
            LEFT JOIN facilities_tags ON facilities_tags.facility_tag_id = facilities.tag_id
            LEFT JOIN tags ON facilities_tags.tag_id = tags.id
            WHERE facilities.id = :id', [':id' => $id]);
            (new Status\Ok(['message' => 'Facility: ' . $id . ' deleted successfully']))->send();
            exit();
        } catch (\Exception $e) {
            (new Status\InternalServerError($e->getMessage()))->send();
            exit();
        }
    }

    /**
     * This method searches for facilities based on a search term
     * @param mixed $search
     * @return void
     */
    public function searchForFacilities($search){
        if($_SERVER['REQUEST_METHOD'] != 'GET') throw new Exceptions\BadRequest('Only GET method is allowed');

        try {
            $this->db->executeQuery('SELECT facilities.id, facilities.name, GROUP_CONCAT(tags.name) AS tag_names, locations.city, locations.address, locations.phone_number, locations.country_code FROM facilities
            LEFT JOIN facilities_tags ON facilities_tags.facility_tag_id = facilities.tag_id
            LEFT JOIN tags ON facilities_tags.tag_id = tags.id
            LEFT JOIN locations ON facilities.location_id = locations.id
            WHERE facilities.name LIKE :search OR tags.name LIKE :search OR locations.city LIKE :search
            GROUP BY facilities.id, facilities_tags.facility_tag_id
            ORDER BY facilities.id ASC
            ', [':search' => '%' . $search . '%']);
             $stmt = $this->db->getStatement();
             $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            (new Status\Ok($result))->send();
            exit();
        } catch (\Exception $e) {
            (new Status\InternalServerError($e->getMessage()))->send();
            exit();
        }
    }

    /**
     * getAllFacilities - get all facilities from the database with related tags and location info
     * @return never
     */
    public function getAllFacilities() {
        if($_SERVER['REQUEST_METHOD'] != 'GET') throw new Exceptions\BadRequest('Only GET method is allowed');

        try {
            $this->db->executeQuery('SELECT facilities.id AS id,facilities.name as facility_name,  facilities_tags.facility_tag_id, facilities.location_id, facilities.created, GROUP_CONCAT(facilities_tags.tag_id) AS tag_ids, GROUP_CONCAT(tags.name) as tag_names, locations.city, locations.address, locations.zip_code, locations.country_code, locations.phone_number
                FROM facilities
                LEFT JOIN facilities_tags ON facilities.tag_id = facilities_tags.facility_tag_id
                LEFT JOIN tags ON facilities_tags.tag_id = tags.id
                LEFT JOIN locations ON facilities.location_id = locations.id
            GROUP BY facilities.id, facilities_tags.facility_tag_id
            ORDER BY facilities.id ASC
            ');
             $stmt = $this->db->getStatement();
             $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            (new Status\Ok($result))->send();
            exit();
        } catch (\Exception $e) {
            (new Status\InternalServerError($e->getMessage()))->send();
            exit();
        }
    }

}
