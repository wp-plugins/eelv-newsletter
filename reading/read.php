<?php

require_once("wp.php");

$vars = explode('!', base64_decode(substr($_REQUEST['vars'], 0, -9)));
if (is_array($vars) && sizeof($vars) > 1) {
    if (filter_var($vars[0], FILTER_VALIDATE_EMAIL) && is_numeric($vars[1])) {

        $dests = get_post_meta($vars[1], 'sentmails', true);
        if (!empty($dests)) {
            $dests = str_replace($vars[0] . ':1', $vars[0] . ':3', $dests);
            update_post_meta($vars[1], 'sentmails', $dests);
            $sentmeta = array(
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'ip' => $_SERVER['REMOTE_ADDR'],
                'date' => date('Y-m-d H:i:s')
            );
            if (isset($_SERVER['HTTP_REFERER']))
                $sentmeta['referer'] = $_SERVER['HTTP_REFERER'];
            add_post_meta($vars[1], 'eelv_nl_read_' . $vars[0], serialize($sentmeta));
        }
    }
}
$my_img = imagecreate(400, 15);
$background = imagecolorallocate($my_img, 255, 255, 255);
$text_colour = imagecolorallocate($my_img, 150, 150, 150);
$font = 'Lato-Regular.ttf';
$spy_text = get_option('newsletter_spy_text');
if ($spy_text == '') {
    $spy_text = str_replace(array('http://', 'https://'), '', get_bloginfo('url'));
}
imagettftext($my_img, 8, 0, 1, 10, $text_colour, plugin_dir_path(__FILE__) . $font, $spy_text);
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT', true, 200);
header('Expires: ' . gmdate('D, d M Y H:i:s', strtotime('+1 day')) . ' GMT', true, 200);
header("Content-type: image/png");
imagepng($my_img);
imagedestroy($my_img);
