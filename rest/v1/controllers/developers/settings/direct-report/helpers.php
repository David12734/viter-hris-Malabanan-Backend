<?php

function validateDirectReport($val)
{
    checkId($val->direct_report_subordinate_id);
    checkId($val->direct_report_supervisor_id);

    if ($val->direct_report_subordinate_id == $val->direct_report_supervisor_id) {
        returnHandleError("The same employee cannot be both supervisor and subordinate.");
    }

    $subordinate = $val->readEmployeeById($val->direct_report_subordinate_id);
    if ($subordinate->rowCount() === 0) {
        returnHandleError("Selected subordinate is invalid or inactive.");
    }

    $supervisor = $val->readEmployeeById($val->direct_report_supervisor_id);
    if ($supervisor->rowCount() === 0) {
        returnHandleError("Selected supervisor is invalid or inactive.");
    }

    $reverse = $val->checkReverseAssignment();
    if ($reverse->rowCount() > 0) {
        returnHandleError("Invalid request, the supervisor cannot be assigned to the selected subordinate.");
    }
}

function syncEmployeeSupervisor($conn, $subordinateId, $supervisor)
{
    $employee = new Employees($conn);
    $employee->employee_aid = $subordinateId;
    $employee->employee_supervisor_id = $supervisor["employee_aid"];
    $employee->employee_supervisor_first_name = $supervisor["employee_first_name"];
    $employee->employee_supervisor_last_name = $supervisor["employee_last_name"];
    $employee->employee_supervisor_email = $supervisor["employee_email"];
    $employee->employee_updated = date("Y-m-d H:i:s");
    return $employee->updateSupervisor();
}

function clearEmployeeSupervisor($conn, $subordinateId)
{
    $employee = new Employees($conn);
    $employee->employee_aid = $subordinateId;
    $employee->employee_updated = date("Y-m-d H:i:s");
    return $employee->clearSupervisor();
}
