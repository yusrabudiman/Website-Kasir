<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use Ramsey\Uuid\Uuid;

class UserController extends Controller {
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }

    public function index() {
        $this->checkAuth(['admin']);
        
        $users = $this->userModel->getAll();
        return $this->view('users/index', [
            'title' => 'User Management',
            'users' => $users
        ]);
    }

    public function create() {
        $this->checkAuth(['admin']);
        
        if ($this->isPost()) {
            $this->validateCSRF();
            
            $data = [
                'id' => Uuid::uuid4()->toString(),
                'name' => filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING),
                'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
                'username' => filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING),
                'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
                'role' => filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING)
            ];

            // Validate required fields
            if (empty($data['name']) || empty($data['email']) || empty($data['username']) || empty($_POST['password'])) {
                $_SESSION['flash_message'] = 'All required fields must be filled';
                $_SESSION['flash_type'] = 'error';
                return $this->redirect('/users/create');
            }

            // Check if username or email already exists
            if ($this->userModel->findByUsername($data['username'])) {
                $_SESSION['flash_message'] = 'Username already exists';
                $_SESSION['flash_type'] = 'error';
                return $this->redirect('/users/create');
            }

            if ($this->userModel->findByEmail($data['email'])) {
                $_SESSION['flash_message'] = 'Email already exists';
                $_SESSION['flash_type'] = 'error';
                return $this->redirect('/users/create');
            }

            try {
                $this->userModel->create($data);
                $this->logActivity('User created: ' . $data['username']);
                
                $_SESSION['flash_message'] = 'User created successfully';
                $_SESSION['flash_type'] = 'success';
                return $this->redirect('/users');
            } catch (\Exception $e) {
                $_SESSION['flash_message'] = 'Error creating user: ' . $e->getMessage();
                $_SESSION['flash_type'] = 'error';
                return $this->redirect('/users/create');
            }
        }

        return $this->view('users/create', [
            'title' => 'Create User',
            'csrf_token' => $this->generateCSRFToken()
        ]);
    }

    public function edit($id) {
        $this->checkAuth(['admin']);
        
        $user = $this->userModel->findById($id);
        
        if (!$user) {
            $_SESSION['flash_message'] = 'User not found';
            $_SESSION['flash_type'] = 'error';
            return $this->redirect('/users');
        }

        if ($this->isPost()) {
            $this->validateCSRF();
            
            $data = [
                'id' => $id,
                'name' => filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING),
                'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
                'role' => filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING)
            ];

            // Add password only if it's provided
            if (!empty($_POST['password'])) {
                $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }

            // Validate required fields
            if (empty($data['name']) || empty($data['email'])) {
                $_SESSION['flash_message'] = 'Name and email are required';
                $_SESSION['flash_type'] = 'error';
                return $this->redirect("/users/edit/{$id}");
            }

            // Check if email already exists (for another user)
            $existingUser = $this->userModel->findByEmail($data['email']);
            if ($existingUser && $existingUser->id !== $id) {
                $_SESSION['flash_message'] = 'Email already exists for another user';
                $_SESSION['flash_type'] = 'error';
                return $this->redirect("/users/edit/{$id}");
            }

            try {
                $this->userModel->update($data);
                $this->logActivity('User updated: ' . $user->username);
                
                $_SESSION['flash_message'] = 'User updated successfully';
                $_SESSION['flash_type'] = 'success';
                return $this->redirect('/users');
            } catch (\Exception $e) {
                $_SESSION['flash_message'] = 'Error updating user: ' . $e->getMessage();
                $_SESSION['flash_type'] = 'error';
                return $this->redirect("/users/edit/{$id}");
            }
        }

        return $this->view('users/edit', [
            'title' => 'Edit User',
            'user' => $user,
            'csrf_token' => $this->generateCSRFToken()
        ]);
    }

    public function delete($id) {
        $this->checkAuth(['admin']);
        
        if ($this->isPost()) {
            $this->validateCSRF();
            
            // Prevent deleting your own account
            if ($id === $_SESSION['user_id']) {
                $_SESSION['flash_message'] = 'You cannot delete your own account';
                $_SESSION['flash_type'] = 'error';
                return $this->redirect('/users');
            }

            try {
                $user = $this->userModel->findById($id);
                if (!$user) {
                    throw new \Exception('User not found');
                }
                
                $this->userModel->delete($id);
                $this->logActivity('User deleted: ' . $user->username);
                
                $_SESSION['flash_message'] = 'User deleted successfully';
                $_SESSION['flash_type'] = 'success';
            } catch (\Exception $e) {
                $_SESSION['flash_message'] = 'Error deleting user: ' . $e->getMessage();
                $_SESSION['flash_type'] = 'error';
            }
        }
        
        return $this->redirect('/users');
    }

    public function profile() {
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->findById($userId);
        
        if (!$user) {
            $_SESSION['flash_message'] = 'User not found';
            $_SESSION['flash_type'] = 'error';
            return $this->redirect('/dashboard');
        }

        if ($this->isPost()) {
            $this->validateCSRF();
            
            $data = [
                'id' => $userId,
                'name' => filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING),
                'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL)
            ];

            // Add password only if it's provided
            if (!empty($_POST['password'])) {
                // Verify current password
                if (!password_verify($_POST['current_password'], $user->password)) {
                    $_SESSION['flash_message'] = 'Current password is incorrect';
                    $_SESSION['flash_type'] = 'error';
                    return $this->redirect('/profile');
                }
                
                $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }

            // Validate required fields
            if (empty($data['name']) || empty($data['email'])) {
                $_SESSION['flash_message'] = 'Name and email are required';
                $_SESSION['flash_type'] = 'error';
                return $this->redirect('/profile');
            }

            // Check if email already exists (for another user)
            $existingUser = $this->userModel->findByEmail($data['email']);
            if ($existingUser && $existingUser->id !== $userId) {
                $_SESSION['flash_message'] = 'Email already exists for another user';
                $_SESSION['flash_type'] = 'error';
                return $this->redirect('/profile');
            }

            try {
                $this->userModel->update($data);
                $this->logActivity('Profile updated');
                
                // Update session data
                $_SESSION['user_name'] = $data['name'];
                $_SESSION['user_email'] = $data['email'];
                
                $_SESSION['flash_message'] = 'Profile updated successfully';
                $_SESSION['flash_type'] = 'success';
                return $this->redirect('/profile');
            } catch (\Exception $e) {
                $_SESSION['flash_message'] = 'Error updating profile: ' . $e->getMessage();
                $_SESSION['flash_type'] = 'error';
                return $this->redirect('/profile');
            }
        }

        return $this->view('users/profile', [
            'title' => 'My Profile',
            'user' => $user,
            'csrf_token' => $this->generateCSRFToken()
        ]);
    }
    
    private function logActivity($activity) {
        // Log user activity for audit trail
        if (method_exists($this, 'createAuditLog')) {
            $this->createAuditLog('user', $activity);
        }
    }
} 