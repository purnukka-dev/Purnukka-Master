<?php
/**
 * Plugin Name: Purnukka Stack - AI Tier Controller
 * Description: Dynamically overrides AI instructions based on context.json tier and rules.
 * Version: 0.5
 */

add_filter( 'mwai_ai_instructions', function( $instructions, $query ) {
    $config_path = WP_CONTENT_DIR . '/purnukka-config/context.json';
    
    if ( file_exists( $config_path ) ) {
        $json_data = file_get_contents( $config_path );
        $config = json_decode( $json_data, true );
        
        // Extracting data from our new English JSON structure
        $tier = $config['product']['tier'] ?? "Solo";
        $role = $config['ai_config']['role'] ?? "Hospitality Expert";
        $tone = $config['ai_config']['tone'] ?? "Professional";
        
        // Custom rules for the specific property
        $max_guests = $config['limits']['max_guests'] ?? "Check listing details";
        $address = $config['property_info']['address'] ?? "Finland";

        // THE OVERRIDE: This builds the prompt based on the specific installation
        return "Role: $role. 
                Tone: $tone.
                Service Tier: Purnukka $tier Edition.
                Property Address: $address. 
                Mandatory Capacity: $max_guests guests. 
                Instructions: Always prioritize direct booking benefits. 
                Signature: Powered by Purnukka Stack.";
    }

    return $instructions;
}, 999, 2 );