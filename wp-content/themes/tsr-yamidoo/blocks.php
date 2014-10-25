<div class="clear"></div>
      <div class="hr"></div>
      
        <div id="columns">
        
        <?php 

            $catid1 = $ft_featured_category_1;
            $catid2 = $ft_featured_category_2;
            $catid3 = $ft_featured_category_3;
            $catid4 = $ft_featured_category_4;
            
            $cat1 = get_category($catid1,false);
            $cat2 = get_category($catid2,false);
            $cat3 = get_category($catid3,false);
            $cat4 = get_category($catid4,false);
            
            $catlink1 = get_category_link($catid1);
            $catlink2 = get_category_link($catid2);
            $catlink3 = get_category_link($catid3);
            $catlink4 = get_category_link($catid4);
            
            $breaking_cat1 = "cat=$catid1";
            $breaking_cat2 = "cat=$catid2";
            $breaking_cat3 = "cat=$catid3";
            $breaking_cat4 = "cat=$catid4";
 
			
        ?>
        <div class="column">
        <?php 			
        query_posts('showposts=1&' . $breaking_cat1 );
			 while (have_posts()) : the_post(); ?>
          <h4><?php echo tsr_section_name_from_block_name($cat1->name) ?></h4>
			<?php $seen_articles[] = $post->ID; ?>
		
		    <?php $photo = tsr_photo_path($post); 
          if ($photo){  ?><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><img class="teaser-image-small" src="<?php bloginfo('template_directory'); ?>/scripts/timthumb.php?src=<?php echo $photo; ?>&amp;w=218&amp;h=127&amp;zc=1" alt="<?php the_title(); ?>" /></a><?php } ?>

          <h1><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h1>
		<div class="byline"><small>By <?php coauthors_posts_links(); ?> &mdash; <?php the_time('M j, Y') ?></small></div>
			<p><?php echo get_post_meta($post->ID, 'excerpt_block', true); ?></p>
			
          <?php endwhile; ?>   </div>  
        
        <div class="column">
        <?php 			
        query_posts('showposts=1&' . $breaking_cat2 );
			 while (have_posts()) : the_post(); ?>
          <h4><?php echo tsr_section_name_from_block_name($cat2->name) ?></h4>
			<?php $seen_articles[] = $post->ID; ?>

		    <?php $photo = tsr_photo_path($post); 
          if ($photo){  ?><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><img class="teaser-image-small" src="<?php bloginfo('template_directory'); ?>/scripts/timthumb.php?src=<?php echo $photo; ?>&amp;w=190&amp;h=111&amp;zc=1" alt="<?php the_title(); ?>" /></a><?php } ?>

          <h1><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h1>

		<div class="byline"><small>By <?php coauthors_posts_links(); ?> &mdash; <?php the_time('M j, Y') ?></small></div>
			<p><?php echo get_post_meta($post->ID, 'excerpt_block', true); ?></p>
          <?php endwhile; ?>   </div> 
  
        <div class="column">
        <?php 			
        query_posts('showposts=1&' . $breaking_cat3 );
			 while (have_posts()) : the_post(); ?>
          <h4><?php echo tsr_section_name_from_block_name($cat3->name) ?></h4>
			<?php $seen_articles[] = $post->ID; ?>

		    <?php $photo = tsr_photo_path($post); 
          if ($photo){  ?><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><img class="teaser-image-small" src="<?php bloginfo('template_directory'); ?>/scripts/timthumb.php?src=<?php echo $photo; ?>&amp;w=190&amp;h=111&amp;zc=1" alt="<?php the_title(); ?>" /></a><?php } ?>

          <h1><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h1>
		<div class="byline"><small>By <?php coauthors_posts_links(); ?> &mdash; <?php the_time('M j, Y') ?></small></div>
			<p><?php echo get_post_meta($post->ID, 'excerpt_block', true); ?></p>
          <?php endwhile; ?>   </div> 

            <div class="column_last">
        
        <?php 			
        query_posts('showposts=1&' . $breaking_cat4 );
			 while (have_posts()) : the_post(); ?>
          <h4><?php echo Blog ?></h4>
			<?php $seen_articles[] = $post->ID; ?>

		    <?php $photo = tsr_photo_path($post); 
          if ($photo){  ?><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><img class="teaser-image-small" src="<?php bloginfo('template_directory'); ?>/scripts/timthumb.php?src=<?php echo $photo; ?>&amp;w=190&amp;h=111&amp;zc=1" alt="<?php the_title(); ?>" /></a><?php } ?>

          <h1><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h1>
		<div class="byline"><small>By <?php coauthors_posts_links(); ?> &mdash; <?php the_time('M j, Y') ?></small></div>
			<p><?php echo get_post_meta($post->ID, 'excerpt_block', true); ?></p>
         <?php endwhile; ?> 
  </div>  
        
       
      </div> <!-- end columns -->