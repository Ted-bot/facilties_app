<?php

namespace App\Controllers;

use App\Plugins\Http\Response as Status;
use App\Plugins\Http\Exceptions;
use App\Plugins\Db\Db;

class TagController extends BaseController {

    /**
     * Controllr function to create a row in the database table with user input
     */
    public function create() {
        if($_SERVER['REQUEST_METHOD'] != 'POST') throw new Exceptions\BadRequest('Only POST method is allowed');

        // sanitize input
        $sanitized_data = $this->sanitize($_POST['name']);

        // print_r($sanitized_data); exit();

        $data = [
            'name' => trim($sanitized_data),
        ];

        // validation data
        if(empty($data['name'])){
            (new Status\BadRequest('please enter name'))->send();
            exit();
        };

        $query = 'INSERT INTO tags (name) VALUES(:name)';

        $bind = [':name' => $data['name']];

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
     * this method creates a relation between tags and facilities in the database
     * @return void
     */
    public function setTagAndFacililityRelation() {

        $sanitized_data = [];
        foreach($_POST as $key => $value){
            $sanitized_data[$key] = $this->sanitize($value);
        }

        $query = 'INSERT INTO facilities_tags (facility_tag_id, tag_id) VALUES(:facility_tag_id, :tag_id)';

        $bind = [
            ':facility_tag_id' => $sanitized_data['facility_tag_id'],
            ':tag_id' => $sanitized_data['tag_id']
        ];
        
        try {
            if($this->db->executeQuery($query, $bind)) {
                (new Status\Created($sanitized_data))->send();
                exit();
            } 
        } catch (\Exception $e) {
            (new Status\InternalServerError($e->getMessage()))->send();
            exit();
        }
    }

}
