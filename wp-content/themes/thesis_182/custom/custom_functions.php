<?php
 
/* ------ [ Display Co-Authors in the Byline ] ------ */
function multi_authors() { ?>
	<?
	if( function_exists( 'get_coauthors' ) ) {
		$i = new CoAuthorsIterator();
		$return = '<p class="headline_meta byline">by <span class="author vcard">';
		$i->iterate();
	 
		$return .= '<a href="'.get_author_posts_url(get_the_author_meta('ID')).'">'.get_the_author_meta('display_name').'</a>';
	 
		while($i->iterate()){
			$return.= $i->is_last() ? ' and ' : ', ';
			$return .= '<a href="'.get_author_posts_url(get_the_author_meta('ID')).'">'.get_the_author_meta('display_name').'</a>';
		}
	 
		$return .= '</span> on <abbr class="published" title="'.get_the_time('jS F Y').'">'.get_the_time('jS F Y').'</abbr></p>';
	 
		echo $return;
	} else { ?>
		<p class="headline_meta byline">by <?php thesis_author(); ?></p>
	<?php }
}
add_action('thesis_hook_after_headline', 'multi_authors');


/* By taking advantage of hooks, filters, and the Custom Loop API, you can make Thesis
 * do ANYTHING you want. For more information, please see the following articles from
 * the Thesis User’s Guide or visit the members-only Thesis Support Forums:
 * 
 * Hooks: http://diythemes.com/thesis/rtfm/customizing-with-hooks/
 * Filters: http://diythemes.com/thesis/rtfm/customizing-with-filters/
 * Custom Loop API: http://diythemes.com/thesis/rtfm/custom-loop-api/

---:[ place your custom code below this line ]:---*/


/* GOOGLE ANALYTICS */
function g_analytics() {
?>
<!-- Google Analytics -->
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-31284330-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

<?php
}
add_action('wp_head', 'g_analytics');

/* SEARCH BAR IN THE HEADER */

function header_menu1() {
?>
<div id="header_menu_area">
	<?php thesis_search_form(); ?>
</div>
<?php
}
add_action('thesis_hook_after_header', 'header_menu1');




/* NEWS FEATURED STORY */
function news_featured() {
if (is_front_page())  {
?><div class="news_feature">
<?php  
$custom_loop = new WP_Query('showposts=1&category_name=news-featured');
    ?>
<?php while ( $custom_loop->have_posts() ) : $custom_loop->the_post(); ?>
			<h1><?php echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>'; ?></h1>
			<div class="front-meta">By <?php coauthors_posts_links(); ?></div>
			<?php the_excerpt(); ?> 
		<?php endwhile; ?>
		<?php wp_reset_query(); ?>
</div>
<?php } }
add_action('thesis_hook_before_content' , 'news_featured');


function news_featured_headlines() {
if (is_front_page())  {
?><div class="news_feature_headlines">
<h4>More Headlines</h4>
<ul class="news-headlines-list">
<?php  
$custom_loop = new WP_Query('showposts=3&category_name=news-featured-headlines');
    ?>
<?php while ( $custom_loop->have_posts() ) : $custom_loop->the_post(); ?>
			<li><?php echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>'; ?></li>
		<?php endwhile; ?>
		<?php wp_reset_query(); ?>
</ul>
</div>
<?php } }
add_action('thesis_hook_before_content' , 'news_featured_headlines');



/* SUB SECTIONS */
function sub_news() {
if (is_front_page())  {
?><div id="sub_section">
<div class="sub_section_teaser">
<h3>News</h3>
<?php  
$custom_loop = new WP_Query('showposts=1&category_name=news-sub');
    ?>
<?php while ( $custom_loop->have_posts() ) : $custom_loop->the_post(); ?>
<?php thesis_teaser('teaser', 'false', 'true'); ?>
<?php endwhile; ?>
</div>

<div class="news_sub_headlines">
<h4>More News</h4>
<ul class="news-headlines-list">
<?php  
$custom_loop = new WP_Query('showposts=3&category_name=news&offset=1');
    ?>
<?php while ( $custom_loop->have_posts() ) : $custom_loop->the_post(); ?>
			<li><?php echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>'; ?></li>
		<?php endwhile; ?>
		<?php wp_reset_query(); ?>
</ul>
</div></div>
<?php } }
add_action('thesis_hook_before_content' , 'sub_news');





