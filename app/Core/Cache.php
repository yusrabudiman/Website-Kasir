<?php
namespace App\Core;

class Cache {
    private static $instance = null;
    private $cache = [];
    private $prefix = 'app_cache_';
    
    private function __construct() {
        // Create cache directory if it doesn't exist
        $cacheDir = __DIR__ . '/../../storage/cache';
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function set($key, $value, $expiry = 3600) {
        $cacheKey = $this->prefix . $key;
        $data = [
            'value' => $value,
            'expiry' => time() + $expiry,
            'created_at' => time()
        ];
        
        // Save to memory cache
        $this->cache[$cacheKey] = $data;
        
        // Save to file cache with more persistent storage
        $cacheDir = __DIR__ . '/../../storage/cache';
        $cacheFile = $cacheDir . '/' . $cacheKey . '.cache';
        
        // Create a more robust file storage
        $storageData = [
            'data' => $data,
            'last_accessed' => time(),
            'access_count' => 1
        ];
        
        file_put_contents($cacheFile, serialize($storageData), LOCK_EX);
        
        return true;
    }
    
    public function get($key) {
        $cacheKey = $this->prefix . $key;
        
        // Check memory cache first
        if (isset($this->cache[$cacheKey])) {
            $data = $this->cache[$cacheKey];
            if ($data['expiry'] > time()) {
                return $data['value'];
            }
            // Remove expired cache
            $this->delete($key);
            return null;
        }
        
        // Check file cache
        $cacheFile = __DIR__ . '/../../storage/cache/' . $cacheKey . '.cache';
        if (file_exists($cacheFile)) {
            $storageData = unserialize(file_get_contents($cacheFile));
            $data = $storageData['data'];
            
            if ($data['expiry'] > time()) {
                // Update access information
                $storageData['last_accessed'] = time();
                $storageData['access_count']++;
                file_put_contents($cacheFile, serialize($storageData), LOCK_EX);
                
                // Store in memory cache
                $this->cache[$cacheKey] = $data;
                return $data['value'];
            }
            // Remove expired cache
            $this->delete($key);
        }
        
        return null;
    }
    
    public function delete($key) {
        $cacheKey = $this->prefix . $key;
        
        // Remove from memory cache
        unset($this->cache[$cacheKey]);
        
        // Remove from file cache
        $cacheFile = __DIR__ . '/../../storage/cache/' . $cacheKey . '.cache';
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }
        
        return true;
    }
    
    public function clear() {
        // Clear memory cache
        $this->cache = [];
        
        // Clear file cache
        $cacheDir = __DIR__ . '/../../storage/cache';
        if (is_dir($cacheDir)) {
            $files = glob($cacheDir . '/' . $this->prefix . '*.cache');
            foreach ($files as $file) {
                unlink($file);
            }
        }
        
        return true;
    }
    
    public function recoverSession($userId) {
        $cacheKey = 'user_' . $userId;
        $userData = $this->get($cacheKey);
        
        if ($userData) {
            // Check if the session is still valid (within 24 hours)
            if (time() - $userData['last_activity'] < 86400) {
                // Restore session data
                $_SESSION['user_id'] = $userData['id'];
                $_SESSION['user_name'] = $userData['name'];
                $_SESSION['user_role'] = $userData['role'];
                $_SESSION['user_email'] = $userData['email'];
                
                // Update last activity
                $userData['last_activity'] = time();
                $this->set($cacheKey, $userData, 3600);
                
                return true;
            }
        }
        
        return false;
    }
} 