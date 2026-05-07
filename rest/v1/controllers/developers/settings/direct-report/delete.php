<?php

$conn = null;
$conn = checkDBConnection();
$val = new DirectReport($conn);

if (array_key_exists("id", $_GET)) {
    $val->direct_report_aid = $_GET["id"];
    checkId($val->direct_report_aid);

    $record = $val->readById()->fetch();
    $query = checkDelete($val);
    if ($record) {
        clearEmployeeSupervisor($conn, $record["direct_report_subordinate_id"]);
    }

    http_response_code(200);
    returnSuccess($val, "Direct Report Delete", $query);
}

checkEndpoint();
