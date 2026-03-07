<?php
/**
 * Module: AI Connector (v1.6.0 MASTER)
 * Refactor: Constructor Injection. Poistettu riippuvuus $GLOBALS muuttujasta.
 */

if (!defined('ABSPATH')) exit;

class Purnukka_Ai_Connector {
    private $core;

    public function __construct($core) {
        if (!$core) return;
        $this->core = $core;

        // SÄILYTETTY: Alkuperäinen filtteri, mutta kytketty luokan metodiin
        add_filter('mwai_ai_instructions', [$this, 'inject_ai_instructions'], 10, 2);
    }

    public function inject_ai_instructions($instructions, $query) {
        // SÄILYTETTY: Alkuperäinen datan haku coren konfiguraatiosta
        $config = $this->core->config;
        
        $role = $config['ai_config']['role'] ?? "Digital Host";
        $address = $config['property_info']['address'] ?? "";
        $rules = json_encode($config['ai_rules'] ?? []);

        // SÄILYTETTY: Alkuperäinen palautusmuoto
        return "Role: $role. Property: $address. Rules: $rules. instructions: $instructions";
    }
}