function sub_features() {
if (is_front_page())  {
?><div id="sub_section">
<div class="sub_section_teaser">
<h3>Features</h3>
<?php  
$custom_loop = new WP_Query('showposts=1&category_name=features-sub');
    ?>
<?php while ( $custom_loop->have_posts() ) : $custom_loop->the_post(); ?>
<?php thesis_teaser('teaser', 'false', 'true'); ?>
<?php endwhile; ?>
</div>

<div class="news_sub_headlines">
<h4>More Features</h4>
<ul class="news-headlines-list">
<?php  
$custom_loop = new WP_Query('showposts=3&category_name=features&offset=1');
    ?>
<?php while ( $custom_loop->have_posts() ) : $custom_loop->the_post(); ?>
			<li><?php echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>'; ?></li>
		<?php endwhile; ?>
		<?php wp_reset_query(); ?>
</ul>
</div></div>

<?php } }
add_action('thesis_hook_before_content' , 'sub_features');




function sub_opinion() {
if (is_front_page())  {
?><div id="sub_section">
<div class="sub_section_teaser">
<h3>Opinion</h3>
<?php  
$custom_loop = new WP_Query('showposts=1&category_name=opinion-sub');
    ?>
<?php while ( $custom_loop->have_posts() ) : $custom_loop->the_post(); ?>
<?php thesis_teaser('teaser', 'false', 'true'); ?>
<?php endwhile; ?>
</div>

<div class="news_sub_headlines">
<h4>More Opinion</h4>
<ul class="news-headlines-list">
<?php  
$custom_loop = new WP_Query('showposts=3&category_name=opinion&offset=1');
    ?>
<?php while ( $custom_loop->have_posts() ) : $custom_loop->the_post(); ?>
			<li><?php echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>'; ?></li>
		<?php endwhile; ?>
		<?php wp_reset_query(); ?>
</ul>
</div></div>

<?php } }
add_action('thesis_hook_before_content' , 'sub_opinion');


function sub_sports() {
if (is_front_page())  {
?><div id="sub_section">
<div class="sub_section_teaser">
<h3>Sports</h3>
<?php  
$custom_loop = new WP_Query('showposts=1&category_name=sports-sub');
    ?>
<?php while ( $custom_loop->have_posts() ) : $custom_loop->the_post(); ?>
<?php thesis_teaser('teaser', 'false', 'true'); ?>
<?php endwhile; ?>
</div>

<div class="news_sub_headlines">
<h4>More Sports</h4>
<ul class="news-headlines-list">
<?php  
$custom_loop = new WP_Query('showposts=3&category_name=sports&offset=1');
    ?>
<?php while ( $custom_loop->have_posts() ) : $custom_loop->the_post(); ?>
			<li><?php echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>'; ?></li>
		<?php endwhile; ?>
		<?php wp_reset_query(); ?>
</ul>
</div></div>

<?php } }
add_action('thesis_hook_before_content' , 'sub_sports');




/* BLOG COLUMN FRONT PAGE */
function blog_front() {
if (is_front_page())  {
?><div class="blog_front">
<h4>From our blog:</h4>
<h5>Fiat Lux</h5>
<h6>A liberal education, politics, and the intersection thereof at Stanford University</h6>
<?php  
$custom_loop = new WP_Query('showposts=8&category_name=blog');
    ?>
<?php while ( $custom_loop->have_posts() ) : $custom_loop->the_post(); ?>
			<div class="blog_posts_front">
			<div class="blog_posts_date"><?php echo get_the_date(); ?></div> 
			<h2><?php echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>'; ?></h2>
			<?php the_excerpt(); ?> 
			</div>
		<?php endwhile; ?>
		<?php wp_reset_query(); ?>
</div>
<?php } }
add_action('thesis_hook_before_content' , 'blog_front');






