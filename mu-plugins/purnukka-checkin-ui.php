<?php
/**
 * Plugin Name: Purnukka Check-in Master Compact English
 * Description: Low-profile layout to fit screen. English language, ID 276.
 */

if (!defined('ABSPATH')) exit;

add_shortcode('purnukka_checkin', function($atts) {
    ob_start(); ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* NOSTETAAN SISÄLTÖÄ YLÖS (Booklium fix) */
        .purnukka-premium-wrapper {
            font-family: 'Montserrat', sans-serif;
            max-width: 800px;
            margin: -60px auto 40px auto; /* Negatiivinen margin nostaa laatikon ylös */
            padding: 30px 40px;
            background: #ffffff;
            text-align: center; 
            box-shadow: 0px 15px 40px rgba(0,0,0,0.05);
            border-radius: 4px;
            border: 1px solid #f0f0f0;
            box-sizing: border-box;
            position: relative;
            z-index: 99;
        }

        /* TIIVIS HEADER */
        .p-top-icon-small { color: #b89b5e; font-size: 24px; margin-bottom: 10px; display: block; }
        .p-title-compact { font-family: 'Playfair Display', serif; font-size: 24px; color: #1a2b28; margin: 0 0 5px 0; }
        .p-subtitle-compact { font-size: 13px; color: #666; margin-bottom: 20px; display: block; }

        /* MATALA STEP-BOX */
        .p-step-box {
            background: #fdfdfd;
            border: 1px solid #eee;
            border-left: 4px solid #b89b5e; 
            padding: 15px 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-align: left;
            margin-bottom: 20px;
            gap: 15px;
        }

        .btn-p-dark-small {
            background: #1a2b28;
            color: #fff;
            border: none;
            padding: 10px 18px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 1px;
            cursor: pointer;
            white-space: nowrap;
        }

        /* MATALA LASKURI (Inline) */
        #purnukka-form-view {
            display: none;
            background: #fdfdfd;
            border: 1px solid #b89b5e;
            padding: 20px;
            margin-bottom: 20px;
            animation: fadeIn 0.3s ease-out;
        }

        .p-input-row-compact {
            display: flex;
            align-items: center;
            gap: 15px;
            justify-content: space-between;
        }

        .p-input-field-small { flex: 1; }
        .p-input-field-small label { display: block; font-size: 9px; color: #888; text-transform: uppercase; font-weight: bold; }
        .p-input-field-small input { border: none; width: 100%; font-size: 18px; font-weight: bold; color: #1a2b28; outline: none; background: transparent; }

        .p-price-tag { text-align: right; border-left: 1px solid #eee; padding-left: 15px; min-width: 70px; }
        .p-price-num { font-size: 24px; font-weight: bold; color: #1a2b28; display: block; }

        .btn-p-gold-compact {
            background: #b89b5e; color: #fff; border: none; padding: 12px;
            width: 100%; font-weight: bold; text-transform: uppercase;
            cursor: pointer; font-size: 11px; margin-top: 15px;
        }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        @media (max-width: 600px) {
            .p-step-box, .p-input-row-compact { flex-direction: column; text-align: center; }
            .p-price-tag { border-left: none; border-top: 1px solid #eee; padding: 10px 0 0 0; width: 100%; text-align: center; }
        }
    </style>

    <div class="purnukka-premium-wrapper">
        <i class="fas fa-key p-top-icon-small"></i> 
        <h2 class="p-title-compact">Traveler Declaration & Check-in</h2>
        <span class="p-subtitle-compact">A legal declaration ensures your insurance coverage during the stay.</span>
        
        <div class="p-step-box" id="p-step-1">
            <div style="line-height: 1.3;">
                <strong style="color: #1a2b28; font-size: 14px;">Group size changed?</strong>
                <span style="font-size: 11px; color: #888; display: block;">Add and pay for additional guests here.</span>
            </div>
            <button class="btn-p-dark-small" onclick="activateForm()">Add guests</button>
        </div>

        <div id="purnukka-form-view">
            <div class="p-input-row-compact">
                <div class="p-input-field-small">
                    <label>Guests</label>
                    <input type="number" id="p-guests" value="1" min="1" oninput="runRecalc()">
                </div>
                <div class="p-input-field-small">
                    <label>Nights</label>
                    <input type="number" id="p-nights" value="2" min="2" oninput="runRecalc()">
                </div>
                <div class="p-price-tag">
                    <label style="font-size: 9px; color: #b89b5e;">Total</label>
                    <span class="p-price-num"><span id="p-sum">60</span>€</span>
                </div>
            </div>
            <button class="btn-p-gold-compact" onclick="proceedToPay()">Update and Pay</button>
            <div onclick="location.reload()" style="text-align: center; margin-top: 10px; font-size: 9px; cursor: pointer; color: #aaa; text-transform: uppercase;">Cancel</div>
        </div>

        <div style="margin-top: 20px; text-align: left; border-top: 1px solid #f8f8f8; padding-top: 20px;">
            <?php echo do_shortcode('[formidable id=4]'); ?>
        </div>
    </div>

    <script>
    function activateForm() {
        document.getElementById('p-step-1').style.display = 'none';
        document.getElementById('purnukka-form-view').style.display = 'block';
        runRecalc();
    }

    function runRecalc() {
        const g = parseInt(document.getElementById('p-guests').value) || 0;
        let n = parseInt(document.getElementById('p-nights').value) || 0;
        if (n < 2) n = 2;
        let up = 30;
        if (n > 2 && n <= 6) up = 20;
        else if (n > 6 && n <= 13) up = 15;
        else if (n >= 14) up = 10;
        document.getElementById('p-sum').innerText = g * n * up;
    }

    function proceedToPay() {
        const sum = document.getElementById('p-sum').innerText;
        window.location.href = window.location.origin + '/checkout/?add-to-cart=276&quantity=' + sum;
    }
    </script>

    <?php
    return ob_get_clean();
});