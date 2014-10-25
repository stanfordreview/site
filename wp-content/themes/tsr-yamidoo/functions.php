<?php
  /* 
Recent Comments http://mtdewvirus.com/code/wordpress-plugins/ 
*/ 

require("tsr-functions.php");

// disable HTML filtering in user bios
remove_filter('pre_user_description', 'wp_filter_kses');

function dp_recent_comments($no_comments = 10, $comment_len = 120) { 
    global $wpdb; 
	
	$request = "SELECT * FROM $wpdb->comments";
	$request .= " JOIN $wpdb->posts ON ID = comment_post_ID";
	$request .= " WHERE comment_approved = '1' AND post_status = 'publish' AND post_password ='' AND comment_type = ''"; 
	$request .= " ORDER BY comment_date DESC LIMIT $no_comments"; 
		
	$comments = $wpdb->get_results($request);
		
	if ($comments) { 
		foreach ($comments as $comment) { 
			ob_start();
			?>
				<li>
					<div class="tab-comments-avatar"><?php echo get_avatar($comment,$size='40' ); ?></div>
					<div class="tab-comments-text">
						<a href="<?php echo get_permalink( $comment->comment_post_ID ) . '#comment-' . $comment->comment_ID; ?>"><?php echo dp_get_author($comment); ?>:</a>
						<?php echo strip_tags(substr(apply_filters('get_comment_text', $comment->comment_content), 0, $comment_len)); ?>...
						<br/><a href="<?php echo get_permalink( $comment->comment_post_ID )?>#comment-<?php echo $comment->comment_ID;?>"><?php print_r($comment->post_title) ?></a>
					</div>
				</li>
			<?php
			ob_end_flush();
		} 
	} else { 
		echo "<li>No comments</li>";
	}
}

function dp_get_author($comment) {
	$author = "";

	if ( empty($comment->comment_author) )
		$author = __('Anonymous');
	else
		$author = $comment->comment_author;
		
	return $author;
}


/* Popular News
----------------------*/	

function ft_popular_posts () { 

		 

		// Extract widget options
		$options = get_option('ft_popular_posts');
		$title = $options['title'];
		$maxposts = $options['maxposts'];
		$timeline = $options['sincewhen'];

		// Generate output
		echo $before_widget . $before_title . $title . $after_title;
		echo "<ul class='mcplist'>\n";
		
		// Since we're passing a SQL statement, globalise the $wpdb var
		global $wpdb;
		$sql = "SELECT ID, post_title, comment_count FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' ";
        
		// What's the chosen timeline?
		switch ($timeline) {
			case "thismonth":
				$sql .= "AND MONTH(post_date) = MONTH(NOW()) AND YEAR(post_date) = YEAR(NOW()) ";
				break;
			case "thisyear":
				$sql .= "AND YEAR(post_date) = YEAR(NOW()) ";
				break;
			default:
				$sql .= "";
		}
		
		// Make sure only integers are entered
		if (!ctype_digit($maxposts)) {
			$maxposts = 10;
		} else {
			// Reformat the submitted text value into an integer
			$maxposts = $maxposts + 0;
			// Only accept sane values
			if ($maxposts <= 0 or $maxposts > 10) {
				$maxposts = 10;
			}
		}
		
		// Complete the SQL statement
		$sql .= "AND comment_count > 0 ORDER BY comment_count DESC LIMIT ". $maxposts;
		
		$res = $wpdb->get_results($sql);
		
		if($res) {
			$mcpcounter = 1;
			foreach ($res as $r) {
				echo "<li class='mcpitem mcpitem-$mcpcounter'><a href='".get_permalink($r->ID)."' rel='bookmark'>".htmlspecialchars($r->post_title, ENT_QUOTES)."</a> <span class='mcpctr'>(".htmlspecialchars($r->comment_count, ENT_QUOTES).")</span></li>\n";
				$mcpcounter++;
			}
		} else {
			echo "<li class='mcpitem mcpitem-0'>". __('No commented posts yet') . "</li>\n";
		}
		
		echo "</ul>\n";
		echo $after_widget;
	} 


