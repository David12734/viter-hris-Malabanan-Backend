<?php

require '../../../../core/header.php';
require '../../../../core/functions.php';
require '../../../../models/developers/settings/direct-report/DirectReport.php';

$conn = null;
$conn = checkDBConnection();
$val = new DirectReport($conn);

$body = file_get_contents("php://input");
$data = json_decode($body, true);

if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    if (array_key_exists('start', $_GET)) {
        checkPayload($data);
        $val->start = $_GET['start'];
        $val->total = 10;
        $val->direct_report_is_active = $data['filterData'];
        $val->search = $data['searchValue'];

        checkLimitId($val->start, $val->total);

        $query = checkReadLimit($val);
        $total_result = checkReadAll($val);

        http_response_code(200);
        checkReadQuery($query, $total_result, $val->total, $val->start);
    }

    checkEndpoint();
}

checkAccess();
