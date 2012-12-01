<?php
require_once("../../../../wp-config.php");
$wp->init();
$wp->parse_request();
$wp->query_posts();
$wp->register_globals();
$wp->send_headers();

if(is_user_logged_in() && isset($_REQUEST['m']) && isset($_REQUEST['i'])){	
	$reads = get_post_meta($_REQUEST['i'], 'eelv_nl_read_'.$_REQUEST['m']);
	foreach($reads as $k=>$read){
		$read = unserialize($read);
		
		$dat=strtotime($read['date']);
		$read['date']=date_i18n(get_option('date_format') ,$dat).', '.date('H:i',$dat);
		
		$attr='';
		
		if ( strpos( $read['user_agent'], 'Windows' ) !== FALSE ) { $attr.='windows '; }
		elseif ( strpos( $read['user_agent'], 'Android' ) !== FALSE ) { $attr.='android '; }
		elseif ( strpos( $read['user_agent'], 'Linux' ) !== FALSE ) { $attr.='linux '; }
		elseif ( strpos( $read['user_agent'], 'Apple' ) !== FALSE ) { $attr.='apple '; }	
		elseif ( strpos( $read['user_agent'], 'BlackBerry' ) !== FALSE ) { $attr.='blackberry '; }		
		
		if ( strpos( $read['user_agent'], 'Thunderbird' ) !== FALSE ) { $attr.='Thunderbird'; }
		elseif ( strpos( $read['user_agent'], 'Mail' ) !== FALSE ) { $attr.='Mail'; }
		elseif ( strpos( $read['user_agent'], 'Outlook' ) !== FALSE ) { $attr.='Outlook'; }
		elseif ( strpos( $read['user_agent'], 'Firefox' ) !== FALSE ) { $attr.='Firefox'; }
		elseif ( strpos( $read['user_agent'], 'Opera' ) !== FALSE ) { $attr.='Opera'; }
		elseif ( strpos( $read['user_agent'], 'Chrome' ) !== FALSE ) { $attr.='Chrome'; }
		elseif ( strpos( $read['user_agent'], 'Safari' ) !== FALSE ) { $attr.='Safari'; }
		elseif ( strpos($read['user_agent'], 'MSIE 6' ) !== FALSE ) { $attr.='ie6'; }
		elseif ( strpos($read['user_agent'], 'MSIE 7' ) !== FALSE ) { $attr.='ie7'; }
		elseif ( strpos($read['user_agent'], 'MSIE 8' ) !== FALSE ) { $attr.='ie8'; }
		elseif ( strpos($read['user_agent'], 'MSIE 9' ) !== FALSE ) { $attr.='ie9'; }
		elseif ( strpos($read['user_agent'], 'MSIE 10' ) !== FALSE ) { $attr.='ie10'; }
		elseif ( strpos($read['user_agent'], 'MSIE' ) !== FALSE ) { $attr.='ie'; }
		else { echo "robot"; }
		
		if ( strpos($read['user_agent'], 'tablet' ) !== FALSE || strpos($read['user_agent'], 'Tablet' ) !== FALSE  ) { $attr.=' Tablet'; }
		elseif ( strpos($read['user_agent'], 'mobile' ) !== FALSE || strpos($read['user_agent'], 'Mobile' ) !== FALSE ) { $attr.=' Mobile '; }
		else{ $attr.=' Desktop'; }
		$read['user_agent']=$attr;
		
		$reads[$k]=$read;
	}
	echo json_encode($reads);	
}
?>