function ft_popular_posts_admin() {
	
// Get our options and see if we're handling a form submission.
		$options = get_option('ft_popular_posts');
		if ( !is_array($options) )
			$options = array(
				'title'=>__('Popular Posts'),
				'sincewhen' => 'forever',
				'maxposts'=> 10
			);
		if ( $_POST['htnetmcp-submit'] ) {

			// Remember to sanitize and format use input appropriately.
			$options['title'] = strip_tags(stripslashes($_POST['htnetmcp-title']));
			$options['sincewhen'] = strip_tags(stripslashes($_POST['htnetmcp-sincewhen']));
			$options['maxposts'] = strip_tags(stripslashes($_POST['htnetmcp-maxposts']));
			update_option('ft_popular_posts', $options);
		}

		// Be sure you format your options to be valid HTML attributes.
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$sincewhen = htmlspecialchars($options['sincewhen'], ENT_QUOTES);
		$maxposts = htmlspecialchars($options['maxposts'], ENT_QUOTES);
		
		// Here is our little form segment. Notice that we don't need a
		// complete form. This will be embedded into the existing form.
		echo '<p style="text-align:center;"><label for="htnetmcp-title">' . __('Title:') . ' <input style="width: 200px;" id="htnetmcp-title" name="htnetmcp-title" type="text" value="'.$title.'" /></label></p>';
		
		echo '<p style="text-align:center;"><label for="htnetmcp-sincewhen">' . __('Since:') 
			.'<select style="width: 120px;" id="htnetmcp-sincewhen" name="htnetmcp-sincewhen">';
		if ($sincewhen != 'thismonth' or $sincewhen != 'thisyear') {
			echo "<option value='forever' selected='selected'>".__('Forever')."</option>";
		} else {
			echo "<option value='forever'>".__('Forever')."</option>";
		}
		if ($sincewhen == 'thisyear') {
			echo "<option value='thisyear' selected='selected'>".__('This Year')."</option>";
		} else {
			echo "<option value='thisyear'>".__('This Year')."</option>";
		}
		if ($sincewhen == 'thismonth') {
			echo "<option value='thismonth' selected='selected'>".__('This Month')."</option>";
		} else {
			echo "<option value='thismonth'>".__('This Month')."</option>";
		}
		echo '</select></label></p>';
		
		echo '<p style="text-align:center;"><label for="htnetmcp-maxposts">' . __('Posts To Display:') 
			.'<select style="width: 120px;" id="htnetmcp-maxposts" name="htnetmcp-maxposts">';
		for ($mp = 1; $mp <= 10; $mp++) {
			if ($mp == $maxposts) {
				echo "<option selected='selected'>$mp</option>";
			} else {
				echo "<option>$mp</option>";
			}
		}
		echo '</select></label></p>';	
		echo '<input type="hidden" id="htnetmcp-submit" name="htnetmcp-submit" value="1" />';
	}

 


$yam_trackbacks	= array();
$yam_comments	= array();

// based upon the work done by Steve Smith - http://orderedlist.com/wordpress-plugins/feedburner-plugin/ and feedburner - http://www.feedburner.com/fb/a/help/wordpress_quickstart
function feed_redirect() {

	global $wp, $feed, $withcomments;
	
	$newURL1 = trim( get_settings( "feedmail" ) );
	$newURL1 = trim( get_settings( "yam_feedlinkURL" ) );
	$newURL2 = trim( get_settings( "yam_feedlinkComments" ) );
	
	if( is_feed() ) {

		if ( $feed != 'comments-rss2' 
				&& !is_single() 
				&& $wp->query_vars[ 'category_name' ] == ''
				&& !is_author() 
				&& ( $withcomments != 1 )
				&& $newURL1 != '' ) {
		
			if ( function_exists( 'status_header' ) ) { status_header( 302 ); }
			header( "Location:" . $newURL1 );
			header( "HTTP/1.1 302 Temporary Redirect" );
			exit();
			
		} elseif ( ( $feed == 'comments-rss2' || $withcomments == 1 ) && $newURL2 != '' ) {
	
			if ( function_exists( 'status_header' ) ) { status_header( 302 ); }
			header( "Location:" . $newURL2 );
			header( "HTTP/1.1 302 Temporary Redirect" );
			exit();
			
		}
	
	}

}

function feed_check_url() {

	switch ( basename( $_SERVER[ 'PHP_SELF' ] ) ) {
		case 'wp-rss.php':
		case 'wp-rss2.php':
		case 'wp-atom.php':
		case 'wp-rdf.php':
		
			$newURL = trim( get_settings( "yam_feedlinkURL" ) );
			
			if ( $newURL != '' ) {
				if ( function_exists('status_header') ) { status_header( 302 ); }
				header( "Location:" . $newURL );
				header( "HTTP/1.1 302 Temporary Redirect" );
				exit();
			}
			
			break;
			
		case 'wp-commentsrss2.php':
		
			$newURL = trim( get_settings( "yam_feedlinkComments" ) );
			
			if ( $newURL != '' ) {
				if ( function_exists('status_header') ) { status_header( 302 ); }
				header( "Location:" . $newURL );
				header( "HTTP/1.1 302 Temporary Redirect" );
				exit();
			}
			
			break;
	}
}

if (!preg_match("/feedburner|feedvalidator/i", $_SERVER['HTTP_USER_AGENT'])) {
	add_action('template_redirect', 'feed_redirect');
	add_action('init','feed_check_url');
}


/*
Plugin Name: Limit Posts
Plugin URI: http://labitacora.net/comunBlog/limit-post.phps
Description: Limits the displayed text length on the index page entries and generates a link to a page to read the full content if its bigger than the selected maximum length.
Usage: the_content_limit($max_charaters, $more_link)
Version: 1.1
Author: Alfonso Sanchez-Paus Diaz y Julian Simon de Castro
Author URI: http://labitacora.net/
License: GPL
Download URL: http://labitacora.net/comunBlog/limit-post.phps
Make:
    In file index.php
    replace the_content()
    with the_content_limit(1000, "more")
*/

