<?php
global $options;
foreach ($options as $value) {
    if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
}

        global $wp_query;
        $curauth = $wp_query->get_queried_object();


?>
<?php get_header(); ?>
 <div id="articles">
          
		<?php if (true || have_posts()) : ?>

		<?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
	 
		
	<?php the_post() ?>

			<h3 class="page-title"><?php echo $curauth->display_name; ?></h3>
		 
<?php rewind_posts() ?>	
 
<div class="article">
<?php echo get_avatar( $curauth->user_email, '65' );?> 
 
<p><?php echo $curauth->user_description; ?></p>

<div class="clear"></div>

</div>
		<?php while (have_posts()) : the_post(); ?>
		 <div class="article"  id="post-<?php the_ID(); ?>">
 <h1><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>	
<?php $photo = get_post_meta($post->ID, "$ft_cf_photo", true); 
          if ($photo){  ?>               <img class="teaser-image-small" src="<?php bloginfo('template_directory'); ?>/scripts/timthumb.php?src=<?php echo $photo; ?>&amp;w=154&amp;h=90&amp;zc=1" alt="<?php the_title_attribute(); ?>" /><?php } ?>
		 <small>By <?php coauthors_posts_links(); ?> &mdash; <?php the_time('F j, Y') ?> &mdash; <?php tsr_the_section($post) ?></small>

             <?php the_excerpt(); ?>
            </div> <!-- end article -->
         	<?php endwhile; ?>
<div class="navigation"><?php if (function_exists('wp_pagenavi')) { wp_pagenavi(); } else { ?>
        <div class="floatleft"><?php next_posts_link( __('&laquo; Older Entries', '') ); ?></div>
        <div class="floatright"><?php previous_posts_link( __('Newer Entries &raquo;', '') ); ?></div>
    
    <?php } ?>
    </div>
    </div>
 

	<?php else : ?>

		<h2>Not Found</h2>
		<?php include (TEMPLATEPATH . '/searchform.php'); ?>

	<?php endif; ?>
 
<?php get_sidebar(); ?>

<?php get_footer(); ?>
