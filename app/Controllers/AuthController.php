<?php
namespace App\Controllers;

use App\Core\Controller as BaseController;
use App\Core\Cache;
use App\Models\User;
use Ramsey\Uuid\Uuid;

class AuthController extends BaseController {
    private $userModel;
    protected $cache;

    public function __construct() {
        parent::__construct();
        error_log("AuthController constructor called");
        $this->userModel = $this->model('User');
        $this->cache = Cache::getInstance();
    }

    protected function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    protected function validateCSRF() {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            http_response_code(403);
            die('Invalid CSRF token');
        }
    }

    public function login() {
        error_log("Login method called");
        if ($this->isPost()) {
            $this->validateCSRF();
            
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
            $password = $_POST['password'];

            $user = $this->userModel->authenticate($username, $password);

            if ($user) {
                $_SESSION['user_id'] = $user->id;
                $_SESSION['user_name'] = $user->name;
                $_SESSION['user_role'] = $user->role;
                $_SESSION['user_email'] = $user->email ?? '';
                
                // Regenerate session ID for security
                session_regenerate_id(true);
                
                // Cache user data
                $this->cache->set('user_' . $user->id, [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $user->role,
                    'email' => $user->email ?? '',
                    'last_activity' => time()
                ], 3600); // Cache for 1 hour
                
                // Redirect to the intended URL
                $this->redirect('/dashboard');
            } else {
                return $this->view('auth/login', [
                    'error' => 'Invalid username or password',
                    'csrf_token' => $this->generateCSRFToken()
                ]);
            }
        }

        return $this->view('auth/login', [
            'csrf_token' => $this->generateCSRFToken()
        ]);
    }

    public function signup() {
        if ($this->isPost()) {
            $this->validateCSRF();
            
            // Get form data
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);
            
            // Validate email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->view('auth/signup', [
                    'error' => 'Please enter a valid email address',
                    'csrf_token' => $this->generateCSRFToken()
                ]);
            }
            
            // Validate role
            if (!in_array($role, ['admin', 'cashier'])) {
                return $this->view('auth/signup', [
                    'error' => 'Invalid role selected',
                    'csrf_token' => $this->generateCSRFToken()
                ]);
            }
            
            // Validate password match
            if ($password !== $confirm_password) {
                return $this->view('auth/signup', [
                    'error' => 'Passwords do not match',
                    'csrf_token' => $this->generateCSRFToken()
                ]);
            }
            
            // Check if username already exists
            if ($this->userModel->findByUsername($username)) {
                return $this->view('auth/signup', [
                    'error' => 'Username already exists',
                    'csrf_token' => $this->generateCSRFToken()
                ]);
            }
            
            // Check if email already exists
            if ($this->userModel->findByEmail($email)) {
                return $this->view('auth/signup', [
                    'error' => 'Email already exists',
                    'csrf_token' => $this->generateCSRFToken()
                ]);
            }
            
            // Create new user
            $data = [
                'id' => Uuid::uuid4()->toString(),
                'username' => $username,
                'password' => $password,
                'name' => $name,
                'email' => $email,
                'role' => $role
            ];
            
            if ($this->userModel->create($data)) {
                // Redirect to login page with success message
                $_SESSION['success'] = 'Account created successfully. Please login.';
                $this->redirect('/login');
            } else {
                return $this->view('auth/signup', [
                    'error' => 'Error creating account. Please try again.',
                    'csrf_token' => $this->generateCSRFToken()
                ]);
            }
        }
        
        return $this->view('auth/signup', [
            'csrf_token' => $this->generateCSRFToken()
        ]);
    }

    public function logout() {
        // Clear user cache if exists
        if (isset($_SESSION['user_id'])) {
            $this->cache->delete('user_' . $_SESSION['user_id']);
        }
        
        // Destroy all session data
        session_destroy();
        
        // Clear session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-3600, '/');
        }
        
        $this->redirect('/login');
    }

    public function unauthorized() {
        http_response_code(403);
    
    return $this->view('errors/403');
    }
}
