<?php


// BASE NEWSLETTER
$this->eelv_nl_default_themes['base newsletter']="
<div style=\"background-color: #DDDDDD; width:100%; font-family: Arial, Helvetica, sans-serif; text-align:center;\"><table width='700' border='0' cellspacing='0' cellpadding='0' bgcolor='white'><tbody><tr><td><a href='".get_bloginfo('url')."'><img src='".get_header_image()."'  width='700' border='none'/></a></td></tr><tr><td bgcolor='EEEEEE'><h1><a href='".get_bloginfo('url')."'>".get_bloginfo('name')."</a></h1></td></tr><tr><td align='left'><div style='padding: 15px;'>[newsletter]</div></td></tr><tr><td bgcolor='EEEEEE' color='333333'><div style='font-size: 0.7em; padding: 10px; line-height: 0.9em;'>".sprintf(__("If you no longer wish to receive messages from %s, please exercise your right to unsubscribe by clicking here: [desinsc_url]. Accordance with the law, you have a right of access, rectification and deletion of  your datas.",'eelv_lettreinfo'),get_bloginfo('name'))."</div></td></tr></tbody></table></div>";

if((isset($_GET['post']) && isset($_GET['action']) && $_GET['action']=='edit') || 
(isset($_GET['post_type']) && $_GET['post_type']=='newsletter')){
     $derniers_articles='';
      $qu=new WP_Query(array('status'=>'publish','posts_per_page'=>'3'));
      if($qu->have_posts()){
        while($qu->have_posts()){
         $qu->the_post(); 
          $derniers_articles.="<div style='width:650px; margin:5px 0px;text-align:left;  clear:both; border-top:#CCC 1px dotted; padding-top:1em; margin-top:1em;'>
                             <a href='".get_post_permalink()."' style='text-decoration:none;color:#666666;'>".get_the_post_thumbnail(get_the_ID(),array(550,100),array('style'=>'float:left; margin-right:10px;'))."</a> <h3 style='margin:0px !important; font-variant: small-caps; font-size: 20px; color:#748D75'><a href='".get_post_permalink()."' style='text-decoration:none;color:#000000;'><font color='000000'>".get_the_title()."</font></a></h3>
                             <a href='".get_post_permalink()."' style='text-decoration:none;color:#666666;'>".substr(strip_tags(apply_filters('the_content_rss',get_the_content())),0,300)."...</a>
        </div>&nbsp;
";
        }
      }
      
$this->eelv_nl_content_themes['base newsletter']=get_bloginfo('description')."
   
  ".$derniers_articles."
      
        <div style='clear:both;'>&nbsp;</div>";
}


// NEWSLETTER 2 COLS
$this->eelv_nl_default_themes['newsletter 2 cols']="
<div style=\"background-color: #DDDDDD; width:100%; font-family: Arial, Helvetica, sans-serif; text-align:center;\"><table width='650' border='0' cellspacing='0' cellpadding='0' bgcolor='white'><tbody><tr><td><a href='".get_bloginfo('url')."'><img src='".get_header_image()."'  width='700' border='none'/></a></td></tr><tr><td bgcolor='EEEEEE'><h1><a href='".get_bloginfo('url')."'>".get_bloginfo('name')."</a></h1></td></tr><tr><td align='center'><div style='padding: 15px;'>[newsletter]</div></td></tr><tr><td bgcolor='EEEEEE' color='333333'><div style='font-size: 0.7em; padding: 10px; line-height: 0.9em;'>".sprintf(__("If you no longer wish to receive messages from %s, please exercise your right to unsubscribe by clicking here: [desinsc_url]. Accordance with the law, you have a right of access, rectification and deletion of  your datas.",'eelv_lettreinfo'),get_bloginfo('name'))."</div></td></tr></tbody></table></div>";

$this->eelv_nl_content_themes['newsletter 2 cols']="<table cellspacing='0' cellpadding='10' width='630'><tbody><tr><td colspan='2'>".__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec a diam lectus. Sed sit amet ipsum mauris. Maecenas congue ligula ac quam viverra nec consectetur ante hendrerit. Donec et mollis dolor. Praesent et diam eget libero egestas mattis sit amet vitae augue. Nam tincidunt congue enim, ut porta lorem lacinia consectetur. Donec ut libero sed arcu vehicula ultricies a non tortor. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean ut gravida lorem. Ut turpis felis, pulvinar a semper sed, adipiscing id dolor.','eelv_lettreinfo')."</td></tr><tr><td valign='top'>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec a diam lectus. Sed sit amet ipsum mauris. Maecenas congue ligula ac quam viverra nec consectetur ante hendrerit. Donec et mollis dolor. Praesent et diam eget libero egestas mattis sit amet vitae augue. Nam tincidunt congue enim, ut porta lorem lacinia consectetur. Donec ut libero sed arcu vehicula ultricies a non tortor. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean ut gravida lorem. Ut turpis felis, pulvinar a semper sed, adipiscing id dolor.</td><td valign='top'>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec a diam lectus. Sed sit amet ipsum mauris. Maecenas congue ligula ac quam viverra nec consectetur ante hendrerit. Donec et mollis dolor. Praesent et diam eget libero egestas mattis sit amet vitae augue. Nam tincidunt congue enim, ut porta lorem lacinia consectetur. Donec ut libero sed arcu vehicula ultricies a non tortor. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean ut gravida lorem. Ut turpis felis, pulvinar a semper sed, adipiscing id dolor.</td></tr></tbody></table>";