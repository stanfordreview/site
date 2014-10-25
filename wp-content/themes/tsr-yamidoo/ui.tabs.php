<div class="column_right">
 <div class="tab_menu_container">
		<ul id="tab_menu">  
		 
			<li><a rel="tab_sidebar_recent">Recent</a></li>
			<li><a rel="tab_sidebar_comments">Comments</a></li>
			<li><a rel="tab_sidebar_popular">Popular</a></li>
		</ul> <!-- END -->
		<div class="clear"></div>
	</div>
	<div class="tab_container">
		<div class="tab_container_in">
			 
 
			<ul id="tab_sidebar_recent" class="tab_sidebar_list"> <!-- Latest Articles -->
			<?php $the_query = new WP_Query('showposts=5');
				while ($the_query->have_posts()) : $the_query->the_post();
			?>
			<li>
			    <?php $thumb = tsr_photo_path($post); ?>
				 
					<?php if ($thumb != ""): ?>
						<img align="left" src="<?php bloginfo('template_directory'); ?>/scripts/timthumb.php?src=<?php echo $thumb ?>&amp;w=43&amp;h=36&amp;zc=1" alt="<?php the_title(); ?>" />
					<?php else: ?>
						<img align="left" src="<?php bloginfo('template_directory'); ?>/images/no-thumb.png" width="43px" height="36px" alt="<?php the_title(); ?>" />
				<?php endif; ?>
			   <a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a> <div style="clear:right;"></div>
					<small><?php the_time('F j, Y'); ?> </small>	
			 
			</li>
			<?php endwhile; ?>
			</ul> <!-- END -->
			<ul id="tab_sidebar_comments" class="tab_sidebar_list"> <!-- Latest Comments -->
				 
					<?php dp_recent_comments(5); ?>
			 
			</ul> <!-- END -->
			
			<ul id="tab_sidebar_popular" class="tab_sidebar_list"> <!-- Popular Posts -->
				<?php if (function_exists('get_mostpopular')) { get_mostpopular('title=false'); } else { ft_popular_posts(); } ?>
			</ul> <!-- END -->

			<div class="clear"></div></div>
	</div>	<div class="clear"></div>
	</div>
 
	