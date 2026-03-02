<?php
/**
 * Plugin Name: Purnukka Check-in Master (Zero Scroll Fix)
 * Description: Extremely low profile layout. Locked for 100% zoom. ID 276.
 */

if (!defined('ABSPATH')) exit;

add_shortcode('purnukka_checkin', function($atts) {
    ob_start(); ?>

    <style>
        .p-master-zero-scroll {
            font-family: 'Montserrat', sans-serif;
            max-width: 850px;
            /* PAKOTETTU NOSTO: Aloittaa aivan yläreunasta */
            margin: -175px auto 0 auto !important; 
            /* TIIVISTETTY SISÄLTÖ: Poistetaan turha ilma */
            padding: 20px 40px 15px 40px !important;
            background: #ffffff;
            text-align: center; 
            box-shadow: 0px 10px 40px rgba(0,0,0,0.06);
            border: 1px solid #f0f0f0;
            box-sizing: border-box;
            position: relative;
            z-index: 999;
        }

        .p-brand-gold { font-size: 10px; text-transform: uppercase; letter-spacing: 5px; color: #b89b5e; font-weight: bold; display: block; margin-bottom: 2px; }
        .p-title-main { font-family: 'Playfair Display', serif; font-size: 28px; color: #1a2b28; margin: 0 0 10px 0; line-height: 1; }
        
        .p-checkin-gold-divider { border-bottom: 2px solid #b89b5e; width: 40px; margin: 0 auto 15px auto; }

        /* 2+1 GRID: TIIVISTETTY KORKEUS */
        .p-grid-inputs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 12px;
        }

        .p-input-box-min {
            border: 1px solid #b89b5e;
            padding: 8px 12px;
            text-align: left;
        }

        .p-input-box-min label { display: block; font-size: 8px; color: #888; text-transform: uppercase; font-weight: bold; margin-bottom: 2px; }
        .p-input-box-min input { border: none; width: 100%; font-size: 18px; font-weight: bold; color: #1a2b28; outline: none; background: transparent; padding: 0; }

        .p-price-box-min {
            background: #fdfdfd;
            border: 1px solid #1a2b28;
            padding: 10px;
            margin-bottom: 12px;
            text-align: center;
        }

        .p-price-val-min { font-size: 32px; font-weight: bold; color: #1a2b28; line-height: 1; display: block; }

        .btn-pay-min {
            background: #b89b5e; color: #fff; border: none; padding: 14px;
            width: 100%; font-weight: bold; text-transform: uppercase;
            cursor: pointer; font-size: 12px; letter-spacing: 1px;
        }

        @media (max-width: 600px) { .p-grid-inputs { grid-template-columns: 1fr; } .p-master-zero-scroll { margin-top: 0 !important; } }
    </style>

    <div class="p-master-zero-scroll">
        <span class="p-brand-gold">Purnukka Group</span>
        <h1 class="p-title-main">Welcome Home</h1>
        <div class="p-checkin-gold-divider"></div>

        <h2 style="font-family: 'Playfair Display', serif; font-size: 20px; color: #1a2b28; margin-bottom: 5px;">Check-in & Declaration</h2>
        <p style="font-size: 11px; color: #666; margin-bottom: 15px;">Please verify your group size below.</p>

        <div class="p-grid-inputs">
            <div class="p-input-box-min">
                <label>Guests</label>
                <input type="number" id="p-guests" value="1" min="1" oninput="recalc()">
            </div>
            <div class="p-input-box-min">
                <label>Nights</label>
                <input type="number" id="p-nights" value="2" min="2" oninput="recalc()">
            </div>
        </div>

        <div class="p-price-box-min">
            <span style="font-size: 8px; color: #b89b5e; font-weight: bold; display: block; margin-bottom: 2px;">TOTAL ESTIMATE</span>
            <span class="p-price-val-min"><span id="p-sum">60</span> €</span>
        </div>

        <button class="btn-pay-min" onclick="pay()">Update & Pay Now</button>
    </div>

    <script>
    function recalc() {
        const g = parseInt(document.getElementById('p-guests').value) || 0;
        let n = parseInt(document.getElementById('p-nights').value) || 0;
        if (n < 2) n = 2;
        let up = 30;
        if (n > 2 && n <= 6) up = 20;
        else if (n > 6 && n <= 13) up = 15;
        else if (n >= 14) up = 10;
        document.getElementById('p-sum').innerText = g * n * up;
    }
    function pay() {
        const total = document.getElementById('p-sum').innerText;
        window.location.href = window.location.origin + '/checkout/?add-to-cart=276&quantity=' + total;
    }
    </script>

    <?php
    return ob_get_clean();
});