<?php
if (!defined('ABSPATH')) exit;

/**
 * Purnukka AI Connector - Pomminvarma versio.
 */
class Purnukka_Ai_Connector {
    private $core;

    public function __construct($core) {
        $this->core = $core;
        
        // Lukko: Tarkistetaan onko moduuli kytketty päälle
        if (!$this->is_active()) return;

        add_action('wp_enqueue_scripts', [$this, 'register_ai_host_assets']);
    }

    private function is_active() {
        $features = $this->core->get_context('features', []);
        return !empty($features['ai-connector']);
    }

    /**
     * Hakee AI-asetukset turvallisesti.
     */
    public function get_ai_config() {
        $config = $this->core->get_context('ai_config', []);
        $rules = $this->core->get_context('ai_rules', []);

        // Pomminvarma oletus: Jos asetuksia ei ole, palautetaan turvallinen fallback
        return [
            'model' => !empty($config['model']) ? $config['model'] : 'Gemini 1.5 Flash',
            'role' => !empty($config['role']) ? $config['role'] : 'Digital Host',
            'system_instruction' => !empty($config['system_instruction']) ? $config['system_instruction'] : 'Help guests with basic info.',
            'rules' => $rules
        ];
    }

    public function register_ai_host_assets() {
        // Tähän tulee vain JS/CSS lataus, ei raskaata logiikkaa sivuille
    }
}