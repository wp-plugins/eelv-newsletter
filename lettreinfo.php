<?php
/*
Plugin Name: EELV Newsletter
Plugin URI: http://ecolosites.eelv.fr
Description:  Add a registration form on frontOffice, a newsletter manager on BackOffice
Version: 3.1.5
Author: bastho, ecolosites // EELV
Author URI: http://ecolosites.eelv.fr
License: CC BY-NC v3.0
*/
  // ID for DB version
  $eelv_newsletter_version = '2.6.5';
  $newsletter_tb_name = 'eelv_'.$wpdb->blogid. '_newsletter_adr';
  $wproles=array('Super Admin','Administrator','Editor','Author','Contributor','Subscriber');
  global $wpdb, $eelv_nl_default_themes, $eelv_nl_content_themes, $lettreinfo_plugin_path, $newsletter_plugin_url;
  $newsletter_plugin_url = plugins_url();
  $lettreinfo_plugin_path=WP_PLUGIN_DIR.'/'.str_replace( basename( __FILE__), "", plugin_basename(__FILE__) );
  $eelv_nl_content_themes=array();
  $eelv_nl_default_themes=array();
  $newsletter_sql = "CREATE TABLE " . $newsletter_tb_name . " (
    `id` mediumint(9) NOT NULL AUTO_INCREMENT,
    `parent` mediumint(9) DEFAULT 0 NOT NULL,
    `nom` VARCHAR(255) DEFAULT '' NOT NULL,
    `email` VARCHAR(255) DEFAULT '' NOT NULL,
    PRIMARY KEY  (`id`)
    );";
  
  
  /* INSTALLATION DES TABLES  */
  function eelvnewsletter_install() {
    global $eelv_newsletter_version,$newsletter_tb_name,$newsletter_sql;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($newsletter_sql);
    add_option('eelv_newsletter_version', $eelv_newsletter_version);
  }
  // UPDATE PLUGIN
  $installed_ver = get_option( "eelv_newsletter_version" );
  if( $installed_ver != $eelv_newsletter_version ) {
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($newsletter_sql);
    update_option( 'eelv_newsletter_version', $eelv_newsletter_version );
  }
  // WP 3.1 patch upgrade
  function eelvnewsletter_update_db_check() {
    global $eelv_newsletter_version;
    if (get_option('eelv_newsletter_version') != $eelv_newsletter_version) {
      update_option( 'eelv_newsletter_version', $eelv_newsletter_version );
      eelvnewsletter_install();
    }
  }
  if(false===$wpdb->query('SELECT `id` FROM '.$newsletter_tb_name.' LIMIT 0,1') ){
    eelvnewsletter_install();
  }
  // FONCTIONS
  add_action( 'admin_enqueue_scripts', 'eelv_nl_my_admin_enqueue_scripts' );
  function eelv_nl_my_admin_enqueue_scripts() {
    if ( 'newsletter_archive' == get_post_type() )
      wp_dequeue_script( 'autosave' );
  }
  function newsletter_BO(){
    global $eelv_nl_content_themes,$eelv_nl_default_themes, $newsletter_plugin_url,$lettreinfo_plugin_path;
    
    load_plugin_textdomain( 'eelv_lettreinfo', false, 'eelv_lettreinfo/languages' );
    register_post_type('newsletter', array(  'label' => 'Newsletter','description' => '','public' => true,'show_ui' => true,'show_in_menu' => true,'capability_type' => 'post','hierarchical' => false,'rewrite' => array('slug' => ''),'query_var' => true,'has_archive' => true,'supports' => array('title','editor','author'),'labels' => array (
      'name' => __("Lettre d'info",'eelv_lettreinfo'),
      'singular_name' => __("Lettre d'info",'eelv_lettreinfo'),
      'menu_name' => __("Lettre d'info",'eelv_lettreinfo'),
      'add_new' => __('ajouter','eelv_lettreinfo'),
      'add_new_item' => __('Ajouter','eelv_lettreinfo'),
      'edit' => __('Editer','eelv_lettreinfo'),
      'edit_item' => __('Editer','eelv_lettreinfo'),
      'new_item' => __('Nouvelle','eelv_lettreinfo'),
      'view' => __('Afficher','eelv_lettreinfo'),
      'view_item' => __('Afficher Lettre d\'info','eelv_lettreinfo'),
      'search_items' => __('Chercher','eelv_lettreinfo'),
      'not_found' => __('No newsletter Found','eelv_lettreinfo'),
      'not_found_in_trash' => __('No newsletter Found in Trash','eelv_lettreinfo'),
      'parent' => __('Parent newsletter','eelv_lettreinfo'),
    ),) );
    register_post_type(
      'newsletter_template', array(  'label' => 'Mod&egrave;les','description' => '','public' => true,'show_ui' => true,'show_in_menu' => false,'capability_type' => 'post','hierarchical' => false,'rewrite' => array('slug' => ''),'query_var' => true,'has_archive' => true,'supports' => array('title','editor','revisions'),'show_in_menu' => 'edit.php?post_type=newsletter','labels' => array (
        'name' => __('Habillage','eelv_lettreinfo'),
        'singular_name' => __('Habillage','eelv_lettreinfo'),
        'menu_name' => __('Habillages','eelv_lettreinfo'),
        'add_new_item' => __('Ajouter','eelv_lettreinfo'),
        'edit' => __('Editer','eelv_lettreinfo'),
        'edit_item' => __('Editer','eelv_lettreinfo'),
        'new_item' => __('Nouveau','eelv_lettreinfo'),
        'view' => __('Afficher','eelv_lettreinfo'),
        'view_item' => __('Voir','eelv_lettreinfo'),
        'search_items' => __('Rechercher','eelv_lettreinfo'),
        'not_found' => __('No template Found','eelv_lettreinfo'),
        'not_found_in_trash' => __('No template Found in Trash','eelv_lettreinfo'),
        'parent' => __('Parent template','eelv_lettreinfo'),
      ),) );
    register_post_type('newsletter_archive', array(  'label' => 'Archives','description' => '','public' => true,'show_ui' => true,'show_in_menu' => false,'capability_type' => 'post','hierarchical' => false,'rewrite' => array('slug' => ''),'query_var' => true,'has_archive' => true,'supports' => array('title','editor'),'show_in_menu' => 'edit.php?post_type=newsletter','labels' => array (
      'name' => __('Archives','eelv_lettreinfo'),
      'singular_name' => __('Archive','eelv_lettreinfo'),
      'menu_name' => __('Archives','eelv_lettreinfo'),
      'add_new_item' => __('Ajouter','eelv_lettreinfo'),
      'edit' => __('Editer','eelv_lettreinfo'),
      'edit_item' => __('Editer archive','eelv_lettreinfo'),
      'new_item' => __('Nouvelle archive','eelv_lettreinfo'),
      'view' => __('Afficher','eelv_lettreinfo'),
      'view_item' => __('Voir archive','eelv_lettreinfo'),
      'search_items' => __('Rechercher une archive','eelv_lettreinfo'),
      'not_found' => __('Aucun envoi n\'a encore &eacute;t&eacute; effectu&eacute;','eelv_lettreinfo'),
      'not_found_in_trash' => __('No archive Found in Trash','eelv_lettreinfo'),
      'parent' => __('Parent archive','eelv_lettreinfo'),
    ),) );
    require_once($lettreinfo_plugin_path.'/templates.php');
  }
  // ADD NEW COLUMN  
  function lettreinfo_columns_head($defaults) {  
    $defaults['envoyer'] = __('Envoyer','eelv_lettreinfo');  
    return $defaults;  
  }  
  // COLUMN CONTENT  
  function lettreinfo_columns_content($column_name, $post_ID) {  
    if ($column_name == 'envoyer') {  
      $my_temp=get_post(get_post_meta(get_the_ID(), 'nl_template',true));
      if(get_the_ID()!=0 && get_the_title()!='' && get_the_content()!=''  && $my_temp){
        echo $my_temp->post_title;
        echo '<br/><a href="edit.php?post_type=newsletter&page=news_envoi&post='.get_the_ID().'">'.__('Pr&eacute;visualiser et envoyer','eelv_lettreinfo').'</a>';
      }
      else{
        echo __('Pas encore pr&ecirc;te...','eelv_lettreinfo');
      }  
    }  
  }
  // ADD NEW COLUMN (ARCHIVES) 
  function lettreinfo_archives_columns_head($defaults) {  
    $defaults['queue'] = __('File d\'attente','eelv_lettreinfo'); 
    $defaults['sent'] = __('Envoy&eacute;(s)','eelv_lettreinfo');  
    $defaults['read'] = __('Lu(s)','eelv_lettreinfo');  
    return $defaults;  
  }  
  // COLUMN CONTENT  (ARCHIVES) 
  function lettreinfo_archives_columns_content($column_name, $post_ID) {  
    if ($column_name == 'queue') {  
      $dest = get_post_meta($post_ID, 'destinataires',true);
      echo abs(substr_count($dest,',')); 
    }
    if ($column_name == 'sent') {  
      $sent = get_post_meta($post_ID, 'sentmails',true);
      echo abs(substr_count($sent,',')); 
    }  
    if ($column_name == 'read') {  
      $spy = get_post_meta($post_ID, 'nl_spy',true);
	  if($spy==1){
		  $sent = get_post_meta($post_ID, 'sentmails',true);
		  $lus = abs(substr_count($sent,':3'));
		  $tot = abs(substr_count($sent,','));
		  echo $lus.' ('.round($lus/$tot*100).'%)'; 
	  }
	  else{
		 _e('suivi d&eacute;sactiv&eacute;','eelv_lettreinfo'); 
	  }
    }  
  }
  /* Adds a box to the main column on the Post and Page edit screens */
  function newsletter_add_custom_box() {
    add_meta_box( 
      'news-carnet-adresse',
      __( "Outils d'&eacute;ditions", 'eelv_lettreinfo' ),
      'newsletter_admin',
      'newsletter' 
    );
    add_meta_box( 
      'news-envoi-edit',
      __( "Envoyer", 'eelv_lettreinfo' ),
      'newsletter_admin_prev',
      'newsletter',
      'side' 
    );
    add_meta_box( 
      'news-convert-post',
      __( "Envoyer en lettre d'info", 'eelv_lettreinfo' ),
      'news_transform',
      'post',
      'side' 
    ); 
    add_meta_box( 
      'news-archive_viewer',
      __( "Aper&ccedil;u", 'eelv_lettreinfo' ),
      'newsletter_archive_admin',
      'newsletter_archive' 
    );
    add_meta_box( 
      'news-archive_viewerdest',
      __( "Destinataires", 'eelv_lettreinfo' ),
      'newsletter_archive_admin_dest',
      'newsletter_archive' 
    );
    add_meta_box( 
      'news-archive_viewerqueue',
      __( "File d'attente", 'eelv_lettreinfo' ),
      'newsletter_archive_admin_queue',
      'newsletter_archive' 
    ); 
  }
  // Ajout du menu et sous menu
  function eelv_news_ajout_menu() {
    add_submenu_page('edit.php?post_type=newsletter', __('Carnet d\'adresses', 'eelv_lettreinfo' ), __('Carnet d\'adresses', 'eelv_lettreinfo' ), 'manage_options', 'news_carnet_adresse', 'news_carnet_adresse');
    add_submenu_page('edit.php?post_type=newsletter', __('Envoi', 'eelv_lettreinfo' ), __('Envoi', 'eelv_lettreinfo' ), 'manage_options', 'news_envoi', 'news_envoi');
    add_submenu_page('edit.php?post_type=newsletter', __('Configuration/aide', 'eelv_lettreinfo' ), __('Configuration/aide', 'eelv_lettreinfo' ), 'manage_options', 'newsletter_page_configuration', 'newsletter_page_configuration');
    add_submenu_page('edit.php?post_type=newsletter', __('V&eacute;rifier les parametres', 'eelv_lettreinfo' ), __('V&eacute;rifier les parametres', 'eelv_lettreinfo' ), 'manage_options', 'newsletter_checkdb', 'newsletter_checkdb');
  }
  // Ajout du menu d'option sur le r&eacute;seau
  function eelv_news_ajout_network_menu() {
    add_submenu_page('settings.php', __('Lettre d\'info', 'eelv_lettreinfo' ), __('Lettre d\'info', 'eelv_lettreinfo' ), 'Super Admin', 'newsletter_network_configuration', 'newsletter_network_configuration');
    //add_submenu_page('tpe', 'Historique', 'Historique', 'Super Admin', 'eelv_tpe_liste', 'tpe_supercommandes_liste');    
  }
  function get_news_meta($id){
    global $wpdb,$newsletter_tb_name ;
    $ret =  $wpdb->get_results("SELECT * FROM `$newsletter_tb_name` WHERE `id`='$id'");
    if(is_array($ret) && sizeof($ret)>0){
      return $ret[0];  
    }
    return false;
  }
  function nl_content($post_id,$type='newsletter'){
    //global $wpdb;
    // wp_reset_query();
    // query_posts('post_type='.$type.'&p='.$post_id);
    $desinsc_url = get_option( 'newsletter_desinsc_url' );
    // the_post();
    $nl =  get_post($post_id); 
    //$post_content = get_the_content();
    // wp_reset_query();
    $content=$nl->post_content;
    $template =  get_post(get_post_meta($post_id,'nl_template',true)); 
    if($template){
      $content= str_replace('[newsletter]',$content,$template->post_content);
    }
    $content=str_replace('[desinsc_url]',"<a href='".$desinsc_url."' target='_blank' class='nl_a'>".$desinsc_url."</a>",$content);
    return apply_filters('the_content',$content);
  }
  function eelv_newsletter_sharelinks($title,$link){
						return "<div style='width:550px; margin:0px;text-align:left; clear:both;font-size:9px; '><span style='display:block;float:left;padding:2px;padding-left:10px;padding-right:10px;background:#888;color:#FFF;'>".__('Partager sur :', 'eelv_lettreinfo' )."</span><a href='http://www.facebook.com/sharer.php?u=".urlencode($link)."&t=".$title."' target='_blank' style='display:block;float:left;padding:2px;padding-left:10px;padding-right:10px;background:#3B5998;color:#FFF;'>Facebook</a><a href='https://twitter.com/intent/tweet?text=".$title."%20".urlencode($link)."' target='_blank' style='display:block;float:left;padding:2px;padding-left:10px;padding-right:10px;background:#2BB7EA;color:#FFF;'>Twitter</a><a href='https://plus.google.com/share?url=".urlencode($link)."' target='_blank' style='display:block;float:left;padding:2px;padding-left:10px;padding-right:10px;background:#DB4B39;color:#FFF;'>Google+</a><a href='http://www.linkedin.com/shareArticle?mini=true&url=".urlencode($link)."&title=".$title."' target='_blank' style='display:block;float:left;padding:2px;padding-left:10px;padding-right:10px;background:#0073B2;color:#FFF;'>Linked in</a></div>&nbsp;\n";	
 }
  /////////////////////////////////////////////FEUILLE DE STYLE + VALIDATION FORMULAIRE
  function style_newsletter(){
    global $wpdb,$newsletter_tb_name,$newsletter_plugin_url,$news_reg_return;
    ?>
<link rel="stylesheet" type="text/css" media="all" href="<?=plugins_url( 'newsletter.css' , __FILE__ )?>" />
<?php
    $query='';
    if(isset($_POST['news_email'])){
      $email = stripslashes($_POST['news_email']);
      if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        switch($_POST['news_action']){
          case '1':
            $ret =  $wpdb->get_results("SELECT * FROM `$newsletter_tb_name` WHERE `email`='".str_replace("'","''",$email)."'");
            if(is_array($ret) && sizeof($ret)>0){
              $ret = $ret[0];
              if($ret->parent==2){
                $query="UPDATE $newsletter_tb_name SET `parent`='1' WHERE `email`='".str_replace("'","''",$email)."'";
                if($query!='' && false===$wpdb->query($query)){
                  $news_reg_return.=__("Erreur d'enregistrement !", 'eelv_lettreinfo') ;
                }
                elseif($query!=''){
                  $news_reg_return=__("Vous avez bien &eacute;t&eacute; r&eacute;-inscrit(e).", 'eelv_lettreinfo') ;                
                }                
              }
              else{
                $news_reg_return.=__("Votre email est d&eacute;jà enregistr&eacute; dans notre liste d'envoi.", 'eelv_lettreinfo') ;
              }
            }
            else{
              $query="INSERT INTO $newsletter_tb_name (`parent`,`nom`,`email`) 
                VALUES (1,'".str_replace("'","''",substr($email,0,strpos($email,'@')))."','".str_replace("'","''",$email)."')";
              if($query!='' && false==$wpdb->query($query)){
                $news_reg_return.=__("Erreur d'enregistrement !", 'eelv_lettreinfo');
              }
              elseif($query!=''){
                $news_reg_return.=__("Merci, vous avez bien &eacute;t&eacute; inscrit(e)", 'eelv_lettreinfo');                
              }
            }
            break;
          case '0':
            $ret =  $wpdb->get_results("SELECT * FROM `$newsletter_tb_name` WHERE `email`='".str_replace("'","''",$email)."'");
            if(is_array($ret) && sizeof($ret)>0){
              $query="UPDATE $newsletter_tb_name SET `parent`='2' WHERE `email`='".str_replace("'","''",$email)."'";
              if($query!='' && false===$wpdb->query($query)){
                $news_reg_return.=__("Erreur d'enregistrement !", 'eelv_lettreinfo');
              }
              elseif($query!=''){
                $news_reg_return.=__("Merci, vous avez bien &eacute;t&eacute; d&eacute;sinscrit(e)", 'eelv_lettreinfo');                
              }  
            }
            else{
              $news_reg_return.=__("Votre email n'est pas enregistr&eacute; dans notre liste d'envoi. Aucune d&eacute;sinscription n'est n&eacute;cessaire.", 'eelv_lettreinfo');
            }
            break;
                                    }
      }
      else{
        $news_reg_return.="$email : ".__("adresse non valide", 'eelv_lettreinfo');
      }
    }
  }
  ////////////////////////////////////////////////////////////////////////////////////////////////////// FRONT OFFICE
  function get_news_form($id=''){
    global $wpdb,$newsletter_tb_name,$newsletter_plugin_url,$news_reg_return;
	$eelv_li_xs_archives = get_option('eelv_li_xs_archives',0);
    ?>
<form action="#" method="post" id="newsform<?=$id?>" class="newsform" onsubmit="if(this.news_email.value=='' || this.news_email.value=='newsletter : votre email'){ return false; }">
  <div>
    <label class="screen-reader-text" for="news_email<?=$id?>"><?=__('Inscription &agrave; la newsletter :', 'eelv_lettreinfo')?></label>
    <input type="text" name="news_email" id="news_email<?=$id?>" value="" placeholder="newsletter : votre email" onfocus="document.getElementById('news_hidden_option<?=$id?>').style.display='block';"/>
    <input type="submit" value="ok"/>        
    <div id='news_hidden_option<?=$id?>' class='news_hidden_option'>
      <label for='news_option_1<?=$id?>'><input type="radio" name='news_action' value='1' id='news_option_1<?=$id?>' checked="checked"/><?=__(" S'inscrire", 'eelv_lettreinfo')?></label>
      <label for='news_option_2<?=$id?>'><input type="radio" name='news_action' value='0'  id='news_option_2<?=$id?>'/> <?=__("Se d&eacute;sinscrire", 'eelv_lettreinfo')?></label>
      <?php if($eelv_li_xs_archives==0){ ?>
      <p><a href="/newsletter_archive/"><?=__("Derni&egrave;res Lettres d'info", 'eelv_lettreinfo')?></a></p>
      <?php } ?>
    </div>
    <?php if($news_reg_return!=''){?>
    <div class='news_return' id='news_return<?=$id?>' onclick="document.getElementById('news_return<?=$id?>').style.display='none';">
      <?=$news_reg_return?>
    </div>            
    <?php }  ?>
  </div>
</form>
<?php
  }
  function get_news_large_form(){
    global $wpdb,$newsletter_tb_name,$newsletter_plugin_url,$news_reg_return;
    $ret='
      <form action="#" method="post" id="newslform" onsubmit="if(this.news_email.value=="" || this.news_email.value=="newsletter : votre email"){ return false; }">
      <div>
      <p>
      <label for="news_l_email">'.__('Votre email:', 'eelv_lettreinfo').'</label>
      <input type="text" name="news_email" id="news_l_email" value="" />
      </p>        
      <p>
      <label for="news_l_option_1">
      <input type="radio" name="news_action" value="1" id="news_l_option_1" checked="checked"/> '.__("S'inscrire", 'eelv_lettreinfo').'
      </label>
      </p>
      <p>        
      <label for="news_l_option_2">
      <input type="radio" name="news_action" value="0"  id="news_l_option_2"/> '.__('Se d&eacute;sinscrire', 'eelv_lettreinfo').'
      </label>
      </p>
      <p><input type="submit" value="'.__('ok', 'eelv_lettreinfo').'"/></p>';
    if($news_reg_return!=''){
      $ret.='<div class="news_retour">'.$news_reg_return.'</div>';            
    }
    $ret.='
      <p><a href="/newsletter_archive/">'.__("Derni&egrave;res Lettres d\'information", 'eelv_lettreinfo').'</a></p>
      </div>
      </form>';
    return $ret;
  }
  ////////////////////////////////////////////////////////////////////////////////////////////////////// BACK OFFICE
  function news_liste_groupes(){
    global $newsletter_tb_name,$wpdb;
    //newsletter_checkdb();
    $querystr = "SELECT `id`,`nom` FROM `$newsletter_tb_name` WHERE `parent`='0' ORDER BY `nom`";
    return $wpdb->get_results($querystr);  
  }
  function news_liste_contacts($groupe,$fields='`id`,`nom`,`email`'){
    global $newsletter_tb_name,$wpdb;
    if(is_array($groupe)){
      $groupe = implode("' OR `parent`='",$groupe);
    }
    $querystr = "SELECT $fields FROM `$newsletter_tb_name` WHERE `parent`='$groupe' GROUP BY `email`ORDER BY `nom`";
    return $wpdb->get_results($querystr);    
  }
  /*****************************************************************************************************************************************
  A D R E S S E S                                                           
  *****************************************************************************************************************************************/
  function news_carnet_adresse(){
    global $newsletter_tb_name,$wpdb,$newsletter_plugin_url;
    style_newsletter();
    //newsletter_checkdb();
    ?>
<div class="wrap">
  <div id="icon-edit" class="icon32 icon32-posts-newsletter"><br/></div>
  <h2><?=__("Lettre d'info",'eelv_lettreinfo')?></h2>
  <script>
    function changegrp(form,url,grpname){
      is_confirmed = confirm("êtes vous sûr de vouloir supprimer d&eacute;placer les adresses s&eacute;lectionn&eacute;es vers "+grpname+" ?");
      if (is_confirmed) {
        form.action=url;
        form.submit();
      }  
    }
    function confsup(url,action){
      is_confirmed = confirm("êtes vous sûr de vouloir supprimer d&eacute;finitivement les enregistrements s&eacute;lectionn&eacute;s et tout ce qu'il contiennent ?");
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
  </script>
  <table class="widefat" style="margin-top: 1em;">
    <thead>
      <tr>
        <th scope="col" colspan="2"><?php _e('Carnet d\'adresse', 'eelv_lettreinfo' ) ?></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>
          <?php
  $for='liste';
    $for2='liste';
    $grp_id='';
    $con_id='';
    // Suppression de groupe
    if(isset($_GET['delgroupe'])){
      $for='liste';
      $grp_id = $_GET['delgroupe'];
      $query="DELETE FROM $newsletter_tb_name WHERE `id`='$grp_id' OR `parent`='$grp_id'";
      if(false===$wpdb->query($query)){
        ?><div class="updated"><p><strong><?php _e('Une erreur s\'est produite, aucun groupe n\'a &eacute;t&eacute; supprim&eacute; !', 'eelv_lettreinfo' ) ?></strong></p></div><?php
  }
  else{
  ?><div class="updated"><p><strong><?php _e('Suppression effectu&eacute;e avec succ&egrave;s !', 'eelv_lettreinfo' ) ?></strong></p></div><?php
  }
  $grp_id='';
      }
      // Supression de contacts
      if(isset($_GET['delcontacts']) && isset($_GET['liste'])){
        $grp_id = $_GET['liste'];
        $MBRS = news_liste_contacts($grp_id);    
        if(sizeof($MBRS)>0){
          $ac = '`id`=0';
          $nb=0;
          foreach($MBRS as $contact){ 
            if(isset($_POST['contact_'.$contact->id])){
              $ac.=' OR `id`='.$contact->id;
              $nb++;
            }
          }
          $query="DELETE FROM $newsletter_tb_name WHERE $ac";
          if(false===$wpdb->query($query)){
            ?><div class="updated"><p><strong><?php _e('Une erreur s\'est produite, aucun contact n\'a &eacute;t&eacute; supprim&eacute; !', 'eelv_lettreinfo' ) ?></strong></p></div><?php
  }
  else{
  ?><div class="updated"><p><strong><?php printf(__('Suppression de %s contact(s) effectu&eacute;e avec succ&egrave;s !', 'eelv_lettreinfo' ),$nb) ?></strong></p></div><?php
  }
  }
  }
  // d&eacute;placement de contacts
  if(isset($_GET['ngrp']) && isset($_GET['liste'])){
  $grp_id = $_GET['liste'];
            $MBRS = news_liste_contacts($grp_id);    
            if(sizeof($MBRS)>0){
              $ac = '`id`=0';
              $nb=0;
              foreach($MBRS as $contact){ 
                if(isset($_POST['contact_'.$contact->id])){
                  $ac.=' OR `id`='.$contact->id;
                  $nb++;
                }
              }
              $query="UPDATE $newsletter_tb_name SET `parent`='".str_replace("'","''",$_GET['ngrp'])."' WHERE $ac";
              if(false===$wpdb->query($query)){
                ?><div class="updated"><p><strong><?php _e('Une erreur s\'est produite, aucun contact n\'a &eacute;t&eacute; d&eacute;plac&eacute; !', 'eelv_lettreinfo' ) ?></strong></p></div><?php
  }
  else{
  ?><div class="updated"><p><strong><?php printf(__('D&eacute;placement de %s contact(s) effectu&eacute;e avec succ&egrave;s !', 'eelv_lettreinfo' ),$nb) ?></strong></p></div><?php
  }
  }
  }
  if(isset($_GET['groupe'])){
  $for='groupe';  
                $grp_id = $_GET['groupe'];
              }
              if(isset($_GET['liste'])){
                $for='liste';  
                $grp_id = $_GET['liste'];
              }  
              if(isset($_GET['contact'])){
                $for2='contact';  
                $con_id = $_GET['contact'];
              }
              if(isset($_POST['grp_nom']) ){
                $grp_nom = stripslashes($_POST['grp_nom']);
                if(is_numeric($grp_id)){
                  $query="UPDATE $newsletter_tb_name SET `nom`='".str_replace("'","''",$grp_nom)."' WHERE `id`='$grp_id'";
                }
                else{
                  $query="INSERT INTO $newsletter_tb_name (`nom`) VALUES ('".str_replace("'","''",$grp_nom)."')";
                }
                if(false===$wpdb->query($query)){
                  ?><div class="updated"><p><strong><?php _e('Une erreur s\'est produite...', 'eelv_lettreinfo' ) ?></strong></p></div><?php
  }
  else{
  $for='liste';
                  ?><div class="updated"><p><strong><?php _e('Enregistrement sauvegard&eacute;.', 'eelv_lettreinfo' ); ?></strong></p></div><?php      
                }    
              }
              if(isset($_POST['con_nom']) && is_numeric($grp_id)  ){
                $con_nom = stripslashes($_POST['con_nom']);
                $con_email = stripslashes($_POST['con_email']);
                if(is_numeric($con_id)){
                  $query="UPDATE $newsletter_tb_name SET `nom`='".str_replace("'","''",$con_nom)."',`email`='".str_replace("'","''",$con_email)."' WHERE `id`='$con_id'";
                }
                else{
                  switch($_POST['import_type']){
                    case 'unite':
                      $query="INSERT INTO ".$newsletter_tb_name." (`parent`,`nom`,`email`) 
                        VALUES ('$grp_id','".str_replace("'","''",$con_nom)."','".str_replace("'","''",$con_email)."')";
                      break;
                    case 'masse':
                      $imp = preg_split('/[,;\n\t]/',stripslashes($_POST['con_mul'].','));
                      $query='INSERT INTO '.$newsletter_tb_name.' (`parent`,`nom`,`email`) VALUES ';
                      foreach($imp as $entry){
                        $entry=trim($entry);
                        if (filter_var($entry, FILTER_VALIDATE_EMAIL)) {
                          $query.="              
                            ('$grp_id','".str_replace("'","''",substr($entry,0,strpos($entry,'@')))."','".str_replace("'","''",$entry)."'),";
                        }
                        elseif($entry!=''){
                          echo"<p>$entry : adresse non valide</p>";  
                        }
                      }
                      $query = substr($query,0,-1);
                      $query.="";
                      break;
                    case 'file':
                      ?><div class="updated"><p><strong><?php _e('Fonctionnalit&eacute; en cours de d&eacute;veloppement', 'eelv_lettreinfo' ) ?></strong></p></div><?php
  break;
                                              }
                }
                if($query!='' && false===$wpdb->query($query)){
                  ?><div class="updated"><p><strong><?php _e('Erreur d\'enregistrement !', 'eelv_lettreinfo' ) ?></strong></p></div><?php
  }
  elseif($query!=''){
  ?><div class="updated"><p><strong><?php _e('Enregistrement sauvegard&eacute;.', 'eelv_lettreinfo' ); ?></strong></p></div><?php
                }    
                $for2='liste';
                $for ='liste';  
              }
              // Edition de groupe
              if($for=='groupe'){
                $grp_nom = 'Nouveau groupe';
                $action="edit.php?post_type=newsletter&page=news_carnet_adresse&groupe=new";
                if(is_numeric($grp_id)){
                  $news_info = get_news_meta($grp_id);
                  $grp_nom = $news_info->nom;
                  $action="edit.php?post_type=newsletter&page=news_carnet_adresse&groupe=$grp_id";
                }
                ?>
          <?php _e('&Eacute;dition Groupe', 'eelv_lettreinfo' ) ?>
          <form action='<?=$action;?>' method="post">
            <div id="titlediv">
              <div id="titlewrap">               
                <input type="text" name="grp_nom" size="30" tabindex="1" value="<?=$grp_nom;?>" id="title" autocomplete="off"/>                
              </div>
              <input type='submit' value='<?php _e('Enregistrer', 'eelv_lettreinfo' ) ?>' class="button-primary"/>
            </div>
          </form>
          <p>    <a href="edit.php?post_type=newsletter&page=news_carnet_adresse" class="button add-new-h2"><?php _e('annuler', 'eelv_lettreinfo' ) ?></a></p>
          <?php
  }
  if($for=='liste'){
  ////////////////////////////////////////////////////////////Listes
  if(!is_numeric($grp_id)){ // groupes
  $GRPS = news_liste_groupes();    
                $nb_groups = sizeof($GRPS);
                ?><h3 class="sectiontitle title3"><?php _e('Groupes', 'eelv_lettreinfo' ) ?></h3>  <a href="edit.php?post_type=newsletter&page=news_carnet_adresse&groupe=new" class="button add-new-h2"><?php _e('Nouveau groupe', 'eelv_lettreinfo' ) ?></a>    <?php
  if($nb_groups>0){?>  
          <table class='eelv_news_groups'>   
            <?php
  $coup=false;
                foreach($GRPS as $groupe){ 
                  $nbinsc = sizeof(news_liste_contacts($groupe->id));                
                  ?>      
            <tr>
              <td><a href='edit.php?post_type=newsletter&page=news_carnet_adresse&liste=<?=$groupe->id?>'><b><?=$groupe->nom?></b></a></td>
              <td><b><?=$nbinsc?></b></td>
              <td><a href='edit.php?post_type=newsletter&page=news_carnet_adresse&liste=<?=$groupe->id?>' class="button"><?php _e('Liste', 'eelv_lettreinfo' ) ?></a></td>
              <td><a href='edit.php?post_type=newsletter&page=news_carnet_adresse&groupe=<?=$groupe->id?>' class="button"><?php _e('Renomer', 'eelv_lettreinfo' ) ?></a></td>
              <td><a onclick="confsup('edit.php?post_type=newsletter&page=news_carnet_adresse&delgroupe=<?=$groupe->id?>',1)" class="button"><?php _e('Supprimer', 'eelv_lettreinfo' ) ?></a></td>
            </tr>      
            <?php }  ?>
          </table>
          <?php
  }
  ?><p>&nbsp;</p><?php
  }
  else{ // contacts
  $news_info = get_news_meta($grp_id);
                  $grp_nom = $news_info->nom;
                  $MBRS = news_liste_contacts($grp_id);    
                  $nb_contacts = sizeof($MBRS);
                  ?>
          <h3 class="sectiontitle title3"><a href='edit.php?post_type=newsletter&page=news_carnet_adresse'><?php _e('Groupes', 'eelv_lettreinfo' ) ?></a> > <?=$grp_nom?></h3>  
          <?php  
  if($for2=='liste'){ // liste contact
  ?>
          <table class='eelv_news_groups'><tr><td>
            <input type="checkbox" onclick="tout(document.getElementById('liste_mel'),this)"/>
            <select onchange="eval(this.value)">
              <option value=""><?php _e('Action group&eacute;e', 'eelv_lettreinfo' ) ?></option>
              <?php
  $GRPS = news_liste_groupes();    
                  $nb_groups = sizeof($GRPS);
                  if($nb_groups>0){
                    foreach($GRPS as $groupe){ 
                      if($groupe->id !=$grp_id){
                        $nbinsc = sizeof(news_liste_contacts($groupe->id));                
                        ?>      
              <option value="changegrp(document.getElementById('liste_mel'),'edit.php?post_type=newsletter&page=news_carnet_adresse&liste=<?=$grp_id?>&ngrp=<?=$groupe->id?>','<?=$groupe->nom?>')"><?php _e('d&eacute;placer dans :', 'eelv_lettreinfo' ) ?> <?=$groupe->nom?></option>
              <?php } } }  ?>
              <option value="confsup(document.getElementById('liste_mel'),2)"><?php _e('Supprimer', 'eelv_lettreinfo' ) ?></option>
            </select>
            <a href='edit.php?post_type=newsletter&page=news_carnet_adresse' class="button"><?php _e('Retour', 'eelv_lettreinfo' ) ?></a>
            <a href='edit.php?post_type=newsletter&page=news_carnet_adresse&liste=<?=$grp_id?>&contact=new' class="button-primary"><?php _e('Nouveau destinataire', 'eelv_lettreinfo' ) ?></a>
            </td></tr></table>
          <?php
  if($nb_contacts>0){?>                
          <form id='liste_mel' action="edit.php?post_type=newsletter&page=news_carnet_adresse&liste=<?=$grp_id?>&delcontacts" method="post">
            <table class='eelv_news_groups'> 
              <?php
  $coup=false;
                        foreach($MBRS as $contact){ ?>      
              <tr>
                <td><input type="checkbox" name="contact_<?=$contact->id?>" value="1" /></td>
                <td><a href='edit.php?post_type=newsletter&page=news_carnet_adresse&liste=<?=$grp_id?>&contact=<?=$contact->id?>'><b><?=$contact->nom?></b></a></td>                         <td><a href='edit.php?post_type=newsletter&page=news_carnet_adresse&liste=<?=$grp_id?>&contact=<?=$contact->id?>'><b><?=$contact->email?></b></a></td>                       
              </tr>      
              <?php }  ?>
            </table>
          </form>
          <?php
                      }
                      else{
                        echo"Aucun destinataire n'a encore &eacute;t&eacute; enregistr&eacute;";  
                      }
                      ?>
          <?php
                    }
                    else{ // edit contact
                      $con_nom = '';
                      $action="edit.php?post_type=newsletter&page=news_carnet_adresse&liste=$grp_id&contact=new";
                      if(is_numeric($con_id)){
                        $news_info = get_news_meta($con_id);
                        $con_nom = $news_info->nom;
                        $con_email = $news_info->email;
                        $action="edit.php?post_type=newsletter&page=news_carnet_adresse&groupe=$grp_id&contact=$con_id";
                      }
                      ?>  
          <form action='<?=$action;?>' method="post" enctype="multipart/form-data">              
            <ul>
              <li>
                <label for="imp_unite">
                  <h3><input type="radio" name="import_type" value='unite' id='imp_unite' checked="checked" /> <?php _e('Edition contact', 'eelv_lettreinfo' ) ?></h3>
                  <?php _e('Nom', 'eelv_lettreinfo' ) ?>            
                  <input type="text" name="con_nom" size="30" value="<?=$con_nom;?>" id="con_nom" autocomplete="off" onfocus="import_type[0].checked=true"/>  
                  <?php _e('Email', 'eelv_lettreinfo' ) ?>               
                  <input type="email" name="con_email" size="30" value="<?=$con_email;?>" id="con_email" autocomplete="off" onfocus="import_type[0].checked=true"/>
                </label>
              </li>
              <?php if(!is_numeric($con_id)){ ?>
              <li>
                <label for="imp_masse">
                  <h3><input type="radio" name="import_type" value='masse' id='imp_masse' /> <?php _e('Copier en masse', 'eelv_lettreinfo' ) ?></h3>
                  <p><?php _e('Adresses email s&eacute;par&eacute;es par un retour à la ligne', 'eelv_lettreinfo' ) ?></p>                       
                  <textarea cols="50" rows="10" name="con_mul" id="con_mul" onfocus="import_type[1].checked=true"></textarea>
                </label> 
              </li>
              <!--li>
              <label for="imp_file">
              <h3><input type="radio" name="import_type" value='file' id='imp_file' /> Importer fichier csv</h3>
              <p>Export depuis votre carnet d'adresse habituel... </p>
              <input type='file'  accept="text/csv,text/plain" name="con_file" id="con_file" onfocus="import_type[2].checked=true"/>
            </label>
            </li-->                
              <?php } ?>
            </ul>
            <a href='edit.php?post_type=newsletter&page=news_carnet_adresse&liste=<?=$grp_id?>' class="button">Retour</a>
            <input type="submit" value='Enregistrer' class="button-primary" />
          </form>
          <?php
  }
  }
  }
  ?>
        </td></tr></tbody></table>
</div>
<?php
  }
  /*****************************************************************************************************************************************
  E N V O I                                                                  
  *****************************************************************************************************************************************/
  function news_envoi(){
  style_newsletter();
                      //newsletter_checkdb();
                      global $newsletter_tb_name,$wpdb,$newsletter_plugin_url,$wproles;
                      $default_exp = get_option( 'newsletter_default_exp' );
                      $default_mel = get_option( 'newsletter_default_mel' );
                      $desinsc_url = get_option( 'newsletter_desinsc_url' );
                      ?>
<div class="wrap">
  <div id="icon-edit" class="icon32 icon32-posts-newsletter"><br/></div>
  <h2><?php _e('Lettre d\'information', 'eelv_lettreinfo' ) ?></h2>
  <table class="widefat" style="margin-top: 1em;">
    <thead>
      <tr>
        <th scope="col" colspan="2">Envoi</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>    
          <?php
  if(!isset($_GET['post']) || !is_numeric($_GET['post'])){
  /////////////////////////////////// CHOIX DE LA LETTRE
  $querystr = "SELECT `ID`,`post_title` FROM `$wpdb->posts` WHERE `post_status` = 'publish' AND `post_type` = 'newsletter' ORDER BY `post_name`";
                      $res = $wpdb->get_results($querystr,ARRAY_N);
                      if(sizeof($res)>0){
                        ?><ul><?php
                        foreach($res as $item){
                          ?><li><a href="edit.php?post_type=newsletter&page=news_envoi&post=<?=$item[0]?>"><?=$item[1]?></a></li><?php
                        }
                        ?></ul><?php    
                      }
                      else{
                        ?>
          <?php _e('Aucune lettre n\'est en cours. pour en cr&eacute;er une', 'eelv_lettreinfo' ) ?> <a href="post-new.php?post_type=newsletter" class="button"><?php _e('cliquez ici', 'eelv_lettreinfo' ) ?></a>
          <?php    
  }
  }
  else{
  $post_id = $_GET['post'];
                        $post = get_post( $post_id );
                        if(isset($_GET['convert']) && is_numeric($_GET['convert'])){
							
							$content=$post->post_content;
							if(isset($_GET['add_title'])){
								$content='<h1>'.$post->post_title.'</h1>'.$content;	
							}
							if(isset($_GET['add_sharelinks'])){
								$content.=eelv_newsletter_sharelinks($post->post_title,$post->guid);	
							}
							
                          if(0!== $new_post = wp_insert_post( array('post_type'=>'newsletter','post_title' => $post->post_title,  'post_content' => $content,  'post_status' => 'publish'))){
                            add_post_meta($new_post, 'nl_template', $_GET['convert']);
                            $post_id=$new_post;        
                            $post = get_post( $new_post);
                            echo"Une lettre d'info a &eacute;t&eacute; cr&eacute;&eacute;e";
                          }
                          else{
                            echo "Erreur de converstion...";  
                          }
                        }
                        $content=nl_content($post_id); 
                        $template_id = get_post_meta($post->ID,'nl_template',true);
                        /*$post = get_post( $post_id);
                        $template =  get_post(get_post_meta($post_id,'nl_template',true));
                        $content=str_replace('[newsletter]',(trim($post->post_content)),$template->post_content);
                        $content=str_replace('[desinsc_url]',"<a href='$desinsc_url' target='_blank' class='nl_a'>$desinsc_url</a>",$content);
                        */  
                        if(!isset($_POST['send']) ){
                          get_userdata( $userid );
                          /////////////////////////////////// CHOIX DES DESTINATAIRES
                          ?>
          <h3 class="sectiontitle title3"><?php _e('Aperçu', 'eelv_lettreinfo' ) ?></h3>
          <div class='eelv_news_frame'>
            <?php
  echo $content;
                          ?></div>
          <a href="post.php?post=<?=$post_id?>&action=edit" class="button"><?php _e('Modifier', 'eelv_lettreinfo' ) ?></a>               
          <form action="edit.php?post_type=newsletter&page=news_envoi&post=<?=$post_id?>" method="post" class='eelv_news'>
            <input type="hidden" name="send" value="1" />
            <table><tr>
              <td>
                <h3 class="sectiontitle title3"><?php _e('Entêtes', 'eelv_lettreinfo' ) ?></h3>
                <p><label for="sujet"><?php _e('Sujet', 'eelv_lettreinfo' ) ?>               
                  <input type="text" name="eelv_news_sujet" size="30" tabindex="1" value="<?=$post->post_title?>" id="sujet" autocomplete="off" required/></label> </p>
                <p><label for="exp"><?php _e('Exp&eacute;diteur ', 'eelv_lettreinfo' ) ?>              
                  <input type="text" name="eelv_news_exp" size="30" tabindex="1" value="<?=$default_exp?>" id="exp" autocomplete="off" required/></label> </p>
                <p><label for="mel"><?php _e('Adresse de r&eacute;ponse ', 'eelv_lettreinfo' ) ?>         
                  <input type="email" name="eelv_news_mel" size="30" tabindex="1" value="<?=$default_mel?>" id="mel" autocomplete="off" required/></label></p>
                <p><label for="stat"><?php _e('Statut de l\'archive', 'eelv_lettreinfo' ) ?>            
                  <select name="eelv_news_stat" id="stat" required>
                    <option value='publish'><?php _e('publi&eacute;e', 'eelv_lettreinfo' ) ?></option>
                    <option value='private'><?php _e('priv&eacute;', 'eelv_lettreinfo' ) ?></option>
                  </select>
                  </label> </p>
                 <p><label for="spy"><?php _e('Suivi de l\'envoi', 'eelv_lettreinfo' ) ?>            
                  <select name="eelv_news_spy" id="spy" required>
                    <option value='1'><?php _e('essayer de savoir si les mails sont lus', 'eelv_lettreinfo' ) ?></option>
                    <option value='0'><?php _e('d&eacute;sactiv&eacute;', 'eelv_lettreinfo' ) ?></option>
                  </select>
                  </label> </p>
              </td>
              <td>
                <h3 class="sectiontitle title3"><?php _e('Destinataires', 'eelv_lettreinfo' ) ?></h3>
                <table><tr>
                  <td>
                    <h4><?php _e('Groupes', 'eelv_lettreinfo' ) ?></h4>                  
                    <ul class='eelv_news_groups'>   
                      <?php      
  $GRPS = news_liste_groupes();
                          foreach($GRPS as $groupe){ 
                            $nbinsc = sizeof(news_liste_contacts($groupe->id));        
                            ?>      
                      <li>
                        <label  for='grp_<?=$groupe->id?>'>
                          <input type="checkbox" name='grp_<?=$groupe->id?>' id='grp_<?=$groupe->id?>' value='1'/>
                          <b><?=$groupe->nom?></b>
                          <i>(<?=$nbinsc?>)</i>
                        </label>
                      </li>      
                      <?php }  ?>
                    </ul>
                  </td>
                  <td>
                    <h4><?php _e('Utilisateurs du site', 'eelv_lettreinfo' ) ?></h4>
                    <ul class='eelv_news_groups'> 
                      <?php  
  $result = count_users();
                          foreach($result['avail_roles'] as $role => $count){        
                            ?>      
                      <li>
                        <label  for='rol_<?=$role?>'>
                          <input type="checkbox" name='rol_<?=$role?>' id='rol_<?=$role?>' value='1'/>
                          <b><?=__($role)?></b>
                          <i>(<?=$count?>)</i>
                        </label>
                      </li>      
                      <?php }  ?>
                    </ul>
                  </td>
                  <td>
                    <h4><?php _e('Destinataires suppl&eacute;mentaires', 'eelv_lettreinfo' ) ?></h4>                  
                    <textarea name="dests" cols="60" rows="5"></textarea> 
                  </td>
                  </tr></table>
              </td></tr></table>
            <input type='submit' value='Envoyer' class="button-primary"/>
          </form>
          <?php
  }
  else{
  /////////////////////////////////// ENVOI
  $contacts='';
                          // CUSTOM GROUPES
                          $dest = array();
                          $GRPS = news_liste_groupes();
                          foreach($GRPS as $groupe){ 
                            if(isset($_POST['grp_'.$groupe->id])){
                              array_push($dest,$groupe->id);
                            }
                          }
                          $temp = news_liste_contacts($dest,'email');        
                          foreach($temp as $contact){
                            $contacts.=$contact->email.',';  
                          }
                          // USERS
                          $result = count_users();
                          foreach($result['avail_roles'] as $role => $count){
                            if(isset($_POST['rol_'.$role])){
                              $blogusers = get_users('blog_id='.$wpdb->blogid.'&orderby=nicename&role=$role');
                              foreach ($blogusers as $user) {
                                $contacts.=$user->user_email.',';
                              }
                            }
                          }
                          // UNITE
                          $temp = preg_split('/[;,\n\t]/',$_POST['dests']);
                          foreach($temp as $contact){
                            if(trim($contact)!=''){
                              $contacts.=trim($contact).',';  
                            }
                          }
                          $contacts=implode(',',array_unique(explode(',',$contacts)));
                          if(0=== $archive = wp_insert_post( array('post_type'=>'newsletter_archive','post_title' => $post->post_title,  'post_content' => $post->post_content,  'post_status' => $_POST['eelv_news_stat']))){
                            echo __("Erreur d'enregistrement dans la base de donn&eacute;es !",'eelv_lettreinfo');
                          }
                          else{
                            add_post_meta($archive, 'sujet', $_POST['eelv_news_sujet']);
                            add_post_meta($archive, 'nl_template', $template_id);
                            add_post_meta($archive, 'expediteur', $_POST['eelv_news_exp']);
                            add_post_meta($archive, 'reponse', $_POST['eelv_news_mel']);
                            add_post_meta($archive, 'destinataires', $contacts);
                            add_post_meta($archive, 'sentmails', '');
                            add_post_meta($archive, 'lastsend', date('Y-m-d H:i:s'));
                            add_post_meta($archive, 'nl_spy', $_POST['eelv_news_spy']);
							
							
						  
                            /* $my_postu = array(
                            'post_title' => $post->post_title,
                            'post_content' => $post->post_content,
                            'post_type' => 'newsletter'
                            );
                            // Update the post into the database
                            wp_update_post( $my_postu );*/
                            echo __('Envoi en cours...','eelv_lettreinfo')."
                              <script>
                              document.location='edit.php?post_type=newsletter_archive';
                              </script>
                              ".__("Pour consulter le statut d'envoi, veuillez vous rendre aux",'eelv_lettreinfo')."
                              <a href='edit.php?post_type=newsletter_archive'>".__('archives','eelv_lettreinfo')."</a>
                              ";
                          }
                        }
                      }
                      ?>
        </td></tr></tbody></table>
</div>
<?php  
                    }
                    function newsletter_save_postdata( $post_id ) {
                      if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )      return;
                      if ( isset($_REQUEST['newslettertemplate']) && $_REQUEST['newslettertemplate']!=''){
                        update_post_meta($post_id, 'nl_template', $_REQUEST['newslettertemplate']);
                      }
                    } 
                    function newsletter_admin_prev() {
                      $my_temp=get_post(get_post_meta(get_the_ID(), 'nl_template',true));
                      $env=true;
                      if(get_the_ID()==0){
                        $env=false;
                        echo"<p>".__("Votre lettre d'info n'est pas encore enregistr&eacute;",'eelv_lettreinfo')."</p>";
                      }
                      if(get_the_title()==''){
                        $env=false;
                        echo"<p>".__("Votre lettre d'info n'a pas de titre",'eelv_lettreinfo')."</p>";
                      }
                      if(!$my_temp){
                        $env=false;
                        echo"<p>".__("Aucun habillage n'a &eacute;t&eacute; s&eacute;lectionn&eacute;",'eelv_lettreinfo')."</p>";
                      }
                      if($env==true){
                        echo'<p><a href="edit.php?post_type=newsletter&page=news_envoi&post='.get_the_ID().'" class="button-primary">'.__('Pr&eacute;visualiser et envoyer','eelv_lettreinfo').'</a></p>';
                      }
                    }
					
					
                    function newsletter_admin() {
                      global $wpdb, $eelv_nl_content_themes;
                      //newsletter_checkdb();
                      //print_r($eelv_nl_content_themes);
                      ?>
<table><tr>
  <td valign="top">
    <h4><?php _e('Habillage', 'eelv_lettreinfo' ) ?></h4>
    <?php
  $querystr = "SELECT `ID` FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'newsletter_template' ORDER BY `post_title`";
                      $IDS = $wpdb->get_col($querystr);  
                      $templates_nb = sizeof($IDS);

                      if($templates_nb>0){
                        $my_temp=get_post_meta(get_the_ID(), 'nl_template',true);
                        foreach($IDS as $item_id){ 
                          if($my_temp==NULL){
                            add_post_meta(get_the_ID(), 'nl_template', $item_id);
                            $my_temp=$item_id;
                          }
                          ?>
    <p><label for='nt_<?=$item_id;?>' onclick="set_default_content('dc_<?=$item_id;?>')"><input type='radio' name='newslettertemplate' id='nt_<?=$item_id;?>' value='<?=$item_id;?>' <?php if($item_id==$my_temp){ echo' checked=checked ';} ?>/> <?=get_the_title($item_id);?></label><textarea id="dc_<?=$item_id;?>" style="display:none;"><?=$eelv_nl_content_themes[get_the_title($item_id)]?></textarea></p> 
      <?php }
                      }
                      ?>
      </td><td valign="top" style='padding-left:20px'>
      <h4><?php _e('Ins&eacute;rer du contenu', 'eelv_lettreinfo' ) ?></h4>
      <script>
        var IEbof=false;
      </script>
      <!--[if lt IE 9]>
      <script>IEbof=true;</script>
      <![endif]-->
      <script>
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
            if(confirm("Voulez vous charger le contenu par d&eacute;faut pour ce mod&egrave;le ?\n\nAttention !\n\nVous aller perdre tout le contenu actuel.")){
              str=ki.value;
              switchEditors.go('content', 'html');
              document.post.content.value=str;
              switchEditors.go('content', 'tinymce');  
            }
          }
        }
      </script>
      <?php
  $querystr = "";
                      $optis='<option value="">'.__('Articles', 'eelv_lettreinfo' ).'</option>';
                      wp_reset_query();
                      query_posts(array('status'=>'publish','post_type'=>'post','posts_per_page'=>'-1'));
                      if(have_posts()){  
                        while(have_posts()){
                          the_post();  
                          ?>
      <textarea id="nl_post_<?php the_ID();?>" style="display:none"><?php echo"<div style='width:550px; margin:5px 0px;text-align:left;  clear:both; border-top:#CCC 1px dotted; padding-top:1em; margin-top:1em;'>
  <a href='".get_post_permalink()."' style='text-decoration:none;color:#666666;'>".get_the_post_thumbnail(get_the_ID(),array(550,100),array('style'=>'float:left; margin-right:10px;'))."</a> <h3 style='margin:0px !important;'><a href='".get_post_permalink()."' style='text-decoration:none;color:#000000;'>".get_the_title()."</a></h3>
  <a href='".get_post_permalink()."' style='text-decoration:none;color:#666666;'>".substr(strip_tags(get_the_content()),0,300)."...</a>
  </div>&nbsp;
  "; ?></textarea>
      <textarea id="nl_share_<?php the_ID();?>" style="display:none"><?php echo eelv_newsletter_sharelinks(get_the_title(),get_post_permalink()); ?></textarea>
      <?php 
                          $optis.='<option value="'.get_the_ID().'">'.substr(get_the_title(),0,70).'</option>';
                        } ?> 
      <p><select name="nl_insert_post" onchange="var nl_p_content=getElementById('nl_post_'+this.value).value; if(document.getElementById('nl_with_share').checked==true){nl_p_content+=getElementById('nl_share_'+this.value).value}incontent(nl_p_content);this.value=''">
        <?=$optis?>
        </select>
      </p>
      <?php  }
                      
                      $optis='<option value="">'.__('Pages', 'eelv_lettreinfo' ).'</option>';
                      wp_reset_query();
                      query_posts(array('status'=>'publish','post_type'=>'page','posts_per_page'=>'-1'));
                      if(have_posts()){  
                        while(have_posts()){
                          the_post();  
                          ?>
      <textarea id="nl_page_<?php the_ID();?>" style="display:none"><?php echo"<div style='width:550px; margin:5px 0px;text-align:left;  clear:both; border-top:#CCC 1px dotted; padding-top:1em; margin-top:1em;'>
  <a href='".get_post_permalink()."' style='text-decoration:none;color:#666666;'>".get_the_post_thumbnail(get_the_ID(),array(550,100),array('style'=>'float:left; margin-right:10px;'))."</a> <h3 style='margin:0px !important;'><a href='".get_post_permalink()."' style='text-decoration:none;color:#000000;'>".get_the_title()."</a></h3>
  <a href='".get_post_permalink()."' style='text-decoration:none;color:#666666;'>".substr(strip_tags(get_the_content()),0,300)."...</a>
  </div>&nbsp;
  "; ?></textarea>
      
      <?php 
                          $optis.='<option value="'.get_the_ID().'">'.substr(get_the_title(),0,70).'</option>';
                        } ?> 
      <p><select name="nl_insert_page" onchange="incontent(getElementById('nl_page_'+this.value).value);this.value=''">
        <?=$optis?>
        </select></p>
      <?php  }
                      ?>
      <label for="">
        <input type="checkbox" id="nl_with_share" checked="checked"/><?=__('Ajouter les boutons de partage', 'eelv_lettreinfo' )?>
      </label>
      </td></tr></table>
      <?php
                    }
                    function newsletter_archive_admin() {
                      global $wpdb,$newsletter_plugin_url;
                      $post_id = get_the_ID(); //$_GET['id'];
                      $my_temp=get_post_meta($post_id, 'nl_template',true);
                      $sujet = get_post_meta($post_id, 'sujet', true);
                      $expediteur = get_post_meta($post_id, 'expediteur', true);
                      $reponse = get_post_meta($post_id, 'reponse' ,true);
                      $lastsend = get_post_meta($post_id, 'lastsend',true);  
                      // $post = get_post( $post_id);
                      $template =  get_post(get_post_meta($post_id,'nl_template',true));
                      $content=nl_content($post_id );   ?>
      <h2><?=$sujet?></h2>
      <?php if(!$template){ ?>
      <?php _e('L\'habillage a disparu ! voulez en appliquer un nouveau ?', 'eelv_lettreinfo' ) ?>
      <select name="newslettertemplate">
        <option></option>
        <?php
  $querystr = "SELECT `ID` FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'newsletter_template' ORDER BY `post_title`";
$IDS = $wpdb->get_col($querystr);  
$templates_nb = sizeof($IDS);
if($templates_nb>0){
  $my_temp=get_post_meta(get_the_ID(), 'nl_template',true);
  foreach($IDS as $item_id){ 
    if($my_temp==NULL){
      add_post_meta(get_the_ID(), 'nl_template', $item_id);
      $my_temp=$item_id;
    }
    ?>
        <option value='<?=$item_id;?>'><?=get_the_title($item_id);?></option> 
        <?php }
}
?></select>
      <?php } ?>
      <p>envoy&eacute; par : <?=$expediteur?> (<?=$reponse?>)</p>
      <p>dernier envoi le : <?=$lastsend?></p>
      <div><?=$content?></div>
      <?php 
                    }
                    function newsletter_archive_admin_dest() {
                      global $wpdb,$newsletter_plugin_url;
                      $post_id = get_the_ID(); //$_GET['id'];
                      $sent = get_post_meta($post_id, 'sentmails',true);
					  $nl_spy=get_post_meta($post_id, 'nl_spy',true);
					  $lus = abs(substr_count($sent,':3'));
		  			 $tot = abs(substr_count($sent,','));
                      ?>    
                    <p><?php  printf(__('%s d\'ouverture','eelv_lettreinfo'),round($lus/$tot*100).'%'); ?></p>
      <p><ul id="eelv_nl_sentlist"><?php 
                      echo '<li data-email="'.str_replace(
                        array(
						  ',',	
                          ':',
                        ),
                        array(
						  '"></li><li data-email="',
                          '" class="eelv_nl_sent eelv_nl_status_',
                        ),
                        $sent).'">&nbsp;</li>';
                      
                      ?></ul>
                      <?php if($nl_spy==0) _e('Pas de suivi de lecture','eelv_lettreinfo'); ?>
                      </p>
					  <style>
					  #eelv_nl_sentlist li.eelv_nl_sent{
						display:inline-block;
						width:30%;
						padding:7px;
						margin:2px;
						background-position-x: 98%;
						background-position-y: 50%;
						background-repeat:no-repeat;
						background-color:#FFF;
						border:#CCC 1px outset;
						border-radius:5px;
						box-shadow:rgba(0,0,0,0.2) 0px 2px 3px; 
					  }
					  #eelv_nl_sentlist li.eelv_nl_status_-1{
						background-image:url(<?=$newsletter_plugin_url?>/eelv-newsletter/img/-1.jpg);
						color:#900;
					  }
					  #eelv_nl_sentlist li.eelv_nl_status_0{
						background-image:url(<?=$newsletter_plugin_url?>/eelv-newsletter/img/0.jpg);
						color:#900;
					  }
					  #eelv_nl_sentlist li.eelv_nl_status_1{
						background-image:url(<?=$newsletter_plugin_url?>/eelv-newsletter/img/1.jpg);
						color:#090;
					  }
					  #eelv_nl_sentlist li.eelv_nl_status_2{
						background-image:url(<?=$newsletter_plugin_url?>/eelv-newsletter/img/2.jpg);
						color:#900;
					  }
					  #eelv_nl_sentlist li.eelv_nl_status_3{
						background-image:url(<?=$newsletter_plugin_url?>/eelv-newsletter/img/3.jpg);
						border:#0F0 1px outset;
						color:#0C0;
					  }
					  
					  </style>
                      <script>
					  jQuery(document).ready(function(e) {
                        jQuery('#eelv_nl_sentlist').children('li').each(function(index, element) {
							if(jQuery(this).data('email')!=''){
								
					  			jQuery(this).html(jQuery(this).data('email')).click(function(){
									<?php if($nl_spy==1): ?>
									jQuery.ajax({
										type: 'POST',
										url:'<?=$newsletter_plugin_url?>/eelv-newsletter/reading/check.php?i=<?=$post_id?>&m='+jQuery(this).data('email'),
										dataType: 'json',
										async: false,
										success: function (k) {
											var txt='';
											for(var i=0 ; i<k.length ; i++){
											  txt+='<?=str_replace('\'','\\\'',__('Lu le :','eelv_lettreinfo'))?> '+k[i]['date']+'\n';											  
											  txt+='<?=str_replace('\'','\\\'',__('Sur :','eelv_lettreinfo'))?> '+k[i]['user_agent']+'\n';
											  txt+='<?=str_replace('\'','\\\'',__('Depuis l\'adresse IP :','eelv_lettreinfo'))?> '+k[i]['ip']+'\n';
											  txt+='\n\n'; 
											}
											if(txt==''){
												txt='<?=str_replace('\'','\\\'',__('Non lu','eelv_lettreinfo'))?> ';	
											}
											alert(txt);
										}
									});	
									<?php endif; ?>
								});
								
							}
                    	});
                    });
					  
					  </script>
      <?php 
                    }
                    function newsletter_archive_admin_queue() {
                      global $wpdb,$newsletter_plugin_url;
                      $post_id = get_the_ID(); //$_GET['id'];
                      $dest = get_post_meta($post_id, 'destinataires',true);
                      ?>    
      <p><?=$dest?></p>
      <?php if($dest!=''){ ?>
      <a href='edit.php?post=<?=$post_id?>&action=edit&ref=<?=time()?>'><?=__('Envoi automatique d\'une nouvelle salve','eelv_lettreinfo')?></a>
      <script>
        document.location='edit.php?post=<?=$post_id?>&action=edit&ref=<?=time()?>';
      </script>
      <?php }
                    }
                    ///////////////////////////////////// CHECK DB
                    function newsletter_checkdb(){
                      ?>
      <div class="wrap">
        <div id="icon-edit" class="icon32 icon32-posts-newsletter"><br/></div>
        <h2><?php _e('Lettre d\'information', 'eelv_lettreinfo' ) ?></h2>
        <table class="widefat" style="margin-top: 1em;">
          <thead>
            <tr>
              <th scope="col" colspan="2"><?php _e('V&eacute;rification', 'eelv_lettreinfo' ) ?></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>
                <?php
  global $newsletter_tb_name,$wpdb,$newsletter_plugin_url,$eelv_nl_default_themes;
                      // GROUPE NON CLASSE
                      $ret =  $wpdb->get_results("SELECT * FROM `$newsletter_tb_name` WHERE `id`='1'");
                      echo'<h3>Carnet d\'adresses</h3>';
                      echo'<p>Groupe "Non class&eacute;s" : ';
                      if(is_array($ret) && sizeof($ret)>0){
                        $query="UPDATE $newsletter_tb_name SET `nom`='Non class&eacute;s',`email`='',`parent`='0' WHERE `id`='1'";
                        echo'ok';
                      }
                      else{
                        $query="INSERT INTO $newsletter_tb_name (`id`,`nom`) VALUES ('1','Non class&eacute;s')";
                        'ajout ok';
                      }
                      echo'  </p>';
                      $wpdb->query($query);
                      // GROUPE RED LIST
                      echo'<p>Liste rouge : ';
                      $ret =  $wpdb->get_results("SELECT * FROM `$newsletter_tb_name` WHERE `id`='2'");
                      if(is_array($ret) && sizeof($ret)>0){
                        $query="UPDATE $newsletter_tb_name SET `nom`='Liste rouge',`email`='',`parent`='0' WHERE `id`='2'";
                        echo'ajout ok';
                      }
                      else{
                        $query="INSERT INTO $newsletter_tb_name (`id`,`nom`) VALUES ('2','Liste rouge')";
                        echo'cr&eacute;&eacute;';
                      }
                      echo'  </p>';
                      $wpdb->query($query);
                      // THEMES PAR DEFAUT
                      echo'<h3>Habillages par defaut</h3>';
                      foreach($eelv_nl_default_themes as $check_theme=>$check_content){
                        echo'<p><b>'.$check_theme.'</b> : ';
                        $req="SELECT * FROM $wpdb->posts WHERE post_type = 'newsletter_template' AND `post_status`='publish' AND `post_title`='$check_theme'";
                        $ret =  $wpdb->get_results($req);
                        if(is_array($ret) && sizeof($ret)>0){
                          if(sizeof($ret)>1){      
                            $wpdb->query("DELETE FROM `$wpdb->posts` WHERE `post_type`='newsletter_template' AND `post_status`='publish'  AND `post_title`='$check_theme'");
                          }
                          $my_postb = array(
                            'ID' => $ret[0]->ID,
                            'post_content' => $check_content
                          );
                          wp_update_post( $my_postb );
                          echo'mise à jour ok';
                        }
                        else{
                          $my_posta = array(
                            'post_type' => 'newsletter_template',
                            'post_title' => $check_theme,
                            'post_content' => $check_content,
                            'post_status' => 'publish'
                          );
                          wp_insert_post( $my_posta );
                          echo'ajout ok';
                        }
                        echo'</p>';
                      } ?>
              </td></tr></tbody></table></div>
      <?php
                    }
                    ///////////////////////////////////// SEMI CRON AUTO SEND
                    function newsletter_autosend(){
                      global $newsletter_tb_name,$wpdb,$newsletter_plugin_url,$eelv_nl_default_themes;
                      $querystr = "SELECT $wpdb->posts.`ID` FROM $wpdb->posts,$wpdb->postmeta WHERE (post_status = 'publish' OR post_status = 'private') AND post_type = 'newsletter_archive'  AND $wpdb->postmeta.`post_id`=$wpdb->posts.`ID` AND $wpdb->postmeta.`meta_key`='destinataires' AND $wpdb->postmeta.`meta_value`!=''";
                      $IDS = $wpdb->get_col($querystr);  
                      $send_nb = sizeof($IDS);
                      if($send_nb>0){
                        $desinsc_url = get_option( 'newsletter_desinsc_url' );
                        $env=0;
                        foreach($IDS as $post_id){ 
                          $my_temp=get_post_meta($post_id, 'nl_template',true);
                          $sujet = get_post_meta($post_id, 'sujet', true);
                          $expediteur = get_post_meta($post_id, 'expediteur', true);
                          $reponse = get_post_meta($post_id, 'reponse' ,true);
                          $dests = get_post_meta($post_id, 'destinataires',true);
                          $nl_spy = get_post_meta($post_id, 'nl_spy',true);
                          if(substr($dests,0,1)==',') $dests=substr($dests,1);
                          $dests = explode(',',$dests);
                          $sent = get_post_meta($post_id, 'sentmails',true);  
                          $template=get_post($my_temp);
                          if($template){
                            $content = "<center><a href='".home_url()."/?post_type=newsletter_archive&p=".$post_id."' target='_blank'><font size='1'>Si vous n'arrivez pas à lire cet email, cliquez ici</font></a></center>".nl_content($post_id);
                            $prov = getenv("SERVER_NAME");
                            $eol="\n";
                            $now = time();
                            $headers = "From: $expediteur <$reponse>".$eol;
                            $headers .= "Reply-To: $expediteur <$reponse>".$eol;
                            $headers .= "Return-Path: $expediteur <$reponse>".$eol;    
                            $headers .= "Message-ID: <".$post_id."@".$prov.">".$eol;
                            $headers .= "X-Mailer: PHP v".phpversion().$eol;         
                            $mime_boundary="----=_NextPart_".md5(time());
                            $headers .= 'MIME-Version: 1.0'.$eol;
                            $headers .= "Content-Type: text/html; charset=\"utf-8\"; Content-Transfer-Encoding: quoted-printable; boundary=\"".$mime_boundary."\"".$eol;
                            //print_r($dests);    
                            $newsletter_admin_surveillance = get_site_option( 'newsletter_admin_surveillance' );
                            if($newsletter_admin_surveillance!=''){
                              mail($newsletter_admin_surveillance,'[EELV-newsletter:envoi] '.$sujet,$content,$headers);
                            }
                            while($dest = array_shift($dests)){
                              $dest=trim($dest);
                              if (filter_var($dest, FILTER_VALIDATE_EMAIL)) {
                                $ret = $wpdb->get_results("SELECT * FROM `$newsletter_tb_name` WHERE `email`='".str_replace("'","''",$dest)."' AND `parent`='2' LIMIT 0,1");
                                if(is_array($ret) && sizeof($ret)==0){             // White liste OK            
                                  if( update_post_meta($post_id, 'destinataires',implode(',',$dests)) ){
									$the_content=$content;
									if($nl_spy==1){
										  $the_content.='<a href="'.get_bloginfo('url').'"><img src="'.$newsletter_plugin_url.'/eelv-newsletter/reading/'.base64_encode($dest.'!'.$post_id).'/logo.png" border="none" alt="'.get_bloginfo('url').'"/></a>';
									  }
                                    if(mail($dest,$sujet,$the_content,$headers)){  // Envoi OK
                                      $sent = $dest.':1,'.$sent;
                                    }
                                    else{                    // Envoi KO
                                      $sent = $dest.':0,'.$sent;
                                    }
                                    update_post_meta($post_id, 'sentmails',$sent);
                                    $env++;
                                  }
                                }
                                elseif(is_array($ret) && sizeof($ret)==1){           // Black list
                                  $sent = $dest.':2,'.$sent;
                                  update_post_meta($post_id, 'destinataires',implode(',',$dests));
                                  update_post_meta($post_id, 'sentmails',$sent);
                                }
                                else{                         // Envoi OK
                                  $sent = $dest.':0+,'.$sent;
                                  update_post_meta($post_id, 'destinataires',implode(',',$dests));
                                  update_post_meta($post_id, 'sentmails',$sent);
                                }
                              }
                              else{            // Mail invalide
                                $sent = $dest.':-1,'.$sent;
                                update_post_meta($post_id, 'destinataires',implode(',',$dests));
                                update_post_meta($post_id, 'sentmails',$sent);
                              }
                              if($env>100){
                                break 2;
                              }
                            }
                          }
                        }
                      }
                    }
                    /*****************************************************************************************************************************************
                    C O N F I G U R A T I O N                                            *****************************************************************************************************************************************/
