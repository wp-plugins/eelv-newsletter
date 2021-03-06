<?php
class EELV_NL_Subscribe_Widget extends WP_Widget {
    public $defaults;
    function __construct() {
        global $eelv_newsletter;
        $this->defaults = $eelv_newsletter->form_defaults;
  	parent::__construct(false, __( 'Subscribe newsletter', 'eelv-newsletter' ),array('description'=>__( 'Form / unsubscribe and archives NewsLetter', 'eelv-newsletter' )));
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
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'eelv-newsletter' ) ?></label>
          <input class="widefat" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" value="<?php echo $instance['title']; ?>"/>
        </p>

        <p><label for="<?php echo $this->get_field_id('label'); ?>"><?php _e('Label text', 'eelv-newsletter' ) ?></label>
          <input class="widefat" name="<?php echo $this->get_field_name('label'); ?>" id="<?php echo $this->get_field_id('label'); ?>" value="<?php echo $instance['label']; ?>"/>
        </p>

        <p><label for="<?php echo $this->get_field_id('input'); ?>"><?php _e('Input text', 'eelv-newsletter' ) ?></label>
          <input class="widefat" name="<?php echo $this->get_field_name('input'); ?>" id="<?php echo $this->get_field_id('input'); ?>" value="<?php echo $instance['input']; ?>"/>
        </p>

        <p><label for="<?php echo $this->get_field_id('button'); ?>"><?php _e('Button text', 'eelv-newsletter' ) ?></label>
          <input class="widefat" name="<?php echo $this->get_field_name('button'); ?>" id="<?php echo $this->get_field_id('button'); ?>" value="<?php echo $instance['button']; ?>"/>
        </p>

        <hr>
        <p>
          <input type="checkbox" name="<?php echo $this->get_field_name('options'); ?>" id="<?php echo $this->get_field_id('options'); ?>" value="1" <?php checked(1, $instance['options'], true); ?>/>
          <label for="<?php echo $this->get_field_id('options'); ?>"><?php _e('Show subscribe/unsubscribe options', 'eelv-newsletter' ) ?></label>
        </p>
        <p>
          <input type="checkbox" name="<?php echo $this->get_field_name('archives'); ?>" id="<?php echo $this->get_field_id('options'); ?>" value="1" <?php checked(1, $instance['archives'], true); ?>/>
          <label for="<?php echo $this->get_field_id('archives'); ?>"><?php _e('Show archives link', 'eelv-newsletter' ) ?></label>
        </p>

        <p><label for="<?php echo $this->get_field_id('group'); ?>"><?php _e('Group of destination', 'eelv-newsletter' ) ?></label>
          <select class="widefat" name="<?php echo $this->get_field_name('group'); ?>" id="<?php echo $this->get_field_id('group'); ?>">
              <?php foreach($GRPS as $groupe): ?>
              <option value="<?php echo $groupe->id; ?>" <?php selected($groupe->id, $instance['group'], true); ?>><?php echo $groupe->nom; ?></option>
              <?php endforeach; ?>
          </select>
        </p>

        <h5><?php _e('Advanced options', 'eelv-newsletter' ) ?></h5>

