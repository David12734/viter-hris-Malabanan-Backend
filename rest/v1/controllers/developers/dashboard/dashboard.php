<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

require __DIR__ . '/../../../core/header.php';
require __DIR__ . '/../../../core/functions.php';
require __DIR__ . '/../../../models/developers/memo/Memo.php';
require __DIR__ . '/../../../models/developers/employees/Employees.php';

$conn = null;
$conn = checkDBConnection();

$body = file_get_contents("php://input");
$data = json_decode($body, true);

if (isset($_SERVER['HTTP_AUTHORIZATION'])) {

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {

        $today = date('Y-m-d');
        $thisMonth = date('m');
        $thisYear  = date('Y');

        // ── ANNOUNCEMENTS (active memos, latest 10) ──
        $memoSql = "SELECT * FROM memo WHERE memo_is_active = 1 ORDER BY memo_aid DESC LIMIT 10";
        $memoQuery = $conn->prepare($memoSql);
        $memoQuery->execute();
        $memos = $memoQuery->fetchAll(PDO::FETCH_ASSOC);

        // ── ALL ACTIVE EMPLOYEES (for team grouping) ──
        $empSql  = "SELECT e.*, d.department_name ";
        $empSql .= "FROM employees e ";
        $empSql .= "LEFT JOIN settings_department d ON e.employee_department_id = d.department_aid ";
        $empSql .= "WHERE e.employee_is_active = 1 ORDER BY d.department_name ASC, e.employee_last_name ASC";
        $empQuery = $conn->prepare($empSql);
        $empQuery->execute();
        $employees = $empQuery->fetchAll(PDO::FETCH_ASSOC);

        // ── BIRTHDAYS THIS MONTH ──
        $bdaySql = "SELECT * FROM employees WHERE employee_is_active = 1 AND MONTH(employee_birthday) = :month ORDER BY DAY(employee_birthday) ASC";
        $bdayQuery = $conn->prepare($bdaySql);
        $bdayQuery->execute([":month" => $thisMonth]);
        $birthdays = $bdayQuery->fetchAll(PDO::FETCH_ASSOC);

        // ── WORK ANNIVERSARIES THIS MONTH ──
        $annivSql = "SELECT * FROM employees WHERE employee_is_active = 1 AND MONTH(employee_start_work_date) = :month AND YEAR(employee_start_work_date) < :year ORDER BY DAY(employee_start_work_date) ASC";
        $annivQuery = $conn->prepare($annivSql);
        $annivQuery->execute([":month" => $thisMonth, ":year" => $thisYear]);
        $anniversaries = $annivQuery->fetchAll(PDO::FETCH_ASSOC);

        // ── NEW EMPLOYEES THIS MONTH (started this month) ──
        $newEmpSql = "SELECT e.*, d.department_name FROM employees e LEFT JOIN settings_department d ON e.employee_department_id = d.department_aid WHERE e.employee_is_active = 1 AND MONTH(e.employee_start_work_date) = :month AND YEAR(e.employee_start_work_date) = :year ORDER BY e.employee_start_work_date ASC";
        $newEmpQuery = $conn->prepare($newEmpSql);
        $newEmpQuery->execute([":month" => $thisMonth, ":year" => $thisYear]);
        $newEmployees = $newEmpQuery->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(200);
        echo json_encode([
            "success"      => true,
            "memos"        => $memos,
            "employees"    => $employees,
            "birthdays"    => $birthdays,
            "anniversaries"=> $anniversaries,
            "new_employees"=> $newEmployees,
        ]);
        exit;
    }
}

checkAccess();