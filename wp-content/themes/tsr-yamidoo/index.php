<?php
global $options;
foreach ($options as $value) {
    if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
}

$seen_articles = array();
?>
<?php get_header(); ?>
<?php if ( $paged < 2) { ?>
<div id="feature">
 <?php 


            $catid5 = $ft_featured_category_5;
            
            
            $cat5 = get_category($catid5,false);
            
            
            $catlink5 = get_category_link($catid5);
            
            
            $breaking_cat5 = "cat=$catid5";
            
 
        ?>
 <div id="headline">  <!-- Modified Featured Content Box from The Stars Theme by www.premiumwp.com.  -->
	     <div id="headline-content" class="glidecontentwrapper">
					<?php $headline = new WP_Query('showposts=4&' . $breaking_cat5 ); while($headline->have_posts()) : $headline->the_post(); ?>
		 <div class="clearfix glidecontent">
     <?php $photo = tsr_photo_path($post); 
          if ($photo){  ?> 
            <img class="teaser-image" src="<?php bloginfo('template_directory'); ?>/scripts/timthumb.php?src=<?php echo $photo; ?>&amp;w=398&amp;h=233&amp;zc=1" alt="<?php the_title(); ?>" />
          
          <?php }	else { ?>
            <img class="teaser-image" src="<?php bloginfo('template_directory'); ?>/images/image-blank-headline.jpg" alt="<?php the_title(); ?>" />
          <?php }	?>

						<div class="teaser-main">
							<div class="teaser-wrap">
								<div class="overlay"></div>
								<h1 class="teaser-title"><a href="<?php the_permalink(); ?>" title="<?php printf(__( 'Read %s', 'wpbx' ), wp_specialchars(get_the_title(), 1)) ?>"><?php the_title(); ?></a></h1>
								<p class="teaser-text"><?php echo get_post_meta($post->ID, 'excerpt_slidey', true); ?></p>
								
							</div>
						</div>  

					</div>
					<?php endwhile; ?>
				</div><!-- big-image end -->

<div id="teaser" class="glidecontenttoggler">
<?php $teaser_small = new WP_Query('showposts=4&' . $breaking_cat5 ); while($teaser_small->have_posts()) : $teaser_small->the_post(); ?>
<a href="#" class="toc"><span class="togglerwrap"><span class="togglercontent clearfix">
	<?php $seen_articles[] = $post->ID; ?>
	
    <?php $photo = tsr_photo_path($post); 
          if ($photo){  ?>
				<img class="teaser-image-small" src="<?php bloginfo('template_directory'); ?>/scripts/timthumb.php?src=<?php echo $photo; ?>&amp;w=90&amp;h=60&amp;zc=1" alt="<?php the_title(); ?>" />
							<?php }	else { ?>
				<img class="teaser-image-small" src="<?php bloginfo('template_directory'); ?>/images/image-blank-small.jpg" alt="<?php the_title(); ?>" /><?php }	?></span></span></a>
					<?php endwhile; ?>
				<a href="#" class="next"></a>
        </div><!-- thumbs-feature -->
			</div><!-- #headline -->
 
			
      </div>
          
          
          
    <div id="featured-articles">
      <?php query_posts('showposts=2&cat=' . get_cat_ID('Featured')); ?> <!-- Shows the most recent post (featured article) -->
				<?php while (have_posts()) : the_post(); ?>
					<?php $seen_articles[] = $post->ID; ?>
					<div class="featured-article"> 
						<h1><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h1>
						  	<small>By <?php coauthors_posts_links(); ?> &mdash; <?php the_time('F j, Y') ?> <!-- &mdash; <?php the_category(', ') ?> --></small> 		
							<p><?php echo get_post_meta($post->ID, 'excerpt_featured', true); ?></p>
					</div>
					<?php endwhile; ?>
	</div> <!-- end featured articles -->
       
    <div class="clear"></div>
      
    <?php	include(TEMPLATEPATH . '/blocks.php'); ?> <!-- calling categories boxes (4) -->
    
    <?php wp_reset_query(); ?>	

<?php } // if pages=2 ?>
  <div id="articles" class="frontpage">  <!-- shows latest articles -->
	
	<h3>Recent articles</h3>
	<div class="clear"></div>
	
   <?php global $wp_query;
         $args = array_merge(array('post__not_in' =>$seen_articles, 'showposts'=>8), $wp_query->query);
         query_posts($args); ?>
   <?php if (have_posts()) : ?>
     <?php while ($wp_query->have_posts()) : $wp_query->the_post(); ?>	
       

        <div class="article">
            <h1><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>	
        
		<?php $photo = get_post_meta($post->ID, "$ft_cf_photo", true); 
          if (false && $photo){  ?><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><img class="teaser-image-small" src="<?php bloginfo('template_directory'); ?>/scripts/timthumb.php?src=<?php echo $photo; ?>&amp;w=190&amp;h=111&amp;zc=1" alt="<?php the_title_attribute(); ?>" /></a><?php } ?>
               
        <small>By <?php coauthors_posts_links(); ?> &mdash; <?php the_time('F j, Y') ?> &mdash; <?php tsr_the_section($post) ?></small>


             <?php the_excerpt(); ?>
             
        
       </div> <!-- end article -->
         	
         	<?php endwhile; ?>
          <?php wp_reset_query(); ?>	
          
          
            <div id="more">
			   <?php $args = array('post__not_in' =>$seen_articles, 'showposts'=>7, 'offset'=>8);
			         query_posts($args); ?>            
              <ul>
              <?php while (have_posts()) : the_post(); ?>
                <li><div class="more_left"><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></div> <div class="more_right"><?php the_time('  F j, Y'); ?></div></li>
          	<?php endwhile; ?>
               </ul>
             </div>
      <?php wp_reset_query(); ?>
	
	<div id="archives_link">
		<br/>
		<a href="/archives">Older articles &raquo;</a>
	</div>


    </div>


     
    	 
<?php endif; ?>
<?php get_sidebar(); ?>

<?php get_footer(); ?>
