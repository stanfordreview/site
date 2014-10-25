<?php
add_action('after_setup_theme', 'kopa_front_after_setup_theme');

function kopa_front_after_setup_theme() {
    add_theme_support('post-formats', array('gallery', 'audio', 'video'));
    add_theme_support('post-thumbnails');
    add_theme_support('loop-pagination');
    add_theme_support('automatic-feed-links');

    global $content_width;
    if ( ! isset( $content_width ) ) {
        $content_width = 911;
    }

    register_nav_menus(array(
        'top-nav'      => __( 'Top Menu (All Items Flat)', kopa_get_domain() ),
        'main-nav'     => __( 'Main Menu', kopa_get_domain() ),
        'bottom-nav'   => __( 'Bottom Menu (All Items Flat)', kopa_get_domain() ),
    ));

    if (!is_admin()) {
        add_filter('wp_title', 'kopa_wp_title', 10, 2);
        add_action('wp_enqueue_scripts', 'kopa_front_enqueue_scripts');
        add_action('wp_footer', 'kopa_footer');
        add_action('wp_head', 'kopa_head');
        add_filter('widget_text', 'do_shortcode');
        add_filter('the_category', 'kopa_the_category');
        // add_filter('get_the_excerpt', 'kopa_get_the_excerpt');
        add_filter('post_class', 'kopa_post_class');
        add_filter('body_class', 'kopa_body_class');
        // add_filter('wp_nav_menu_items', 'kopa_add_icon_home_menu', 10, 2);
        add_filter('comment_reply_link', 'kopa_comment_reply_link');
        add_filter('edit_comment_link', 'kopa_edit_comment_link');
        // add_filter('wp_tag_cloud', 'kopa_tag_cloud');
        add_filter('excerpt_length', 'kopa_custom_excerpt_length');
        add_filter('excerpt_more', 'kopa_custom_excerpt_more');
    } else {
        // add_action('show_user_profile', 'kopa_edit_user_profile');
        // add_action('edit_user_profile', 'kopa_edit_user_profile');
        // add_action('personal_options_update', 'kopa_edit_user_profile_update');
        // add_action('edit_user_profile_update', 'kopa_edit_user_profile_update');
        add_filter('image_size_names_choose', 'kopa_image_size_names_choose');
    }

    kopa_add_image_sizes();
}

function kopa_tag_cloud($out) {

    $matches = array();
    $pattern = '/<a[^>]*?>([\\s\\S]*?)<\/a>/';
    preg_match_all($pattern, $out, $matches);

    $htmls = $matches[0];
    $texts = $matches[1];
    $new_html = '';
    for ($index = 0; $index < count($htmls); $index++) {

        $new_html.= preg_replace('#(<a.*?(href=\'.*?\').*?>).*?(</a>)#', '<a '.'$2'.'>' . $texts[$index] . '$3' . ' ', $htmls[$index]);
    }

    return $new_html;
}

function kopa_comment_reply_link($link) {
    return str_replace('comment-reply-link', 'comment-reply-link reply-link', $link);
}

function kopa_edit_comment_link($link) {
    return str_replace('comment-edit-link', 'comment-edit-link edit-link', $link);
}

function kopa_post_class($classes) {
    return $classes;
}

function kopa_body_class($classes) {
    $template_setting = kopa_get_template_setting();

    if (is_home() && is_front_page()) {
        $classes[] = 'sub-page';
    } elseif (is_front_page()) {
        $classes[] = 'home-page';
    } else {
        $classes[] = 'sub-page';
    }

    switch ($template_setting['layout_id']) {
        case 'single':
            $classes[] = 'kp-single-page';
            break;
        case 'page-2':
            $classes[] = 'full-width';
            break;
    }

    return $classes;
}

function kopa_footer() {
    wp_nonce_field('kopa_set_view_count', 'kopa_set_view_count_wpnonce', false);

    $kopa_theme_options_tracking_code = get_option('kopa_theme_options_tracking_code');
    if (!empty($kopa_theme_options_tracking_code)) {
        echo htmlspecialchars_decode(stripslashes($kopa_theme_options_tracking_code));
    }
}

function kopa_front_enqueue_scripts() {
    if (!is_admin()) {
        global $wp_styles, $is_IE, $wp_version;

        $dir = get_template_directory_uri();

        /*======================================================
         *================THEME OPTIONS CUSTOM FONTS============
         *======================================================*/
        $google_fonts = kopa_get_google_font_array();
        $current_heading_font = get_option('kopa_theme_options_heading_font_family');
        $current_content_font = get_option('kopa_theme_options_content_font_family');
        $current_main_nav_font = get_option('kopa_theme_options_main_nav_font_family');
        $current_wdg_sidebar_font = get_option('kopa_theme_options_wdg_sidebar_font_family');
        $load_font_array = array();

        // heading font family
        if ($current_heading_font && ! in_array($current_heading_font, $load_font_array)) {
            array_push($load_font_array, $current_heading_font);
        }

        // content font family
        if ($current_content_font && ! in_array($current_content_font, $load_font_array)) {
            array_push($load_font_array, $current_content_font);
        }

        // main menu font family
        if ($current_main_nav_font && ! in_array($current_main_nav_font, $load_font_array)) {
            array_push($load_font_array, $current_main_nav_font);
        }

        // widget title font family  
        if ($current_wdg_sidebar_font && ! in_array($current_wdg_sidebar_font, $load_font_array)) {
            array_push($load_font_array, $current_wdg_sidebar_font);
        }

        foreach ($load_font_array as $current_font) {
            if ($current_font != '') {
                $google_font_family = $google_fonts[$current_font]['family'];
                $temp_font_name = str_replace(' ', '+', $google_font_family);
                $font_url = 'https://fonts.googleapis.com/css?family=' . $temp_font_name . ':300,300italic,400,400italic,700,700italic&subset=latin';
                wp_enqueue_style('Google-Font-' . $temp_font_name, $font_url);
            }
        }
        /*======================================================
         *==============END THEME OPTIONS CUSTOM FONTS==========
         *======================================================*/

        /* STYLESHEETs */ 

        // don't load content font if theme options sets another content font
        if ( empty( $current_content_font ) ) {
            if ( is_user_logged_in() && version_compare($wp_version, '3.8', '>=') ) {
                wp_enqueue_style('kopa-default-content-font', 'https://fonts.googleapis.com/css?family=Open+Sans:700');
            } else {
                wp_enqueue_style('kopa-default-content-font', 'https://fonts.googleapis.com/css?family=Open+Sans:400,700,600');
            }
        } // endif

        wp_enqueue_style('kopa-bootstrap', $dir . '/css/bootstrap.min.css');
        wp_enqueue_style('font-awesome', $dir . '/css/font-awesome.css');
        wp_enqueue_style('kopa-superfish', $dir . '/css/superfish.css');
        wp_enqueue_style('kopa-flexslider', $dir . '/css/flexslider.css');
        wp_enqueue_style('kopa-prettyPhoto', $dir . '/css/prettyPhoto.css');
        wp_enqueue_style('kopa-style', get_stylesheet_uri());
        wp_enqueue_style('kopa-extra-style', $dir . '/css/extra.css');

        // enable/disable responsive layout
        if ( 'enable' === get_option( 'kopa_theme_options_responsive_status', 'enable' ) ) {
            wp_enqueue_style('kopa-responsive', $dir . '/css/responsive.css');
        }

        if ( $is_IE ) {
            wp_register_style('kopa-ie', $dir . '/css/ie.css');
            $wp_styles->add_data('kopa-ie', 'conditional', 'lt IE 9');
            wp_enqueue_style('kopa-ie');

            /**
             * for loading scripts for ie version < ie9
             * @link http://stackoverflow.com/questions/5302302/php-if-internet-explorer-6-7-8-or-9
             */
            $ie_matches = '';
            preg_match('/MSIE (.*?);/', $_SERVER['HTTP_USER_AGENT'], $ie_matches);
            if ( count( $ie_matches ) > 1 ) {
                $ie_version = $ie_matches[1];
                if ( $ie_version < 9 ) {
                    wp_enqueue_script('kopa-ie-html5shiv', $dir . '/js/html5shiv.js');
                    wp_enqueue_script('kopa-ie-respond', $dir . '/js/respond.min.js');
                    wp_enqueue_script('kopa-css3-mediaqueries', $dir . '/js/css3-mediaqueries.js');
                    wp_enqueue_script('kopa-pie-678', $dir . '/js/PIE_IE678.js');
                }
            }
        }

        /* JAVASCRIPTs */
        wp_enqueue_script('jquery');
        wp_localize_script('jquery', 'kopa_front_variable', kopa_front_localize_script());
        wp_enqueue_script('kopa-superfish-js', $dir . '/js/superfish.js', array('jquery'), null, true);
        wp_enqueue_script('kopa-retina-js', $dir . '/js/retina.js', array('jquery'), null, true);
        wp_enqueue_script('kopa-bootstrap-js', $dir . '/js/bootstrap.min.js', array('jquery'), null, true);
        wp_enqueue_script('kopa-carouFredSel-js', $dir . '/js/jquery.carouFredSel-6.2.1-packed.js', array('jquery'), null, true);
        wp_enqueue_script('kopa-flexslider-js', $dir . '/js/jquery.flexslider-min.js', array('jquery'), null, true);
        wp_enqueue_script('kopa-prettyPhoto-js', $dir . '/js/jquery.prettyPhoto.js', array('jquery'), null, true);
        wp_enqueue_script('kopa-validate-js', $dir . '/js/jquery.validate.min.js', array('jquery'), null, true);
        wp_enqueue_script('kopa-form-js', $dir . '/js/jquery.form.js', array('jquery'), null, true);
        wp_enqueue_script('kopa-timeago-js', $dir . '/js/jquery.timeago.js', array('jquery'), null, true);
        wp_enqueue_script('kopa-tweetable-js', $dir . '/js/tweetable.jquery.min.js', array('jquery'), null, true);
        wp_enqueue_script('kopa-mousewheel-js', $dir . '/js/jquery.mousewheel.js', array('jquery'), null, true);
        wp_enqueue_script('kopa-modernizr-transitions-js', $dir . '/js/modernizr-transitions.js', array('jquery'), null, true);        
        wp_enqueue_script('kopa-set-view-count', $dir . '/js/set-view-count.js', array('jquery'), null, true);
        wp_enqueue_script('kopa-stick-up-js', $dir . '/js/stickUp.min.js', array('jquery'), null, true);
        wp_enqueue_script( 'kopa-article-list-ajax-load-more', $dir . '/js/kopa-article-list-ajax-load-more.js', array( 'jquery' ), '1.0', true );
        wp_enqueue_script('kopa-custom-js', $dir . '/js/custom.js', array('jquery'), null, true);

        // send localization to frontend
        wp_localize_script('kopa-custom-js', 'kopa_custom_front_localization', kopa_custom_front_localization());

        if (is_single() || is_page()) {
            wp_enqueue_script('comment-reply');
        }
    }
}

