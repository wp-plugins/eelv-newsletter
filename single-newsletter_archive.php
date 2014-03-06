<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php  wp_title('','right'); ?></title>
<link rel="stylesheet" href="<?=plugins_url( 'mail.css' , __FILE__ )?>" type="text/css" media="all" />
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
							<?php 
							$the_content= $eelv_newsletter->nl_content(get_the_ID()); 
							
							$destinataire['name']=__('Visitor','eelv_lettreinfo');
							$destinataire['login']='';
							$destinataire['email']='*****@***.**';
							
							if(strstr($the_content,'{dest_name}')){
								$the_content=str_replace(' {dest_name}',' '.$destinataire['name'],$the_content);
								$the_content=str_replace('{dest_name}',$destinataire['name'],$the_content);
							}
							if(strstr($the_content,'{dest_login}')){
								$the_content=str_replace(' {dest_login}',' '.$destinataire['login'],$the_content);
								$the_content=str_replace('{dest_login}',$destinataire['login'],$the_content);
							}
							if(strstr($the_content,'{dest_email}')){
								$the_content=str_replace(' {dest_email}',' '.$destinataire['email'],$the_content);
								$the_content=str_replace('{dest_email}',$destinataire['email'],$the_content);
							}
							
							$the_content=apply_filters('the_content',$the_content);
							echo $the_content;
							?>
						</div>                    
                    </div>
			</article>
            <?php endwhile; ?>
</div><!-- #primary -->
</body>
</html>