function the_content_limit($max_char, $more_link_text = '(more...)', $stripteaser = 0, $more_file = '') {
    $content = get_the_content($more_link_text, $stripteaser, $more_file);
    $content = apply_filters('the_content', $content);
    $content = str_replace(']]>', ']]&gt;', $content);
    $content = strip_tags($content);

   if (strlen($_GET['p']) > 0) {
      echo "<div>";
      echo $content;
      echo "</div>";
   }
   else if ((strlen($content)>$max_char) && ($espacio = strpos($content, " ", $max_char ))) {
        $content = substr($content, 0, $espacio);
        $content = $content;
        echo "<div>";
        echo $content;
        echo "...";
        echo "</div>";
   }
   else {
      echo "<div>";
      echo $content;
      echo "</div>";
   }
}

 
/*
Plugin Name: WordPress Related Posts
Version: 1.0
Plugin URI: http://fairyfish.net/2007/09/12/wordpress-23-related-posts-plugin/
Description: Generate a related posts list via tags of WordPress
Author: Denis
Author URI: http://fairyfish.net/

Copyright (c) 2007
Released under the GPL license
http://www.gnu.org/licenses/gpl.txt

    This file is part of WordPress.
    WordPress is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

	INSTALL: 
	Just install the plugin in your blog and activate
*/

add_action('init', 'init_textdomain');
function init_textdomain(){
  load_plugin_textdomain('wp_related_posts',PLUGINDIR . '/' . dirname(plugin_basename (__FILE__)) . '/lang');
}

function wp_get_related_posts() {
	global $wpdb, $post,$table_prefix;
	$wp_rp = get_option("wp_rp");
	
	$exclude = explode(",",$wp_rp["wp_rp_exclude"]);
	$limit = $wp_rp["wp_rp_limit"];
	$wp_rp_title = $wp_rp["wp_rp_title"];
	$wp_no_rp = $wp_rp["wp_no_rp"];
	$wp_no_rp_text = $wp_rp["wp_no_rp_text"];
	$show_date = $wp_rp["wp_rp_date"];
	$show_comments_count = $wp_rp["wp_rp_comments"];
	
	if ( $exclude != '' ) {
		$q = "SELECT tt.term_id FROM ". $table_prefix ."term_taxonomy tt, " . $table_prefix . "term_relationships tr WHERE tt.taxonomy = 'category' AND tt.term_taxonomy_id = tr.term_taxonomy_id AND tr.object_id = $post->ID";

		$cats = $wpdb->get_results($q);
		
		foreach(($cats) as $cat) {
			if (in_array($cat->term_id, $exclude) != false){
				return;
			}
		}
	}
		
	if(!$post->ID){return;}
	$now = current_time('mysql', 1);
	$tags = wp_get_post_tags($post->ID);

	//print_r($tags);
	
	$taglist = "'" . $tags[0]->term_id. "'";
	
	$tagcount = count($tags);
	if ($tagcount > 1) {
		for ($i = 1; $i <= $tagcount; $i++) {
			$taglist = $taglist . ", '" . $tags[$i]->term_id . "'";
		}
	}
		
	if ($limit) {
		$limitclause = "LIMIT $limit";
	}	else {
		$limitclause = "LIMIT 10";
	}
	
	$q = "SELECT p.ID, p.post_title, p.post_date, p.comment_count, count(t_r.object_id) as cnt FROM $wpdb->term_taxonomy t_t, $wpdb->term_relationships t_r, $wpdb->posts p WHERE t_t.taxonomy ='post_tag' AND t_t.term_taxonomy_id = t_r.term_taxonomy_id AND t_r.object_id  = p.ID AND (t_t.term_id IN ($taglist)) AND p.ID != $post->ID AND p.post_status = 'publish' AND p.post_date_gmt < '$now' GROUP BY t_r.object_id ORDER BY cnt DESC, p.post_date_gmt DESC $limitclause;";

	//echo $q;

	$related_posts = $wpdb->get_results($q);
	$output = "";
	
	if (!$related_posts){
		
		if(!$wp_no_rp || ($wp_no_rp == "popularity" && !function_exists('akpc_most_popular'))) $wp_no_rp = "text";
		
		if($wp_no_rp == "text"){
			if(!$wp_no_rp_text) $wp_no_rp_text= __("No Related Post",'wp_related_posts');
			$output  .= '<li>'.$wp_no_rp_text .'</li>';
		}	else{
			if($wp_no_rp == "random"){
				if(!$wp_no_rp_text) $wp_no_rp_text= __("Random Posts",'wp_related_posts');
				$related_posts = wp_get_random_posts($limitclause);
			}	elseif($wp_no_rp == "commented"){
				if(!$wp_no_rp_text) $wp_no_rp_text= __("Most Commented Posts",'wp_related_posts');
				$related_posts = wp_get_most_commented_posts($limitclause);
			}	elseif($wp_no_rp == "popularity"){
				if(!$wp_no_rp_text) $wp_no_rp_text= __("Most Popular Posts",'wp_related_posts');
				$related_posts = wp_get_most_popular_posts($limitclause);
			}else{
				return __("Something wrong",'wp_related_posts');;
			}
			$wp_rp_title = $wp_no_rp_text;
		}
	}		
		
	foreach ($related_posts as $related_post ){
		$output .= '<li>';
		
		if ($show_date){
			$dateformat = get_option('date_format');
			$output .=   mysql2date($dateformat, $related_post->post_date) . " -- ";
		}
		
		$output .=  '<a href="'.get_permalink($related_post->ID).'" title="'.wptexturize($related_post->post_title).'">'.wptexturize($related_post->post_title).'';
		
		if ($show_comments_count){
			$output .=  " (" . $related_post->comment_count . ")";
		}
		
		$output .=  '</a></li>';
	}
	
	$output = '<ul class="related_post">' . $output . '</ul>';
		
	if($wp_rp_title != '') $output =  '<h3>'.$wp_rp_title .'</h3>'. $output;
	
	return $output;
}

