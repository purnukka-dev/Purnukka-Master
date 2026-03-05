<?php
if (!defined('ABSPATH')) exit;

/**
 * Purnukka_Core Class - Pomminvarma Edition
 * Säilyttää eiliset toiminnot ja lisää suojauksen moduulien lataukseen.
 */
class Purnukka_Core {
    public $config = [];
    private $config_path;
    public $active_modules = [];

    public function __construct() {
        // Asetetaan globaali olio heti alussa näkymiä varten
        $GLOBALS['purnukka'] = $this;

        $this->config_path = WP_CONTENT_DIR . '/purnukka-config/context.json';
        
        $this->load_config();
        $this->boot_modules();
        
        add_action('admin_menu', [$this, 'register_admin_panel']);
        add_action('wp_ajax_update_purnukka_feature', [$this, 'handle_feature_switch']);
    }

    /**
     * Ladataan eilinen JSON-rakenne
     */
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

    /**
     * Moduulien suojattu käynnistys
     */
    private function boot_modules() {
        $features = $this->config['features'] ?? [];
        foreach ($features as $module => $enabled) {
            if ($enabled) {
                $module_file = __DIR__ . "/modules/{$module}.php";
                if (file_exists($module_file)) {
                    try {
                        //include_once suojatussa lohkossa
                        include_once $module_file;
                        
                        // Muodostetaan luokan nimi (esim. ai-connector -> Purnukka_Ai_Connector)
                        $class_name = 'Purnukka_' . str_replace(' ', '_', ucwords(str_replace('-', ' ', $module)));
                        
                        if (class_exists($class_name)) {
                            new $class_name($this);
                            $this->active_modules[] = $module;
                        }
                    } catch (Throwable $e) {
                        // Jos moduuli on rikki, logataan virhe mutta WP ei kaadu
                        error_log("Purnukka Module Load Fail ($module): " . $e->getMessage());
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
            'Purnukka', 'Purnukka', 'manage_options', 'purnukka-stack', 
            [$this, 'render_dashboard'], 'dashicons-admin-generic', 2
        );
    }

    public function render_dashboard() {
        // Ladataan eilinen näkymä
        include_once __DIR__ . '/views/admin-dashboard.php';
    }
}

// Käynnistys
new Purnukka_Core();