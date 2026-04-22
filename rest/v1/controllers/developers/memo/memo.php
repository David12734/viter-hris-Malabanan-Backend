<?php

// set http header
require '../../../core/header.php';
// use needed functions
require '../../../core/functions.php';
// use models
require '../../../models/developers/memo/Memo.php';

// get payload from frontend
$body = file_get_contents("php://input");
$data = json_decode($body, true);

// CREATE / POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $result = require 'create.php';
    sendResponse($result);
    exit;
}

// UPDATE / PUT
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    $result = require 'update.php';
    sendResponse($result);
    exit;
}

// DELETE / DELETE
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    $result = require 'delete.php';
    sendResponse($result);
    exit;
}

// return 404 if endpoint is not available
checkEndpoint();