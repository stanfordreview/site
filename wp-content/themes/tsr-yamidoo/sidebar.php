<?php
global $options;
foreach ($options as $value) {
    if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
}
?>
	<div id="sidebar"  class="<?php single_cat_title(); ?>">
	
	    <?php 
        if ( is_single() ) :
        global $post;
        $categories = get_the_category();
        
			$photos = &tsr_get_photos($post);
			if (!$photos) $photos = array();
          foreach ($photos as $photoid => $photo) { 
			?>      
				<div class="image">
					<?php $m = array();
					      $photofilename = preg_match('/uploads\\/(.*)$/i', $photo->guid, $m)?>
					<img src="<?php bloginfo('template_directory'); ?>/scripts/thumb/thumb.php?src=<?php echo $m[1]; ?>&amp;x=390&amp;y=600&amp;f=0" alt="<?php the_title(); ?>" />
					<div class="caption"><?php echo $photo->post_excerpt; ?></div>
				</div>
				
		  <?php } ?>
        
        
	<?php endif ; ?>
 <?php	include(TEMPLATEPATH . '/ui.tabs.php'); ?>  
	<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar(1) ) : ?>
 	<?php endif; ?>
 
        
        <?php if ( function_exists('get_flickrRSS') )  { echo '<div id="flickrrss" class="widget">
         <h3><font color="#0063DC">Flick</font><font color="#FF0084">r</font> Photos</h3>
          <ul>'; get_flickrRSS(); echo '</ul></div>'; }  else { ?> 
 
            <?php } ?>
        
         
         
      <div id="sidebar_left">        
      <?php 	if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar(2) ) : ?>
      <?php endif; ?>
	 	  </div>
	  
	  
	     <div id="sidebar_right">        
      <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar(3) ) : ?>
 			<?php endif; ?>
	     </div>

<?php if (strlen($ft_ad_side_imgpath) > 1 && $ft_ad_side_select == 'Yes') {?>
      <div id="ads" class="widget"><?php if (strlen($ft_ad_side_imgpath) > 1) { echo stripslashes($ft_ad_side_imgpath); }?></div>
      <?php } ?>
   </div> <!-- end sidebar -->
    
         
