<?php
global $options;
foreach ($options as $value) {
    if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
}
?>
<?php get_header(); ?>



 <div id="articles">
          
		<?php if (have_posts()) : ?>

		<?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
		<?php /* If this is a category archive */ if (is_category()) { ?>
		<h3><?php single_cat_title(); ?></h3>
		<?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
		<h3>Posts Tagged &#8216;<?php single_tag_title(); ?>&#8217;</h3>
		<?php /* If this is a daily archive */ } elseif (is_day()) { ?>
		<h3>Archive for <?php the_time('F jS, Y'); ?></h3>
		<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
		<h3>Archive for <?php the_time('F, Y'); ?></h3>
		<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
		<h3>Archive for <?php the_time('Y'); ?></h3>
		<?php /* If this is an author archive */ } elseif (is_author()) { ?>
		<h3>Author Archive</h3>
		<?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
		<h3>Blog Archives</h3>
		<?php } ?>
		<div class="clear"></div>
		

		<?php while (have_posts()) : the_post(); ?>
		 <div class="article"  id="post-<?php the_ID(); ?>">
			<?php $photo = tsr_photo_path($post); 
	          if ($photo){  ?><img class="teaser-image-small" src="<?php bloginfo('template_directory'); ?>/scripts/timthumb.php?src=<?php echo $photo; ?>&amp;w=154&amp;h=90&amp;zc=1" alt="<?php the_title_attribute(); ?>" /><?php } ?>
 <h1><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>	
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
