<?php get_header(); ?>





   <div class="post">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

		<div class="post" id="post-<?php the_ID(); ?>">
	 
             	 
			<h1><?php the_title(); ?></h1>
    		<small>By <?php coauthors_posts_links(); ?> &mdash; <?php the_time('F j, Y') ?> &mdash; <?php tsr_the_section($post) ?> &mdash; <?php tsr_the_issue($post) ?></small><br />
			<?php the_content('<p>Read the rest of this entry &raquo;</p>'); ?>
 	<small><?php the_tags( __( '<span class="tag-links">Tags: ', 'wpbx' ), ", ", "</span>\n" ) ?></small>
	 

 <div id="socialicons">
                 <small> <ul>
                    <li><a href="http://twitter.com/home?status=Currently reading <?php the_permalink(); ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/twitter.png" alt="Tweet This!" />Tweet This</a></li>
                    <li><a href="http://digg.com/submit?phase=2&url=<?php the_permalink();?>&title=<?php the_title();?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/digg.png" alt="Digg it!" />Digg This</a></li>
                    <li><a href="http://del.icio.us/post?v=4&noui&jump=close&url=<?php the_permalink();?>&title=<?php the_title();?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/delicious.png" alt="Add to Delicious!" />Save to delicious</a></li>
                    <li><a href="http://www.stumbleupon.com/submit?url=<?php the_permalink(); ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/stumble.png" alt="Stumble it" />Stumble it</a></li>
                    <li><a href="<?php bloginfo('rss2_url'); ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/feed.png" alt="Subscribe by RSS" />RSS Feed</a></li>
                 </ul></small>
               </div> <!-- end social box-->
 </div>
<?php comments_template(); ?>
		 
 
	<?php endwhile; else: ?>

		<p>Sorry, no posts matched your criteria.</p>

<?php endif; ?>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?> 
