jQuery(document).ready(function($){
	
// Default content preview
	$('#newsletter_item_post_preview').children('div').clone().appendTo('#newsletter_item_post_preview').clone().appendTo('#newsletter_item_post_preview').clone().appendTo('#newsletter_item_post_preview');
	$('#newsletter_item_post_style input').change(function(){
		$('#newsletter_item_post_preview '+$(this).data('type')).attr('style',$(this).val());
	});
	$('#newsletter_item_post_style input').trigger('change');

// Check tracking
    jQuery('#eelv_nl_sentlist').children('li').each(function(index, element) {
		if(jQuery(this).data('email')!=''){
			
  			jQuery(this).html(jQuery(this).data('email')).click(function(){
				post_id=jQuery('#post_ID').val();
				jQuery.ajax({
					type: 'POST',
					url:eelv_newsletter.url+'/eelv-newsletter/reading/check.php?i='+post_id+'&m='+jQuery(this).data('email'),
					dataType: 'json',
					async: false,
					success: function (k) {
						var txt='';
						for(var i=0 ; i<k.length ; i++){
						  txt+=eelv_newsletter.read_on+k[i]['date']+'\n';											  
						  txt+=eelv_newsletter.on+k[i]['user_agent']+'\n';
						  txt+=eelv_newsletter.from_ip+k[i]['ip']+'\n';
						  txt+='\n\n'; 
						}
						if(txt==''){
							txt=eelv_newsletter.unread;	
						}
						alert(txt);
					}
				});	
			});
			
		}
	});
	
// Convert links
	jQuery('#eelv_nl_convert_id').width(jQuery('#eelv_nl_convert_id').parent().width()-10);
	jQuery('#eelv_nl_convert_a').click(function(){
		var lien=jQuery('#eelv_nl_convert_link').attr('value');
		lien+='&convert=';
		lien+=jQuery('#eelv_nl_convert_id').val();
		if(jQuery('#eelv_nl_convert_title').is(':checked')==true){
			lien+='&add_title=1';
		}
		if(jQuery('#eelv_nl_convert_share').is(':checked')==true){
			lien+='&add_sharelinks=1';
		}
		document.location=lien;
		return false;
	});

});

//Address book
function changegrp(form,url,grpname){
  is_confirmed = confirm(eelv_newsletter.move_contacts_to+grpname+" ?");
  if (is_confirmed) {
    form.action=url;
    form.submit();
  }  
}
function confsup(url,action){
  is_confirmed = confirm(eelv_newsletter.remove_contacts);
  if (is_confirmed) {
    if(action==1){
      document.location=url;
    }
    if(action==2){
      url.submit();
    }
  }
}
function tout(ou,ki){
  chs = ou.getElementsByTagName('input');
  chi = ki.checked;
  for(i=0 ; i<chs.length ; i++){
    if(chs[i].type=='checkbox'){
      chs[i].checked=chi;
    }
  }
}
    
// Editor    
function incontent(str){
  if(IEbof){
    switchEditors.go('content', 'html');
    document.post.content.value+=str;
    switchEditors.go('content', 'tinymce');
  }
  else{
    document.post.content.value+=str;
    if (document.all) {
      value = str;
      document.getElementById('content_ifr').name='content_ifr';
      var ec_sel = document.getElementById('content_ifr').document.selection;
      if(tinyMCE.activeEditor.selection){
        tinyMCE.activeEditor.selection.setContent(str);
      }
      else if(tinyMCE.activeEditor){
        tinyMCE.activeEditor.execCommand("mceInsertRawHTML", false, str);
      }
      else if (ec_sel) {
        var ec_rng = ec_sel.createRange();
        ec_rng.pasteHTML(value);
      }
      else{
      }
    }
    else{
      document.getElementById('content_ifr').name='content_ifr';
      if(document.content_ifr){
        document.content_ifr.document.execCommand('insertHTML', false, str);
      }
      else if(document.getElementById('content_ifr').contentDocument){
        document.getElementById('content_ifr').contentDocument.execCommand('insertHTML', false, str);
      }
      else if(tinyMCE.activeEditor.selection){
        tinyMCE.activeEditor.selection.setContent(str);
      }
      else{
        tinyMCE.activeEditor.execCommand("mceInsertRawHTML", false, str);
      }
    }  
  }
}
function set_default_content(ki){
  ki = document.getElementById(ki);
  if(ki.value && ki.value!=''){
    if(confirm(eelv_newsletter.load_default_content)){
      str=ki.value;
      switchEditors.go('content', 'html');
      document.post.content.value=str;
      switchEditors.go('content', 'tinymce');  
    }
  }
  jQuery('#post').submit();
}
