<?php 


$conn = null;
$conn = checkDbConnection();
$val = new Users($conn);

if(array_key_exists("id",$_GET)){
    $val->users_aid = $_GET['id'];
   


    $query = checkDelete($val);
    http_response_code(200);
    returnSuccess($val, "Roles Delete", $query);
}


checkEndpoint();
