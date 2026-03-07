<?php
/**
 * Module: Check-in UI (v1.7.1 MASTER)
 * Description: Alkuperäinen v1.5 ulkoasu säilytetty täysin. 
 * Fix: Hinnat luetaan dynaamisesti context.json-tiedostosta (Analyysin kohta 2).
 */

if (!defined('ABSPATH')) exit;

add_shortcode('purnukka_checkin', function($atts) {
    // 1. HAETAAN DYNAAMINEN DATA (Analyysin korjaus)
    $purnukka_config = $GLOBALS['purnukka']->config;
    $accent_color    = $purnukka_config['design_system']['colors']['accent'] ?? '#b89b5e';
    $primary_color   = $purnukka_config['design_system']['colors']['primary'] ?? '#1a2b28';
    $property_name   = $purnukka_config['property_info']['name'] ?? 'Villa Purnukka';

    // Luetaan hinnat configista tai käytetään alkuperäisiä oletuksia fallbackina 
    $prices = $purnukka_config['pricing']['stay_rates'] ?? [
        'standard' => 30,
        'mid'      => 20,
        'weekly'   => 15,
        'long'     => 10
    ];

    ob_start(); ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Playfair+Display:ital,wght@0,400;1,400&display=swap" rel="stylesheet">

    <style>
        /* SÄILYTETTY: Alkuperäinen MASTER-tyylitys (v1.5)  */
        .site-content, .entry-content, .post-inner { padding-top: 0 !important; margin-top: 0 !important; }
        .purnukka-welcome-header { background: #ffffff; padding: 30px 20px 10px 20px; text-align: center; margin-top: -60px !important; }
        .p-brand-label { font-family: 'Montserrat', sans-serif; font-size: 10px; text-transform: uppercase; letter-spacing: 5px; color: <?php echo $accent_color; ?>; font-weight: bold; display: block; margin-bottom: 8px; }
        .purnukka-welcome-header h1 { font-family: 'Playfair Display', serif; font-size: 48px; color: <?php echo $accent_color; ?>; margin: 0; font-weight: 400; font-style: italic; letter-spacing: -1px; }
        .purnukka-premium-wrapper { font-family: 'Montserrat', sans-serif; max-width: 800px; margin: 0 auto !important; padding: 25px 45px; background: #ffffff; text-align: center; box-shadow: 0px 15px 50px rgba(0,0,0,0.06); border-radius: 4px; border: 1px solid #f0f0f0; z-index: 999; position: relative; }
        .p-top-icon { color: <?php echo $accent_color; ?>; font-size: 28px; margin-bottom: 12px; display: block; }
        .p-step-box { background: #fdfdfd; border: 1px solid #e8e8e8; border-left: 6px solid <?php echo $accent_color; ?>; padding: 20px 30px; display: flex; align-items: center; justify-content: space-between; text-align: left; margin-top: 20px; }
        .btn-p-dark { background: <?php echo $primary_color; ?>; color: #fff; border: none; padding: 12px 25px; font-weight: bold; text-transform: uppercase; font-size: 11px; cursor: pointer; letter-spacing: 1px; transition: 0.3s; }
        .btn-p-dark:hover { background: <?php echo $accent_color; ?>; }
        .p-input-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px; }
        .p-input-field { background: #fff; border: 1px solid <?php echo $accent_color; ?>; padding: 10px 15px; }
        .p-input-field label { display: block; font-size: 9px; color: <?php echo $accent_color; ?>; text-transform: uppercase; font-weight: bold; margin-bottom: 4px; }
        .p-input-field input { border: none; width: 100%; font-weight: bold; font-size: 20px; color: <?php echo $primary_color; ?>; outline: none; background: transparent; height: 24px; }
        .p-price-summary { border-top: 1px solid #f0f0f0; padding-top: 15px; margin-bottom: 20px; text-align: center; }
        .p-price-note { font-size: 10px; color: <?php echo $accent_color; ?>; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .p-price-total { font-size: 38px; font-weight: bold; color: <?php echo $primary_color; ?>; display: block; line-height: 1; margin-top: 5px; }
        .btn-p-gold { background: <?php echo $accent_color; ?> !important; color: #fff !important; border: none; padding: 18px; width: 100%; font-weight: bold; text-transform: uppercase; cursor: pointer; font-size: 13px; letter-spacing: 2px; box-shadow: 0 4px 15px rgba(184, 155, 94, 0.2); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    </style>

    <div class="purnukka-welcome-header">
        <span class="p-brand-label">Purnukka Group</span>
        <h1>Welcome home</h1>
    </div>

    <div class="purnukka-premium-wrapper">
        <i class="fas fa-key p-top-icon"></i> 
        <h2 style="font-family: 'Playfair Display', serif; font-size: 26px; color: <?php echo $primary_color; ?>; margin: 0 0 8px 0; border-bottom: 2px solid <?php echo $accent_color; ?>; display: inline-block; padding-bottom: 5px;">Traveler Declaration & Check-in</h2>
        
        <p style="font-size: 13px; color: #555; margin: 12px auto 25px auto; max-width: 650px; line-height: 1.7; text-align: center; font-style: italic;">
            Your comfort and safety are our priorities. A legal traveler declaration ensures your stay is documented correctly.
        </p>

        <div class="p-step-box" id="p-step-initial">
            <div>
                <strong style="color: <?php echo $primary_color; ?>; font-size: 15px;">Has your group size changed since booking?</strong><br>
                <span style="font-size: 11px; color: #777;">Update your declaration and settle the additional guest fee here.</span>
            </div>
            <button class="btn-p-dark" onclick="toggleFormDisplay()">Yes, add guests</button>
        </div>

        <div id="purnukka-form-view" style="display:none; animation: fadeIn 0.4s ease;">
            <h3 style="font-family: 'Playfair Display', serif; font-size: 20px; color: <?php echo $primary_color; ?>; margin-bottom: 15px; text-align:left;">Update Guest Details</h3>
            
            <div class="p-input-row">
                <div class="p-input-field">
                    <label><i class="fas fa-users"></i> Additional guests (qty)</label>
                    <input type="number" id="guest-count" value="1" min="1" oninput="calculateTotalAmount()">
                </div>
                <div class="p-input-field">
                    <label><i class="fas fa-calendar-day"></i> Nights of stay</label>
                    <input type="number" id="night-count" value="2" min="2" oninput="calculateTotalAmount()">
                </div>
            </div>

            <div class="p-price-summary">
                <span id="rate-information" class="p-price-note">LOADING RATES...</span>
                <span class="p-price-total"><span id="total-sum-display">0</span> €</span>
            </div>

            <button class="btn-p-gold" onclick="initiatePayment()">Update and Secure Payment</button>
            <div style="text-align:center; margin-top:12px;">
                <span class="btn-cancel-link" style="cursor:pointer; color:<?php echo $accent_color; ?>; font-size:11px;" onclick="location.reload()">Return to Overview</span>
            </div>
        </div>
    </div>

    <script>
    [cite_start]// SÄILYTETTY: Alkuperäinen JS-logiikka, mutta hinnat haetaan PHP:sta [cite: 10, 11]
    const purnukkaRates = <?php echo json_encode($prices); ?>;

    function toggleFormDisplay() {
        document.getElementById('p-step-initial').style.display = 'none';
        document.getElementById('purnukka-form-view').style.display = 'block';
        calculateTotalAmount();
    }

    function calculateTotalAmount() {
        const guests = parseInt(document.getElementById('guest-count').value) || 0;
        let nights = parseInt(document.getElementById('night-count').value) || 0;
        if (nights < 2) nights = 2;
        
        let unitPrice = purnukkaRates.standard;
        let rateLabel = "STANDARD RATE (" + unitPrice + "€/NIGHT)";
        
        if (nights > 2 && nights <= 6) { 
            unitPrice = purnukkaRates.mid; 
            rateLabel = "MID-STAY BENEFIT (" + unitPrice + "€/NIGHT)"; 
        } else if (nights > 6 && nights <= 13) { 
            unitPrice = purnukkaRates.weekly; 
            rateLabel = "WEEKLY BENEFIT (" + unitPrice + "€/NIGHT)"; 
        } else if (nights >= 14) { 
            unitPrice = purnukkaRates.long; 
            rateLabel = "LONG-STAY BENEFIT (" + unitPrice + "€/NIGHT)"; 
        }
        
        document.getElementById('total-sum-display').innerText = guests * nights * unitPrice;
        document.getElementById('rate-information').innerText = rateLabel;
    }

    function initiatePayment() {
        const amount = document.getElementById('total-sum-display').innerText;
        window.location.href = window.location.origin + '/payment-checkout/?add-to-cart=276&quantity=' + amount;
    }
    </script>

    <?php
    return ob_get_clean();
});