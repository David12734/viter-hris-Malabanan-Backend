<?php

$conn = null;
$conn = checkDBConnection();
$val = new DirectReport($conn);

if (array_key_exists("id", $_GET)) {
    $val->direct_report_aid = $_GET["id"];
    $val->direct_report_subordinate_id = trim($data["direct_report_subordinate_id"] ?? "");
    $val->direct_report_supervisor_id = trim($data["direct_report_supervisor_id"] ?? "");
    $val->direct_report_updated = date("Y-m-d H:i:s");

    checkId($val->direct_report_aid);
    $oldRecord = $val->readById()->fetch();
    validateDirectReport($val);

    $supervisor = $val->readEmployeeById($val->direct_report_supervisor_id)->fetch();
    $query = checkUpdate($val);

    if ($oldRecord && $oldRecord["direct_report_subordinate_id"] != $val->direct_report_subordinate_id) {
        clearEmployeeSupervisor($conn, $oldRecord["direct_report_subordinate_id"]);
    }
    syncEmployeeSupervisor($conn, $val->direct_report_subordinate_id, $supervisor);

    http_response_code(200);
    returnSuccess($val, "Direct Report Update", $query);
}

checkEndpoint();
