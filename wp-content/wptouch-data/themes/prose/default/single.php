<?php get_header(); ?>

	<div id="content">
		<?php while ( wptouch_have_posts() ) { ?>

			<?php
				wptouch_the_post();
				$author = get_the_author();
			?>

			<div class="<?php wptouch_post_classes(); ?>">
				<div class="post-head-area">
					<?php prose_header_image( get_the_ID() ); ?>
					<h2 class="post-title heading-font"><?php wptouch_the_title(); ?></h2>
					<div class="post-meta">
						<?php wptouch_the_time(); ?><?php prose_the_author(); ?>
						<span class="reading-time"></span>
					</div>
				</div>
				<div class="post-content">
					<?php wptouch_the_content(); ?>
				</div>
			</div>

		<?php } ?>
	</div> <!-- content -->
	<?php get_template_part( 'related-posts' ); ?>

<?php get_footer(); ?>