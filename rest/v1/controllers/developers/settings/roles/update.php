<?php 


$conn = null;
$conn = checkDbConnection();
$val = new Roles($conn);

if(array_key_exists("id",$_GET)){
    $val->role_aid = $_GET['id'];
    $val->role_name = $data['role_name'];
    $val->role_description = $data['role_description'];
    $val->role_updated = date("Y-m-d H:m:s");

    $role_name_old = $data['role_name_old'];


    
// vaidations
    checkId($val->role_aid);
    compareName($val, $role_name_old, $val->role_name); 


    $query = checkUpdate($val);
    http_response_code(200);
    returnSuccess($val, "Roles Update", $query);
}


checkEndpoint();