/* HOME PAGE CUSTOM TEMPLATE */
function home_pagecustom() {

   if (is_home() || is_front_page())  {
     ?>

<div id="content">
<div id="left">
	<div class="top-section">
		<div class="top-left">
			<?php news_featured(); ?>
			<?php news_featured_headlines(); ?>
			<br><br>
			<!-- Google Adsense -->
			<script type="text/javascript"><!--
			google_ad_client = "ca-pub-9680307497864609";
			/* Mini front page */
			google_ad_slot = "5049008437";
			google_ad_width = 234;
			google_ad_height = 60;
			//-->
			</script>
			<script type="text/javascript"
			src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
			</script>
		</div>
		<div class="top-right">	  
			<div class="dynamic_front"><?php dynamic_content_gallery(); ?></div>
		</div>
	</div>
	<div class="bottom-section">
		<div class="bottom-left">
			<div class="teasers-left">
				<?php sub_news(); ?>
				<?php sub_opinion(); ?>
			</div>
			<div class="teasers-right">
				<?php sub_features(); ?>
				<?php sub_sports(); ?>
			</div>
		</div>
		<div class="bottom-right">
			<!-- Google Adsense -->
			<script type="text/javascript"><!--
			google_ad_client = "ca-pub-9680307497864609";
			/* Front page big ad */
			google_ad_slot = "8636546415";
			google_ad_width = 336;
			google_ad_height = 280;
			//-->
			</script>
			<script type="text/javascript"
			src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
			</script>
			<?php sidebar_3(); ?>	  
		</div>
	</div>
</div>
</div>
<div id="right">
	<?php blog_front(); ?>
</div>

     <?php
   } 
}
remove_action('thesis_hook_custom_template','thesis_custom_template_sample');
add_action('thesis_hook_custom_template', 'home_pagecustom');




/* INSTALLS WORDPRESS THUMBNAIL THEME SUPPORT */

add_theme_support( 'post-thumbnails' );
set_post_thumbnail_size( 500, 400 );
add_image_size( 'teaser-image', 190, 140, true);
add_image_size('image-headline', 135, 100, true);
add_image_size( 'archive-image', 100, 75, true);

add_action('thesis_hook_before_teaser_headline','teaser_thumbnail');
function teaser_thumbnail() {
if(has_post_thumbnail() && is_front_page()):
echo '<p><a href="'.get_permalink().'">';    the_post_thumbnail('teaser-image');
echo '</a></p>';
endif;
}  


add_action('thesis_hook_before_teaser','teaser_thumbnail1');
function teaser_thumbnail1() {
if(has_post_thumbnail() && !is_front_page()):
echo '<p><a href="'.get_permalink().'">';    the_post_thumbnail('archive-image');
echo '</a></p>';
endif;
}  




/* INSTALLS WORDPRESS MENU SUPPORT FOR SECOND MENU */
function header_menu() {
    wp_nav_menu(array(
    'container' => '',
    'menu_id' => 'secondary_menu',
    'fallback_cb' => 'thesis_nav_menu',
    'theme_location' => 'secondary',
    ));
    }
add_action('thesis_hook_after_header','header_menu'); 
register_nav_menu('secondary','Secondary Menu');  




/* INSTALLS WORDPRESS MENU SUPPORT FOR TRENDS MENU */


function trend_menu_text() {
if (!is_front_page())  {
?>
<div class="trends-text"><h3>What's Hot</h3></div>
<?php }}
add_action('thesis_hook_after_header','trend_menu_text'); 

function trend_menu() {
if (!is_front_page())  {
    wp_nav_menu(array(
    'container' => '',
    'menu_id' => 'trend_menu',
    'fallback_cb' => 'thesis_nav_menu',
    'theme_location' => 'third',
    ));
    }}
add_action('thesis_hook_after_header','trend_menu'); 
register_nav_menu('third','Trend Menu');  




/* CREATES CUSTOM CLASS FOR THE HOME PAGE */
function custom_body_class($classes) {
 global $thesis_design;
   if (is_home() || is_front_page())  {
    $classes[] .= 'home-custom1';
 }
    return $classes;
}
add_filter('thesis_body_classes', 'custom_body_class');





/* CUSTOM SIDEBAR FOR FRONT PAGE */

register_sidebars(1,
    array(
        'name' => 'Sidebar 3',
        'before_widget' => '<li class="widget %2$s" id="%1$s">',
        'after_widget' => '</li>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    )
);


function sidebar_3() {
 ?>
	<div id="sidebar-3" class="sidebar">
		<ul class="sidebar_list">
			<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Sidebar 3') ) { ?>
				<li class="widget">
				<div class="widget_box">
					<h3><?php _e('Home Sidebar 1', 'thesis'); ?></h3>
					<p>This is your new sidebar.  Add widgets here from your WP Dashboard just like normal.</p>
</div>
</li>
<?php } ?>
</ul>
</div>
<?php }




