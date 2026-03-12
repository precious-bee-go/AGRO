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

        $success = $stmt->execute();
        
        // Return result set for SELECT, boolean for others
        $result = $stmt->get_result();
        if ($result === false && $success) {
            return true;
        }
        
        return $result;
    }

    public function __destruct()
    {
        if ($this->db) {
            $this->db->close();
        }
    }
}
?>