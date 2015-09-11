<?php
/*
 * ////////////////////////////////////////////////////////////////////////////////////////////////////// WIDGET
// TODO : store options in a serialized variable
    function register_widget(){
	if(class_exists('EELV_NL_Archives_Widget')){
		register_widget('EELV_NL_Archives_Widget');
	}
        if(class_exists('EELV_NL_Subscribe_Widget')){
		register_widget('EELV_NL_Subscribe_Widget');
	}
    }
    function widget_eelv_lettreinfo_side($params) {
	$this->eelv_li_xs_title= get_option('eelv_li_xs_title');
	echo $params['before_widget'];
	echo $params['before_title'];
	echo  $this->eelv_li_xs_title;
	echo $params['after_title'];
	$this->get_news_form('widget');
	echo $params['after_widget'];
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

      <?php
    }
 */
class EELV_NL_Subscribe_Widget extends WP_Widget {
    public $defaults;
    function __construct() {
        global $eelv_newsletter;
        $this->defaults = $eelv_newsletter->form_defaults;
  	parent::__construct(false, __( 'Subscribe newsletter', 'eelv_lettreinfo' ),array('description'=>__( 'Form / unsubscribe and archives NewsLetter', 'eelv_lettreinfo' )));
    }
    function EELV_NL_Subscribe_Widget(){
       $this->__construct();
    }
    function widget($args, $instance) {
        global $eelv_newsletter;
        $instance = wp_parse_args( (array) $instance, $this->defaults );
	
        echo $args['before_widget'];
        if(!empty($instance['title'])){
                echo $args['before_title'];
                echo $instance['title'];
                echo $args['after_title'];
        }		
	echo $eelv_newsletter->get_news_form($instance);
	echo $args['after_widget'];
    }
   
    function update($new_instance, $old_instance) {
       return $new_instance;
    }

    function form($instance) {
        global $eelv_newsletter;
       // Set up some default widget settings.        
        $instance = wp_parse_args( (array) $instance, $this->defaults );
        $GRPS = $eelv_newsletter->news_liste_groupes();
       ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'eelv_lettreinfo' ) ?></label>
          <input class="widefat" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" value="<?php echo $instance['title']; ?>"/>
        </p>

        <p><label for="<?php echo $this->get_field_id('label'); ?>"><?php _e('Label text', 'eelv_lettreinfo' ) ?></label>
          <input class="widefat" name="<?php echo $this->get_field_name('label'); ?>" id="<?php echo $this->get_field_id('label'); ?>" value="<?php echo $instance['label']; ?>"/>
        </p>

        <p><label for="<?php echo $this->get_field_id('input'); ?>"><?php _e('Input text', 'eelv_lettreinfo' ) ?></label>
          <input class="widefat" name="<?php echo $this->get_field_name('input'); ?>" id="<?php echo $this->get_field_id('input'); ?>" value="<?php echo $instance['input']; ?>"/>
        </p>

        <p><label for="<?php echo $this->get_field_id('button'); ?>"><?php _e('Button text', 'eelv_lettreinfo' ) ?></label>
          <input class="widefat" name="<?php echo $this->get_field_name('button'); ?>" id="<?php echo $this->get_field_id('button'); ?>" value="<?php echo $instance['button']; ?>"/>
        </p>

        <hr>
        <p>
          <input type="checkbox" name="<?php echo $this->get_field_name('options'); ?>" id="<?php echo $this->get_field_id('options'); ?>" value="1" <?php checked(1, $instance['options'], true); ?>/>
          <label for="<?php echo $this->get_field_id('options'); ?>"><?php _e('Show subscribe/unsubscribe options', 'eelv_lettreinfo' ) ?></label>
        </p>
        <p>
          <input type="checkbox" name="<?php echo $this->get_field_name('archives'); ?>" id="<?php echo $this->get_field_id('options'); ?>" value="1" <?php checked(1, $instance['archives'], true); ?>/>
          <label for="<?php echo $this->get_field_id('archives'); ?>"><?php _e('Show archives link', 'eelv_lettreinfo' ) ?></label>
        </p>
        
        <p><label for="<?php echo $this->get_field_id('group'); ?>"><?php _e('Group of destination', 'eelv_lettreinfo' ) ?></label>
          <select class="widefat" name="<?php echo $this->get_field_name('group'); ?>" id="<?php echo $this->get_field_id('group'); ?>">
              <?php foreach($GRPS as $groupe): ?>
              <option value="<?php echo $groupe->id; ?>" <?php selected($groupe->id, $instance['group'], true); ?>><?php echo $groupe->nom; ?></option>
              <?php endforeach; ?>
          </select>
        </p>
   
        <h5><?php _e('Advanced options', 'eelv_lettreinfo' ) ?></h5>
        
