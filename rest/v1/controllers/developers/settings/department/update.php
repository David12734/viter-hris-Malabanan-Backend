<?php

$conn = null;
$conn = checkDbConnection();
$val = new Department($conn);

if (array_key_exists("id", $_GET)) {
    $val->department_aid     = $_GET['id'];
    $val->department_name    = trim($data['department_name']);
    $val->department_updated = date('Y-m-d H:i:s');

    $department_name_old = $data['department_name_old'];

    checkId($val->department_aid);
    compareName($val, $department_name_old, $val->department_name);

    $query = checkUpdate($val);
    http_response_code(200);
    returnSuccess($val, "Department Update", $query);
}

checkEndpoint();