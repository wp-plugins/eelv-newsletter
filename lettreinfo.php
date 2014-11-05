<?php
/*
Plugin Name: EELV Newsletter
Plugin URI: http://ecolosites.eelv.fr/tag/newsletter/
Description:  Add a registration form on frontOffice, a newsletter manager on BackOffice
Version: 3.12.1
Author: bastho, ecolosites // EELV
Author URI: http://ecolosites.eelv.fr
License: GPLv2
Text Domain: eelv_lettreinfo
Domain Path: /languages/
*/


$eelv_newsletter=new EELV_newsletter();
class EELV_newsletter{

	// ID for DB version
	// Beeing updated each time the SQL structure changes
	var $eelv_newsletter_version;
	var $js_version;
	var $installed_ver;
	// Current version of the options version
	// Beeing updated each time the configuration page changes
	var $eelv_newsletter_options_version;

	var $newsletter_tb_name;
	var $newsletter_plugin_url;
	var $newsletter_base_url;
	var $lettreinfo_plugin_path;
	var $eelv_nl_content_themes;
	var $eelv_nl_default_themes;
	var $default_item_style;
	var $default_item_style_trads;
	var $newsletter_sql;
        var $mime_type;

	var $news_reg_return;

        var $eol;


	//Initialize the plugin
	function EELV_newsletter(){
	  global $wpdb;

	  //Plugin translation
	  load_plugin_textdomain( 'eelv_lettreinfo', false, 'eelv-newsletter/languages' );
	  $plugin_description=__('Add a registration form on frontOffice, a newsletter manager on BackOffice','eelv_lettreinfo');
	  $plugin_name=__('EELV Newsletter','eelv_lettreinfo');

  	  $this->news_reg_return='';
	  $this->eelv_newsletter_version = '2.6.5';
	  $this->js_version='2.7.1';
	  $this->installed_ver = get_option( "eelv_newsletter_version" );
	  $this->eelv_newsletter_options_version = 5;
	  $this->newsletter_tb_name = 'eelv_'.$wpdb->blogid. '_newsletter_adr';
	  $this->newsletter_plugin_url = plugins_url();
	  $this->newsletter_base_url = plugins_url('/', __FILE__);
	  $this->lettreinfo_plugin_path=plugin_dir_path(__FILE__);
	  $this->eelv_nl_content_themes=array();
	  $this->eelv_nl_default_themes=array();
          $this->eol=get_option( 'newsletter_eol' )=='n'?"\n":"\r\n";
	  $this->newsletter_sql = "CREATE TABLE " . $this->newsletter_tb_name . " (
	    `id` mediumint(9) NOT NULL AUTO_INCREMENT,
	    `parent` mediumint(9) DEFAULT 0 NOT NULL,
	    `nom` VARCHAR(255) DEFAULT '' NOT NULL,
	    `email` VARCHAR(255) DEFAULT '' NOT NULL,
	    PRIMARY KEY  (`id`)
	    );";

          $this->mime_type=get_option( 'newsletter_mime_type' );
          if($this->mime_type==''){
              $this->mime_type='html_txt';
          }

	  include_once ($this->lettreinfo_plugin_path.'widget.php');
	  include_once ($this->lettreinfo_plugin_path.'reply.php');

	  $this->default_item_style=array(
	  						  't_size'=>'thumbnail',
						      'div'=>'width:550px; margin:5px 0px;text-align:left; clear:both; border-top:#CCC 1px dotted; padding-top:1em; margin-top:1em;',
						      'a'=>'text-decoration:none;color:#666666;',
						      'img'=>'float:left; margin-right:10px;',
						      'h3' => 'margin:0px !important;text-decoration:none;color:#000000;',
						      'p' => '',
						      'readmore'=>'',
						      'readmore_content'=>'',
						      'excerpt_length'=>300
					     );

		$this->default_item_style_trads=array(
							't_size'=>__('Thumbnail size','eelv_lettreinfo'),
							'div'=>__('Div style:','eelv_lettreinfo'),
							'a'=>__('A style:','eelv_lettreinfo'),
							'img'=>__('Img style:','eelv_lettreinfo'),
							'h3'=>__('H3 style:','eelv_lettreinfo'),
							'p'=>__('P style:','eelv_lettreinfo'),
							'readmore'=>__('Readmore style:','eelv_lettreinfo'),
							'readmore_content'=>__('Readmore content:','eelv_lettreinfo'),
							'excerpt_length'=>__('Excerpt length: (characters)','eelv_lettreinfo')
						);

	  // Hooks into WordPress
	    register_activation_hook(__FILE__,array(&$this,'eelvnewsletter_install'));
		add_action('plugins_loaded', array(&$this,'eelvnewsletter_update_db_check'));
		add_action( 'init', array(&$this,'newsletter_BO' ));
		add_action( 'admin_init', array(&$this,'newsletter_autosend' ));

		add_action( 'save_post', array(&$this,'newsletter_save_postdata' ));
		add_action( 'add_meta_boxes', array(&$this,'newsletter_add_custom_box' ));

		add_action('admin_menu', array(&$this,'eelv_news_ajout_menu'));
		add_action( 'network_admin_menu', array(&$this,'eelv_news_ajout_network_menu'));

		add_action('wp_head', array(&$this,'inscform_action'));
		add_action('admin_head',array(&$this,'eelv_newsletter_addAlert'));
		add_action('wp_enqueue_scripts', array(&$this,'eelv_news_scripts'));
		add_action('admin_enqueue_scripts', array(&$this,'eelv_news_adminscripts'));

		add_filter( 'archive_template', array(&$this,'eelv_lettrinfo_get_custom_archive_template' ));
		add_filter( 'single_template', array(&$this,'eelv_lettrinfo_get_custom_single_template' ));

		add_filter('manage_newsletter_posts_columns', array(&$this,'lettreinfo_columns_head'));
		add_action('manage_newsletter_posts_custom_column', array(&$this,'lettreinfo_columns_content'), 10, 2);
		add_filter('manage_newsletter_archive_posts_columns', array(&$this,'lettreinfo_archives_columns_head'));
		add_action('manage_newsletter_archive_posts_custom_column', array(&$this,'lettreinfo_archives_columns_content'), 10, 2);

                add_action('admin_post_newsletter_export_address_csv', array( &$this, 'export_address_csv'));

		add_shortcode( 'eelv_news_form' , array(&$this,'get_news_large_form' ));
	  	add_shortcode( 'desinsc_url' , array(&$this,'nl_short_desinsc' ));
  		add_shortcode( 'nl_date' , array(&$this,'nl_short_date' ));


		add_action('widgets_init', array(&$this,'register_widget'));

