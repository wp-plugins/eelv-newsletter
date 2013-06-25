<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<?php wp_head(); ?>
<title><?php  /*
   * Print the <title> tag based on what is being viewed.
   */
  global $page, $paged;

  wp_title( '|', true, 'right' );

  // Add the blog name.
  bloginfo( 'name' );

  // Add the blog description for the home/front page.
  $site_description = get_bloginfo( 'description', 'display' );
  if ( $site_description && ( is_home() || is_front_page() ) )
    echo " | $site_description";

  // Add a page number if necessary:
  if ( $paged >= 2 || $page >= 2 )
    echo ' | ' . sprintf( __( 'Page %s'), max( $paged, $page ) );

  ?></title>
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<!--[if IE]>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<![endif]-->
</head>
<body id="newsletter_archive" <?php body_class(); ?>>
<div class="eelvnl">
			
			<?php wp_reset_query();?>
			<?php while ( have_posts() ) : the_post(); ?>
		
			<article>
					
					
					<div class="eelvnl_content">
                    <div class="eelvnl_wrapper">
                    
                	<h1 class="entry-title"><?php the_title(); ?></h1>
					
					<?php //$template =  get_post(get_post_meta(get_the_ID(),'nl_template',true)); ?>
                    <?php echo apply_filters('the_content',nl_content(get_the_ID())); //str_replace('[newsletter]',nl2br(trim(get_the_content())),$template->post_content); ?>
					</div>
                    
                    </div>

				
			</article>
            <?php endwhile; ?>

</div><!-- #primary -->
</body>
</html>