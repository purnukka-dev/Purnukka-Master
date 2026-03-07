<?php
/**
 * Module: Check-in UI (v1.7.0)
 * Description: Dynaaminen vierashallinta ja saapumiskokemus. 
 * Fix: Hinnat luetaan dynaamisesti context.json-tiedostosta.
 * File: checkin-ui.php
 */

if (!defined('ABSPATH')) exit;

class Purnukka_Checkin_UI {
    public function __construct() {
        add_shortcode('purnukka_checkin', [$this, 'render_ui']);
    }

    public function render_ui() {
        $config = $GLOBALS['purnukka']->config;
        
        // Luetaan dynaamiset hinnat tai käytetään alkuperäisiä oletuksia (Analyysin kohta 2)
        $prices = $config['pricing']['extra_guests'] ?? [
            'adult' => 30,
            'child' => 20,
            'infant' => 15,
            'pet'   => 10
        ];

        ob_start();
        ?>
        <style>
            .purnukka-checkin-container { font-family: 'Inter', sans-serif; max-width: 600px; margin: auto; }
            /* ... kaikki alkuperäiset CSS-tyylit tässä ... */
        </style>

        <div class="purnukka-checkin-container">
            <div id="guest-selector">
                <h2>Ketä on tulossa?</h2>
                <div class="guest-row" data-type="adult">
                    <span>Aikuiset (<span class="price-tag"><?php echo $prices['adult']; ?></span>€)</span>
                    <div class="controls">
                        <button type="button" class="minus">-</button>
                        <span class="count">0</span>
                        <button type="button" class="plus">+</button>
                    </div>
                </div>
                </div>
        </div>

        <script>
        (function($) {
            // Injektoidaan PHP:lla haetut dynaamiset hinnat JavaScriptiin
            const guestPrices = <?php echo json_encode($prices); ?>;

            $(document).ready(function() {
                $('.plus, .minus').on('click', function() {
                    const row = $(this).closest('.guest-row');
                    const type = row.data('type');
                    let count = parseInt(row.find('.count').text());

                    if ($(this).hasClass('plus')) {
                        count++;
                    } else if (count > 0) {
                        count--;
                    }

                    row.find('.count').text(count);
                    updateTotal();
                });

                function updateTotal() {
                    let total = 0;
                    $('.guest-row').each(function() {
                        const type = $(this).data('type');
                        const count = parseInt($(this).find('.count').text());
                        total += count * (guestPrices[type] || 0);
                    });
                    $('#total-price').text(total + '€');
                }
            });
        })(jQuery);
        </script>
        <?php
        return ob_get_clean();
    }
}

new Purnukka_Checkin_UI();