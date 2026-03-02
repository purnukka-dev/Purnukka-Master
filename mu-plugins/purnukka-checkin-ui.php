<?php
/**
 * Plugin Name: Purnukka Check-in UI (Master Standard v1.2.9)
 * Description: Fixed UI logic to prevent both boxes from showing simultaneously.
 * Version: 1.2.9
 * Author: Purnukka Group Master
 */

if (!defined('ABSPATH')) exit;

add_shortcode('purnukka_checkin', function($atts) {
    $checkout_url = function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : site_url('/payment-checkout/');

    $a = shortcode_atts(array(
        'rate'       => '30',
        'min_stay'   => '2',
        'product_id' => '276',
        'form_id'    => '4', 
        'title'      => 'Welcome Home'
    ), $atts);

    ob_start(); ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        .p-master-wrapper { font-family: 'Montserrat', sans-serif; max-width: 1000px; margin: 0 auto 60px; padding: 0 20px; text-align: center; }
        
        /* THE GATE BOX (Phase 1) */
        .p-gate-production {
            background: #fff;
            border: 1px solid #1a2b28;
            border-left: 10px solid #b89b5e;
            padding: 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 30px 0 50px;
            text-align: left;
        }

        /* CALCULATOR CONTAINER (Phase 2) - EXPLICITLY HIDDEN */
        #calc-box-ui { 
            display: none !important; /* Force hide initially */
            text-align: left; 
            margin-top: 30px;
        }
        
        .p-calc-title { font-size: 18px; font-weight: 700; color: #1a2b28; margin-bottom: 25px; display: block; text-align: center; }
        .p-input-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 35px; }
        
        .p-field-box { border: 1px solid #dcdcdc; padding: 18px 25px; background: #fff; }
        .p-field-label { display: block; font-size: 10px; color: #888; text-transform: uppercase; font-weight: 700; margin-bottom: 8px; }
        .p-field-label i { margin-right: 10px; color: #b89b5e; font-size: 12px; }

        .p-field-box input { border: none; width: 100%; font-weight: 700; font-size: 28px; color: #1a2b28; outline: none; padding: 0; }

        .p-price-big { font-size: 64px; font-weight: 700; color: #1a2b28; display: block; line-height: 1; text-align: center; margin: 20px 0; }
        .p-btn-action { background: #b89b5e; color: #fff; border: none; padding: 24px; width: 100%; font-weight: 700; text-transform: uppercase; font-size: 13px; letter-spacing: 3px; cursor: pointer; }
        .p-btn-action:hover { background: #1a2b28; }

        @media (max-width: 600px) {
            .p-gate-production { flex-direction: column; text-align: center; gap: 25px; }
            .p-input-grid { grid-template-columns: 1fr; }
        }
    </style>

    <div id="p-master-app" 
         data-rate="<?php echo esc_attr($a['rate']); ?>" 
         data-min-stay="<?php echo esc_attr($a['min_stay']); ?>" 
         data-product-id="<?php echo esc_attr($a['product_id']); ?>"
         data-checkout-url="<?php echo esc_url($checkout_url); ?>">

        <div class="p-master-wrapper">
            <div style="text-align: center; padding: 40px 0;">
                <i class="fas fa-key" style="color: #b89b5e; font-size: 36px; margin-bottom: 20px;"></i>
                <h1 style="font-family: 'Playfair Display', serif; font-size: 32px; color: #1a2b28; border-bottom: 2px solid #b89b5e; display: inline-block; padding-bottom: 8px;">Check-in & Declaration</h1>
                <p style="font-size: 14px; color: #666; margin-top: 20px;">The mandatory traveler declaration ensures a safe stay and keeps your insurance coverage active.</p>
            </div>

            <div class="p-gate-production" id="gate-box-ui">
                <div>
                    <h3 style="margin: 0; font-size: 19px; color: #1a2b28;">Change in group size?</h3>
                    <p style="margin: 5px 0 0; color: #666; font-size: 14px;">Add and pay for additional guests here.</p>
                </div>
                <button class="p-btn-action" style="width: auto; padding: 15px 30px; font-size: 11px;" onclick="pShowCalc()">Add Guests</button>
            </div>

            <div id="calc-box-ui">
                <span class="p-calc-title">Add guests to booking</span>
                
                <div class="p-input-grid">
                    <div class="p-field-box">
                        <label><i class="fa-solid fa-users"></i> Additional Guests</label>
                        <input type="number" id="in-g-val" value="1" min="1" oninput="pRecalc()">
                    </div>
                    <div class="p-field-box">
                        <label><i class="fa-solid fa-moon"></i> Nights</label>
                        <input type="number" id="in-n-val" value="<?php echo esc_attr($a['min_stay']); ?>" min="<?php echo esc_attr($a['min_stay']); ?>" oninput="pRecalc()">
                    </div>
                </div>

                <div style="text-align: center; margin-bottom: 30px;">
                    <span id="rate-note-ui" style="font-size: 11px; text-transform: uppercase; color: #b89b5e; font-weight: 700;">Standard Rate</span>
                    <span class="p-price-big"><span id="sum-val-ui">0</span> €</span>
                </div>

                <button class="p-btn-action" onclick="pGoToPay()">Update & Pay Now</button>
                <p onclick="location.reload()" style="text-align: center; cursor: pointer; color: #bbb; font-size: 10px; margin-top: 25px; text-transform: uppercase;">Cancel</p>
            </div>

            <div style="margin-top: 80px; text-align: left;">
                <?php echo do_shortcode('[formidable id=' . esc_attr($a['form_id']) . ']'); ?>
            </div>
        </div>
    </div>

    <script>
    function pShowCalc() {
        // Hide gate, show calculator
        document.getElementById('gate-box-ui').style.setProperty('display', 'none', 'important');
        document.getElementById('calc-box-ui').style.setProperty('display', 'block', 'important');
        pRecalc();
    }

    function pRecalc() {
        const app = document.getElementById('p-master-app');
        const base = parseInt(app.getAttribute('data-rate'));
        const g = parseInt(document.getElementById('in-g-val').value) || 0;
        let n = parseInt(document.getElementById('in-n-val').value) || 0;
        const minN = parseInt(app.getAttribute('data-min-stay'));
        
        if (n < minN) n = minN;

        let r = base;
        let txt = "STANDARD RATE (" + base + "€/NIGHT)";
        
        if (n > 2 && n <= 6) { r = 20; txt = "MID-TERM RATE (20€/NIGHT)"; }
        else if (n > 6 && n <= 13) { r = 15; txt = "WEEKLY RATE (15€/NIGHT)"; }
        else if (n >= 14) { r = 10; txt = "LONG-STAY RATE (10€/NIGHT)"; }

        document.getElementById('sum-val-ui').innerText = g * n * r;
        document.getElementById('rate-note-ui').innerText = txt;
    }

    function pGoToPay() {
        const app = document.getElementById('p-master-app');
        const url = app.getAttribute('data-checkout-url');
        const pid = app.getAttribute('data-product-id');
        const sum = document.getElementById('sum-val-ui').innerText;
        const sep = url.includes('?') ? '&' : '?';
        window.location.href = url + sep + 'add-to-cart=' + pid + '&quantity=' + sum;
    }
    </script>

    <?php
    return ob_get_clean();
});