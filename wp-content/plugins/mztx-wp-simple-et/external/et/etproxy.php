<?php declare(strict_types=1);
define('ET_DOMAIN', 'www.evangelische-termine.de');
define('ET_URL', 'https://' . ET_DOMAIN);
$query = $_SERVER['QUERY_STRING'];
switch ($query) {
    case 'ljs':
        getJavascript();
        break;
    case 'cbjs':
        getColorboxJavascript();
        break;
    case 'getCss':
        getCss();
        break;
    case '':
        $query = 'itemsPerPage=10';
        getEvents($query);
        break;
    default:
        if (substr($query, 0, 9) == 'getConfig') {
            getConfig($query);
        } else if (substr($query, 0, 9) == 'getDetail') {
            getDetail($query);
        } else if (substr($query, 0, 13) == 'getHTMLDetail') {
            getHTMLDetail($query);
        } else if (substr($query, 0, 6) == 'getImg') {
            getImg($query);
        } else if (substr($query, 0, 8) == 'dcssfile') {
            getDCss($query);
        } else if (substr($query, 0, 7) == 'djsfile') {
            getDJs($query);
        } else if (substr($query, 0, 4) == 'dimg') {
            getDImg($query);
        } else if (substr($query, 0, 5) == 'getFA') {
            getFA($query);
        } else {
            getEvents($query);
        }
}
function getJavascript()
{
    $url = ET_URL . '/getwidgetjavascript';
    $res = makeCall($url);
    header('Content-Type: application/javascript');
    echo $res['content'];
}

function getColorboxJavascript()
{
    $url = ET_URL . '/getwidgetcolorboxjavascript';
    $res = makeCall($url);
    header('Content-Type: application/javascript');
    echo $res['content'];
}

function getCss()
{
    $url = ET_URL . '/getwidgetcss';
    $res = makeCall($url);
    header('Content-Type: text/css');
    echo $res['content'];
}

function getDCss($query)
{
    parse_str($query, $params);
    $url = ET_URL . $params['dcssfile'];
    $res = makeCall($url);
    header('Content-Type: text/css');
    echo $res['content'];
}

function getDJs($query)
{
    parse_str($query, $params);
    $url = ET_URL . $params['djsfile'];
    $res = makeCall($url);
    header('Content-Type: application/javascript');
    echo $res['content'];
}

function getDImg($query)
{
    parse_str($query, $params);
    $url = ET_URL . $params['dimg'];
    $res = makeCall($url);
    header('Content-Type: image/png');
    echo $res['content'];
}

function getImg($query)
{
    parse_str($query, $params);
    $url = 'https:' . $params['getImg'];
    $res = makeCall($url);
    $content = $res['content'];
    header('Content-Type: image/png');
    echo $content;
}

function getFA($query)
{
    parse_str($query, $params);
    $url = ET_URL . '/bundles/vket/webfonts/' . $params['file'];
    $res = makeCall($url);
    $content = $res['content'];
    $suffix = substr($params['file'], strrpos($params['file'], '.'));
    header('Content-Type: font/' . $suffix);
    echo $content;
}

function getConfig($query)
{
    parse_str($query, $params);
    $url = ET_URL . '/getwidgetconf/' . $params['conf'];
    $params = $filter = [];
    $template = 'list';
    $mapDefaultGeo = [48.147641476893504, 11.539893150329592];
    $mapDefaultZoom = 7;
    $res = makeCall($url);
    $content = $res['content'];
    $info = $res['info'];
    if ($info['http_code'] == '200') {
        $content = json_decode($content);
        $params = $content->params;
        $filter = $content->filter;
        $error = $content->error;
        $template = $content->template;
        $mapDefaultGeo = $content->mapDefaultGeo;
        $mapDefaultZoom = $content->mapDefaultZoom;
    } else {
        $error = $content;
    }
    $cont = ['filter' => $filter, 'params' => $params, 'error' => $error, 'template' => $template, 'mapDefaultGeo' => $mapDefaultGeo, 'mapDefaultZoom' => $mapDefaultZoom];
    header('Content-Type: application/json');
    echo json_encode($cont);
}

function getEvents($query)
{
    $url = ET_URL . '/json?' . $query;
    $res = makeCall($url);
    $content = $res['content'];
    $info = $res['info'];
    if ($info['http_code'] == '200') {
        $cont = ['status' => '', 'events' => json_decode($content),];
    } else {
        $cont = ['status' => $content, 'events' => [],];
    }
    header('Content-Type: application/json');
    echo json_encode($cont);
}

function getDetail($query)
{
    parse_str($query, $params);
    $url = ET_URL . '/json?ID=' . $params['ID'];
    $res = makeCall($url);
    $content = $res['content'];
    $info = $res['info'];
    if ($info['http_code'] == '200') {
        $cont = ['status' => '', 'detail' => json_decode($content),];
    } else {
        $cont = ['status' => $content, 'events' => [],];
    }
    header('Content-Type: application/json');
    echo json_encode($cont);
}

function getHTMLDetail($query)
{
    parse_str($query, $params);
    $url = ET_URL . '/d-' . $params['ID'] . '?popup=3';
    $res = makeCall($url);
    $c = $res['content'];
    if (ET_DOMAIN != $_SERVER['HTTP_HOST']) {
        $c = str_replace('href="/bundles/vket/css/all.min.css"', 'href="/bundles/vket/css/all-etproxy.min.css"', $c);
        $c = str_replace('href="/bundles/vket/', 'href="/et/etproxy.php?dcssfile=/bundles/vket/', $c);
        $c = str_replace('src="/bundles/vket/', 'src="/et/etproxy.php?djsfile=/bundles/vket/', $c);
        $c = str_replace('src="//' . ET_DOMAIN . '/Upload/', 'src="/et/etproxy.php?dimg=/Upload/', $c);
        $c = str_replace('src="/Upload/', 'src="/et/etproxy.php?dimg=/Upload/', $c);
        $c = str_replace('src="//' . ET_DOMAIN . '/Upload2/', 'src="/et/etproxy.php?dimg=/Upload2/', $c);
        $c = str_replace('src="//' . ET_DOMAIN . '/UserImage/', 'src="/et/etproxy.php?dimg=/UserImage/', $c);
        $c = str_replace('src="//' . ET_DOMAIN . '/bundles/vket/images/', 'src="/et/etproxy.php?dimg=/bundles/vket/images/', $c);
    }
    header('Content-Type: text/html');
    echo $c;
}

function makeCall($url)
{
    if (function_exists('curl_init')) {
        $h = curl_init($url);
        curl_setopt($h, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($h, CURLOPT_USERAGENT, 'ET-Widget-Script 2.0');
        curl_setopt($h, CURLOPT_REFERER, $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
        curl_setopt($h, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($h, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($h, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($h, CURLOPT_SSL_VERIFYPEER, 0);
        $content = curl_exec($h);
        $i = curl_getinfo($h);
        if ($i['http_code'] != '200') {
            $content = 'Die Datei konnte nicht abgerufen werden.';
        }
        return ['content' => $content, 'info' => $i];
    }
    return ['content' => 'Das curl-Modul ist nicht vorhanden.', 'info' => null];
}
