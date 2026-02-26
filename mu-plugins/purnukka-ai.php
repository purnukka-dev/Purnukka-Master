<?php
/**
 * Plugin Name: Purnukka Stack - AI Connector Template
 * Description: Geneerinen liitin AI-moottorin ja kohdekohtaisen konfiguraation välille.
 */

add_filter( 'mwai_ai_query', function( $query ) {
    $config_path = WP_CONTENT_DIR . '/purnukka-config/context.json';
    
    if ( file_exists( $config_path ) ) {
        $config = json_decode( file_get_contents( $config_path ), true );
        
        $name = $config['property_info']['name'] ?? 'Majoituskohde';
        $brand = $config['property_info']['brand_footer'] ?? 'Powered by Purnukka';
        
        $instruction = "\n\nOlet kohteen $name digitaalinen isäntä.\n";
        $instruction .= "Käytä vastauksissasi kohteen sääntöjä:\n";
        
        foreach ( ($config['ai_rules'] ?? []) as $key => $value ) {
            $instruction .= "- " . ucfirst($key) . ": $value\n";
        }
        
        $instruction .= "\nAllekirjoitus: $brand";

        $query->set_instruction( $query->get_instruction() . $instruction );
    }
    return $query;
}, 10, 1 );