function newsletter_network_configuration(){
  if( $_REQUEST[ 'type' ] == 'update' ) {    
	update_site_option( 'newsletter_admin_surveillance', $_REQUEST['newsletter_admin_surveillance'] );      
	?>
<div class="updated"><p><strong><?php _e('Options sauvegard&eacute;es.', 'eelv_lettreinfo' ); ?></strong></p></div>
<?php 
  }
  $newsletter_admin_surveillance = get_site_option( 'newsletter_admin_surveillance' );
  ?>  
      <div class="wrap">
        <div id="icon-edit" class="icon32 icon32-posts-newsletter"><br/></div>
        <h2><?=_e('Lettre d\'information', 'eelv_lettreinfo' )?></h2>
        <form name="typeSite" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">  
          <input type="hidden" name="type" value="update">
          <table class="widefat" style="margin-top: 1em;">
            <thead>
              <tr>
                <th scope="col" colspan="2"><?= __( 'Configuration ', 'menu-config' ) ?></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td width="30%">
                  <label for="newsletter_default_exp"><?=_e('Envoyer une copie de chaque salve d\'envois à:', 'eelv_lettreinfo' )?> :</label>
                </td><td>
                <input  type="text" name="newsletter_admin_surveillance"  size="60"  id="newsletter_admin_surveillance"  value="<?=$newsletter_admin_surveillance?>" class="wide">
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <p class="submit">
                    <input type="submit" name="Submit" value="<?php _e('sauvegarder', 'eelv_lettreinfo' ) ?>" />
                  </p>                    
                </td>
              </tr>
            </tbody>
          </table>
        </form>
      </div>
      <?php
}
// mt_toplevel_page() displays the page content for the custom Test Toplevel menu
function newsletter_page_configuration() {
  global $newsletter_plugin_url,$wpdb;
  if( $_REQUEST[ 'type' ] == 'update' ) {    
	update_option( 'newsletter_default_exp', $_REQUEST['newsletter_default_exp'] );
	update_option( 'newsletter_default_mel', $_REQUEST['newsletter_default_mel'] );
	update_option( 'newsletter_desinsc_url', $_REQUEST['newsletter_desinsc_url'] );
	update_option( 'affichage_NL_hp', $_REQUEST['affichage_NL_hp'] );
	?>
<div class="updated"><p><strong><?php _e('Options sauvegard&eacute;es.', 'eelv_lettreinfo' ); ?></strong></p></div>
<?php 
  }  
  $default_exp = get_option( 'newsletter_default_exp' );
  $default_mel = get_option( 'newsletter_default_mel' );
  $desinsc_url = get_option( 'newsletter_desinsc_url' );
  $affichage_NL_hp = get_option( 'affichage_NL_hp' );
  ?>  
      <div class="wrap">
        <div id="icon-edit" class="icon32 icon32-posts-newsletter"><br/></div>
        <h2><?=_e('Lettre d\'information', 'eelv_lettreinfo' )?></h2>
        <form name="typeSite" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">  
          <input type="hidden" name="type" value="update">
          <table class="widefat" style="margin-top: 1em;">
            <thead>
              <tr>
                <th scope="col" colspan="2"><?= __( 'Configuration ', 'menu-config' ) ?></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td width="30%">
                  <label for="newsletter_default_exp"><?php _e('Nom d\'exp&eacute;diteur par d&eacute;faut :', 'eelv_lettreinfo' ) ?></label>
                </td><td>
                <input  type="text" name="newsletter_default_exp"  size="60"  id="newsletter_default_exp"  value="<?=$default_exp?>" class="wide">
                </td>
              </tr>
              <tr>
                <td width="30%">
                  <label for="newsletter_default_mel"><?php _e('Adresse de r&eacute;ponse par d&eacute;faut :', 'eelv_lettreinfo' ) ?></label>
                </td><td>
                <input  type="text" name="newsletter_default_mel"  size="60"  id="newsletter_default_mel"  value="<?=$default_mel?>" class="wide">
                </td>
              </tr>
              <tr>
                <td width="30%">
                  <label for="newsletter_desinsc_url"><?php _e('Page de d&eacute;sinscription :', 'eelv_lettreinfo' ) ?></label>
                  <legend><?=$desinsc_url?></legend>
                </td><td>
                <select  name="newsletter_desinsc_url"   id="newsletter_desinsc_url">
                  <option></option>
                  <?php
  $querystr = "";
                      $ret =  $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'page' ORDER BY `post_title`");
                      if(is_array($ret) && sizeof($ret)>0){            
                        foreach($ret as $item){ 
                          $permalink = get_permalink( $item->ID );
                          ?>
                  <option value="<?=$permalink;?>" <? if($desinsc_url==$permalink) echo"selected"; ?>><?=$item->post_title;?></option> 
                  <?php } 
                      }
                      ?>
                </select>
                </td>
              </tr>
               <tr>
                <td colspan="2">
                  <input type='submit' value='<?php _e('Enregistrer', 'eelv_lettreinfo' ) ?>' class="button-primary"/>
                </td>
              </tr>
            </tbody>
          </table>
          <table class="widefat" style="margin-top: 1em;">
            <thead>
              <tr>
                <th scope="col"><?php _e('Aide', 'eelv_lettreinfo' ) ?></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  <?php _e('Shortcodes dans le site:', 'eelv_lettreinfo' ) ?>
                  <ul>
                    <li><?php _e('insertion du formulaire d\'inscription dans une page :', 'eelv_lettreinfo' ) ?><strong>[eelv_news_form]</strong></li>
                  </ul>
                  <?php _e(' Shortcodes dans un th&egrave;me:', 'eelv_lettreinfo' ) ?>        
                  <ul>
                    <li><?php _e('insertion du contenu d\'une lettre dans un th&egrave;me : ', 'eelv_lettreinfo' ) ?><strong>[newsletter]</strong></li>
                    <li><?php _e('insertion du contenu d\'une lettre dans un th&egrave;me :', 'eelv_lettreinfo' ) ?><strong>[desinsc_url]</strong></li>
                  </ul>
                  <?php _e('L&eacute;gende des symboles d\'envoi :', 'eelv_lettreinfo' ) ?><ul>
                  <li><img src="<?=$newsletter_plugin_url?>/eelv-newsletter/img/-1.jpg"/> <?php _e('Email invalide:', 'eelv_lettreinfo' ) ?></li>
                  <li><img src="<?=$newsletter_plugin_url?>/eelv-newsletter/img/0.jpg"/> <?php _e('Envoi &eacute;chou&eacute;:', 'eelv_lettreinfo' ) ?></li>
                  <li><img src="<?=$newsletter_plugin_url?>/eelv-newsletter/img/1.jpg"/> <?php _e('Envoi effectu&eacute; avec succ&egrave;s:', 'eelv_lettreinfo' ) ?></li>
                  <li><img src="<?=$newsletter_plugin_url?>/eelv-newsletter/img/2.jpg"/> <?php _e('Adresse inscrite sur la liste des d&eacute;sinscrits:', 'eelv_lettreinfo' ) ?></li>
                  <li><img src="<?=$newsletter_plugin_url?>/eelv-newsletter/img/3.jpg"/> <?php _e('L\'email a &eacute;t&eacute; lu', 'eelv_lettreinfo' ) ?></li>
                  </ul>
                </td></tr></tbody></table>
        </form>
      </div>
      <?php
  }  
