<?php 


$conn = null;
$conn = checkDbConnection();
$val = new Roles($conn);

if(array_key_exists("id",$_GET)){
    $val->role_aid = $_GET['id'];
   


    $query = checkDelete($val);
    http_response_code(200);
    returnSuccess($val, "Roles Delete", $query);
}


checkEndpoint();
