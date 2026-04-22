<?php 


$conn = null;
$conn = checkDbConnection();
$val = new Employees($conn);

if(array_key_exists("id",$_GET)){
    $val->employee_aid = $_GET['id'];
   


    $query = checkDelete($val);
    http_response_code(200);
    returnSuccess($val, "Employees Delete", $query);
}


checkEndpoint();
