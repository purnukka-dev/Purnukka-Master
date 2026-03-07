<?php
/**
 * Purnukka_Core Class - MASTER Edition (v1.6.0)
 * Centralized logic for modules and admin UI.
 * Refactor: Added Dependency Injection and Whitelist Security.
 */

if (!defined('ABSPATH')) exit;

class Purnukka_Core {
    public $version = '1.6.0';
    public $config = [];
    private $config_path;
    public $active_modules = []; 
    public $modules = []; // Tänne tallennetaan alustetut oliot

    public function __construct() {
        // Dynaaminen polku konfiguraatiolle
        $this->config_path = WP_CONTENT_DIR . '/purnukka-config/context.json';
        
        $this->load_config();
        add_action('plugins_loaded', [$this, 'boot_modules'], 11);
        
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

    public function boot_modules() {
        if (empty($this->config['features'])) return;

        // PDF-ANALYYSI KOHTA 6: Sallitut moduulit (Whitelist)
        $whitelist = [
            'hub-sync', 'checkout-logic', 'tier-manager', 
            'branding', 'ai-connector', 'mail-connector', 
            'checkin-ui', 'upsell-ui', 'access-control'
        ];

        foreach ($this->config['features'] as $module => $enabled) {
            if ($enabled && in_array($module, $whitelist)) {
                $module_file = __DIR__ . "/modules/{$module}.php";
                if (file_exists($module_file)) {
                    include_once $module_file;
                    
                    // MUUTOS: Syötetään Core-instanssi ($this) moduulille (Dependency Injection)
                    $class_name = 'Purnukka_' . str_replace('-', '_', ucwords($module, '-'));
                    if (class_exists($class_name)) {
                        $this->modules[$module] = new $class_name($this);
                        $this->active_modules[] = $module;
                    }
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

        // PDF-ANALYYSI KOHTA 6: Varmistetaan whitelist ennen tallennusta
        $whitelist = ['hub-sync', 'checkout-logic', 'tier-manager', 'branding', 'ai-connector', 'mail-connector', 'checkin-ui', 'upsell-ui', 'access-control'];
        if (!in_array($feature, $whitelist)) {
            wp_send_json_error('Invalid module');
        }

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

    public function