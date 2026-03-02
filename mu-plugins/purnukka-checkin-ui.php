<?php
/**
 * Plugin Name: Purnukka Check-in UI (Production Perfect)
 * Description: 1:1 Visual match with production, including icons and typography.
 * Version: 1.2.6
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
        .p-master-wrapper { 
            font-family: 'Montserrat', sans-serif; 
            max-width: 1000px; 
            margin: 0 auto 60px; 
            padding: 0 20px; 
            text-align: center;
        }

        /* HEADER SECTION */
        .p-header-main { padding: 60px 0 30px; }
        .p-brand-tag { font-size: 10px; text-transform: uppercase; letter-spacing: 5px; color: #b89b5e; font-weight: 700; }
        .p-title-main { font-family: 'Playfair Display', serif; font-size: 42px; margin: 15px 0; color: #1a2b28; }
        .p-gold-divider { width: 40px; height: 1px; background: #b89b5e; margin: 20px auto; }

        /* THE GATE BOX (High Contrast) */
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

        /* CALCULATOR GRID - MATCHES PRODUCTION */
        .p-calc-container { text-align: left; display: none; }
        .p-calc-title { font-size: 18px; font-weight: 700; color: #1a2b28; margin-bottom: 25px; display: block; }
        
        .p-input-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 35px; }
        
        .p-field-box {
            border: 1px solid #dcdcdc; /* Exact production gray */
            padding: 18px 25px;
            background: #fff;
            border-radius: 0px; /* Production uses sharp corners */
        }

        .p-field-label {
            display: block;
            font-size: 10px;
            color: #888;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .p-field-label i { margin-right: 10px; color: #666; font-size: 12px; }

        .p-field-box input {
            border: none;
            width: 100%;
            font-weight: 700;
            font-size: 28px;
            color: #1a2b28;
            outline: none;
            padding: 0;
        }

        /* PRICE DISPLAY */
        .p-price-wrap { text-align: center; padding: 20px 0 40px; }
        .p-rate-small { font-size: 11px; text-transform: uppercase; color: #b89b5e; font-weight: 700; letter-spacing: 1px; }
        .p-price-big { font-size: 64px; font-weight: 700; color: #1a2b28; display: block; line-height: 1; margin-top: 10px; }

        /* BUTTONS */
        .p-btn-action {
            background: #b89b5e;
            color: #fff;
            border: none;
            padding: 24px;
            width: 100%;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 3px;
            cursor: pointer;
            transition: 0.3s;
        }
        .p-btn-action:hover { background: #1a2b28; }
        
        .p-cancel-link {
            display: block;
            margin-top: 30px;
            font-size: 11px;
            color: #bbb;
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 2px;
            cursor: pointer;
        }

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
            
            <div class="p-header-main">
                <span class="p-brand-tag">Purnukka Group</span>
                <h1 class="p-title-main"><?php echo esc_html($a['title']); ?></h1>
                <div class="p-gold-divider"></div>
                <p style="font-size: 15px; color: #666;">Please complete your check-in and traveler declaration.</p>
            </div>

            <div class="p-gate-production" id="gate-box">
                <div>
                    <h3 style="margin: 0; font-size: 19px; color: #1a2b28;">Change in group size?</h3>
                    <p style="margin: 5px 0 0; color: #666; font-size: 14px;">Add and pay for missing persons here.</p>
                </div>
                <button class="p-btn-action" style="width: auto; padding: 15px 30px; font-size: 11px;" onclick="showCalculator()">Add Guests</button>
            </div>

            <div class="p-calc-container" id="calc-box">
                <span class="p-calc-title">Add guests to booking</span>
                
                <div class="p-input-grid">
                    <div class="p-field-box">
                        <label><i class="fa-solid fa-users"></i> Additional Guests (QTY)</label>
                        <input type="number" id="in-g" value="1" min="1" oninput="runMasterCalc()">
                    </div>
                    <div class="p-field-box">
                        <label><i class="fa-solid fa-moon"></i> Nights (QTY)</label>
                        <input type="number" id="in-n" value="<?php echo esc_attr($a['min_stay']); ?>" min="<?php echo esc_attr($a['min_stay']); ?>" oninput="runMasterCalc()">
                    </div>
                </div>

                <div class="p-price-wrap">
                    <span id="rate-label" class="p-rate-small">Standard Rate</span>
                    <span class="p-price-big"><span id="total-val">0</span> €</span>
                </div>

                <button class="p-btn-action" onclick="goToPay()">Update & Pay Now</button>
                <span class="p-cancel-link" onclick="location.reload()">Cancel</span>
            </div>

            <div style="margin-top: 80px; border-top: 1px solid #eee; padding-top: 60px;">
                <?php echo do_shortcode('[formidable id=' . esc_attr($a['form_id']) . ']'); ?>
            </div>
        </div>
    </div>

    <script>
    function showCalculator() {
        document.getElementById('gate-box').style.display = 'none';
        document.getElementById('calc-box').style.display = 'block';
        runMasterCalc();
    }

    function runMasterCalc() {
        const app = document.getElementById('p-master-app');
        const base = parseInt(app.getAttribute('data-rate'));
        const g = parseInt(document.getElementById('in-g').value) || 0;
        let n = parseInt(document.getElementById('in-n').value) || 0;
        const minN = parseInt(app.getAttribute('data-min-stay'));
        
        if (n < minN) n = minN;

        let r = base;
        let txt = "STANDARD RATE (" + base + "€/NIGHT)";
        
        if (n > 2 && n <= 6) { r = 20; txt = "MID-TERM RATE (20€/NIGHT)"; }
        else if (n > 6 && n <= 13) { r = 15; txt = "WEEKLY RATE (15€/NIGHT)"; }
        else if (n >= 14) { r = 10; txt = "LONG-STAY RATE (10€/NIGHT)"; }

        document.getElementById('total-val').innerText = g * n * r;
        document.getElementById('rate-label').innerText = txt;
    }

    function goToPay() {
        const app = document.getElementById('p-master-app');
        const url = app.getAttribute('data-checkout-url');
        const pid = app.getAttribute('data-product-id');
        const sum = document.getElementById('total-val').innerText;
        const sep = url.includes('?') ? '&' : '?';
        window.location.href = url + sep + 'add-to-cart=' + pid + '&quantity=' + sum;
    }
    </script>

    <?php
    return ob_get_clean();
});