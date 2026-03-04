<?php
/**
 * Module: Upsell & Cart UI (v1.5 MOTOPRESS ADAPTED)
 * Description: Fetches cross-sells and protects MotoPress-WC integration.
 */

if (!defined('ABSPATH')) exit;

// 1. UI LUKITUS: Suojellaan MotoPress -> WC -linkitystä
add_action('admin_footer', function() {
    $screen = get_current_screen();
    if ($screen && $screen->post_type === 'mphb_service') {
        ?>
        <script>
        jQuery(document).ready(function($) {
            // Lukitaan pudotusvalikko, jolla MPHB-palvelu on kytketty WC-tuotteeseen
            $('select[name="_mphb_woocommerce_product_id"]').prop('disabled', true).css('background', '#f0f0f1');
            
            $('.mphb-service-settings').prepend(
                '<div class="notice notice-info"><p><strong>Purnukka Master:</strong> This service-product link is managed by the Hub to ensure booking stability.</p></div>'
            );
        });
        </script>
        <?php
    }
});

// 2. SHORTCODE: [purnukka_upsell]
add_shortcode('purnukka_upsell', function() {
    if (is_admin() || !function_exists('WC') || WC()->cart->is_empty()) return '';

    $config = $GLOBALS['purnukka']->config;
    $primary_color = $config['design_system']['colors']['primary'] ?? '#1a2b28';
    $accent_color  = $config['design_system']['colors']['accent'] ?? '#b89b5e';

    $cross_sell_ids = [];
    $cart_items_map = [];

    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        $product = $cart_item['data'];
        $product_id = $cart_item['product_id'];
        $cart_items_map[$product_id] = $cart_item_key;
        
        $linked = $product->get_cross_sell_ids();
        if (!empty($linked)) {
            $cross_sell_ids = array_merge($cross_sell_ids, $linked);
        }
    }

    $unique_ids = array_unique($cross_sell_ids);
    if (empty($unique_ids)) return ''; 

    ob_start(); ?>
    <div class="purnukka-upsell-container" style="border:1px solid <?php echo $primary_color; ?>; padding:20px; background:#fff; margin-bottom:30px; border-radius: 4px;">
        <h3 style="font-family: serif; color:<?php echo $primary_color; ?>; margin-top:0;">Enhance Your Stay</h3>
        <p style="font-size:12px; color:#666; margin-bottom:15px;">Add extra comfort to your arrival at <?php echo esc_html($config['property_info']['name'] ?? 'the villa'); ?>.</p>
        
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:15px;">
            <?php foreach ($unique_ids as $id): 
                $product = wc_get_product($id);
                if (!$product || !$product->is_in_stock()) continue;
                $in_cart = isset($cart_items_map[$id]);
            ?>
                <div class="upsell-item" style="border:1px solid #eee; padding:15px; text-align:center; display:flex; flex-direction:column; justify-content:space-between;">
                    <div>
                        <span style="display:block; font-weight:bold; font-size:14px; margin-bottom:5px;"><?php echo $product->get_name(); ?></span>
                        <span style="color:<?php echo $accent_color; ?>; font-weight:600;"><?php echo $product->get_price_html(); ?></span>
                    </div>
                    
                    <div style="margin-top:15px;">
                        <?php if ($in_cart): ?>
                            <span style="color:green; font-size:11px; font-weight:bold; display:block; margin-bottom:5px;">✓ SELECTED</span>
                            <a href="<?php echo esc_url(wc_get_cart_remove_url($cart_items_map[$id])); ?>" style="color:#ba2d2d; font-size:10px; text-decoration:none; text-transform:uppercase;">Remove</a>
                        <?php else: ?>
                            <?php 
                            // Luodaan AJAX-yhteensopiva lisäyslinkki, joka ohjaa takaisin kassalle
                            $add_url = esc_url(add_query_arg('add-to-cart', $id, wc_get_checkout_url())); 
                            ?>
                            <a href="<?php echo $add_url; ?>" style="background:<?php echo $primary_color; ?>; color:<?php echo $accent_color; ?>; padding:8px 10px; text-decoration:none; font-size:11px; font-weight:bold; display:block; border-radius:2px;">Add to stay</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
});