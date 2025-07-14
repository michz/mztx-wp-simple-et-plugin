<?php

declare(strict_types=1);

namespace mztx\wp\plugin\SimpleEt;

use function add_action;
use function add_rewrite_rule;
use function get_option;
use function get_query_var;
use function header;
use function http_response_code;
use function is_string;
use function readfile;
use function strlen;

readonly class Router
{
    public const EXTERNAL_QUERY_PARAM = 'mztx-simple-et-plugin-external';

    public function __construct(
        private string $pluginBasePath,
    ) {
        add_action('template_redirect', [$this, 'template_redirect'], 5);
        add_action('parse_request', [$this, 'parse_request'], 30);

        add_action('init', function () {
            add_rewrite_rule('et/etproxy.php$', 'index.php?' . self::EXTERNAL_QUERY_PARAM . '=etproxy.php', 'top');
            add_rewrite_rule('et/images/controls.png$', 'index.php?' . self::EXTERNAL_QUERY_PARAM . '=images/controls.png', 'top');
        });

        add_filter('query_vars', function($query_vars) {
            $query_vars[] = 'mztx-simple-et-plugin-external';
            return $query_vars;
        });

    }

    public function parse_request(\WP $environment): void
    {
        if (isset($_GET['simple-et-file'])) {
            if ($_GET['simple-et-file'] === 'cssiframe') {
                $setting = get_option('mztxsimpleet_styles_css_iframe');
                if (is_string($setting) && !empty($setting)) {
                    header('Content-type: text/css');
                    header('Content-Length: ' . strlen($setting));
                    echo $setting;
                    exit;
                }

                http_response_code(404);
                exit;
            }
        }
    }

    public function template_redirect(): void
    {
        $queryVar = get_query_var(self::EXTERNAL_QUERY_PARAM);
        if ($queryVar === 'etproxy.php') {
            include $this->pluginBasePath . '/external/et/etproxy.php';
            exit;
        } elseif ($queryVar === 'images/controls.png') {
            $imgPath = $this->pluginBasePath . '/external/et/images/controls.png';
            header('Content-Type: image/png');
            header('Content-Length: ' . filesize($imgPath));
            readfile($imgPath);
            exit;
        }
    }
}
