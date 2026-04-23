<?php

class Department
{
    public $department_aid;
    public $department_is_active;
    public $department_name;
    public $department_created;
    public $department_updated;

    public $connection;
    public $lastInsertedId;
    public $start;
    public $total;
    public $search;

    public $tblDepartment;

    public function __construct($db)
    {
        $this->connection = $db;
        $this->tblDepartment = "settings_department";
    }

    // CREATE
    public function create()
    {
        try {
            $sql = "insert into {$this->tblDepartment} ";
            $sql .= " ( ";
            $sql .= " department_is_active, ";
            $sql .= " department_name, ";
            $sql .= " department_created, ";
            $sql .= " department_updated ";
            $sql .= " ) values (";
            $sql .= " :department_is_active, ";
            $sql .= " :department_name, ";
            $sql .= " :department_created, ";
            $sql .= " :department_updated ";
            $sql .= " ) ";
            $query = $this->connection->prepare($sql);
            $query->execute([
                "department_is_active" => $this->department_is_active,
                "department_name"      => $this->department_name,
                "department_created"   => $this->department_created,
                "department_updated"   => $this->department_updated,
            ]);
            $this->lastInsertedId = $this->connection->lastInsertId();
        } catch (PDOException $e) {
            returnError($e->getMessage());
            $query = false;
        }
        return $query;
    }

    // READ ALL (used for total count + dropdown list)
    public function readAll()
    {
        try {
            $sql  = "select * from {$this->tblDepartment} ";
            $sql .= "where true ";
            $sql .= $this->department_is_active != "" ? " and department_is_active = :department_is_active " : "";
            $sql .= $this->search != "" ? " and department_name like :department_name " : "";
            $sql .= "order by department_aid asc ";
            $query = $this->connection->prepare($sql);
            $query->execute([
                ...$this->department_is_active != "" ? ["department_is_active" => $this->department_is_active] : [],
                ...$this->search != "" ? ["department_name" => "%{$this->search}%"] : [],
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
            $sql  = "select * from {$this->tblDepartment} ";
            $sql .= "where true ";
            $sql .= $this->department_is_active != "" ? " and department_is_active = :department_is_active " : "";
            $sql .= $this->search != "" ? " and department_name like :department_name " : "";
            $sql .= "order by department_aid asc ";
            $sql .= "limit :start, :total ";
            $query = $this->connection->prepare($sql);
            $query->execute([
                "start" => $this->start - 1,
                "total" => $this->total,
                ...$this->department_is_active != "" ? ["department_is_active" => $this->department_is_active] : [],
                ...$this->search != "" ? ["department_name" => "%{$this->search}%"] : [],
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
            $sql  = "update {$this->tblDepartment} set ";
            $sql .= "department_name = :department_name, ";
            $sql .= "department_updated = :department_updated ";
            $sql .= "where department_aid = :department_aid ";
            $query = $this->connection->prepare($sql);
            $query->execute([
                "department_name"    => $this->department_name,
                "department_updated" => $this->department_updated,
                "department_aid"     => $this->department_aid,
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
            $sql  = "update {$this->tblDepartment} set ";
            $sql .= "department_is_active = :department_is_active, ";
            $sql .= "department_updated = :department_updated ";
            $sql .= "where department_aid = :department_aid ";
            $query = $this->connection->prepare($sql);
            $query->execute([
                "department_is_active" => $this->department_is_active,
                "department_updated"   => $this->department_updated,
                "department_aid"       => $this->department_aid,
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
            $sql  = "delete from {$this->tblDepartment} ";
            $sql .= "where department_aid = :department_aid ";
            $query = $this->connection->prepare($sql);
            $query->execute([
                "department_aid" => $this->department_aid,
            ]);
        } catch (PDOException $e) {
            $query = false;
        }
        return $query;
    }

    // CHECK NAME (duplicate validation)
    public function checkName()
    {
        try {
            $sql  = "select department_name from {$this->tblDepartment} ";
            $sql .= "where department_name = :department_name ";
            $query = $this->connection->prepare($sql);
            $query->execute([
                "department_name" => $this->department_name,
            ]);
        } catch (PDOException $e) {
            $query = false;
        }
        return $query;
    }
}