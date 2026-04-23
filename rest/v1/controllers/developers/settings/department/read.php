<?php

$conn = null;
$conn = checkDbConnection();
$val = new Department($conn);
$val->department_is_active = "";
$val->search = "";

$query = checkReadAll($val);
http_response_code(200);
return getResultData($query);