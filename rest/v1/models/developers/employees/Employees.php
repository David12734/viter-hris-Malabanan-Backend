<?php

class Employees
{
    public $employee_aid;
    public $employee_is_active;
    public $employee_first_name;
    public $employee_middle_name;
    public $employee_last_name;
    public $employee_email;
    public $employee_department_id;
    public $employee_created;
    public $employee_updated;
    public $start;
    public $total;
    public $search;

    public $connection;
    public $lastInsertedId;

    public $tblEmployees;
    public $tblDepartment;

    public function __construct($db)
    {
        $this->connection    = $db;
        $this->tblEmployees  = "employees";
        $this->tblDepartment = "settings_department";
    }

    // CREATE
    public function create()
    {
        try {
            $sql  = "insert into {$this->tblEmployees} ";
            $sql .= " ( ";
            $sql .= " employee_is_active, ";
            $sql .= " employee_first_name, ";
            $sql .= " employee_middle_name, ";
            $sql .= " employee_last_name, ";
            $sql .= " employee_email, ";
            $sql .= " employee_department_id, ";
            $sql .= " employee_created, ";
            $sql .= " employee_updated ";
            $sql .= " ) values (";
            $sql .= " :employee_is_active, ";
            $sql .= " :employee_first_name, ";
            $sql .= " :employee_middle_name, ";
            $sql .= " :employee_last_name, ";
            $sql .= " :employee_email, ";
            $sql .= " :employee_department_id, ";
            $sql .= " :employee_created, ";
            $sql .= " :employee_updated ";
            $sql .= " ) ";
            $query = $this->connection->prepare($sql);
            $query->execute([
                "employee_is_active"     => $this->employee_is_active,
                "employee_first_name"    => $this->employee_first_name,
                "employee_middle_name"   => $this->employee_middle_name,
                "employee_last_name"     => $this->employee_last_name,
                "employee_email"         => $this->employee_email,
                "employee_department_id" => $this->employee_department_id,
                "employee_created"       => $this->employee_created,
                "employee_updated"       => $this->employee_updated,
            ]);
            $this->lastInsertedId = $this->connection->lastInsertId();
        } catch (PDOException $e) {
            returnError($e->getMessage());
            $query = false;
        }
        return $query;
    }

    // READ ALL (used for total count)
    public function readAll()
    {
        try {
            $sql  = "select e.*, d.department_name ";
            $sql .= "from {$this->tblEmployees} e ";
            $sql .= "left join {$this->tblDepartment} d on e.employee_department_id = d.department_aid ";
            $sql .= "where true ";
            $sql .= $this->employee_is_active ? " and e.employee_is_active = :employee_is_active" : "";
            $sql .= $this->search != "" ? " and ( " : " ";
            $sql .= $this->search != "" ? " e.employee_first_name like :employee_first_name  " : " ";
            $sql .= $this->search != "" ? " or e.employee_middle_name like :employee_middle_name " : " ";
            $sql .= $this->search != "" ? " or e.employee_last_name like :employee_last_name " : " ";
            $sql .= $this->search != "" ? " or e.employee_email like :employee_email " : " ";
            $sql .= $this->search != "" ? " ) " : " ";
            $sql .= "order by e.employee_aid asc ";
            $query = $this->connection->prepare($sql);
            $query->execute([
                ...$this->employee_is_active ? ["employee_is_active" => $this->employee_is_active] : [],
                ...$this->search ? [
                    "employee_first_name"  => "%{$this->search}%",
                    "employee_middle_name" => "%{$this->search}%",
                    "employee_last_name"   => "%{$this->search}%",
                    "employee_email"       => "%{$this->search}%",
                ] : [],
            ]);
        } catch (PDOException $e) {
            $query = false;
        }
        return $query;
    }

