<?php
namespace App\Middleware;

class AuthMiddleware {
    public function handle() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            return false;
        }
        return true;
    }
}

class AdminMiddleware {
    public function handle() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: /unauthorized');
            return false;
        }
        return true;
    }
}