function kopa_front_localize_script() {
    $kopa_variable = array(
        'ajax' => array(
            'url' => admin_url('admin-ajax.php')
        ),
        'template' => array(
            'post_id' => (is_singular()) ? get_queried_object_id() : 0
        )
    );
    return $kopa_variable;
}

/**
 * Send the translated texts to frontend
 * @package Circle
 * @since Circle 1.12
 */
function kopa_custom_front_localization() {
    $front_localization = array(
        'validate' => array(
            'form' => array(
                'submit'  => __('SEND', kopa_get_domain()),
                'sending' => __('SENDING...', kopa_get_domain())
            ),
            'name' => array(
                'required'  => __('Please enter your name.', kopa_get_domain()),
                'minlength' => __('At least {0} characters required.', kopa_get_domain())
            ),
            'email' => array(
                'required' => __('Please enter your email.', kopa_get_domain()),
                'email'    => __('Please enter a valid email.', kopa_get_domain())
            ),
            'url' => array(
                'required' => __('Please enter your url.', kopa_get_domain()),
                'url'      => __('Please enter a valid url.', kopa_get_domain())
            ),
            'message' => array(
                'required'  => __('Please enter a message.', kopa_get_domain()),
                'minlength' => __('At least {0} characters required.', kopa_get_domain())
            )
        )
    );

    return $front_localization;
}

function kopa_the_category($thelist) {
    return $thelist;
}

/* FUNCTION */

/**
 * Create a nicely formatted and more specific title element text for output
 * in head of document, based on current view.
 *
 * @package Nictitate
 * 
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string The filtered title.
 */
function kopa_wp_title( $title, $sep ) {
    global $paged, $page;

    if ( is_feed() ) {
        return $title;
    }

    // Add the site name.
    $title .= get_bloginfo( 'name' );

    // Add the site description for the home/front page.
    $site_description = get_bloginfo( 'description', 'display' );
    if ( $site_description && ( is_home() || is_front_page() ) ) {
        $title = "$title $sep $site_description";
    }

    // Add a page number if necessary.
    if ( $paged >= 2 || $page >= 2 ) {
        $title = "$title $sep " . sprintf( __( 'Page %s', kopa_get_domain() ), max( $paged, $page ) );
    }

    return $title;
}

function kopa_get_image_sizes() {
    $sizes = array(
        'kopa-article-list-size'    => array(224, 336, true, __('Articles list widget size (Kopatheme)', kopa_get_domain())),
        'kopa-entry-list-size'      => array(324, 429, true, __('Entries list widget size (Kopatheme)', kopa_get_domain())),
        'kopa-entry-list-sm-size'   => array(180, 200, true, __('Entries list widget small size (Kopatheme)', kopa_get_domain())),
        'kopa-single-featured-size' => array(316, 400, false, __('Single featured image size (Kopatheme)', kopa_get_domain())),
    );

    return apply_filters('kopa_get_image_sizes', $sizes);
}

function kopa_add_image_sizes() {
    $sizes = kopa_get_image_sizes();
    foreach ($sizes as $slug => $details) {
        add_image_size($slug, $details[0], $details[1], $details[2]);
    }
}

function kopa_image_size_names_choose($sizes) {
    $kopa_sizes = kopa_get_image_sizes();
    foreach ($kopa_sizes as $size => $image) {
        $width = ($image[0]) ? $image[0] : __('auto', kopa_get_domain());
        $height = ($image[1]) ? $image[1] : __('auto', kopa_get_domain());
        $sizes[$size] = $image[3] . " ({$width} x {$height})";
    }
    return $sizes;
}

function kopa_set_view_count($post_id) {
    $new_view_count = 0;
    $meta_key = 'kopa_' . kopa_get_domain() . '_total_view';

    $current_views = (int) get_post_meta($post_id, $meta_key, true);

    if ($current_views) {
        $new_view_count = $current_views + 1;
        update_post_meta($post_id, $meta_key, $new_view_count);
    } else {
        $new_view_count = 1;
        add_post_meta($post_id, $meta_key, $new_view_count);
    }
    return $new_view_count;
}

function kopa_get_view_count($post_id) {
    $key = 'kopa_' . kopa_get_domain() . '_total_view';
    return kopa_get_post_meta($post_id, $key, true, 'Int');
}

/**
 * Template tag: print breadcrumb
 */
