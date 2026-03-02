<?php
/**
 * Plugin Name: Purnukka Check-in UI (Production Style)
 * Description: Master standard with production-matching contrast, gold accents, and wide layout.
 * Version: 1.2.4
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

    <style>
        .p-master-wrapper { 
            font-family: 'Montserrat', sans-serif; 
            max-width: 1000px; 
            margin: 0 auto 60px; 
            padding: 0 20px; 
        }

        /* PRODUCTION MATCHING BOX */
        .p-master-box-styled {
            background: #fff;
            border: 1px solid #1a2b28; /* Tumma reunus kuten tuotannossa */
            border-left: 8px solid #b89b5e; /* Paksu kultainen tehoste vasemmalla */
            padding: 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 40px;
            text-align: left;
        }

        /* INPUT SECTIONS WITH CONTRAST */
        .p-input-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .p-input-card {
            background: #f4f4f4; /* Harmaa tausta jotta erottuu reunoista */
            border: 1px solid #e0e0e0;
            padding: 20px;
            border-radius: 2px;
        }

        .p-input-card label {
            display: block;
            font-size: 10px;
            color: #b89b5e;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .p-input-card input {
            border: none;
            background: transparent;
            width: 100%;
            font-weight: 700;
            font-size: 24px;
            color: #1a2b28;
            outline: none;
        }

        /* BUTTONS MATCHING PRODUCTION */
        .p-btn-prod-dark {
            background: #1a2b28;
            color: #fff;
            border: none;
            padding: 15px 30px;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
            cursor: pointer;
        }

        .p-btn-prod-gold {
            background: #b89b5e;
            color: #fff;
            border: none;
            padding: 20px;
            width: 100%;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 2px;
            cursor: pointer;
            margin-top: 20px;
        }

        .p-total-display {
            text-align: center;
            padding: 30px 0;
        }

        .p-total-price {
            font-size: 52px;
            font-weight: 700;
            color: #1a2b28;
            display: block;
        }

        @media (max-width: 600px) {
            .p-master-box-styled { flex-direction: column; text-align: center; }
            .p-input-grid { grid-template-columns: 1fr; }
        }
    </style>

    <div id="p-master-app" 
         data-rate="<?php echo esc_attr($a['rate']); ?>" 
         data-min-stay="<?php echo esc_attr($a['min_stay']); ?>" 
         data-product-id="<?php echo esc_attr($a['product_id']); ?>"
         data-checkout-url="<?php echo esc_url($checkout_url); ?>">

        <div class="p-master-wrapper">
            <div style="text-align: center; padding: 60px 0 40px;">
                <span style="font-size: 10px; text-transform: uppercase; letter-spacing: 5px; color: #b89b5e; font-weight: 700;">Purnukka Group</span>
                <h1 style="font-family: 'Playfair Display', serif; font-size: 36px; margin: 10px 0;"><?php echo esc_html($a['title']); ?></h1>
                <div style="width: 40px; height: 1px; background: #b89b5e; margin: 20px auto;"></div>
            </div>

            <div class="p-master-box-styled" id="p-gate">
                <div>
                    <h3 style="margin: 0; font-size: 18px;">Change in group size?</h3>
                    <p style="margin: 5px 0 0; color: #666; font-size: 14px;">Update your booking and pay for extra guests here.</p>
                </div>
                <button class="p-btn-prod-dark" onclick="showCalc()">Yes, Add Guests</button>
            </div>

            <div id="p-calc" style="display: none;">
                <div class="p-input-grid">
                    <div class="p-input-card">
                        <label>Additional Guests</label>
                        <input type="number" id="p-g" value="1" min="1" oninput="recalc()">
                    </div>
                    <div class="p-input-card">
                        <label>Nights</label>
                        <input type="number" id="p-n" value="<?php echo esc_attr($a['min_stay']); ?>" min="<?php echo esc_attr($a['min_stay']); ?>" oninput="recalc()">
                    </div>
                </div>

                <div class="p-total-display">
                    <span id="p-note" style="font-size: 11px; text-transform: uppercase; color: #888;">Standard Rate</span>
                    <span class="p-total-price"><span id="p-res">0</span> €</span>
                </div>

                <button class="p-btn-prod-gold" onclick="pay()">Update & Pay Now</button>
                <p onclick="location.reload()" style="text-align: center; cursor: pointer; color: #999; font-size: 11px; margin-top: 20px; text-transform: uppercase;">Cancel</p>
            </div>

            <div style="margin-top: 80px;">
                <?php echo do_shortcode('[formidable id=' . esc_attr($a['form_id']) . ']'); ?>
            </div>
        </div>
    </div>

    <script>
    function showCalc() {
        document.getElementById('p-gate').style.display = 'none';
        document.getElementById('p-calc').style.display = 'block';
        recalc();
    }
    function recalc() {
        const app = document.getElementById('p-master-app');
        const rate = parseInt(app.getAttribute('data-rate'));
        const g = parseInt(document.getElementById('p-g').value) || 0;
        let n = parseInt(document.getElementById('p-n').value) || 0;
        const minN = parseInt(app.getAttribute('data-min-stay'));
        if (n < minN) n = minN;

        let curR = rate;
        let note = "STANDARD RATE ("+rate+"€/NIGHT)";
        if (n > 2 && n <= 6) { curR = 20; note = "MID-TERM DISCOUNT (20€/NIGHT)"; }
        else if (n > 6 && n <= 13) { curR = 15; note = "WEEKLY DISCOUNT (15€/NIGHT)"; }
        else if (n >= 14) { curR = 10; note = "LONG-STAY DISCOUNT (10€/NIGHT)"; }

        document.getElementById('p-res').innerText = g * n * curR;
        document.getElementById('p-note').innerText = note;
    }
    function pay() {
        const app = document.getElementById('p-master-app');
        const url = app.getAttribute('data-checkout-url');
        const pid = app.getAttribute('data-product-id');
        const sum = document.getElementById('p-res').innerText;
        const sep = url.includes('?') ? '&' : '?';
        window.location.href = url + sep + 'add-to-cart=' + pid + '&quantity=' + sum;
    }
    </script>

    <?php
    return ob_get_clean();
});