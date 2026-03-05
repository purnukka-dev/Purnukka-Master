<?php
/**
 * Purnukka_Core Class - Hub-Ready Edition
 * Säilyttää eiliset toiminnot ja lisää API-valmiuden hub.purnukka.com -synkronointiin.
 */

if (!defined('ABSPATH')) exit;

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
        
        // Rekisteröidään API-päätepiste Hub-synkronointia varten
        add_action('rest_api_init', [$this, 'register_hub_api']);
    }

    /**
     * Ladataan nykyinen konfiguraatio
     */
    private function load_config() {
        if (!file_exists($this->config_path)) {
            $this->config = ['features' => [], 'product' => ['tier' => 'Solo']];
            return;
        }

        $json_data = file_get_contents($this->config_path);
        $decoded = json_decode($json_data, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            $this->config = $decoded;
        } else {
            error_log("Purnukka Error: context.json is corrupted.");
            $this->config = ['features' => [], 'product' => ['tier' => 'Solo']];
        }
    }

    /**
     * Moduulien suojattu käynnistys (Pomminvarma try-catch)
     */
    private function boot_modules() {
        $features = $this->config['features'] ?? [];
        foreach ($features as $module => $enabled) {
            if ($enabled) {
                $module_file = __DIR__ . "/modules/{$module}.php";
                if (file_exists($module_file)) {
                    try {
                        include_once $module_file;
                        $class_name = 'Purnukka_' . str_replace(' ', '_', ucwords(str_replace('-', ' ', $module)));
                        
                        if (class_exists($class_name)) {
                            new $class_name($this);
                            $this->active_modules[] = $module;
                        }
                    } catch (Throwable $e) {
                        error_log("Purnukka Module Load Fail ($module): " . $e->getMessage());
                    }
                }
            }
        }
    }

    /**
     * Rekisteröidään REST API Hubia varten
     * Endpoint: /wp-json/purnukka/v1/sync
     */
    public function register_hub_api() {
        register_rest_route('purnukka/v1', '/sync', [
            'methods' => 'POST',
            'callback' => [$this, 'handle_hub_sync'],
            'permission_callback' => [$this, 'verify_hub_token'],
        ]);
    }

    /**
     * Varmistetaan Hub-pyynnön oikeellisuus (Tämä on laajennettavissa API-avaimella)
     */
    public function verify_hub_token($request) {
        // Tähän voidaan lisätä myöhemmin hub_api_key tarkistus
        return current_user_can('manage_options') || defined('PURNUKKA_HUB_SYNC');
    }

    /**
     * Käsitellään Hubista tuleva JSON-syöte
     */
    public function handle_hub_sync($request) {
        $new_data = $request->get_json_params();
        
        if (empty($new_data)) {
            return new WP_Error('no_data', 'Hub sent empty JSON', ['status' => 400]);
        }

        // Yhdistetään uusi data olemassa olevaan, jotta ei ylikirjoiteta tärkeitä paikallisia tietoja
        $this->config = array_replace_recursive($this->config, $new_data);

        $success = file_put_contents(
            $this->config_path, 
            json_encode($this->config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            LOCK_EX 
        );

        if ($success !== false) {
            return new WP_REST_Response(['message' => 'Synkronointi onnistui', 'tier' => $this->config['product']['tier']], 200);
        } else {
            return new WP_Error('write_error', 'Disk write error during sync', ['status' => 500]);
        }
    }

    /**
     * AJAX-käsittelijä admin-dashboardin kytkimille (Säilytetty ennallaan)
     */
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
        include_once __DIR__ . '/views/admin-dashboard.php';
    }
}

// Käynnistys
new Purnukka_Core();