function wp_related_posts(){
		
	$output = wp_get_related_posts() ;

	echo $output;
}

function wp23_related_posts() {
	wp_related_posts();
}

function wp_related_posts_for_feed($content=""){
	$wp_rp = get_option("wp_rp");
	$wp_rp_rss = ($wp_rp["wp_rp_rss"] == 'yes') ? 1 : 0;
	if ( (! is_feed()) || (! $wp_rp_rss)) return $content;
	
	$output = wp_get_related_posts() ;
	$content = $content . $output;
	
	return $content;
}

add_filter('the_content', 'wp_related_posts_for_feed',1);

function wp_related_posts_auto($content=""){
	$wp_rp = get_option("wp_rp");
	$wp_rp_auto = ($wp_rp["wp_rp_auto"] == 'yes') ? 1 : 0;
	if ( (! is_single()) || (! $wp_rp_auto)) return $content;
	
	$output = wp_get_related_posts() ;
	$content = $content . $output;
	
	return $content;
}

add_filter('the_content', 'wp_related_posts_auto',99);

function wp_get_random_posts ($limitclause="") {
    global $wpdb, $tableposts, $post;
		
    $q = "SELECT ID, post_title, post_date, comment_count FROM $tableposts WHERE post_status = 'publish' AND post_type = 'post' AND ID != $post->ID ORDER BY RAND() $limitclause";
    return $wpdb->get_results($q);
}

function wp_random_posts ($number = 10){
	$limitclause="LIMIT " . $number;
	$random_posts = wp_get_random_posts ($limitclause);
	
	foreach ($random_posts as $random_post ){
		$output .= '<li>';
		
		$output .=  '<a href="'.get_permalink($random_post->ID).'" title="'.wptexturize($random_post->post_title).'">'.wptexturize($random_post->post_title).'</a></li>';
	}
	
	$output = '<ul class="randome_post">' . $output . '</ul>';
	
	echo $output;
}

function wp_get_most_commented_posts($limitclause="") {
	global $wpdb; 
    $q = "SELECT ID, post_title, post_date, COUNT($wpdb->comments.comment_post_ID) AS 'comment_count' FROM $wpdb->posts, $wpdb->comments WHERE comment_approved = '1' AND $wpdb->posts.ID=$wpdb->comments.comment_post_ID AND post_status = 'publish' GROUP BY $wpdb->comments.comment_post_ID ORDER BY comment_count DESC $limitclause"; 
    return $wpdb->get_results($q);
} 

function wp_most_commented_posts ($number = 10){
	$limitclause="LIMIT " . $number;
	$most_commented_posts = wp_get_most_commented_posts ($limitclause);
	
	foreach ($most_commented_posts as $most_commented_post ){
		$output .= '<li>';
		
		$output .=  '<a href="'.get_permalink($most_commented_post->ID).'" title="'.wptexturize($most_commented_post->post_title).'">'.wptexturize($most_commented_post->post_title).'</a></li>';
	}
	
	$output = '<ul class="most_commented_post">' . $output . '</ul>';
	
	echo $output;
}

function wp_get_most_popular_posts ($limitclause="") {
    global $wpdb, $table_prefix;
		
    $q = $sql = "SELECT p.ID, p.post_title, p.post_date, p.comment_count FROM ". $table_prefix ."ak_popularity as akpc,".$table_prefix ."posts as p WHERE p.ID = akpc.post_id ORDER BY akpc.total DESC $limitclause";;
    return $wpdb->get_results($q);
}

function wp_most_popular_posts ($number = 10){
	$limitclause="LIMIT " . $number;
	$most_popular_posts = wp_get_most_popular_posts ($limitclause);
	
	foreach ($most_popular_posts as $most_popular_post ){
		$output .= '<li>';
		
		$output .=  '<a href="'.get_permalink($most_popular_post->ID).'" title="'.wptexturize($most_popular_post->post_title).'">'.wptexturize($most_popular_post->post_title).'</a></li>';
	}
	
	$output = '<ul class="most_popular_post">' . $output . '</ul>';
	
	echo $output;
}

add_action('admin_menu', 'wp_add_related_posts_options_page');

function wp_add_related_posts_options_page() {
	if (function_exists('add_options_page')) {
		add_options_page( __('WordPress Related Posts','wp_related_posts'), __('WordPress Related Posts','wp_related_posts'), 8, basename(__FILE__), 'wp_related_posts_options_subpanel');
	}
}

