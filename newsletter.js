jQuery(document).ready(function(){
	jQuery('#news_hidden_optionwidget').hide();
	jQuery('#news_emailwidget').focus(function(){
		jQuery('#news_hidden_optionwidget').animate({height:'100px'},500);
	});
	jQuery('#news_emailwidget').blur(function(){
		jQuery('#news_hidden_optionwidget').animate({height:'0px'},500);
	});
});
