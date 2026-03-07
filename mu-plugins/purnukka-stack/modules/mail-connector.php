<?php
/**
 * Module: Mail Connector (v1.6.0 MASTER)
 * Description: Automatically synchronizes SMTP settings and locks the UI when active.
 * Refactor: Constructor Injection.
 */

if (!defined('ABSPATH')) exit;

class Purnukka_Mail_Connector {
    private $core;

    public function __construct($core) {
        if (!$core) return;
        $this->core = $core;

        // 1. LOGIC: SMTP SYNCHRONIZATION
        add_action('admin_init', [$this, 'sync_smtp_settings']);

        // 2. UI: SMART LOCKING
        add_action('admin_footer', [$this, 'lock_smtp_fields']);
    }

    /**
     * Forces the WP Mail SMTP settings from context.json.
     */
    public function sync_smtp_settings() {
        $config = $this->core->config;
        if (empty($config['technical']['smtp_host'])) return;

        $tech = $config['technical'];
        $info = $config['property_info'] ?? [];

        $smtp_options = [
            'mail' => [
                'from_email' => $info['email'] ?? get_option('admin_email'),
                'from_name'  => $info['name'] ?? get_bloginfo('name'),
                'mailer'     => 'smtp',
                'return_path' => true
            ],
            'smtp' => [
                'host' => $tech['smtp_host'],
                'port' => $tech['smtp_port'] ?? 587,
                'auth' => true,
                'user' => $tech['smtp_user'],
                'pass' => $tech['smtp_pass'],
                'encryption' => $tech['smtp_encryption'] ?? 'tls',
                'autotls'     => true
            ]
        ];

        update_option('wp_mail_smtp', $smtp_options);
    }

    /**
     * Since this module is LOADED (On), we lock the manual SMTP fields.
     */
    public function lock_smtp_fields() {
        $screen = get_current_screen();
        if (!$screen || strpos($screen->id, 'wp-mail-smtp') === false) return;
        ?>
        <script>
        jQuery(document).ready(function($) {
            // Valitaan SMTP-asetusten rivit ja syöttökentät
            const smtpSelectors = [
                '.wp-mail-smtp-setting-row-general-mail-from_email input',
                '.wp-mail-smtp-setting-row-general-mail-from_name input',
                '.wp-mail-smtp-setting-row-smtp input',
                'select[name="wp-mail-smtp[mail][mailer]"]',
                'input[name="wp-mail-smtp[smtp][host]"]',
                'input[name="wp-mail-smtp[smtp][user]"]',
                'input[name="wp-mail-smtp[smtp][pass]"]'
            ];
            
            // Lukitaan kentät
            $(smtpSelectors.join(', ')).prop('readonly', true).css({
                'background-color': '#f0f0f1',
                'opacity': '0.7',
                'pointer-events': 'none'
            });

            // Lisätään selkeä Purnukka-ilmoitus yläreunaan
            $('.wp-mail-smtp-page-title, .wp-mail-smtp-setting-row-general-mail-from_email').first().after(
                '<div class="notice notice-info"><p><strong>Purnukka Stack Master Control:</strong> Sähköpostiautomaatio on PÄÄLLÄ (On). Asetukset on lukittu muokkaukselta.</p></div>'
            );
        });
        </script>
        <?php
    }
}