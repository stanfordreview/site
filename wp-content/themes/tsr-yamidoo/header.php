<?php
global $options;
foreach ($options as $value) {
    if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
}

// for ads - some display only on front page
if (is_home()) {
	$pageclass = 'frontpage';
} else {
	$pageclass = 'notfrontpage';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
		
<title><?php bloginfo('name'); ?> <?php wp_title(); ?></title>
<meta http-equiv="content-type" content="<?php bloginfo('html_type') ?>; charset=<?php bloginfo('charset') ?>" />
<meta name="description" content="<?php bloginfo('description') ?>" />
<meta name="google-site-verification" content="seSVs6eBzSw3TCY4PbQ1V_t55S1of0pBtrsH9j8jskQ" />
<?php if(is_search()) { ?>
<meta name="robots" content="noindex, nofollow" /> 
<?php }?>

<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/styles/reset.css" media="screen" />
<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_url'); ?>" media="screen" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/styles/tsr-style.css" type="text/css" media="screen" />
<link rel="shortcut icon" href="<?php bloginfo('template_url'); ?>/images/favicon.ico">

 
<script type="text/javascript" src="<?php echo bloginfo('template_url'); ?>/scripts/slider.js"></script>
<script type="text/javascript" src="<?php echo bloginfo('template_url'); ?>/scripts/dropdowns.js"></script>
<script type="text/javascript" src="<?php echo bloginfo('template_url'); ?>/scripts/featuredcontentglider.js"></script>
<script type="text/javascript" src="<?php echo bloginfo('template_url'); ?>/scripts/jquery-1.2.3.pack.js"></script>
<?php remove_action('wp_print_styles', 'pagenavi_stylesheets'); ?>	

 <script type="text/javascript">
featuredcontentglider.init({
gliderid: "headline-content",
contentclass: "glidecontent",
togglerid: "teaser",
remotecontent: "",
selected: 0,
persiststate: false,
speed: 300,
direction: "<?php echo $ft_featured_slideshow_effect; ?>",
autorotate: true,
autorotateconfig: [<?php echo $ft_featured_slideshow_speed; ?>, 0]
})
</script>
<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>	

	<script type="text/javascript">
		 menuscript.definemenu("tab_menu", 0)
	</script>
	
<?php wp_head(); ?>

<script type='text/javascript' src='http://partner.googleadservices.com/gampad/google_service.js'>
</script>
<script type='text/javascript'>
GS_googleAddAdSenseService("ca-pub-5913108846433714");
GS_googleEnableAllServices();
</script>
<script type='text/javascript'>
GA_googleAddSlot("ca-pub-5913108846433714", "stanfordReview_home_300x250");
</script>
<script type='text/javascript'>
GA_googleFetchAds();
</script>


</head>
<body class='<?php echo $pageclass ?>'>
    <div id="page-wrap">
    	<div id="header">
	      	<?php if (strlen($ft_ad_head_imgpath) > 1 && $ft_ad_head_select == 'Yes') {?>
	      		<div class="banner banner-head"><?php if (strlen($ft_ad_head_imgpath) > 1) { echo stripslashes($ft_ad_head_imgpath); }?></div>
	      	<?php } ?>

		    <div id="topline">
			    <ul id="nav">
		          	<?php wp_list_pages('title_li='); ?>
					<li class="page_item"><a href="http://stanfordreview.org/feed" title="RSS Feed"><img src="/wp-content/themes/tsr-yamidoo/images/feed.png" alt="RSS"> RSS</a></li>
		    	</ul>
			</div>
	
	        <div id="logo">
	        	<h1><a href="<?php echo get_option('home'); ?>/"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/logo.png" alt="<?php bloginfo('name'); ?>" /></a></h1>
	        </div>
	    </div>

		<div id="sections_wrap">
	        <ol id="sections">
				<li id="search"><?php include (TEMPLATEPATH . '/searchform.php'); ?></li>

	            <li><a href="<?php echo get_option('home'); ?>/" class="on">Front Page</a></li>
				<li class="cat-item cat-item-480"><a href="/cat/sections/news" title="View all posts filed under News">News</a></li>
					<li class="cat-item cat-item-481"><a href="/cat/sections/opinion" title="View all posts filed under Opinion">Opinion</a></li>
					<li class="cat-item cat-item-484"><a href="/cat/sections/features" title="View all posts filed under Features">Features</a></li>	 
					
					<li class="cat-item cat-item-482 current-cat"><a href="/cat/sections/world" title="View all posts filed under 
World">World</a></li>
					           		<li><a href="http://blog.stanfordreview.org/">Blog</a></li>
	        </ol>
		</div>
    

      
	    <div id="content-wrap">
