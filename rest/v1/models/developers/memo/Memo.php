<?php

class Memo
{
    public $memo_aid;
    public $memo_is_active;
    public $memo_from;
    public $memo_to;
    public $memo_date;
    public $memo_category;
    public $memo_text;
    public $memo_created;
    public $memo_updated;

    public $connection;
    public $lastInsertedId;
    public $start;
    public $total;
    public $search;

    public $tblMemo;

    public function __construct($db)
    {
        $this->connection = $db;
        $this->tblMemo = "memo";
    }

    // CREATE
    public function create()
    {
        try {
            $sql = "insert into {$this->tblMemo} ";
            $sql .= " ( ";
            $sql .= " memo_is_active, ";
            $sql .= " memo_from, ";
            $sql .= " memo_to, ";
            $sql .= " memo_date, ";
            $sql .= " memo_category, ";
            $sql .= " memo_text, ";
            $sql .= " memo_created, ";
            $sql .= " memo_updated ";
            $sql .= " ) values (";
            $sql .= " :memo_is_active, ";
            $sql .= " :memo_from, ";
            $sql .= " :memo_to, ";
            $sql .= " :memo_date, ";
            $sql .= " :memo_category, ";
            $sql .= " :memo_text, ";
            $sql .= " :memo_created, ";
            $sql .= " :memo_updated ";
            $sql .= " ) ";
            $query = $this->connection->prepare($sql);
            $query->execute([
                "memo_is_active" => $this->memo_is_active,
                "memo_from" => $this->memo_from,
                "memo_to" => $this->memo_to,
                "memo_date" => $this->memo_date,
                "memo_category" => $this->memo_category,
                "memo_text" => $this->memo_text,
                "memo_created" => $this->memo_created,
                "memo_updated" => $this->memo_updated,
            ]);
            $this->lastInsertedId = $this->connection->lastInsertId();
        } catch (PDOException $e) {
            returnError($e->getMessage());
            $query = false;
        }
        return $query;
    }

    // READ ALL (for total count)
    public function readAll()
    {
        try {
            $sql =  "select ";
            $sql .= "* ";
            $sql .= "from {$this->tblMemo} ";
            $sql .= "where true ";
            $sql .= $this->memo_is_active != "" ? " and memo_is_active = :memo_is_active" : "";
            $sql .= $this->search != "" ? " and ( " : " ";
            $sql .= $this->search != "" ? " memo_from like :memo_from " : " ";
            $sql .= $this->search != "" ? " or memo_to like :memo_to " : " ";
            $sql .= $this->search != "" ? " or memo_category like :memo_category " : " ";
            $sql .= $this->search != "" ? " ) " : " ";
            $sql .= "order by memo_aid desc ";
            $query = $this->connection->prepare($sql);
            $query->execute([
                ...$this->memo_is_active != "" ? ["memo_is_active" => $this->memo_is_active] : [],
                ...$this->search != "" ? [
                    "memo_from" => "%{$this->search}%",
                    "memo_to" => "%{$this->search}%",
                    "memo_category" => "%{$this->search}%",
                ] : [],
            ]);
        } catch (PDOException $e) {
            $query = false;
        }
        return $query;
    }

    // READ WITH LIMIT (pagination)
    public function readLimit()
    {
        try {
            $sql =  "select ";
            $sql .= "* ";
            $sql .= "from {$this->tblMemo} ";
            $sql .= "where true ";
            $sql .= $this->memo_is_active != "" ? " and memo_is_active = :memo_is_active" : "";
            $sql .= $this->search != "" ? " and ( " : " ";
            $sql .= $this->search != "" ? " memo_from like :memo_from " : " ";
            $sql .= $this->search != "" ? " or memo_to like :memo_to " : " ";
            $sql .= $this->search != "" ? " or memo_category like :memo_category " : " ";
            $sql .= $this->search != "" ? " ) " : " ";
            $sql .= "order by memo_aid desc ";
            $sql .= "limit :start, ";
            $sql .= " :total ";
            $query = $this->connection->prepare($sql);
            $query->execute([
                "start" => $this->start - 1,
                "total" => $this->total,
                ...$this->memo_is_active != "" ? ["memo_is_active" => $this->memo_is_active] : [],
                ...$this->search != "" ? [
                    "memo_from" => "%{$this->search}%",
                    "memo_to" => "%{$this->search}%",
                    "memo_category" => "%{$this->search}%",
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
            $sql = "update {$this->tblMemo} set ";
            $sql .= "memo_from = :memo_from, ";
            $sql .= "memo_to = :memo_to, ";
            $sql .= "memo_date = :memo_date, ";
            $sql .= "memo_category = :memo_category, ";
            $sql .= "memo_text = :memo_text, ";
            $sql .= "memo_updated = :memo_updated ";
            $sql .= "where memo_aid = :memo_aid ";
            $query = $this->connection->prepare($sql);
            $query->execute([
                "memo_from" => $this->memo_from,
                "memo_to" => $this->memo_to,
                "memo_date" => $this->memo_date,
                "memo_category" => $this->memo_category,
                "memo_text" => $this->memo_text,
                "memo_updated" => $this->memo_updated,
                "memo_aid" => $this->memo_aid,
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
            $sql = "update {$this->tblMemo} set ";
            $sql .= "memo_is_active = :memo_is_active, ";
            $sql .= "memo_updated = :memo_updated ";
            $sql .= "where memo_aid = :memo_aid ";
            $query = $this->connection->prepare($sql);
            $query->execute([
                "memo_is_active" => $this->memo_is_active,
                "memo_updated" => $this->memo_updated,
                "memo_aid" => $this->memo_aid,
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
            $sql = "delete from {$this->tblMemo} ";
            $sql .= "where memo_aid = :memo_aid ";
            $query = $this->connection->prepare($sql);
            $query->execute([
                "memo_aid" => $this->memo_aid,
            ]);
        } catch (PDOException $e) {
            $query = false;
        }
        return $query;
    }
}