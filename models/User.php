<?php
namespace Models;

use Core\Model;

class User extends Model
{
    /**
     * Create a new user
     */
    public function create($data)
    {
        $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
        return $this->query($sql, [
            $data['name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['role']
        ], "ssss");
    }

    /**
     * Find user by email
     */
    public function findByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = ?";
        $result = $this->query($sql, [$email], "s");
        return $result ? $result->fetch_assoc() : null;
    }

    /**
     * Create user profile
     */
    public function createProfile($userId, $data)
    {
        $sql = "INSERT INTO profiles (user_id, phone, address, farm_name) VALUES (?, ?, ?, ?)";
        return $this->query($sql, [
            $userId,
            $data['phone'] ?? null,
            $data['address'] ?? null,
            $data['farm_name'] ?? null
        ], "isss");
    }
}
?>