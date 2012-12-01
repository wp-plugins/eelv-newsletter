<?php
require_once("../../../../wp-config.php");
$wp->init();
$wp->parse_request();
$wp->query_posts();
$wp->register_globals();
$wp->send_headers();

//echo base64_encode(substr($_REQUEST['vars'],0,-9));
$vars = explode('!',base64_decode(substr($_REQUEST['vars'],0,-9)));
if(is_array($vars) && sizeof($vars)>1){
	if (filter_var($vars[0], FILTER_VALIDATE_EMAIL) && is_numeric($vars[1])) {
		
		$dests = get_post_meta($vars[1], 'sentmails', true);
		if(!empty($dests)){			
			$dests=str_replace($vars[0].':1',$vars[0].':3',$dests);	
			update_post_meta($vars[1], 'sentmails',$dests);
			add_post_meta(
				$vars[1],'eelv_nl_read_'.$vars[0],serialize(
					array(
						'user_agent'=>$_SERVER['HTTP_USER_AGENT'],
						'ip'=>$_SERVER['REMOTE_ADDR'],
						'date'=>date('Y-m-d H:i:s'),
						'referer'=>$_SERVER['HTTP_REFERER']
					)
				)
			);
		}
	}
}
$my_img = imagecreate( 400, 15 );
$background = imagecolorallocate( $my_img, 255, 255, 255 );
$text_colour = imagecolorallocate( $my_img, 150, 150, 150 );
$font = 'Lato-Regular.ttf';
imagettftext($my_img, 8, 0, 1, 10, $text_colour, $font, get_bloginfo('url'));
header( "Content-type: image/png" );
imagepng( $my_img );
imagedestroy( $my_img );
?>