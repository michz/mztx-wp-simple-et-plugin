<?php

declare(strict_types=1);

namespace mztx\wp\plugin\SimpleEt;

use mztx\wp\plugin\SimpleEt\Helper\Attributes;

use function add_action;
use function add_shortcode;
use function get_home_url;
use function get_option;
use function htmlentities;
use function http_build_query;
use function implode;
use function sprintf;
use function wp_add_inline_style;
use function wp_enqueue_style;
use function wp_register_style;

readonly class Shortcode
{
    const EVTERMINE_IFRAME_URL_FORMAT_DETAIL_CALENDAR = 'https://www.evangelische-termine.de/veranstaltungen2';
    const EVTERMINE_IFRAME_URL_FORMAT_DETAIL_DROPDOWN = 'https://www.evangelische-termine.de/veranstaltungen';
    const EVTERMINE_IFRAME_URL_FORMAT_TEASER = 'https://www.evangelische-termine.de/teaser.html';

    public function __construct(
        private string $pluginBaseUrl,
    ) {
        add_shortcode('evtermine', [$this, 'codeEvTermine']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
    }

    public function enqueueScripts(): void
    {
        wp_register_style(
            'mztx-ev-termine-de-embed-style',
            $this->pluginBaseUrl . 'static/evterminembed.css',
            [],
            MZTX_WP_SIMPLE_ET_PLUGIN_VERSION
        );
        wp_enqueue_style('mztx-ev-termine-de-embed-style');

        $customWpCss = get_option('mztxsimpleet_styles_css_wp');
        if ($customWpCss) {
            wp_register_style('mztx-ev-termine-de-embed-style-customized', false);
            wp_enqueue_style('mztx-ev-termine-de-embed-style-customized');

            wp_add_inline_style('mztx-ev-termine-de-embed-style-customized', $customWpCss);
        }
    }

    /**
     * @param array<array-key, string> $atts
     */
    public function codeEvTermine(array $atts, ?string $content, string $shortcodeTag): string
    {
        $defaultFilter = 'kalender';
        $allowedFilters = ['dropdown', 'kalender'];
        $defaultPerPage = 10;

        $attributes = new Attributes($atts);

        $view = $attributes->getFromEnumLowercase('anzeige', ['detail', 'teaser'], 'detail');
        if ($view === 'teaser') {
            $defaultFilter = 'keiner';
            $allowedFilters = ['keiner'];
            $defaultPerPage = 5;
        }

        $filter = $attributes->getFromEnumLowercase('filter', $allowedFilters, $defaultFilter);

        $baseUrl = match (true) {
            ($view === 'detail' && $filter === 'dropdown') => self::EVTERMINE_IFRAME_URL_FORMAT_DETAIL_DROPDOWN,
            ($view === 'detail' && ($filter === 'kalender' || $filter === 'keiner')) => self::EVTERMINE_IFRAME_URL_FORMAT_DETAIL_CALENDAR,
            default => self::EVTERMINE_IFRAME_URL_FORMAT_TEASER,
        };

        $cssUrl = get_home_url() . '?simple-et-file=cssiframe';

        $heightClass = $attributes->getFromEnumLowercase('hoehe', ['50vh', '80vh'], '80vh');

        $tags = $attributes->getStringArray('tags', ',');
        $tagsMode = $attributes->getFromEnumLowercase('tagsmodus', ['alle', 'eins'], 'eins');

        $defaultVid = (int) (get_option('mztxsimpleet_general_default_vid') ?? 0);
        $args = [
            'vid' => $attributes->getInt('vid', $defaultVid),
            'css' => $cssUrl,
            'itemsPerPage' => $attributes->getInt('proSeite', $defaultPerPage),
            'tags' => implode($tagsMode === 'alle' ? '.' : ',', $tags),
            'encoding' => 'utf8',
            'tpl' => '1',
        ];

        $url =
            sprintf(
                '%s?%s',
                $baseUrl,
                http_build_query($args, encoding_type: PHP_QUERY_RFC3986),
            );

        return '
            <iframe class="evtermine-embedded iframe-height--' . $heightClass . '" src="' . htmlentities($url) . '"></iframe>
        ';
    }
}
