<?php 


$conn = null;
$conn = checkDbConnection();
$val = new Employees($conn);

if(array_key_exists("id",$_GET)){
    $val->employee_aid = $_GET['id'];
    $val->employee_first_name = $data['employee_first_name'];
    $val->employee_middle_name = isset($data['employee_middle_name']) ? $data['employee_middle_name'] : "";
    $val->employee_last_name = $data['employee_last_name'];
    $val->employee_email = $data['employee_email'];
    $val->employee_updated = date("Y-m-d H:m:s");

    $employee_first_name_old = $data['employee_first_name_old'];
    $employee_email_old = $data['employee_email_old'];
    
    // vaidations
    checkId($val->employee_aid);
    compareName($val, $employee_first_name_old, $val->employee_first_name); 
    compareEmail($val, $employee_email_old, $val->employee_email);


    $query = checkUpdate($val);
    http_response_code(200);
    returnSuccess($val, "Employees Update", $query);
}


checkEndpoint();