function wp_related_posts_options_subpanel() {
	if($_POST["wp_rp_Submit"]){
		$message = "WordPress Related Posts Setting Updated";
	
		$wp_rp_saved = get_option("wp_rp");
	
		$wp_rp = array (
			"wp_rp_title" 	=> $_POST['wp_rp_title_option'],
			"wp_no_rp"		=> $_POST['wp_no_rp_option'],
			"wp_no_rp_text"	=> $_POST['wp_no_rp_text_option'],
			"wp_rp_limit"	=> $_POST['wp_rp_limit_option'],
			'wp_rp_exclude'	=> $_POST['wp_rp_exclude_option'],
			'wp_rp_auto'	=> $_POST['wp_rp_auto_option'],
			'wp_rp_rss'		=> $_POST['wp_rp_rss_option'],
			'wp_rp_comments'=> $_POST['wp_rp_comments_option'],
			'wp_rp_date'	=> $_POST['wp_rp_date_option']
		);
		
		if ($wp_rp_saved != $wp_rp)
			if(!update_option("wp_rp",$wp_rp))
				$message = "Update Failed";
		
		echo '<div id="message" class="updated fade"><p>'.$message.'.</p></div>';
	}
	
	$wp_rp = get_option("wp_rp");
?>
    <div class="wrap">
        <h2 id="write-post"><?php _e("Related Posts Options&hellip;",'wp_related_posts');?></h2>
        <p><?php _e("WordPress Related Posts Plugin will generate a related posts via WordPress tags, and add the related posts to feed.",'wp_related_posts');?></p>
        <div style="float:right;">
          <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
          <input type="hidden" name="cmd" value="_donations">
          <input type="hidden" name="business" value="honghua.deng@gmail.com">
          <input type="hidden" name="item_name" value="Donate to fairyfish.net">
          <input type="hidden" name="no_shipping" value="0">
          <input type="hidden" name="no_note" value="1">
          <input type="hidden" name="currency_code" value="USD">
          <input type="hidden" name="tax" value="0">
          <input type="hidden" name="lc" value="US">
          <input type="hidden" name="bn" value="PP-DonationsBF">
          <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
          <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1"><br />
          </form>
        </div>
        <h3><?php _e("Related Posts Preference",'wp_related_posts');?></h3>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo basename(__FILE__); ?>">
        
        <table class="form-table">
          <tr>
            <th><?php _e("Related Posts Title:",'wp_related_posts'); ?></th>
            <td>
              <input type="text" name="wp_rp_title_option" value="<?php echo $wp_rp["wp_rp_title"]; ?>" />
            </td>
          </tr>
          <tr>
            <th><?php _e("When No Related Posts, Dispaly:",'wp_related_posts'); ?></th>
            <td>
              <?php $wp_no_rp = $wp_rp["wp_no_rp"]; ?>
              <select name="wp_no_rp_option" >
              <option value="text" <?php if($wp_no_rp == 'text') echo 'selected' ?> ><?php _e("Text: 'No Related Posts'",'wp_related_posts'); ?></option>
              <option value="random" <?php if($wp_no_rp == 'random') echo 'selected' ?>><?php _e("Random Posts",'wp_related_posts'); ?></option>
              <option value="commented" <?php if($wp_no_rp == 'commented') echo 'selected' ?>><?php _e("Most Commented Posts",'wp_related_posts'); ?></option>
              <?php if (function_exists('akpc_most_popular')){ ?>
              <option value="popularity" <?php if($wp_no_rp == 'popularity') echo 'selected' ?>><?php _e("Most Popular Posts",'wp_related_posts'); ?></option>
              <?php } ?> 
              </select>
            </td>
          </tr>
          <tr>
            <th><?php _e("No Related Post's Title or Text:",'wp_related_posts'); ?></th>
            <td>
              <input type="text" name="wp_no_rp_text_option" value="<?php echo $wp_rp["wp_no_rp_text"]; ?>" />
            </td>
          </tr>
          <tr>
            <th><?php _e("Limit:",'wp_related_posts');?></th>
            <td>
              <input type="text" name="wp_rp_limit_option" value="<?php echo $wp_rp["wp_rp_limit"]; ?>" />
            </td>
          </tr>
          <tr>
            <th><?php _e("Exclude(category IDs):",'wp_related_posts');?></th>
            <td>
              <input type="text" name="wp_rp_exclude_option" value="<?php echo $wp_rp["wp_rp_exclude"]; ?>" />
            </td>
          </tr>
          <tr>
            <th><?php _e("Other Setting:",'wp_related_posts');?></th>
            <td>
              <label>
              <?php
              if ( $wp_rp["wp_rp_auto"] == 'yes' ) {
              echo '<input name="wp_rp_auto_option" type="checkbox" value="yes" checked>';
              } else {
              echo '<input name="wp_rp_auto_option" type="checkbox" value="yes">';
              }
              ?>
              <?php _e("Auto Insert Related Posts",'wp_related_posts');?>
              </label>
              <br />
              <label>
              <?php
              if ( $wp_rp["wp_rp_rss"] == 'yes' ) {
                echo '<input name="wp_rp_rss_option" type="checkbox" value="yes" checked>';
              } else {
                echo '<input name="wp_rp_rss_option" type="checkbox" value="yes">';
              }
              ?>
              <?php _e("Related Posts for RSS",'wp_related_posts');?>
              </label>
              <br />
              <label>
              <?php
              if ( $wp_rp["wp_rp_comments"] == 'yes' ) {
                echo '<input name="wp_rp_comments_option" type="checkbox" value="yes" checked>';
              } else {
                echo '<input name="wp_rp_comments_option" type="checkbox" value="yes">';
              }
              ?>
              <?php _e("Display Comments Count",'wp_related_posts');?>
              </label>
              <br />
              <label>
              <?php
              if ( $wp_rp["wp_rp_date"] == 'yes' ) {
                echo '<input name="wp_rp_date_option" type="checkbox" value="yes" checked>';
              } else {
                echo '<input name="wp_rp_date_option" type="checkbox" value="yes">';
              }
              ?>
              <?php _e("Display Post Date",'wp_related_posts');?>
              </label>
              <br />
            </td>
          </tr>
        </table>
        <p class="submit"><input type="submit" value="<?php _e("Update Preferences &raquo;",'wp_related_posts');?>" name="wp_rp_Submit" /></p>
        </form>
      </div>
<?php }


