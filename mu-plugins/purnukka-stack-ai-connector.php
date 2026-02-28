<?php
/**
 * Plugin Name: Purnukka Stack - True Override
 */

add_filter( 'mwai_ai_instructions', function( $instructions, $query ) {
    $config_path = WP_CONTENT_DIR . '/purnukka-config/context.json';
    
    if ( file_exists( $config_path ) ) {
        $json_data = file_get_contents( $config_path );
        $config = json_decode( $json_data, true );
        
        // Haetaan luku 99 sieltä JSONista
        $max = $config['ai_rules']['max_guests'] ?? "15";
        $addr = $config['ai_rules']['address'] ?? "Finland";

        // TÄMÄ YLIKIRJOITTAA DASHBOARDIN "10" -OHJEEN
        return "You are the Digital Host for Villa Purnukka. 
                Address: $addr. 
                MANDATORY CAPACITY: $max. 
                Answer in Finnish. 
                Signature: Powered by Purnukka.";
    }

    return $instructions;
}, 999, 2 );
// Connection verified