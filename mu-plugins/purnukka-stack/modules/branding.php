<?php
/**
 * Module: Visual Branding
 */
if (!defined('ABSPATH')) exit;

add_action('admin_head', function() {
    $config = $GLOBALS['purnukka']->config;
    $bg = $config['design_system']['colors']['primary'] ?? '#1a2b28';
    $accent = $config['design_system']['colors']['accent'] ?? '#b89b5e';

    echo "<style>
        #wpadminbar, #adminmenu, #adminmenu .wp-submenu, #adminmenuback, #adminmenuwrap { background-color: $bg !important; }
        #adminmenu li.current a.menu-top, .wp-core-ui .button-primary { background: $accent !important; border-color: $accent !important; color: $bg !important; }
        .tier-tag { background: $accent; color: $bg; padding: 2px 6px; border-radius: 3px; font-size: 10px; }
    </style>";
});