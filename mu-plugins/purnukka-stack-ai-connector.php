<?php
/**
 * Plugin Name: Purnukka Stack - AI Connector (v0.2)
 * Description: Connects the AI engine to the property-specific configuration from context.json.
 * Author: Purnukka Group Oy
 */

if ( !defined('ABSPATH') ) exit;

add_filter( 'mwai_ai_query', function( $query ) {
    $config_path = WP_CONTENT_DIR . '/purnukka-config/context.json';
    
    if ( file_exists( $config_path ) ) {
        $json_data = file_get_contents( $config_path );
        $config = json_decode( $json_data, true );
        
        // Extract basic info with fallbacks
        $name = $config['property_info']['name'] ?? 'Property';
        $brand = $config['property_info']['brand_footer'] ?? 'Powered by Purnukka';
        
        // Build the system instructions for the AI
        $instruction = "\n\n--- PROPERTY CONTEXT ---\n";
        $instruction .= "You are the digital host for $name.\n";
        $instruction .= "Please use the following property rules and information in your responses:\n";
        
        // Loop through the AI rules defined in context.json
        if ( isset($config['ai_rules']) && is_array($config['ai_rules']) ) {
            foreach ( $config['ai_rules'] as $key => $value ) {
                $instruction .= "- " . ucfirst(str_replace('_', ' ', $key)) . ": $value\n";
            }
        }
        
        $instruction .= "\nSignature: $brand";

        // Append this context to the existing AI instructions
        $query->set_instruction( $query->get_instruction() . $instruction );
    }
    
    return $query;
}, 10, 1 );