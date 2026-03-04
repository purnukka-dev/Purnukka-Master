<?php
/**
 * Module: AI Connector
 */
if (!defined('ABSPATH')) exit;

add_filter('mwai_ai_instructions', function($instructions, $query) {
    $config = $GLOBALS['purnukka']->config;
    
    $role = $config['ai_config']['role'] ?? "Digital Host";
    $address = $config['property_info']['address'] ?? "";
    $rules = json_encode($config['ai_rules'] ?? []);

    return "Role: $role. Property: $address. Rules: $rules. instructions: $instructions";
});