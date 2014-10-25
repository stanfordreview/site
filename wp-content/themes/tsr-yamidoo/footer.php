<?php
 global $options;
foreach ($options as $value) {
    if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
}
?>
         </div> <!-- end content-wrap -->
         
         
         <div id="footer">
              <!-- <div id="subscribe">
              <h4><img src="<?php bloginfo('template_directory'); ?>/images/feed.png" alt="Subscribe to RSS" /> Subscribe</h4>
              <p>Subscribe to <a href="<?php bloginfo('rss2_url'); ?>">RSS</a><?php if (strlen($ft_misc_feedburnerID) > 0) { ?> or enter you email to receive newsletter for news, articles, and updates about what's new.</p>
                <form action="http://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="window.open('http://feedburner.google.com/fb/a/mailverify?uri=<?php echo $ft_misc_feedburnerID; ?>', 'popupwindow', 'scrollbars=yes,width=550,height=520');return true">
                <input type="text" onblur="if (this.value == '') {this.value = 'enter your email...';}" onfocus="if (this.value == 'enter your email...') {this.value = '';}" value="enter your email..." name="semail" id="semail" />
                <input type="hidden" value="<?php echo $ft_misc_feedburnerID; ?>" name="uri" />
                <input type="hidden" name="loc" value="en_US" />
                <input type="submit" id="submit" value="Subscribe" />
                </form><?php } ?>
               </div>  -->
      
               
               
			<div class="ads">
				<h2>Support Our Sponsors</h2>
							<ul>
		<li><a href="http://www.reefhotspot.com/">Salt Water Fish</a></li>
		<li><a href="http://www.greataupair.com/">Child Care Agency AuPair Nanny Tutor Babysitter Pet Sitter Housekeeper
Senior Care and Personal Assistant Jobs. Now hiring for great jobs.</a></li>

				</ul>
			</div>

               <div id="footer_right">

		

                <ul>
                <li> <a href="<?php echo get_option('home'); ?>">Home</a></li>
          <?php wp_list_pages('title_li='); ?>
                 </ul>
                 <small> Copyright &copy; <a href="<?php echo get_option('home'); ?>/" class="on"><?php bloginfo('name'); ?></a> <?php echo date("Y",time()+(7*24*60*60)); ?>. All Rights Reserved.</small><br />
                 <small><a href="http://www.wpzoom.com/themes/yamidoo/">Yamidoo Magazine</a> theme by <a href="http://www.wpzoom.com">WPZOOM</a></small>
                </div>
                
            </div> <!-- end footer -->
            
            <div class="clear"></div>
            
      
	</div>
	
	
	
<?php if ($ft_misc_analytics != '' && $ft_misc_analytics_select == 'Yes')
{
  echo stripslashes($ft_misc_analytics);
} ?>
 

<?php wp_footer(); ?>

<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
var pageTracker = _gat._getTracker("UA-5695518-1");
pageTracker._trackPageview();
</script>

</body>
</html>
