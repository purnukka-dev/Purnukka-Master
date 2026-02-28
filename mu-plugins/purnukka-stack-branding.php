<?php
/**
 * Plugin Name: Purnukka Stack - Visual Branding
 * Description: Injects custom colors (Dark Green & Gold) from context.json to WP Admin.
 * Version: 0.4
 */

add_action( 'admin_head', function() {
    $config_path = WP_CONTENT_DIR . '/purnukka-config/context.json';
    if ( !file_exists( $config_path ) ) return;
    
    $config = json_decode( file_get_contents( $config_path ), true );
    
    // Poimitaan värit kuvasi perusteella
    $bg = $config['design_system']['colors']['primary'] ?? '#1a2b28'; // Tummanvihreä
    $accent = $config['design_system']['colors']['accent'] ?? '#b89b5e'; // Kulta

    echo "<style>
        /* Yläpalkki ja valikon tausta */
        #wpadminbar, #adminmenu, #adminmenu .wp-submenu, 
        #adminmenuback, #adminmenuwrap { 
            background-color: $bg !important; 
        }
        /* Aktiivinen valikko ja napit kultaiseksi */
        #adminmenu li.current a.menu-top, 
        .wp-core-ui .button-primary { 
            background: $accent !important; 
            border-color: $accent !important;
            color: $bg !important; 
        }
        /* Tekstin värit */
        #adminmenu a, #wpadminbar #wp-admin-bar-site-name > a { 
            color: #ffffff !important; 
        }
    </style>";
});