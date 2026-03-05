<?php
/**
 * Purnukka_Core Class - Robust Edition
 */

if (!defined('ABSPATH')) exit;

class Purnukka_Core {
    public $config = [];
    private $config_path;
    public $active_modules = []; // Tänne tallennetaan ladatut moduuli-instanssit

    public function __construct() {
        $this->config_path = WP_CONTENT_DIR . '/purnukka-config/context.json';
        
        $this->load_config();
        $this->boot_modules();
        
        add_action('admin_menu', [$this, 'register_admin_panel']);
        add_action('wp_ajax_update_purnukka_feature', [$this, 'handle_feature_switch']);
    }

    /**
     * Vahvistettu lataus: Estetään korruptoituneen JSONin aiheuttamat virheet
     */
    private function load_config() {
        if (!file_exists($this->config_path)) {
            $this->config = ['features' => []]; // Fallback
            return;
        }

        $json_data = file_get_contents($this->config_path);
        $decoded = json_decode($json_data, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            $this->config = $decoded;
        } else {
            // Jos JSON on rikki, logataan virhe ja käytetään tyhjää, ettei koodi kaadu
            error_log("Purnukka Error: context.json is corrupted.");
            $this->config = ['features' => []];
        }
    }

    /**
     * Moduulien hallittu käynnistys
     */
    private function boot_modules() {
        $features = $this->config['features'] ?? [];
        foreach ($features as $module => $enabled) {
            if ($enabled) {
                $module_file = __DIR__ . "/modules/{$module}.php";
                if (file_exists($module_file)) {
                    // Käytetään includea ja kääritään tarvittaessa, 
                    // jotta yksi moduuli ei kaada koko putkea.
                    include_once $module_file;
                    $this->active_modules[] = $module;
                }
            }
        }
    }

    /**
     * Vahvistettu kirjoitus: LOCK_EX estää samanaikaiset kirjoitukset
     */
    public function handle_feature_switch() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        $feature = sanitize_text_field($_POST['feature']);
        $status  = $_POST['status'] === 'true'; 

        $this->config['features'][$feature] = $status;

        // Kirjoitetaan tiedostoon lukituksella (LOCK_EX)
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
        include_once __DIR__ . '/views/admin-dashboard.php';
    }
}

// Global instance
$GLOBALS['purnukka'] = new Purnukka_Core();