        <p><label for="<?php echo $this->get_field_id('css_template'); ?>"><?php _e('CSS template', 'eelv-newsletter' ) ?></label>
          <select class="widefat" name="<?php echo $this->get_field_name('css_template'); ?>" id="<?php echo $this->get_field_id('css_template'); ?>">
              <option value="none" <?php selected('none', $instance['css_template'], true); ?>><?php _e('None', 'eelv-newsletter' ) ?></option>
              <option value="default" <?php selected('default', $instance['css_template'], true); ?>><?php _e('Default', 'eelv-newsletter' ) ?></option>
              <option value="bootstrap" <?php selected('bootstrap', $instance['css_template'], true); ?>>Bootstrap</option>
          </select>
        </p>
        <p><label for="<?php echo $this->get_field_id('form_class'); ?>"><?php _e('Additional form class', 'eelv-newsletter' ) ?></label>
          <input class="widefat" name="<?php echo $this->get_field_name('form_class'); ?>" id="<?php echo $this->get_field_id('form_class'); ?>" value="<?php echo $instance['form_class']; ?>"/>
        </p>
        <p><label for="<?php echo $this->get_field_id('label_class'); ?>"><?php _e('Additional label class', 'eelv-newsletter' ) ?></label>
          <input class="widefat" name="<?php echo $this->get_field_name('label_class'); ?>" id="<?php echo $this->get_field_id('label_class'); ?>" value="<?php echo $instance['label_class']; ?>"/>
        </p>
        <p><label for="<?php echo $this->get_field_id('input_class'); ?>"><?php _e('Additional text class', 'eelv-newsletter' ) ?></label>
          <input class="widefat" name="<?php echo $this->get_field_name('input_class'); ?>" id="<?php echo $this->get_field_id('input_class'); ?>" value="<?php echo $instance['input_class']; ?>"/>
        </p>
        <p><label for="<?php echo $this->get_field_id('button_class'); ?>"><?php _e('Additional button class', 'eelv-newsletter' ) ?></label>
          <input class="widefat" name="<?php echo $this->get_field_name('button_class'); ?>" id="<?php echo $this->get_field_id('button_class'); ?>" value="<?php echo $instance['button_class']; ?>"/>
        </p>

        <hr>
        <p><label for="<?php echo $this->get_field_id('form_color'); ?>"><?php _e('Form color', 'eelv-newsletter' ) ?></label>
          <input type="color" name="<?php echo $this->get_field_name('form_color'); ?>" id="<?php echo $this->get_field_id('form_color'); ?>" value="<?php echo $instance['form_color']; ?>"/>
          <label for="<?php echo $this->get_field_id('form_transparent'); ?>">
          <input type="checkbox" name="<?php echo $this->get_field_name('form_transparent'); ?>" id="<?php echo $this->get_field_id('form_transparent'); ?>" value="1" <?php checked(1, $instance['form_transparent'], true); ?>/>
          <?php _e('Transparent', 'eelv-newsletter' ) ?></label>
        </p>
        <p><label for="<?php echo $this->get_field_id('text_color'); ?>"><?php _e('Input color', 'eelv-newsletter' ) ?></label>
          <input type="color" name="<?php echo $this->get_field_name('text_color'); ?>" id="<?php echo $this->get_field_id('text_color'); ?>" value="<?php echo $instance['text_color']; ?>"/>
          <label for="<?php echo $this->get_field_id('text_color_auto'); ?>">
          <input type="checkbox" name="<?php echo $this->get_field_name('text_color_auto'); ?>" id="<?php echo $this->get_field_id('text_color_auto'); ?>" value="1" <?php checked(1, $instance['text_color_auto'], true); ?>/>
          <?php _e('Inherit', 'eelv-newsletter' ) ?></label>
        </p>
        <p><label for="<?php echo $this->get_field_id('options_color'); ?>"><?php _e('Options color', 'eelv-newsletter' ) ?></label>
          <input type="color" name="<?php echo $this->get_field_name('options_color'); ?>" id="<?php echo $this->get_field_id('options_color'); ?>" value="<?php echo $instance['options_color']; ?>"/>
          <label for="<?php echo $this->get_field_id('options_transparent'); ?>">
          <input type="checkbox" name="<?php echo $this->get_field_name('options_transparent'); ?>" id="<?php echo $this->get_field_id('options_transparent'); ?>" value="1" <?php checked(1, $instance['options_transparent'], true); ?>/>
          <?php _e('Transparent', 'eelv-newsletter' ) ?></label>
        </p>
        <p><label for="<?php echo $this->get_field_id('options_text_color'); ?>"><?php _e('Options text color', 'eelv-newsletter' ) ?></label>
          <input type="color" name="<?php echo $this->get_field_name('options_text_color'); ?>" id="<?php echo $this->get_field_id('options_text_color'); ?>" value="<?php echo $instance['options_text_color']; ?>"/>
          <label for="<?php echo $this->get_field_id('options_color_auto'); ?>">
          <input type="checkbox" name="<?php echo $this->get_field_name('options_color_auto'); ?>" id="<?php echo $this->get_field_id('options_color_auto'); ?>" value="1" <?php checked(1, $instance['options_color_auto'], true); ?>/>
          <?php _e('Inherit', 'eelv-newsletter' ) ?></label>
        </p>
       <?php
   }

}

