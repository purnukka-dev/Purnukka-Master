<?php
/**
 * Module: Visual Branding (v1.5 FULL PORT)
 * Description: Injects custom admin styles, colors, and tier badges based on the global config.
 */

if (!defined('ABSPATH')) exit;

add_action('admin_head', function() {
    // Access global Purnukka instance
    $config = $GLOBALS['purnukka']->config;
    
    // Extract colors from the design system configuration
    $primary_color = $config['design_system']['colors']['primary'] ?? '#1a2b28';
    $accent_color  = $config['design_system']['colors']['accent'] ?? '#b89b5e';

    echo "<style>
        /* WP ADMIN: Top bar and menu background */
        #wpadminbar, #adminmenu, #adminmenu .wp-submenu, 
        #adminmenuback, #adminmenuwrap { 
            background-color: $primary_color !important; 
        }

        /* WP ADMIN: Active menu items and primary buttons */
        #adminmenu li.current a.menu-top, 
        #adminmenu li.wp-has-current-submenu a.wp-has-current-submenu,
        .wp-core-ui .button-primary { 
            background: $accent_color !important; 
            border-color: $accent_color !important;
            color: $primary_color !important; 
            text-shadow: none !important;
        }

        /* Ohjaamo specific UI elements (v1.5) */
        .v-badge { 
            background: $accent_color !important; 
            color: $primary_color !important; 
            padding: 2px 8px; 
            border-radius: 4px; 
            font-weight: bold; 
        }
        
        .tier-tag { 
            color: $accent_color !important; 
            font-weight: bold; 
            text-transform: uppercase; 
            font-size: 10px; 
            letter-spacing: 1px; 
        }

        /* Login page branding (Optional) */
        .login h1 a { 
            background-image: none, url('" . esc_url($config['property_info']['logo_url'] ?? '') . "') !important;
            background-size: contain !important;
            width: 100% !important;
        }
    </style>";
});