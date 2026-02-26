<?php
/**
 * Plugin Name: Purnukka Stack - Core Branding (v0.2)
 * Description: Handles general white-labeling, admin footer and property identity.
 * Author: Purnukka Group Oy
 */

if ( !defined('ABSPATH') ) exit;

class PurnukkaStackCore {
    private $context;

    public function __construct() {
        $this->load_context();
        
        if (is_admin()) {
            add_filter('admin_footer_text', [$this, 'customize_admin_footer']);
            add_action('wp_before_admin_bar_render', [$this, 'remove_wp_logo'], 0);
        }
    }

    private function load_context() {
        $config_path = WP_CONTENT_DIR . '/purnukka-config/context.json';
        if ( file_exists( $config_path ) ) {
            $json = file_get_contents( $config_path );
            $this->context = json_decode( $json, true );
        }
    }

    public function customize_admin_footer() {
        $brand = $this->context['property_info']['brand_footer'] ?? 'Powered by Purnukka';
        $package = $this->context['stack_limits']['package_level'] ?? 'Standard';

        return sprintf(
            '<strong>%s</strong> | <span style="color: #666;">Plan: %s</span>',
            esc_html($brand),
            esc_html($package)
        );
    }

    public function remove_wp_logo() {
        global $wp_admin_bar;
        $wp_admin_bar->remove_menu('wp-logo');
    }
}

new PurnukkaStackCore();