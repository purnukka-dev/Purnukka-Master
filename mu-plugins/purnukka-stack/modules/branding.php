<?php
if (!defined('ABSPATH')) exit;

/**
 * Purnukka Branding - Hallitsee visuaalista ilmettä muuttujilla.
 */
class Purnukka_Branding {
    private $core;

    public function __construct($core) {
        $this->core = $core;
        
        add_action('wp_head', [$this, 'inject_brand_styles'], 10);
        add_action('login_head', [$this, 'inject_brand_styles'], 10);
        add_filter('admin_footer_text', [$this, 'update_admin_footer']);
    }

    /**
     * Injektoi CSS-muuttujat context.json perusteella.
     * Käyttää JSON-rakenteen design_system-lohkoa.
     */
    public function inject_brand_styles() {
        $design = $this->core->get_context('design_system', []);
        $colors = isset($design['colors']) ? $design['colors'] : [];
        $branding = isset($design['branding']) ? $design['branding'] : [];

        // Hakee arvot JSONista tai käyttää Master-oletuksia
        $primary = !empty($colors['primary']) ? $colors['primary'] : '#1a2b28';
        $accent  = !empty($colors['accent'])  ? $colors['accent']  : '#b89b5e';
        $text    = !empty($colors['text'])    ? $colors['text']    : '#ffffff';
        $logo    = !empty($branding['logo_url']) ? $branding['logo_url'] : '';

        echo "<style id='purnukka-branding-css'>
            :root {
                --purnukka-primary: {$primary};
                --purnukka-accent: {$accent};
                --purnukka-text: {$text};
            }
            " . ($logo ? "
            .login h1 a { 
                background-image: url('{$logo}') !important; 
                background-size: contain !important; 
                width: 100% !important; 
                height: 80px !important;
            }" : "") . "
        </style>";
    }

    /**
     * Päivittää admin-footerin property_info-datalla.
     */
    public function update_admin_footer() {
        $info = $this->core->get_context('property_info', []);
        $footer_text = !empty($info['brand_footer']) ? $info['brand_footer'] : 'Powered by Purnukka Stack';
        echo '<span id="purnukka-footer">' . esc_html($footer_text) . '</span>';
    }
}