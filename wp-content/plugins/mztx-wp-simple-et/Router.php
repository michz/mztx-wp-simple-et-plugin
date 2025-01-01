<?php

declare(strict_types=1);

namespace mztx\wp\plugin\SimpleEt;

use function add_action;
use function get_option;
use function header;
use function http_response_code;
use function is_string;
use function strlen;

class Router
{
    public function __construct()
    {
        add_action('parse_request', [$this, 'parse_request'], 30);
    }

    public function parse_request(\WP $environment): ?\WP
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

        return $environment;
    }
}