/* 
Function Name: getCategories 
Version: 1.0 
Description: Gets the list of categories. Represents a workaround for the get_categories bug in WP 2.8 
Author: Dumitru Brinzan
Author URI: http://www.brinzan.net 
*/ 

function getCategories($parent) {

	global $wpdb, $table_prefix;
	
	$tb1 = "$table_prefix"."terms";
	$tb2 = "$table_prefix"."term_taxonomy";
	
	if ($parent == '1')
	{
	 $qqq = "AND $tb2".".parent = 0";
  }
  else
  {
    $qqq = "";
  }
  
	$q = "SELECT $tb1.term_id,$tb1.name,$tb1.slug FROM $tb1,$tb2 WHERE $tb1.term_id = $tb2.term_id AND $tb2.taxonomy = 'category' $qqq ORDER BY $tb1.name ASC";
	$q = $wpdb->get_results($q);
	
  foreach ($q as $cat) {
    	$categories[$cat->term_id] = $cat->name;
    } // foreach
  return($categories);
} // end func

/* 
Function Name: getPages 
Version: 1.0 
Description: Gets the list of pages. Represents a workaround for the get_categories bug in WP 2.8 
Author: Dumitru Brinzan
Author URI: http://www.brinzan.net 
*/ 

function getPages() {

	global $wpdb, $table_prefix;
	
	$tb1 = "$table_prefix"."posts";
  
	$q = "SELECT $tb1.ID,$tb1.post_title FROM $tb1 WHERE $tb1.post_type = 'page' AND $tb1.post_status = 'publish' ORDER BY $tb1.post_title ASC";
	$q = $wpdb->get_results($q);
	
  foreach ($q as $pag) {
    	$pages[$pag->ID] = $pag->post_title;
    } // foreach
  return($pages);
} // end func

		$categories = getCategories(0);
		$pages = getPages();
		
		foreach ( $categories as $key => $value ) {

			$catids[] = $key;
			$catnames[] = $value;
		}
		
		$homepath = get_bloginfo('stylesheet_directory');
		
		



###############################################################################################################

/* Yamidoo Theme Settings Panel in Dashboard */
$themename = "Yamidoo";
$shortname = "ft";
$options = array (

array(    "name" => "Yamidoo Global Settings",
        "type" => "title"),

array(    "type" => "open"),

array(    "type" => "start-column",
          "name" => "Featured Content Settings"),

array(    "name" => "Featured Category 1",
        "desc" => "Select the category which should be featured as #1 on the homepage.",
        "id" => $shortname."_featured_category_1",
        "categoryids" => $catids,
        "categorynames" => $catnames,
        "std" => "",
        "type" => "select-category"),

array(    "name" => "Featured Category 2",
        "desc" => "Select the category which should be featured as #2 on the homepage.",
        "id" => $shortname."_featured_category_2",
        "categoryids" => $catids,
        "categorynames" => $catnames,
        "std" => "",
        "type" => "select-category"),

array(    "name" => "Featured Category 3",
        "desc" => "Select the category which should be featured as #3 on the homepage.",
        "id" => $shortname."_featured_category_3",
        "categoryids" => $catids,
        "categorynames" => $catnames,
        "std" => "",
        "type" => "select-category"),
        
array(    "name" => "Featured Category 4",
        "desc" => "Select the category which should be featured as #4 on the homepage.",
        "id" => $shortname."_featured_category_4",
        "categoryids" => $catids,
        "categorynames" => $catnames,
        "std" => "",
        "type" => "select-category"),

array(    "name" => "Custom field for photos",
        "desc" => "Select the name of the custom field for photos.<br />Default: image",
        "id" => $shortname."_cf_photo",
        "std" => "image",
        "type" => "text"),

array(    "name" => "Slideshow Category",
        "desc" => "Select the category which you want to show in the SlideShow Section. It could be a category with name 'Feature'",
        "id" => $shortname."_featured_category_5",
        "categoryids" => $catids,
        "categorynames" => $catnames,
        "std" => "",
        "type" => "select-category"),
        
array(    "name" => "Slideshow Transition Direction",
        "desc" => "Choose the direction for the slideshow",
        "id" => $shortname."_featured_slideshow_effect",
        "options" => array('leftright', 'rightleft'),
        "std" => "leftright",
        "type" => "select"),

array(    "name" => "Slideshow Transition Speed",
        "desc" => "Select the speed (in miliseconds) with which the slideshow should change the pictures. Default: 3000 (3 seconds).",
        "id" => $shortname."_featured_slideshow_speed",
        "std" => "3000",
        "type" => "text"),

array(    "type" => "end-column"),

array(    "type" => "start-column",
          "name" => "Banner Management"),

array(    "name" => "Add banner in the header?",
        "desc" => "Display a banner in the header?",
        "id" => $shortname."_ad_head_select",
        "options" => array('No', 'Yes'),
        "std" => "No",
        "type" => "select"),

array(    "name" => "Header Banner HTML Code",
        "desc" => "Enter complete HTML code for your banner.",
        "id" => $shortname."_ad_head_imgpath",
        "std" => "",
        "type" => "textarea"),
        
array(    "name" => "Add banner in the sidebar?",
        "desc" => "Display a banner in the sidebar?",
        "id" => $shortname."_ad_side_select",
        "options" => array('No', 'Yes'),
        "std" => "No",
        "type" => "select"),

array(    "name" => "Sidebar Banner HTML Code",
        "desc" => "Enter complete HTML code for your banner.",
        "id" => $shortname."_ad_side_imgpath",
        "std" => "",
        "type" => "textarea"),

array(    "type" => "end-column"),

array(    "type" => "start-column",
          "name" => "Miscellaneous Settings"),

array(    "name" => "Include tracking script in footer?",
        "desc" => "If you want to add some tracking script to the footer, like Google Analytics, choose Yes",
        "id" => $shortname."_misc_analytics_select",
        "options" => array('No', 'Yes'),
        "std" => "No",
        "type" => "select"),

array(    "name" => "Tracking Script Code",
        "desc" => "Insert the complete tracking script that should be included in the footer.",
        "id" => $shortname."_misc_analytics",
        "std" => "",
        "type" => "textarea"),

array(    "name" => "FeedBurner Feed Address",
        "desc" => "If you want to use Google Feedburner to track your RSS, insert your Feedburner Feed Address.<br />Example: <strong>http://feeds2.feedburner.com/wpzoom</strong><br />Leave it blank if you want to use Analytics the standard Wordpress Feed.",
        "id" => $shortname."_misc_feedburner",
        "std" => "",
        "type" => "text"),

array(    "name" => "Feedburner ID for Email Subscriptions",
        "desc" => "Insert the ID from your feedburner URL. <br />Example: <strong>http://feeds2.feedburner.com/THIS-IS-YOUR-ID/</strong><br />Leave it blank if you don't want to provide such a feature (in the header).",
        "id" => $shortname."_misc_feedburnerID",
        "std" => "",
        "type" => "text"),
        
array(    "type" => "end-column"),

array(    "type" => "close")

);

