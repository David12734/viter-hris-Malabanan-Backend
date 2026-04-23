<?php

$conn = null;
$conn = checkDbConnection();
$val = new Department($conn);

if (array_key_exists("id", $_GET)) {
    $val->department_aid = $_GET['id'];

    $query = checkDelete($val);
    http_response_code(200);
    returnSuccess($val, "Department Delete", $query);
}

checkEndpoint();