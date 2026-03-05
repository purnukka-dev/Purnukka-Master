<?php
if (!defined('ABSPATH')) exit;

/**
 * Purnukka Core - Keskusyksikkö joka hallitsee dataa ja moduuleita.
 * Versio: 1.5.0 - Solo-optimized generic core.
 */
class Purnukka_Core {
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
        $this->load_context();
        $this->init_modules();
    }

    /**
     * Lataa konfiguraation paikallisesta tiedostosta.
     * Tiedosto on Git-ignoroitu asiakasdata (purnukka-config/context.json).
     */
    private function load_context() {
        $config_file = WP_CONTENT_DIR . '/purnukka-config/context.json';
        
        if (file_exists($config_file)) {
            $json_content = file_get_contents($config_file);
            $this->context = json_decode($json_content, true);
        } else {
            // Turvamekanismi: Oletusarvot estävät virheet jos tiedosto puuttuu.
            $this->context = [
                'product' => ['tier' => 'Solo', 'version' => '1.5.0'],
                'features' => [],
                'property_info' => ['name' => 'Purnukka Instance'],
                'design_system' => [
                    'colors' => ['primary' => '#1a2b28', 'accent' => '#b89b5e', 'text' => '#ffffff']
                ]
            ];
        }
    }

    /**
     * Alustaa moduulit jos ne on kytketty päälle contextissa.
     */
    private function init_modules() {
        $module_files = glob(PURNUKKA_STACK_PATH . 'modules/*.php');
        
        foreach ($module_files as $file) {
            require_once $file;
            $base_name = basename($file, '.php');
            $class_name = 'Purnukka_' . str_replace(' ', '_', ucwords(str_replace('-', ' ', $base_name)));
            
            if (class_exists($class_name)) {
                // Moduuli ladataan vain jos se on merkitty 'true' features-listassa
                if (!empty($this->context['features'][$base_name])) {
                    $this->modules[$base_name] = new $class_name($this);
                }
            }
        }
    }

    /**
     * Hakee arvon context-rakenteesta polun perusteella.
     */
    public function get_context($key, $default = null) {
        return isset($this->context[$key]) ? $this->context[$key] : $default;
    }
}