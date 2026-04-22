<?php

// error_reporting(E_ALL);
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';

header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: {$origin}");
header("Vary: Origin");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept, Origin");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Max-Age: 86400");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// data_default_timezone_set("Asia/Manila");
date_default_timezone_set("Asia/Taipei");
