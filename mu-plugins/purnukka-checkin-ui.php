<?php
/**
 * Plugin Name: Purnukka Check-in Master Compact (v1.8.0)
 * Description: Space-optimized UI for Master site with vertical sidebar.
 */

if (!defined('ABSPATH')) exit;

add_shortcode('purnukka_checkin', function($atts) {
    ob_start(); ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* KOMPAKTI WRAPPER - Ei pakota leveyttä, vaan sopeutuu */
        .p-master-compact {
            font-family: 'Montserrat', sans-serif;
            max-width: 600px; /* Pienempi maksimileveys näyttää paremmalta kapeassa tilassa */
            margin: 0 auto;
            background: #fff;
            padding: 20px;
        }

        .p-compact-card {
            background: #ffffff;
            border: 1px solid #f0f0f0;
            border-radius: 4px;
            box-shadow: 0px 10px 30px rgba(0,0,0,0.05);
            padding: 30px 25px;
            text-align: center;
        }

        /* OTSIKOIDEN TIIVISTYS */
        .p-brand-small { font-size: 10px; text-transform: uppercase; letter-spacing: 3px; color: #b89b5e; font-weight: bold; display: block; margin-bottom: 5px; }
        .p-title-small { font-family: 'Playfair Display', serif; font-size: 28px; color: #1a2b28; margin: 0 0 20px 0; }

        /* LASKURI YHDELLÄ RIVILLÄ (Desktop) */
        .p-calc-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fdfdfd;
            border: 1px solid #b89b5e;
            padding: 15px;
            margin-bottom: 20px;
            gap: 15px;
        }

        .p-input-group { flex: 1; text-align: left; }
        .p-input-group label { display: block; font-size: 9px; color: #888; text-transform: uppercase; font-weight: bold; }
        .p-input-group input { border: none; width: 100%; font-size: 18px; font-weight: bold; color: #1a2b28; outline: none; background: transparent; }

        .p-price-box {
            border-left: 1px solid #eee;
            padding-left: 15px;
            text-align: right;
            min-width: 80px;
        }
        .p-price-val { font-size: 24px; font-weight: bold; color: #1a2b28; display: block; }

        .btn-p-gold-compact {
            background: #b89b5e; color: #fff; border: none; padding: 15px;
            width: 100%; font-weight: bold; text-transform: uppercase;
            cursor: pointer; font-size: 12px; transition: 0.3s;
        }

        /* MOBIILI */
        @media (max-width: 500px) {
            .p-calc-row { flex-direction: column; text-align: center; }
            .p-price-box { border-left: none; border-top: 1px solid #eee; padding-top: 10px; width: 100%; text-align: center; }
        }
    </style>

    <div class="p-master-compact">
        
        <div style="text-align: center; margin-bottom: 25px;">
            <span class="p-brand-small">Purnukka Group</span>
            <h1 class="p-title-small">Check-in & Declaration</h1>
        </div>

        <div class="p-compact-card">
            <p style="font-size: 13px; color: #666; margin-bottom: 25px; line-height: 1.5;">
                Please verify your group size and complete the mandatory declaration below.
            </p>

            <div id="p-calculator">
                <div class="p-calc-row">
                    <div class="p-input-group">
                        <label>Guests</label>
                        <input type="number" id="p-guests" value="1" min="1" oninput="runRecalc()">
                    </div>
                    <div class="p-input-group">
                        <label>Nights</label>
                        <input type="number" id="p-nights" value="2" min="2" oninput="runRecalc()">
                    </div>
                    <div class="p-price-box">
                        <label style="font-size: 9px; color: #b89b5e;">Total</label>
                        <span class="p-price-val"><span id="p-sum">60</span>€</span>
                    </div>
                </div>

                <button class="btn-p-gold-compact" onclick="proceedToPay()">Update & Pay Now</button>
            </div>

            <div style="margin-top: 40px; text-align: left; border-top: 1px solid #eee; padding-top: 30px;">
                <?php echo do_shortcode('[formidable id=4]'); ?>
            </div>
        </div>
    </div>

    <script>
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