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
    public $employee_birthday;
    public $employee_start_work_date;
    public $employee_supervisor_id;
    public $employee_supervisor_first_name;
    public $employee_supervisor_last_name;
    public $employee_supervisor_email;
    public $employee_created;
    public $employee_updated;

    public $start;
    public $total;
    public $search;

    public $connection;
    public $lastInsertedId;

    public $tblEmployees;
    public $tblSettingsDepartment;

    public function __construct($db)
    {
        $this->connection = $db;
        $this->tblEmployees = "employees";
        $this->tblSettingsDepartment = "settings_department";
        $this->ensureSupervisorColumns();
    }

    private function ensureSupervisorColumns()
    {
        $columns = [
            "employee_supervisor_id" => "int(11) null",
            "employee_supervisor_first_name" => "varchar(128) not null default ''",
            "employee_supervisor_last_name" => "varchar(128) not null default ''",
            "employee_supervisor_email" => "varchar(255) not null default ''",
        ];

        foreach ($columns as $column => $definition) {
            $query = $this->connection->prepare("
                select count(*) as total
                from information_schema.columns
                where table_schema = database()
                    and table_name = :table_name
                    and column_name = :column_name
            ");
            $query->execute([
                "table_name" => $this->tblEmployees,
                "column_name" => $column,
            ]);
            $row = $query->fetch();
            if ((int)$row["total"] === 0) {
                $this->connection->exec("alter table {$this->tblEmployees} add {$column} {$definition}");
            }
        }
    }

    public function create()
    {
        try {
            $sql  = "insert into {$this->tblEmployees} ";
            $sql .= " ( employee_is_active, employee_first_name, employee_middle_name, employee_last_name, ";
            $sql .= " employee_email, employee_department_id, employee_birthday, employee_start_work_date, ";
            $sql .= " employee_created, employee_updated ) values (";
            $sql .= " :employee_is_active, :employee_first_name, :employee_middle_name, :employee_last_name, ";
            $sql .= " :employee_email, :employee_department_id, :employee_birthday, :employee_start_work_date, ";
            $sql .= " :employee_created, :employee_updated )";
            $query = $this->connection->prepare($sql);
            $query->execute([
                "employee_is_active"      => $this->employee_is_active,
                "employee_first_name"     => $this->employee_first_name,
                "employee_middle_name"    => $this->employee_middle_name,
                "employee_last_name"      => $this->employee_last_name,
                "employee_email"          => $this->employee_email,
                "employee_department_id"  => $this->employee_department_id,
                "employee_birthday"       => $this->employee_birthday,
                "employee_start_work_date"=> $this->employee_start_work_date,
                "employee_created"        => $this->employee_created,
                "employee_updated"        => $this->employee_updated,
            ]);
            $this->lastInsertedId = $this->connection->lastInsertId();
        } catch (PDOException $e) {
            returnError($e->getMessage());
            $query = false;
        }
        return $query;
    }

    public function readAll()
    {
        try {
            $sql  = "select e.*, d.department_name ";
            $sql .= "from {$this->tblEmployees} e ";
            $sql .= "left join {$this->tblSettingsDepartment} as d on d.department_aid = e.employee_department_id ";
            $sql .= "where true ";
            $sql .= $this->employee_is_active !== null && $this->employee_is_active !== "" ? " and e.employee_is_active = :employee_is_active " : "";
            $sql .= $this->search != "" ? " and ( e.employee_first_name like :employee_first_name or e.employee_middle_name like :employee_middle_name or e.employee_last_name like :employee_last_name or e.employee_email like :employee_email or d.department_name like :department_name ) " : "";
            $sql .= "order by e.employee_aid desc ";
            $query = $this->connection->prepare($sql);
            if ($this->employee_is_active !== null && $this->employee_is_active !== "") {
                $query->bindValue(":employee_is_active", $this->employee_is_active);
            }
            if ($this->search != "") {
                $s = "%{$this->search}%";
                $query->bindValue(":employee_first_name", $s);
                $query->bindValue(":employee_middle_name", $s);
                $query->bindValue(":employee_last_name", $s);
                $query->bindValue(":employee_email", $s);
                $query->bindValue(":department_name", $s);
            }
            $query->execute();
        } catch (PDOException $e) {
            $query = false;
        }
        return $query;
    }

    public function readLimit()
    {
        try {
            $sql  = "select e.*, d.department_name ";
            $sql .= "from {$this->tblEmployees} e ";
            $sql .= "left join {$this->tblSettingsDepartment} as d on d.department_aid = e.employee_department_id ";
            $sql .= "where true ";
            $sql .= $this->employee_is_active !== null && $this->employee_is_active !== "" ? " and e.employee_is_active = :employee_is_active " : "";
            $sql .= $this->search != "" ? " and ( e.employee_first_name like :employee_first_name or e.employee_middle_name like :employee_middle_name or e.employee_last_name like :employee_last_name or e.employee_email like :employee_email or d.department_name like :department_name ) " : "";
            $sql .= "order by e.employee_aid desc ";
            $sql .= "limit :start, :total ";
            $query = $this->connection->prepare($sql);
            $query->bindValue(":start", (int)$this->start - 1, PDO::PARAM_INT);
            $query->bindValue(":total", (int)$this->total, PDO::PARAM_INT);
            if ($this->employee_is_active !== null && $this->employee_is_active !== "") {
                $query->bindValue(":employee_is_active", $this->employee_is_active);
            }
            if ($this->search != "") {
                $s = "%{$this->search}%";
                $query->bindValue(":employee_first_name", $s);
                $query->bindValue(":employee_middle_name", $s);
                $query->bindValue(":employee_last_name", $s);
                $query->bindValue(":employee_email", $s);
                $query->bindValue(":department_name", $s);
            }
            $query->execute();
        } catch (PDOException $e) {
            $query = false;
        }
        return $query;
    }

    public function update()
    {
        try {
            $sql  = "update {$this->tblEmployees} set ";
            $sql .= "employee_first_name = :employee_first_name, ";
            $sql .= "employee_middle_name = :employee_middle_name, ";
            $sql .= "employee_last_name = :employee_last_name, ";
            $sql .= "employee_email = :employee_email, ";
            $sql .= "employee_department_id = :employee_department_id, ";
            $sql .= "employee_birthday = :employee_birthday, ";
            $sql .= "employee_start_work_date = :employee_start_work_date, ";
            $sql .= "employee_updated = :employee_updated ";
            $sql .= "where employee_aid = :employee_aid ";
            $query = $this->connection->prepare($sql);
            $query->execute([
                "employee_first_name"      => $this->employee_first_name,
                "employee_middle_name"     => $this->employee_middle_name,
                "employee_last_name"       => $this->employee_last_name,
                "employee_email"           => $this->employee_email,
                "employee_department_id"   => $this->employee_department_id,
                "employee_birthday"        => $this->employee_birthday,
                "employee_start_work_date" => $this->employee_start_work_date,
                "employee_updated"         => $this->employee_updated,
                "employee_aid"             => $this->employee_aid,
            ]);
        } catch (PDOException $e) {
            returnError($e->getMessage());
            $query = false;
        }
        return $query;
    }

    public function updateSupervisor()
    {
        try {
            $sql  = "update {$this->tblEmployees} set ";
            $sql .= "employee_supervisor_id = :employee_supervisor_id, ";
            $sql .= "employee_supervisor_first_name = :employee_supervisor_first_name, ";
            $sql .= "employee_supervisor_last_name = :employee_supervisor_last_name, ";
            $sql .= "employee_supervisor_email = :employee_supervisor_email, ";
            $sql .= "employee_updated = :employee_updated ";
            $sql .= "where employee_aid = :employee_aid ";
            $query = $this->connection->prepare($sql);
            $query->execute([
                "employee_supervisor_id" => $this->employee_supervisor_id,
                "employee_supervisor_first_name" => $this->employee_supervisor_first_name,
                "employee_supervisor_last_name" => $this->employee_supervisor_last_name,
                "employee_supervisor_email" => $this->employee_supervisor_email,
                "employee_updated" => $this->employee_updated,
                "employee_aid" => $this->employee_aid,
            ]);
        } catch (PDOException $e) {
            returnError($e->getMessage());
            $query = false;
        }
        return $query;
    }

    public function clearSupervisor()
    {
        try {
            $sql  = "update {$this->tblEmployees} set ";
            $sql .= "employee_supervisor_id = null, ";
            $sql .= "employee_supervisor_first_name = '', ";
            $sql .= "employee_supervisor_last_name = '', ";
            $sql .= "employee_supervisor_email = '', ";
            $sql .= "employee_updated = :employee_updated ";
            $sql .= "where employee_aid = :employee_aid ";
            $query = $this->connection->prepare($sql);
            $query->execute([
                "employee_updated" => $this->employee_updated,
                "employee_aid" => $this->employee_aid,
            ]);
        } catch (PDOException $e) {
            returnError($e->getMessage());
            $query = false;
        }
        return $query;
    }

    public function syncSupervisorSnapshot()
    {
        try {
            $sql  = "update {$this->tblEmployees} set ";
            $sql .= "employee_supervisor_first_name = :employee_supervisor_first_name, ";
            $sql .= "employee_supervisor_last_name = :employee_supervisor_last_name, ";
            $sql .= "employee_supervisor_email = :employee_supervisor_email, ";
            $sql .= "employee_updated = :employee_updated ";
            $sql .= "where employee_supervisor_id = :employee_supervisor_id ";
            $query = $this->connection->prepare($sql);
            $query->execute([
                "employee_supervisor_first_name" => $this->employee_first_name,
                "employee_supervisor_last_name" => $this->employee_last_name,
                "employee_supervisor_email" => $this->employee_email,
                "employee_updated" => $this->employee_updated,
                "employee_supervisor_id" => $this->employee_aid,
            ]);
        } catch (PDOException $e) {
            returnError($e->getMessage());
            $query = false;
        }
        return $query;
    }

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

    public function delete()
    {
        try {
            $sql  = "delete from {$this->tblEmployees} where employee_aid = :employee_aid ";
            $query = $this->connection->prepare($sql);
            $query->execute(["employee_aid" => $this->employee_aid]);
        } catch (PDOException $e) {
            $query = false;
        }
        return $query;
    }

    public function checkName()
    {
        try {
            $sql  = "select employee_first_name from {$this->tblEmployees} where employee_first_name = :employee_first_name ";
            $query = $this->connection->prepare($sql);
            $query->execute(["employee_first_name" => $this->employee_first_name]);
        } catch (PDOException $e) {
            $query = false;
        }
        return $query;
    }

    public function checkEmail()
    {
        try {
            $sql  = "select employee_email from {$this->tblEmployees} where employee_email = :employee_email ";
            $query = $this->connection->prepare($sql);
            $query->execute(["employee_email" => $this->employee_email]);
        } catch (PDOException $e) {
            $query = false;
        }
        return $query;
    }

    public function checkDepartment()
    {
        try {
            $sql  = "select department_aid from {$this->tblSettingsDepartment} where department_aid = :department_aid and department_is_active = 1 ";
            $query = $this->connection->prepare($sql);
            $query->execute(["department_aid" => $this->employee_department_id]);
        } catch (PDOException $e) {
            $query = false;
        }
        return $query;
    }
}
