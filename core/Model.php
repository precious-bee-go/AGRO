<?php
namespace Core;

abstract class Model
{
    protected $db;

    public function __construct()
    {
        $this->db = get_db_connection();
    }

    public function query($sql, $params = [], $types = "")
    {
        if (empty($params)) {
            return $this->db->query($sql);
        }

        $stmt = $this->db->prepare($sql);
        if ($stmt === false) {
            return false;
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        return $stmt->get_result();
    }

    public function __destruct()
    {
        if ($this->db) {
            $this->db->close();
        }
    }
}
?>