		add_filter('tiny_mce_before_init', array(&$this,'myformatTinyMCE') );

	}



  //WP init function
  function newsletter_BO(){
    global $wpdb;
    register_post_type('newsletter', array(  'label' => 'Newsletter','description' => '','public' => false,'show_ui' => true,'show_in_menu' => true,'capability_type' => 'post','hierarchical' => false,'rewrite' => array('slug' => ''),'query_var' => true,'has_archive' => true,'supports' => array('title','editor','author'),'labels' => array (
      'name' => __("Newsletter",'eelv_lettreinfo'),
      'singular_name' => __("Newsletter",'eelv_lettreinfo'),
      'menu_name' => __("Newsletter",'eelv_lettreinfo'),
      'add_new' => __('add','eelv_lettreinfo'),
      'add_new_item' => __('Add','eelv_lettreinfo'),
      'edit' => __('Edit','eelv_lettreinfo'),
      'edit_item' => __('Edit','eelv_lettreinfo'),
      'new_item' => __('New','eelv_lettreinfo'),
      'view' => __('View','eelv_lettreinfo'),
      'view_item' => __('View newsletter','eelv_lettreinfo'),
      'search_items' => __('Search','eelv_lettreinfo'),
      'not_found' => __('No newsletter Found','eelv_lettreinfo'),
      'not_found_in_trash' => __('No newsletter Found in Trash','eelv_lettreinfo'),
      'parent' => __('Parent newsletter','eelv_lettreinfo'),
    ),) );
    register_post_type(
      'newsletter_template', array(  'label' => 'Mod&egrave;les','description' => '','public' => false,'show_ui' => true,'show_in_menu' => false,'capability_type' => 'post','hierarchical' => false,'rewrite' => array('slug' => ''),'query_var' => true,'has_archive' => true,'supports' => array('title','editor','revisions'),'show_in_menu' => 'edit.php?post_type=newsletter','labels' => array (
        'name' => __('Skin','eelv_lettreinfo'),
        'singular_name' => __('Skin','eelv_lettreinfo'),
        'menu_name' => __('Skins','eelv_lettreinfo'),
        'add_new_item' => __('Add','eelv_lettreinfo'),
        'edit' => __('Edit','eelv_lettreinfo'),
        'edit_item' => __('Edit','eelv_lettreinfo'),
        'new_item' => __('New','eelv_lettreinfo'),
        'view' => __('View','eelv_lettreinfo'),
        'view_item' => __('View','eelv_lettreinfo'),
        'search_items' => __('Search','eelv_lettreinfo'),
        'not_found' => __('No template Found','eelv_lettreinfo'),
        'not_found_in_trash' => __('No template Found in Trash','eelv_lettreinfo'),
        'parent' => __('Parent template','eelv_lettreinfo'),
      ),) );
    register_post_type('newsletter_archive', array(  'label' => 'Archives','description' => '','public' => true,'show_ui' => true,'show_in_menu' => false,'capability_type' => 'post','hierarchical' => false,'rewrite' => array('slug' => ''),'query_var' => true,'has_archive' => true,'supports' => array('title','editor'),'show_in_menu' => 'edit.php?post_type=newsletter','labels' => array (
      'name' => __('Archives','eelv_lettreinfo'),
      'singular_name' => __('Archive','eelv_lettreinfo'),
      'menu_name' => __('Archives','eelv_lettreinfo'),
      'add_new_item' => __('Add','eelv_lettreinfo'),
      'edit' => __('Edit','eelv_lettreinfo'),
      'edit_item' => __('Edit archive','eelv_lettreinfo'),
      'new_item' => __('New archive','eelv_lettreinfo'),
      'view' => __('View','eelv_lettreinfo'),
      'view_item' => __('Preview archive','eelv_lettreinfo'),
      'search_items' => __('Search an archive','eelv_lettreinfo'),
      'not_found' => __('No entry has been made','eelv_lettreinfo'),
      'not_found_in_trash' => __('No archive Found in Trash','eelv_lettreinfo'),
      'parent' => __('Parent archive','eelv_lettreinfo'),
    ),) );

  register_taxonomy('newsletter_archives_types',array('newsletter','newsletter_archive'),array(
    'hierarchical' => true,
    'show_ui' => true,
    'query_var' => true,
    'public'=>true,
    'show_in_nav_menus'=>true,
    'show_admin_column'=>true,
    'rewrite' => array( 'slug' => 'newsletter_archive' ),
    'labels' => array(
	    'name' => __('News types','eelv_lettreinfo'),
	    'singular_name' => __('News type','eelv_lettreinfo'),
	    'search_items' =>  __('Search news type','eelv_lettreinfo'),
	    'all_items' => __('All news types','eelv_lettreinfo'),
	    'parent_item' => __('News type parent','eelv_lettreinfo'),
	    'parent_item_colon' => __('news type parent','eelv_lettreinfo'),
	    'edit_item' => __('Edit news type','eelv_lettreinfo'),
	    'update_item' => __('Update news type','eelv_lettreinfo'),
	    'add_new_item' => __('Add new news type','eelv_lettreinfo'),
	    'new_item_name' => __('New news type','eelv_lettreinfo'),
	    'menu_name' => __('News types','eelv_lettreinfo'),
	  )
  ));

    require_once($this->lettreinfo_plugin_path.'templates.php');


	// UPDATE PLUGIN

	  if( $this->installed_ver != $this->eelv_newsletter_version ) {
	    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	    dbDelta($this->newsletter_sql);
	    update_option( 'eelv_newsletter_version', $this->eelv_newsletter_version );
	  }
	  if(false===$wpdb->query('SELECT `id` FROM '.$this->newsletter_tb_name.' LIMIT 0,1') ){
	    $this->eelvnewsletter_install();
	  }


	 wp_register_sidebar_widget(
	  'widget_eelv_lettreinfo_insc',        // your unique widget id
	  __('Subscribe newsletter','eelv_lettreinfo'),          // widget name
	  array(&$this,'widget_eelv_lettreinfo_side'),  // callback function
	  array(                  // options
		'description' => __('Form / unsubscribe and archives NewsLetter','eelv_lettreinfo')
	  )
	);
	 wp_register_widget_control('widget_eelv_lettreinfo_insc', __('Subscribe newsletter','eelv_lettreinfo'),array(&$this,'widget_eelv_lettreinfo_insc_control'));

  }

	/* INSTALLATION DES TABLES  */
	  function eelvnewsletter_install() {
	    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	    dbDelta($this->newsletter_sql);
	    add_option('eelv_newsletter_version', $this->eelv_newsletter_version);
	  }

	  // WP 3.1 patch upgrade
	  function eelvnewsletter_update_db_check() {
	    if (get_option('eelv_newsletter_version') != $this->eelv_newsletter_version) {
	      update_option( 'eelv_newsletter_version', $this->eelv_newsletter_version );
	      $this->eelvnewsletter_install();
	    }
	  }



  // CSS & JS scripts
  //Admin side
   function eelv_news_adminscripts(){
	  wp_enqueue_style('eelv_newsletter',plugins_url( 'admin.css' , __FILE__ ));
	  wp_enqueue_script('eelv_newsletter_admin',plugins_url( 'admin.js' , __FILE__ ),'jquery',$this->js_version,true);
	  wp_localize_script('eelv_newsletter_admin', 'eelv_newsletter', array(
			'url' => $this->newsletter_base_url,
			// Tracking
			'read_on' => str_replace('\'','\\\'',__('Read on :','eelv_lettreinfo')),
			'on'=>str_replace('\'','\\\'',__('On :','eelv_lettreinfo')),
			'from_ip'=>str_replace('\'','\\\'',__('From IP address :','eelv_lettreinfo')),
			'unread'=>str_replace('\'','\\\'',__('Unread','eelv_lettreinfo')),
			//Address book
			'move_contacts_to'=>__('Do you really want to move selected contacts to :', 'eelv_lettreinfo' ),
			'remove_contacts'=>__('Do you really want to remove selected contacts ?', 'eelv_lettreinfo' ),
			// Editing
			'load_default_content'=>__('Do you want to load this skin\'s default content ?\n\nWarning!\n\nYou will loose the current content.', 'eelv_lettreinfo' ),
		));
		if ( 'newsletter_archive' == get_post_type() )
      	wp_dequeue_script( 'autosave' );
  	}
   //Front side
    function eelv_news_scripts() {
      wp_enqueue_style('eelv_newsletter',plugins_url( 'newsletter.css' , __FILE__ ));
	  wp_enqueue_script('jquery');
	  wp_enqueue_script('eelv_newsletter',plugins_url( 'newsletter.js' , __FILE__ ),'jquery',$this->eelv_newsletter_version,true);
	}



	/* Colums management
	 *
	 *
	 */
  // ADD NEW COLUMN
  function lettreinfo_columns_head($defaults) {
    $defaults['envoyer'] = __('Send','eelv_lettreinfo');
    return $defaults;
  }
  // COLUMN CONTENT
  function lettreinfo_columns_content($column_name, $post_ID) {
  	if ($column_name == 'envoyer') {
      $my_temp=get_post(get_post_meta($post_ID, 'nl_template',true));
      if(0!==get_the_ID() && ''!=get_the_title() && ''!=get_the_content()  && $my_temp){
        echo $my_temp->post_title;
        echo '<br/><a href="edit.php?post_type=newsletter&page=news_envoi&post='.get_the_ID().'">'.__('Preview and send','eelv_lettreinfo').'</a>';
      }
      else{
        echo __('Not ready yet...','eelv_lettreinfo');
      }
    }
  }
  // ADD NEW COLUMN (ARCHIVES)
  function lettreinfo_archives_columns_head($defaults) {
    $defaults['queue'] = __('Queue','eelv_lettreinfo');
    $defaults['sent'] = __('Sent','eelv_lettreinfo');
    $defaults['read'] = __('Readen','eelv_lettreinfo');
    $defaults['answers'] = __('Answers','eelv_lettreinfo');
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
		  if($tot>0){
		  	 echo $lus.' ('.round($lus/$tot*100).'%)';
		  }
		  else{
		  	echo '-';
		  }
	  }
	  else{
		 _e('deactivated','eelv_lettreinfo');
	  }
    }
	if ($column_name == 'answers') {
	  $nl =  get_post($post_ID);
	  $content=$nl->post_content;
	  if(strpos($content,'[nl_reply_link')>-1){
	  	 $answers = get_post_meta($post_ID,'eelv_nl_reply');
	  	 echo sizeof($answers);
	  }
	  else{
	  	echo __( "No question", 'eelv_lettreinfo' );
	  }
    }
  }


  /* Adds a box to the main column on the Post and Page edit screens */
  function newsletter_add_custom_box() {

	//Native posts & Custom post Newsletter edit interface
    add_meta_box(
      'news-carnet-adresse',
      __( "Edit tools", 'eelv_lettreinfo' ),
      array(&$this,'newsletter_admin'),
      'newsletter'
    );
    add_meta_box(
      'newsletter_admin_wizard',
      __( "Insert a question", 'eelv_lettreinfo' ),
      array(&$this,'newsletter_admin_wizard'),
      'newsletter'
    );
    add_meta_box(
      'news-envoi-edit',
      __( "Send", 'eelv_lettreinfo' ),
      array(&$this,'newsletter_admin_prev'),
      'newsletter',
      'side'
    );
    add_meta_box(
      'news-convert-post',
      __( "Send as newsletter", 'eelv_lettreinfo' ),
      array(&$this,'news_transform'),
      'post',
      'side'
    );

	//Custom post Newsletter Archive edit interface
    add_meta_box(
      'news-archive_viewer',
      __( "Preview", 'eelv_lettreinfo' ),
     array(&$this, 'newsletter_archive_admin'),
      'newsletter_archive'
    );
    add_meta_box(
      'news-archive_viewerdest',
      __( "Recipients", 'eelv_lettreinfo' ),
      array(&$this,'newsletter_archive_admin_dest'),
      'newsletter_archive'
    );
    add_meta_box(
      'news-archive_viewerqueue',
      __( "Queue", 'eelv_lettreinfo' ),
     array(&$this, 'newsletter_archive_admin_queue'),
      'newsletter_archive'
    );
    add_meta_box(
      'news-archive_answers',
      __( "Answers", 'eelv_lettreinfo' ),
      array(&$this,'newsletter_archive_admin_answers'),
      'newsletter_archive'
    );

	//Custom post Newsletter Skin edit interface
	add_meta_box(
      'news-skin_content',
      __( "Default content", 'eelv_lettreinfo' ),
      array(&$this,'newsletter_template_default_content'),
      'newsletter_template'
    );
	add_meta_box(
      'news-skin_style',
      __( "Item style", 'eelv_lettreinfo' ),
      array(&$this,'newsletter_template_item_style'),
      'newsletter_template'
    );
  }



 // Ajout du menu et sous menu
  function eelv_news_ajout_menu() {
    add_submenu_page('edit.php?post_type=newsletter', __('Address book', 'eelv_lettreinfo' ), __('Address book', 'eelv_lettreinfo' ), 'publish_posts', 'news_carnet_adresse', array(&$this,'news_carnet_adresse'));
    add_submenu_page('edit.php?post_type=newsletter', __('Send', 'eelv_lettreinfo' ), __('Send', 'eelv_lettreinfo' ), 'publish_posts', 'news_envoi', array(&$this,'news_envoi'));
    add_submenu_page('edit.php?post_type=newsletter', __('Configuration/help', 'eelv_lettreinfo' ), __('Configuration/help', 'eelv_lettreinfo' ), 'manage_options', 'newsletter_page_configuration', array(&$this,'newsletter_page_configuration'));
    add_submenu_page('edit.php?post_type=newsletter', __('Reload parameters', 'eelv_lettreinfo' ), __('Reload parameters', 'eelv_lettreinfo' ), 'manage_options', 'newsletter_checkdb', array(&$this,'newsletter_checkdb'));
  }
  // Network side
  function eelv_news_ajout_network_menu() {
    add_submenu_page('settings.php', __('Newsletter', 'eelv_lettreinfo' ), __('Newsletter', 'eelv_lettreinfo' ), 'Super Admin', 'newsletter_network_configuration', array(&$this,'newsletter_network_configuration'));
  }





	/*
	 * Usefull functions
	 *
	 *
	 */
  //Get informations from address book table
  function get_news_meta($id){
    global $wpdb;
    $ret =  $wpdb->get_results("SELECT * FROM `$this->newsletter_tb_name` WHERE `id`='$id'");
    if(is_array($ret) && sizeof($ret)>0){
      return $ret[0];
    }
    return false;
  }



  // Make plain text version from an HTML newsletter
  function nl_plain_txt($str){
        $str = preg_replace('/<\s*style.+?<\s*\/\s*style.*?>/si', '', $str);
        $str = str_replace(array('&nbsp;','&laquo;','&raquo;','&rsquo;'),array(' ','"','"',"'"),$str);
  	$str = strip_tags($str,'<a>');
	$str=str_replace('</a>','</a>'."\n",$str);
	$str=str_replace('&#038;','&',$str);
		preg_match_all('/<a (.+)?href=[\'"](.*)[\'"][.+]?>(.*)<\/a>/',$str,$links);
		if(is_array($links)){
			foreach($links[0] as $id=>$link){
				$lien  = strip_tags($links[2][$id]);
				if(-1 < $p = strpos($lien,'"')) $lien  = substr($lien,0,$p);
				if(-1 < $p = strpos($lien,'\'')) $lien  = substr($lien,0,$p);
				$lien  = str_replace(' ','%20',$lien);
				$str=str_replace($link,$links[3][$id].' : '.$lien.' ',$str);
			}
		}
		$str=str_replace("\n\n\n","\n\n",$str);
		return $str;
	}


  //Construct a multipart body content
	function nl_mime_txt($str,$boundary=''){
		$eol=$this->eol;
		$message = '';
                if($this->mime_type=='html_txt'){
                    $message .= '--'.$boundary.$eol;
                    $message .= 'Content-Type: text/plain; charset=UTF-8'.$eol;
                    $message .= 'Content-Transfer-Encoding: quoted-printable'.$eol.$eol;
                    $message .= quoted_printable_encode ($this->nl_plain_txt($str)).$eol.$eol;

                    $message .= '--'.$boundary.$eol;
                    $message .= 'Content-Type: text/html; charset=UTF-8'.$eol;
                    $message .= 'Content-Transfer-Encoding: quoted-printable'.$eol.$eol;
                    $message .= quoted_printable_encode ('<html><body>'.$str.'</body></html>').$eol.$eol;
                    $message .= '--'.$boundary.'--'.$eol;
                }
                else{
                    $message .= ('<html><body>'.$str.'</body></html>').$eol.$eol;
                }
		return $message;
	}
	function myformatTinyMCE($in)	{
		if(get_post_type()=='newsletter' || get_post_type()=='newsletter_archive' || get_post_type()=='newsletter_template'){
			$in['wpautop']=false;
		}
		return $in;
	}
	 function nl_content($post_id,$type='newsletter'){
	    $nl =  get_post($post_id);
		if(is_object($nl)){
		    $content=$nl->post_content;
                    $content = preg_replace('/<\s*style.+?<\s*\/\s*style.*?>/si', '', $content);
                    $content = str_replace(array('&nbsp;','&laquo;','&raquo;','&rsquo;'),array(' ','"','"',"'"),$content);
		    $template =  get_post(get_post_meta($post_id,'nl_template',true));
		    if($template){
		      $content= str_replace('[newsletter]',$content,$template->post_content);
		    }
		    remove_filter('the_content', 'wpautop');
		    $return  = apply_filters('the_content',$content);
                    add_filter('the_content', 'wpautop');
                    if($return=='' && $content!=''){
                            $return=$content;
                    }
                    $desinsc_url = get_option( 'newsletter_desinsc_url' );
                    $return= str_replace('[nl_date]',date_i18n(get_option('date_format')),$return);
                    $return=str_replace('[desinsc_url]',"<a href='".$desinsc_url."' target='_blank' class='nl_a'>".$desinsc_url."</a>",$return);
		    return $return;
		}
		return '';
	  }
	  function eelv_newsletter_sharelinks($title,$link){
			return "<div style='margin:0px;text-align:left; clear:both;font-size:9px; '><span style='display:block;float:left;padding:2px;padding-left:10px;padding-right:10px;background:#888;color:#FFF;'>".__('Share on : ', 'eelv_lettreinfo' )."</span><a href='http://www.facebook.com/sharer.php?u=".urlencode($link)."&t=".$title."' target='_blank' style='display:block;float:left;padding:2px;padding-left:10px;padding-right:10px;background:#3B5998;color:#FFF;'>Facebook</a><a href='https://twitter.com/intent/tweet?text=".$title."%20".urlencode($link)."' target='_blank' style='display:block;float:left;padding:2px;padding-left:10px;padding-right:10px;background:#2BB7EA;color:#FFF;'>Twitter</a><a href='https://plus.google.com/share?url=".urlencode($link)."' target='_blank' style='display:block;float:left;padding:2px;padding-left:10px;padding-right:10px;background:#DB4B39;color:#FFF;'>Google+</a><a href='http://www.linkedin.com/shareArticle?mini=true&url=".urlencode($link)."&title=".$title."' target='_blank' style='display:block;float:left;padding:2px;padding-left:10px;padding-right:10px;background:#0073B2;color:#FFF;'>Linked in</a></div>&nbsp;\n";
	 }



	//Shortcodes
	 function nl_short_date(){
	  	return date_i18n(get_option('date_format'));
	  }

	  function nl_short_desinsc(){
	    $desinsc_url = get_option( 'newsletter_desinsc_url' );
	  	return '<a href="'.$desinsc_url.'" target="_blank" class="nl_a">'.$desinsc_url.'</a>';
	  }






	/*
	 *
	 * Front stuff
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 */
	///////////////////////////////////////////// VALIDATION FORMULAIRE

	function inscform_action(){
    global $wpdb;
    $query='';
    if(isset($_POST['news_email'])){
      $email = stripslashes($_POST['news_email']);
      if (filter_var($email, FILTER_VALIDATE_EMAIL)) {


	  $msg = get_option( 'newsletter_msg' ,array(
		'sender'=>'' ,
		'suscribe_title'=>'' ,
		'suscribe'=>'' ,
		'unsuscribe_title'=>'' ,
		'unsuscribe'=>''
		));
	  $sender = $msg['sender'];
	  $suscribe_title = $msg['suscribe_title'];
	  $suscribe = $msg['suscribe'];
	  $unsuscribe_title = $msg['unsuscribe_title'];
	  $unsuscribe = $msg['unsuscribe'];
	  $suscribegrp = isset($_POST['news_grp']) && is_numeric($_POST['news_grp']) ? $_POST['news_grp'] : 1;

	  if($suscribegrp>2){
	  	  $ret =  $wpdb->get_results("SELECT * FROM `$this->newsletter_tb_name` WHERE `id`='".$suscribegrp."' AND `parent`='0'");
		  if(!is_array($ret) || sizeof($ret)==0){
		    $suscribegrp=1;
		  }
	  }




        switch($_POST['news_action']){
          case '1':
            $ret =  $wpdb->get_results("SELECT * FROM `$this->newsletter_tb_name` WHERE `email`='".str_replace("'","''",$email)."' AND `parent`='".$suscribegrp."'");
            if(is_array($ret) && sizeof($ret)>0){
              $ret = $ret[0];
              if($ret->parent==2){ // Red list
                $query="UPDATE $this->newsletter_tb_name SET `parent`='".$suscribegrp."' WHERE `email`='".str_replace("'","''",$email)."' AND `parent`='".$suscribegrp."'";
                if($query!='' && false===$wpdb->query($query)){
                  $this->news_reg_return.=__("An error occured !", 'eelv_lettreinfo') ;
                }
                elseif($query!=''){
                  $this->news_reg_return=__("You have been successfully re-registered", 'eelv_lettreinfo');
				  if(!empty($sender) && !empty($suscribe_title) && !empty($suscribe)){
				  	mail($email,$suscribe_title,$suscribe,'From:'.$sender.$this->eol,'-f '.$sender);
				  }
                }
              }
              else{
                $this->news_reg_return.=__("Your email is already registered in our mailing-list.", 'eelv_lettreinfo') ;
              }
            }
            else{
              $query="INSERT INTO $this->newsletter_tb_name (`parent`,`nom`,`email`)
                VALUES (".$suscribegrp.",'".str_replace("'","''",substr($email,0,strpos($email,'@')))."','".str_replace("'","''",$email)."')";
              if($query!='' && false==$wpdb->query($query)){
                $this->news_reg_return.=__("An error occured !", 'eelv_lettreinfo');
              }
              elseif($query!=''){
                $this->news_reg_return.=__("Thank you for your subscription", 'eelv_lettreinfo');
				if(!empty($sender) && !empty($suscribe_title) && !empty($suscribe)){
				  	mail($email,$suscribe_title,$suscribe,'From:'.$sender.$this->eol,'-f '.$sender);
				  }
              }
            }
            break;
          case '0':
            $ret =  $wpdb->get_results("SELECT * FROM `$this->newsletter_tb_name` WHERE `email`='".str_replace("'","''",$email)."'");
            if(is_array($ret) && sizeof($ret)>0){
              $query="UPDATE $this->newsletter_tb_name SET `parent`='2' WHERE `email`='".str_replace("'","''",$email)."'";
              if($query!='' && false===$wpdb->query($query)){
                $this->news_reg_return.=__("An error occured !", 'eelv_lettreinfo');
              }
              elseif($query!=''){
                $this->news_reg_return.=__("Thank you, your email have been deleted from our mailing-list", 'eelv_lettreinfo');
				  if(!empty($sender) && !empty($unsuscribe_title) && !empty($unsuscribe)){
				  	mail($email,$unsuscribe_title,$unsuscribe,'From:'.$sender.$this->eol,'-f '.$sender);
				  }
              }
            }
            else{
              $this->news_reg_return.=__("Your email does'nt appear in our mailing list. No unsubscribe needed", 'eelv_lettreinfo');
            }
            break;
         }
      }
      else{
        $this->news_reg_return.= '<b>'.htmlspecialchars(strip_tags($email)).'</b> : '.__('invalid address', 'eelv_lettreinfo');
      }
    }
  }
  ////////////////////////////////////////////////////////////////////////////////////////////////////// FRONT OFFICE
  function get_news_form($id=''){
    global $wpdb;
	$this->eelv_li_xs_archives = get_option('eelv_li_xs_archives',0);
	$this->eelv_li_xs_title= get_option('eelv_li_xs_title');
	$this->eelv_li_xs_options = get_option('eelv_li_xs_options',0);
	$this->eelv_li_xs_formclass = get_option('eelv_li_xs_formclass','');
	$this->eelv_li_xs_textclass = get_option('eelv_li_xs_textclass','');
	$this->eelv_li_xs_buttonclass = get_option('eelv_li_xs_buttonclass','');

	$this->eelv_li_xs_buttontext = get_option('eelv_li_xs_buttontext','ok');
	$this->eelv_li_xs_labeltext = get_option('eelv_li_xs_labeltext',__('Subscribe our newsletter', 'eelv_lettreinfo'));
	$this->eelv_li_xs_texttext = get_option('eelv_li_xs_texttext',__('Newsletter : your email address', 'eelv_lettreinfo'));

	if(empty($id)) $id=md5(time());
    ?>
<form action="#" method="post" id="newsform<?=$id?>" class="newsform <?php if(!empty($this->eelv_li_xs_formclass)) echo $this->eelv_li_xs_formclass; ?>" onsubmit="if(this.news_email.value=='' || this.news_email.value=='<?=addslashes($this->eelv_li_xs_texttext)?>'){ return false; }" >
  <div>
    <label class="screen-reader-text" for="news_email<?=$id?>"><?=$this->eelv_li_xs_labeltext?></label>
    <input type="text" name="news_email" id="news_email<?=$id?>" value="" placeholder="<?=$this->eelv_li_xs_texttext?>" <?php if(!empty($this->eelv_li_xs_textclass)) echo'class="'.$this->eelv_li_xs_textclass.'"'; ?>/>
    <input type="submit" value="<?=$this->eelv_li_xs_buttontext?>" <?php if(!empty($this->eelv_li_xs_buttonclass)) echo'class="'.$this->eelv_li_xs_buttonclass.'"'; ?>/>
    <?php if($this->eelv_li_xs_options==0 || $this->eelv_li_xs_archives==0){ ?>
    <div id='news_hidden_option<?=$id?>' class='news_hidden_option'>
      <?php if($this->eelv_li_xs_options==0){ ?>
	      <label for='news_option_1<?=$id?>'><input type="radio" name='news_action' value='1' id='news_option_1<?=$id?>' checked="checked"/><?=__("Subscribe", 'eelv_lettreinfo')?></label>
	      <label for='news_option_2<?=$id?>'><input type="radio" name='news_action' value='0'  id='news_option_2<?=$id?>'/> <?=__("Unsuscribe", 'eelv_lettreinfo')?></label>
      <?php } if($this->eelv_li_xs_archives==0){ ?>
      	  <p><a href="/newsletter_archive/"><?=__("Last newsletters", 'eelv_lettreinfo')?></a></p>
      <?php } ?>
    </div>
    <?php }
    if($this->eelv_li_xs_options==1){ ?>
      		<input type="hidden" name='news_action' value='1'>
      <?php } ?>
    <?php if($this->news_reg_return!=''){?>
    <div class='news_return' id='news_return<?=$id?>' onclick="document.getElementById('news_return<?=$id?>').style.display='none';">
      <?=$this->news_reg_return?>
    </div>
    <?php }  ?>
  </div>
</form>
<?php
  }

	//Inscription form
  function get_news_large_form($atts){
    global $wpdb;
	extract(shortcode_atts(array(
		      'group'=>1,
		      'subscribe'=>1,
		      'unsubscribe' => 1,
		      'archives' => 1,
		      'archives_title' => __("Last newsletters", 'eelv_lettreinfo')
	     ), $atts));
    $ret='
      <form action="#" method="post" class="newslform" onsubmit="if(this.news_email.value==\'\' || this.news_email.value==\''.__('Your email', 'eelv_lettreinfo').'\'){ return false; }">
      <fieldset>
      <input type="hidden" name="news_grp" value="'.$group.'" />
      <p>
	      <label for="news_l_email">'.__('Your email:', 'eelv_lettreinfo').'
	      	<input type="text" name="news_email" id="news_l_email" value="'.__('Your email', 'eelv_lettreinfo').'" placeholder="'.__('Your email', 'eelv_lettreinfo').'" onfocus="this.select()" />
	      </label>
      </p> ';
	  if($subscribe==1){
		  $ret.='
	      <p>
		      <label for="news_l_option_1">
		      	<input type="radio" name="news_action" value="1" id="news_l_option_1" checked="checked"/> '.__("Subscribe", 'eelv_lettreinfo').'
		      </label>
	      </p>';
      }
	  if($unsubscribe==1){
		  $ret.='
	      <p>
		      <label for="news_l_option_2">
		      	<input type="radio" name="news_action" value="0"  id="news_l_option_2"/> '.__('Unsubscribe', 'eelv_lettreinfo').'
		      </label>
	      </p>';
	  }
	  $ret.='
      <p><input type="submit" value="'.__('ok', 'eelv_lettreinfo').'" class="btn"/></p>';
    if($this->news_reg_return!='' && isset($_POST['news_grp']) && $_POST['news_grp']==$group){
      $ret.='<div class="news_retour">'.$this->news_reg_return.'</div>';
    }
	if($archives==1){
		$ret.='<p><a href="/newsletter_archive/">'.$archives_title.'</a></p>';
	}
    $ret.='
      </fieldset>
      </form>';
    return $ret;
  }








	/*
	 *
	 * Back office stuff
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 *
	 */

	////////////////////////////////////////////////////////////////////////////////////////////////////// BACK OFFICE
  function news_liste_groupes(){
    global $wpdb;
    //newsletter_checkdb();
    $querystr = "SELECT `id`,`nom` FROM `$this->newsletter_tb_name` WHERE `parent`='0' ORDER BY `nom`";
    return $wpdb->get_results($querystr);
  }
  function news_liste_contacts($groupe,$fields='`id`,`nom`,`email`'){
    global $wpdb;
    if(is_array($groupe)){
      $groupe = implode("' OR `parent`='",$groupe);
    }
    $querystr = "SELECT $fields FROM `$this->newsletter_tb_name` WHERE `parent`='$groupe' GROUP BY `email`ORDER BY `nom`";
    return $wpdb->get_results($querystr);
  }



