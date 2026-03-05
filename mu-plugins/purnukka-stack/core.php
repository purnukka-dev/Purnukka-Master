<?php
if (!defined('ABSPATH')) exit;

/**
 * Purnukka Core - Keskusyksikkö.
 * TÄYSI VERSIO - EI KARSINTAA.
 */
class Purnukka_Core {
    public $config = [];
    public $context = [];
    public $modules = [];
    private static $instance = null;

    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        // Määritellään polut ja urlit heti, jotta assettien ja näkymien lataus onnistuu
        if (!defined('PURNUKKA_STACK_PATH')) {
            define('PURNUKKA_STACK_PATH', plugin_dir_path(__FILE__));
        }
        if (!defined('PURNUKKA_STACK_URL')) {
            define('PURNUKKA_STACK_URL', content_url('mu-plugins/purnukka-stack/'));
        }

        $this->load_config();
        
        // Asetetaan globaali muuttuja, jota views/admin-dashboard.php ja moduulit kutsuvat
        $GLOBALS['purnukka'] = $this;

        $this->init_modules();
        
        // Varmistetaan, että päävalikko ladataan
        add_action('admin_menu', array($this, 'add_admin_menu'), 1);
    }

    private function load_config() {
        $config_file = WP_CONTENT_DIR . '/purnukka-config/context.json';
        
        if (file_exists($config_file)) {
            $json_data = file_get_contents($config_file);
            $decoded = json_decode($json_data, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->config = $decoded;
                $this->context = $decoded; // Synkronointi molemmille nimille
                return;
            }
        }
        
        // Fail-safe jos tiedosto puuttuu tai on rikki
        $this->config = array('product' => array('tier' => 'Solo'), 'features' => array());
        $this->context = &$this->config;
    }

    private function init_modules() {
        $module_dir = PURNUKKA_STACK_PATH . 'modules/';
        if (!is_dir($module_dir)) return;

        $files = glob($module_dir . '*.php');
        foreach ($files as $file) {
            require_once $file;
            $base_name = basename($file, '.php');
            $class_name = 'Purnukka_' . str_replace(' ', '_', ucwords(str_replace('-', ' ', $base_name)));
            
            if (class_exists($class_name)) {
                // Alustetaan kaikki moduulit, jotta ne voivat rekisteröidä omat hookkinsa
                $this->modules[$base_name] = new $class_name($this);
            }
        }
    }

    public function add_admin_menu() {
        // Luodaan päävalikko, joka lataa dashboardin
        add_menu_page(
            'Purnukka Stack',
            'Purnukka',
            'manage_options',
            'purnukka-stack',
            array($this, 'render_dashboard'),
            'dashicons-admin-home',
            2
        );
    }

    public function render_dashboard() {
        // Tarjotaan data näkymälle täsmälleen siinä muodossa kuin se oli aamulla
        $config = $this->config;
        $view_file = PURNUKKA_STACK_PATH . 'views/admin-dashboard.php';
        
        if (file_exists($view_file)) {
            include $view_file;
        } else {
            echo '<div class="wrap"><h1>Purnukka Stack</h1><p>Näkymää ei löytynyt: ' . esc_html($view_file) . '</p></div>';
        }
    }

    public function get_config($key, $default = null) {
        $keys = explode('.', $key);
        $current = $this->config;
        foreach ($keys as $i_key) {
            if (!isset($current[$i_key])) return $default;
            $current = $current[$i_key];
        }
        return $current;
    }
}

// Käynnistys
Purnukka_Core::instance();