<?php
/**
 * Module: AI Connector
 * Description: Connects property rules and system instructions to the AI engine.
 */

if (!defined('ABSPATH')) exit;

class Purnukka_AI_Connector {
    public function __construct() {
        // Hooks into the AI prompt generation or settings
        add_filter('purnukka_ai_system_prompt', [$this, 'get_dynamic_system_prompt']);
    }

    /**
     * Build a system prompt based on property info and rules from context.json
     */
    public function get_dynamic_system_prompt($default_prompt) {
        $config = $GLOBALS['purnukka']->config;
        
        $role = $config['ai_config']['role'] ?? 'Digital Host';
        $rules = $config['ai_rules'] ?? [];
        $property = $config['property_info']['name'] ?? 'The Villa';

        $instruction = "You are $role. ";
        $instruction .= "Rules for $property: ";
        
        foreach ($rules as $key => $value) {
            $label = ucfirst(str_replace('_', ' ', $key));
            $instruction .= "$label: $value. ";
        }

        return $instruction . ($config['ai_config']['system_instruction'] ?? '');
    }
}

new Purnukka_AI_Connector();