function mytheme_add_admin() {

    global $themename, $shortname, $options;

    if ( $_GET['page'] == basename(__FILE__) ) {

        if ( 'save' == $_REQUEST['action'] ) {

                foreach ($options as $value) {
                    update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }

                foreach ($options as $value) {
                    if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); } }

                header("Location: themes.php?page=functions.php&saved=true");
                die;

        } else if( 'reset' == $_REQUEST['action'] ) {

            foreach ($options as $value) {
                delete_option( $value['id'] ); }

            header("Location: themes.php?page=functions.php&reset=true");
            die;

        }
    }

    add_theme_page($themename." Options", "".$themename." Options", 'edit_themes', basename(__FILE__), 'mytheme_admin');

}

function mytheme_admin() {

    global $themename, $shortname, $options;

    if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings saved.</strong></p></div>';
    if ( $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings reset.</strong></p></div>';

?>
<div class="wrap">
<h1><?php echo $themename; ?> settings</h1>
<style type="text/css">
<!--
table {font-size: 12px; font-family: Georgia, sans-serif; }
div.column {width: 95%; float: left; padding: 10px 15px; vertical-align: top; background-color: #f1f1f1; margin: 0 20px 20px 0; border: solid 5px #e1e1e1;} 
div.column input, div.column select, div.column textarea {border: solid 1px #aaa; padding-left: 5px; font-size: 12px; margin-left: 5px; width: 350px; }
div.column input {height: 30px; line-height: 30px; }
div.column input.checkbox {width: 30px;}
div.column textarea {width: 500px; height: 150px;}
div.column td {padding: 10px 0 0; }
div.column input.submit {font-weight: normal; font-size: 11px; border: none; width: auto; height: auto; padding:0; margin:0 0 10px; }
div.column td.label {width: 300px; font-weight: bold; background-color: #ddd; padding:0 5px; line-height: 18px; font-size: 12px; }
div.column tr.sep1 td {border-bottom: dotted 1px #000; height: 10px; font-size: 1px; line-height: 1px; }
div.column tr.description td p {font-size: 11px; color: #666; font-family: Tahoma, sans-serif; line-height: 15px;}
div.column td.save {text-align: center; background-color: #ddd;}
div.column h2 span.top {font-size: 14px; font-style: normal;}
div.cleaner {font-size: 1px; line-height:1px; height:1px; clear: left; margin: 5px 0; }

ul.navMenu {margin: 10px 0 50px; padding:0; }
ul.navMenu li {float: left; display: inline; margin-right: 1px; font-size: 12px; font-weight: bold; }
ul.navMenu li a {padding: 5px 9px; background-color: #777; color: #fff; }
ul.navMenu li a:hover {background-color: #3dcef2;}
-->
</style>
<a id="top" href="#"></a>
<h2>Check our <a href="http://www.wpzoom.com/forum/">support forums</a> if you find any difficulties with this theme or if you need theme customization.</h2>
<div class="cleaner">&nbsp;</div>
<form method="post">
<?php global $homepath; ?>
<?php foreach ($options as $value) {

switch ( $value['type'] ) {

case "open":
?>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td>
<?php break;

case "close":
?>

</td></tr>
</table><br />

<?php break;

case "start-column":
?>
<div class="column" id="<?php echo $value['id']; ?>">
  <h2><?php echo $value['name']; ?> <span class="top">(<a href="#top">back to top</a>)</span></h2>
  <table width="100%" cellpadding="0" cellspacing="0" border="0">

<?php break;

case "end-column":
?>
<tr>
   <td colspan="2" class="save"><input name="save" type="image" src="<?php echo $homepath; ?>/images/bt_save.png" value="Save Changes" class="submit" /></td>
   </tr>
</table>
</div>

<?php break;

case "separator":
?>
        <tr class="sep1">
          <td colspan="2">&nbsp;</td>
        </tr>

<?php break;

case "cleaner":
?>
        <div style="font-size: 1px; line-height:1px; clear: left; margin: 5px 0; ">&nbsp;</div>

<?php break;

case 'text':
?>

<tr>
    <td class="label"><?php echo $value['name']; ?></td>
    <td><input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_settings( $value['id'] ) != "") { echo get_settings( $value['id'] ); } else { echo $value['std']; } ?>" /></td>
</tr>
<tr class="description">
    <td colspan="2"><p><?php echo $value['desc']; ?></p></td>
</tr>
<tr class="sep1">
          <td colspan="2">&nbsp;</td>
        </tr>
<?php
break;

case 'textarea':
?>

<tr>
    <td class="label"><?php echo $value['name']; ?></td>
    <td><textarea name="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" cols="" rows=""><?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id'] )); } else { echo $value['std']; } ?></textarea></td>

</tr>

<tr class="description">
    <td colspan="2"><p><?php echo $value['desc']; ?></p></td>
</tr>
<tr class="sep1">
          <td colspan="2">&nbsp;</td>
        </tr>
<?php
break;

case 'select':
?>
<tr>
    <td class="label"><?php echo $value['name']; ?></td>
    <td><select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>"><?php foreach ($value['options'] as $option) { ?><option<?php if ( get_settings( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option><?php } ?></select></td>
</tr>

<tr class="description">
    <td colspan="2"><p><?php echo $value['desc']; ?></p></td>
</tr>
<tr class="sep1">
          <td colspan="2">&nbsp;</td>
        </tr>
<?php
break;

case 'select-category':
?>
<tr>
    <td class="label"><?php echo $value['name']; ?></td>
    <td><select name="<?php echo $value['id']; ?>"><?php foreach ($value['categoryids'] as $key => $val) { ?><option value="<?php echo"$val";?>"<?php if ( get_settings( $value['id'] ) == $val) { echo ' selected="selected"'; } ?>><?php echo $value['categorynames'][$key]; ?></option><?php } ?></select></td>
</tr>

<tr class="description">
    <td colspan="2"><p><?php echo $value['desc']; ?></p></td>
</tr>
<tr class="sep1">
          <td colspan="2">&nbsp;</td>
        </tr>
<?php
break;

case 'select-category-multi':

$activeoptions = get_settings( $value['id'] );

if (!$activeoptions)
{
$activeoptions = array();
}

?>
<tr>
    <td class="label"><?php echo $value['name']; ?></td>
    <td><select multiple="true" name="<?php echo $value['id']; ?>[]" style="height: 150px;">
    <?php foreach ($value['categoryids'] as $key => $val) { ?><option value="<?php echo"$val";?>"<?php if ( in_array($val,$activeoptions)) { echo ' selected="selected"'; } ?>><?php echo $value['categorynames'][$key]; ?></option><?php } ?></select></td>
</tr>

<tr class="description">
    <td colspan="2"><p><?php echo $value['desc']; ?></p></td>
</tr>
<tr class="sep1">
          <td colspan="2">&nbsp;</td>
        </tr>
<?php
break;

case "checkbox":
?>
    <tr>
    <td class="label"><?php echo $value['name']; ?></td>
    <td><?php if(get_settings($value['id'])){ $checked = "checked=\"checked\""; }else{ $checked = ""; } ?><input type="checkbox" class="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?> /></td>
    </tr>
<tr class="description">
    <td colspan="2"><p><?php echo $value['desc']; ?></p></td>
</tr>
<tr class="sep1">
          <td colspan="2">&nbsp;</td>
        </tr>
<?php break; } } ?>
<p class="submit">
<input name="save" type="submit" value="Save changes" />
<input type="hidden" name="action" value="save" />
</p>
</form>
<form method="post">
<p class="submit">
<input name="reset" type="submit" value="Reset" />
<input type="hidden" name="action" value="reset" />
</p>
</form>

<?php
}

add_action('admin_menu', 'mytheme_add_admin');

###############################################################################################################

function legacy_comments($file) {
	if(!function_exists('wp_list_comments')) 	$file = TEMPLATEPATH . '/legacy.comments.php';
	return $file;
}

add_filter('comments_template', 'legacy_comments');

// Generates Custom Fields
function get_custom_field_value($szKey, $bPrint = false) {
	
  global $post;
	
  $szValue = get_post_meta($post->ID, $szKey, true);
	
  if ( $bPrint == false )
  { 
  return $szValue;
  } 
  else { echo $szValue; }
} 

// Generates 3 sidebars
if ( function_exists('register_sidebars') ) {
   register_sidebars(3,array(
       'before_widget' => '<ul id="%1$s" class="widget">',
       'after_widget' => '</ul>',
       'before_title' => '<h3>',
       'after_title' => '</h3>'));
} 

 
 
register_sidebar_widget( 'FT: Yamidoo Recent Comments', 'recent_comments' );
register_widget_control( 'FT: Yamidoo Recent Comments', 'recent_comments_admin', 300, 200 );
register_sidebar_widget( 'FT: Yamidoo Popular Posts', 'popular_posts' );
register_widget_control( 'FT: Yamidoo Popular Posts', 'popular_posts_admin', 300, 200 ); ?>
