<?php
/**
 * Plugin Name: Purnukka Check-in UI (Cross-Site Standard v1.3.4)
 * Description: Synchronizes Master and Villa Purnukka visuals. High contrast, floating header, and correct branding.
 * Version: 1.3.4
 */

if (!defined('ABSPATH')) exit;

add_shortcode('purnukka_checkin', function($atts) {
    $checkout_url = function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : site_url('/payment-checkout/');

    $a = shortcode_atts(array(
        'rate'       => '30',
        'min_stay'   => '2',
        'product_id' => '3775', 
        'form_id'    => '4', 
        'title'      => 'Welcome Home'
    ), $atts);

    ob_start(); ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* 1. GLOBAL RESET FOR MASTER/CHILD BALANCE */
        .p-master-global-container {
            font-family: 'Montserrat', sans-serif;
            background: #ffffff;
            width: 100%;
            margin: 0 auto;
        }

        /* 2. PREMIUM BRANDING HEADER (Matches Villa Purnukka) */
        .p-brand-header-top {
            text-align: center;
            padding: 80px 20px 60px;
            background: #ffffff;
            border-bottom: 1px solid #f0f0f0;
        }
        .p-brand-label-gold {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 5px;
            color: #b89b5e;
            font-weight: 700;
            display: block;
            margin-bottom: 15px;
        }
        .p-brand-header-top h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(32px, 7vw, 48px);
            color: #1a2b28;
            margin: 0;
            font-weight: 400;
            letter-spacing: -1px;
        }

        /* 3. FLOATING CONTENT WRAPPER (The "Villa" Look) */
        .p-content-float-card {
            max-width: 900px;
            margin: -40px auto 80px; /* Floats over the header border */
            padding: 60px 50px;
            background: #ffffff;
            text-align: center; 
            box-shadow: 0px 30px 60px rgba(0,0,0,0.07);
            border-radius: 4px;
            border: 1px solid #f0f0f0;
            position: relative;
            z-index: 10;
        }

        .p-main-key-icon { color: #b89b5e; font-size: 44px; margin-bottom: 30px; display: block; }

        .p-title-underline {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            color: #1a2b28;
            margin-bottom: 20px;
            border-bottom: 2px solid #b89b5e;
            display: inline-block;
            padding-bottom: 10px;
        }

        /* 4. PHASE 1: GATE BOX (Production Match) */
        .p-gate-box-styled {
            background: #fdfdfd;
            border: 1px solid #1a2b28;
            border-left: 10px solid #b89b5e; 
            padding: 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-align: left;
            margin: 40px 0;
            gap: 30px;
        }

        /* 5. PHASE 2: CALCULATOR */
        #p-calc-view-master {
            display: none; 
            margin-top: 40px;
            text-align: left;
        }

        .p-input-grid-master {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 40px;
        }

        .p-field-card-master {
            background: #fff;
            border: 1px solid #b89b5e;
            padding: 22px;
        }

        .p-field-card-master label {
            display: block;
            font-size: 10px;
            color: #888;
            text-transform: uppercase;
            margin-bottom: 10px;
            font-weight: 700;
        }
        .p-field-card-master label i { margin-right: 12px; color: #b89b5e; }

        .p-field-card-master input {
            border: none;
            width: 100%;
            font-weight: 700;
            font-size: 28px;
            color: #1a2b28;
            outline: none;
        }

        /* SUMMARY & BUTTONS */
        .p-summary-box-master {
            border-top: 2px solid #f8f8f8;
            padding-top: 40px;
            margin-bottom: 40px;
            text-align: center;
        }
        .p-total-price-master { font-size: 64px; font-weight: 700; color: #1a2b28; display: block; line-height: 1; margin: 15px 0; }

        .p-btn-gold-master { background: #b89b5e; color: #fff; border: none; padding: 24px; width: 100%; font-weight: 700; text-transform: uppercase; cursor: pointer; font-size: 14px; letter-spacing: 2px; }
        .p-btn-dark-master { background: #1a2b28; color: #fff; border: none; padding: 18px 35px; font-weight: 700; text-transform: uppercase; font-size: 11px; letter-spacing: 1px; cursor: pointer; }

        @media (max-width: 650px) {
            .p-input-grid-master { grid-template-columns: 1fr; }
            .p-gate-box-styled { flex-direction: column; text-align: center; }
            .p-content-float-card { padding: 40px 20px; margin-top: -30px; }
        }
    </style>

    <div id="p-master-app-root" 
         data-rate="<?php echo esc_attr($a['rate']); ?>" 
         data-min-stay="<?php echo esc_attr($a['min_stay']); ?>" 
         data-product-id="<?php echo esc_attr($a['product_id']); ?>"
         data-checkout-url="<?php echo esc_url($checkout_url); ?>">

        <div class="p-master-global-container">
            <div class="p-brand-header-top">
                <span class="p-brand-label-gold">Purnukka Group</span>
                <h1><?php echo esc_html($a['title']); ?></h1>
            </div>

            <div class="p-content-float-card">
                <i class="fas fa-key p-main-key-icon"></i> 
                <h2 class="p-title-underline">Check-in & Declaration</h2>
                <p style="font-size: 16px; color: #666; margin: 0 auto 40px; max-width: 700px; line-height: 1.8;">
                    Welcome! A mandatory traveler declaration ensures a safe stay and keeps your insurance coverage active.
                </p>

                <div class="p-gate-box-styled" id="p-gate-ui-master">
                    <div>
                        <strong style="color: #1a2b28; font-size: 18px;">Change in group size?</strong><br>
                        <span style="font-size: 14px; color: #666;">Add and pay for additional guests here.</span>
                    </div>
                    <button class="p-btn-dark-master" onclick="pOpenCalc()">Add Guests</button>
                </div>

                <div id="p-calc-view-master">
                    <h3 style="font-family: 'Playfair Display', serif; font-size: 26px; color: #1a2b28; margin-bottom: 30px; text-align: left;">Add guests to booking</h3>
                    
                    <div class="p-input-grid-master">
                        <div class="p-field-card-master">
                            <label><i class="fas fa-users"></i> Additional Guests</label>
                            <input type="number" id="p-g-master" value="1" min="1" oninput="pMasterRecalc()">
                        </div>
                        <div class="p-field-card-master">
                            <label><i class="fas fa-moon"></i> Nights</label>
                            <input type="number" id="p-n-master" value="<?php echo esc_attr($a['min_stay']); ?>" min="<?php echo esc_attr($a['min_stay']); ?>" oninput="pMasterRecalc()">
                        </div>
                    </div>

                    <div class="p-summary-box-master">
                        <span id="p-note-master" style="font-size: 11px; color: #b89b5e; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Standard Rate</span>
                        <span class="p-total-price-master"><span id="p-sum-master">0</span> €</span>
                    </div>

                    <button class="p-btn-gold-master" onclick="pMasterGoPay()">Update & Pay Now</button>
                    <div onclick="location.reload()" style="text-align: center; margin-top: 25px; font-size: 11px; cursor: pointer; color: #aaa; text-transform: uppercase; letter-spacing: 2px;">Cancel</div>
                </div>

                <div style="margin-top: 100px; text-align: left; border-top: 1px solid #eee; padding-top: 60px;">
                    <?php echo do_shortcode('[formidable id=' . esc_attr($a['form_id']) . ']'); ?>
                </div>
            </div>
        </div>
    </div>

    <script>
    function pOpenCalc() {
        document.getElementById('p-gate-ui-master').style.display = 'none';
        document.getElementById('p-calc-view-master').style.display = 'block';
        pMasterRecalc();
    }

    function pMasterRecalc() {
        const app = document.getElementById('p-master-app-root');
        const rateBase = parseInt(app.getAttribute('data-rate'));
        const g = parseInt(document.getElementById('p-g-master').value) || 0;
        let n = parseInt(document.getElementById('p-n-master').value) || 0;
        const minN = parseInt(app.getAttribute('data-min-stay'));
        
        if (n < minN) n = minN;

        let r = rateBase;
        let txt = "STANDARD RATE (" + rateBase + "€/NIGHT)";

        if (n > 2 && n <= 6) { r = 20; txt = "MID-TERM RATE (20€/NIGHT)"; }
        else if (n > 6 && n <= 13) { r = 15; txt = "WEEKLY RATE (15€/NIGHT)"; }
        else if (n >= 14) { r = 10; txt = "LONG-STAY RATE (10€/NIGHT)"; }

        document.getElementById('p-sum-master').innerText = g * n * r;
        document.getElementById('p-note-master').innerText = txt;
    }

    function pMasterGoPay() {
        const app = document.getElementById('p-master-app-root');
        const url = app.getAttribute('data-checkout-url');
        const pid = app.getAttribute('data-product-id');
        const sum = document.getElementById('p-sum-master').innerText;
        
        if (parseInt(sum) > 0) {
            const sep = url.includes('?') ? '&' : '?';
            window.location.href = url + sep + 'add-to-cart=' + pid + '&quantity=' + sum;
        }
    }
    </script>

    <?php
    return ob_get_clean();
});