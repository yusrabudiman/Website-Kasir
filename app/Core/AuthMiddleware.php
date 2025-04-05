<?php

namespace App\Core;

class AuthMiddleware {
    public function handle() {
        if (!isset($_SESSION['user_id'])) {
            // Store the intended URL to redirect back after login
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            
            // Set flash message
            $_SESSION['flash_message'] = 'Please login to access this page';
            $_SESSION['flash_type'] = 'error';
            
            // Redirect to login page
            header('Location: /login');
            exit();
        }
        
        return true;
    }
} 