function kopa_breadcrumb() {
    // get show/hide option
    $kopa_breadcrumb_status = get_option('kopa_theme_options_breadcrumb_status', 'show');

    if ( $kopa_breadcrumb_status != 'show' ) {
        return;
    }

    if (is_main_query()) {
        global $post, $wp_query;

        $prefix = '&nbsp;/&nbsp;';
        $current_class = 'current-page';
        $description = '';
        $breadcrumb_before = '<div class="breadcrumb clearfix">';
        $breadcrumb_after = '</div>';
        $breadcrumb_home = '<a href="' . home_url() . '">' . __('Home', kopa_get_domain()) . '</a>';
        $breadcrumb = '';
        ?>

        <?php
        if (is_home()) {
            $breadcrumb.= $breadcrumb_home;
            if ( get_option( 'page_for_posts' ) ) {
                $breadcrumb.= $prefix . sprintf('<span class="%1$s">%2$s</span>', $current_class, get_the_title(get_option('page_for_posts')));
            } else {
                $breadcrumb.= $prefix . sprintf('<span class="%1$s">%2$s</span>', $current_class, __('Blog', kopa_get_domain()));                
            }
        } else if (is_post_type_archive('product') && get_option('woocommerce_shop_page_id')) {
            $breadcrumb.= $breadcrumb_home;
            $breadcrumb.= $prefix . sprintf('<span class="%1$s">%2$s</span>', $current_class, get_the_title(get_option('woocommerce_shop_page_id')));
        } else if (is_tag()) {
            $breadcrumb.= $breadcrumb_home;

            $term = get_term(get_queried_object_id(), 'post_tag');
            $breadcrumb.= $prefix . sprintf('<span class="%1$s">%2$s</span>', $current_class, $term->name);
        } else if (is_category()) {
            $breadcrumb.= $breadcrumb_home;

            $category_id = get_queried_object_id();
            $terms_link = explode(',', substr(get_category_parents(get_queried_object_id(), TRUE, ','), 0, (strlen(',') * -1)));
            $n = count($terms_link);
            if ($n > 1) {
                for ($i = 0; $i < ($n - 1); $i++) {
                    $breadcrumb.= $prefix . $terms_link[$i];
                }
            }
            $breadcrumb.= $prefix . sprintf('<span class="%1$s">%2$s</span>', $current_class, get_the_category_by_ID(get_queried_object_id()));

        } else if ( is_tax('product_cat') ) {
            $breadcrumb.= $breadcrumb_home;
            $breadcrumb.= $prefix . '<a href="'.get_page_link( get_option('woocommerce_shop_page_id') ).'">'.get_the_title( get_option('woocommerce_shop_page_id') ).'</a>';
            $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );

            $parents = array();
            $parent = $term->parent;
            while ($parent):
                $parents[] = $parent;
                $new_parent = get_term_by( 'id', $parent, get_query_var( 'taxonomy' ));
                $parent = $new_parent->parent;
            endwhile;
            if( ! empty( $parents ) ):
                $parents = array_reverse($parents);
                foreach ($parents as $parent):
                    $item = get_term_by( 'id', $parent, get_query_var( 'taxonomy' ));
                    $breadcrumb .= $prefix . '<a href="' . get_term_link( $item->slug, 'product_cat' ) . '">' . $item->name . '</a>';
                endforeach;
            endif;

            $queried_object = get_queried_object();
            $breadcrumb.= $prefix . sprintf('<span class="%1$s">%2$s</span>', $current_class, $queried_object->name);
        } else if ( is_tax( 'product_tag' ) ) {
            $breadcrumb.= $breadcrumb_home;
            $breadcrumb.= $prefix . '<a href="'.get_page_link( get_option('woocommerce_shop_page_id') ).'">'.get_the_title( get_option('woocommerce_shop_page_id') ).'</a>';
            $queried_object = get_queried_object();
            $breadcrumb.= $prefix . sprintf('<span class="%1$s">%2$s</span>', $current_class, $queried_object->name);
        } else if (is_single()) {
            $breadcrumb.= $breadcrumb_home;

            if ( get_post_type() === 'product' ) :

                $breadcrumb .= $prefix . '<a href="'.get_page_link( get_option('woocommerce_shop_page_id') ).'">'.get_the_title( get_option('woocommerce_shop_page_id') ).'</a>';

                if ($terms = get_the_terms( $post->ID, 'product_cat' )) :
                    $term = apply_filters( 'jigoshop_product_cat_breadcrumb_terms', current($terms), $terms);
                    $parents = array();
                    $parent = $term->parent;
                    while ($parent):
                        $parents[] = $parent;
                        $new_parent = get_term_by( 'id', $parent, 'product_cat');
                        $parent = $new_parent->parent;
                    endwhile;
                    if(!empty($parents)):
                        $parents = array_reverse($parents);
                        foreach ($parents as $parent):
                            $item = get_term_by( 'id', $parent, 'product_cat');
                            $breadcrumb .= $prefix . '<a href="' . get_term_link( $item->slug, 'product_cat' ) . '">' . $item->name . '</a>';
                        endforeach;
                    endif;
                    $breadcrumb .= $prefix . '<a href="' . get_term_link( $term->slug, 'product_cat' ) . '">' . $term->name . '</a>';
                endif;

                $breadcrumb.= $prefix . sprintf('<span class="%1$s">%2$s</span>', $current_class, get_the_title());

            else : 

                $categories = get_the_category(get_queried_object_id());
                if ($categories) {
                    foreach ($categories as $category) {
                        $breadcrumb.= $prefix . sprintf('<a href="%1$s">%2$s</a>', get_category_link($category->term_id), $category->name);
                    }
                }

                $post_id = get_queried_object_id();
                $breadcrumb.= $prefix . sprintf('<span class="%1$s">%2$s</span>', $current_class, get_the_title($post_id));

            endif;

        } else if (is_page()) {
            if (!is_front_page()) {
                $post_id = get_queried_object_id();
                $breadcrumb.= $breadcrumb_home;
                $post_ancestors = get_post_ancestors($post);
                if ($post_ancestors) {
                    $post_ancestors = array_reverse($post_ancestors);
                    foreach ($post_ancestors as $crumb)
                        $breadcrumb.= $prefix . sprintf('<a href="%1$s">%2$s</a>', get_permalink($crumb), get_the_title($crumb));
                }
                $breadcrumb.= $prefix . sprintf('<span class="%1$s">%2$s</span>', $current_class, get_the_title(get_queried_object_id()));
            }
        } else if (is_year() || is_month() || is_day()) {
            $breadcrumb.= $breadcrumb_home;

            $date = array('y' => NULL, 'm' => NULL, 'd' => NULL);

            $date['y'] = get_the_time('Y');
            $date['m'] = get_the_time('m');
            $date['d'] = get_the_time('j');

            if (is_year()) {
                $breadcrumb.= $prefix . sprintf('<span class="%1$s">%2$s</span>', $current_class, $date['y']);
            }

            if (is_month()) {
                $breadcrumb.= $prefix . sprintf('<a href="%1$s">%2$s</a>', get_year_link($date['y']), $date['y']);
                $breadcrumb.= $prefix . sprintf('<span class="%1$s">%2$s</span>', $current_class, date_i18n('F', $date['m']));
            }

            if (is_day()) {
                $breadcrumb.= $prefix . sprintf('<a href="%1$s">%2$s</a>', get_year_link($date['y']), $date['y']);
                $breadcrumb.= $prefix . sprintf('<a href="%1$s">%2$s</a>', get_month_link($date['y'], $date['m']), date_i18n('F', $date['m']));
                $breadcrumb.= $prefix . sprintf('<span class="%1$s">%2$s</span>', $current_class, $date['d']);
            }

        } else if (is_search()) {
            $breadcrumb.= $breadcrumb_home;

            $s = get_search_query();
            $c = $wp_query->found_posts;

            $description = sprintf(__('<span class="%1$s">Your search for "%2$s"', kopa_get_domain()), $current_class, $s);
            $breadcrumb .= $prefix . $description;
        } else if (is_author()) {
            $breadcrumb.= $breadcrumb_home;
            $author_id = get_queried_object_id();
            $breadcrumb.= $prefix . sprintf('<span class="%1$s">%2$s</a>', $current_class, sprintf(__('Posts created by %1$s', kopa_get_domain()), get_the_author_meta('display_name', $author_id)));
        } else if (is_404()) {
            $breadcrumb.= $breadcrumb_home;
            $breadcrumb.= $prefix . sprintf('<span class="%1$s">%2$s</span>', $current_class, __('Error 404', kopa_get_domain()));
        }

        if ($breadcrumb)
            echo apply_filters('kopa_breadcrumb', $breadcrumb_before . $breadcrumb . $breadcrumb_after);
    }
}

