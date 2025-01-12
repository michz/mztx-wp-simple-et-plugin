<?php

declare(strict_types=1);

namespace mztx\wp\plugin\SimpleEt;

use function add_filter;
use function basename;
use function dirname;
use function file_get_contents;
use function json_decode;

/**
 * Based on https://webfoersterei.de/blog/wordpress-plugins-update-hook/
 */
readonly class Updater
{
    public function __construct(
        private string $slug,
    ) {
        add_filter('update_plugins_michz.github.io', [$this, 'plugin_update_handler'], 10, 4);
    }

    /**
     * @param array<array-key, mixed>|false $updateInfo
     * @param array<array-key, mixed> $pluginHeaders
     * @param array<array-key, string> $language
     * @return object|false
     */
    public function plugin_update_handler(array|false $updateInfo, array $pluginHeaders, string $pluginFile, array $language): object|false
    {
        $requestedSlug = basename(dirname($pluginFile));
        if ($requestedSlug !== $this->slug) {
            return false;
        }

        // @TODO Error handling etc.
        $response = @file_get_contents($pluginHeaders['UpdateURI']);
        if ($response === false) {
            return false;
        }

        return @json_decode($response) ?? false;
    }
}
