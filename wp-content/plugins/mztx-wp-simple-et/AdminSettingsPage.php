<?php

declare(strict_types=1);

namespace mztx\wp\plugin\SimpleEt;

use function add_action;
use function add_options_page;
use function add_settings_field;
use function add_settings_section;
use function current_user_can;
use function do_settings_sections;
use function get_option;
use function register_setting;
use function settings_fields;
use function submit_button;
use function wp_register_style;

/**
 * Based on https://webfoersterei.de/blog/wordpress-plugins-update-hook/
 */
readonly class AdminSettingsPage
{
    public function __construct()
    {
        add_action(
            'admin_menu',
            fn () => add_options_page(
                'Einstellungen für evangelische-termine.de',
                'evangelische-termine.de',
                'manage_options',
                'mztx-simple-et',
                [$this, 'optionsPageHtml'],
            )
        );

        add_action(
            'admin_init',
            function () {
                // register a new setting for "mztxsimpleet" page
                register_setting('mztxsimpleet', 'mztxsimpleet_styles_css_iframe');
                register_setting('mztxsimpleet', 'mztxsimpleet_styles_css_wp');
                register_setting('mztxsimpleet', 'mztxsimpleet_general_default_vid');

                add_settings_section(
                    'mztxsimpleet_general',
                    'Allgemeine Einstellungen',
                    [$this, 'renderSectionGeneral'],
                    'mztxsimpleet',
                );

                add_settings_field(
                    'mztxsimpleet_general_default_vid',
                    'Veranstalter-ID (VID)',
                    [$this, 'renderFieldVid'],
                    'mztxsimpleet',
                    'mztxsimpleet_general',
                );

                add_settings_section(
                    'mztxsimpleet_styles',
                    'Styles',
                    [$this, 'renderSectionStyles'],
                    'mztxsimpleet',
                );
                add_settings_field(
                    'mztxsimpleet_styles_css_iframe',
                    'iframe-Styles (css)',
                    [$this, 'renderFieldCssIframe'],
                    'mztxsimpleet',
                    'mztxsimpleet_styles',
                );
                add_settings_field(
                    'mztxsimpleet_styles_css_wp',
                    'Seiten-Styles (css)',
                    [$this, 'renderFieldCssWp'],
                    'mztxsimpleet',
                    'mztxsimpleet_styles',
                );
            }
        );

        add_action('admin_enqueue_scripts', [$this, 'adminEnqueueScripts']);
    }

    public function adminEnqueueScripts($hook): void
    {
        if ('settings_page_mztx-simple-et' !== $hook) {
            return;
        }

        wp_register_style(
            'mztx-ev-termine-de-admin-styles',
            plugin_dir_url(__FILE__) . 'static/admin.css',
            [],
            MZTX_WP_SIMPLE_ET_PLUGIN_VERSION
        );
        wp_enqueue_style('mztx-ev-termine-de-admin-styles');
    }

    public function renderFieldVid(): void
    {
        $setting = get_option('mztxsimpleet_general_default_vid');
        ?>
        <input type="number" min="1" max="9999999999" name="mztxsimpleet_general_default_vid" class="mztxsimpleet-general-default-vid" value="<?php echo isset($setting) ? (int) $setting : ''; ?>" />
        <span class="description">Standard-Veranstalter-ID, die verwendet wird, wenn keine andere im Shortcode angegeben ist.</span>
        <?php
    }

    public function renderFieldCssIframe(): void
    {
        $setting = get_option('mztxsimpleet_styles_css_iframe');
        ?>
        <textarea name="mztxsimpleet_styles_css_iframe" class="mztxsimpleet-css-textarea"><?php echo isset($setting) ? esc_attr($setting) : ''; ?></textarea>
        <span class="description">Wird innerhalb der eingebundenen Seite geladen und dient dazu, das Aussehen der eingebundenen Seite anzupassen.</span>
        <?php
    }

    public function renderFieldCssWp(): void
    {
        $setting = get_option('mztxsimpleet_styles_css_wp');
        ?>
        <textarea name="mztxsimpleet_styles_css_wp" class="mztxsimpleet-css-textarea"><?php echo isset($setting) ? esc_attr($setting) : ''; ?></textarea>
        <span class="description">Wird von Wordpress eingebunden und dient dazu, äußere Eigenschaften der Einbindung anzupassen (z.B. Höhe, Breite).</span>
        <?php
    }

    public function renderSectionGeneral(): void
    {
    }

    public function renderSectionStyles(): void
    {
        ?>
        <p>Hier können abweichende Styles definiert werden, um das Aussehen anzupassen.</p>
        <?php
    }

    public function optionsPageHtml(): void
    {
        // check user capabilities
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('mztxsimpleet');
                do_settings_sections('mztxsimpleet');
                submit_button('Speichern');
                ?>
            </form>
        </div>
        <?php
    }
}