function kopa_related_articles() {
    if (is_single()) {
        $get_by = get_option('kopa_theme_options_post_related_get_by', 'post_tag');
        if ('hide' != $get_by) {
            $limit = (int) get_option('kopa_theme_options_post_related_limit', 4);
            if ($limit > 0) {
                global $post;
                $taxs = array();
                if ('category' == $get_by) {
                    $cats = get_the_category(($post->ID));
                    if ($cats) {
                        $ids = array();
                        foreach ($cats as $cat) {
                            $ids[] = $cat->term_id;
                        }
                        $taxs [] = array(
                            'taxonomy' => 'category',
                            'field' => 'id',
                            'terms' => $ids
                        );
                    }
                } else {
                    $tags = get_the_tags($post->ID);
                    if ($tags) {
                        $ids = array();
                        foreach ($tags as $tag) {
                            $ids[] = $tag->term_id;
                        }
                        $taxs [] = array(
                            'taxonomy' => 'post_tag',
                            'field' => 'id',
                            'terms' => $ids
                        );
                    }
                }

                if ($taxs) {
                    $related_args = array(
                        'tax_query' => $taxs,
                        'post__not_in' => array($post->ID),
                        'posts_per_page' => $limit
                    );
                    $related_posts = new WP_Query( $related_args );
                    if ( $related_posts->have_posts() ) { ?>
                        
                        <div class="related-post">
                            <h5><?php _e( 'RELATED ARTICLES', kopa_get_domain() ); ?></h5>
                            <ul class="related-article clearfix">
                            <?php while ( $related_posts->have_posts() ) {
                                $related_posts->the_post();
                                ?>
                                <li>
                                    <article class="entry-item clearfix">
	                                    <?php if ( has_post_thumbnail() ) { ?>
                                        <div class="entry-thumb">
                                            <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'thumbnail' ); ?></a>
                                        </div>
                                        <?php } ?>
                                        <div class="entry-content">
                                            <h6 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h6>
                                            <span class="entry-date clearfix"><?php echo KopaIcon::getIcon('fa fa-calendar-o entry-icon', 'span'); ?><span><?php the_time( get_option( 'date_format' ) ); ?>, <?php the_time( get_option( 'time_format' ) ); ?></span></span>
                                        </div>
                                    </article>
                                </li>
                            <?php } // endwhile ?>
                            </ul> <!-- owl-carousel-related -->
                        </div> <!-- related-article -->

                        <?php
                    } // endif
                    wp_reset_postdata();
                }
            }
        }
    }
}

function kopa_social_sharing_links() {
    $display_facebook_sharing_button = get_option('kopa_theme_options_post_sharing_button_facebook', 'show');
    $display_twitter_sharing_button = get_option('kopa_theme_options_post_sharing_button_twitter', 'show');
    $display_google_sharing_button = get_option('kopa_theme_options_post_sharing_button_google', 'show');

    if ( $display_facebook_sharing_button == 'show' ||
         $display_twitter_sharing_button == 'show' ||
         $display_google_sharing_button == 'show' ) :
    ?>
    	<ul class="socials-link pull-left clearfix">
        	<li><span>Share this Post:</span></li>

        <?php if ($display_facebook_sharing_button == 'show') : ?>
            <li><a class="fa fa-facebook" href="https://www.facebook.com/share.php?u=<?php echo urlencode(get_permalink()); ?>" title="Facebook" target="_blank"></a></li>
        <?php endif; ?>

        <?php if ($display_twitter_sharing_button == 'show') : ?>
            <li><a class="fa fa-twitter" href="https://twitter.com/home?status=<?php echo get_the_title() . ':+' . urlencode(get_permalink()); ?>" title="Twitter" target="_blank"></a></li>
        <?php endif; ?>

        <?php if ($display_google_sharing_button == 'show') : ?>
            <li><a class="fa fa-google-plus" href="https://plus.google.com/share?url=<?php echo urlencode(get_permalink()); ?>" title="Google" target="_blank"></a></li>
        <?php endif; ?>

        </ul>
    <?php
    endif;
}

function kopa_about_author() {
    if ('show' != get_option('kopa_theme_options_post_about_author', 'show')) { 
        return;
    }
    ?>

    <div class="about-author clearfix">
	    <header class="clearfix">
	        <h6 class="pull-left"><?php _e( 'About the author:', kopa_get_domain() ); ?> <?php the_author_posts_link(); ?></h6>
	        <a class="author-article" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php printf( _n( 'Has %s Article', 'Has %s Articles', get_the_author_posts(), kopa_get_domain() ), get_the_author_posts() ); ?></a>
	    </header>
	    <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>" class="avatar-thumb"><?php echo get_avatar( get_the_author_meta( 'ID' ), 100 ); ?></a>                                
	    <div class="author-content">
	        <p><?php the_author_meta( 'description' ); ?></p>      
	    </div><!--author-content-->
	</div>
	<!-- about-author -->

    <?php
}

function kopa_edit_user_profile($user) {
    ?>   
    <table class="form-table">
        <tr>
            <th><label for="facebook"><?php _e('Facebook', kopa_get_domain()); ?></label></th>
            <td>
                <input type="url" name="facebook" id="facebook" value="<?php echo esc_attr(get_the_author_meta('facebook', $user->ID)); ?>" class="regular-text" /><br />
                <span class="description"><?php _e('Please enter your Facebook URL', kopa_get_domain()); ?></span>
            </td>
        </tr>
        <tr>
            <th><label for="twitter"><?php _e('Twitter', kopa_get_domain()); ?></label></th>
            <td>
                <input type="url" name="twitter" id="twitter" value="<?php echo esc_attr(get_the_author_meta('twitter', $user->ID)); ?>" class="regular-text" /><br />
                <span class="description"><?php _e('Please enter your Twitter URL', kopa_get_domain()); ?></span>
            </td>
        </tr>    
        <tr>
            <th><label for="google-plus"><?php _e('Google Plus', kopa_get_domain()); ?></label></th>
            <td>
                <input type="url" name="google-plus" id="google-plus" value="<?php echo esc_attr(get_the_author_meta('google-plus', $user->ID)); ?>" class="regular-text" /><br />
                <span class="description"><?php _e('Please enter your Google Plus URL', kopa_get_domain()); ?></span>
            </td>
        </tr>
    </table>
    <?php
}

function kopa_edit_user_profile_update($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    update_user_meta($user_id, 'facebook', $_POST['facebook']);
    update_user_meta($user_id, 'twitter', $_POST['twitter']);
    update_user_meta($user_id, 'google-plus', $_POST['google-plus']);
}

function kopa_get_the_excerpt($excerpt) {
    if (is_main_query()) {
        if (is_category() || is_tag()) {
            $limit = get_option('gs_excerpt_max_length', 100);
            if (strlen($excerpt) > $limit) {
                $break_pos = strpos($excerpt, ' ', $limit);
                $visible = substr($excerpt, 0, $break_pos);
                return balanceTags($visible);
            } else {
                return $excerpt;
            }
        } else if (is_search()) {
            $keys = implode('|', explode(' ', get_search_query()));
            return preg_replace('/(' . $keys . ')/iu', '<span class="kopa-search-keyword">\0</span>', $excerpt);
        } else {
            return $excerpt;
        }
    }
}

function kopa_get_template_setting() {
    $kopa_setting = get_option('kopa_setting');
    $setting = array();

    if (is_home()) {
        $setting = $kopa_setting['home'];
    } else if (is_post_type_archive('product')) {
        $setting = $kopa_setting['shop'];
    } else if (is_archive()) {
        if (is_category() || is_tag()) {
            $setting = get_option("kopa_category_setting_" . get_queried_object_id(), $kopa_setting['taxonomy']);
        } else if (is_tax('product_cat') || is_tax('product_tag')) {
            $setting = $kopa_setting['shop'];
        } else {
            $setting = get_option("kopa_category_setting_" . get_queried_object_id(), $kopa_setting['archive']);
        }
    } else if (is_singular()) {
        if (is_singular('post')) {
            $setting = get_option("kopa_post_setting_" . get_queried_object_id(), $kopa_setting['post']);
        } else if (is_singular('product')) {
            $setting = $kopa_setting['single-product'];
        } else if (is_page()) {

            $setting = get_option("kopa_page_setting_" . get_queried_object_id());
            if (!$setting) {
                if (is_front_page()) {
                    $setting = $kopa_setting['front-page'];
                } else {
                    $setting = $kopa_setting['page'];
                }
            }
        } else {
            $setting = $kopa_setting['post'];
        }
    } else if (is_404()) {
        $setting = $kopa_setting['_404'];
    } else if (is_search()) {
        $setting = $kopa_setting['search'];
    }

    return $setting;
}

function kopa_content_get_gallery($content, $enable_multi = false) {
    return kopa_content_get_media($content, $enable_multi, array('gallery'));
}

function kopa_content_get_video($content, $enable_multi = false) {
    return kopa_content_get_media($content, $enable_multi, array('vimeo', 'youtube', 'video'));
}

