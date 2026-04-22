<?php 
// check database connection 
$conn = null;
$conn = checkDbConnection();
// make use of classes to save database
$val = new Users($conn);

if (empty($GET)){
    $query = checkReadAll($val);
    http_response_code(200);
    getQueriedData($query);
}

// return 404 if endpoint is not found
checkEndpoint();
