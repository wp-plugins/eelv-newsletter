<?php


add_shortcode('nl_reply_link','EelvNlReply_link');
add_shortcode('li_lien_reponse','EelvNlReply_link');

add_shortcode('nl_reply_innerlink','EelvNlReply_innerlink');

add_shortcode('nl_reply_form','EelvNlReply_form');
add_shortcode('li_form_reponse','EelvNlReply_form');
/*
add_shortcode('nl_reply_link',array( 'EelvNlReply', 'link'));
add_shortcode('li_lien_reponse',array( 'EelvNlReply', 'link'));

add_shortcode('nl_reply_innerlink',array( 'EelvNlReply', 'innerlink'));

add_shortcode('nl_reply_form',array( 'EelvNlReply', 'form'));
add_shortcode('li_form_reponse',array( 'EelvNlReply', 'form'));*/



	function EelvNlReply_getanswer($postid,$email){
		global $wpdb;
		$querystr = "SELECT * FROM `$wpdb->postmeta` WHERE `post_id` = '$postid' AND `meta_key`='eelv_nl_reply' AND `meta_value`LIKE'%:\"$email\";%'";
        $meta = $wpdb->get_results($querystr); 
		if($meta){
			$answer = $meta[0];
			if(sizeof($meta>1)){
				$querystr = "DELETE FROM `$wpdb->postmeta` WHERE `post_id` = '$postid' AND `meta_key`='eelv_nl_reply' AND `meta_value`LIKE'%:\"$email\";%' AND `meta_id`!='".$answer->meta_id."'";
        		$clean = $wpdb->get_col($querystr);				
			}
			return $answer;
		} 
		return false;
	}
	function EelvNlReply_form($atts){
		if(isset($_REQUEST['nl']) && is_numeric($_REQUEST['nl'])){		
			$ret='<span class="nl_message nl_alert">'.__('Invalid answer link','eelv_lettreinfo').'</span>';
			define('NL_IN_REPLY_PAGE',true);
			$nl =  get_post($_REQUEST['nl']); 
			if($nl && is_object($nl)){
				
						
				$ret='<form>
				<input type="hidden" name="nl" value="'.$_REQUEST['nl'].'"/>';
				$ret.='<h2>'.$nl->post_title.'</h2>';
				
				$pb=0;
				if(!isset($_REQUEST['m']) || !filter_var($_REQUEST['m'], FILTER_VALIDATE_EMAIL)){
					$pb=1;
				}
				if(!isset($_REQUEST['r']) || !is_string($_REQUEST['r']) || empty($_REQUEST['r'])){
					$pb=1;
				}
				if($pb==0){
					define('NL_IN_REPLIED_PAGE',true);
					
					$r = htmlentities(strip_tags(stripslashes($_REQUEST['r'])));
					//register answer
					$value = serialize(array(
						'date'=>time(),
						'email'=>$_REQUEST['m'],
						'val'=>$r
					));
					$ret.='<div class="nl_confirm">';
					$print_answer='<strong>'.$r.'</strong>';
					if(false !== $answer = EelvNlReply_getanswer($_REQUEST['nl'],$_REQUEST['m'])){
						update_post_meta($_REQUEST['nl'], 'eelv_nl_reply', $value, $answer->meta_value);
						$ret.=sprintf(__('Thank you, your answer "%s" has been updated !','eelv_lettreinfo'),$print_answer);
					}
					else{
						add_post_meta(	$_REQUEST['nl'],'eelv_nl_reply',$value);
						$ret.=sprintf(__('Thank you, your answer "%s" has been registered !','eelv_lettreinfo'),$print_answer);
					}
					$ret.='</div>';
				}
				
				// ask for missing informations	
				$content=nl_content($_REQUEST['nl'],$type='newsletter_archive');		
				$ret.=$content;	
				$ret.='</form>';
			
			}
			
			
			
		}
		else{
			$ret='<div class="nl_alert">'.__('Please access this page only by following a reply link','eelv_lettreinfo').'</div>';
		}
		return $ret;
	}
	function EelvNlReply_innerlink($atts){	
		extract(shortcode_atts(array(
		      'val'=>__('Visible link','eelv_lettreinfo'),
		      'rep'=>__('reply_code','eelv_lettreinfo'),
		      'nl'=>0
	     ), $atts));
		 $ret='';
		 global $dest;
  		$reply_url = get_option( 'newsletter_reply_url','');
		 if($nl>0){
		 	$reply_url.=strpos($reply_url,'?')>-1 ? '&' : '?';
		 	$ret.=' <a href="'.$reply_url.'nl='.$nl.'&r='.$rep.'&m='.$dest.'">'.$val.'</a> ';
		 }
		 elseif(is_admin()){
		 	$ret.='<a href="#TB_inline?width=400&height=150&inlineId=eelv_news_prevlink" class="thickbox">'.__('Preview link:','eelv_lettreinfo').' '.$val.'</a>';
		 }
		else{
		 	$ret.='<span class="nl_message nl_alert">'.__('Invalid answer link','eelv_lettreinfo').'</span>';
		 }
		
		return $ret;
	}
	function EelvNlReply_link($atts){	
		$ret='';	
  		$reply_url = get_option( 'newsletter_reply_url','');
		if(!empty($reply_url)){			
			if(!defined('NL_IN_REPLIED_PAGE')){
				extract(shortcode_atts(array(
				      'val'=>__('Visible link','eelv_lettreinfo'),
				      'rep'=>__('reply_code','eelv_lettreinfo')
			     ), $atts));
				 if(defined('NL_IN_REPLY_PAGE') && NL_IN_REPLY_PAGE==true){
				 	if(!defined('NL_HAS_REPLY_PAGE')){
				 		define('NL_HAS_REPLY_PAGE',true);
				 		$ret.='<label>'.__('Please fill your email','eelv_lettreinfo').' 
				 			<input type="text" name="m" value="'.(isset($_REQUEST['m'])?$_REQUEST['m']:'').'"/>		 		
				 		</label>		 	
				 		<label><input type="submit" class="button" value="'.__('ok','eelv_lettreinfo').'"/></label>	
				 		';
				 	}
				 	$ret.='<label><input type="radio" name="r" value="'.$rep.'"/> '.$val.'</label>';
				 }
				 else{
				 	global $nl_id;
				 	$ret.='[nl_reply_innerlink nl='.$nl_id.' rep="'.$rep.'" val="'.$val.'"]';
				 }
			}
			elseif(!defined('NL_HAS_REPLY_PAGE')){
			 	define('NL_HAS_REPLY_PAGE',true);
			 	$ret.='<span class="nl_message nl_confirm">'.__('Thank you for your answer','eelv_lettreinfo').'</span>';
			}
		}	
		else{
		 	$ret.='<span class="nl_message nl_alert">'.__('Invalid answer link','eelv_lettreinfo').'</span>';
		 }	
		return $ret;
	}
		


