<?php
/**
 * Plugin Name: Purnukka Check-in Master (Full-Space Lock)
 * Description: English, ID 276. Locked to fill 100% of available screen height.
 */

if (!defined('ABSPATH')) exit;

add_shortcode('purnukka_checkin', function($atts) {
    ob_start(); ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Playfair+Display&display=swap" rel="stylesheet">

    <style>
        /* LUKITAAN KOKO SIVUN KORKEUS */
        .p-locked-dashboard {
            font-family: 'Montserrat', sans-serif;
            max-width: 950px;
            min-height: 85vh; /* Pakotetaan viemään lähes koko ruudun korkeus */
            margin: -160px auto 0 auto !important;
            padding: 60px 50px !important;
            background: #ffffff;
            text-align: center; 
            box-shadow: 0px 30px 60px rgba(0,0,0,0.1);
            border-radius: 4px;
            border: 1px solid #f0f0f0;
            box-sizing: border-box;
            position: relative;
            z-index: 999;
            display: flex;
            flex-direction: column;
            justify-content: center; /* Keskittää sisällön pystysuunnassa laatikon sisällä */
        }

        .p-brand-gold { font-size: 11px; text-transform: uppercase; letter-spacing: 6px; color: #b89b5e; font-weight: bold; margin-bottom: 15px; display: block; }
        .p-title-main { font-family: 'Playfair Display', serif; font-size: 42px; color: #1a2b28; margin: 0 0 30px 0; }

        /* 2+1 ASettelu (Suuret laatikot kuten suomenkielisessä) */
        .p-step-box-locked {
            background: #fdfdfd;
            border: 1px solid #1a2b28;
            border-left: 8px solid #b89b5e; 
            padding: 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-align: left;
            margin-bottom: 20px;
        }

        #p-calc-locked-view { display: none; animation: fadeIn 0.4s ease-out; }

        .p-grid-2-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .p-input-card {
            border: 1px solid #b89b5e;
            padding: 20px;
            text-align: left;
            background: #fff;
        }

        .p-input-card label { display: block; font-size: 10px; color: #888; text-transform: uppercase; font-weight: bold; margin-bottom: 8px; }
        .p-input-card input { border: none; width: 100%; font-size: 24px; font-weight: bold; color: #1a2b28; outline: none; background: transparent; }

        .p-price-card-wide {
            background: #fdfdfd;
            border: 1px solid #1a2b28;
            padding: 30px;
            margin-bottom: 25px;
        }

        .p-price-val { font-size: 48px; font-weight: bold; color: #1a2b28; display: block; }

        .btn-pay-locked {
            background: #b89b5e; color: #fff; border: none; padding: 25px;
            width: 100%; font-weight: bold; text-transform: uppercase;
            cursor: pointer; font-size: 14px; letter-spacing: 2px;
            transition: 0.3s;
        }

        .btn-pay-locked:hover { background: #1a2b28; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        @media (max-width: 800px) {
            .p-locked-dashboard { margin-top: 0 !important; min-height: auto; }
            .p-grid-2-col { grid-template-columns: 1fr; }
            .p-step-box-locked { flex-direction: column; text-align: center; gap: 20px; }
        }
    </style>

    <div class="p-locked-dashboard">
        <span class="p-brand-gold">Purnukka Group</span>
        <h1 class="p-title-main">Welcome Home</h1>

        <div style="border-bottom: 2px solid #b89b5e; width: 80px; margin: 0 auto 40px auto;"></div>

        <h2 style="font-family: 'Playfair Display', serif; font-size: 28px; color: #1a2b28; margin-bottom: 15px;">Traveler Declaration & Check-in</h2>
        
        <div class="p-step-box-locked" id="p-step-1">
            <div>
                <strong style="font-size: 18px; color: #1a2b28;">Has your group size changed?</strong><br>
                <span style="font-size: 13px; color: #666;">You can add and pay for additional guests here.</span>
            </div>
            <button onclick="unlockCalc()" style="background:#1a2b28; color:#fff; border:none; padding:15px 25px; cursor:pointer; font-weight:bold; text-transform:uppercase; font-size:11px;">Add guests</button>
        </div>

        <div id="p-calc-locked-view">
            <div class="p-grid-2-col">
                <div class="p-input-card">
                    <label>Additional Guests</label>
                    <input type="number" id="p-guests" value="1" min="1" oninput="recalc()">
                </div>
                <div class="p-input-card">
                    <label>Nights of Stay</label>
                    <input type="number" id="p-nights" value="2" min="2" oninput="recalc()">
                </div>
            </div>

            <div class="p-price-card-wide">
                <span style="font-size: 11px; color: #b89b5e; font-weight: bold; text-transform: uppercase;">Estimated Total</span>
                <span class="p-price-val"><span id="p-sum">60</span> €</span>
            </div>

            <button class="btn-pay-locked" onclick="pay()">Update and Pay Now</button>
            <div onclick="location.reload()" style="text-align: center; margin-top: 20px; font-size: 11px; cursor: pointer; color: #aaa; text-transform: uppercase; letter-spacing: 1px;">Cancel / Return</div>
        </div>
    </div>

    <script>
    function unlockCalc() {
        document.getElementById('p-step-1').style.display = 'none';
        document.getElementById('p-calc-locked-view').style.display = 'block';
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