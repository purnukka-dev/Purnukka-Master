<?php
/**
 * Plugin Name: Purnukka Check-in UI (Master)
 * Description: Dynaaminen matkustajailmoituksen laskuri Purnukka Groupin kohteisiin.
 * Version: 1.0.0
 * Author: Purnukka Group Master
 */

if (!defined('ABSPATH')) exit;

add_shortcode('purnukka_checkin', function($atts) {
    // 1. MASTER-ASETUKSET (Oletuksena Master-tuote ID 276)
    $a = shortcode_atts(array(
        'hinta'    => '30',
        'minimi'   => '2',
        'tuote_id' => '276', // Master-tuote
        'otsikko'  => 'Tervetuloa kotiin'
    ), $atts);

    ob_start(); ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* --- MASTER UI STYLES --- */
        .p-master-header {
            background: #ffffff;
            padding: 50px 20px;
            text-align: center;
            border-bottom: 1px solid #f0f0f0;
        }
        .p-master-brand {
            font-family: 'Montserrat', sans-serif;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: #b89b5e;
            display: block;
            margin-bottom: 10px;
            font-weight: 700;
        }
        .p-master-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(26px, 7vw, 38px);
            color: #1a2b28;
            margin: 0;
            font-weight: 400;
        }
        .p-master-wrapper {
            font-family: 'Montserrat', sans-serif;
            max-width: 850px;
            margin: -30px auto 50px auto;
            padding: 40px;
            background: #ffffff;
            text-align: center; 
            box-shadow: 0px 15px 40px rgba(0,0,0,0.06);
            border: 1px solid #eee;
            position: relative;
            z-index: 5;
        }
        .p-master-icon { color: #b89b5e; font-size: 32px; margin-bottom: 15px; display: block; }
        .p-master-box {
            background: #fdfdfd;
            border: 1px solid #1a2b28;
            border-left: 6px solid #b89b5e; 
            padding: 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-align: left;
            margin-top: 25px;
            gap: 20px;
        }
        .p-btn-dark {
            background: #1a2b28; color: #fff; border: none; padding: 14px 22px;
            font-weight: 700; text-transform: uppercase; font-size: 11px; cursor: pointer; transition: 0.3s;
        }
        .p-btn-dark:hover { background: #b89b5e; }
        #p-master-form { display: none; margin-top: 30px; text-align: left; animation: pFade 0.4s; }
        .p-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; }
        .p-input-box { border: 1px solid #b89b5e; padding: 12px; background: #fff; }
        .p-input-box label { display: block; font-size: 10px; color: #888; text-transform: uppercase; font-weight: 700; }
        .p-input-box input { border: none; width: 100%; font-weight: 700; font-size: 20px; color: #1a2b28; outline: none; }
        .p-price-display { border-top: 1px solid #eee; padding-top: 20px; text-align: center; margin-bottom: 25px; }
        .p-price-total { font-size: 36px; font-weight: 700; color: #1a2b28; display: block; }
        .p-price-note { font-size: 11px; color: #b89b5e; font-weight: 700; text-transform: uppercase; }
        .p-btn-gold {
            background: #b89b5e; color: #fff; border: none; padding: 16px; width: 100%;
            font-weight: 700; text-transform: uppercase; cursor: pointer; font-size: 12px;
        }
        @keyframes pFade { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
        @media (max-width: 600px) {
            .p-master-box { flex-direction: column; text-align: center; }
            .p-btn-dark { width: 100%; }
            .p-grid { grid-template-columns: 1fr; }
        }
    </style>

    <div id="p-master-app" 
         data-hinta="<?php echo esc_attr($a['hinta']); ?>" 
         data-minimi="<?php echo esc_attr($a['minimi']); ?>" 
         data-pid="<?php echo esc_attr($a['tuote_id']); ?>">

        <div class="p-master-header">
            <span class="p-master-brand">Purnukka Group Master</span>
            <h1><?php echo esc_html($a['otsikko']); ?></h1>
        </div>

        <div class="p-master-wrapper">
            <i class="fas fa-key p-master-icon"></i>
            <h2 style="font-family: 'Playfair Display', serif; font-size: 26px; color: #1a2b28; margin-bottom: 10px;">Check-in & Matkustajailmoitus</h2>
            <p style="font-size: 14px; color: #666;">Varmistathan majoittujien tiedot vakuutusturvaa varten.</p>

            <div class="p-master-box" id="p-gate-master">
                <div>
                    <strong>Onko seurueenne koko muuttunut?</strong><br>
                    <span style="font-size: 12px;">Lisää puuttuvat henkilöt varaukseenne tästä.</span>
                </div>
                <button class="p-btn-dark" onclick="initPurnukkaMaster()">Lisää henkilöitä</button>
            </div>

            <div id="p-master-form">
                <div class="p-grid">
                    <div class="p-input-box">
                        <label>Lisähenkilöt (kpl)</label>
                        <input type="number" id="p-m-guests" value="1" min="1" oninput="recalcPurnukkaMaster()">
                    </div>
                    <div class="p-input-box">
                        <label>Yöpymiset (vrk)</label>
                        <input type="number" id="p-m-nights" value="<?php echo esc_attr($a['minimi']); ?>" min="<?php echo esc_attr($a['minimi']); ?>" oninput="recalcPurnukkaMaster()">
                    </div>
                </div>
                <div class="p-price-display">
                    <span id="p-m-note" class="p-price-note">LASKETAAN...</span>
                    <span class="p-price-total"><span id="p-m-sum">0</span> €</span>
                </div>
                <button class="p-btn-gold" onclick="payPurnukkaMaster()">Päivitä ja Maksa</button>
                <div onclick="location.reload()" style="text-align: center; margin-top: 15px; font-size: 11px; cursor: pointer; color: #888; text-transform: uppercase;">Peruuta</div>
            </div>
        </div>
    </div>

    <script>
    function initPurnukkaMaster() {
        document.getElementById('p-gate-master').style.display = 'none';
        document.getElementById('p-master-form').style.display = 'block';
        recalcPurnukkaMaster();
    }
    function recalcPurnukkaMaster() {
        const app = document.getElementById('p-master-app');
        const hBase = parseInt(app.getAttribute('data-hinta'));
        const g = parseInt(document.getElementById('p-m-guests').value) || 0;
        let n = parseInt(document.getElementById('p-m-nights').value) || 0;
        const mN = parseInt(app.getAttribute('data-minimi'));
        if (n < mN) n = mN;

        let curP = hBase;
        let note = "PERUSHINTA ("+hBase+"€/YÖ)";
        
        if (n > 2 && n <= 6) { curP = 20; note = "KESKIPITKÄ ETU (20€/YÖ)"; }
        else if (n > 6 && n <= 13) { curP = 15; note = "VIIKKOETU (15€/YÖ)"; }
        else if (n >= 14) { curP = 10; note = "PITKÄAIKAISETU (10€/YÖ)"; }

        document.getElementById('p-m-sum').innerText = g * n * curP;
        document.getElementById('p-m-note').innerText = note;
    }
    function payPurnukkaMaster() {
        const app = document.getElementById('p-master-app');
        const pid = app.getAttribute('data-pid');
        const sum = document.getElementById('p-m-sum').innerText;
        window.location.href = window.location.origin + '/checkout/?add-to-cart=' + pid + '&quantity=' + sum;
    }
    </script>

    <?php
    return ob_get_clean();
});