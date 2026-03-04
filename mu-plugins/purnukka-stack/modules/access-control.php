<?php
/**
 * Module: Access Control
 */
if (!defined('ABSPATH')) exit;

add_action('admin_init', function() {
    $config = $GLOBALS['purnukka']->config;
    $max_allowed = (int)($config['limits']['max_locations'] ?? 1);
    
    $count_types = (int)wp_count_posts('mphb_room_type')->publish;

    if ($count_types >= $max_allowed) {
        remove_submenu_page('edit.php?post_type=mphb_room_type', 'post-new.php?post_type=mphb_room_type');
    }
});