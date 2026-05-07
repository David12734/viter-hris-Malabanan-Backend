<?php

class DirectReport
{
    public $direct_report_aid;
    public $direct_report_is_active;
    public $direct_report_subordinate_id;
    public $direct_report_supervisor_id;
    public $direct_report_created;
    public $direct_report_updated;

    public $start;
    public $total;
    public $search;
    public $connection;
    public $lastInsertedId;

    public $tblDirectReport;
    public $tblEmployees;

    public function __construct($db)
    {
        $this->connection = $db;
        $this->tblDirectReport = "settings_direct_report";
        $this->tblEmployees = "employees";
        $this->ensureSchema();
    }

    private function ensureSchema()
    {
        $this->connection->exec("
            create table if not exists {$this->tblDirectReport} (
                direct_report_aid int(11) not null auto_increment,
                direct_report_is_active tinyint(1) not null default 1,
                direct_report_subordinate_id int(11) not null,
                direct_report_supervisor_id int(11) not null,
                direct_report_created datetime not null,
                direct_report_updated datetime not null,
                primary key (direct_report_aid),
                unique key direct_report_subordinate_unique (direct_report_subordinate_id)
            ) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_general_ci
        ");

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
            $sql  = "insert into {$this->tblDirectReport} ";
            $sql .= "(direct_report_is_active, direct_report_subordinate_id, direct_report_supervisor_id, direct_report_created, direct_report_updated) ";
            $sql .= "values (:direct_report_is_active, :direct_report_subordinate_id, :direct_report_supervisor_id, :direct_report_created, :direct_report_updated) ";
            $sql .= "on duplicate key update ";
            $sql .= "direct_report_is_active = values(direct_report_is_active), ";
            $sql .= "direct_report_supervisor_id = values(direct_report_supervisor_id), ";
            $sql .= "direct_report_updated = values(direct_report_updated) ";
            $query = $this->connection->prepare($sql);
            $query->execute([
                "direct_report_is_active" => $this->direct_report_is_active,
                "direct_report_subordinate_id" => $this->direct_report_subordinate_id,
                "direct_report_supervisor_id" => $this->direct_report_supervisor_id,
                "direct_report_created" => $this->direct_report_created,
                "direct_report_updated" => $this->direct_report_updated,
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
            $sql  = "select dr.*, ";
            $sql .= "sub.employee_first_name as subordinate_first_name, sub.employee_last_name as subordinate_last_name, sub.employee_email as subordinate_email, ";
            $sql .= "sup.employee_first_name as supervisor_first_name, sup.employee_last_name as supervisor_last_name, sup.employee_email as supervisor_email ";
            $sql .= "from {$this->tblDirectReport} dr ";
            $sql .= "left join {$this->tblEmployees} sub on sub.employee_aid = dr.direct_report_subordinate_id ";
            $sql .= "left join {$this->tblEmployees} sup on sup.employee_aid = dr.direct_report_supervisor_id ";
            $sql .= "where true ";
            $sql .= $this->direct_report_is_active !== null && $this->direct_report_is_active !== "" ? "and dr.direct_report_is_active = :direct_report_is_active " : "";
            $sql .= $this->search != "" ? "and (sub.employee_first_name like :search or sub.employee_last_name like :search or sub.employee_email like :search or sup.employee_first_name like :search or sup.employee_last_name like :search or sup.employee_email like :search) " : "";
            $sql .= "order by dr.direct_report_aid desc ";
            $query = $this->connection->prepare($sql);
            if ($this->direct_report_is_active !== null && $this->direct_report_is_active !== "") {
                $query->bindValue(":direct_report_is_active", $this->direct_report_is_active);
            }
            if ($this->search != "") {
                $query->bindValue(":search", "%{$this->search}%");
            }
            $query->execute();
        } catch (PDOException $e) {
            returnError($e->getMessage());
            $query = false;
        }
        return $query;
    }

    public function readLimit()
    {
        try {
            $sql  = "select dr.*, ";
            $sql .= "sub.employee_first_name as subordinate_first_name, sub.employee_last_name as subordinate_last_name, sub.employee_email as subordinate_email, ";
            $sql .= "sup.employee_first_name as supervisor_first_name, sup.employee_last_name as supervisor_last_name, sup.employee_email as supervisor_email ";
            $sql .= "from {$this->tblDirectReport} dr ";
            $sql .= "left join {$this->tblEmployees} sub on sub.employee_aid = dr.direct_report_subordinate_id ";
            $sql .= "left join {$this->tblEmployees} sup on sup.employee_aid = dr.direct_report_supervisor_id ";
            $sql .= "where true ";
            $sql .= $this->direct_report_is_active !== null && $this->direct_report_is_active !== "" ? "and dr.direct_report_is_active = :direct_report_is_active " : "";
            $sql .= $this->search != "" ? "and (sub.employee_first_name like :search or sub.employee_last_name like :search or sub.employee_email like :search or sup.employee_first_name like :search or sup.employee_last_name like :search or sup.employee_email like :search) " : "";
            $sql .= "order by dr.direct_report_aid desc ";
            $sql .= "limit :start, :total ";
            $query = $this->connection->prepare($sql);
            $query->bindValue(":start", (int)$this->start - 1, PDO::PARAM_INT);
            $query->bindValue(":total", (int)$this->total, PDO::PARAM_INT);
            if ($this->direct_report_is_active !== null && $this->direct_report_is_active !== "") {
                $query->bindValue(":direct_report_is_active", $this->direct_report_is_active);
            }
            if ($this->search != "") {
                $query->bindValue(":search", "%{$this->search}%");
            }
            $query->execute();
        } catch (PDOException $e) {
            returnError($e->getMessage());
            $query = false;
        }
        return $query;
    }

    public function update()
    {
        try {
            $sql  = "update {$this->tblDirectReport} set ";
            $sql .= "direct_report_subordinate_id = :direct_report_subordinate_id, ";
            $sql .= "direct_report_supervisor_id = :direct_report_supervisor_id, ";
            $sql .= "direct_report_updated = :direct_report_updated ";
            $sql .= "where direct_report_aid = :direct_report_aid ";
            $query = $this->connection->prepare($sql);
            $query->execute([
                "direct_report_subordinate_id" => $this->direct_report_subordinate_id,
                "direct_report_supervisor_id" => $this->direct_report_supervisor_id,
                "direct_report_updated" => $this->direct_report_updated,
                "direct_report_aid" => $this->direct_report_aid,
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
            $sql  = "update {$this->tblDirectReport} set ";
            $sql .= "direct_report_is_active = :direct_report_is_active, ";
            $sql .= "direct_report_updated = :direct_report_updated ";
            $sql .= "where direct_report_aid = :direct_report_aid ";
            $query = $this->connection->prepare($sql);
            $query->execute([
                "direct_report_is_active" => $this->direct_report_is_active,
                "direct_report_updated" => $this->direct_report_updated,
                "direct_report_aid" => $this->direct_report_aid,
            ]);
        } catch (PDOException $e) {
            returnError($e->getMessage());
            $query = false;
        }
        return $query;
    }

    public function delete()
    {
        try {
            $sql = "delete from {$this->tblDirectReport} where direct_report_aid = :direct_report_aid ";
            $query = $this->connection->prepare($sql);
            $query->execute(["direct_report_aid" => $this->direct_report_aid]);
        } catch (PDOException $e) {
            returnError($e->getMessage());
            $query = false;
        }
        return $query;
    }

    public function readById()
    {
        try {
            $sql = "select * from {$this->tblDirectReport} where direct_report_aid = :direct_report_aid ";
            $query = $this->connection->prepare($sql);
            $query->execute(["direct_report_aid" => $this->direct_report_aid]);
        } catch (PDOException $e) {
            returnError($e->getMessage());
            $query = false;
        }
        return $query;
    }

    public function readEmployeeById($employeeId)
    {
        try {
            $sql = "select * from {$this->tblEmployees} where employee_aid = :employee_aid and employee_is_active = 1 ";
            $query = $this->connection->prepare($sql);
            $query->execute(["employee_aid" => $employeeId]);
        } catch (PDOException $e) {
            returnError($e->getMessage());
            $query = false;
        }
        return $query;
    }

    public function checkReverseAssignment()
    {
        try {
            $sql  = "select direct_report_aid from {$this->tblDirectReport} ";
            $sql .= "where direct_report_subordinate_id = :direct_report_supervisor_id ";
            $sql .= "and direct_report_supervisor_id = :direct_report_subordinate_id ";
            $sql .= "and direct_report_is_active = 1 ";
            $sql .= $this->direct_report_aid ? "and direct_report_aid != :direct_report_aid " : "";
            $sql .= "union ";
            $sql .= "select employee_aid as direct_report_aid from {$this->tblEmployees} ";
            $sql .= "where employee_aid = :employee_supervisor_id ";
            $sql .= "and employee_supervisor_id = :employee_subordinate_id ";
            $query = $this->connection->prepare($sql);
            $query->bindValue(":direct_report_supervisor_id", $this->direct_report_supervisor_id);
            $query->bindValue(":direct_report_subordinate_id", $this->direct_report_subordinate_id);
            $query->bindValue(":employee_supervisor_id", $this->direct_report_supervisor_id);
            $query->bindValue(":employee_subordinate_id", $this->direct_report_subordinate_id);
            if ($this->direct_report_aid) {
                $query->bindValue(":direct_report_aid", $this->direct_report_aid);
            }
            $query->execute();
        } catch (PDOException $e) {
            returnError($e->getMessage());
            $query = false;
        }
        return $query;
    }
}
