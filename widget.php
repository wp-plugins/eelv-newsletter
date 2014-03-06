<?php
class EELV_NL_Archives_Widget extends WP_Widget {
   function EELV_NL_Archives_Widget() {
  	  parent::WP_Widget(false, __( 'Newsletter Archives', 'eelv_lettreinfo' ),array('description'=>__( 'Displays a list of last sent newsletters', 'eelv_lettreinfo' )));
   }
   function widget($args, $instance) {
       extract( $args );
	   $title = isset($instance['title'])?$instance['title']:'';
       $number = isset($instance['number'])?$instance['number']:5;
       $type = isset($instance['type'])?$instance['type']:0;
       $is_desc = isset($instance['is_desc'])?$instance['is_desc']:0;
 		
		$arg=array(
			'post_type'=>'newsletter_archive',
			'posts_per_page'=>$number
		);
		if($type>0){
			$arg['tax_query'] = array(
				array(
					'taxonomy' => 'newsletter_archives_types',
					'terms' => $type
				)
			);
		}
		$tf=get_option('date_format');
		
		$qu=new WP_Query($arg);
		if($qu->have_posts()){
			echo $args['before_widget'];
			if(!empty($title)){
				echo $args['before_title'];
				echo $title;
				echo $args['after_title'];
			}		
			while($qu->have_posts()){
				$qu->the_post();
				?>
				<div>
					<a href="<?php the_permalink(); ?>"><h4><?php the_title(); ?></h4></a>
					<time datetime="<?php the_time('c'); ?>"><?php the_time($tf); ?></time>
					<?php if($is_desc==1) the_excerpt(); ?>
				</div>
				<?php	
			}
			echo $args['after_widget'];
		}	
   }
   
   function update($new_instance, $old_instance) {
       return $new_instance;
   }

   function form($instance) { 	    
	   $title = isset($instance['title'])?$instance['title']:'';
       $number = isset($instance['number'])?$instance['number']:5;
       $type = isset($instance['type'])?$instance['type']:0;
       $is_desc = isset($instance['is_desc'])?$instance['is_desc']:0;
      
       ?>
       <input type="hidden" id="<?php echo $this->get_field_id('title'); ?>-title" value="<?php echo $title; ?>">
       <p>
       <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title','eelv_lettreinfo'); ?>
       <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
       </label>
       </p>       
     
       <p style="margin-top:10px;">
       <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number','eelv_lettreinfo'); ?>
       <input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="number" value="<?php echo $number; ?>" />
       </label>
       </p>
       
       <p>
       	<label for="<?php echo $this->get_field_id('cat'); ?>"><?php _e('Type','eelv_lettreinfo'); ?>
       	<select  id="<?php echo $this->get_field_id('type'); ?>" name="<?php echo $this->get_field_name('type'); ?>">
       		<option value='0'><?php _e('All','eelv_lettreinfo') ?></option>
       <?php 
	   	$types = get_terms( 'newsletter_archives_types', array(
		 	'hierarchical'=>false,
		 	'hide_empty'=>false,
		 ) );
		foreach($types as $typ){ ?>
       	<option value="<?=$typ->term_id?>" <?php if($typ->term_id==$type){ echo'selected';} ?>><?=$typ->name?> (<?=$typ->count?>)</option>
       <?php  }  ?>
       </select>
       </label>
       </p>
       
       
       <p>
       <label for="<?php echo $this->get_field_id('is_desc'); ?>"><?php _e('Display content','eelv_lettreinfo'); ?>
       <select id="<?php echo $this->get_field_id('is_desc'); ?>" name="<?php echo $this->get_field_name('is_desc'); ?>">
       	<option value="0" <?=$is_desc==0?'selected':''?>><?php _e('No','eelv_lettreinfo'); ?></option>
       	<option value="1" <?=$is_desc==1?'selected':''?>><?php _e('Yes','eelv_lettreinfo'); ?></option>
       </select>
       </label>
       </p>
       
       <?php
   }

}

