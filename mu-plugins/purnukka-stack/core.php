<?php
/**
 * Purnukka_Core Class
 * The central brain of the Purnukka Stack.
 */

if (!defined('ABSPATH')) exit;

class Purnukka_Core {
    public $config;
    private $config_path;

    public function __construct() {
        $this->config_path = WP_CONTENT_DIR . '/purnukka-config/context.json';
        $this->load_config();
        $this->boot_modules();
        
        // Register Admin Panel
        add_action('admin_menu', [$this, 'register_admin_panel']);

        // MASTER SWITCH: This listens for the AJAX signal from your Dashboard
        add_action('wp_ajax_update_purnukka_feature', [$this, 'handle_feature_switch']);
    }

    /**
     * AJAX Handler: Saves toggle state directly to context.json
     */
    public function handle_feature_switch() {
        // Security check
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        $feature = sanitize_text_field($_POST['feature']);
        $status  = $_POST['status'] === 'true'; 

        // Ensure config is loaded
        if (empty($this->config)) {
            $this->load_config();
        }

        // Update memory and write to file
        $this->config['features'][$feature] = $status;

        $updated = file_put_contents(
            $this->config_path, 
            json_encode($this->config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        if ($updated) {
            wp_send_json_success('Configuration updated');
        } else {
            wp_send_json_error('Failed to write to context.json');
        }
    }

    private function load_config() {
        if (file_exists($this->config_path)) {
            $json_data = file_get_contents($this->config_path);
            $this->config = json_decode($json_data, true);
        }
    }

    private function boot_modules() {
        $features = $this->config['features'] ?? [];
        foreach ($features as $module => $enabled) {
            if ($enabled) {
                $module_file = __DIR__ . "/modules/{$module}.php";
                if (file_exists($module_file)) {
                    require_once $module_file;
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
        include_once __DIR__ . '/views/admin-dashboard.php';
    }
}

// Global instance
$GLOBALS['purnukka'] = new Purnukka_Core();