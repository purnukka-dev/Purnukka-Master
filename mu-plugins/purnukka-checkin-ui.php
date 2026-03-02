<?php
/**
 * Plugin Name: Purnukka Check-in UI (Master Standard v1.2.8)
 * Description: Final Master Standard with Premium UI, English logic, and dynamic routing.
 * Version: 1.2.8
 * Author: Purnukka Group Master
 */

if (!defined('ABSPATH')) exit;

add_shortcode('purnukka_checkin', function($atts) {
    // 1. DYNAMIC ROUTING & MASTER SETTINGS
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
        /* 1. PREMIUM HEADER SECTION */
        .p-master-welcome-header {
            background: #ffffff;
            padding: 80px 20px 60px 20px;
            text-align: center;
            border-bottom: 1px solid #f0f0f0;
        }

        .p-brand-tag {
            font-family: 'Montserrat', sans-serif;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 5px;
            color: #b89b5e;
            display: block;
            margin-bottom: 12px;
            font-weight: 700;
        }

        .p-master-welcome-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(32px, 7vw, 48px);
            color: #1a2b28;
            margin: 0;
            font-weight: 400;
            letter-spacing: -1px;
        }

        /* 2. MASTER CONTAINER - FLOATING UI */
        .p-master-premium-wrapper {
            font-family: 'Montserrat', sans-serif;
            max-width: 850px;
            margin: -40px auto 80px auto; 
            padding: 50px;
            background: #ffffff;
            text-align: center; 
            box-shadow: 0px 30px 60px rgba(0,0,0,0.07);
            border-radius: 4px;
            border: 1px solid #f0f0f0;
            box-sizing: border-box;
            position: relative;
            z-index: 10;
        }

        .p-main-icon { color: #b89b5e; font-size: 40px; margin-bottom: 25px; display: block; }

        /* 3. GATE BOX (PRODUCTION MATCH) */
        .p-gate-box {
            background: #fdfdfd;
            border: 1px solid #1a2b28;
            border-left: 10px solid #b89b5e; 
            padding: 35px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-align: left;
            margin-top: 40px;
            gap: 25px;
        }

        .p-btn-dark {
            background: #1a2b28;
            color: #fff;
            border: none;
            padding: 16px 30px;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 1.5px;
            cursor: pointer;
            transition: 0.3s ease;
            white-space: nowrap;
        }
        .p-btn-dark:hover { background: #b89b5e; }

        /* 4. DYNAMIC CALCULATOR VIEW */
        #p-calc-view {
            display: none;
            margin-top: 40px;
            text-align: left;
            animation: pFadeIn 0.5s ease-out;
        }

        .p-input-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .p-field-card {
            background: #fff;
            border: 1px solid #dcdcdc; /* Production matching gray */
            padding: 20px;
            transition: border-color 0.3s;
        }
        .p-field-card:focus-within { border-color: #b89b5e; }

        .p-field-card label {
            display: block;
            font-size: 10px;
            color: #888;
            text-transform: uppercase;
            margin-bottom: 8px;
            font-weight: 700;
        }
        .p-field-card label i { margin-right: 8px; color: #b89b5e; }

        .p-field-card input {
            border: none;
            width: 100%;
            font-weight: 700;
            font-size: 26px;
            color: #1a2b28;
            outline: none;
            background: transparent;
        }

        .p-summary-section {
            border-top: 2px solid #f8f8f8;
            padding-top: 30px;
            margin-bottom: 40px;
            text-align: center;
        }

        .p-summary-note { font-size: 11px; color: #b89b5e; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
        .p-summary-total { font-size: 52px; font-weight: 700; color: #1a2b28; display: block; line-height: 1.2; }

        .p-btn-gold {
            background: #b89b5e;
            color: #fff;
            border: none;
            padding: 22px;
            width: 100%;
            font-weight: 700;
            text-transform: uppercase;
            cursor: pointer;
            font-size: 14px;
            letter-spacing: 2px;
            transition: 0.3s;
        }
        .p-btn-gold:hover { background: #1a2b28; }

        @keyframes pFadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* MOBILE ADJUSTMENTS */
        @media (max-width: 650px) {
            .p-master-premium-wrapper { padding: 40px 20px; margin-top: -30px; }
            .p-gate-box { flex-direction: column; text-align: center; }
            .p-btn-dark { width: 100%; }
            .p-input-grid { grid-template-columns: 1fr; }
            .p-summary-total { font-size: 40px; }
        }
    </style>

    <div id="p-master-app" 
         data-rate="<?php echo esc_attr($a['rate']); ?>" 
         data-min-stay="<?php echo esc_attr($a['min_stay']); ?>" 
         data-product-id="<?php echo esc_attr($a['product_id']); ?>"
         data-checkout-url="<?php echo esc_url($checkout_url); ?>">

        <div class="p-master-welcome-header">
            <span class="p-brand-tag">Purnukka Group</span>
            <h1><?php echo esc_html($a['title']); ?></h1>
        </div>

        <div class="p-master-premium-wrapper">
            <i class="fas fa-key p-main-icon"></i> 
            <h2 style="font-family: 'Playfair Display', serif; font-size: 30px; color: #1a2b28; margin: 0 0 20px 0; border-bottom: 2px solid #b89b5e; display: inline-block; padding-bottom: 8px;">Check-in & Declaration</h2>
            <p style="font-size: 15px; color: #666; margin: 10px auto 40px auto; max-width: 650px; line-height: 1.7;">
                The mandatory traveler declaration ensures a safe stay and keeps your insurance coverage active throughout your visit.
            </p>

            <div class="p-gate-box" id="p-gate-ui">
                <div>
                    <strong style="color: #1a2b28; font-size: 17px;">Change in group size?</strong><br>
                    <span style="font-size: 13px; color: #666;">Add and pay for additional guests here.</span>
                </div>
                <button class="p-btn-dark" onclick="pRevealCalc()">Add Guests</button>
            </div>

            <div id="p-calc-ui">
                <h3 style="font-family: 'Playfair Display', serif; font-size: 24px; color: #1a2b28; margin-bottom: 25px;">Add guests to booking</h3>
                
                <div class="p-input-grid">
                    <div class="p-field-card">
                        <label><i class="fas fa-users"></i> Additional Guests</label>
                        <input type="number" id="p-qty-guests" value="1" min="1" oninput="pRunRecalc()">
                    </div>
                    <div class="p-field-card">
                        <label><i class="fas fa-moon"></i> Nights</label>
                        <input type="number" id="p-qty-nights" value="<?php echo esc_attr($a['min_stay']); ?>" min="<?php echo esc_attr($a['min_stay']); ?>" oninput="pRunRecalc()">
                    </div>
                </div>

                <div class="p-summary-section">
                    <span id="p-rate-note" class="p-summary-note">STANDARD RATE</span>
                    <span class="p-summary-total"><span id="p-final-val">0</span> €</span>
                </div>

                <button class="p-btn-gold" onclick="pSubmitPay()">Update & Pay Now</button>
                <div onclick="location.reload()" style="text-align: center; margin-top: 20px; font-size: 11px; cursor: pointer; color: #aaa; text-transform: uppercase; letter-spacing: 2px;">Cancel</div>
            </div>

            <div style="margin-top: 80px; border-top: 1px solid #eee; padding-top: 60px; text-align: left;">
                <?php echo do_shortcode('[formidable id=' . esc_attr($a['form_id']) . ']'); ?>
            </div>
        </div>
    </div>

    <script>
    function pRevealCalc() {
        document.getElementById('p-gate-ui').style.display = 'none';
        document.getElementById('p-calc-ui').style.display = 'block';
        pRunRecalc();
    }

    function pRunRecalc() {
        const app = document.getElementById('p-master-app');
        const rateBase = parseInt(app.getAttribute('data-rate'));
        const g = parseInt(document.getElementById('p-qty-guests').value) || 0;
        let n = parseInt(document.getElementById('p-qty-nights').value) || 0;
        const minN = parseInt(app.getAttribute('data-min-stay'));
        
        if (n < minN) n = minN;

        let r = rateBase;
        let txt = "STANDARD RATE (" + rateBase + "€/NIGHT)";

        if (n > 2 && n <= 6) { r = 20; txt = "MID-TERM RATE (20€/NIGHT)"; }
        else if (n > 6 && n <= 13) { r = 15; txt = "WEEKLY RATE (15€/NIGHT)"; }
        else if (n >= 14) { r = 10; txt = "LONG-STAY RATE (10€/NIGHT)"; }

        document.getElementById('p-final-val').innerText = g * n * r;
        document.getElementById('p-rate-note').innerText = txt;
    }

    function pSubmitPay() {
        const app = document.getElementById('p-master-app');
        const url = app.getAttribute('data-checkout-url');
        const pid = app.getAttribute('data-product-id');
        const sum = document.getElementById('p-final-val').innerText;
        
        if (parseInt(sum) > 0) {
            const sep = url.includes('?') ? '&' : '?';
            window.location.href = url + sep + 'add-to-cart=' + pid + '&quantity=' + sum;
        }
    }
    </script>

    <?php
    return ob_get_clean();
});