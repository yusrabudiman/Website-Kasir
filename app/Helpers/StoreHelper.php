<?php
namespace App\Helpers;

use App\Models\StoreSetting;

class StoreHelper {
    private static $instance = null;
    private $settings = null;
    private $storeName = null;
    private $currencySymbol = null;
    private $lowStockThreshold = null;

    private function __construct() {
        $settingModel = new StoreSetting();
        $this->settings = $settingModel->getSettings();
        $this->storeName = $this->settings ? $this->settings->store_name : 'POS System';
        $this->currencySymbol = $this->settings ? $this->settings->currency_symbol : 'Rp';
        $this->lowStockThreshold = $this->settings ? $this->settings->low_stock_threshold : 10;
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getStoreName() {
        return $this->storeName;
    }

    public function getCurrencySymbol() {
        return $this->currencySymbol;
    }

    public function getLowStockThreshold() {
        return $this->lowStockThreshold;
    }

    public function getSettings() {
        return $this->settings;
    }

    public function refresh() {
        $settingModel = new StoreSetting();
        $this->settings = $settingModel->getSettings();
        $this->storeName = $this->settings ? $this->settings->store_name : 'POS System';
        $this->currencySymbol = $this->settings ? $this->settings->currency_symbol : 'Rp';
        $this->lowStockThreshold = $this->settings ? $this->settings->low_stock_threshold : 10;
    }
} 