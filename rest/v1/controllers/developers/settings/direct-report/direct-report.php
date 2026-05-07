<?php

require '../../../../core/header.php';
require '../../../../core/functions.php';
require '../../../../models/developers/settings/direct-report/DirectReport.php';
require '../../../../models/developers/employees/Employees.php';
require 'helpers.php';

$body = file_get_contents("php://input");
$data = json_decode($body, true);

if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $result = require 'create.php';
        sendResponse($result);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $conn = null;
        $conn = checkDBConnection();
        $val = new DirectReport($conn);
        $val->direct_report_is_active = "";
        $val->search = "";
        $query = checkReadAll($val);
        http_response_code(200);
        getQueriedData($query);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $result = require 'update.php';
        sendResponse($result);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        $result = require 'delete.php';
        sendResponse($result);
        exit;
    }
}

checkAccess();