function kopa_content_get_audio($content, $enable_multi = false) {
    return kopa_content_get_media($content, $enable_multi, array('audio', 'soundcloud'));
}

function kopa_content_get_media($content, $enable_multi = false, $media_types = array()) {
    $media = array();
    $regex_matches = '';
    $regex_pattern = get_shortcode_regex();
    preg_match_all('/' . $regex_pattern . '/s', $content, $regex_matches);
    foreach ($regex_matches[0] as $shortcode) {
        $regex_matches_new = '';
        preg_match('/' . $regex_pattern . '/s', $shortcode, $regex_matches_new);

        if (in_array($regex_matches_new[2], $media_types)) :
            $media[] = array(
                'shortcode' => $regex_matches_new[0],
                'type' => $regex_matches_new[2],
                'url' => $regex_matches_new[5]
            );
            if (false == $enable_multi) {
                break;
            }
        endif;
    }

    return $media;
}


/**
 * Retrive Vimeo or Youtube Thumbnails
 * @since Nictitate Free v1.0
 */
function kopa_get_video_thumbnails_url($type, $url) {
    $thumbnails = '';
    $matches = array();
    if ('youtube' === $type) {
        preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $url, $matches);
        $file_url = "http://gdata.youtube.com/feeds/api/videos/" . $matches[0] . "?v=2&alt=jsonc";
        $results = wp_remote_get( $file_url );
        
        if ( ! is_wp_error($results) ) {
            $json = json_decode( $results['body'] );
            $thumbnails = $json->data->thumbnail->hqDefault;
        }
    } else if ('vimeo' === $type) {
        preg_match_all('#(http://vimeo.com)/([0-9]+)#i', $url, $matches);
        $imgid = $matches[2][0];
           
        $results = wp_remote_get("http://vimeo.com/api/v2/video/$imgid.php");
        
        if ( ! is_wp_error($results) ) { 
            $hash = unserialize($results['body']);
            $thumbnails = $hash[0]['thumbnail_large'];
        }
    }
    return $thumbnails;
}

function kopa_get_client_IP() {
    $IP = NULL;

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        //check if IP is from shared Internet
        $IP = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        //check if IP is passed from proxy
        $ip_array = explode(",", $_SERVER['HTTP_X_FORWARDED_FOR']);
        $IP = trim($ip_array[count($ip_array) - 1]);
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        //standard IP check
        $IP = $_SERVER['REMOTE_ADDR'];
    }
    return $IP;
}

function kopa_get_post_meta($pid, $key = '', $single = false, $type = 'String', $default = '') {
    $data = get_post_meta($pid, $key, $single);
    switch ($type) {
        case 'Int':
            $data = (int) $data;
            return ($data >= 0) ? $data : $default;
            break;
        default:
            return ($data) ? $data : $default;
            break;
    }
}

function kopa_get_like_permission($pid) {
    $permission = 'disable';

    $key = 'kopa_' . kopa_get_domain() . '_like_by_' . kopa_get_client_IP();
    $is_voted = kopa_get_post_meta($pid, $key, true, 'Int');

    if (!$is_voted)
        $permission = 'enable';

    return $permission;
}

function kopa_get_like_count($pid) {
    $key = 'kopa_' . kopa_get_domain() . '_total_like';
    return kopa_get_post_meta($pid, $key, true, 'Int');
}

function kopa_total_post_count_by_month($month, $year) {
    $args = array(
        'monthnum' => (int) $month,
        'year' => (int) $year,
    );
    $the_query = new WP_Query($args);
    return $the_query->post_count;
    ;
}

