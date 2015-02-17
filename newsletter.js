jQuery(document).ready(function(){
	jQuery('.news_hidden_option').animate({height:'toggle'},1);
	jQuery('.newsform input').focus(function(){
		console.log('focus');
		jQuery(this).parent().find('.news_hidden_option').animate({height:'toggle'},500);
	});
	jQuery('.newsform input').blur(function(){
		jQuery(this).parent().find('.news_hidden_option').animate({height:'toggle'},500);
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
