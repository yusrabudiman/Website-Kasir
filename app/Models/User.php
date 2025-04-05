<?php
namespace App\Models;

use App\Core\Database;

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function authenticate($username, $password) {
        $this->db->query('SELECT * FROM users WHERE username = :username');
        $this->db->bind(':username', $username);
        $user = $this->db->single();

        if ($user && password_verify($password, $user->password)) {
            return $user;
        }
        return false;
    }

    public function findByUsername($username) {
        $this->db->query('SELECT * FROM users WHERE username = :username');
        $this->db->bind(':username', $username);
        return $this->db->single();
    }

    public function create($data) {
        $this->db->query('INSERT INTO users (id, username, password, name, email, role) 
                         VALUES (:id, :username, :password, :name, :email, :role)');
        
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email'] ?? null);
        $this->db->bind(':role', $data['role']);

        return $this->db->execute();
    }

    public function findById($id) {
        $this->db->query('SELECT id, username, name, email, role FROM users WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getAll() {
        $this->db->query('SELECT id, username, name, email, role, created_at FROM users');
        return $this->db->resultSet();
    }

    public function update($data) {
        $query = 'UPDATE users SET name = :name';
        
        if (isset($data['email'])) {
            $query .= ', email = :email';
        }
        
        if (!empty($data['password'])) {
            $query .= ', password = :password';
        }
        
        $query .= ' WHERE id = :id';
        
        $this->db->query($query);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':id', $data['id']);
        
        if (isset($data['email'])) {
            $this->db->bind(':email', $data['email']);
        }
        
        if (!empty($data['password'])) {
            $this->db->bind(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        }
        
        return $this->db->execute();
    }

    public function findByEmail($email) {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);
        return $this->db->single();
    }
} 