function eelv_lettrinfo_locate_plugin_template($template_names, $load = false, $require_once = true ){
  if ( !is_array($template_names) )
  return '';     
  $located = '';     
  $this_plugin_dir = WP_PLUGIN_DIR.'/'.str_replace( basename( __FILE__), "", plugin_basename(__FILE__) );     
  foreach ( $template_names as $template_name ) {
	if ( !$template_name )
	  continue;
	if ( file_exists(STYLESHEETPATH . '/' . $template_name)) {
	  $located = STYLESHEETPATH . '/' . $template_name;
	  break;
	} else if ( file_exists(TEMPLATEPATH . '/' . $template_name) ) {
	  $located = TEMPLATEPATH . '/' . $template_name;
	  break;
	} else if ( file_exists( $this_plugin_dir .  $template_name) ) {
	  $located =  $this_plugin_dir . $template_name;
	  break;
	}
  }     
  if ( $load && '' != $located )
	load_template( $located, $require_once );     
  return $located;
}
function eelv_lettrinfo_get_custom_archive_template($template){
  global $wp_query;
  if($wp_query->get_queried_object()->query_var=='newsletter_archive') {  
	  $templates = array('archive-newsletter_archive.php', 'archive.php');
	  $template = eelv_lettrinfo_locate_plugin_template($templates);
  }
  return $template;
}
function eelv_lettrinfo_get_custom_single_template($template){
  global $wp_query;
  $object = $wp_query->get_queried_object();
  if ( 'newsletter_archive' == $object->post_type ) {
	$templates = array('single-' . $object->post_type . '.php', 'single.php');
	$template = eelv_lettrinfo_locate_plugin_template($templates);
  }
  return $template;
}
////////////////////////////////////////////////////////////////////////////////////////////////////// WIDGET
wp_register_sidebar_widget(
  'widget_eelv_lettreinfo_insc',        // your unique widget id
  __('EELV Inscription &agrave; la Lettre d\'info','eelv_lettreinfo'),          // widget name
  'widget_eelv_lettreinfo_side',  // callback function
  array(                  // options
	'description' => __('Formulaire d\'inscription/d&eacute;sinscription et archives de la Lettre d\'info','eelv_lettreinfo')
  )
);
function widget_eelv_lettreinfo_side($params) {
  $eelv_li_xs_title= get_option('eelv_li_xs_title')?>
      <?php echo $params['before_widget']; ?>
      <?php echo $params['before_title'];?>
      <?php echo  $eelv_li_xs_title; ?>
      <?php echo $params['after_title'];?>
      <?php echo $params['before_content'];?>
      <?php get_news_form('widget'); ?>
      <?php echo $params['after_content'];?>
      <?php echo $params['after_widget'];?>
      <?php
                    }
                    
                    register_widget_control('widget_eelv_lettreinfo_insc','widget_eelv_lettreinfo_insc_control');
                    function widget_eelv_lettreinfo_insc_control(){
                      if( isset($_POST['eelv_li_xs_title']) ){
                        update_option('eelv_li_xs_title', stripslashes($_POST['eelv_li_xs_title']));
                        update_option('eelv_li_xs_archives', $_POST['eelv_li_xs_archives']);
                        echo 'Options sauvegard&eacute;es<br/>';
                      }
                      $eelv_li_xs_title= get_option('eelv_li_xs_title');
                      $eelv_li_xs_archives = get_option('eelv_li_xs_archives',0);
                      ?>
      <p><label for='eelv_li_xs_title'><?php _e('Titre', 'eelv_lettreinfo' ) ?><br/>
        <input type='text' name='eelv_li_xs_title' id='eelv_li_xs_title' value="<?=$eelv_li_xs_title?>"/></label>
      </p>
      <p><label for='eelv_li_xs_archives'><?php _e('Cacher le lien vers les archives', 'eelv_lettreinfo' ) ?><br/>
        <select name='eelv_li_xs_archives' id='eelv_li_xs_archives'>
            <option value="0" <?php if($eelv_li_xs_archives==0){ echo'selected'; } ?>><?php _e('Non', 'eelv_lettreinfo' ) ?></option>
            <option value="1" <?php if($eelv_li_xs_archives==1){ echo'selected'; } ?>><?php _e('Oui', 'eelv_lettreinfo' ) ?></option>
        </select>
        </label>
      </p>
      <?php
  }
  function news_transform(){
  global $wpdb;
  $querystr = "SELECT `ID` FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'newsletter_template' ORDER BY `post_title`";
  $IDS = $wpdb->get_col($querystr);  
  $templates_nb = sizeof($IDS);
  if($templates_nb>0){ ?>
  
  <input type="hidden" id="eelv_nl_convert_link" value="edit.php?post_type=newsletter&page=news_envoi&post=<?=get_the_ID()?>"/>
	<?php foreach($IDS as $item_id){ ?>
<p><label for="eelv_nl_convert_id_<?=$item_id?>"><input type="radio" id="eelv_nl_convert_id_<?=$item_id?>" name="eelv_nl_convert_id" value="<?=$item_id?>"/> <?=get_the_title($item_id);?></label></p> 
<?php } ?>
	<hr/>
    <p><label for="eelv_nl_convert_title"><input type="checkbox" id="eelv_nl_convert_title"  value="1"/> <?php _e("Ajouter le titre au contenu",'eelv_lettreinfo'); ?></label></p>
    <p><label for="eelv_nl_convert_share"><input type="checkbox" id="eelv_nl_convert_share"  value="1"/> <?php _e("Ajouter les boutons de partage",'eelv_lettreinfo'); ?></label></p>
    
    
    <p><a id="eelv_nl_convert_a" class="button"> <?php _e("Pr&eacute;visualiser et envoyer",'eelv_lettreinfo'); ?></a></p>
    <script>
	jQuery(document).ready(function(e) {
		jQuery('#eelv_nl_convert_a').click(function(){
			var lien=jQuery('#eelv_nl_convert_link').attr('value');
			lien+='&convert=';
			lien+=jQuery('input[type=radio][name=eelv_nl_convert_id]:checked').attr('value');
			if(jQuery('#eelv_nl_convert_title').is(':checked')==true){
				lien+='&add_title=1';
			}
			if(jQuery('#eelv_nl_convert_share').is(':checked')==true){
				lien+='&add_sharelinks=1';
			}
			//console.log(lien);
			document.location=lien;
			return false;
		});
	});
	</script>
<?php
  }else{
	_e("aucun habillage disponible",'eelv_lettreinfo');  
  }  
}
///////////////////////////////////////////////////////////////////////// INSERTION DANS WORDPRESS
register_activation_hook(__FILE__,'eelvnewsletter_install');
add_action( 'save_post', 'newsletter_save_postdata' );
add_action('plugins_loaded', 'eelvnewsletter_update_db_check');
add_action( 'add_meta_boxes', 'newsletter_add_custom_box' );
add_action( 'init', 'newsletter_BO' );
add_action('admin_menu', 'eelv_news_ajout_menu');
add_action( 'network_admin_menu', 'eelv_news_ajout_network_menu'); 
add_action('wp_head', 'style_newsletter');
add_shortcode( 'eelv_news_form' , 'get_news_large_form' );
add_action( 'admin_init', 'newsletter_autosend' );
add_filter( 'archive_template', 'eelv_lettrinfo_get_custom_archive_template' );
add_filter( 'single_template', 'eelv_lettrinfo_get_custom_single_template' );
add_filter('manage_newsletter_posts_columns', 'lettreinfo_columns_head');  
add_action('manage_newsletter_posts_custom_column', 'lettreinfo_columns_content', 10, 2); 
add_filter('manage_newsletter_archive_posts_columns', 'lettreinfo_archives_columns_head');  
add_action('manage_newsletter_archive_posts_custom_column', 'lettreinfo_archives_columns_content', 10, 2); 