function kopa_head() {
    // contains all theme options custom styles
    $custom_styles = '';

    $logo_margin_top = get_option('kopa_theme_options_logo_margin_top');
    $logo_margin_left = get_option('kopa_theme_options_logo_margin_left');
    $logo_margin_right = get_option('kopa_theme_options_logo_margin_right');
    $logo_margin_bottom = get_option('kopa_theme_options_logo_margin_bottom');
    $kopa_theme_options_color_code = get_option('kopa_theme_options_color_code', '#222222');

    $logo_margin = '';
    if ( $logo_margin_top ) {
        $logo_margin .= "margin-top:{$logo_margin_top}px;";
    }
    if ( $logo_margin_left ) {
        $logo_margin .= "margin-left:{$logo_margin_left}px;";
    }
    if ( $logo_margin_right ) {
        $logo_margin .= "margin-right:{$logo_margin_right}px;";
    }
    if ( $logo_margin_bottom ) {
        $logo_margin .= "margin-bottom:{$logo_margin_bottom}px;";
    }
    if ( $logo_margin ) {
        $custom_styles .= "#logo-image { $logo_margin }";
    }

    /* =========================================================
      Main Colorscheme
      ============================================================ */
    if ( $kopa_theme_options_color_code != '#222222' ) {

        $custom_styles .= "
        /* MAIN COLORSCHEME */
        .kp-dropcap,
        .kp-dropcap.radius,
        .kp-newsletter-widget .newsletter-form .submit,
        #back-top a,
        #submit-comment,
        #submit-contact,
        .list-container-1 ul li.active a, 
        .list-container-1 ul li:hover a,
        .kp-button,
        .kp-bline-button:hover
        {
            background-color: $kopa_theme_options_color_code;
        }

        #mobile-menu > span,
        #toggle-view-menu h3 a,
        #toggle-view-menu span,
        #toggle-view-menu li.active span,
        #toggle-view-menu li.active h3 a,
        #toggle-view-menu .menu-panel,
        #toggle-view-menu li .menu-panel ul li,
        #toggle-view-menu .menu-panel ul li a,
        .accordion-title span,
        .pagination ul li span,
        #toggle-view li span,
        .kp-bline-button,
        .error-404 .left-col p,
        .error-404 .right-col h1,
        .error-404 .right-col a,
        .kopa-pagelink a,
        #pf-filters li a.selected
        {
            color: $kopa_theme_options_color_code;
        }

        #responsive-menu,
        #top-responsive-menu,
        .kp-search-widget .search-form .search-text:focus,
        .kp-newsletter-widget .newsletter-form .email:focus,
        .kp-newsletter-widget .newsletter-form .submit,
        #back-top a,
        #comments-form #comment_name:focus,
        #comments-form #comment_email:focus,
        #comments-form #comment_url:focus,
        #comments-form #comment_message:focus,
        #contact-form #contact_name:focus,
        #contact-form #contact_email:focus,
        #contact-form #contact_url:focus,
        #contact-form #contact_message:focus,
        #submit-comment,
        #submit-contact,
        .kp-button
        {
            border-color: $kopa_theme_options_color_code;
        }

        .elements-title,
        #related-portfolio h5,
        .kp-portfolio-detail .entry-box header .entry-title {
            border-bottom-color: $kopa_theme_options_color_code;
        }
        ";        
    } // endif

    /* ==================================================================================================
     * Custom heading color
     * ================================================================================================= */
    $kopa_theme_options_body_text_color_code = get_option( 'kopa_theme_options_body_text_color_code', '#666666' );
    $kopa_theme_options_wdg_sidebar_color_code = get_option('kopa_theme_options_wdg_sidebar_color_code', '#666666');
    $kopa_theme_options_h1_color_code = get_option('kopa_theme_options_h1_color_code', '#666666');
    $kopa_theme_options_h2_color_code = get_option('kopa_theme_options_h2_color_code', '#666666');
    $kopa_theme_options_h3_color_code = get_option('kopa_theme_options_h3_color_code', '#666666');
    $kopa_theme_options_h4_color_code = get_option('kopa_theme_options_h4_color_code', '#666666');
    $kopa_theme_options_h5_color_code = get_option('kopa_theme_options_h5_color_code', '#666666');
    $kopa_theme_options_h6_color_code = get_option('kopa_theme_options_h6_color_code', '#666666');
    
    if ( $kopa_theme_options_body_text_color_code != '#666666' ) {
        $custom_styles .= "
        /* BODY TEXT COLOR */
        body,
        blockquote,
        .kp-search-widget .search-form .search-text,
        .kp-newsletter-widget .newsletter-form .email {
            color: {$kopa_theme_options_body_text_color_code};
        }
        ";
    }
    if ( $kopa_theme_options_wdg_sidebar_color_code != '#666666' ) {
        $custom_styles .= "
        /* WIDGET TITLE COLOR */
        #main-content .widget .widget-title,
        #bottom-sidebar .widget .widget-title {
            color: {$kopa_theme_options_wdg_sidebar_color_code};
        }";
    }
    if ( $kopa_theme_options_h1_color_code != '#666666' ) {
        $custom_styles .= "
        /* HEADING 1 COLOR */
        h1 {
            color: {$kopa_theme_options_h1_color_code};
        }";
    }
    if ( $kopa_theme_options_h2_color_code != '#666666' ) {
        $custom_styles .= "
        /* HEADING 2 COLOR */
        h2 {
            color: {$kopa_theme_options_h2_color_code};
        }";
    }
    if ( $kopa_theme_options_h3_color_code != '#666666' ) {
        $custom_styles .= "
        /* HEADING 3 COLOR */
        h3 {
            color: {$kopa_theme_options_h3_color_code};
        }";
    }
    if ( $kopa_theme_options_h4_color_code != '#666666' ) {
        $custom_styles .= "
        /* HEADING 4 COLOR */
        h4 {
            color: {$kopa_theme_options_h4_color_code};
        }";
    }
    if ( $kopa_theme_options_h5_color_code != '#666666' ) {
        $custom_styles .= "
        /* HEADING 5 COLOR */
        h5 {
            color: {$kopa_theme_options_h5_color_code};
        }";
    }
    if ( $kopa_theme_options_h6_color_code != '#666666' ) {
        $custom_styles .= "
        /* HEADING 6 COLOR */
        h6 {
            color: {$kopa_theme_options_h6_color_code};
        }";
    }

    /* =========================================================
      Font family
      ============================================================ */
    $google_fonts = kopa_get_google_font_array();
    $current_heading_font = get_option('kopa_theme_options_heading_font_family');
    $current_content_font = get_option('kopa_theme_options_content_font_family');
    $current_main_nav_font = get_option('kopa_theme_options_main_nav_font_family');
    $current_wdg_sidebar_font = get_option('kopa_theme_options_wdg_sidebar_font_family');
    $load_font_array = array();

    
    if ($current_heading_font) {
        $google_font_family = $google_fonts[$current_heading_font]['family'];
        $custom_styles .= "
        h1, h2, h3, h4, h5, h6,
        .kp-article-list-widget .entry-item .entry-title,
        .load-more,
        .add-to-cart-button,
        .widget_tag_cloud a,
        #bottom-menu li a,
        .kp-newsletter-widget .newsletter-form .submit,
        .sub-page ul.article-list li .entry-title,
        .pagination ul li a,
        .pagination ul li span,
        .breadcrumb,
        .page-links,
        .socials-link li,
        .tag-box,
        .entry-box footer p,
        .comments-list .comment .comment-body header .comment-button,
        #comments .pagination,
        #comments-form .required,
        #contact-form .required,
        #submit-comment,
        #submit-contact,
        .home-slider ul li .entry-content .entry-title,
        .home-slider ul li .entry-content p {
            font-family: '{$google_font_family}', sans-serif;
        }
        ";
    }
    if ($current_content_font) {
        $google_font_family = $google_fonts[$current_content_font]['family'];
        $custom_styles .= "
        body {
            font-family: '{$google_font_family}', sans-serif;
        }
        ";
    }
    if ($current_main_nav_font) {
        $google_font_family = $google_fonts[$current_main_nav_font]['family'];
        $custom_styles .= "
        #main-menu > li > a,
        #main-menu li ul li a {
            font-family: '{$google_font_family}', sans-serif;
        }
        ";
    }
    if ($current_wdg_sidebar_font) {
        $google_font_family = $google_fonts[$current_wdg_sidebar_font]['family'];
        $custom_styles .= "
        .widget .widget-title {
            font-family: '{$google_font_family}', sans-serif;
        }
        ";
    }


    /* =========================================================
      Font size
      ============================================================ */
    
    // heading 1 font-size
    $kopa_theme_options_h1_font_size = get_option('kopa_theme_options_h1_font_size');
    if ($kopa_theme_options_h1_font_size) {
        $custom_styles .= "
        h1 {
            font-size:{$kopa_theme_options_h1_font_size}px;
        }
        ";
    }

    // heading 2 font-size
    $kopa_theme_options_h2_font_size = get_option('kopa_theme_options_h2_font_size');
    if ($kopa_theme_options_h2_font_size) {
        $custom_styles .= "
        h2 {
            font-size:{$kopa_theme_options_h2_font_size}px;
        }
        ";
    }

    // heading 3 font-size
    $kopa_theme_options_h3_font_size = get_option('kopa_theme_options_h3_font_size');
    if ($kopa_theme_options_h3_font_size) {
        $custom_styles .= "
        h3 {
            font-size:{$kopa_theme_options_h3_font_size}px;
        }
        ";
    }

    // heading 4 font-size
    $kopa_theme_options_h4_font_size = get_option('kopa_theme_options_h4_font_size');
    if ($kopa_theme_options_h4_font_size) {
        $custom_styles .= "
        h4 {
            font-size:{$kopa_theme_options_h4_font_size}px;
        }
        ";
    }

    // heading 5 font-size
    $kopa_theme_options_h5_font_size = get_option('kopa_theme_options_h5_font_size');
    if ($kopa_theme_options_h5_font_size) {
        $custom_styles .= "
        h5 {
            font-size:{$kopa_theme_options_h5_font_size}px;
        }
        ";
    }

    // heading 6 font-size
    $kopa_theme_options_h6_font_size = get_option('kopa_theme_options_h6_font_size');
    if ($kopa_theme_options_h6_font_size) {
        $custom_styles .= "
        h6 {
            font-size:{$kopa_theme_options_h6_font_size}px;
        }
        ";
    }

    // body font-size
    $kopa_theme_options_content_font_size = get_option('kopa_theme_options_content_font_size');
    if ($kopa_theme_options_content_font_size) {
        $custom_styles .= "
        body {
           font-size:{$kopa_theme_options_content_font_size}px;
        }
        ";
    }

    // main menu font-size
    $kopa_theme_options_main_nav_font_size = get_option('kopa_theme_options_main_nav_font_size');
    if ($kopa_theme_options_main_nav_font_size) {
        $custom_styles .= "
        #main-menu > li > a,
        #main-menu li ul li a {
            font-size: {$kopa_theme_options_main_nav_font_size}px;
        }
        ";
    }
    
    // widget title font-size
    $kopa_theme_options_wdg_sidebar_font_size = get_option('kopa_theme_options_wdg_sidebar_font_size');
    if ($kopa_theme_options_wdg_sidebar_font_size) {
        $custom_styles .= "
        #main-content .widget .widget-title,
        #bottom-sidebar .widget .widget-title {
            font-size: {$kopa_theme_options_wdg_sidebar_font_size}px;
        }
        ";
    }
    
    /* ================================================================================
     * Font weight
      ================================================================================ */

    // heading 1 font-weight
    $kopa_theme_options_h1_font_weight = get_option('kopa_theme_options_h1_font_weight');
    if ($kopa_theme_options_h1_font_weight) {
        $custom_styles .= "
        h1 {
            font-weight: {$kopa_theme_options_h1_font_weight};
        }
        ";
    }

    // heading 2 font-weight
    $kopa_theme_options_h2_font_weight = get_option('kopa_theme_options_h2_font_weight');
    if ($kopa_theme_options_h2_font_weight) {
        $custom_styles .= "
        h2 {
            font-weight: {$kopa_theme_options_h2_font_weight};
        }
        ";
    }

    // heading 3 font-weight
    $kopa_theme_options_h3_font_weight = get_option('kopa_theme_options_h3_font_weight');
    if ($kopa_theme_options_h3_font_weight) {
        $custom_styles .= "
        h3 {
            font-weight: {$kopa_theme_options_h3_font_weight};
        }
        ";
    }

    // heading 4 font-weight
    $kopa_theme_options_h4_font_weight = get_option('kopa_theme_options_h4_font_weight');
    if ($kopa_theme_options_h4_font_weight) {
        $custom_styles .= "
        h4 {
            font-weight: {$kopa_theme_options_h4_font_weight};
        }
        ";
    }

    // heading 5 font-weight
    $kopa_theme_options_h5_font_weight = get_option('kopa_theme_options_h5_font_weight');
    if ($kopa_theme_options_h5_font_weight) {
        $custom_styles .= "
        h5 {
            font-weight: {$kopa_theme_options_h5_font_weight};
        }
        ";
    }

    // heading 6 font-weight
    $kopa_theme_options_h6_font_weight = get_option('kopa_theme_options_h6_font_weight');
    if ($kopa_theme_options_h6_font_weight) {
        $custom_styles .= "
        h6,
        .home-slider ul li .entry-content .entry-title {
            font-weight: {$kopa_theme_options_h6_font_weight};
        }
        ";
    }

    // content font-weight
    $kopa_theme_options_content_font_weight = get_option( 'kopa_theme_options_content_font_weight' );

    if ($kopa_theme_options_content_font_weight) {
        $custom_styles .= "
        body {
            font-weight: {$kopa_theme_options_content_font_weight};
        }
        ";
    }


    // main menu font-weight
    $kopa_theme_options_main_nav_font_weight = get_option('kopa_theme_options_main_nav_font_weight');
    if ($kopa_theme_options_main_nav_font_weight) {
        $custom_styles .= "
        #main-menu > li > a {
            font-weight: {$kopa_theme_options_main_nav_font_weight};
        }
        ";
    }

    // widget tilte font-weight
    $kopa_theme_options_wgd_sidebar_font_weight = get_option('kopa_theme_options_wgd_sidebar_font_weight');
    if ($kopa_theme_options_wgd_sidebar_font_weight) {
        $custom_styles .= "
        .widget .widget-title {
            font-weight: {$kopa_theme_options_wgd_sidebar_font_weight};
        }
        ";
    }

    /* ==================================================================================================
     * Theme Options custom styles
     * ================================================================================================= */
    echo '<style id="kopa-theme-options-custom-styles">'.$custom_styles.'</style>';

    /* ==================================================================================================
     * Custom CSS
     * ================================================================================================= */
    $kopa_theme_options_custom_css = htmlspecialchars_decode(stripslashes(get_option('kopa_theme_options_custom_css')));
    if ($kopa_theme_options_custom_css)
        echo "<style id='kopa-user-custom-css'>{$kopa_theme_options_custom_css}</style>";
}