        <p><label for="<?php echo $this->get_field_id('css_template'); ?>"><?php _e('CSS template', 'eelv_lettreinfo' ) ?></label>
          <select class="widefat" name="<?php echo $this->get_field_name('css_template'); ?>" id="<?php echo $this->get_field_id('css_template'); ?>">
              <option value="none" <?php selected('none', $instance['css_template'], true); ?>><?php _e('None', 'eelv_lettreinfo' ) ?></option>
              <option value="default" <?php selected('default', $instance['css_template'], true); ?>><?php _e('Default', 'eelv_lettreinfo' ) ?></option>
              <option value="bootstrap" <?php selected('bootstrap', $instance['css_template'], true); ?>>Bootstrap</option>
          </select>
        </p>
        <p><label for="<?php echo $this->get_field_id('form_class'); ?>"><?php _e('Additional form class', 'eelv_lettreinfo' ) ?></label>
          <input class="widefat" name="<?php echo $this->get_field_name('form_class'); ?>" id="<?php echo $this->get_field_id('form_class'); ?>" value="<?php echo $instance['form_class']; ?>"/>
        </p>
        <p><label for="<?php echo $this->get_field_id('label_class'); ?>"><?php _e('Additional label class', 'eelv_lettreinfo' ) ?></label>
          <input class="widefat" name="<?php echo $this->get_field_name('label_class'); ?>" id="<?php echo $this->get_field_id('label_class'); ?>" value="<?php echo $instance['label_class']; ?>"/>
        </p>
        <p><label for="<?php echo $this->get_field_id('input_class'); ?>"><?php _e('Additional text class', 'eelv_lettreinfo' ) ?></label>
          <input class="widefat" name="<?php echo $this->get_field_name('input_class'); ?>" id="<?php echo $this->get_field_id('input_class'); ?>" value="<?php echo $instance['input_class']; ?>"/>
        </p>
        <p><label for="<?php echo $this->get_field_id('button_class'); ?>"><?php _e('Additional button class', 'eelv_lettreinfo' ) ?></label>
          <input class="widefat" name="<?php echo $this->get_field_name('button_class'); ?>" id="<?php echo $this->get_field_id('button_class'); ?>" value="<?php echo $instance['button_class']; ?>"/>
        </p>
        
        <hr>
        <p><label for="<?php echo $this->get_field_id('form_color'); ?>"><?php _e('Form color', 'eelv_lettreinfo' ) ?></label>
          <input type="color" name="<?php echo $this->get_field_name('form_color'); ?>" id="<?php echo $this->get_field_id('form_color'); ?>" value="<?php echo $instance['form_color']; ?>"/>
          <label for="<?php echo $this->get_field_id('form_transparent'); ?>">
          <input type="checkbox" name="<?php echo $this->get_field_name('form_transparent'); ?>" id="<?php echo $this->get_field_id('form_transparent'); ?>" value="1" <?php checked(1, $instance['form_transparent'], true); ?>/>
          <?php _e('Transparent', 'eelv_lettreinfo' ) ?></label>
        </p>
        <p><label for="<?php echo $this->get_field_id('text_color'); ?>"><?php _e('Input color', 'eelv_lettreinfo' ) ?></label>
          <input type="color" name="<?php echo $this->get_field_name('text_color'); ?>" id="<?php echo $this->get_field_id('text_color'); ?>" value="<?php echo $instance['text_color']; ?>"/>
          <label for="<?php echo $this->get_field_id('text_color_auto'); ?>">
          <input type="checkbox" name="<?php echo $this->get_field_name('text_color_auto'); ?>" id="<?php echo $this->get_field_id('text_color_auto'); ?>" value="1" <?php checked(1, $instance['text_color_auto'], true); ?>/>
          <?php _e('Inherit', 'eelv_lettreinfo' ) ?></label>
        </p>
        <p><label for="<?php echo $this->get_field_id('options_color'); ?>"><?php _e('Options color', 'eelv_lettreinfo' ) ?></label>
          <input type="color" name="<?php echo $this->get_field_name('options_color'); ?>" id="<?php echo $this->get_field_id('options_color'); ?>" value="<?php echo $instance['options_color']; ?>"/>
          <label for="<?php echo $this->get_field_id('options_transparent'); ?>">
          <input type="checkbox" name="<?php echo $this->get_field_name('options_transparent'); ?>" id="<?php echo $this->get_field_id('options_transparent'); ?>" value="1" <?php checked(1, $instance['options_transparent'], true); ?>/>
          <?php _e('Transparent', 'eelv_lettreinfo' ) ?></label>
        </p>
        <p><label for="<?php echo $this->get_field_id('options_text_color'); ?>"><?php _e('Options text color', 'eelv_lettreinfo' ) ?></label>
          <input type="color" name="<?php echo $this->get_field_name('options_text_color'); ?>" id="<?php echo $this->get_field_id('options_text_color'); ?>" value="<?php echo $instance['options_text_color']; ?>"/>
          <label for="<?php echo $this->get_field_id('options_color_auto'); ?>">
          <input type="checkbox" name="<?php echo $this->get_field_name('options_color_auto'); ?>" id="<?php echo $this->get_field_id('options_color_auto'); ?>" value="1" <?php checked(1, $instance['options_color_auto'], true); ?>/>
          <?php _e('Inherit', 'eelv_lettreinfo' ) ?></label>
        </p>
       <?php
   }

}

