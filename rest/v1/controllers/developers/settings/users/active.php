<?php

//set http header
require '../../../../core/header.php';
// use needed funcions
require '../../../../core/functions.php';
// use models
require '../../../../models/developers/settings/users/Users.php';
//check database connection
$conn = null;
$conn = checkDBConnection();
// make use of classes to save database
// store model into variable
$val = new Users($conn);
//get payload from frontend
$body = file_get_contents("php://input");
$data = json_decode($body, true);

if(array_key_exists('id',$_GET)){

checkPayload($data);
    $val->users_aid = $_GET['id'];
    $val->users_is_active = trim($data['isActive']);
    $val->users_updated = date("Y-m-d H:m:s");

    // validate is id
    checkId($val->users_aid);

    $query = checkActive($val);
    http_response_code(200);
    returnSuccess($val, "role active", $query);
}
// return 404 if endpoint is not available
checkEndpoint();