/* ==============================================================================
 * Mobile Menu Walker Class
  ============================================================================= */

class kopa_mobile_menu extends Walker_Nav_Menu {

    function start_el(&$output, $item, $depth = 0, $args = array(), $current_object_id = 0) {
        global $wp_query;
        $indent = ( $depth ) ? str_repeat("\t", $depth) : '';

        $class_names = $value = '';

        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
        
        if ($depth == 0)
            $class_names = $class_names ? ' class="' . esc_attr($class_names) . ' clearfix"' : 'class="clearfix"';
        else 
            $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : 'class=""';

        $id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';

        $output .= $indent . '<li' . $id . $value . $class_names . '>';

        $attributes = !empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
        $attributes .=!empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
        $attributes .=!empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
        $attributes .=!empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';
        if ($depth == 0) {
            $item_output = $args->before;
            $item_output .= '<h3><a' . $attributes . '>';
            $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
            $item_output .= '</a></h3>';
            $item_output .= $args->after;
        } else {
            $item_output = $args->before;
            $item_output .= '<a' . $attributes . '>';
            $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
            $item_output .= '</a>';
            $item_output .= $args->after;
        }
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

    function start_lvl(&$output, $depth = 0, $args = array()) {
        $indent = str_repeat("\t", $depth);
        if ($depth == 0) {
            $output .= "\n$indent<span>+</span><div class='clearfix'></div><div class='menu-panel clearfix'><ul>";
        } else {
            $output .= '<ul>'; // indent for level 2, 3 ...
        } 
    }

    function end_lvl(&$output, $depth = 0, $args = array()) {
        $indent = str_repeat("\t", $depth);
        if ($depth == 0) {
            $output .= "$indent</ul></div>\n";
        } else {
            $output .= '</ul>';
        } 
    }

}

function kopa_add_icon_home_menu($items, $args) {

    if ( 'enable' != get_option( 'kopa_theme_options_home_menu_item_status', 'enable' ) ) {
        return $items;
    }

    if ($args->theme_location == 'main-nav') {
        if ($args->menu_id == 'kp-main-menu') {
            $homelink = '<li><a class="fa fa-home kp-icon-home" href="' . home_url() . '"></a></li>';
            $items = $homelink . $items;
        }
    }

    return $items;
}

function kopa_custom_excerpt_length( $length ) {
    if ( is_home() || is_archive() || is_search() ) {
        return get_option( 'kopa_theme_options_blog_excerpt_length', 10 );
    }

    return get_option( 'kopa_theme_options_frontpage_excerpt_length', 10 );
}

function kopa_custom_excerpt_more( $more ) {
    return '';
}


/**
 * Convert Hex Color to RGB using PHP
 * @link http://bavotasan.com/2011/convert-hex-color-to-rgb-using-php/
 */
function kopa_hex2rgba($hex, $alpha = false) {
    $hex = str_replace("#", "", $hex);

    if(strlen($hex) == 3) {
        $r = hexdec(substr($hex,0,1).substr($hex,0,1));
        $g = hexdec(substr($hex,1,1).substr($hex,1,1));
        $b = hexdec(substr($hex,2,1).substr($hex,2,1));
    } else {
        $r = hexdec(substr($hex,0,2));
        $g = hexdec(substr($hex,2,2));
        $b = hexdec(substr($hex,4,2));
    }
    if ( $alpha ) {
        return array($r, $g, $b, $alpha);
    }
    
    return array($r, $g, $b);
}

/**
 * Get gallery string ids after getting matched gallery array
 * @return array of attachment ids in gallery
 * @return empty if no gallery were found
 */
function kopa_content_get_gallery_attachment_ids( $content ) {
    $gallery = kopa_content_get_gallery( $content );

    if (isset( $gallery[0] )) {
        $gallery = $gallery[0];
    } else {
        return '';
    } 

    if ( isset($gallery['shortcode']) ) {
        $shortcode = $gallery['shortcode'];
    } else {
        return '';
    } 

    // get gallery string ids
    preg_match_all('/ids=\"(?:\d+,*)+\"/', $shortcode, $gallery_string_ids);
    if ( isset( $gallery_string_ids[0][0] ) ) {
        $gallery_string_ids = $gallery_string_ids[0][0];
    } else {
        return '';
    } 

    // get array of image id
    preg_match_all('/\d+/', $gallery_string_ids, $gallery_ids);
    if ( isset( $gallery_ids[0] ) ) {
        $gallery_ids = $gallery_ids[0];
    } else {
        return '';
    } 

    return $gallery_ids;
}

/**
 * Get weather data of location automatically by ip
 * @since Gammar 1.0
 */
function kopa_weather_widget() {
    $kopa_weather_data_get_method = get_option( 'kopa_theme_options_weather_data', 'automatic' );
    $kopa_weather_override_title = get_option( 'kopa_theme_options_weather_override_title' );

    $units = 'metric';

    $locale = 'en';

    $sytem_locale = get_locale();
    $available_locales = array( 'en', 'sp', 'fr', 'it', 'de', 'pt', 'ro', 'pl', 'ru', 'ua', 'fi', 'nl', 'bg', 'se', 'tr', 'zh_tw', 'zh_cn' ); 

    
    // CHECK FOR LOCALE
    if( in_array( $sytem_locale , $available_locales ) )
    {
        $locale = $sytem_locale;
    }
    
    // CHECK FOR LOCALE BY FIRST TWO DIGITS
    if( in_array(substr($sytem_locale, 0, 2), $available_locales ) )
    {
        $locale = substr($sytem_locale, 0, 2);
    }

    // check whether get weather data automatically or custom by admin
    if ( 'automatic' == $kopa_weather_data_get_method ) {
        /**
         * GET CITY BY NAME AUTOMATICALLY BY IP
         */
        $geourl = "http://www.geoplugin.net/json.gp?ip=" . $_SERVER['REMOTE_ADDR'];

        $result = wp_remote_get( $geourl );
        if ( ! is_wp_error( $result ) && isset( $result['body'] ) ) {
            $result = json_decode( $result['body'] );
            
            if ( ! empty( $result->geoplugin_city ) && ! empty ( $result->geoplugin_countryName ) ) {
                $location = $result->geoplugin_city . ', ' . $result->geoplugin_countryName;
            } elseif ( ! empty( $result->geoplugin_city ) && empty ( $result->geoplugin_countryName ) ) {
                $location = $result->geoplugin_city;
            } elseif ( empty( $result->geoplugin_city ) && ! empty ( $result->geoplugin_countryName ) ) {
                $location = $result->geoplugin_countryName;
            }
        }
    } else {
        $location = get_option( 'kopa_theme_options_weather_location', '' );
    }

    // NO LOCATION, ABORT ABORT!!!1!
    if( !isset($location) || !$location ) { return ''; }

    //FIND AND CACHE CITY ID
    $city_name_slug                 = sanitize_title( $location );
    $weather_transient_name         = 'kopa-awesome-weather-' . $units . '-' . $city_name_slug . "-". $locale;

    // GET WEATHER DATA
    if( get_transient( $weather_transient_name ) )
    {
        $weather_data = get_transient( $weather_transient_name );
    }
    else
    {
        // NOW
        $now_ping = "http://api.openweathermap.org/data/2.5/weather?q=" . $city_name_slug . "&lang=" . $locale . "&units=" . $units;
        $now_ping_get = wp_remote_get( $now_ping );
    
        if( is_wp_error( $now_ping_get ) ) 
        {
            return '';
        }   
    
        $city_data = json_decode( $now_ping_get['body'] );
        
        if( isset($city_data->cod) AND $city_data->cod == 404 )
        {
            return '';
        }
        else
        {
            $weather_data = $city_data;
        }
        
        if( $weather_data )
        {
            // SET THE TRANSIENT, CACHE FOR AN HOUR
            set_transient( $weather_transient_name, $weather_data, apply_filters( 'kopa_auto_awesome_weather_cache', 3600 ) ); 
        }
    }

    // NO WEATHER
    if( !$weather_data ) { 
        return '';
    }

    // if location custom by admin, get the return name of openweather api
    if ( 'custom' == $kopa_weather_data_get_method ) {
        if ( ! empty( $kopa_weather_override_title ) ) {
            $location = $kopa_weather_override_title;
        } else {
            $location = $weather_data->name . ', ' . $weather_data->sys->country;
        }
    }

    ?>
    <div class="widget-area-1">
        <div class="widget clearfix widget_awesomeweatherwidget">
            <h6 class="widget-title"><?php _e( 'Weather Forecast', kopa_get_domain() ); ?></h6>
            <div class="awesome-weather-wrap awecf temp8 awe_without_stats awe_wide">
                <div class="awesome-weather-header"><?php echo $location; ?></div>
                <div class="awesome-weather-current-temp"><?php echo round( $weather_data->main->temp ); ?><sup>C</sup>
                </div> <!-- /.awesome-weather-current-temp -->
            </div> <!-- /.awesome-weather-wrap -->
        </div>
    </div>
    <?php
}

/**
 * Template tag: print header ticker
 * @since FastNews 1.0
 */
function kopa_header_ticker() {
    // get option
    $kopa_theme_options_display_headline_status = get_option( 'kopa_theme_options_display_headline_status', 'show' );
    $kopa_theme_options_headline_category_id = get_option( 'kopa_theme_options_headline_category_id', null );
    $kopa_theme_options_headline_posts_number = (int) get_option( 'kopa_theme_options_headline_posts_number', 10 );

    // validate
    if ( $kopa_theme_options_headline_posts_number <= 1 ) {
        $kopa_theme_options_headline_posts_number = 10;
    }

    if ( 'show' == $kopa_theme_options_display_headline_status ) {
        $args = array(
            'category__in'        => $kopa_theme_options_headline_category_id,
            'posts_per_page'      => $kopa_theme_options_headline_posts_number,
            'ignore_sticky_posts' => true,
        );

        $ticker_posts = new WP_Query( $args );
    ?>

        <?php if ( $ticker_posts->have_posts() ) { 
            $post_index = 1;
            ?>

        <div class="main-top">
            <div class="owl-carousel owl-carousel-text"> 

            <?php while ( $ticker_posts->have_posts() ) {
                $ticker_posts->the_post(); ?>
            
            <div class="item clearfix">
                <span class="pull-left"><?php echo $post_index; ?></span>
                <a href="<?php the_permalink(); ?>" class="item-right"><?php the_title(); ?></a>
            </div>    
            
            <?php $post_index++; // increment post index by 1
            } // end while ?>
            
            </div>
        </div>

        <?php wp_reset_postdata(); ?>

        <?php } // endif ?>

    <?php }
}

/**
 * Template tag: print socials link
 * @since Ultra Mag 1.0
 */
function kopa_social_links() {
    $social_links = array(
        'facebook'  => array(
            'url'     => '',
            'icon'    => 'fa fa-facebook',
            'display' => false,
        ),
        'twitter'   => array(
            'url'    => '',
            'icon'   => 'fa fa-twitter',
            'display' => false,
        ),
        'gplus'     => array(
            'url'    => '',
            'icon'   => 'fa fa-google-plus',
            'display' => false,
        ),
        'dribbble'  => array(
            'url'     => '',
            'icon'    => 'fa fa-dribbble',
            'display' => false,
        ),
        'linkedin'     => array(
            'url'    => '',
            'icon'   => 'fa fa-linkedin',
            'display' => false,
        ),
        'rss'  => array(
            'url'    => '',
            'icon'   => 'fa fa-rss',
            'display' => false,
        ),
    );

    foreach( $social_links as $social_name => $social_atts ) {
        $option_name = 'kopa_theme_options_social_links_' . $social_name . '_url';
        $social_atts['url'] = get_option( $option_name, '' );

        if ( 'rss' == $social_name ) {
            if ( empty( $social_atts['url'] ) ) {
                $social_atts['url'] = get_bloginfo('rss2_url');
                $social_atts['display'] = true;
            } elseif ( $social_atts['url'] != 'HIDE' ) {
                $social_atts['url'] = esc_url( $social_atts['url'] );
                $social_atts['display'] = true;
            }
        } else {
            $social_atts['url'] = esc_url( $social_atts['url'] );
            if ( !empty( $social_atts['url'] ) ) { $social_atts['display'] = true; }
        }

        $social_links[ $social_name ] = $social_atts;
    }

    $social_link_target = get_option( 'kopa_theme_options_social_link_target', '_self' );
    ?>

    <?php foreach ( $social_links as $social_name => $social_atts) { ?>
        <?php if ( $social_atts['display'] ) { ?>
        <li><a href="<?php echo $social_atts['url']; ?>" class="<?php echo $social_atts['icon']; ?>" target="<?php echo $social_link_target; ?>"></a></li>
        <?php } // endif ?>
    <?php } // endforeach ?>

    <?php
}

/**
 * Color darken or lighten function
 * @author clearpixel
 * @link http://lab.clearpixel.com.au/2008/06/darken-or-lighten-colours-dynamically-using-php/
 * @since FastNews 1.0
 */
function kopa_color_brightness($hex, $percent) {
    // Work out if hash given
    $hash = '';
    if (stristr($hex,'#')) {
        $hex = str_replace('#','',$hex);
        $hash = '#';
    }
    /// HEX TO RGB
    $rgb = array(hexdec(substr($hex,0,2)), hexdec(substr($hex,2,2)), hexdec(substr($hex,4,2)));
    //// CALCULATE 
    for ($i=0; $i<3; $i++) {
        // See if brighter or darker
        if ($percent > 0) {
            // Lighter
            $rgb[$i] = round($rgb[$i] * $percent) + round(255 * (1-$percent));
        } else {
            // Darker
            $positivePercent = $percent - ($percent*2);
            $rgb[$i] = round($rgb[$i] * $positivePercent) + round(0 * (1-$positivePercent));
        }
        // In case rounding up causes us to go to 256
        if ($rgb[$i] > 255) {
            $rgb[$i] = 255;
        }
    }
    //// RBG to Hex
    $hex = '';
    for($i=0; $i < 3; $i++) {
        // Convert the decimal digit to hex
        $hexDigit = dechex($rgb[$i]);
        // Add a leading zero if necessary
        if(strlen($hexDigit) == 1) {
        $hexDigit = "0" . $hexDigit;
        }
        // Append to the hex string
        $hex .= $hexDigit;
    }
    return $hash.$hex;
}

