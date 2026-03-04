<?php
/**
 * Purnukka_Core Class
 * The central brain of the Purnukka Stack.
 * Handles module loading and provides the foundation for Hub synchronization.
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

        /** * API Sync - Disabled until Hub.purnukka.com is ready
         * To enable, uncomment the line below and set your YOUR_SECRET_HUB_TOKEN
         */
        // add_action('rest_api_init', [$this, 'register_api_routes']);
    }

    /**
     * Load settings from local JSON file (Property Context)
     */
    private function load_config() {
        if (file_exists($this->config_path)) {
            $json_data = file_get_contents($this->config_path);
            $this->config = json_decode($json_data, true);
        }
    }

    /**
     * Boot enabled feature modules dynamically
     */
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

    /**
     * Register REST API endpoints (Ready for future Hub sync)
     */
    public function register_api_routes() {
        register_rest_route('purnukka/v1', '/sync', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handle_config_sync'],
            'permission_callback' => [$this, 'verify_api_token'],
        ]);
    }

    /**
     * Security check: Verify Bearer Token from the Hub
     */
    public function verify_api_token($request) {
        $auth_header = $request->get_header('Authorization');
        if (!$auth_header) return false;

        $master_token = 'YOUR_SECRET_HUB_TOKEN'; 
        return $auth_header === 'Bearer ' . $master_token;
    }

    /**
     * Update context.json when the Hub pushes new data
     */
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
            return new WP_REST_Response([
                'success' => true,
                'message' => 'Config synced',
                'property' => $new_config['property_info']['name'] ?? 'Unknown'
            ], 200);
        }

        return new WP_Error('save_failed', 'Failed to write JSON', ['status' => 500]);
    }

    public function register_admin_panel() {
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
        // Points to the new modular view path
        include_once __DIR__ . '/views/admin-dashboard.php';
    }
}

// Global instance to make the engine accessible to modules
$GLOBALS['purnukka'] = new Purnukka_Core();