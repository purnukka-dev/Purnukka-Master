<?php
/**
 * Plugin Name: Purnukka Check-in UI (Header Match v1.3.3)
 * Description: 1:1 visual match with production, focusing on the top "Purnukka Group" branding.
 * Version: 1.3.3
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
        /* 1. TÄMÄ ON SE PUUTTUVA YLÄOSA (Branding Header) */
        .p-master-branding-top {
            text-align: center;
            padding: 40px 0 20px 0;
            background: #ffffff;
        }
        .p-brand-text {
            font-family: 'Montserrat', sans-serif;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 5px;
            color: #b89b5e;
            font-weight: 700;
            display: block;
            margin-bottom: 5px;
        }
        .p-main-title-top {
            font-family: 'Playfair Display', serif;
            font-size: 42px;
            color: #1a2b28;
            margin: 0;
            font-weight: 400;
        }

        /* 2. FLOATING CONTENT WRAPPER */
        .p-master-float-card {
            font-family: 'Montserrat', sans-serif;
            max-width: 850px;
            margin: 0 auto 60px auto; 
            padding: 50px 40px;
            background: #ffffff;
            text-align: center; 
            box-shadow: 0px 20px 50px rgba(0,0,0,0.06);
            border-radius: 4px;
            border: 1px solid #f0f0f0;
            position: relative;
            z-index: 10;
        }

        .p-key-icon { color: #b89b5e; font-size: 36px; margin-bottom: 25px; display: block; }

        .p-underline-header {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            color: #1a2b28;
            margin: 0 0 15px 0;
            border-bottom: 2px solid #b89b5e;
            display: inline-block;
            padding-bottom: 8px;
        }

        /* 3. PHASE 1: START BOX */
        .p-gate-box-prod {
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

        /* 4. PHASE 2: CALCULATOR (Initially hidden) */
        #p-calc-view-prod {
            display: none; 
            margin-top: 40px;
            text-align: left;
        }

        .p-input-grid-prod {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .p-field-card-prod {
            background: #fff;
            border: 1px solid #b89b5e;
            padding: 18px 20px;
        }

        .p-field-card-prod label {
            display: block;
            font-size: 10px;
            color: #888;
            text-transform: uppercase;
            margin-bottom: 8px;
            font-weight: 700;
        }
        .p-field-card-prod label i { margin-right: 10px; color: #666; font-size: 12px; }

        .p-field-card-prod input {
            border: none;
            width: 100%;
            font-weight: 700;
            font-size: 24px;
            color: #1a2b28;
            outline: none;
            background: transparent;
        }

        .p-summary-prod {
            border-top: 2px solid #f8f8f8;
            padding-top: 30px;
            margin-bottom: 40px;
            text-align: center;
        }
        .p-note-prod { font-size: 11px; color: #b89b5e; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
        .p-total-prod { font-size: 52px; font-weight: 700; color: #1a2b28; display: block; line-height: 1; margin-top: 10px; }

        .p-btn-gold-prod { background: #b89b5e; color: #fff; border: none; padding: 22px; width: 100%; font-weight: 700; text-transform: uppercase; cursor: pointer; font-size: 14px; letter-spacing: 2px; }
        .p-btn-dark-prod { background: #1a2b28; color: #fff; border: none; padding: 15px 30px; font-weight: 700; text-transform: uppercase; font-size: 11px; letter-spacing: 1px; cursor: pointer; }

        @media (max-width: 650px) {
            .p-input-grid-prod { grid-template-columns: 1fr; }
            .p-gate-box-prod { flex-direction: column; text-align: center; }
        }
    </style>

    <div id="p-master-standard-root" 
         data-rate="<?php echo esc_attr($a['rate']); ?>" 
         data-min-stay="<?php echo esc_attr($a['min_stay']); ?>" 
         data-product-id="<?php echo esc_attr($a['product_id']); ?>"
         data-checkout-url="<?php echo esc_url($checkout_url); ?>">

        <div class="p-master-branding-top">
            <span class="p-brand-text">Purnukka Group</span>
            <h1 class="p-main-title-top"><?php echo esc_html($a['title']); ?></h1>
        </div>

        <div class="p-master-float-card">
            <i class="fas fa-key p-key-icon"></i> 
            <h2 class="p-underline-header">Check-in & Declaration</h2>
            <p style="font-size: 15px; color: #666; margin: 10px auto 40px auto; max-width: 650px; line-height: 1.7;">
                The mandatory traveler declaration ensures a safe stay and keeps your insurance coverage active throughout your visit.
            </p>

            <div class="p-gate-box-prod" id="p-gate-ui">
                <div>
                    <strong style="color: #1a2b28; font-size: 17px;">Change in group size?</strong><br>
                    <span style="font-size: 13px; color: #666;">Add and pay for additional guests here.</span>
                </div>
                <button class="p-btn-dark-prod" onclick="pShowCalc()">Add Guests</button>
            </div>

            <div id="p-calc-view-prod">
                <h3 style="font-family: 'Playfair Display', serif; font-size: 22px; color: #1a2b28; margin-bottom: 25px; text-align: left;">Add guests to booking</h3>
                
                <div class="p-input-grid-prod">
                    <div class="p-field-card-prod">
                        <label><i class="fas fa-users"></i> Additional Guests</label>
                        <input type="number" id="p-g-in" value="1" min="1" oninput="pRecalc()">
                    </div>
                    <div class="p-field-card-prod">
                        <label><i class="fas fa-moon"></i> Nights</label>
                        <input type="number" id="p-n-in" value="<?php echo esc_attr($a['min_stay']); ?>" min="<?php echo esc_attr($a['min_stay']); ?>" oninput="pRecalc()">
                    </div>
                </div>

                <div class="p-summary-prod">
                    <span id="p-note-ui" class="p-note-prod">STANDARD RATE (<?php echo $a['rate']; ?>€/NIGHT)</span>
                    <span class="p-total-prod"><span id="p-sum-ui">0</span> €</span>
                </div>

                <button class="p-btn-gold-prod" onclick="pProceedToPay()">Update & Pay Now</button>
                <div onclick="location.reload()" style="text-align: center; margin-top: 20px; font-size: 11px; cursor: pointer; color: #aaa; text-transform: uppercase; letter-spacing: 2px;">Cancel</div>
            </div>

            <div style="margin-top: 80px; text-align: left; border-top: 1px solid #eee; padding-top: 50px;">
                <?php echo do_shortcode('[formidable id=' . esc_attr($a['form_id']) . ']'); ?>
            </div>
        </div>
    </div>

    <script>
    function pShowCalc() {
        document.getElementById('p-gate-ui').style.display = 'none';
        document.getElementById('p-calc-view-prod').style.display = 'block';
        pRecalc();
    }

    function pRecalc() {
        const app = document.getElementById('p-master-standard-root');
        const rateBase = parseInt(app.getAttribute('data-rate'));
        const g = parseInt(document.getElementById('p-g-in').value) || 0;
        let n = parseInt(document.getElementById('p-n-in').value) || 0;
        const minN = parseInt(app.getAttribute('data-min-stay'));
        
        if (n < minN) n = minN;

        let r = rateBase;
        let txt = "STANDARD RATE (" + rateBase + "€/NIGHT)";

        if (n > 2 && n <= 6) { r = 20; txt = "MID-TERM RATE (20€/NIGHT)"; }
        else if (n > 6 && n <= 13) { r = 15; txt = "WEEKLY RATE (15€/NIGHT)"; }
        else if (n >= 14) { r = 10; txt = "LONG-STAY RATE (10€/NIGHT)"; }

        document.getElementById('p-sum-ui').innerText = g * n * r;
        document.getElementById('p-note-ui').innerText = txt;
    }

    function pProceedToPay() {
        const app = document.getElementById('p-master-standard-root');
        const url = app.getAttribute('data-checkout-url');
        const pid = app.getAttribute('data-product-id');
        const sum = document.getElementById('p-sum-ui').innerText;
        
        if (parseInt(sum) > 0) {
            const sep = url.includes('?') ? '&' : '?';
            window.location.href = url + sep + 'add-to-cart=' + pid + '&quantity=' + sum;
        }
    }
    </script>

    <?php
    return ob_get_clean();
});