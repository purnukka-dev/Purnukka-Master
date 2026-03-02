<?php
/**
 * Plugin Name: Purnukka Check-in UI (Master Standard v1.2.5)
 * Description: High-contrast Production style with Master Standard English logic.
 * Version: 1.2.5
 * Author: Purnukka Group Master
 */

if (!defined('ABSPATH')) exit;

add_shortcode('purnukka_checkin', function($atts) {
    // 1. DYNAMIC ROUTING
    $checkout_url = function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : site_url('/payment-checkout/');

    // 2. MASTER ATTRIBUTES
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
        .p-master-wrapper { 
            font-family: 'Montserrat', sans-serif; 
            max-width: 1000px; 
            margin: 0 auto 60px; 
            padding: 0 20px; 
        }

        /* PRODUCTION STYLE BOX (The Gate) */
        .p-production-gate {
            background: #fff;
            border: 1px solid #1a2b28;
            border-left: 8px solid #b89b5e; /* Gold accent from production */
            padding: 35px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 40px;
            text-align: left;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        /* INPUT CARDS WITH ICONS & CONTRAST */
        .p-input-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .p-input-card {
            background: #f8f8f8; /* Contrast background */
            border: 1px solid #e0e0e0;
            padding: 20px 25px;
            border-radius: 2px;
            text-align: left;
            transition: border-color 0.3s;
        }

        .p-input-card:focus-within {
            border-color: #b89b5e;
        }

        .p-input-card label {
            display: block;
            font-size: 10px;
            color: #b89b5e;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }

        .p-input-card label i {
            margin-right: 8px;
            font-size: 12px;
        }

        .p-input-card input {
            border: none;
            background: transparent;
            width: 100%;
            font-weight: 700;
            font-size: 26px;
            color: #1a2b28;
            outline: none;
            padding: 0;
        }

        /* BUTTONS */
        .p-btn-dark-small {
            background: #1a2b28;
            color: #fff;
            border: none;
            padding: 16px 28px;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 1px;
            cursor: pointer;
            transition: 0.3s;
        }
        .p-btn-dark-small:hover { background: #b89b5e; }

        .p-btn-gold-large {
            background: #b89b5e;
            color: #fff;
            border: none;
            padding: 22px;
            width: 100%;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 2px;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 20px;
            box-shadow: 0 4px 15px rgba(184, 155, 94, 0.3);
        }
        .p-btn-gold-large:hover { background: #1a2b28; transform: translateY(-2px); }

        /* TOTAL DISPLAY */
        .p-total-wrap {
            text-align: center;
            padding: 40px 0;
        }
        .p-total-amount {
            font-size: 56px;
            font-weight: 700;
            color: #1a2b28;
            display: block;
            line-height: 1;
        }
        .p-total-note {
            font-size: 11px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 10px;
            display: block;
        }

        @media (max-width: 650px) {
            .p-production-gate { flex-direction: column; text-align: center; gap: 20px; }
            .p-input-grid { grid-template-columns: 1fr; }
        }
    </style>

    <div id="p-master-app" 
         data-rate="<?php echo esc_attr($a['rate']); ?>" 
         data-min-stay="<?php echo esc_attr($a['min_stay']); ?>" 
         data-product-id="<?php echo esc_attr($a['product_id']); ?>"
         data-checkout-url="<?php echo esc_url($checkout_url); ?>">

        <div class="p-master-wrapper">
            <div style="text-align: center; padding: 60px 0 50px;">
                <span style="font-size: 10px; text-transform: uppercase; letter-spacing: 6px; color: #b89b5e; font-weight: 700;">Purnukka Group</span>
                <h1 style="font-family: 'Playfair Display', serif; font-size: 38px; margin: 15px 0; color: #1a2b28;"><?php echo esc_html($a['title']); ?></h1>
                <div style="width: 50px; height: 1px; background: #b89b5e; margin: 25px auto;"></div>
            </div>

            <div class="p-production-gate" id="p-gate-ui">
                <div>
                    <h3 style="margin: 0; font-size: 19px; color: #1a2b28;">Change in group size?</h3>
                    <p style="margin: 5px 0 0; color: #666; font-size: 14px;">Update your booking and pay for additional guests.</p>
                </div>
                <button class="p-btn-dark-small" onclick="revealCalc()">Add Guests</button>
            </div>

            <div id="p-calc-ui" style="display: none; animation: pFade 0.5s ease;">
                <div class="p-input-grid">
                    <div class="p-input-card">
                        <label><i class="fa-solid fa-users"></i> Additional Guests</label>
                        <input type="number" id="p-guests-val" value="1" min="1" oninput="updateMasterSum()">
                    </div>
                    <div class="p-input-card">
                        <label><i class="fa-solid fa-moon"></i> Nights</label>
                        <input type="number" id="p-nights-val" value="<?php echo esc_attr($a['min_stay']); ?>" min="<?php echo esc_attr($a['min_stay']); ?>" oninput="updateMasterSum()">
                    </div>
                </div>

                <div class="p-total-wrap">
                    <span id="p-rate-note" class="p-total-note">Standard Rate</span>
                    <span class="p-total-amount"><span id="p-final-sum">0</span> €</span>
                </div>

                <button class="p-btn-gold-large" onclick="redirectToCheckout()">Update & Pay Now</button>
                <p onclick="location.reload()" style="text-align: center; cursor: pointer; color: #bbb; font-size: 10px; margin-top: 25px; text-transform: uppercase; letter-spacing: 1px;">Cancel / Back</p>
            </div>

            <div style="margin-top: 80px; border-top: 1px solid #eee; padding-top: 60px;">
                <?php echo do_shortcode('[formidable id=' . esc_attr($a['form_id']) . ']'); ?>
            </div>
        </div>
    </div>

    <script>
    function revealCalc() {
        document.getElementById('p-gate-ui').style.display = 'none';
        document.getElementById('p-calc-ui').style.display = 'block';
        updateMasterSum();
    }

    function updateMasterSum() {
        const app = document.getElementById('p-master-app');
        const baseRate = parseInt(app.getAttribute('data-rate'));
        const g = parseInt(document.getElementById('p-guests-val').value) || 0;
        let n = parseInt(document.getElementById('p-nights-val').value) || 0;
        const minN = parseInt(app.getAttribute('data-min-stay'));
        
        if (n < minN) n = minN;

        let rate = baseRate;
        let note = "STANDARD RATE (" + baseRate + "€/NIGHT)";
        
        if (n > 2 && n <= 6) { rate = 20; note = "MID-TERM DISCOUNT (20€/NIGHT)"; }
        else if (n > 6 && n <= 13) { rate = 15; note = "WEEKLY DISCOUNT (15€/NIGHT)"; }
        else if (n >= 14) { rate = 10; note = "LONG-STAY DISCOUNT (10€/NIGHT)"; }

        document.getElementById('p-final-sum').innerText = g * n * rate;
        document.getElementById('p-rate-note').innerText = note;
    }

    function redirectToCheckout() {
        const app = document.getElementById('p-master-app');
        const url = app.getAttribute('data-checkout-url');
        const pid = app.getAttribute('data-product-id');
        const sum = document.getElementById('p-final-sum').innerText;
        
        if (parseInt(sum) > 0) {
            const sep = url.includes('?') ? '&' : '?';
            window.location.href = url + sep + 'add-to-cart=' + pid + '&quantity=' + sum;
        }
    }
    </script>

    <?php
    return ob_get_clean();
});