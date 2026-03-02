<?php
/**
 * Plugin Name: Purnukka Check-in Master Ultra-Low (No Form)
 * Description: Focus only on calculator and branding. Form removed to save space.
 */

if (!defined('ABSPATH')) exit;

add_shortcode('purnukka_checkin', function($atts) {
    ob_start(); ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Playfair+Display&display=swap" rel="stylesheet">

    <style>
        .p-ultra-low-wrapper {
            font-family: 'Montserrat', sans-serif;
            max-width: 850px;
            margin: -140px auto 20px auto !important; /* Nostaa laatikon ylös Booklium-palkin viereen */
            padding: 30px 40px !important;
            background: #ffffff;
            text-align: center; 
            box-shadow: 0px 20px 50px rgba(0,0,0,0.06);
            border-radius: 4px;
            border: 1px solid #f0f0f0;
            box-sizing: border-box;
            position: relative;
            z-index: 999;
        }

        .p-brand-gold { font-size: 10px; text-transform: uppercase; letter-spacing: 5px; color: #b89b5e; font-weight: bold; display: block; margin-bottom: 8px; }
        .p-title-main { font-family: 'Playfair Display', serif; font-size: 32px; color: #1a2b28; margin: 0 0 25px 0; }

        /* VAIHE 1: MATALA ALOITUSBOXI */
        .p-step-box-low {
            background: #fdfdfd;
            border: 1px solid #1a2b28;
            border-left: 6px solid #b89b5e; 
            padding: 20px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-align: left;
            margin-bottom: 5px;
        }

        /* VAIHE 2: LASKURI (Guests & Nights vierekkäin) */
        #p-calc-view { display: none; animation: fadeIn 0.3s ease; }

        .p-input-grid-compact {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .p-input-box {
            border: 1px solid #b89b5e;
            padding: 12px 15px;
            text-align: left;
        }

        .p-input-box label { display: block; font-size: 9px; color: #888; text-transform: uppercase; font-weight: bold; margin-bottom: 4px; }
        .p-input-box input { border: none; width: 100%; font-size: 20px; font-weight: bold; color: #1a2b28; outline: none; background: transparent; }

        .p-price-row-compact {
            background: #fdfdfd;
            border: 1px solid #1a2b28;
            padding: 15px;
            margin-bottom: 15px;
            text-align: center;
        }

        .p-price-val { font-size: 32px; font-weight: bold; color: #1a2b28; }

        .btn-update-pay {
            background: #b89b5e; color: #fff; border: none; padding: 18px;
            width: 100%; font-weight: bold; text-transform: uppercase;
            cursor: pointer; font-size: 13px; letter-spacing: 1px;
        }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        @media (max-width: 600px) {
            .p-input-grid-compact { grid-template-columns: 1fr; }
            .p-step-box-low { flex-direction: column; text-align: center; gap: 15px; }
        }
    </style>

    <div class="p-ultra-low-wrapper">
        <span class="p-brand-gold">Purnukka Group</span>
        <h1 class="p-title-main">Welcome Home</h1>

        <h2 style="font-family: 'Playfair Display', serif; font-size: 24px; color: #1a2b28; margin-bottom: 10px;">Traveler Declaration & Check-in</h2>
        
        <div class="p-step-box-low" id="p-step-1">
            <div>
                <strong style="font-size: 15px;">Has your group size changed?</strong><br>
                <span style="font-size: 11px; color: #666;">Add and pay for additional guests here.</span>
            </div>
            <button onclick="activateCalc()" style="background:#1a2b28; color:#fff; border:none; padding:10px 20px; cursor:pointer; font-weight:bold; text-transform:uppercase; font-size:10px;">Add guests</button>
        </div>

        <div id="p-calc-view">
            <div class="p-input-grid-compact">
                <div class="p-input-box">
                    <label>Guests</label>
                    <input type="number" id="p-guests" value="1" min="1" oninput="recalc()">
                </div>
                <div class="p-input-box">
                    <label>Nights</label>
                    <input type="number" id="p-nights" value="2" min="2" oninput="recalc()">
                </div>
            </div>

            <div class="p-price-row-compact">
                <span style="font-size: 9px; color: #b89b5e; font-weight: bold; display: block;">ESTIMATED TOTAL</span>
                <span class="p-price-val"><span id="p-sum">60</span> €</span>
            </div>

            <button class="btn-update-pay" onclick="pay()">Update and Pay</button>
            <div onclick="location.reload()" style="text-align: center; margin-top: 10px; font-size: 9px; cursor: pointer; color: #aaa; text-transform: uppercase;">Cancel</div>
        </div>
    </div>

    <script>
    function activateCalc() {
        document.getElementById('p-step-1').style.display = 'none';
        document.getElementById('p-calc-view').style.display = 'block';
        recalc();
    }
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