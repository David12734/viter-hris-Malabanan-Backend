<?php

$conn = null;
$conn = checkDbConnection();
$val = new Memo($conn);

if (array_key_exists("id", $_GET)) {
    $val->memo_aid = $_GET['id'];
    $val->memo_from = trim($data['memo_from']);
    $val->memo_to = trim($data['memo_to']);
    $val->memo_date = trim($data['memo_date']);
    $val->memo_category = trim($data['memo_category']);
    $val->memo_text = trim($data['memo_text']);
    $val->memo_updated = date('Y-m-d H:i:s');

    // validations
    checkId($val->memo_aid);

    $query = checkUpdate($val);
    http_response_code(200);
    returnSuccess($val, "Memo Update", $query);
}

checkEndpoint();