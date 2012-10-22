<?php
/**
 * @package WordPress
 * @subpackage Genese
 */
get_header(); ?>
<section id="primary" class="eelvnl">
			
			<?php wp_reset_query();?>
			<?php while ( have_posts() ) : the_post(); ?>
		
			<article>
					
					
					<div class="eelvnl_content">
                    <div class="eelvnl_wrapper">
                    
                	<h1 class="entry-title"><?php the_title(); ?></h1>
					
					<?php //$template =  get_post(get_post_meta(get_the_ID(),'nl_template',true)); ?>
                    <?php echo nl_content(get_the_ID()); //str_replace('[newsletter]',nl2br(trim(get_the_content())),$template->post_content); ?>
					</div>
                    
                    </div>

				
			</article>
            <?php endwhile; ?>

</section><!-- #primary -->


<?php get_footer(); ?>