<?php

// set http header
require '../../../../core/header.php';
// use needed functions
require '../../../../core/functions.php';
// use models
require '../../../../models/developers/settings/department/Department.php';

// get payload from frontend
$body = file_get_contents("php://input");
$data = json_decode($body, true);

if (isset($_SERVER['HTTP_AUTHORIZATION'])) {

    // CREATE / POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $result = require 'create.php';
        sendResponse($result);
        exit;
    }

    // READ / GET — return all departments (used for dropdowns)
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $conn = null;
        $conn = checkDBConnection();
        $val  = new Department($conn);
        $val->department_is_active = "";
        $val->search = "";
        $query = checkReadAll($val);
        http_response_code(200);
        getQueriedData($query);
        exit;
    }

    // UPDATE / PUT
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $result = require 'update.php';
        sendResponse($result);
        exit;
    }

    // DELETE / DELETE
    if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        $result = require 'delete.php';
        sendResponse($result);
        exit;
    }
}

// forbidden if no auth
checkAccess();