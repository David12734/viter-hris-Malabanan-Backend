<?php

$conn = null;
$conn = checkDbConnection();
$val = new Memo($conn);

if (array_key_exists("id", $_GET)) {
    $val->memo_aid = $_GET['id'];

    $query = checkDelete($val);
    http_response_code(200);
    returnSuccess($val, "Memo Delete", $query);
}

checkEndpoint();