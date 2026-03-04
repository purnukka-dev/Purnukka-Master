<?php
/**
 * Module: Branding
 * Purpose: Inject property-specific logos and colors into WP Admin and Frontend.
 */

add_action('wp_head', 'p_apply_frontend_branding');
add_action('admin_head', 'p_apply_admin_branding');

function p_apply_frontend_branding() {
    $config = $GLOBALS['purnukka']->config;
    $primary = $config['colors']['primary'] ?? '#1a2b28';
    
    echo "<style>:root { --purnukka-brand: {$primary}; }</style>";
}

function p_apply_admin_branding() {
    $config = $GLOBALS['purnukka']->config;
    $logo = $config['branding']['logo_url'] ?? '';
    
    if ($logo) {
        echo "<style>
            #adminmenu::before {
                content: '';
                display: block;
                height: 60px;
                background: url('{$logo}') no-content center center;
                background-size: contain;
                margin: 10px;
            }
        </style>";
    }
}