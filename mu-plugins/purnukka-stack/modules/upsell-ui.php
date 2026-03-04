<?php
/**
 * Module: Upsell & Cart UI (v1.5 PORT)
 * Description: Handles "Enhance Your Stay" cross-sell products and cart removal links on checkout.
 * Ported from: purnukka-stack-checkout-logic.php
 */

if (!defined('ABSPATH')) exit;

add_shortcode('purnukka_upsell', function() {
    // Only show on frontend and if WooCommerce is active
    if (is_admin() || !function_exists('WC') || WC()->cart->is_empty()) return '';

    $config = $GLOBALS['purnukka']->config;
    $primary_color = $config['design_system']['colors']['primary'] ?? '#1a2b28';
    $accent_color  = $config['design_system']['colors']['accent'] ?? '#b89b5e';

    // List of Upsell Product IDs (Add more here if needed)
    $upsell_ids = [280, 281]; // Example IDs for linens, cleaning etc.
    
    // Map current cart items to check what's already added
    $cart_items_map = [];
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        $cart_items_map[$cart_item['product_id']] = $cart_item_key;
    }

    ob_start(); ?>
    <div class="purnukka-upsell-container" style="margin-bottom:30px; font-family:'Montserrat',sans-serif;">
        <h3 style="font-family:'Playfair Display',serif; color:<?php echo $primary_color; ?>; border-bottom:1px solid <?php echo $accent_color; ?>; padding-bottom:10px;">Enhance Your Stay</h3>
        
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:15px; margin-top:15px;">
            <?php foreach ($upsell_ids as $id): 
                $product = wc_get_product($id);
                if (!$product) continue;
                $is_in_cart = isset($cart_items_map[$id]);
            ?>
                <div style="border:1px solid #eee; padding:15px; text-align:center; position:relative;">
                    <strong style="display:block; font-size:14px; color:<?php echo $primary_color; ?>;"><?php echo $product->get_name(); ?></strong>
                    <span style="color:<?php echo $accent_color; ?>; font-weight:bold;"><?php echo $product->get_price_html(); ?></span>
                    
                    <div style="margin-top:10px;">
                        <?php if ($is_in_cart): ?>
                            <span style="color:#27ae60; font-size:10px; text-transform:uppercase; font-weight:bold; display:block;">✓ Added to stay</span>
                            <a href="<?php echo esc_url(wc_get_cart_remove_url($cart_items_map[$id])); ?>" 
                               style="color:#e74c3c; font-size:10px; text-decoration:underline; text-transform:uppercase;">Remove</a>
                        <?php else: ?>
                            <a href="?add-to-cart=<?php echo $id; ?>" 
                               style="background:<?php echo $primary_color; ?>; color:<?php echo $accent_color; ?>; padding:5px 15px; text-decoration:none; font-size:11px; font-weight:bold; display:inline-block; border-radius:3px;">Add to stay</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
});