<?php

function tsr_the_section($post) {
	$section_categ_id = get_cat_ID('Sections');
	
	if ($section_categ_id == null) {
		error_log("Couldn't find Sections category");
		echo "No section";
		return;
	}
	
	$post_categories = get_the_category($post->id);
	foreach ($post_categories as $category) {
		if ($category->parent == $section_categ_id) {
			$url = get_category_link($category);
			$name = $category->name;
			echo "<a href='$url'>$name</a>";
			return;
		}
	}
	
	error_log("Couldn't find section for post");
	echo "No section";
}

function tsr_the_issue($post) {
	$section_categ_id = get_cat_ID('Issues');
	
	if ($section_categ_id == null) {
		error_log("Couldn't find Issues category");
		echo "No section";
		return;
	}
	
	$post_categories = get_the_category($post->id);
	foreach ($post_categories as $category) {
		if ($category->parent == $section_categ_id) {
			$url = get_category_link($category);
			$name = $category->name;
			echo "<a href='$url'>$name</a>";
			return;
		}
	}
	
	error_log("Couldn't find issue for post");
	echo "No section";
}


function tsr_section_name_from_block_name($block_name) {
	$remove_str = ' FP Block';
	return str_replace($remove_str, '', $block_name);
}

function &tsr_get_photos($post) {
	$photos = &get_children(array('post_type'=>'attachment', 'post_parent'=>$post->ID));
	return $photos;
}

function tsr_get_photo($post) {
	$photos = &tsr_get_photos($post);
	if (!$photos) return null;
	return reset($photos); # first elem of array
}

function tsr_photo_path($post) {
	$photo = tsr_get_photo($post);
	if (!$photo) return null;
	$url = $photo->guid;
	$path = str_replace('http://new.stanfordreview.org', '', $url);
	return $path;
}

?>