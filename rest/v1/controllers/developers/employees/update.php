<?php
$conn = null;
$conn = checkDbConnection();
$val = new Employees($conn);

if (array_key_exists("id", $_GET)) {
    $val->employee_aid             = $_GET["id"];
    $val->employee_first_name      = trim($data['employee_first_name']);
    $val->employee_middle_name     = trim($data['employee_middle_name'] ?? "");
    $val->employee_last_name       = trim($data['employee_last_name']);
    $val->employee_email           = trim($data['employee_email']);
    $val->employee_department_id   = trim($data['employee_department_id']);
    $val->employee_birthday        = trim($data['employee_birthday'] ?? "");
    $val->employee_start_work_date = trim($data['employee_start_work_date'] ?? "");
    $val->employee_updated         = date("Y-m-d H:i:s");

    $employee_email_old = $data['employee_email_old'];

    checkId($val->employee_aid);
    compareEmail($val, $employee_email_old, $val->employee_email);
    isDepartmentExist($val);

    $query = checkUpdate($val);
    $val->syncSupervisorSnapshot();
    http_response_code(200);
    returnSuccess($val, "Employees Update", $query);
}

checkEndpoint();
