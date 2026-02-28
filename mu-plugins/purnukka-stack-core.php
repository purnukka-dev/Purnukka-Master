<?php
/**
 * Plugin Name: Purnukka Stack - Core Branding (v0.4)
 * Description: Handles white-labeling, admin footer, and dynamic CSS branding based on the English context.json.
 * Author: Purnukka Group Oy
 * Version: 0.4
 */

if ( !defined('ABSPATH') ) exit;

class PurnukkaStackCore {
    private $context = null;

    public function __construct() {
        $this->load_context();
        
        // Admin-side branding
        if (is_admin()) {
            add_filter('admin_footer_text', [$this, 'customize_admin_footer']);
            add_action('wp_before_admin_bar_render', [$this, 'remove_wp_logo'], 0);
        }

        // Front-end and Editor branding
        add_action('wp_head', [$this, 'inject_dynamic_branding'], 10);
        add_action('enqueue_block_editor_assets', [$this, 'inject_editor_branding']);
    }

    private function load_context() {
        $config_path = WP_CONTENT_DIR . '/purnukka-config/context.json';
        if ( file_exists( $config_path ) ) {
            $json = file_get_contents( $config_path );
            $this->context = json_decode( $json, true );
        }
    }

    /**
     * Inject brand colors as CSS variables.
     */
    public function get_branding_css() {
        if (!$this->context || !isset($this->context['design_system']['colors'])) return '';

        $colors = $this->context['design_system']['colors'];
        
        return "
        :root {
            --purnukka-primary: " . esc_attr($colors['primary']) . ";
            --purnukka-secondary: " . esc_attr($colors['secondary']) . ";
            --purnukka-text: " . esc_attr($colors['text']) . ";
            --purnukka-accent: " . esc_attr($colors['accent']) . ";
        }
        /* Override Booklium/Gutenberg buttons and backgrounds */
        .wp-block-button__link, .mphb-book-button {
            background-color: var(--purnukka-secondary) !important;
            color: var(--purnukka-text) !important;
        }
        body { background-color: var(--purnukka-primary); color: var(--purnukka-text); }
        ";
    }

    public function inject_dynamic_branding() {
        echo '<style id="purnukka-dynamic-css">' . $this->get_branding_css() . '</style>';
    }

    public function inject_editor_branding() {
        wp_add_inline_style('wp-edit-blocks', $this->get_branding_css());
    }

    public function customize_admin_footer() {
        // Fallbacks if context keys are missing
        $brand_name = $this->context['product']['name'] ?? 'Purnukka Stack';
        $tier = $this->context['product']['tier'] ?? 'Solo';

        return sprintf(
            '<strong>%s</strong> | <span style="color: #666;">Edition: %s</span>',
            esc_html($brand_name),
            esc_html($tier)
        );
    }

    public function remove_wp_logo() {
        global $wp_admin_bar;
        $wp_admin_bar->remove_menu('wp-logo');
    }
}

new PurnukkaStackCore();
// Updated deployment for the new tier structure - 2026-02-28