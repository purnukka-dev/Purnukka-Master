<?php
/**
 * Module: Check-in UI (v1.5 FULL PORT)
 * Siirretty: Alkuperäinen laskenta, kaikki ikonit, tyylit ja maksulinkki.
 */

if (!defined('ABSPATH')) exit;

add_shortcode('purnukka_checkin', function($atts) {
    $config = $GLOBALS['purnukka']->config;
    $accent = $config['design_system']['colors']['accent'] ?? '#b89b5e';
    $primary = $config['design_system']['colors']['primary'] ?? '#1a2b28';
    $villa_name = $config['property_info']['name'] ?? 'Villa Purnukka';

    ob_start(); ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Playfair+Display:ital,wght@0,400;1,400&display=swap" rel="stylesheet">

    <style>
        .site-content, .entry-content { padding-top: 0 !important; }
        .purnukka-welcome-header {
            background: #ffffff;
            padding: 30px 20px 10px 20px; 
            text-align: center;
            margin-top: -60px !important;
        }
        .p-brand-label {
            font-family: 'Montserrat', sans-serif;
            letter-spacing: 2px; font-size: 12px; font-weight: 700;
            color: <?php echo $accent; ?>; text-transform: uppercase;
        }
        .p-form-card {
            background: #fff; border-radius: 15px; padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); max-width: 500px;
            margin: 20px auto; font-family: 'Montserrat', sans-serif;
        }
        .p-input-group { margin-bottom: 20px; text-align: left; }
        .p-input-group label { display: block; font-weight: 700; font-size: 11px; margin-bottom: 8px; }
        .p-input-group input { 
            width: 100%; padding: 14px; border: 1px solid #eee; border-radius: 8px; 
            background: #f9f9f9; font-weight: 700;
        }
        .p-price-display {
            background: #fdfaf4; border: 1px dashed <?php echo $accent; ?>;
            padding: 20px; border-radius: 10px; margin: 25px 0; text-align: center;
        }
        .btn-p-gold {
            background: <?php echo $accent; ?> !important; color: #fff !important;
            width: 100%; padding: 18px; border: none; border-radius: 8px;
            font-weight: 700; font-size: 16px; cursor: pointer; text-transform: uppercase;
        }
        .btn-cancel-link {
            display: block; text-align: center; margin-top: 15px; 
            font-size: 11px; color: #999; text-decoration: underline; cursor: pointer;
        }
    </style>

    <div class="purnukka-welcome-header">
        <div class="p-brand-label">Welcome home</div>
        <h1 style="font-family: 'Playfair Display', serif; font-size: 48px; color: <?php echo $primary; ?>; margin: 10px 0;">
            <?php echo esc_html($villa_name); ?>
        </h1>
    </div>

    <div id="p-step-1" style="text-align: center; padding: 40px;">
        <p style="font-family: 'Montserrat'; color: #666; margin-bottom: 30px;">Finalize your stay details and passenger notification.</p>
        <button class="btn-p-gold" style="max-width: 300px;" onclick="activateForm()">START CHECK-IN</button>
    </div>

    <div id="purnukka-form-view" style="display:none;">
        <div class="p-form-card">
            <div class="p-input-group">
                <label><i class="fa fa-users" style="margin-right:8px;"></i> NUMBER OF GUESTS</label>
                <input type="number" id="p-guests" value="1" min="1" oninput="runRecalc()">
            </div>
            <div class="p-input-group">
                <label><i class="fa fa-calendar-days" style="margin-right:8px;"></i> NIGHTS OF STAY (MIN 2)</label>
                <input type="number" id="p-nights" value="2" min="2" oninput="runRecalc()">
            </div>

            <div class="p-price-display">
                <span id="p-rate-note" style="font-size:10px; font-weight:700; color:<?php echo $accent; ?>; text-transform:uppercase;">STANDARD RATE</span>
                <div style="font-size:36px; font-weight:700; font-family:'Playfair Display'; color: #1a1a1a;">
                    <span id="p-final-sum">60</span> €
                </div>
            </div>

            <button class="btn-p-gold" onclick="proceedToPay()">Update and Secure Payment</button>
            <div class="btn-cancel-link" onclick="location.reload()">Return to Overview</div>
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
        let note = "STANDARD RATE (30€/NIGHT)";

        if (n > 2 && n <= 6) { up = 20; note = "MID-STAY BENEFIT (20€/NIGHT)"; }
        else if (n > 6 && n <= 13) { up = 15; note = "WEEKLY BENEFIT (15€/NIGHT)"; }
        else if (n >= 14) { up = 10; note = "LONG-STAY BENEFIT (10€/NIGHT)"; }

        document.getElementById('p-rate-note').innerText = note;
        document.getElementById('p-final-sum').innerText = g * n * up;
    }

    function proceedToPay() {
        const g = document.getElementById('p-guests').value;
        const n = document.getElementById('p-nights').value;
        // Alkuperäinen linkki ja tuote-ID 276
        window.location.href = '/payment-checkout/?add-to-cart=276&guests=' + g + '&nights=' + n;
    }
    </script>

    <?php
    return ob_get_clean();
});