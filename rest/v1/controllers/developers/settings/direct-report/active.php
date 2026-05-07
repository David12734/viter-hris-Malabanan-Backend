<?php

require '../../../../core/header.php';
require '../../../../core/functions.php';
require '../../../../models/developers/settings/direct-report/DirectReport.php';
require '../../../../models/developers/employees/Employees.php';
require 'helpers.php';

$body = file_get_contents("php://input");
$data = json_decode($body, true);

if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    if ($_SERVER['REQUEST_METHOD'] == 'PUT' && array_key_exists("id", $_GET)) {
        $conn = null;
        $conn = checkDBConnection();
        $val = new DirectReport($conn);
        $val->direct_report_aid = $_GET["id"];
        $val->direct_report_updated = date("Y-m-d H:i:s");

        checkId($val->direct_report_aid);
        $record = $val->readById()->fetch();
        if (!$record) {
            returnHandleError("Direct report record not found.");
        }

        $val->direct_report_subordinate_id = $record["direct_report_subordinate_id"];
        $val->direct_report_supervisor_id = $record["direct_report_supervisor_id"];
        $val->direct_report_is_active = $record["direct_report_is_active"] == 1 ? 0 : 1;

        if ($val->direct_report_is_active == 1) {
            validateDirectReport($val);
        }

        $query = checkActive($val);
        if ($val->direct_report_is_active == 1) {
            $supervisor = $val->readEmployeeById($val->direct_report_supervisor_id)->fetch();
            syncEmployeeSupervisor($conn, $val->direct_report_subordinate_id, $supervisor);
        } else {
            clearEmployeeSupervisor($conn, $val->direct_report_subordinate_id);
        }

        http_response_code(200);
        returnSuccess($val, "Direct Report Active", $query);
    }
}

checkAccess();
