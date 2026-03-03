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
/**
 * PHASE 1: MASTER CONTROL PANEL
 * Creates a centralized settings page for branding and configuration.
 * All UI labels, variables, and logic are in English.
 */

// 1. Register the Purnukka Stack menu in the WordPress sidebar
add_action('admin_menu', function() {
    add_menu_page(
        'Purnukka Settings',          // Browser tab title
        'Purnukka Stack',             // Sidebar menu label
        'manage_options',             // Access level (Admins only)
        'purnukka-settings',          // Unique URL slug
        'render_purnukka_settings_page', // UI render function
        'dashicons-admin-generic',    // Cog icon
        2                             // Top-level position
    );
});

// 2. Render the Settings Page Content
function render_purnukka_settings_page() {
    ?>
    <div class="wrap">
        <h1 style="color: #c5a059; font-weight: bold;">Purnukka Stack – Master Control Panel</h1>
        <p>Global configuration for branding, colors, and API integrations.</p>
        <hr>
        
        <form method="post" action="options.php">
            <?php 
                settings_fields('purnukka-settings-group'); 
                do_settings_sections('purnukka-settings-group'); 
            ?>
            
            <table class="form-table">
                <tr valign="top">
                    <th scope="row" style="width: 200px;">Property Brand Name</th>
                    <td>
                        <input type="text" name="purnukka_brand_name" 
                               value="<?php echo esc_attr(get_option('purnukka_brand_name', 'Villa Purnukka')); ?>" 
                               class="regular-text" />
                        <p class="description">Global name used in emails and frontend texts.</p>
                    </td>
                </tr>
                
                <tr valign="top">
                    <th scope="row">Primary Brand Color</th>
                    <td>
                        <input type="color" name="purnukka_primary_color" 
                               value="<?php echo esc_attr(get_option('purnukka_primary_color', '#c5a059')); ?>" />
                        <p class="description">Main accent color for buttons, icons, and UI elements.</p>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Logo Image URL</th>
                    <td>
                        <input type="text" name="purnukka_logo_url" 
                               value="<?php echo esc_attr(get_option('purnukka_logo_url')); ?>" 
                               class="regular-text" />
                        <p class="description">Direct link to the PNG/SVG logo file.</p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button('Update Purnukka Stack'); ?>
        </form>
    </div>
    <?php
}

// 3. Initialize and whitelist settings in the database
add_action('admin_init', function() {
    register_setting('purnukka-settings-group', 'purnukka_brand_name');
    register_setting('purnukka-settings-group', 'purnukka_primary_color');
    register_setting('purnukka-settings-group', 'purnukka_logo_url');
});
new PurnukkaStackCore();
// Updated deployment for the new tier structure - 2026-02-28