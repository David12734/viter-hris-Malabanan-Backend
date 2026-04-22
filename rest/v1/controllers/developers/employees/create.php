<?php 

// check database connection 
$conn = null;
$conn = checkDbConnection();
// make use of classes to save database
$val = new Employees($conn);
$val->employee_is_active = 1;
$val->employee_first_name = trim($data['employee_first_name']);
$val->employee_middle_name = isset($data['employee_middle_name']) ? trim($data['employee_middle_name']) : "";
$val->employee_last_name = trim($data['employee_last_name']);
$val->employee_email = trim($data['employee_email']);
$val->employee_created = date('Y-m-d H:m:s');
$val->employee_updated = date('Y-m-d H:m:s');



isEmailExist($val, $val->employee_email);

$query = checkCreate($val);
http_response_code(200);
returnSuccess($val, "Employees Create", $query);
