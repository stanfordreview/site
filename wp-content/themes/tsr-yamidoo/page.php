<?php get_header(); ?>
   <div class="post">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

		<div class="post" id="post-<?php the_ID(); ?>">
	 
             	 
			<h3><?php the_title(); ?></h3>
			<?php the_content('<p>Read the rest of this entry &raquo;</p>'); ?>

			<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
	 

	 
 </div>
 
	<?php endwhile; else: ?>

		<p>Sorry, no posts matched your criteria.</p>

<?php endif; ?>
</div>

<div class="<?php echo $post->post_name; ?>">
<?php get_sidebar(); ?>
</div>



<?php get_footer(); ?> 
