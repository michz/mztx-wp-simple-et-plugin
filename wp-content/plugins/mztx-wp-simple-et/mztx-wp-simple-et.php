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

require_once __DIR__ . '/update.php';

$mztxWpSimpleEtPluginUpdater = new Updater(
    MZTX_WP_SIMPLE_ET_PLUGIN_SLUG,
);
