jQuery(document).ready(function(){
	jQuery('#news_hidden_optionwidget').animate({height:'toggle'},1);
	jQuery('#news_emailwidget').focus(function(){
		jQuery('#news_hidden_optionwidget').animate({height:'toggle'},500);
	});
	jQuery('#news_emailwidget').blur(function(){
		jQuery('#news_hidden_optionwidget').animate({height:'toggle'},500);
	});
	
	jQuery('.newsform input[type=text]').each(function(){
		ph = jQuery(this).attr('placeholder');
		val = jQuery(this).val();
		if(ph!=''){
			if(val==''){
				jQuery(this).val(ph);
			}
		}
		jQuery(this).focus(function(){
			ph = jQuery(this).attr('placeholder');
			val = jQuery(this).val();
			if(val==ph){
				jQuery(this).val('');
			}
		});
		jQuery(this).blur(function(){
			ph = jQuery(this).attr('placeholder');
			val = jQuery(this).val();
			if(val==''){
				jQuery(this).val(ph);
			}
		});
	});
});
