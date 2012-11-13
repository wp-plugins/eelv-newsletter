<?php
/**
 * @package WordPress
 * @subpackage Genese
 */
function eelv_nl_custom_excerpt_length( $length ) {
	return 100;
}
add_filter( 'excerpt_length', 'eelv_nl_custom_excerpt_length', 999 );
get_header(); ?>
<section id="primary" class='eelvnl'>
			
				<?php /* Start the Loop */ ?>
				<?php wp_reset_query();
				query_posts(array('post_type'=>'newsletter_archive')); ?>
                <div class="eelvnl_content">
                    <div class="eelvnl_wrapper">
                    
				<ol class="posts-list">
				<?php while ( have_posts() ) : the_post(); ?>
				<li class="eelvnl_archives">
					<article id="post-<?php the_ID(); ?>">
						<a href="<?php the_permalink()?>" title="<? the_title ?>" target="_blank">
							<header class="entry-header">
								<h3 class="entry-title"><?php the_title(); ?></h3>
							</header><!-- .entry-header -->

								<div class="entry-content">
									<?php the_excerpt(); ?>
								</div><!--.entry-content-->

						</a>
					</article><!-- #post-<?php the_ID(); ?> -->
				</li>
				<?php endwhile; ?>
				</ol>
                
                </div>
                </div>
</section><!-- #primary -->
<?php get_footer(); ?>