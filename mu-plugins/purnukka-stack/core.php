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

        // NEW: Register AJAX handler for the Dashboard toggles
        add_action('wp_ajax_update_purnukka_feature', [$this, 'handle_feature_switch']);

        /** * API Sync - Disabled until Hub.purnukka.com is ready */
        // add_action('rest_api_init', [$this, 'register_api_routes']);
    }

    /**
     * AJAX Handler: Updates context.json when a toggle is flipped
     */
    public function handle_feature_switch() {
        if (!current_user_can('manage_options')) wp_send_json_error('Unauthorized');
        
        $feature = sanitize_text_field($_POST['feature']);
        $status  = $_POST['status'] === 'true'; 

        if (empty($this->config)) $this->load_config();

        // Update the feature status in memory
        $this->config['features'][$feature] = $status;

        // Write back to context.json
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

    public function register_api_routes() {
        register_rest_route('purnukka/v1', '/sync', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handle_config_sync'],
            'permission_callback' => [$this, 'verify_api_token'],
        ]);
    }

    public function verify_api_token($request) {
        $auth_header = $request->get_header('Authorization');
        if (!$auth_header) return false;
        $master_token = 'YOUR_SECRET_HUB_TOKEN'; 
        return $auth_header === 'Bearer ' . $master_token;
    }

    public function handle_config_sync($request) {
        $new_config = $request->get_json_params();
        if (empty($new_config)) {
            return new WP_Error('empty_config', 'No data received', ['status' => 400]);
        }
        $updated = file_put_contents(
            $this->config_path, 
            json_encode($new_config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
        if ($updated) {
            return new WP_REST_Response(['success' => true, 'message' => 'Config synced'], 200);
        }
        return new WP_Error('save_failed', 'Failed to write JSON', ['status' => 500]);
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

$GLOBALS['purnukka'] = new Purnukka_Core();