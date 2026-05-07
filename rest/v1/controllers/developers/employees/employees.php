<?php

//set http header
require '../../../core/header.php';
// use needed funcions
require '../../../core/functions.php';
// use models
require '../../../models/developers/employees/Employees.php';

//get payload from frontend
$body = file_get_contents("php://input");
$data = json_decode($body, true);

//CREATE / POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $result = require 'create.php';
    sendResponse($result);
    exit;
}

//READ / GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $conn = null;
    $conn = checkDBConnection();
    $val = new Employees($conn);
    $val->employee_is_active = "";
    $val->search = "";
    $query = checkReadAll($val);
    http_response_code(200);
    getQueriedData($query);
    exit;
}


//Update / PUT
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    $result = require 'update.php';
    sendResponse($result);
    exit;
}
//Delete / DELETE
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    $result = require 'delete.php';
    sendResponse($result);
    exit;
}