    // READ WITH LIMIT (scroll pagination)
    public function readLimit()
    {
        try {
            $sql  = "select e.*, d.department_name ";
            $sql .= "from {$this->tblEmployees} e ";
            $sql .= "left join {$this->tblDepartment} d on e.employee_department_id = d.department_aid ";
            $sql .= "where true ";
            $sql .= $this->employee_is_active != "" ? " and e.employee_is_active = :employee_is_active" : "";
            $sql .= $this->search != "" ? " and ( " : " ";
            $sql .= $this->search != "" ? " e.employee_first_name like :employee_first_name  " : " ";
            $sql .= $this->search != "" ? " or e.employee_middle_name like :employee_middle_name " : " ";
            $sql .= $this->search != "" ? " or e.employee_last_name like :employee_last_name " : " ";
            $sql .= $this->search != "" ? " or e.employee_email like :employee_email " : " ";
            $sql .= $this->search != "" ? " ) " : " ";
            $sql .= "order by e.employee_aid asc ";
            $sql .= "limit :start, :total ";
            $query = $this->connection->prepare($sql);
            $query->execute([
                "start" => $this->start - 1,
                "total" => $this->total,
                ...$this->employee_is_active != "" ? ["employee_is_active" => $this->employee_is_active] : [],
                ...$this->search != "" ? [
                    "employee_first_name"  => "%{$this->search}%",
                    "employee_middle_name" => "%{$this->search}%",
                    "employee_last_name"   => "%{$this->search}%",
                    "employee_email"       => "%{$this->search}%",
                ] : [],
            ]);
        } catch (PDOException $e) {
            $query = false;
        }
        return $query;
    }

    // UPDATE
    public function update()
    {
        try {
            $sql  = "update {$this->tblEmployees} set ";
            $sql .= "employee_first_name = :employee_first_name, ";
            $sql .= "employee_middle_name = :employee_middle_name, ";
            $sql .= "employee_last_name = :employee_last_name, ";
            $sql .= "employee_email = :employee_email, ";
            $sql .= "employee_department_id = :employee_department_id, ";
            $sql .= "employee_updated = :employee_updated ";
            $sql .= "where employee_aid = :employee_aid ";
            $query = $this->connection->prepare($sql);
            $query->execute([
                "employee_first_name"    => $this->employee_first_name,
                "employee_middle_name"   => $this->employee_middle_name,
                "employee_last_name"     => $this->employee_last_name,
                "employee_email"         => $this->employee_email,
                "employee_department_id" => $this->employee_department_id,
                "employee_updated"       => $this->employee_updated,
                "employee_aid"           => $this->employee_aid,
            ]);
        } catch (PDOException $e) {
            returnError($e->getMessage());
            $query = false;
        }
        return $query;
    }

    // ACTIVE / ARCHIVE / RESTORE
    public function active()
    {
        try {
            $sql  = "update {$this->tblEmployees} set ";
            $sql .= "employee_is_active = :employee_is_active, ";
            $sql .= "employee_updated = :employee_updated ";
            $sql .= "where employee_aid = :employee_aid ";
            $query = $this->connection->prepare($sql);
            $query->execute([
                "employee_is_active" => $this->employee_is_active,
                "employee_updated"   => $this->employee_updated,
                "employee_aid"       => $this->employee_aid,
            ]);
        } catch (PDOException $e) {
            $query = false;
        }
        return $query;
    }

    // DELETE
    public function delete()
    {
        try {
            $sql  = "delete from {$this->tblEmployees} ";
            $sql .= "where employee_aid = :employee_aid ";
            $query = $this->connection->prepare($sql);
            $query->execute([
                "employee_aid" => $this->employee_aid,
            ]);
        } catch (PDOException $e) {
            $query = false;
        }
        return $query;
    }

    // CHECK EMAIL
    public function checkEmail()
    {
        try {
            $sql  = "select employee_email from {$this->tblEmployees} ";
            $sql .= "where employee_email = :employee_email ";
            $query = $this->connection->prepare($sql);
            $query->execute([
                "employee_email" => $this->employee_email,
            ]);
        } catch (PDOException $e) {
            returnError($e->getMessage());
            $query = false;
        }
        return $query;
    }

    // CHECK NAME
    public function checkName()
    {
        try {
            $sql  = "select employee_first_name from {$this->tblEmployees} ";
            $sql .= "where employee_first_name = :employee_first_name ";
            $query = $this->connection->prepare($sql);
            $query->execute([
                "employee_first_name" => $this->employee_first_name,
            ]);
        } catch (PDOException $e) {
            $query = false;
        }
        return $query;
    }
}