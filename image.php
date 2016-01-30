<?php
header("Expires: " . date('D, j F Y H:i:s', (gmmktime() + (3 * 60 * 60))) . " GMT");
header("Cache-control: public, max-age=1800");
header("Pragma: cache");

require_once(dirname(__FILE__) . '/image.lib.php');

$args = array(
    'src' => trim($_REQUEST['src']),
    'width' => (int)($_REQUEST['width']),
    'height' => (int)($_REQUEST['height']),
    'background' => trim($_REQUEST['background']),
    'method' => trim($_REQUEST['method']),
    'type' => (($type = trim($_REQUEST['type'])) ? $type : 'jpg'),
    'quality' => (int)$_REQUEST['quality']
);

$cache_dir = constant('ABSPATH') . '/wp-content/cache';
if (!file_exists($cache_dir)) {
    mkdir($cache_dir);
    chmod($cache_dir, 0777);
}

$cache_dir .= '/images';
if (!file_exists($cache_dir)) {
    mkdir($cache_dir);
    chmod($cache_dir, 0777);
}

$filename = $cache_dir . '/' . md5(implode('', $args)) . '.' . $args['type'];

if (file_exists($filename) && (filectime($filename) > (time() - 600))) {
    switch ($args['type']):
        case('jpeg'):
        case('jpg'):
        default:
            header("content-type: image/jpeg");
            break;
        case('png'):
            header("content-type: image/png");
            break;
        case('gif'):
            header("content-type: image/gif");
            break;
    endswitch;
    echo file_get_contents($filename);
} else {
    $image = new Image(constant('ABSPATH') . '/' . $args['src']);
    $image->resize($args['width'], $args['height'], $args['background'], $args['method'])->save($filename, $args['type'], $args['quality'])->display($args['type'], $args['quality']);
    chmod($filename, 0777);
}
