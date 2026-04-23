<?php

// set http header
require '../../../../core/header.php';
// use needed functions
require '../../../../core/functions.php';
// use models
require '../../../../models/developers/settings/department/Department.php';

$body = file_get_contents("php://input");
$data = json_decode($body, true);

if (isset($_SERVER['HTTP_AUTHORIZATION'])) {

    if (array_key_exists('id', $_GET)) {
        checkPayload($data);
        $conn = null;
        $conn = checkDBConnection();
        $val  = new Department($conn);

        $val->department_aid       = $_GET['id'];
        $val->department_is_active = trim($data['isActive']);
        $val->department_updated   = date("Y-m-d H:i:s");

        checkId($val->department_aid);

        $query = checkActive($val);
        http_response_code(200);
        returnSuccess($val, "department active", $query);
    }
}

checkAccess();