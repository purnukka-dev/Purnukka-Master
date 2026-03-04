<?php
/**
 * Module: Upsell & Cart UI (v1.5 MOTOPRESS ADAPTED)
 * Description: Fetches cross-sells from products in the cart, including Motopress bookings.
 */

if (!defined('ABSPATH')) exit;

add_shortcode('purnukka_upsell', function() {
    if (is_admin() || !function_exists('WC') || WC()->cart->is_empty()) return '';

    $config = $GLOBALS['purnukka']->config;
    $primary_color = $config['design_system']['colors']['primary'] ?? '#1a2b28';
    $accent_color  = $config['design_system']['colors']['accent'] ?? '#b89b5e';

    $cross_sell_ids = [];
    $cart_items_map = [];

    // Loop through cart items to find cross-sells set in WooCommerce
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        $product = $cart_item['data'];
        $product_id = $cart_item['product_id'];
        $cart_items_map[$product_id] = $cart_item_key;
        
        // This picks up cross-sells from the "Purnukka Flex" OR the Motopress Room Product
        $linked = $product->get_cross_sell_ids();
        if (!empty($linked)) {
            $cross_sell_ids = array_merge($cross_sell_ids, $linked);
        }
    }

    $unique_ids = array_unique($cross_sell_ids);
    if (empty($unique_ids)) return ''; 

    ob_start(); ?>
    <div class="purnukka-upsell-container" style="border:1px solid <?php echo $primary_color; ?>; padding:20px; background:#fff; margin-bottom:30px;">
        <h3 style="font-family:'Playfair Display',serif; color:<?php echo $primary_color; ?>; margin-top:0;">Enhance Your Stay</h3>
        <p style="font-size:12px; color:#666; margin-bottom:15px;">Add extra comfort to your arrival at <?php echo esc_html($config['property_info']['name']); ?>.</p>
        
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:15px;">
            <?php foreach ($unique_ids as $id): 
                $product = wc_get_product($id);
                if (!$product) continue;
                $in_cart = isset($cart_items_map[$id]);
            ?>
                <div style="border:1px solid #eee; padding:15px; text-align:center;">
                    <span style="display:block; font-weight:bold; font-size:14px;"><?php echo $product->get_name(); ?></span>
                    <span style="color:<?php echo $accent_color; ?>;"><?php echo $product->get_price_html(); ?></span>
                    
                    <div style="margin-top:10px;">
                        <?php if ($in_cart): ?>
                            <span style="color:green; font-size:10px; display:block;">✓ SELECTED</span>
                            <a href="<?php echo esc_url(wc_get_cart_remove_url($cart_items_map[$id])); ?>" style="color:red; font-size:9px; text-decoration:underline;">Remove</a>
                        <?php else: ?>
                            <a href="?add-to-cart=<?php echo $id; ?>" style="background:<?php echo $primary_color; ?>; color:<?php echo $accent_color; ?>; padding:5px 10px; text-decoration:none; font-size:11px; font-weight:bold; display:block;">Add to stay</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
});