<?php
/**
 * Plugin Name: Purnukka Check-in Master (Wide Layout & Golden Title)
 * Description: Wide layout with golden 'Purnukka Group' title, English texts, ID 276.
 */

if (!defined('ABSPATH')) exit;

add_shortcode('purnukka_checkin', function($atts) {
    ob_start(); ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Playfair+Display&display=swap" rel="stylesheet">

    <style>
        /* TAVOITELTU LEVEÄ ASettelu (image_4.png mukainen) */
        .p-master-layout-container {
            font-family: 'Montserrat', sans-serif;
            max-width: 950px !important; /* Pakotetaan leveys tässä, kuten image_4.png */
            margin: -80px auto 40px auto !important; /* Negatiivinen margin nostaa laatikon ylös */
            padding: 40px !important;
            background: #ffffff !important;
            text-align: center !important; 
            box-shadow: 0px 20px 50px rgba(0,0,0,0.06) !important;
            border-radius: 4px !important;
            border: 1px solid #f0f0f0 !important;
            box-sizing: border-box !important;
            position: relative;
            z-index: 99;
        }

        /* 1. KULTAINEN PURNUKKA GROUP -OTSIKKO */
        .p-gold-title-section {
            padding: 0 0 30px 0;
            text-align: center;
        }

        .p-brand-label-gold {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 5px;
            color: #b89b5e; /* Kultainen väri, kuten image_4.png */
            font-weight: bold;
            display: block;
            margin-bottom: 12px;
        }

        .p-main-title-dark {
            font-family: 'Playfair Display', serif;
            font-size: 42px; /* Suuri otsikko, kuten image_4.png */
            color: #1a2b28;
            margin: 0;
            font-weight: 400;
        }

        /* 2. PREMIUM CONTENT BOX */
        .p-premium-content-box {
            border-top: 1px solid #f0f0f0;
            padding-top: 35px;
            position: relative;
        }

        .p-top-icon-gold { color: #b89b5e; font-size: 36px; margin-bottom: 25px; display: block; }

        .p-checkin-title-line {
            font-family: 'Playfair Display', serif;
            font-size: 30px;
            color: #1a2b28;
            margin: 0 0 15px 0;
            border-bottom: 2px solid #b89b5e; /* Kultainen viiva alla */
            display: inline-block;
            padding-bottom: 8px;
        }

        /* 3. STEP-BOX & INLINE CALCULATOR ROW (Wide Layout) */
        .p-step-box {
            background: #fdfdfd;
            border: 1px solid #1a2b28;
            border-left: 8px solid #b89b5e; /* Paksu kultainen reuna */
            padding: 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-align: left;
            margin: 30px 0;
            gap: 20px;
        }

        /* TÄMÄ ON SE LEVEÄ RIVI (Laskuri vierekkäin) */
        #purnukka-form-view {
            display: none;
            margin: 30px 0;
            animation: fadeIn 0.4s ease-out;
        }

        .p-input-row-wide {
            display: flex !important;
            flex-direction: row !important;
            justify-content: space-between !important;
            align-items: center !important;
            border: 1px solid #b89b5e; /* Kultainen kehys */
            padding: 20px;
            gap: 20px;
            margin-bottom: 30px;
        }

        .p-input-group-compact { flex: 1; text-align: left; }
        .p-input-group-compact label { display: block; font-size: 9px; color: #888; text-transform: uppercase; font-weight: bold; margin-bottom: 5px; }
        .p-input-group-compact input { border: none; width: 100%; font-size: 20px; font-weight: bold; color: #1a2b28; outline: none; background: transparent; }

        .p-price-display-gold {
            border-left: 1px solid #eee;
            padding-left: 20px;
            text-align: right;
            min-width: 90px;
        }
        .p-price-display-gold label { font-size: 9px; color: #b89b5e; font-weight: bold; text-transform: uppercase; }
        .p-price-display-gold .p-total-val { font-size: 36px; font-weight: bold; color: #1a2b28; display: block; }

        /* BUTTONS & FOOTER */
        .btn-p-gold-wide {
            background: #b89b5e; color: #fff; border: none; padding: 20px;
            width: 100%; font-weight: bold; text-transform: uppercase;
            cursor: pointer; font-size: 13px; letter-spacing: 1px;
        }

        .btn-p-dark-compact {
            background: #1a2b28; color: #fff; border: none; padding: 12px 20px;
            font-weight: bold; text-transform: uppercase; font-size: 10px;
            cursor: pointer; white-space: nowrap;
        }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* MOBILE OPTIMIZATION */
        @media (max-width: 900px) {
            .p-master-layout-container { margin-top: 20px !important; padding: 25px !important; }
            .p-step-box, .p-input-row-wide { flex-direction: column !important; text-align: center !important; }
            .p-price-display-gold { border-left: none; border-top: 1px solid #eee; padding: 15px 0 0 0; width: 100%; text-align: center; }
            .p-main-title-dark { font-size: 32px; }
        }
    </style>

    <div class="p-master-layout-container">
        
        <div class="p-gold-title-section">
            <span class="p-brand-label-gold">Purnukka Group</span>
            <h1 class="p-main-title-dark">Welcome Home</h1>
        </div>

        <div class="p-premium-content-box">
            <i class="fas fa-key p-top-icon-gold"></i> 
            <h2 class="p-checkin-title-line">Traveler Declaration & Check-in</h2>
            <p style="font-size: 14px; color: #666; margin: 15px auto 35px auto; max-width: 650px; line-height: 1.6;">
                Alegal traveler declaration ensures your insurance coverage valid throughout your visit.
            </p>

            <div class="p-step-box" id="p-step-1">
                <div>
                    <strong style="color: #1a2b28; font-size: 16px;">Has your group size changed?</strong><br>
                    <span style="font-size: 12px; color: #666;">You can add and pay for additional guests here.</span>
                </div>
                <button class="btn-p-dark-compact" onclick="activateForm()">Yes, add guests</button>
            </div>

            <div id="purnukka-form-view">
                <div class="p-input-row-wide">
                    <div class="p-input-group-compact">
                        <label><i class="fas fa-users"></i> Guests</label>
                        <input type="number" id="p-guests" value="1" min="1" oninput="runRecalc()">
                    </div>
                    <div class="p-input-group-compact">
                        <label><i class="fas fa-moon"></i> Nights</label>
                        <input type="number" id="p-nights" value="2" min="2" oninput="runRecalc()">
                    </div>
                    <div class="p-price-display-gold">
                        <label>Total</label>
                        <span class="p-total-val"><span id="p-final-sum">60</span> €</span>
                    </div>
                </div>

                <button class="btn-p-gold-wide" onclick="proceedToPay()">Update and Pay</button>
                <div onclick="location.reload()" style="text-align: center; margin-top: 15px; font-size: 10px; cursor: pointer; color: #888; text-transform: uppercase;">Cancel</div>
            </div>

            <div style="margin-top: 60px; text-align: left; border-top: 1px solid #eee; padding-top: 50px;">
                <?php echo do_shortcode('[formidable id=4]'); ?>
            </div>
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
        document.getElementById('p-final-sum').innerText = g * n * up;
    }

    function proceedToPay() {
        const sum = document.getElementById('p-final-sum').innerText;
        // Master Product ID 276
        window.location.href = window.location.origin + '/checkout/?add-to-cart=276&quantity=' + sum;
    }
    </script>

    <?php
    return ob_get_clean();
});