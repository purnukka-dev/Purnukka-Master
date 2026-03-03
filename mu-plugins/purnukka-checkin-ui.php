<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    /* 1. PREMIUM HEADER - PUHDAS JA RAikas */
    .purnukka-welcome-header {
        background: #ffffff;
        padding: 60px 20px 40px 20px;
        text-align: center;
        border-bottom: 1px solid #f0f0f0;
    }

    .p-brand-label {
        font-family: 'Montserrat', sans-serif;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 4px;
        color: #b89b5e;
        display: block;
        margin-bottom: 10px;
        font-weight: bold;
    }

    .purnukka-welcome-header h1 {
        font-family: 'Playfair Display', serif;
        font-size: clamp(28px, 8vw, 42px);
        color: #1a2b28;
        margin: 0;
        font-weight: 400;
        letter-spacing: -1px;
    }

    /* 2. MASTER CONTAINER - LEIJUVA BALANSSI */
    .purnukka-premium-wrapper {
        font-family: 'Montserrat', sans-serif;
        max-width: 850px;
        margin: -30px auto 60px auto; /* Nousee hieman headerin päälle */
        padding: 40px;
        background: #ffffff;
        text-align: center; 
        box-shadow: 0px 20px 50px rgba(0,0,0,0.06);
        border-radius: 4px;
        border: 1px solid #f0f0f0;
        box-sizing: border-box;
        position: relative;
        z-index: 10;
    }

    .p-top-icon { color: #b89b5e; font-size: 36px; margin-bottom: 20px; display: block; }

    /* 3. VAIHE 1: ALOITUSLAATIKKO */
    .p-step-box {
        background: #fdfdfd;
        border: 1px solid #1a2b28;
        border-left: 6px solid #b89b5e; 
        padding: 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        text-align: left;
        margin-top: 30px;
        gap: 20px;
    }

    .btn-p-dark {
        background: #1a2b28;
        color: #fff;
        border: none;
        padding: 14px 25px;
        font-weight: bold;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 1px;
        cursor: pointer;
        transition: 0.3s ease;
        white-space: nowrap;
    }

    .btn-p-dark:hover { background: #b89b5e; }

    /* 4. VAIHE 2: LOMAKE (Dynaaminen) */
    #purnukka-form-view {
        display: none;
        margin-top: 30px;
        text-align: left;
        animation: fadeIn 0.4s ease-out;
    }

    .p-input-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 25px;
    }

    .p-input-field {
        background: #fff;
        border: 1px solid #b89b5e;
        padding: 15px;
    }

    .p-input-field label {
        display: block;
        font-size: 10px;
        color: #888;
        text-transform: uppercase;
        margin-bottom: 5px;
        font-weight: bold;
    }

    .p-input-field input {
        border: none;
        width: 100%;
        font-weight: bold;
        font-size: 22px;
        color: #1a2b28;
        outline: none;
        background: transparent;
    }

    .p-price-summary {
        border-top: 2px solid #f8f8f8;
        padding-top: 25px;
        margin-bottom: 30px;
        text-align: center;
    }

    .p-price-note { font-size: 11px; color: #b89b5e; font-weight: bold; text-transform: uppercase; }
    .p-price-total { font-size: 40px; font-weight: bold; color: #1a2b28; display: block; }

    .btn-p-gold {
        background: #b89b5e;
        color: #fff;
        border: none;
        padding: 18px;
        width: 100%;
        font-weight: bold;
        text-transform: uppercase;
        cursor: pointer;
        font-size: 13px;
        transition: 0.3s;
    }

    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    /* 📱 MOBIILI-OPTIMOINTI */
    @media (max-width: 650px) {
        .purnukka-premium-wrapper { padding: 30px 15px; margin-top: -20px; }
        .p-step-box { flex-direction: column; text-align: center; padding: 25px; }
        .btn-p-dark { width: 100%; }
        .p-input-row { grid-template-columns: 1fr; }
        .p-price-total { font-size: 32px; }
    }
</style>

<div class="purnukka-welcome-header">
    <span class="p-brand-label">Purnukka Group</span>
    <h1>Tervetuloa Villa Purnukkaan</h1>
</div>

<div class="purnukka-premium-wrapper">
    <i class="fas fa-key p-top-icon"></i> 
    <h2 style="font-family: 'Playfair Display', serif; font-size: 28px; color: #1a2b28; margin: 0 0 15px 0; border-bottom: 2px solid #b89b5e; display: inline-block; padding-bottom: 6px;">Matkustajailmoitus & Check-in</h2>
    <p style="font-size: 14px; color: #666; margin: 10px auto 30px auto; max-width: 650px; line-height: 1.6;">
        Lainmukainen matkustajailmoitus takaa teille turvallisen loman ja varmistaa vakuutusturvan voimassaolon koko viipymänne ajaksi.
    </p>

    <div class="p-step-box" id="p-step-1">
        <div>
            <strong style="color: #1a2b28; font-size: 16px;">Onko seurueenne koko muuttunut?</strong><br>
            <span style="font-size: 12px; color: #666;">Voit lisätä ja maksaa puuttuvat henkilöt tästä.</span>
        </div>
        <button class="btn-p-dark" onclick="activateForm()">Kyllä, lisää henkilöitä</button>
    </div>

    <div id="purnukka-form-view">
        <h3 style="font-family: 'Playfair Display', serif; font-size: 22px; color: #1a2b28; margin-bottom: 20px; text-align: left;">Lisää henkilöitä varaukseen</h3>
        
        <div class="p-input-row">
            <div class="p-input-field">
                <label><i class="fas fa-users"></i> Lisähenkilöt (kpl)</label>
                <input type="number" id="p-guests" value="1" min="1" oninput="runRecalc()">
            </div>
            <div class="p-input-field">
                <label><i class="fas fa-moon"></i> Yöpymiset (vrk)</label>
                <input type="number" id="p-nights" value="2" min="2" oninput="runRecalc()">
            </div>
        </div>

        <div class="p-price-summary">
            <span id="p-info" class="p-price-note">PERUSHINTA (30€/YÖ)</span>
            <span class="p-price-total"><span id="p-final-sum">60</span> €</span>
        </div>

        <button class="btn-p-gold" onclick="proceedToPay()">Päivitä ja Maksa</button>
        <div onclick="location.reload()" style="text-align: center; margin-top: 15px; font-size: 11px; cursor: pointer; color: #888; text-transform: uppercase; letter-spacing: 1px;">Peruuta</div>
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
    
    // Pakotetaan minimi 2 yötä
    if (n < 2) n = 2;

    let up = 30;
    let note = "PERUSHINTA (30€/YÖ)";

    // Purnukka Smart Pricing -portaat
    if (n > 2 && n <= 6) { up = 20; note = "KESKIPITKÄ ETU (20€/YÖ)"; }
    else if (n > 6 && n <= 13) { up = 15; note = "VIIKKOETU (15€/YÖ)"; }
    else if (n >= 14) { up = 10; note = "PITKÄAIKAISETU (10€/YÖ)"; }

    document.getElementById('p-final-sum').innerText = g * n * up;
    document.getElementById('p-info').innerText = note;
}

function proceedToPay() {
    const g = document.getElementById('p-guests').value;
    let n = parseInt(document.getElementById('p-nights').value);
    if (n < 2) n = 2;
    
    let up = 30;
    if (n > 2 && n <= 6) up = 20;
    else if (n > 6 && n <= 13) up = 15;
    else if (n >= 14) up = 10;
    
    const finalVal = g * n * up;
    // Ohjaus kassalle tuotteella 3775 (1€ yksikkö)
    window.location.href = window.location.origin + '/checkout/?add-to-cart=3775&quantity=' + finalVal;
}
</script>