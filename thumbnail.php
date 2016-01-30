<?php
/**
 * Plugin Name: Dynamic Thumbnail
 * Plugin URI: http://www.wp-admin.org.ua
 * Description: dynamic generate thumbnail of image
 * Author: Sergey Cherepanov
 * Version: 0.4
 * Author URI: http://www.wp-admin.org.ua
 */

/**
 * @param string $args
 * @return bool|string
 */
function get_thumbnail($args = '')
{
    $defaults = array(
        'src' => false,
        'width' => '',
        'height' => '',
        'background' => 'FFFFFF',
        'method' => 'crop',
        'type' => 'jpeg',
        'quality' => 75
    );
    $args = wp_parse_args($args, $defaults);
    $args['thumbnail'] = 1;

    if ($args['src']) {
        if (file_exists(constant('ABSPATH') . '/' . $args['src'])) {
            return add_query_arg($args, get_bloginfo('home'));
        } else {
            $src = str_replace(constant('ABSPATH'), '', $args['src']);
            if (file_exists(constant('ABSPATH') . '/' . $src)) {
                $args['src'] = $src;
                return add_query_arg($args, get_bloginfo('home'));
            }
        }
    }
    return false;
}

/**
 * @param array $args
 */
function the_thumbnail($args)
{
    echo get_thumbnail($args);
}

function thumbnail_js()
{
    ?>
    <script type="text/javascript">
        function get_thumbnail($args) {
            if (typeof($args) != 'array' && typeof($args) != 'object') {
                return false;
            }
            var $path = '<?php bloginfo('home')?>?thumbnail=1';
            for ($key in $args) {
                $path += '&' + $key + '=' + $args[$key] || '';
            }
            return $path;
        }
    </script>
    <?php
}

add_action('plugins_loaded', create_function('', 'if(isset($_REQUEST["thumbnail"])){include(dirname(__FILE__)."/image.php");exit();};'));