/* CUSTOM BLOG PAGE */

function custom_body_class1($classes) {
 global $thesis_design;
    if (is_category('blog')) {
    $classes[] .= 'blog';
 }
    return $classes;
}
add_filter('thesis_body_classes', 'custom_body_class1');


$blog_loop = new fiat_lux;

class fiat_lux extends thesis_custom_loop {
function category() {
    if (is_category('blog')) {
        thesis_archive_intro();
        while (have_posts()) {
            the_post(); ?>
        <div class="post_box">
                <div class="headline_area">
                    <h2 class="entry-title" rel="bookmark" title="<?php the_title();?>">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h2>
                </div>
		<div class="blog-meta">By <?php coauthors_posts_links(); ?></div>
		<div class="blog-social"><?php social_media3(); ?></div>
                <div class="format_text entry-content">
                    <?php the_content('Continue Reading'); ?>
                </div>
		<div class="blog-tags">
		    <?php the_tags(); ?>
		</div>
        </div>
        <?php }
    }
    else
        thesis_loop::category();
}
}



/* FACEBOOK AND TWITTER BUTTONS */

function social_media1() {
?>
	<div class="social">

<div class="twitter"><a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal" data-text="<?php the_title(); ?>" data-url="<?php the_permalink(); ?>" data-via="StanfordReview">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script></div>

<div class="facebook"><script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script><fb:like href="<?php the_permalink(); ?>" send="false" layout="button_count" width="200" show_faces="false" action="recommend" font=""></fb:like></iframe></div>

	</div>

<?php
}
add_action('thesis_hook_after_post' , 'social_media1');

function social_media2() {
?>
	<div class="social">

<div class="twitter"><a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal" data-text="<?php the_title(); ?>" data-url="<?php the_permalink(); ?>" data-via="StanfordReview">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script></div>

<div class="facebook"><script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script><fb:like href="<?php the_permalink(); ?>" send="false" layout="button_count" width="200" show_faces="false" action="recommend" font=""></fb:like></iframe></div>

	</div>

<?php
}
// add_action('thesis_hook_before_post' , 'social_media2');


function social_media3() {
if (!is_single())  {
?>
	<div class="social">

<div class="twitter"><a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal" data-text="<?php the_title(); ?>" data-url="<?php the_permalink(); ?>" data-via="StanfordReview">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script></div>

<div class="facebook"><script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script><fb:like href="<?php the_permalink(); ?>" send="false" layout="button_count" width="200" show_faces="false" action="recommend" font=""></fb:like></iframe></div>

<div class="comments-blog"><a href="<?php comments_link(); ?>"><?php comments_number(); ?></a></div>

	</div>

<?php
}}
add_action('thesis_hook_before_post' , 'social_media3');






/* BEGIN: Facebook Javascript SDK Code */

function script_facebook(){
	?>
		<div id="fb-root"></div>
		<script>
		  window.fbAsyncInit = function() {
			FB.init({appId: '299352093409866', status: true, cookie: true,
					 xfbml: true});
		  };
		  (function() {
			var e = document.createElement('script'); e.async = true;
		e.src = document.location.protocol +
		  '//connect.facebook.net/en_US/all.js';
		document.getElementById('fb-root').appendChild(e);
		  }());
		</script>
	<?
}

add_action('thesis_hook_before_html','script_facebook');

add_filter('language_attributes', 'add_og_xml_ns');

function add_og_xml_ns($content) {
  return ' xmlns:og="http://opengraphprotocol.org/schema/" ' . $content;
}

add_filter('language_attributes', 'add_fb_xml_ns');

function add_fb_xml_ns($content) {
  return ' xmlns:fb="http://www.facebook.com/2008/fbml" ' . $content;
}

/* END: Facebook Javascript SDK Code */




/* FOOTER */
remove_action('thesis_hook_footer', 'thesis_attribution');


register_nav_menu('footer','Footer Menu');

function footer_menu() {
  wp_nav_menu(array(
    'container' => '',
    'menu_id' => 'footer_menu',
    'fallback_cb' => 'thesis_nav_menu',
    'theme_location' => 'footer',
  ));
}

add_action('thesis_hook_before_footer','footer_menu');  


