<?php
/**
 * Purnukka_Core Class - Robust Edition (v1.5.1)
 * Centralized logic for modules and admin UI.
 */

if (!defined('ABSPATH')) exit;

class Purnukka_Core {
    public $config = [];
    private $config_path;
    public $active_modules = []; 

    public function __construct() {
        // Dynaaminen polku konfiguraatiolle
        $this->config_path = WP_CONTENT_DIR . '/purnukka-config/context.json';
        
        $this->load_config();
        $this->boot_modules();
        
        // Prioriteetti 1: Keskistetty valikkorekisteröinti
        add_action('admin_menu', [$this, 'register_admin_panel'], 9);
        add_action('wp_ajax_update_purnukka_feature', [$this, 'handle_feature_switch']);
    }

    private function load_config() {
        if (!file_exists($this->config_path)) {
            $this->config = ['features' => []];
            return;
        }

        $json_data = file_get_contents($this->config_path);
        $decoded = json_decode($json_data, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            $this->config = $decoded;
        } else {
            error_log("Purnukka Error: context.json is corrupted.");
            $this->config = ['features' => []];
        }
    }

    private function boot_modules() {
        $features = $this->config['features'] ?? [];
        foreach ($features as $module => $enabled) {
            if ($enabled) {
                $module_file = __DIR__ . "/modules/{$module}.php";
                if (file_exists($module_file)) {
                    include_once $module_file;
                    $this->active_modules[] = $module;
                }
            }
        }
    }

    public function handle_feature_switch() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        $feature = sanitize_text_field($_POST['feature']);
        $status  = $_POST['status'] === 'true'; 

        $this->config['features'][$feature] = $status;

        $success = file_put_contents(
            $this->config_path, 
            json_encode($this->config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            LOCK_EX 
        );

        if ($success !== false) {
            wp_send_json_success(['message' => 'Config updated', 'module' => $feature]);
        } else {
            wp_send_json_error('Disk write error');
        }
    }

    public function register_admin_panel() {
        // Päävalikko, jonka alle moduulit voivat myöhemmin rekisteröityä
        add_menu_page(
            'Purnukka', 
            'Purnukka', 
            'manage_options', 
            'purnukka-stack', 
            [$this, 'render_dashboard'], 
            'dashicons-admin-generic', 
            2
        );
    }

    public function render_dashboard() {
        // Hyödynnetään määritettyä polkuvakiota
        $view_file = PURNUKKA_STACK_PATH . 'views/admin-dashboard.php';
        if (file_exists($view_file)) {
            include_once $view_file;
        }
    }
}

// Global instance pidetään ennallaan
$GLOBALS['purnukka'] = new Purnukka_Core();