<?php
if (!defined('ABSPATH')) exit;

/**
 * Purnukka Core - Keskusyksikkö.
 * Version: 1.5.2 - Global Object Sync.
 */
class Purnukka_Core {
    public $config = []; // Käytetään tätä moduulien yhteensopivuuteen
    public $context = []; // Fallback
    public $active_modules = [];

    public function __construct() {
        $this->load_config();
        $this->boot_modules();
        
        add_action('admin_menu', [$this, 'register_admin_panel']);
        add_action('wp_ajax_update_purnukka_feature', [$this, 'handle_feature_switch']);
    }

    private function load_config() {
        $config_file = WP_CONTENT_DIR . '/purnukka-config/context.json';
        
        if (file_exists($config_file)) {
            $json_data = file_get_contents($config_file);
            $decoded = json_decode($json_data, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->config = $decoded;
                $this->context = $decoded; // Synkataan molemmat
                return;
            }
        }
        
        // Fail-safe jos tiedosto puuttuu
        $this->config = ['features' => []];
        $this->context = &$this->config;
    }

    private function boot_modules() {
        $features = $this->config['features'] ?? [];
        foreach ($features as $module => $enabled) {
            if ($enabled) {
                $module_file = __DIR__ . "/modules/{$module}.php";
                if (file_exists($module_file)) {
                    include_once $module_file;
                    $this->active_modules[] = $module;
                    
                    // Jos moduuli on luokka, alustetaan se
                    $class_name = 'Purnukka_' . str_replace(' ', '_', ucwords(str_replace('-', ' ', $module)));
                    if (class_exists($class_name)) {
                        new $class_name($this);
                    }
                }
            }
        }
    }

    public function register_admin_panel() {
        add_menu_page(
            'Purnukka', 'Purnukka', 'manage_options', 'purnukka-stack', 
            [$this, 'render_dashboard'], 'dashicons-admin-generic', 2
        );
    }

    public function render_dashboard() {
        $view = __DIR__ . '/views/admin-dashboard.php';
        if (file_exists($view)) {
            include_once $view;
        } else {
            echo "Dashboard view missing.";
        }
    }

    // Helpperi syvälle JSON-hakuun (estää kaatumisen jos avain puuttuu)
    public function get_context($path, $default = null) {
        $current = $this->config;
        $keys = explode('.', $path);
        foreach ($keys as $key) {
            if (!isset($current[$key])) return $default;
            $current = $current[$key];
        }
        return $current;
    }
}

// ALUSTUS: Tämä on se mitä moduulit tarvitsevat
$GLOBALS['purnukka'] = new Purnukka_Core();