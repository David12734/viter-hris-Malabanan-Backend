<?php

$conn = null;
$conn = checkDBConnection();
$val = new DirectReport($conn);

$val->direct_report_is_active = 1;
$val->direct_report_subordinate_id = trim($data["direct_report_subordinate_id"] ?? "");
$val->direct_report_supervisor_id = trim($data["direct_report_supervisor_id"] ?? "");
$val->direct_report_created = date("Y-m-d H:i:s");
$val->direct_report_updated = date("Y-m-d H:i:s");

validateDirectReport($val);

$supervisor = $val->readEmployeeById($val->direct_report_supervisor_id)->fetch();
$query = checkCreate($val);
syncEmployeeSupervisor($conn, $val->direct_report_subordinate_id, $supervisor);

http_response_code(200);
returnSuccess($val, "Direct Report Create", $query);
