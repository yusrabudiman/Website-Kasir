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
            'expiry' => time() + $expiry
        ];
        
        // Save to memory cache
        $this->cache[$cacheKey] = $data;
        
        // Save to file cache
        $cacheDir = __DIR__ . '/../../storage/cache';
        $cacheFile = $cacheDir . '/' . $cacheKey . '.cache';
        file_put_contents($cacheFile, serialize($data));
        
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
            $data = unserialize(file_get_contents($cacheFile));
            if ($data['expiry'] > time()) {
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
} 