/*****************************************************************************************************************************************
  A D R E S S E S
  *****************************************************************************************************************************************/
  function export_address_csv(){
      require$this->lettreinfo_plugin_path.'csv.php';
  }
  function news_carnet_adresse(){
    global $wpdb;
    $this->inscform_action();
    //newsletter_checkdb();
    ?>
<div class="wrap">
  <div id="icon-edit" class="icon32 icon32-posts-newsletter"><br/></div>
  <h2><?=__("Newsletter",'eelv_lettreinfo')?></h2>
  <table class="widefat" style="margin-top: 1em;">
    <thead>
      <tr>
        <th scope="col" colspan="2"><?php _e('Address book', 'eelv_lettreinfo' ) ?></th>
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
      $query="DELETE FROM $this->newsletter_tb_name WHERE `id`='$grp_id' OR `parent`='$grp_id'";
      if(false===$wpdb->query($query)){
        ?><div class="updated"><p><strong><?php _e('An error occured, no group has been deleted !', 'eelv_lettreinfo' ) ?></strong></p></div><?php
  }
  else{
  ?><div class="updated"><p><strong><?php _e('Successful deletion !', 'eelv_lettreinfo' ) ?></strong></p></div><?php
  }
  $grp_id='';
      }
      // Supression de contacts
      if(isset($_GET['delcontacts']) && isset($_GET['liste'])){
        $grp_id = $_GET['liste'];
        $MBRS = $this->news_liste_contacts($grp_id);
        if(sizeof($MBRS)>0){
          $ac = '`id`=0';
          $nb=0;
          foreach($MBRS as $contact){
            if(isset($_POST['contact_'.$contact->id])){
              $ac.=' OR `id`='.$contact->id;
              $nb++;
            }
          }
          $query="DELETE FROM $this->newsletter_tb_name WHERE $ac";
          if(false===$wpdb->query($query)){
            ?><div class="updated"><p><strong><?php _e('An error occured, no contact has been deleted !', 'eelv_lettreinfo' ) ?></strong></p></div><?php
  }
  else{
  ?><div class="updated"><p><strong><?php printf(__('%s contacts deleted !', 'eelv_lettreinfo' ),$nb) ?></strong></p></div><?php
  }
  }
  }
  // deplacement de contacts
  if(isset($_GET['ngrp']) && isset($_GET['liste'])){
  $grp_id = $_GET['liste'];
            $MBRS = $this->news_liste_contacts($grp_id);
            if(sizeof($MBRS)>0){
              $ac = '`id`=0';
              $nb=0;
              foreach($MBRS as $contact){
                if(isset($_POST['contact_'.$contact->id])){
                  $ac.=' OR `id`='.$contact->id;
                  $nb++;
                }
              }
              $query="UPDATE $this->newsletter_tb_name SET `parent`='".str_replace("'","''",$_GET['ngrp'])."' WHERE $ac";
              if(false===$wpdb->query($query)){
                ?><div class="updated"><p><strong><?php _e('An error occured, no contact has been moved !', 'eelv_lettreinfo' ) ?></strong></p></div><?php
  }
  else{
  ?><div class="updated"><p><strong><?php printf(__('%s contacts succesfully moved !', 'eelv_lettreinfo' ),$nb) ?></strong></p></div><?php
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
                  $query="UPDATE $this->newsletter_tb_name SET `nom`='".str_replace("'","''",$grp_nom)."' WHERE `id`='$grp_id'";
                }
                else{
                  $query="INSERT INTO $this->newsletter_tb_name (`nom`) VALUES ('".str_replace("'","''",$grp_nom)."')";
                }
                if(false===$wpdb->query($query)){
                  ?><div class="updated"><p><strong><?php _e('An error occured...', 'eelv_lettreinfo' ) ?></strong></p></div><?php
  }
  else{
  $for='liste';
                  ?><div class="updated"><p><strong><?php _e('Record saved', 'eelv_lettreinfo' ); ?></strong></p></div><?php
                }
              }
              if(isset($_POST['con_nom']) && is_numeric($grp_id)  ){
                $con_nom = stripslashes($_POST['con_nom']);
                $con_email = stripslashes($_POST['con_email']);
                if(is_numeric($con_id)){
                  $query="UPDATE $this->newsletter_tb_name SET `nom`='".str_replace("'","''",$con_nom)."',`email`='".str_replace("'","''",$con_email)."' WHERE `id`='$con_id'";
                }
                else{
                  switch($_POST['import_type']){
                    case 'unite':
                      $query="INSERT INTO ".$this->newsletter_tb_name." (`parent`,`nom`,`email`)
                        VALUES ('$grp_id','".str_replace("'","''",$con_nom)."','".str_replace("'","''",$con_email)."')";
                      break;
                    case 'masse':
                      $imp = preg_split('/[,;\n\t]/',stripslashes($_POST['con_mul'].','));
                      $query='INSERT INTO '.$this->newsletter_tb_name.' (`parent`,`nom`,`email`) VALUES ';
                      foreach($imp as $entry){
                        $entry=trim($entry);
                        if (filter_var($entry, FILTER_VALIDATE_EMAIL)) {
                          $query.="
                            ('$grp_id','".str_replace("'","''",substr($entry,0,strpos($entry,'@')))."','".str_replace("'","''",$entry)."'),";
                        }
                        elseif($entry!=''){
                          echo'<p>'.htmlentities(strip_tags($entry)).' : '.__('invalid address', 'eelv_lettreinfo').'</p>';
                        }
                      }
                      $query = substr($query,0,-1);
                      $query.="";
                      break;
                    case 'file':
                      ?><div class="updated"><p><strong><?php _e('Functionality under development', 'eelv_lettreinfo' ) ?></strong></p></div><?php
  break;
                                              }
                }
                if($query!='' && false===$wpdb->query($query)){
                  ?><div class="updated"><p><strong><?php _e('An error occured !', 'eelv_lettreinfo' ) ?></strong></p></div><?php
  }
  elseif($query!=''){
  ?><div class="updated"><p><strong><?php _e('Record saved', 'eelv_lettreinfo' ); ?></strong></p></div><?php
                }
                $for2='liste';
                $for ='liste';
              }
              // Edition de groupe
              if($for=='groupe'){
                $grp_nom = __('New group', 'eelv_lettreinfo');
                $action="edit.php?post_type=newsletter&page=news_carnet_adresse&groupe=new";
                if(is_numeric($grp_id)){
                  $news_info = $this->get_news_meta($grp_id);
                  $grp_nom = $news_info->nom;
                  $action="edit.php?post_type=newsletter&page=news_carnet_adresse&groupe=$grp_id";
                }
                ?>
          <?php _e('Edit group', 'eelv_lettreinfo' ) ?>
          <form action='<?=$action;?>' method="post">
            <div id="titlediv">
              <div id="titlewrap">
                <input type="text" name="grp_nom" size="30" tabindex="1" value="<?=$grp_nom;?>" id="title" autocomplete="off"/>
              </div>
              <input type='submit' value='<?php _e('Save options', 'eelv_lettreinfo' ) ?>' class="button-primary"/>
            </div>
          </form>
          <p>    <a href="edit.php?post_type=newsletter&page=news_carnet_adresse" class="button add-new-h2"><?php _e('cancel', 'eelv_lettreinfo' ) ?></a></p>
          <?php
  }
  if($for=='liste'){
  ////////////////////////////////////////////////////////////Listes
  if(!is_numeric($grp_id)){ // groupes
  $GRPS = $this->news_liste_groupes();
                $nb_groups = sizeof($GRPS);
                ?><h3 class="sectiontitle title3"><?php _e('Groups', 'eelv_lettreinfo' ) ?></h3>  <a href="edit.php?post_type=newsletter&page=news_carnet_adresse&groupe=new" class="button add-new-h2"><?php _e('New group', 'eelv_lettreinfo' ) ?></a>    <?php
  if($nb_groups>0){?>
          <table class='eelv_news_groups widefat'>
          	<thead>
          		<tr>
          			<th><?php _e('ID','eelv_lettreinfo'); ?></th>
          			<th><?php _e('Name','eelv_lettreinfo'); ?></th>
          			<th><?php _e('Subscribers','eelv_lettreinfo'); ?></th>
          			<th><?php _e('Subscription form','eelv_lettreinfo'); ?></th>
          			<th><?php _e('List','eelv_lettreinfo'); ?></th>
          			<th><?php _e('Rename','eelv_lettreinfo'); ?></th>
          			<th><?php _e('Delete','eelv_lettreinfo'); ?></th>
          		</tr>
          	</thead>
          	<tbody>
            <?php
  $coup=false;
                foreach($GRPS as $groupe){
                  $nbinsc = sizeof($this->news_liste_contacts($groupe->id));
                  ?>
            <tr>
              <td><?=$groupe->id?></td>
              <td><a href='edit.php?post_type=newsletter&page=news_carnet_adresse&liste=<?=$groupe->id?>'><b><?=$groupe->nom?></b></a></td>
              <td><b><?=$nbinsc?></b></td>
              <td>[eelv_news_form groupe=<?=$groupe->id?>]</td>
              <td><a href='edit.php?post_type=newsletter&page=news_carnet_adresse&liste=<?=$groupe->id?>' class="button"><span class="dashicons list"></span> <?php _e('List', 'eelv_lettreinfo' ) ?></a></td>
              <td><a href='admin-post.php?action=newsletter_export_address_csv&grp_id=<?=$groupe->id?>' class="button" target="_blank"><span class="dashicons list"></span> <?php _e('Export', 'eelv_lettreinfo' ) ?></a></td>
              <td><a href='edit.php?post_type=newsletter&page=news_carnet_adresse&groupe=<?=$groupe->id?>' class="button"><span class="dashicons edit"></span> <?php _e('Rename', 'eelv_lettreinfo' ) ?></a></td>
              <td><a onclick="confsup('edit.php?post_type=newsletter&page=news_carnet_adresse&delgroupe=<?=$groupe->id?>',1)" class="button"><span class="dashicons trash"></span> <?php _e('Delete', 'eelv_lettreinfo' ) ?></a></td>
            </tr>
            <?php }  ?>
          </tbody> </table>
          <?php
  }
  ?><p>&nbsp;</p><?php
  }
  else{ // contacts
  $news_info = $this->get_news_meta($grp_id);
                  $grp_nom = $news_info->nom;
                  $MBRS = $this->news_liste_contacts($grp_id);
                  $nb_contacts = sizeof($MBRS);
                  ?>
          <h3 class="sectiontitle title3"><a href='edit.php?post_type=newsletter&page=news_carnet_adresse'><?php _e('Groups', 'eelv_lettreinfo' ) ?></a> > <?=$grp_nom?></h3>
          <?php
  if($for2=='liste'){ // liste contact
  ?>
          <table class='eelv_news_groups widefat'>
          	<thead>
          	<tr><th>
            <input type="checkbox" onclick="tout(document.getElementById('liste_mel'),this)"/>
            <select onchange="eval(this.value)">
              <option value=""><?php _e('Bulk actions', 'eelv_lettreinfo' ) ?></option>
              <?php
  $GRPS = $this->news_liste_groupes();
                  $nb_groups = sizeof($GRPS);
                  if($nb_groups>0){
                    foreach($GRPS as $groupe){
                      if($groupe->id !=$grp_id){
                        $nbinsc = sizeof($this->news_liste_contacts($groupe->id));
                        ?>
              <option value="changegrp(document.getElementById('liste_mel'),'edit.php?post_type=newsletter&page=news_carnet_adresse&liste=<?=$grp_id?>&ngrp=<?=$groupe->id?>','<?=$groupe->nom?>')"><?php _e('Move to : ', 'eelv_lettreinfo' ) ?> <?=$groupe->nom?></option>
              <?php } } }  ?>
              <option value="confsup(document.getElementById('liste_mel'),2)"><?php _e('Delete', 'eelv_lettreinfo' ) ?></option>
            </select>
            <a href='edit.php?post_type=newsletter&page=news_carnet_adresse' class="button"><?php _e('Back', 'eelv_lettreinfo' ) ?></a>
            <a href='edit.php?post_type=newsletter&page=news_carnet_adresse&liste=<?=$grp_id?>&contact=new' class="button-primary"><?php _e('New recipient', 'eelv_lettreinfo' ) ?></a>

            </th></tr></thead></table>
          <?php
  if($nb_contacts>0){?>
          <form id='liste_mel' action="edit.php?post_type=newsletter&page=news_carnet_adresse&liste=<?=$grp_id?>&delcontacts" method="post">
            <table class='eelv_news_groups widefat'> <tbody>
              <?php
  $coup=false;
                        foreach($MBRS as $contact){ ?>
              <tr>
                <td><input type="checkbox" name="contact_<?=$contact->id?>" value="1" /></td>
                <td><a href='edit.php?post_type=newsletter&page=news_carnet_adresse&liste=<?=$grp_id?>&contact=<?=$contact->id?>'><b><?=$contact->nom?></b></a></td>                         <td><a href='edit.php?post_type=newsletter&page=news_carnet_adresse&liste=<?=$grp_id?>&contact=<?=$contact->id?>'><b><?=$contact->email?></b></a></td>
              </tr>
              <?php }  ?>
            </tbody></table>
          </form>
          <?php
                      }
                      else{
                         _e('No recipient have been selected', 'eelv_lettreinfo' );
                      }
                      ?>
          <?php
                    }
                    else{ // edit contact
                      $con_nom = '';
                      $action="edit.php?post_type=newsletter&page=news_carnet_adresse&liste=$grp_id&contact=new";
                      if(is_numeric($con_id)){
                        $news_info = $this->get_news_meta($con_id);
                        $con_nom = $news_info->nom;
                        $con_email = $news_info->email;
                        $action="edit.php?post_type=newsletter&page=news_carnet_adresse&groupe=$grp_id&contact=$con_id";
                      }
                      ?>
          <form action='<?=$action;?>' method="post" enctype="multipart/form-data">
            <ul>
              <li>
              	<label>
                  <h3><input type="radio" name="import_type" value='unite' id='imp_unite' checked="checked" /> <?php _e('Edit contact', 'eelv_lettreinfo' ) ?></h3>
                 </label>
                  <label><?php _e('Name', 'eelv_lettreinfo' ) ?>
                  <input type="text" name="con_nom" size="30" value="<?=$con_nom;?>" id="con_nom" autocomplete="off" onfocus="import_type[0].checked=true"/>
                  </label>
                  <label><?php _e('E-mail', 'eelv_lettreinfo' ) ?>
                  <input type="email" name="con_email" size="30" value="<?=$con_email;?>" id="con_email" autocomplete="off" onfocus="import_type[0].checked=true"/>
                </label>
              </li>
              <?php if(!is_numeric($con_id)){ ?>
              <li>
                <label for="imp_masse">
                  <h3><input type="radio" name="import_type" value='masse' id='imp_masse' /> <?php _e('Mass copy', 'eelv_lettreinfo' ) ?></h3>
                 </label>
                 <label>
                 <p><?php _e('Return separated email address', 'eelv_lettreinfo' ) ?></p>
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

        <?php if(isset($grp_id) && $grp_id>0): ?>
        <p>&nbsp;</p>
        <table class="widefat">
        	<thead>
        		<tr><th><?php _e('Subscription form for this address book', 'eelv_lettreinfo' ) ?></th></tr>
        	</thead>
          	<tbody>
          		<tr><td>
          			[eelv_news_form groupe=<?=$grp_id?>]
          		</td></tr>
          	</tbody>
          </table>
          <?php endif; ?>
</div>
<?php
  }
  /*****************************************************************************************************************************************
  E N V O I
  *****************************************************************************************************************************************/
  function news_envoi(){
  $this->inscform_action();
                      //newsletter_checkdb();
                      global $wpdb;
                      $default_exp = get_option( 'newsletter_default_exp' );
                      $default_mel = get_option( 'newsletter_default_mel' );
                      $desinsc_url = get_option( 'newsletter_desinsc_url' );
                      ?>
<div class="wrap">
  <div id="icon-edit" class="icon32 icon32-posts-newsletter"><br/></div>
  <h2><?php _e('Newsletter', 'eelv_lettreinfo' ) ?></h2>
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
          <?php _e('No letter is in progress. to create one', 'eelv_lettreinfo' ) ?> <a href="post-new.php?post_type=newsletter" class="button"><?php _e('click here', 'eelv_lettreinfo' ) ?></a>
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
								$content.=$this->eelv_newsletter_sharelinks($post->post_title,$post->guid);
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
                        if(isset($_GET['settemplate']) && is_numeric($_GET['settemplate'])){
							update_post_meta($post->ID,'nl_template',$_GET['settemplate']);
						}
                        $content=$this->nl_content($post_id);
						$preview = apply_filters('the_content',$content);

						$reply_url = get_option( 'newsletter_reply_url','');
						if(empty($reply_url)){					?>
							<div class="updated"><p><a href="edit.php?post_type=newsletter&page=newsletter_page_configuration">
							<?php _e('Missing parameter "Answer page" for your Newsletter, please go to the configuration page', 'eelv_lettreinfo' ); ?></a></p></div>
							<?php
						}
						$desinsc_url = get_option( 'newsletter_desinsc_url' );
						if(empty($desinsc_url)){					?>
							<div class="updated"><p><a href="edit.php?post_type=newsletter&page=newsletter_page_configuration">
							<?php _e('Missing parameter "Unsubscribe page" for your Newsletter, please go to the configuration page', 'eelv_lettreinfo' ); ?></a></p></div>
							<?php
						}


                        $template_id = get_post_meta($post->ID,'nl_template',true);
                        /*$post = get_post( $post_id);
                        $template =  get_post(get_post_meta($post_id,'nl_template',true));
                        $content=str_replace('[newsletter]',(trim($post->post_content)),$template->post_content);
                        $content=str_replace('[desinsc_url]',"<a href='$desinsc_url' target='_blank' class='nl_a'>$desinsc_url</a>",$content);
                        */
                        if(!isset($_POST['send']) ){
                          $user_info = get_userdata(get_current_user_id());
                          /////////////////////////////////// CHOIX DES DESTINATAIRES
                          ?>
                          <?php add_thickbox(); ?>
          <h3 class="sectiontitle title3"><?php _e('Preview', 'eelv_lettreinfo' ) ?></h3>
          <div id="eelv_news_prevlink" style="display:none;">
		     <p>
		        <?php _e('This is only a preview link', 'eelv_lettreinfo' ); ?></a>
			</p>
		</div>
          <div class='eelv_news_frame' id="nl_preview">
            <?php echo $preview; ?></div>
          <a href="post.php?post=<?=$post_id?>&action=edit" class="button"><?php _e('Edit', 'eelv_lettreinfo' ) ?></a>
          <?php _e('Skin', 'eelv_lettreinfo' ) ?>:
          <select name="newslettertemplate" onchange="document.location='edit.php?post_type=newsletter&page=news_envoi&post=<?=$post_id?>&settemplate='+this.value+'#nl_preview';">
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
    <option value="<?=$item_id;?>" <?php if($item_id==$template_id){ echo' selected ';} ?>/> <?=get_the_title($item_id);?></option>
      <?php }
                      }
                      ?>
          </select>
          <form action="edit.php?post_type=newsletter&page=news_envoi&post=<?=$post_id?>#nl_preview" method="post" class='eelv_news'>
            <input type="hidden" name="send" value="1" />
            <table><tr>
              <td>
                <h3 class="sectiontitle title3"><?php _e('Headers', 'eelv_lettreinfo' ) ?></h3>
                <p><label for="sujet"><?php _e('Subject', 'eelv_lettreinfo' ) ?>
                  <input type="text" name="eelv_news_sujet" size="30" tabindex="1" value="<?=$post->post_title?>" id="sujet" autocomplete="off" required/></label> </p>
                <p><label for="exp"><?php _e('Sender name', 'eelv_lettreinfo' ) ?>
                  <input type="text" name="eelv_news_exp" size="30" tabindex="1" value="<?=$default_exp?>" id="exp" autocomplete="off" required/></label> </p>
                <p><label for="mel"><?php _e('Reply email', 'eelv_lettreinfo' ) ?>
                  <input type="email" name="eelv_news_mel" size="30" tabindex="1" value="<?=$default_mel?>" id="mel" autocomplete="off" required/></label></p>

                <p><label for="stat"><?php _e('Archive stat', 'eelv_lettreinfo' ) ?>
                  <select name="eelv_news_stat" id="stat" required>
                    <option value='publish'><?php _e('Published', 'eelv_lettreinfo' ) ?></option>
                    <option value='private'><?php _e('private', 'eelv_lettreinfo' ) ?></option>
                  </select>
                  </label>
                </p>

                <p>
                	<?php _e('Archive news types', 'eelv_lettreinfo' ) ?>
                	<ul>
			       <?php
				   	$types = get_terms(array('newsletter_archives_types'),array( 'hide_empty' => 0 ));
					foreach($types as $type){ ?>
			       		<li><label for='type_<?=$type->term_id?>'>
                          <input type="checkbox" name='types[<?=$type->term_id?>]' id='type_<?=$type->term_id?>' value='<?=$type->slug?>'/>
                          <?=$type->name?>
                        </label></li>
			       <?php  } ?>
				       <li><label for='type_new'>
				       	<?php _e('New type:', 'eelv_lettreinfo' ) ?>
                          <input type="text" name='types[]' id='type_new' value=''/>
                        </label></li>
			       </ul>
                </p>

                 <p><label for="spy"><?php _e('Reading tracking', 'eelv_lettreinfo' ) ?>
                  <select name="eelv_news_spy" id="spy" required>
                    <option value='1'><?php _e('try to know if emails is readen', 'eelv_lettreinfo' ) ?></option>
                    <option value='0'><?php _e('deactivated', 'eelv_lettreinfo' ) ?></option>
                  </select>
                  </label>
                  </p>
              </td>
              <td>
                <h3 class="sectiontitle title3"><?php _e('Recipients', 'eelv_lettreinfo' ) ?></h3>
                <table><tr>
                  <td>
                    <h4><?php _e('Groups', 'eelv_lettreinfo' ) ?></h4>
                    <ul class='eelv_news_groups'>
                      <?php
  $GRPS = $this->news_liste_groupes();
                          foreach($GRPS as $groupe){
                            $nbinsc = sizeof($this->news_liste_contacts($groupe->id));
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
                    <h4><?php _e('blog users', 'eelv_lettreinfo' ) ?></h4>
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
                  <?php do_action('eelv_newsletter_select_receipients'); ?>
                  <td>
                    <h4><?php _e('Additional recipients', 'eelv_lettreinfo' ) ?></h4>
                    <textarea name="dests" cols="30" rows="5"><?php echo $user_info->user_email; ?></textarea>
					<legend><?php _e('Return separated email address', 'eelv_lettreinfo' ) ?></legend>
                  </td>
                  </tr></table>
              </td></tr></table>
            <input type='submit' value='<?php _e( "Send", 'eelv_lettreinfo' ) ?>' class="button-primary"/>
          </form>
          <?php
  }
  else{
  /////////////////////////////////// ENVOI
  $contacts='';
                          // CUSTOM GROUPES
                          $dest = array();
                          $GRPS = $this->news_liste_groupes();
                          foreach($GRPS as $groupe){
                            if(isset($_POST['grp_'.$groupe->id])){
                              array_push($dest,$groupe->id);
                            }
                          }
                          $temp = $this->news_liste_contacts($dest,'email');
                          foreach($temp as $contact){
                            $contacts.=$contact->email.',';
                          }
                          // USERS
                          $result = count_users();
                          foreach($result['avail_roles'] as $role => $count){
                            if(isset($_POST['rol_'.$role])){
                              $blogusers = get_users(array('blog_id'=>$wpdb->blogid,'orderby'=>'nicename','role'=>$role));
                              foreach ($blogusers as $user) {
                              	if(in_array($role,$user->roles) || $role==$user->roles){
	                                $contacts.=$user->user_email.',';
				}
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
                          // OTHER
                          $contacts=apply_filters('eelv_newsletter_parse_receipients',$contacts);


                          $contacts=implode(',',array_unique(explode(',',$contacts)));
                          if(0=== $archive = wp_insert_post( array('post_type'=>'newsletter_archive','post_title' => $post->post_title,  'post_content' => $post->post_content,  'post_status' => $_POST['eelv_news_stat']))){
                            echo __("An error occured !",'eelv_lettreinfo');
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

                            if(isset($_POST['types'])){
                                    $types = $_POST['types'];
                                    wp_set_object_terms( $archive, $types,'newsletter_archives_types' );
                            }

                            echo __('Sending...','eelv_lettreinfo')."
                              <script>
                              setTimeout(function(){document.location='post.php?post=".$archive."&action=edit&ref=".time()."';},5000);
                              </script>
                              ".__("To view the delivery status, please go to",'eelv_lettreinfo')."
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

					  //Save temple in NL
                      if (isset($_POST['post_type']) && 'newsletter' == $_POST['post_type']  && isset($_REQUEST['newslettertemplate']) && $_REQUEST['newslettertemplate']!=''){
                        update_post_meta($post_id, 'nl_template', $_REQUEST['newslettertemplate']);
                      }

                      //Save default content in template
                      if (isset($_POST['post_type']) &&  'newsletter_template' == $_POST['post_type']  && isset($_REQUEST['item_style']) && is_array($_REQUEST['item_style'])) {
                      	update_post_meta($post_id, 'item_style', $_REQUEST['item_style']);
                      }
                      if (isset($_POST['post_type']) &&  'newsletter_template' == $_POST['post_type']  && isset($_REQUEST['default_content']) && $_REQUEST['default_content']!='') {
                      	update_post_meta($post_id, 'default_content', stripslashes($_REQUEST['default_content']));
                      }
                      	//
                    }
                    function newsletter_admin_prev() {
                      $my_temp=get_post(get_post_meta(get_the_ID(), 'nl_template',true));
                      $env=true;
                      if(get_the_ID()==0){
                        $env=false;
                        echo"<p>".__("Your newsletter has'nt been saved yet",'eelv_lettreinfo')."</p>";
                      }
                      if(get_the_title()==''){
                        $env=false;
                        echo"<p>".__("Your newsletter has no title",'eelv_lettreinfo')."</p>";
                      }
                      if(!$my_temp){
                        $env=false;
                        echo"<p>".__("No skin applied",'eelv_lettreinfo')."</p>";
                      }
                      if($env==true){
                        echo'<p><a href="edit.php?post_type=newsletter&page=news_envoi&post='.get_the_ID().'#nl_preview" class="button-primary">'.__('Preview and send','eelv_lettreinfo').'</a></p>';
                      }
                    }














                    function newsletter_admin() {
                      global $wpdb;
                      //newsletter_checkdb();
                      //print_r($this->eelv_nl_content_themes);
                      ?>
<table id="eelv_nl_edit_choose_skins"><tr>
  <td valign="top">
    <h4><?php _e('Skin', 'eelv_lettreinfo' ) ?></h4>
    <?php
  $querystr = "SELECT `ID` FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'newsletter_template' ORDER BY `post_title`";
                      $IDS = $wpdb->get_col($querystr);
                      $templates_nb = sizeof($IDS);

                      if($templates_nb>0){
                        $my_temp=get_post_meta(get_the_ID(), 'nl_template',true);
						$my_temp_title='';
                        foreach($IDS as $item_id){
                          if($my_temp==NULL){
                            add_post_meta(get_the_ID(), 'nl_template', $item_id);
						    $my_temp=$item_id;
                          }
							$my_temp_title=get_the_title($item_id);
							$default_content = get_post_meta($item_id, 'default_content',true);
							if(!empty($default_content)){
								$this->eelv_nl_content_themes[$my_temp_title]=$default_content;
							}
							elseif(!isset($this->eelv_nl_content_themes[$my_temp_title])){
								$this->eelv_nl_content_themes[$my_temp_title]='';
							}
                          ?>
    <p>
    	<label>
	    	<input type='radio' onclick="set_default_content('dc_<?=$item_id;?>')" name='newslettertemplate' id='nt_<?=$item_id;?>' value='<?=$item_id;?>' <?php if($item_id==$my_temp){ echo' checked=checked ';} ?>/>
	    	<?=$my_temp_title;?>
    	</label>
    	<?php if($this->eelv_nl_content_themes[$my_temp_title]!=''){ ?>
    	<u onclick="apply_default_content('dc_<?=$item_id;?>')"><?php _e('Load default content', 'eelv_lettreinfo' ) ?></u>
    	<?php } ?>
    	<textarea id="dc_<?=$item_id;?>" style="display:none;"><?=$this->eelv_nl_content_themes[$my_temp_title]?></textarea>
    </p>
      <?php }
      } ?>
      </td><td valign="top" style='padding-left:20px'>
      <h4><?php _e('Insert some content', 'eelv_lettreinfo' ) ?></h4>
      <h5><?php _e('Available addressing variables', 'eelv_lettreinfo' ) ?></h5>
      <ul>
      	<li><a onclick="incontent(' {dest_name} ');">{dest_name}</a></li>
      	<li><a onclick="incontent(' {dest_login} ');">{dest_login}</a></li>
      	<li><a onclick="incontent(' {dest_email} ');">{dest_email}</a></li>
      </ul>

      <script>
        var IEbof=false;
      </script>
      <!--[if lt IE 9]>
      <script>IEbof=true;</script>
      <![endif]-->

      <h5><?php _e('Last posts', 'eelv_lettreinfo' ) ?></h5>
      <?php
      $item_style=shortcode_atts($this->default_item_style, get_post_meta($my_temp, 'item_style',true));

	    $imgsize=$item_style['t_size'];
	    /*if ( function_exists( 'add_image_size' ) ) {
	   		$imgsize='newletter_template_thumbnail_'.$my_temp;
			//add_image_size( $imgsize, $item_style['t_size_width'],$item_style['t_size_height'],true);
		}*/

  					$querystr = "";
                      $optis='<option value="">'.__('Posts', 'eelv_lettreinfo' ).'</option>';
                      $post_list=new WP_Query(array('status'=>'publish','post_type'=>'post','posts_per_page'=>'-1'));
                      if($post_list->have_posts()){
                        while($post_list->have_posts()){
                          $post_list->the_post();

                          ?>
      <textarea id="nl_post_<?php the_ID();?>" style="display:none"><?php echo"<div style='".$item_style['div']."'>";
      echo"<a href='".get_post_permalink()."' style='".$item_style['a']."'>".get_the_post_thumbnail(get_the_ID(),$imgsize,array('style'=>$item_style['img']))."</a>";
      echo"<h3><a href='".get_post_permalink()."' style='".$item_style['h3']."'>".get_the_title()."</a></h3>";
      echo"<p style='".$item_style['p']."'>".substr(strip_tags(get_the_content()),0,$item_style['excerpt_length'])."...</p>";
  		if($item_style['readmore_content']!=''){ echo "<a href='".get_post_permalink()."' style='".$item_style['readmore']."'>".$item_style['readmore_content']."</a>";  }
  		?></textarea>
      <textarea id="nl_share_<?php the_ID();?>" style="display:none"><?php echo $this->eelv_newsletter_sharelinks(get_the_title(),get_post_permalink()); ?></textarea>
      <?php
                          $optis.='<option value="'.get_the_ID().'">'.substr(get_the_title(),0,70).'</option>';
                        } ?>
      <p><select name="nl_insert_post" onchange="var nl_p_content=getElementById('nl_post_'+this.value).value; if(document.getElementById('nl_with_share').checked==true){nl_p_content+=getElementById('nl_share_'+this.value).value}incontent(nl_p_content+'</div><br><br>');this.value=''">
        <?=$optis?>
        </select>
      </p>
      <?php  }

                      $optis='<option value="">'.__('Pages', 'eelv_lettreinfo' ).'</option>';
                      $page_list=new WP_Query(array('status'=>'publish','post_type'=>'page','posts_per_page'=>'-1'));
                      if($page_list->have_posts()){
                        while($page_list->have_posts()){
                          $page_list->the_post();
                          ?>
      <textarea id="nl_page_<?php the_ID();?>" style="display:none"><?php echo"<div style='".$item_style['div']."'><a href='".get_post_permalink()."' style='".$item_style['a']."'>".get_the_post_thumbnail(get_the_ID(),$imgsize,array('style'=>$item_style['img']))." <h3 style='".$item_style['h3']."'>".get_the_title()."</h3> <p style='".$item_style['p']."'>".substr(strip_tags(get_the_content()),0,300)."...</p></a>";
  if($item_style['readmore_content']!=''){ echo "<a href='".get_post_permalink()."' style='".$item_style['readmore']."'>".$item_style['readmore_content']."</a>";  }
  echo"</div><br><br>"; ?></textarea>

      <?php
                          $optis.='<option value="'.get_the_ID().'">'.substr(get_the_title(),0,70).'</option>';
                        } ?>
      <p><select name="nl_insert_page" onchange="incontent(getElementById('nl_page_'+this.value).value);this.value=''">
        <?=$optis?>
        </select></p>
      <?php  }
                      ?>
      <label for="">
        <input type="checkbox" id="nl_with_share" <?php if(get_option( 'newsletter_precoch_rs' )=='1') echo'checked="checked"'; ?>/><?=__('Add share links', 'eelv_lettreinfo' )?>
      </label>
      </td></tr></table>
      <?php
                    }


                    function newsletter_admin_wizard() { ?>
                            <div class="all">
                                <?php _e('You can get answers to a simple question by inserting some answer links like "Do you like this plugin ? click here for yes, click here fo no", anwsers will be stored in the archive page.','eelv_lettreinfo'); ?>
                            <p>
                                <label><?php _e('Answer value:','eelv_lettreinfo'); ?>
                                    <input type="text" id="newsletter_wizard_rep" value="<?php _e('Yes','eelv_lettreinfo'); ?>" data-att="rep">
                                </label>
                            </p>
                            <p>
                                <label><?php _e('Displayed text:','eelv_lettreinfo'); ?>
                                    <input type="text" id="newsletter_wizard_val" value="<?php _e('Click here for yes','eelv_lettreinfo'); ?>" data-att="val">
                                </label>
                            </p>
                            </div>
                    <div id="newsletter_wizard_shortcode">[nl_reply_link rep="<?php _e('Yes','eelv_lettreinfo'); ?>" val="<?php _e('Click here for yes','eelv_lettreinfo'); ?>"]</div>
                    <a class="button" id="newsletter_wizard_submit"><?php _e('Insert answer link','eelv_lettreinfo'); ?></a>
                    </div>
              <?php
                    }


                    function newsletter_archive_admin() {
                      global $wpdb;
                      $post_id = get_the_ID(); //$_GET['id'];
                      $my_temp=get_post_meta($post_id, 'nl_template',true);
                      $sujet = get_post_meta($post_id, 'sujet', true);
                      $expediteur = get_post_meta($post_id, 'expediteur', true);
                      $reponse = get_post_meta($post_id, 'reponse' ,true);
                      $lastsend = get_post_meta($post_id, 'lastsend',true);
                      // $post = get_post( $post_id);
                      $template =  get_post(get_post_meta($post_id,'nl_template',true));
                      $content=apply_filters('the_content',$this->nl_content($post_id ));   ?>
                      <div id="eelv_news_prevlink" style="display:none;">
		     <p>
		        <?php _e('This is only a preview link', 'eelv_lettreinfo' ); ?></a>
		        </p>
		     </div>

      <h2><?=$sujet?></h2>
      <?php if(!$template){ ?>
      <?php _e('The skin has gone away ! Do you want to apply another one ?', 'eelv_lettreinfo' ) ?>
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
      <p><?php _e('Sent by','eelv_lettreinfo') ?> : <?=$expediteur?> (<?=$reponse?>)</p>
      <p><?php _e('Last sent','eelv_lettreinfo') ?> : <?=$lastsend?></p>
      <div><?=$content?></div>
      <?php
                    }
                    function newsletter_archive_admin_dest() {
                      global $wpdb;
                      $post_id = get_the_ID(); //$_GET['id'];
                      $sent = get_post_meta($post_id, 'sentmails',true);
					  $nl_spy=get_post_meta($post_id, 'nl_spy',true);
					  $lus = abs(substr_count($sent,':3'));
		  			 $tot = abs(substr_count($sent,','));
                      ?>
                    <p><?php  printf(__('%s opened','eelv_lettreinfo'),($tot>0?round($lus/$tot*100):0).'%'); ?></p>
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
                      <?php if($nl_spy==0) _e('No reading-tracking','eelv_lettreinfo'); ?>
                      </p>
      <?php
                    }


					//Meta boxes

					//Queue list
                    function newsletter_archive_admin_queue() {
                      global $wpdb;
                      $post_id = get_the_ID(); //$_GET['id'];
                      $dest = get_post_meta($post_id, 'destinataires',true);
                      ?>
      <p><?=$dest?></p>
      <?php if($dest!=''){ ?>
      <a href='post.php?post=<?=$post_id?>&action=edit&ref=<?=time()?>'><?=__('Automatically sending a new burst','eelv_lettreinfo')?></a>
      <script>
      setTimeout(function(){document.location='post.php?post=<?=$post_id?>&action=edit&ref=<?=time()?>';},5000);
      </script>
      <?php }
                    }

					//answers
					function newsletter_archive_admin_answers() {
						$nl =  get_post(get_the_ID());
	 					$content=$nl->post_content;
						if(strpos($content,'[nl_reply_link')>-1){


                      $answers = get_post_meta(get_the_ID(),'eelv_nl_reply');
					  ?>
					  <table class="sortable widefat">
					  	<thead>
					  		<tr>
						  		<td><?php _e('E-mail','eelv_lettreinfo')?></td>
						  		<td><?php _e('Answer','eelv_lettreinfo')?></td>
						  		<td><?php _e('Date','eelv_lettreinfo')?></td>
					  		</tr>
					  	</thead>
					  	<tboby>
					  <?php
					  if(sizeof($answers)>0){
					  foreach($answers as $answer){ if(is_array($val = @unserialize($answer))){ ?>
					  	<tr>
					  		<td>
					  			<?php echo $val['email']; ?>
					  		</td>
					  		<td>
					  			<?php echo $val['val']; ?>
					  		</td>
					  		<td>
					  			<?php echo date_i18n(get_option('date_format') ,$val['date']); ?>
					  			<?php echo date('H:i:s',$val['date']); ?>
					  		</td>
					  	</tr>
					 <?php }}
      					}
					  else{ ?>
					  	<tr><td colspan="3"><?php _e( "No answer", 'eelv_lettreinfo' ); ?></td></tr>
					  <?php } ?>
					  </tboby></table>
					  <?php
						}
						  else{
						  	_e( "No question", 'eelv_lettreinfo' );
						  }
                    }


					function newsletter_template_default_content(){
						$post_id = get_the_ID();
                        $default_content = get_post_meta($post_id, 'default_content',true);
						_e('This content will be loaded when you\'ll choose this template for editing a newsletter.','eelv_lettreinfo');
						wp_editor( $default_content, 'default_content' );
					}
					function newsletter_template_item_style(){
						$post_id = get_the_ID();
						$item_style=shortcode_atts($this->default_item_style, get_post_meta($post_id, 'item_style',true));

						?>
						 <div id="newsletter_item_post_style">
						 	<div>
								<label>
									<?php _e('Thumbnail size','eelv_lettreinfo')?> :
									<select data-type="t_size" name="item_style[t_size]">
										<option value="thumbnail" <?=($item_style['t_size']=='thumbnail'?'selected':'') ?>><?=_x('Thumbnail size','eelv_lettreinfo')?></option>
										<option value="medium" <?=($item_style['t_size']=='medium'?'selected':'') ?>><?=_x('Medium size','eelv_lettreinfo')?></option>
										<option value="large" <?=($item_style['t_size']=='large'?'selected':'') ?>><?=_x('Large size','eelv_lettreinfo')?></option>
										<option value="full" <?=($item_style['t_size']=='full'?'selected':'') ?>><?=_x('Full size','eelv_lettreinfo')?></option>
									</select>
								</label>
							</div>
					  <?php
						 foreach ($item_style as $k => $v) { if($k!='t_size'){?>
						<div>
							<label>
								<?=$this->default_item_style_trads[$k]?>
								<input type="text" data-type="<?=$k?>" name="item_style[<?=$k?>]" value="<?=$v?>" class="widefat">
							</label>
						</div>
						<?php
						}}
						 ?>
						 </div>

						 <h3><?php _e( "Preview", 'eelv_lettreinfo' ) ?></h3>
						 <div id="newsletter_item_post_preview">

						 	<?php $pst = new WP_Query('posts_per_page=5');
							while($pst->have_posts()){ $pst->the_post(); ?>

						 <div class="nl_preview">
  <a href='#' class="nl_preview">
  	<?php the_post_thumbnail($item_style['t_size'],array('class'=>'nl_preview')) ?>
  <h3 class="nl_preview"><?php the_title(); ?></h3></a>
  <p class="nl_preview"><?=substr(strip_tags(get_the_content()),0,$item_style['excerpt_length']);?>...</p></a>
  <a href='#' class="readmore"><?=$item_style['readmore_content']?></a>
  						</div>
  						<?php } ?>
  						</div>
						 <?php
					}

                    ///////////////////////////////////// CHECK DB
                    function newsletter_checkdb(){
                      ?>
      <div class="wrap">
        <div id="icon-edit" class="icon32 icon32-posts-newsletter"><br/></div>
        <h2><?php _e('Newsletter', 'eelv_lettreinfo' ) ?></h2>
        <table class="widefat" style="margin-top: 1em;">
          <thead>
            <tr>
              <th scope="col" colspan="2"><?php _e('Reload parameters', 'eelv_lettreinfo' ) ?></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>
                <?php
  global $wpdb;
                      // GROUPE NON CLASSE
                      $ret =  $wpdb->get_results("SELECT * FROM `$this->newsletter_tb_name` WHERE `id`='1'");?>
                     <h3><?php _e('Address book','eelv_lettreinfo'); ?></h3>
                      <p><?php _e('Uncategorized group :','eelv_lettreinfo'); ?>
                     <?php if(is_array($ret) && sizeof($ret)>0){
                        $query="UPDATE $this->newsletter_tb_name SET `nom`='".__("Uncategorized",'eelv_lettreinfo')."',`email`='',`parent`='0' WHERE `id`='1'";
                        _e('ok','eelv_lettreinfo');
                      }
                      else{
                        $query="INSERT INTO $this->newsletter_tb_name (`id`,`nom`) VALUES ('1','".__("Uncategorized",'eelv_lettreinfo')."')";
                        _e('Created','eelv_lettreinfo');
                      }
						?>
						</p>
						<?php

                      $wpdb->query($query);
                      // GROUPE RED LIST
                      echo'<p>'.__('Red list group :','eelv_lettreinfo').' ';
                      $ret =  $wpdb->get_results("SELECT * FROM `$this->newsletter_tb_name` WHERE `id`='2'");
                      if(is_array($ret) && sizeof($ret)>0){
                        $query="UPDATE $this->newsletter_tb_name SET `nom`='".__("Red list",'eelv_lettreinfo')."',`email`='',`parent`='0' WHERE `id`='2'";
                        _e('ok','eelv_lettreinfo');
                      }
                      else{
                        $query="INSERT INTO $this->newsletter_tb_name (`id`,`nom`) VALUES ('2','".__("Red list",'eelv_lettreinfo')."')";
                        _e('Created','eelv_lettreinfo');
                      }
                      echo'  </p>';
                      $wpdb->query($query);
                      // THEMES PAR DEFAUT
                      echo'<h3>'.__('Default skins','eelv_lettreinfo').'</h3>';
                      foreach($this->eelv_nl_default_themes as $check_theme=>$check_content){
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

					function newsletter_getuserinfos($courriel){
						global $wpdb;
						$destin=array(
							'name'=>'',
							'login'=>''
						);
						$user = $wpdb->get_results($wpdb->prepare(
						"SELECT `display_name`,`user_login` FROM ".$wpdb->prefix."users WHERE `user_email`='%s'",
						$courriel
						));

						if($user){
							$user=$user[0];
							$destin['name']=$user->display_name;
	                        $destin['login']=$user->user_login;
						}
						else{
							$ret = $wpdb->get_results("SELECT * FROM `$this->newsletter_tb_name` WHERE `email`='".str_replace("'","''",$courriel)."'");
	                        if(is_array($ret) && sizeof($ret)>0){
	                          $destin['name']=$ret[0]->nom;
	                          $destin['login']='';
							}
						}
						return $destin;
					}
                    ///////////////////////////////////// SEMI CRON AUTO SEND
                    function newsletter_autosend(){
                      global $wpdb,$nl_id,$dest;
					  $querystr = "SELECT $wpdb->posts.`ID` FROM $wpdb->posts,$wpdb->postmeta WHERE (post_status = 'publish' OR post_status = 'private') AND post_type = 'newsletter_archive'  AND $wpdb->postmeta.`post_id`=$wpdb->posts.`ID` AND $wpdb->postmeta.`meta_key`='destinataires' AND $wpdb->postmeta.`meta_value`!=''";
                      $IDS = $wpdb->get_col($querystr);
                      $send_nb = sizeof($IDS);
                      if($send_nb>0){
                        $desinsc_url = get_option( 'newsletter_desinsc_url' );
                        $env=0;

						$content_top='<link rel="stylesheet" href="'.plugins_url( 'mail.css' , __FILE__ ).'" type="text/css" media="all" />';
                        $content_top.='<style type="text/css">';
						$content_top.=file_get_contents(plugin_dir_path(__FILE__ ).'mail.css');
                        $content_top.='</style>';


                        foreach($IDS as $nl_id){
                          $my_temp=get_post_meta($nl_id, 'nl_template',true);
                          $sujet = get_post_meta($nl_id, 'sujet', true);
                          $expediteur = get_post_meta($nl_id, 'expediteur', true);
                          $reponse = get_post_meta($nl_id, 'reponse' ,true);
                          $dests = get_post_meta($nl_id, 'destinataires',true);
                          $nl_spy = get_post_meta($nl_id, 'nl_spy',true);
                          if(substr($dests,0,1)==',') $dests=substr($dests,1);
                          $dests = explode(',',$dests);
                          $sent = get_post_meta($nl_id, 'sentmails',true);
                          $template=get_post($my_temp);
                          if($template){


                            $content=$content_top.'<center><a href="'.home_url().'/?post_type=newsletter_archive&p='.$nl_id.'" target="_blank"><font size="1">'.__('Click here if you cannot read this e-mail','eelv_lettreinfo').'</font></a></center>';
                            $content.=$this->nl_content($nl_id);
                            $the_content=$content;

                            $prov = getenv("SERVER_NAME");
                            $eol=$this->eol;
                            $now = time();
                            $boundary=md5(time());
                            $headers = "From: $expediteur <$reponse>".$eol;
                            $headers .= "Reply-To: $expediteur <$reponse>".$eol;
                            $headers .= "Return-Path: $expediteur <$reponse>".$eol;
                            $headers .= "Message-ID: <".$nl_id.".".$prov.">".$eol;
                            $headers .= "X-Mailer: PHP v".phpversion().$eol;
                            if($this->mime_type=='html_txt'){
                                $headers .= "Content-Type: multipart/alternative; boundary=".$boundary.$eol;
                            }
                            else{
                               $headers .= "Content-Type: text/html; charset=UTF-8".$eol;
                                $headers .= 'Content-Transfer-Encoding: 8bit'.$eol;
                            }
                            $headers .= 'MIME-Version: 1.0';

                            //print_r($dests);
                            $this->newsletter_admin_surveillance = get_site_option( 'newsletter_admin_surveillance' );
                            if($this->newsletter_admin_surveillance!=''){
                              mail($this->newsletter_admin_surveillance,'[EELV-newsletter:'.__('Sending','eelv_lettreinfo').'] '.$sujet,$this->nl_mime_txt($content,$boundary),$headers,'-f '.$reponse);
                            }

                            while($dest = array_shift($dests)){
                              $dest=trim($dest);
							  if(strstr($the_content,'{')){
							  	$destinataire=$this->newsletter_getuserinfos($dest);
							  }
							  else{
							  	$destinataire=true;
							  }
                              if ($destinataire && filter_var($dest, FILTER_VALIDATE_EMAIL)) {
                                $ret = $wpdb->get_results("SELECT * FROM `$this->newsletter_tb_name` WHERE `email`='".str_replace("'","''",$dest)."' AND `parent`='2' LIMIT 0,1");
                                if(is_array($ret) && sizeof($ret)==0){             // White liste OK
                                  if( update_post_meta($nl_id, 'destinataires',implode(',',$dests)) ){

                                        $the_content=$content;
                                        $the_sujet=$sujet;
                                        if(strstr($the_sujet,'{dest_name}')){
                                                $the_sujet=str_replace('{dest_name}',$destinataire['name'],$the_sujet);
                                        }
                                        if(strstr($the_sujet,'{dest_login}')){
                                                $the_sujet=str_replace('{dest_login}',$destinataire['login'],$the_sujet);
                                        }
                                        if(strstr($the_sujet,'{dest_email}')){
                                                $the_sujet=str_replace('{dest_email}',$dest,$the_sujet);
                                        }


                                        if(strstr($the_content,'{dest_name}')){
                                                $the_content=str_replace(' {dest_name}',' '.$destinataire['name'],$the_content);
                                                $the_content=str_replace('{dest_name}',$destinataire['name'],$the_content);
                                        }
                                        if(strstr($the_content,'{dest_login}')){
                                                $the_content=str_replace(' {dest_login}',' '.$destinataire['login'],$the_content);
                                                $the_content=str_replace('{dest_login}',$destinataire['login'],$the_content);
                                        }
                                        if(strstr($the_content,'{dest_email}')){
                                                $the_content=str_replace(' {dest_email}',' '.$dest,$the_content);
                                                $the_content=str_replace('{dest_email}',$dest,$the_content);
                                        }

                                        $the_content=apply_filters('the_content',$the_content);

                                        if($nl_spy==1){
                                                  $the_content.='<a href="'.get_bloginfo('url').'"><img src="'.$this->newsletter_plugin_url.'/eelv-newsletter/reading/'.base64_encode($dest.'!'.$nl_id).'/logo.png" border="none" alt="'.get_bloginfo('url').'"/></a>';
                                          }


                                    if(mail($dest,$the_sujet,$this->nl_mime_txt($the_content,$boundary),$headers,'-f '.$reponse)){  // Envoi OK
                                      $sent = $dest.':1,'.$sent;
                                    }
                                    else{                    // Envoi KO
                                      $sent = $dest.':0,'.$sent;
                                    }
                                    update_post_meta($nl_id, 'sentmails',$sent);
                                    $env++;

                                  }
                                }
                                elseif(is_array($ret) && sizeof($ret)==1){           // Black list
                                  $sent = $dest.':2,'.$sent;
                                  update_post_meta($nl_id, 'destinataires',implode(',',$dests));
                                  update_post_meta($nl_id, 'sentmails',$sent);
                                }
                                else{                         // Envoi OK
                                  $sent = $dest.':0+,'.$sent;
                                  update_post_meta($nl_id, 'destinataires',implode(',',$dests));
                                  update_post_meta($nl_id, 'sentmails',$sent);
                                }
                              }
                              else{            // Mail invalide
                                $sent = $dest.':-1,'.$sent;
                                update_post_meta($nl_id, 'destinataires',implode(',',$dests));
                                update_post_meta($nl_id, 'sentmails',$sent);
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
<div class="updated"><p><strong><?php _e('Options saved', 'eelv_lettreinfo' ); ?></strong></p></div>
<?php
  }
  $this->newsletter_admin_surveillance = get_site_option( 'newsletter_admin_surveillance' );
  ?>
      <div class="wrap">
        <div id="icon-edit" class="icon32 icon32-posts-newsletter"><br/></div>
        <h2><?=_e('Newsletter', 'eelv_lettreinfo' )?></h2>
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
                  <label for="newsletter_default_exp"><?=_e('Send a copy of each burst of shipments to:', 'eelv_lettreinfo' )?> :</label>
                </td><td>
                <input  type="text" name="newsletter_admin_surveillance"  size="60"  id="newsletter_admin_surveillance"  value="<?=$this->newsletter_admin_surveillance?>" class="wide">
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <p class="submit">
                    <input type="submit" name="Submit" value="<?php _e('Save', 'eelv_lettreinfo' ) ?>" />
                  </p>
                </td>
              </tr>
            </tbody>
          </table>
        </form>
      </div>
      <?php
}
function eelv_newsletter_addAlert() {
	$cu = wp_get_current_user();
    if ($cu->has_cap('manage_options') && get_option( 'newsletter_options_version')!=$this->eelv_newsletter_options_version && !is_network_admin()) {
  ?>
	<div class="updated"><p><a href="edit.php?post_type=newsletter&page=newsletter_page_configuration">
		<?php _e('New options are available for your Newsletter, please go to the configuration page', 'eelv_lettreinfo' ); ?></a></p></div>
	<?php
	}
}

// mt_toplevel_page() displays the page content for the custom Test Toplevel menu
function newsletter_page_configuration() {
  global $wpdb;
  if( isset($_REQUEST[ 'type' ]) && $_REQUEST[ 'type' ] == 'update' ) {
	update_option( 'newsletter_default_exp', stripslashes($_REQUEST['newsletter_default_exp']) );
	update_option( 'newsletter_default_mel', stripslashes($_REQUEST['newsletter_default_mel']) );
	update_option( 'newsletter_desinsc_url', stripslashes($_REQUEST['newsletter_desinsc_url']) );
	update_option( 'newsletter_reply_url', stripslashes($_REQUEST['newsletter_reply_url']) );
	update_option( 'newsletter_precoch_rs', stripslashes($_REQUEST['newsletter_precoch_rs']) );
	update_option( 'newsletter_spy_text', stripslashes($_REQUEST['newsletter_spy_text']) );
	update_option( 'newsletter_mime_type', stripslashes($_REQUEST['newsletter_mime_type']) );
	update_option( 'newsletter_eol', stripslashes($_REQUEST['newsletter_eol']) );

	update_option( 'newsletter_msg', array(
		'sender'=>$_REQUEST['newsletter_msg_sender'] ,
		'suscribe_title'=>$_REQUEST['newsletter_msg_suscribe_title'] ,
		'suscribe'=>$_REQUEST['newsletter_msg_suscribe'] ,
		'unsuscribe_title'=>$_REQUEST['newsletter_msg_unsuscribe_title'] ,
		'unsuscribe'=>$_REQUEST['newsletter_msg_unsuscribe']
	));
	//update_option( 'affichage_NL_hp', $_REQUEST['affichage_NL_hp'] );
	?>
<div class="updated"><p><strong><?php _e('Options saved', 'eelv_lettreinfo' ); ?></strong></p></div>
<?php
  }
  update_option( 'newsletter_options_version', $this->eelv_newsletter_options_version );
  $default_exp = get_option( 'newsletter_default_exp' );
  $default_mel = get_option( 'newsletter_default_mel' );
  $desinsc_url = get_option( 'newsletter_desinsc_url' );
  $reply_url = get_option( 'newsletter_reply_url' );
  $precoch_rs = get_option( 'newsletter_precoch_rs' );
  $spy_text = get_option( 'newsletter_spy_text' ,str_replace(array('http://','https://'),'',get_bloginfo('url')));
  $mime_type = get_option( 'newsletter_mime_type' );
  $eol = get_option( 'newsletter_eol' );

  if($spy_text==''){
 	$spy_text=str_replace(array('http://','https://'),'',get_bloginfo('url'));
 }
  //$affichage_NL_hp = get_option( 'affichage_NL_hp' );

  $this->newsletter_msg = get_option( 'newsletter_msg' );
	  $msg_sender = $this->newsletter_msg['sender'];
	  $msg_suscribe_title = $this->newsletter_msg['suscribe_title'];
	  $msg_suscribe = $this->newsletter_msg['suscribe'];
	  $msg_unsuscribe_title = $this->newsletter_msg['unsuscribe_title'];
	  $msg_unsuscribe = $this->newsletter_msg['unsuscribe'];
  ?>

      <div class="wrap">
        <div id="icon-edit" class="icon32 icon32-posts-newsletter"><br/></div>
        <h2><?=_e('Newsletter', 'eelv_lettreinfo' )?></h2>
        <form name="typeSite" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
          <input type="hidden" name="type" value="update">
          <input type="hidden" name="newsletter_options_version" value="<?=$this->eelv_newsletter_options_version?>"/>
          <table class="widefat" style="margin-top: 1em;">
            <thead>
              <tr>
                <th scope="col" colspan="2"><?php _e( 'Configuration ', 'menu-config' ) ?></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td width="30%">
                  <label for="newsletter_default_exp"><?php _e('Default sender name:', 'eelv_lettreinfo' ) ?></label>
                </td><td>
                <input  type="text" name="newsletter_default_exp"  size="60"  id="newsletter_default_exp"  value="<?=$default_exp?>" class="wide">
                </td>
              </tr>
              <tr>
                <td width="30%">
                  <label for="newsletter_default_mel"><?php _e('Default reply address:', 'eelv_lettreinfo' ) ?></label>
                </td><td>
                <input  type="text" name="newsletter_default_mel"  size="60"  id="newsletter_default_mel"  value="<?=$default_mel?>" class="wide">
                </td>
              </tr>
              <tr>
                <td width="30%">
                    <label for="newsletter_desinsc_url"><?php _e('Unsubscribe page', 'eelv_lettreinfo' ) ?> :</label><br>
                    <small>[eelv_news_form]</small>
                </td><td>
                <select  name="newsletter_desinsc_url"   id="newsletter_desinsc_url">
                  <option></option>
                  <?php
  $querystr = "";
  $page_choosen='';
                      $ret =  $wpdb->get_results("SELECT `ID`,`post_title`,`post_content` FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'page' ORDER BY `post_title`");
                      if(is_array($ret) && sizeof($ret)>0){
                        foreach($ret as $item){
                          $permalink = get_permalink( $item->ID );
                          ?>
                  <option value="<?=$permalink;?>" <?php if($desinsc_url==$permalink){ echo"selected"; $page_choosen=$item; } ?>><?=$item->post_title;?></option>
                  <?php }
                      }
                      ?>
                </select><br/>
				<legend>
                                    <?php
                                        echo (
                                                !empty($desinsc_url) && is_object($page_choosen) ?
                                                '<a href="'.$desinsc_url.'" target="_blank">'.$desinsc_url.'</a> '.(
                                                        strstr($page_choosen->post_content,'[eelv_news_form')?
                                                        '<span class="dashicons dashicons-yes"></span>':
                                                        '<br><div class="error"><span class="dashicons dashicons-dismiss"></span> <a href="post.php?post='.$page_choosen->ID.'&action=edit">'.__('It seems that the [eelv_news_form] is not present on the unsubscribe page. Click here to edit the page ', 'eelv_lettreinfo' ).'</a></div>'
                                                        ) :
                                            '<a href="post-new.php?post_type=page&content=[eelv_news_form]&post_title='.__('Newsletter', 'eelv_lettreinfo' ).'">'.__('Create a new page with [eelv_news_form] shotcode', 'eelv_lettreinfo' ).'</a>');
                                    ?>
                                </legend>
                </td>
              </tr>
              <tr>
                <td width="30%">
                    <label for="newsletter_reply_url"><?php _e('Reply page', 'eelv_lettreinfo' ) ?> :</label><br>
                    <small>[nl_reply_form]</small>
                </td><td>
                <select  name="newsletter_reply_url" id="newsletter_reply_url">
                  <option></option>
                  <?php
  $querystr = "";
  $page_choosen='';
                      if(is_array($ret) && sizeof($ret)>0){
                        foreach($ret as $item){
                          $permalink = get_permalink( $item->ID );
                          ?>
                  <option value="<?=$permalink;?>" <?php if($reply_url==$permalink){ echo"selected"; $page_choosen=$item; } ?>><?=$item->post_title;?></option>
                  <?php }
                      }
                      ?>
                </select><br/>
				<legend><?php
                                echo (
                                        !empty($reply_url) && is_object($page_choosen) ?
                                        '<a href="'.$reply_url.'" target="_blank">'.$reply_url.'</a>'.(
                                                        strstr($page_choosen->post_content,'[nl_reply_form')?
                                                        '<span class="dashicons dashicons-yes"></span>':
                                                        '<br><div class="error"><span class="dashicons dashicons-dismiss"></span> <a href="post.php?post='.$page_choosen->ID.'&action=edit">'.__('It seems that the [nl_reply_form] is not present on the reply page. Click here to edit the page ', 'eelv_lettreinfo' ).'</a></div>'
                                                        ) :
                                    '<a href="post-new.php?post_type=page&content=[nl_reply_form]&post_title='.__('Newsletter answer page', 'eelv_lettreinfo' ).'">'.__('Create a new page with [nl_reply_form] shotcode', 'eelv_lettreinfo' ).'</a>');
                                ?>
                                </legend>
                </td>
              </tr>
              <tr>
                <td width="30%">
                  <label for="newsletter_precoch_rs"><?php _e('Pre-check "Share buttons"', 'eelv_lettreinfo' ) ?></label>
                </td><td>
                <input type="checkbox" name="newsletter_precoch_rs"  id="newsletter_precoch_rs"  value="1" <?php echo $precoch_rs==1?'checked':''; ?>>
                </td>
              </tr>
              <tr>
                <td width="30%">
                  <label for="newsletter_spy_text"><?php _e('Spy image text:', 'eelv_lettreinfo' ) ?></label>
                </td><td>
                <input  type="text" name="newsletter_spy_text"  size="60"  id="newsletter_spy_text"  value="<?=$spy_text?>" class="wide">
                </td>
              </tr>
              <tr>
                <td width="30%">
                  <label for="newsletter_mime_type"><?php _e('MIME Type:', 'eelv_lettreinfo' ) ?></label>
                </td><td>
                    <p><label><input type="radio" name="newsletter_mime_type" value="html_only" <?=($mime_type=='html_only'?'checked':'')?>> <?php _e('HTML only', 'eelv_lettreinfo' ) ?></label></p>
                    <p><label><input type="radio" name="newsletter_mime_type"  value="html_txt" <?=($mime_type=='html_txt'||$mime_type==''?'checked':'')?>> <?php _e('HTML + Plain text (better, but may cause troubles on some mac clients)', 'eelv_lettreinfo' ) ?> <?php _e('(default value)', 'eelv_lettreinfo' ) ?></label></p>
                </td>
              </tr>
              <tr>
                <td width="30%">
                  <label for="newsletter_eol"><?php _e('End of line:', 'eelv_lettreinfo' ) ?></label>
                </td><td>
                    <p><label><input type="radio" name="newsletter_eol" value="rn" <?=($eol=='rn'||$eol==''?'checked':'')?>> \r\n <?php _e('(default value)', 'eelv_lettreinfo' ) ?></label></p>
                    <p><label><input type="radio" name="newsletter_eol"  value="n" <?=($eol=='n'?'checked':'')?>> \n</label></p>
                    <p><?php _e('See the related PHP documentation', 'eelv_lettreinfo' ) ?> <a href="http://php.net/manual/fr/function.mail.php">http://php.net/manual/fr/function.mail.php</a></p>
                </td>
              </tr>

              </tbody>
              <thead>
              <tr><th colspan="2"><?php _e( 'Confirmation e-mails ', 'menu-config' ) ?></th></tr>
              </thead>

              <tbody>
              <tr>
                <td width="30%">
                  <label for="newsletter_msg_sender"><?php _e('Sender email:', 'eelv_lettreinfo' ) ?></label>
                </td><td>
                <input  type="text" name="newsletter_msg_sender"  size="60"  id="newsletter_msg_sender"  value="<?=$msg_sender?>" class="wide">
                </td>
              </tr>
              <tr>
                <td width="30%">
                  <label for="newsletter_msg_suscribe_title"><?php _e('Subscribe subject:', 'eelv_lettreinfo' ) ?></label>
                </td><td>
                <input  type="text" name="newsletter_msg_suscribe_title"  size="60"  id="newsletter_msg_suscribe_title"  value="<?=$msg_suscribe_title?>" class="wide">
                </td>
              </tr>
              <tr>
                <td width="30%">
                  <label for="newsletter_msg_suscribe"><?php _e('Subscribe Message:', 'eelv_lettreinfo' ) ?></label>
                </td><td>
                <textarea  name="newsletter_msg_suscribe" id="newsletter_msg_suscribe"><?=$msg_suscribe;?></textarea>
                </td>
              </tr>
              <tr>
                <td width="30%">
                  <label for="newsletter_msg_unsuscribe_title"><?php _e('Unsubscribe subject:', 'eelv_lettreinfo' ) ?></label>
                </td><td>
                <input  type="text" name="newsletter_msg_unsuscribe_title"  size="60"  id="newsletter_msg_unsuscribe_title"  value="<?=$msg_unsuscribe_title?>" class="wide">
                </td>
              </tr>
              <tr>
                <td width="30%">
                  <label for="newsletter_msg_unsuscribe"><?php _e('Unsubscribe message:', 'eelv_lettreinfo' ) ?></label>
                </td><td>
                <textarea  name="newsletter_msg_unsuscribe" id="newsletter_msg_unsuscribe"><?=$msg_unsuscribe;?></textarea>
                </td>
              </tr>

               <tr>
                <td colspan="2">
                  <input type='submit' value='<?php _e('Save options', 'eelv_lettreinfo' ) ?>' class="button-primary"/>
                </td>
              </tr>
            </tbody>
          </table>
          <table class="widefat" style="margin-top: 1em;">
            <thead>
              <tr>
                <th scope="col"><?php _e('Help', 'eelv_lettreinfo' ) ?></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  <p><a href="https://ecolosites.eelv.fr/tag/newsletter/" target="_blank">EELV Newsletter</a></p>
                  <p><?php _e('Shortcods used:', 'eelv_lettreinfo' ) ?></p>
                  <ul>
                    <li><?php _e('Insert subscription form in a page :', 'eelv_lettreinfo' ) ?><strong>[eelv_news_form]</strong></li>
                  	<li><?php _e('Insert answer form in a page :','eelv_lettreinfo')?> <strong>[nl_reply_form]</strong></li>
                  	<li><?php _e('Insert answer link in a newsletter :','eelv_lettreinfo')?> <strong>[nl_reply_link rep="rep" val="val"]</strong></li>
                  </ul>
                  <p><?php _e('Skins shortcodes', 'eelv_lettreinfo' ) ?></p>
                  <ul>
                    <li><?php _e('Insert newsletter content in a skin :', 'eelv_lettreinfo' ) ?><strong>[newsletter]</strong></li>
                    <li><?php _e('Insert an unsubscribe link :', 'eelv_lettreinfo' ) ?><strong>[desinsc_url]</strong></li>
                  </ul>
                  <p><?php _e('Legend of sending symbols:', 'eelv_lettreinfo' ) ?></p>
                  <ul>
                  <li><img src="<?=$this->newsletter_plugin_url?>/eelv-newsletter/img/-1.jpg"/> <?php _e('Invalid email', 'eelv_lettreinfo' ) ?></li>
                  <li><img src="<?=$this->newsletter_plugin_url?>/eelv-newsletter/img/0.jpg"/> <?php _e('Sending failed', 'eelv_lettreinfo' ) ?></li>
                  <li><img src="<?=$this->newsletter_plugin_url?>/eelv-newsletter/img/1.jpg"/> <?php _e('Newsletter successfully sent', 'eelv_lettreinfo' ) ?></li>
                  <li><img src="<?=$this->newsletter_plugin_url?>/eelv-newsletter/img/2.jpg"/> <?php _e('Address on the list of unsubscribed:', 'eelv_lettreinfo' ) ?></li>
                  <li><img src="<?=$this->newsletter_plugin_url?>/eelv-newsletter/img/3.jpg"/> <?php _e('Email has been readen', 'eelv_lettreinfo' ) ?></li>
                  </ul>
                </td></tr></tbody></table>
        </form>
      </div>
      <?php

      $this->newsletter_checkdb();
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
	  $template = $this->eelv_lettrinfo_locate_plugin_template($templates);
  }
  return $template;
}
function eelv_lettrinfo_get_custom_single_template($template){
  global $wp_query;
  $object = $wp_query->get_queried_object();
  if ( 'newsletter_archive' == $object->post_type ) {
	$templates = array('single-' . $object->post_type . '.php', 'single.php');
	$template = $this->eelv_lettrinfo_locate_plugin_template($templates);
  }
  return $template;
}
////////////////////////////////////////////////////////////////////////////////////////////////////// WIDGET
	function register_widget(){
		if(class_exists('EELV_NL_Archives_Widget'))
			register_widget('EELV_NL_Archives_Widget');
	}
   function widget_eelv_lettreinfo_side($params) {
  $this->eelv_li_xs_title= get_option('eelv_li_xs_title')?>
      <?php echo $params['before_widget']; ?>
      <?php echo $params['before_title'];?>
      <?php echo  $this->eelv_li_xs_title; ?>
      <?php echo $params['after_title'];?>
      <?php $this->get_news_form('widget'); ?>
      <?php echo $params['after_widget'];?>
      <?php
   }
function widget_eelv_lettreinfo_insc_control(){
   if( isset($_POST['eelv_li_xs_title']) ){
	    update_option('eelv_li_xs_title', stripslashes($_POST['eelv_li_xs_title']));
	    update_option('eelv_li_xs_archives', stripslashes($_POST['eelv_li_xs_archives']));
	    update_option('eelv_li_xs_options', stripslashes($_POST['eelv_li_xs_options']));
	    update_option('eelv_li_xs_formclass', stripslashes($_POST['eelv_li_xs_formclass']));
	    update_option('eelv_li_xs_textclass', stripslashes($_POST['eelv_li_xs_textclass']));
	    update_option('eelv_li_xs_buttonclass', stripslashes($_POST['eelv_li_xs_buttonclass']));
	    update_option('eelv_li_xs_labeltext', stripslashes($_POST['eelv_li_xs_labeltext']));
	    update_option('eelv_li_xs_texttext', stripslashes($_POST['eelv_li_xs_texttext']));
	    update_option('eelv_li_xs_buttontext', stripslashes($_POST['eelv_li_xs_buttontext']));
	    _e('Options saved', 'eelv_lettreinfo' );
   }
                      $this->eelv_li_xs_title= get_option('eelv_li_xs_title');
                      $this->eelv_li_xs_options = get_option('eelv_li_xs_options',0);
                      $this->eelv_li_xs_archives = get_option('eelv_li_xs_archives',0);
                      $this->eelv_li_xs_formclass = get_option('eelv_li_xs_formclass','');
                      $this->eelv_li_xs_textclass = get_option('eelv_li_xs_textclass','');
                      $this->eelv_li_xs_buttonclass = get_option('eelv_li_xs_buttonclass','');
					  $this->eelv_li_xs_buttontext = get_option('eelv_li_xs_buttontext','ok');
					$this->eelv_li_xs_labeltext = get_option('eelv_li_xs_labeltext',__('Subscribe our newsletter', 'eelv_lettreinfo'));
					$this->eelv_li_xs_texttext = get_option('eelv_li_xs_texttext',__('Newsletter : your email address', 'eelv_lettreinfo'));

                      ?>
      <p><label><?php _e('Title', 'eelv_lettreinfo' ) ?><br/>
        <input type='text' name='eelv_li_xs_title' value="<?=$this->eelv_li_xs_title?>"/></label>
      </p>

      <p><label><?php _e('Label text', 'eelv_lettreinfo' ) ?><br/>
        <input type='text' name='eelv_li_xs_labeltext' value="<?=$this->eelv_li_xs_labeltext?>"/></label>
      </p>

      <p><label><?php _e('Input text', 'eelv_lettreinfo' ) ?><br/>
        <input type='text' name='eelv_li_xs_texttext' value="<?=$this->eelv_li_xs_texttext?>"/></label>
      </p>

      <p><label><?php _e('Button text', 'eelv_lettreinfo' ) ?><br/>
        <input type='text' name='eelv_li_xs_buttontext' value="<?=$this->eelv_li_xs_buttontext?>"/></label>
      </p>

      <p><label'><?php _e('Hide subscribe/unsubscribe options', 'eelv_lettreinfo' ) ?><br/>
        <select name='eelv_li_xs_options'>
            <option value="0" <?php if($this->eelv_li_xs_options==0){ echo'selected'; } ?>><?php _e('No', 'eelv_lettreinfo' ) ?></option>
            <option value="1" <?php if($this->eelv_li_xs_options==1){ echo'selected'; } ?>><?php _e('Yes', 'eelv_lettreinfo' ) ?></option>
        </select>
        </label>
      </p>
      <p><label><?php _e('Hide archives link', 'eelv_lettreinfo' ) ?><br/>
        <select name='eelv_li_xs_archives'>
            <option value="0" <?php if($this->eelv_li_xs_archives==0){ echo'selected'; } ?>><?php _e('No', 'eelv_lettreinfo' ) ?></option>
            <option value="1" <?php if($this->eelv_li_xs_archives==1){ echo'selected'; } ?>><?php _e('Yes', 'eelv_lettreinfo' ) ?></option>
        </select>
        </label>
      </p>
      <h5><?php _e('Advanced options', 'eelv_lettreinfo' ) ?></h5>
      <p><label><?php _e('Form class', 'eelv_lettreinfo' ) ?><br/>
        <input type='text' name='eelv_li_xs_formclass' value="<?=$this->eelv_li_xs_formclass?>"/></label>
      </p>
      <p><label><?php _e('Text class', 'eelv_lettreinfo' ) ?><br/>
        <input type='text' name='eelv_li_xs_textclass' value="<?=$this->eelv_li_xs_textclass?>"/></label>
      </p>
      <p><label><?php _e('Button class', 'eelv_lettreinfo' ) ?><br/>
        <input type='text' name='eelv_li_xs_buttonclass' value="<?=$this->eelv_li_xs_buttonclass?>"/></label>
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
  	<select name="eelv_nl_convert_id" id="eelv_nl_convert_id">
	<?php foreach($IDS as $item_id){ ?>
		<option value="<?=$item_id?>"><?=get_the_title($item_id);?></option>
	<?php } ?>
	</select>
	<hr/>
    <p><label for="eelv_nl_convert_title"><input type="checkbox" id="eelv_nl_convert_title"  value="1"/> <?php _e("Add title to content",'eelv_lettreinfo'); ?></label></p>
    <p><label for="eelv_nl_convert_share"><input type="checkbox" id="eelv_nl_convert_share"  value="1"/> <?php _e("Add share links",'eelv_lettreinfo'); ?></label></p>


    <p><a id="eelv_nl_convert_a" class="button"> <?php _e("Preview and send",'eelv_lettreinfo'); ?></a></p>
<?php
  }else{
	_e("No skin available",'eelv_lettreinfo');
  }
}



}
//End of the EELV_newsletter Class
