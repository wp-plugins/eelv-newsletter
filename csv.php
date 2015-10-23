<?php



if(!current_user_can('publish_posts')){
    wp_die(__('Whoop ! you are not authorized to export datas.','eelv_lettreinfo'));
}

if(isset($_GET['grp_id']) && is_numeric($_GET['grp_id'])){ // groupes
    $grp_id=$_GET['grp_id'];
    $MBRS = $this->news_liste_contacts($grp_id); 
    $outputxt='ID;'.__('Name','eelv_lettreinfo').';'.__('Email','eelv_lettreinfo').";\n";
    foreach($MBRS as $contact){        
        $outputxt.=$contact->id.';'
                . $contact->nom.';'
                . $contact->email.";\n";           
    }
}
else{
    wp_die(__('Hum hum... Sorry, but you are using a bad link.','eelv_lettreinfo'));
}
if(defined('WP_DEBUG') && WP_DEBUG==true){
     header("Content-Type: text/plain");
}
else{
    $news_info = $this->get_news_meta($grp_id);
    $grp_nom = sanitize_key(html_entity_decode($news_info->nom));
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public");
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: inline; filename=".$grp_nom.'_'.date('Y-m-d-His').".csv;");
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: " . strlen($outputxt));
}

echo str_replace('&#8211;', '-', $outputxt);
