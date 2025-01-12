<?php

/**
 * The plugin bootstrap file
 *
 * @wordpress-plugin
 * Plugin Name:       mztx Simple Integration von evangelische-termine.de
 * Plugin URI:        https://mztx.de
 * Description:       Sehr einfache und nicht mächtige Möglichkeit, evangelische-termine.de einzubinden
 * Version:           {{ version }}
 * Author:            Michael Zapf
 * Author URI:        https://mztx.de
 * Update URI:        https://michz.github.io/mztx-wp-simple-et-plugin/update.json
 * License:           MIT
 * License URI:       https://raw.githubusercontent.com/michz/mztx-wp-simple-et/refs/heads/main/LICENSE
 */

declare(strict_types=1);

namespace mztx\wp\plugin\SimpleEt;

const MZTX_WP_SIMPLE_ET_PLUGIN_SLUG = 'mztx-wp-simple-et';
const MZTX_WP_SIMPLE_ET_PLUGIN_VERSION = '{{ version }}';
const MZTX_EV_TERMINE_NAMESPACE_PREFIX = 'mztx\\wp\\plugin\\SimpleEt\\';

$mztxEvTermineNamespacePrefixLength = \strlen(MZTX_EV_TERMINE_NAMESPACE_PREFIX);
\spl_autoload_register(function (string $class) use ($mztxEvTermineNamespacePrefixLength) {
    if (false === \str_starts_with($class, MZTX_EV_TERMINE_NAMESPACE_PREFIX)) {
        return;
    }

    $path = \str_replace('\\', '/', \substr($class, $mztxEvTermineNamespacePrefixLength));
    require_once __DIR__ . '/' . $path . '.php';
});

$pluginBaseUrl = \plugin_dir_url(__FILE__);

(new Shortcode($pluginBaseUrl));
(new AdminSettingsPage());
(new Router());
(new Updater(MZTX_WP_SIMPLE_ET_PLUGIN_SLUG));
