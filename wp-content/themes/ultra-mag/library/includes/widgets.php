<?php
/**
 * Widget Registration
 * @package Ultra Mag
 */

add_action('widgets_init', 'kopa_widgets_init');

function kopa_widgets_init() {
    register_widget('Kopa_Widget_Text');
    register_widget('Kopa_Widget_Articles_List');
    register_widget('Kopa_Widget_Featured_Articles_Slider');
    register_widget('Kopa_Widget_Mailchimp_Subscribe');
    register_widget('Kopa_Widget_Feedburner_Subscribe');
    register_widget('Kopa_Widget_Combo');
    register_widget('Kopa_Widget_Entries_List');
    register_widget('Kopa_Widget_Social_Links');
    register_widget('Kopa_Widget_Advertising');
}

add_action('admin_enqueue_scripts', 'kopa_widget_admin_enqueue_scripts');

function kopa_widget_admin_enqueue_scripts($hook) {
    if ('widgets.php' === $hook) {
        $dir = get_template_directory_uri() . '/library';
        wp_enqueue_style('kopa_widget_admin', "{$dir}/css/widget.css");
        wp_enqueue_script('kopa_widget_admin', "{$dir}/js/widget.js", array('jquery'));
    }
}

function kopa_widget_article_build_query($query_args = array()) {
    $args = array(
        'post_type' => array('post'),
        'posts_per_page' => $query_args['number_of_article']
    );

    $tax_query = array();

    if ($query_args['categories']) {
        $tax_query[] = array(
            'taxonomy' => 'category',
            'field' => 'id',
            'terms' => $query_args['categories']
        );
    }
    if ($query_args['tags']) {
        $tax_query[] = array(
            'taxonomy' => 'post_tag',
            'field' => 'id',
            'terms' => $query_args['tags']
        );
    }
    if ($query_args['relation'] && count($tax_query) == 2) {
        $tax_query['relation'] = $query_args['relation'];
    }

    if ($tax_query) {
        $args['tax_query'] = $tax_query;
    }

    switch ($query_args['orderby']) {
        case 'popular':
            $args['meta_key'] = 'kopa_' . kopa_get_domain() . '_total_view';
            $args['orderby'] = 'meta_value_num';
            break;
        case 'most_comment':
            $args['orderby'] = 'comment_count';
            break;
        case 'random':
            $args['orderby'] = 'rand';
            break;
        default:
            $args['orderby'] = 'date';
            break;
    }
    if (isset($query_args['post__not_in']) && $query_args['post__not_in']) {
        $args['post__not_in'] = $query_args['post__not_in'];
    }
    return new WP_Query($args);
}

function kopa_widget_posttype_build_query( $query_args = array() ) {
    $default_query_args = array(
        'post_type'      => 'post',
        'posts_per_page' => -1,
        'post__not_in'   => array(),
        'ignore_sticky_posts' => 1,
        'categories'     => array(),
        'tags'           => array(),
        'relation'       => 'OR',
        'orderby'        => 'latest',
        'cat_name'       => 'category',
        'tag_name'       => 'post_tag'
    );

    $query_args = wp_parse_args( $query_args, $default_query_args );

    $args = array(
        'post_type'           => $query_args['post_type'],
        'posts_per_page'      => $query_args['posts_per_page'],
        'post__not_in'        => $query_args['post__not_in'],
        'ignore_sticky_posts' => $query_args['ignore_sticky_posts']
    );

    $tax_query = array();

    if ( $query_args['categories'] ) {
        $tax_query[] = array(
            'taxonomy' => $query_args['cat_name'],
            'field'    => 'id',
            'terms'    => $query_args['categories']
        );
    }
    if ( $query_args['tags'] ) {
        $tax_query[] = array(
            'taxonomy' => $query_args['tag_name'],
            'field'    => 'id',
            'terms'    => $query_args['tags']
        );
    }
    if ( $query_args['relation'] && count( $tax_query ) == 2 ) {
        $tax_query['relation'] = $query_args['relation'];
    }

    if ( $tax_query ) {
        $args['tax_query'] = $tax_query;
    }

    switch ( $query_args['orderby'] ) {
    case 'popular':
        $args['meta_key'] = 'kopa_' . kopa_get_domain() . '_total_view';
        $args['orderby'] = 'meta_value_num';
        break;
    case 'most_comment':
        $args['orderby'] = 'comment_count';
        break;
    case 'random':
        $args['orderby'] = 'rand';
        break;
    default:
        $args['orderby'] = 'date';
        break;
    }

    return new WP_Query( $args );
}

class Kopa_Widget_Text extends WP_Widget {

    function __construct() {
        $widget_ops = array('classname' => 'kopa_widget_text widget_text', 'description' => __('Arbitrary text, HTML or shortcodes', kopa_get_domain()));
        $control_ops = array('width' => 600, 'height' => 400);
        parent::__construct('kopa_widget_text', __('Kopa Text', kopa_get_domain()), $widget_ops, $control_ops);
    }

    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        $text = apply_filters('widget_text', empty($instance['text']) ? '' : $instance['text'], $instance);

        echo $before_widget;

        if ( !empty( $title ) ) {
            echo $before_title . $title . $after_title;
        }
        ?>
        <div class="widget-content">
        <?php echo !empty($instance['filter']) ? wpautop($text) : $text; ?>
        </div>
        <?php
        echo $after_widget;
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['title-icon'] = $new_instance['title-icon'];
        if (current_user_can('unfiltered_html')) {
            $instance['text'] = $new_instance['text'];
        } else {
            $instance['text'] = stripslashes(wp_filter_post_kses(addslashes($new_instance['text'])));
        }
        $instance['filter'] = isset($new_instance['filter']);

        return $instance;
    }

    function form($instance) {
        $instance = wp_parse_args((array) $instance, array(
            'title'      => '', 
            'text'       => ''));
        $title = strip_tags($instance['title']);
        $text = esc_textarea($instance['text']);
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', kopa_get_domain()); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>  
        <ul class="kopa_shortcode_icons">
            <?php
            $shortcodes = array(
                'one_half'           => __( 'One Half Column', kopa_get_domain() ),
                'one_third'          => __( 'One Thirtd Column', kopa_get_domain() ),
                'two_third'          => __( 'Two Third Column', kopa_get_domain() ),
                'one_fourth'         => __( 'One Fourth Column', kopa_get_domain() ),
                'three_fourth'       => __( 'Three Fourth Column', kopa_get_domain() ),
                'dropcaps'           => __( 'Add Dropcaps Text', kopa_get_domain() ),
                'button'             => __( 'Add A Button', kopa_get_domain() ),
                'alert'              => __( 'Add A Alert Box', kopa_get_domain() ),
                'tabs'               => __( 'Add A Tabs Content', kopa_get_domain() ),
                'accordions'         => __( 'Add A Accordions Content', kopa_get_domain() ),
                'toggle'             => __( 'Add A Toggle Content', kopa_get_domain() ),
                'contact_form'       => __( 'Add A Contact Form', kopa_get_domain() ),
                // 'posts_lastest'      => __( 'Add A List Latest Post', kopa_get_domain() ),
                // 'posts_popular'      => __( 'Add A List Popular Post', kopa_get_domain() ),
                // 'posts_most_comment' => __( 'Add A List Most Comment Post', kopa_get_domain() ),
                // 'posts_random'       => __( 'Add A List Random Post', kopa_get_domain() ),
                'youtube'            => __( 'Add A Yoube Video Box', kopa_get_domain() ),
                'vimeo'              => __( 'Add A Vimeo Video Box', kopa_get_domain() ),
            );
            foreach ($shortcodes as $rel => $title):
                ?>
                <li>
                    <a onclick="return kopa_shortcode_icon_click('<?php echo $rel; ?>', jQuery('#<?php echo $this->get_field_id('text'); ?>'));" href="#" class="<?php echo "kopa-icon-{$rel}"; ?>" rel="<?php echo $rel; ?>" title="<?php echo $title; ?>"></a>
                </li>
            <?php endforeach; ?>
        </ul>        
        <textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>
        <p>
            <input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox" <?php checked(isset($instance['filter']) ? $instance['filter'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e('Automatically add paragraphs', kopa_get_domain()); ?></label>
        </p>
        <?php
    }

}

/**
 * Articles List Widget Class
 * @since Ultra Mag 1.0
 */
class Kopa_Widget_Articles_List extends WP_Widget {
    private $display_date_meta = true;
    private $display_readmore = true;
    private $display_author_meta = true;

    function __construct() {
        $widget_ops = array('classname' => 'kp-article-list-widget', 'description' => __('Display Articles Widget', kopa_get_domain()));
        $control_ops = array('width' => '500', 'height' => 'auto');
        parent::__construct('kopa_widget_articles_list', __('Kopa Articles List', kopa_get_domain()), $widget_ops, $control_ops);
    }

    /**
     * Display list with ajax load more
     */
    function display_list_with_ajax_load_more( $query_args, $ajax_more_posts = 3, $ajax_btn_text = 'LOAD MORE' ) {

        // query posts
        $posts = kopa_widget_posttype_build_query( $query_args );
        
        /**
         * Prepare datas for ajax load more 
         */
        $datas_ajax_load_more = 'data-action="kopa_article_list_ajax_load_more" ';
        $datas_ajax_load_more .= 'data-nonce="'.wp_create_nonce( 'kopa_article_list_ajax_load_more' ).'" '; 

        // datas for show/hide meta datas of ajax datas
        $datas_ajax_load_more .= 'data-display-date-meta="'.$this->display_date_meta.'" ';
        $datas_ajax_load_more .= 'data-display-readmore="'.$this->display_readmore.'" ';
        $datas_ajax_load_more .= 'data-display-author-meta="'.$this->display_author_meta.'" ';
        
        // contain all queried post ids
        $data_queried_posts = array();
        
        if ( isset( $query_args['categories'] ) ) {
            $data_categories = implode(',', $query_args['categories']);
            $datas_ajax_load_more .= 'data-categories="'.$data_categories.'" ';
        }
        if ( isset( $query_args['tags'] ) ) {
            $data_tags = implode(',', $query_args['tags']);
            $datas_ajax_load_more .= 'data-tags="'.$data_tags.'" ';
        }
        if ( isset( $query_args['relation'] ) ) {
            $datas_ajax_load_more .= 'data-relation="'.$query_args['relation'].'" ';
        }
        if ( isset( $query_args['posts_per_page'] ) ) {
            $data_offset = $query_args['posts_per_page'];
            $datas_ajax_load_more .= 'data-offset="'.$data_offset.'" ';
        }
        if ( isset( $query_args['orderby'] ) ) {
            $datas_ajax_load_more .= 'data-orderby="'.$query_args['orderby'].'" ';
        }

        // number of posts will be loaded when click to load more button
        $datas_ajax_load_more .= 'data-more-posts="'.$ajax_more_posts.'" ';

        // id of list, use to append after loading posts completely
        $datas_ajax_load_more .= 'data-list-id="#'.$this->get_field_id('list').'" ';

        // light box album id
        $datas_ajax_load_more .= 'data-light-box-id="'.$this->get_field_id('gallery').'" ';
        /**
         * End - prepare datas for ajax load more 
         */

        if ( $posts->have_posts() ) { ?>

            <ul id="<?php echo $this->get_field_id( 'list' ); ?>" class="clearfix">
        
            <?php while ( $posts->have_posts() ) { 
                $posts->the_post();

                if ( 'audio' == get_post_format() || 'video' == get_post_format() ) {
                    $post_format_class = get_post_format() . '-post';
                } else {
                    $post_format_class = 'standard-post';
                }

                array_push( $data_queried_posts, get_the_ID() );
                ?>

            <li>
                <article class="entry-item <?php echo $post_format_class; ?>">
                    <?php if ( has_post_thumbnail() ) { 
                        $full_featured_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
                        ?>
                    <div class="entry-thumb hover-effect">
                        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('kopa-article-list-size'); ?></a>

                        <?php if ( get_post_format() !== 'video' ) { ?>
                            <a href="<?php echo esc_url( $full_featured_image[0] ); ?>" class="hover-icon" rel="prettyPhoto[<?php echo $this->get_field_id( 'gallery' ); ?>]"></a>
                        <?php } else {
                            $video = kopa_content_get_video( get_the_content() );
                            $is_displayed_video = false;
                            if ( isset( $video[0] ) && $video[0] ) {
                                $video = $video[0];

                                if ( isset( $video['url'] ) && $video['url'] ) { ?>
                                    <a href="<?php echo esc_url( $video['url'] ); ?>" class="hover-icon" rel="prettyPhoto[<?php echo $this->get_field_id( 'gallery' ); ?>]"></a>
                        <?php
                                    $is_displayed_video = true;
                                } // endif
                            } // endif 

                            if ( ! $is_displayed_video ) { ?>
                                <a href="<?php echo esc_url( $full_featured_image[0] ); ?>" class="hover-icon" rel="prettyPhoto[<?php echo $this->get_field_id( 'gallery' ); ?>]"></a>
                        <?php 
                            } // endif is_displayed_video
                        } // endif ?>
                    </div>
                    <?php } // endif has_post_thumbnail ?>
                    <div class="entry-content">
                        <?php if ( $this->display_date_meta ) { ?>
                            <header><span class="entry-date clearfix"><?php echo KopaIcon::getIcon('fa fa-calendar-o entry-icon', 'span'); ?><span><?php the_time( get_option( 'date_format' ) ); ?>, <?php the_time( get_option( 'time_format' ) ); ?></span></span></header>
                        <?php } ?>

                        <h6 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h6>
                        <?php the_excerpt(); ?>

                        <?php if ( $this->display_readmore ) { ?>
                            <a href="<?php the_permalink(); ?>" class="more-link"><?php _e( 'Read more...', kopa_get_domain() ); ?></a>
                        <?php } ?>
                    </div>
                </article>
            </li>

            <?php } // endwhile 

            // get queried post ids data
            $data_posts_not_in = implode( ',', $data_queried_posts );
            $datas_ajax_load_more .= 'data-posts-not-in="'.$data_posts_not_in.'"';

            ?>

            </ul>

            <?php if ( $ajax_more_posts && $ajax_btn_text ) { ?>
            <div class="text-center"><a <?php echo $datas_ajax_load_more; ?> href="#" class="load-more"><?php echo $ajax_btn_text; ?></a></div>
            <?php } // endif ?>

        <?php } // endif 

        wp_reset_postdata();
    }

    /**
     * Display list with first featured article
     */
    function display_first_featured_list( $query_args ) {
        // query posts
        $posts = kopa_widget_posttype_build_query( $query_args );

        if ( $posts->have_posts() ) {
            $post_index = 1;
            while ( $posts->have_posts() ) {
                $posts->the_post(); 

                if ( 1 === $post_index ) { 
                    if ( 'audio' == get_post_format() || 'video' == get_post_format() ) {
                        $post_format_class = get_post_format() . '-post';
                    } else {
                        $post_format_class = 'standard-post';
                    }
                ?>

                <article class="featured-item entry-item <?php echo $post_format_class; ?>">
                    <?php if ( has_post_thumbnail() ) { 
                        $full_featured_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
                        ?>
                    <div class="entry-thumb hover-effect">
                        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('kopa-article-list-size'); ?></a>

                        <?php if ( get_post_format() !== 'video' ) { ?>
                            <a href="<?php echo esc_url( $full_featured_image[0] ); ?>" class="hover-icon" rel="prettyPhoto[<?php echo $this->get_field_id( 'gallery' ); ?>]"></a>
                        <?php } else {
                            $video = kopa_content_get_video( get_the_content() );
                            $is_displayed_video = false;
                            if ( isset( $video[0] ) && $video[0] ) {
                                $video = $video[0];

                                if ( isset( $video['url'] ) && $video['url'] ) { ?>
                                    <a href="<?php echo esc_url( $video['url'] ); ?>" class="hover-icon" rel="prettyPhoto[<?php echo $this->get_field_id( 'gallery' ); ?>]"></a>
                        <?php
                                    $is_displayed_video = true;
                                } // endif
                            } // endif 

                            if ( ! $is_displayed_video ) { ?>
                                <a href="<?php echo esc_url( $full_featured_image[0] ); ?>" class="hover-icon" rel="prettyPhoto[<?php echo $this->get_field_id( 'gallery' ); ?>]"></a>
                        <?php 
                            } // endif is_displayed_video
                        } // endif ?>
                    </div>
                    <?php } ?>
                    <div class="entry-content">
                        <?php if ( $this->display_date_meta ) { ?>
                        <header>
                            <span class="entry-date clearfix"><?php echo KopaIcon::getIcon('fa fa-calendar-o entry-icon', 'span'); ?><span><?php the_time( get_option( 'date_format' ) ); ?>, <?php the_time( get_option( 'time_format' ) ); ?></span></span>
                        </header>
                        <?php } ?>

                        <h6 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h6>
                        <?php the_excerpt(); ?>
                        
                        <?php if ( $this->display_readmore ) { ?>
                        <a href="<?php the_permalink(); ?>" class="more-link"><?php _e( 'Read more...', kopa_get_domain() ); ?></a>
                        <?php } ?>
                    </div>
                </article>

                <?php } else { 
                    if ( 2 === $post_index ) { ?>
                    <ul class="older-post clearfix">
                    <?php } ?>

                        <li>
                            <article class="entry-item audio-post clearfix">
                                <?php if ( has_post_thumbnail() ) { ?>
                                <div class="entry-thumb hover-effect">
                                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'thumbnail' ); ?></a>
                                    <a href="<?php the_permalink(); ?>" class="hover-icon"></a>
                                </div>
                                <?php } ?>
                                <div class="entry-content">
                                    <?php if ( $this->display_date_meta ) { ?>
                                    <header>
                                        <span class="entry-date clearfix"><?php echo KopaIcon::getIcon('fa fa-calendar-o entry-icon', 'span'); ?><span><?php the_time( get_option( 'date_format' ) ); ?></span></span>
                                    </header>
                                    <?php } ?>

                                    <h6 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h6>
                                    <?php the_excerpt(); ?>

                                    <?php if ( $this->display_readmore ) { ?>
                                    <a href="<?php the_permalink(); ?>" class="more-link"><?php _e( 'Read more...', kopa_get_domain() ); ?></a>
                                    <?php } ?>
                                </div>
                            </article>
                        </li>

                    <?php if ( $post_index === $posts->post_count ) { ?>
                    </ul>
                    <?php } 
                } // endif 

                $post_index++;
            } // endwhile

            echo '<div class="clear"></div>';
        } // endif
    }

    /**
     * Display normal list with medium featured images
     */
    function display_medium_normal_list( $query_args ) {
        // query posts
        $posts = kopa_widget_posttype_build_query( $query_args );

        if ( $posts->have_posts() ) { ?>
        <ul class="clearfix">
            <?php while ( $posts->have_posts() ) {
                $posts->the_post();

                if ( 'audio' == get_post_format() || 'video' == get_post_format() ) {
                    $post_format_class = get_post_format() . '-post';
                } else {
                    $post_format_class = 'standard-post';
                }
                ?>
            <li>
                <article class="entry-item <?php echo $post_format_class; ?>">
                    <header>
                        <?php if ( $this->display_date_meta ) { ?>
                        <span class="entry-date clearfix"><?php echo KopaIcon::getIcon('fa fa-calendar-o entry-icon', 'span'); ?><span><?php the_time( get_option( 'date_format' ) ) ?>, <?php the_time( get_option( 'time_format' ) ); ?></span></span>
                        <?php } ?>

                        <h6 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h6>
                        
                        <?php if ( $this->display_author_meta ) { ?>
                        <span class="entry-author"><?php _e( 'By:', kopa_get_domain() ); ?> <?php the_author_posts_link(); ?></span>
                        <?php } ?>
                    </header>
                    <?php if ( has_post_thumbnail() ) { 
                        $full_featured_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
                        ?>
                    <div class="entry-thumb hover-effect">
                        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium' ); ?></a>
                        
                        <?php if ( get_post_format() !== 'video' ) { ?>
                            <a href="<?php echo esc_url( $full_featured_image[0] ); ?>" class="hover-icon" rel="prettyPhoto[<?php echo $this->get_field_id( 'gallery' ); ?>]"></a>
                        <?php } else {
                            $video = kopa_content_get_video( get_the_content() );
                            $is_displayed_video = false;
                            if ( isset( $video[0] ) && $video[0] ) {
                                $video = $video[0];

                                if ( isset( $video['url'] ) && $video['url'] ) { ?>
                                    <a href="<?php echo esc_url( $video['url'] ); ?>" class="hover-icon" rel="prettyPhoto[<?php echo $this->get_field_id( 'gallery' ); ?>]"></a>
                        <?php
                                    $is_displayed_video = true;
                                } // endif
                            } // endif 

                            if ( ! $is_displayed_video ) { ?>
                                <a href="<?php echo esc_url( $full_featured_image[0] ); ?>" class="hover-icon" rel="prettyPhoto[<?php echo $this->get_field_id( 'gallery' ); ?>]"></a>
                        <?php 
                            } // endif is_displayed_video
                        } // endif ?>
                    </div>
                    <?php } ?>
                    <div class="entry-content">
                        <?php the_excerpt(); ?>

                        <?php if ( $this->display_readmore ) { ?>
                        <a href="<?php the_permalink(); ?>" class="more-link"><?php _e( 'Read more...', kopa_get_domain() ); ?></a>
                        <?php } ?>
                    </div>
                </article>
            </li>
            <?php } // endwhile ?>
        </ul>
        <?php } // endif
    }

    function widget( $args, $instance ) {
        extract( $args );
        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
        $query_args['categories'] = $instance['categories'];
        $query_args['relation'] = $instance['relation'];
        $query_args['tags'] = $instance['tags'];
        $query_args['posts_per_page'] = $instance['number_of_article'];
        $query_args['orderby'] = $instance['orderby'];

        /* get show/hide meta datas */
        $this->display_date_meta = isset( $instance['display_date_meta'] ) ? $instance['display_date_meta'] : false;
        $this->display_readmore = isset( $instance['display_readmore'] ) ? $instance['display_readmore'] : false;
        $this->display_author_meta = isset( $instance['display_author_meta'] ) ? $instance['display_author_meta'] : false;
        /* end get show/hide meta datas */

        echo $before_widget;

        if ( ! empty( $title ) ) {
            echo $before_title . $title . $after_title;
        }

        // display articles list
        if ( 'normal' === $instance['display_type'] ) {
            $this->display_list_with_ajax_load_more( $query_args, $instance['ajax_more_posts'], $instance['ajax_btn_text'] );
        } elseif ( 'first_featured' === $instance['display_type'] ) {
            $this->display_first_featured_list( $query_args );
        } elseif ( 'medium_normal_list' === $instance['display_type'] ) {
            $this->display_medium_normal_list( $query_args );
        } else {
            $this->display_list_with_ajax_load_more( $query_args, 0, '' );
        }

        echo $after_widget;
    }

    function form($instance) {
        $defaults = array(
            'title'               => '',
            'categories'          => array(),
            'relation'            => 'OR',
            'tags'                => array(),
            'number_of_article'   => 6,
            'orderby'             => 'latest',
            'display_type'        => 'normal',
            'ajax_btn_text'       => __( 'LOAD MORE', kopa_get_domain() ),
            'ajax_more_posts'     => 3,
            'display_date_meta'   => true,
            'display_readmore'    => true,
            'display_author_meta' => true,
        );
        $instance = wp_parse_args( (array) $instance, $defaults );
        $title = strip_tags( $instance['title'] );

        $form['categories'] = $instance['categories'];
        $form['relation'] = esc_attr($instance['relation']);
        $form['tags'] = $instance['tags'];
        $form['number_of_article'] = $instance['number_of_article'];
        $form['orderby'] = $instance['orderby'];
        $form['display_type'] = $instance['display_type'];
        $form['ajax_more_posts'] = $instance['ajax_more_posts'];
        $form['ajax_btn_text'] = $instance['ajax_btn_text'];
        $form['display_date_meta'] = $instance['display_date_meta'];
        $form['display_readmore'] = $instance['display_readmore'];
        $form['display_author_meta'] = $instance['display_author_meta'];
        ?>
        <div class="kopa-one-half">
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', kopa_get_domain()); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('categories'); ?>"><?php _e('Categories:', kopa_get_domain()); ?></label>                
                <select class="widefat" id="<?php echo $this->get_field_id('categories'); ?>" name="<?php echo $this->get_field_name('categories'); ?>[]" multiple="multiple" size="5" autocomplete="off">
                    <option value=""><?php _e('-- None --', kopa_get_domain()); ?></option>
                    <?php
                    $categories = get_categories();
                    foreach ($categories as $category) {
                        printf('<option value="%1$s" %4$s>%2$s (%3$s)</option>', $category->term_id, $category->name, $category->count, (in_array($category->term_id, $form['categories'])) ? 'selected="selected"' : '');
                    }
                    ?>
                </select>

            </p>
            <p>
                <label for="<?php echo $this->get_field_id('relation'); ?>"><?php _e('Relation:', kopa_get_domain()); ?></label>                
                <select class="widefat" id="<?php echo $this->get_field_id('relation'); ?>" name="<?php echo $this->get_field_name('relation'); ?>" autocomplete="off">
                    <?php
                    $relation = array(
                        'AND' => __('And', kopa_get_domain()),
                        'OR' => __('Or', kopa_get_domain())
                    );
                    foreach ($relation as $value => $title) {
                        printf('<option value="%1$s" %3$s>%2$s</option>', $value, $title, ($value === $form['relation']) ? 'selected="selected"' : '');
                    }
                    ?>
                </select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('tags'); ?>"><?php _e('Tags:', kopa_get_domain()); ?></label>                
                <select class="widefat" id="<?php echo $this->get_field_id('tags'); ?>" name="<?php echo $this->get_field_name('tags'); ?>[]" multiple="multiple" size="5" autocomplete="off">
                    <option value=""><?php _e('-- None --', kopa_get_domain()); ?></option>
                    <?php
                    $tags = get_tags();
                    foreach ($tags as $tag) {
                        printf('<option value="%1$s" %4$s>%2$s (%3$s)</option>', $tag->term_id, $tag->name, $tag->count, (in_array($tag->term_id, $form['tags'])) ? 'selected="selected"' : '');
                    }
                    ?>
                </select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('number_of_article'); ?>"><?php _e('Number of article:', kopa_get_domain()); ?></label>                
                <input class="widefat" type="number" min="1" id="<?php echo $this->get_field_id('number_of_article'); ?>" name="<?php echo $this->get_field_name('number_of_article'); ?>" value="<?php echo esc_attr( $form['number_of_article'] ); ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e('Orderby:', kopa_get_domain()); ?></label>                
                <select class="widefat" id="<?php echo $this->get_field_id('orderby'); ?>" name="<?php echo $this->get_field_name('orderby'); ?>" autocomplete="off">
                    <?php
                    $orderby = array(
                        'latest' => __('Latest', kopa_get_domain()),
                        'popular' => __('Popular by View Count', kopa_get_domain()),
                        'most_comment' => __('Popular by Comment Count', kopa_get_domain()),
                        'random' => __('Random', kopa_get_domain()),
                    );
                    foreach ($orderby as $value => $title) {
                        printf('<option value="%1$s" %3$s>%2$s</option>', $value, $title, ($value === $form['orderby']) ? 'selected="selected"' : '');
                    }
                    ?>
                </select>
            </p>
        </div>
        <div class="kopa-one-half last">
            <p>
                <label for="<?php echo $this->get_field_id('display_type'); ?>"><?php _e('Display Type:', kopa_get_domain()); ?></label>                
                <select class="widefat" id="<?php echo $this->get_field_id('display_type'); ?>" name="<?php echo $this->get_field_name('display_type'); ?>" autocomplete="off">
                    <?php
                    $display_types = array(
                        'normal'             => __('List with ajax load more, suitable for widget area 3', kopa_get_domain()),
                        'first_featured'     => __('First featured latest post and a posts list, suitable for widget area 4', kopa_get_domain()),
                        'medium_normal_list' => __('List with medium featured images, suitable for left sidebar', kopa_get_domain()),
                    );
                    foreach ($display_types as $value => $title) {
                        printf('<option value="%1$s" %3$s>%2$s</option>', $value, $title, ($value === $form['display_type']) ? 'selected="selected"' : '');
                    }
                    ?>
                </select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('ajax_more_posts'); ?>"><?php _e('Number of articles will be loaded when click to load more button:', kopa_get_domain()); ?></label>                
                <input class="widefat" type="number" min="1" id="<?php echo $this->get_field_id('ajax_more_posts'); ?>" name="<?php echo $this->get_field_name('ajax_more_posts'); ?>" value="<?php echo esc_attr( $form['ajax_more_posts'] ); ?>">
                <small><?php _e( 'Leave it empty to hide ajax load more button.', kopa_get_domain() ); ?></small>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('ajax_btn_text'); ?>"><?php _e('Ajax load more button label:', kopa_get_domain()); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('ajax_btn_text'); ?>" name="<?php echo $this->get_field_name('ajax_btn_text'); ?>" type="text" value="<?php echo esc_attr($form['ajax_btn_text']); ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'display_date_meta' ); ?>"><?php _e( 'Display date metadata', kopa_get_domain() ); ?></label>
                <input type="checkbox" name="<?php echo $this->get_field_name( 'display_date_meta' ); ?>" id="<?php echo $this->get_field_id( 'display_date_meta' ); ?>" <?php checked( $form['display_date_meta'], true ); ?>>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'display_readmore' ); ?>"><?php _e( 'Display read more button', kopa_get_domain() ); ?></label>
                <input type="checkbox" name="<?php echo $this->get_field_name( 'display_readmore' ); ?>" id="<?php echo $this->get_field_id( 'display_readmore' ); ?>" <?php checked( $form['display_readmore'], true ); ?>>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'display_author_meta' ); ?>"><?php _e( 'Display author metadata', kopa_get_domain() ); ?></label>
                <input type="checkbox" name="<?php echo $this->get_field_name( 'display_author_meta' ); ?>" id="<?php echo $this->get_field_id( 'display_author_meta' ); ?>" <?php checked( $form['display_author_meta'], true ); ?>>
            </p>
        </div>
        <div class="clear"></div>
        <?php
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['categories'] = (empty($new_instance['categories'])) ? array() : array_filter($new_instance['categories']);
        $instance['relation'] = $new_instance['relation'];
        $instance['tags'] = (empty($new_instance['tags'])) ? array() : array_filter($new_instance['tags']);
        $instance['number_of_article'] = (int) $new_instance['number_of_article'];
        // validate number of article
        if ( 0 >= $instance['number_of_article'] ) {
            $instance['number_of_article'] = 6;
        }
        $instance['orderby'] = $new_instance['orderby'];
        $instance['display_type'] = $new_instance['display_type'];
        $instance['ajax_more_posts'] = (int) $new_instance['ajax_more_posts'];
        if ( 0 > $instance['ajax_more_posts'] ) {
            $instance['ajax_more_posts'] = 3;
        }
        $instance['ajax_btn_text'] = strip_tags( $new_instance['ajax_btn_text'] );

        $instance['display_date_meta'] = isset( $new_instance['display_date_meta'] ) ? true : false;
        $instance['display_readmore'] = isset( $new_instance['display_readmore'] ) ? true : false;
        $instance['display_author_meta'] = isset( $new_instance['display_author_meta'] ) ? true : false;

        return $instance;
    }
}

/**
 * Featured Articles Slider Widget Class
 * @since Ultra Mag 1.0
 */
class Kopa_Widget_Featured_Articles_Slider extends WP_Widget {
    function __construct() {
        $widget_ops = array('classname' => '', 'description' => __('Display Featured Articles Slider Widget', kopa_get_domain()));
        $control_ops = array('width' => '500', 'height' => 'auto');
        parent::__construct('kopa_widget_featured_articles_slider', __('Kopa Featured Slider', kopa_get_domain()), $widget_ops, $control_ops);
    }

    function widget( $args, $instance ) {
        extract( $args );
        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
        $query_args['categories'] = $instance['categories'];
        $query_args['relation'] = $instance['relation'];
        $query_args['tags'] = $instance['tags'];
        $query_args['posts_per_page'] = $instance['number_of_article'];
        $query_args['orderby'] = $instance['orderby'];

        echo $before_widget;

        if ( ! empty( $title ) ) {
            echo $before_title . $title . $after_title;
        }
        
        $posts = kopa_widget_posttype_build_query( $query_args );

        if ( $posts->have_posts() ) { ?>
        <div class="flexslider home-slider loading" data-animation="<?php echo $instance['animation']; ?>" data-direction="<?php echo $instance['direction'] ?>" data-slideshow_speed="<?php echo $instance['slideshow_speed']; ?>" data-animation_speed="<?php echo $instance['animation_speed']; ?>" data-autoplay="<?php echo $instance['is_auto_play']; ?>">
            <ul class="slides">
            <?php
            while ( $posts->have_posts() ) { 
                $posts->the_post(); 

                $total_view_count = (int) get_post_meta( get_the_ID(), 'kopa_' . kopa_get_domain() . '_total_view', true );

                if ( 'audio' == get_post_format() || 'video' == get_post_format() ) {
                    $post_format_class = get_post_format() . '-post';
                } else {
                    $post_format_class = 'standard-post';
                }

                ?>
            
            <li>
                <article class="entry-item <?php echo $post_format_class; ?>">
                    <?php if ( has_post_thumbnail() ) { 
                        // get featured image full size
                        $full_featured_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
                        ?>
                    <div class="entry-thumb hover-effect">
                        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'large' ); ?></a>

                        <?php if ( get_post_format() !== 'video' ) { ?>
                            <a href="<?php echo esc_url( $full_featured_image[0] ); ?>" class="hover-icon" rel="prettyPhoto[<?php echo $this->get_field_id( 'gallery' ); ?>]"></a>
                        <?php } else {
                            $video = kopa_content_get_video( get_the_content() );
                            $is_displayed_video = false;
                            if ( isset( $video[0] ) && $video[0] ) {
                                $video = $video[0];

                                if ( isset( $video['url'] ) && $video['url'] ) { ?>
                                    <a href="<?php echo esc_url( $video['url'] ); ?>" class="hover-icon" rel="prettyPhoto[<?php echo $this->get_field_id( 'gallery' ); ?>]"></a>
                        <?php
                                    $is_displayed_video = true;
                                } // endif
                            } // endif 

                            if ( ! $is_displayed_video ) { ?>
                                <a href="<?php echo esc_url( $full_featured_image[0] ); ?>" class="hover-icon" rel="prettyPhoto[<?php echo $this->get_field_id( 'gallery' ); ?>]"></a>
                        <?php 
                            } // endif is_displayed_video
                        } // endif ?>
                    </div>
                    <?php } // endif has_post_thumbnail ?>
                    <div class="entry-content">
                        <header>
                            <?php if ( $instance['display_date_meta'] ) { ?>
                            <span class="entry-date clearfix"><?php echo KopaIcon::getIcon('fa fa-calendar-o entry-icon', 'span'); ?><span><?php the_time( get_option( 'date_format' ) ); ?>, <?php the_time( get_option( 'time_format' ) ); ?></span></span>
                            <?php } ?>

                            <?php if ( comments_open() ) { ?>
                            <span class="entry-comments clearfix"><?php echo KopaIcon::getIcon('fa fa-comment-o entry-icon', 'span'); ?><?php comments_popup_link( __('No Comments', kopa_get_domain()), __('1 Comment', kopa_get_domain()), __('% Comments', kopa_get_domain()) ); ?></span>
                            <?php } ?>
                            
                            <?php if ( $instance['display_view_meta'] && $total_view_count ) { ?>
                            <span class="entry-views clearfix"><?php echo KopaIcon::getIcon('fa fa-eye entry-icon', 'span'); ?><a href="<?php the_permalink(); ?>"><?php printf( _n( '1 View', '%s Views', $total_view_count, kopa_get_domain() ), $total_view_count ); ?></a></span>
                            <?php } ?>
                        </header>
                        <h6 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h6>
                        <?php the_excerpt(); ?>

                        <?php if ( $instance['display_readmore'] ) { ?>
                        <a href="<?php the_permalink(); ?>" class="more-link"><?php _e( 'Read more...', kopa_get_domain() ); ?></a>
                        <?php } ?>
                    </div>
                </article>
            </li>

            <?php
            } // endwhile
            ?>
            </ul>
        </div>
        <?php
        } // endif $posts->have_posts()
        
        wp_reset_postdata();
        
        echo $after_widget;
    }

    function form($instance) {
        $defaults = array(
            'title'             => '',
            'categories'        => array(),
            'relation'          => 'OR',
            'tags'              => array(),
            'number_of_article' => 6,
            'orderby'           => 'latest',
            'animation'         => 'slide',
            'direction'         => 'horizontal',
            'slideshow_speed'   => '7000',
            'animation_speed'   => '600',
            'is_auto_play'      => 'true',
            'display_date_meta' => true,
            'display_readmore'  => true,
            'display_view_meta' => true,

        );
        $instance = wp_parse_args( (array) $instance, $defaults );

        $title = strip_tags( $instance['title'] );
        $form['categories'] = $instance['categories'];
        $form['relation'] = esc_attr($instance['relation']);
        $form['tags'] = $instance['tags'];
        $form['number_of_article'] = (int) $instance['number_of_article'];
        $form['orderby'] = $instance['orderby'];

        $form['animation'] = $instance['animation'];
        $form['direction'] = $instance['direction'];
        $form['slideshow_speed'] = (int) $instance['slideshow_speed'];
        $form['animation_speed'] = (int) $instance['animation_speed'];
        $form['is_auto_play'] = $instance['is_auto_play'];
        $form['display_date_meta'] = $instance['display_date_meta'];
        $form['display_view_meta'] = $instance['display_view_meta'];
        $form['display_readmore'] = $instance['display_readmore'];
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', kopa_get_domain()); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <div class="kopa-one-half">
            <p>
                <label for="<?php echo $this->get_field_id('categories'); ?>"><?php _e('Categories:', kopa_get_domain()); ?></label>                
                <select class="widefat" id="<?php echo $this->get_field_id('categories'); ?>" name="<?php echo $this->get_field_name('categories'); ?>[]" multiple="multiple" size="5" autocomplete="off">
                    <option value=""><?php _e('-- None --', kopa_get_domain()); ?></option>
                    <?php
                    $categories = get_categories();
                    foreach ($categories as $category) {
                        printf('<option value="%1$s" %4$s>%2$s (%3$s)</option>', $category->term_id, $category->name, $category->count, (in_array($category->term_id, $form['categories'])) ? 'selected="selected"' : '');
                    }
                    ?>
                </select>

            </p>
            <p>
                <label for="<?php echo $this->get_field_id('relation'); ?>"><?php _e('Relation:', kopa_get_domain()); ?></label>                
                <select class="widefat" id="<?php echo $this->get_field_id('relation'); ?>" name="<?php echo $this->get_field_name('relation'); ?>" autocomplete="off">
                    <?php
                    $relation = array(
                        'AND' => __('And', kopa_get_domain()),
                        'OR' => __('Or', kopa_get_domain())
                    );
                    foreach ($relation as $value => $title) {
                        printf('<option value="%1$s" %3$s>%2$s</option>', $value, $title, ($value === $form['relation']) ? 'selected="selected"' : '');
                    }
                    ?>
                </select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('tags'); ?>"><?php _e('Tags:', kopa_get_domain()); ?></label>                
                <select class="widefat" id="<?php echo $this->get_field_id('tags'); ?>" name="<?php echo $this->get_field_name('tags'); ?>[]" multiple="multiple" size="5" autocomplete="off">
                    <option value=""><?php _e('-- None --', kopa_get_domain()); ?></option>
                    <?php
                    $tags = get_tags();
                    foreach ($tags as $tag) {
                        printf('<option value="%1$s" %4$s>%2$s (%3$s)</option>', $tag->term_id, $tag->name, $tag->count, (in_array($tag->term_id, $form['tags'])) ? 'selected="selected"' : '');
                    }
                    ?>
                </select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('number_of_article'); ?>"><?php _e('Number of article:', kopa_get_domain()); ?></label>                
                <input class="widefat" type="number" min="1" id="<?php echo $this->get_field_id('number_of_article'); ?>" name="<?php echo $this->get_field_name('number_of_article'); ?>" value="<?php echo esc_attr( $form['number_of_article'] ); ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e('Orderby:', kopa_get_domain()); ?></label>                
                <select class="widefat" id="<?php echo $this->get_field_id('orderby'); ?>" name="<?php echo $this->get_field_name('orderby'); ?>" autocomplete="off">
                    <?php
                    $orderby = array(
                        'latest' => __('Latest', kopa_get_domain()),
                        'popular' => __('Popular by View Count', kopa_get_domain()),
                        'most_comment' => __('Popular by Comment Count', kopa_get_domain()),
                        'random' => __('Random', kopa_get_domain())
                    );
                    foreach ($orderby as $value => $title) {
                        printf('<option value="%1$s" %3$s>%2$s</option>', $value, $title, ($value === $form['orderby']) ? 'selected="selected"' : '');
                    }
                    ?>
                </select>
            </p>
        </div>
        <div class="kopa-one-half last">
            <p>
                <label for="<?php echo $this->get_field_id('animation'); ?>"><?php _e('Animation:', kopa_get_domain()); ?></label>                
                <select class="widefat" id="<?php echo $this->get_field_id('animation'); ?>" name="<?php echo $this->get_field_name('animation'); ?>" autocomplete="off">
                    <?php
                    $animation = array(
                        'slide' => __('Slide', kopa_get_domain()),
                        'fade'  => __('Fade', kopa_get_domain()),
                    );
                    foreach ($animation as $value => $title) {
                        printf('<option value="%1$s" %3$s>%2$s</option>', $value, $title, ($value === $form['animation']) ? 'selected="selected"' : '');
                    }
                    ?>
                </select>
            </p>

            <p>
                <label for="<?php echo $this->get_field_id('direction'); ?>"><?php _e('Direction:', kopa_get_domain()); ?></label>                
                <select class="widefat" id="<?php echo $this->get_field_id('direction'); ?>" name="<?php echo $this->get_field_name('direction'); ?>" autocomplete="off">
                    <?php
                    $direction = array(
                        'horizontal' => __('Horizontal', kopa_get_domain()),
                    );
                    foreach ($direction as $value => $title) {
                        printf('<option value="%1$s" %3$s>%2$s</option>', $value, $title, ($value === $form['direction']) ? 'selected="selected"' : '');
                    }
                    ?>
                </select>
            </p>

            <p>
                <label for="<?php echo $this->get_field_id('slideshow_speed'); ?>"><?php _e('Speed of the slideshow cycling:', kopa_get_domain()); ?></label> 
                <input class="widefat" id="<?php echo $this->get_field_id('slideshow_speed'); ?>" name="<?php echo $this->get_field_name('slideshow_speed'); ?>" type="number" value="<?php echo $form['slideshow_speed']; ?>" />
            </p>

            <p>
                <label for="<?php echo $this->get_field_id('animation_speed'); ?>"><?php _e('Speed of animations:', kopa_get_domain()); ?></label>                
                <input class="widefat" id="<?php echo $this->get_field_id('animation_speed'); ?>" name="<?php echo $this->get_field_name('animation_speed'); ?>" type="number" value="<?php echo $form['animation_speed']; ?>" />
            </p>

            <p>
                <input class="" id="<?php echo $this->get_field_id('is_auto_play'); ?>" name="<?php echo $this->get_field_name('is_auto_play'); ?>" type="checkbox" value="true" <?php echo ('true' === $form['is_auto_play']) ? 'checked="checked"' : ''; ?> />
                <label for="<?php echo $this->get_field_id('is_auto_play'); ?>"><?php _e('Auto Play', kopa_get_domain()); ?></label>                                
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'display_date_meta' ); ?>"><?php _e( 'Display date metadata', kopa_get_domain() ); ?></label>
                <input type="checkbox" name="<?php echo $this->get_field_name( 'display_date_meta' ); ?>" id="<?php echo $this->get_field_id( 'display_date_meta' ); ?>" <?php checked( $form['display_date_meta'], true ); ?>>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'display_view_meta' ); ?>"><?php _e( 'Display view count', kopa_get_domain() ); ?></label>
                <input type="checkbox" name="<?php echo $this->get_field_name( 'display_view_meta' ); ?>" id="<?php echo $this->get_field_id( 'display_view_meta' ); ?>" <?php checked( $form['display_view_meta'], true ); ?>>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'display_readmore' ); ?>"><?php _e( 'Display read more button', kopa_get_domain() ); ?></label>
                <input type="checkbox" name="<?php echo $this->get_field_name( 'display_readmore' ); ?>" id="<?php echo $this->get_field_id( 'display_readmore' ); ?>" <?php checked( $form['display_readmore'], true ); ?>>
            </p>
        </div>
        <div class="kopa-clear"></div>
        <?php
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['categories'] = (empty($new_instance['categories'])) ? array() : array_filter($new_instance['categories']);
        $instance['relation'] = $new_instance['relation'];
        $instance['tags'] = (empty($new_instance['tags'])) ? array() : array_filter($new_instance['tags']);
        $instance['number_of_article'] = (int) $new_instance['number_of_article'];
        if ( 0 >= $instance['number_of_article'] ) {
            $instance['number_of_article'] = 6;
        }
        $instance['orderby'] = $new_instance['orderby'];

        $instance['animation'] = $new_instance['animation'];
        $instance['direction'] = $new_instance['direction'];
        $instance['slideshow_speed'] = (int) $new_instance['slideshow_speed'];
        $instance['animation_speed'] = (int) $new_instance['animation_speed'];
        $instance['is_auto_play'] = isset($new_instance['is_auto_play']) ? $new_instance['is_auto_play'] : 'false';
        $instance['display_date_meta'] = isset($new_instance['display_date_meta']) ? true : false;
        $instance['display_view_meta'] = isset($new_instance['display_view_meta']) ? true : false;
        $instance['display_readmore'] = isset($new_instance['display_readmore']) ? true : false;

        return $instance;
    }
}

/**
 * Mailchimp Subscribe Widget Class
 * @since Ultra Mag 1.0
 */
class Kopa_Widget_Mailchimp_Subscribe extends WP_Widget {
    function __construct() {
        $widget_ops = array('classname' => 'kp-newsletter-widget', 'description' => __('Display mailchimp newsletter subscription form', kopa_get_domain()));
        $control_ops = array('width' => '400', 'height' => 'auto');
        parent::__construct('kopa_widget_mailchimp_subscribe', __('Kopa Mailchimp Subscribe', kopa_get_domain()), $widget_ops, $control_ops);
    }

    function widget( $args, $instance ) {
        extract( $args );
        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
        $mailchimp_form_action = $instance['mailchimp_form_action'];
        $mailchimp_enable_popup = $instance['mailchimp_enable_popup'];
        $description = $instance['description'];
        $submit_btn_text = ! empty( $instance['submit_btn_text'] ) ? $instance['submit_btn_text'] : __( 'Subscribe', kopa_get_domain() );
        $placeholder = ! empty( $instance['placeholder'] ) ? $instance['placeholder'] : __( 'Enter your email', kopa_get_domain() );

        echo $before_widget;

        if ( ! empty( $title ) ) {
            echo $before_title . $title . $after_title;
        }

        if ( ! empty( $mailchimp_form_action ) ) {
        ?>

        <form action="<?php echo esc_url( $mailchimp_form_action ); ?>" method="post" class="newsletter-form clearfix" <?php echo $mailchimp_enable_popup ? 'target="_blank"' : ''; ?>>
            <p><?php echo $description; ?></p>
            <p class="input-email clearfix">
                <input type="email" onfocus="if(this.value==this.defaultValue)this.value='';" onblur="if(this.value=='')this.value=this.defaultValue;" name="EMAIL" value="<?php echo $placeholder; ?>" class="email" size="40">
                <input type="submit" value="<?php echo $submit_btn_text ?>" class="submit">
            </p>
        </form>

        <?php
        } // endif
        
        echo $after_widget;
    }

    function form( $instance ) {
        $defaults = array(
            'title'                  => __( 'NEWSLETTER', kopa_get_domain() ),
            'mailchimp_form_action'  => '',
            'mailchimp_enable_popup' => false,
            'description'            => '',
            'placeholder'            => __( 'Enter your email', kopa_get_domain() ),
            'submit_btn_text'        => __( 'Subscribe', kopa_get_domain() ),
        );
        $instance = wp_parse_args( (array) $instance, $defaults );
        $title = strip_tags( $instance['title'] );
        $form['mailchimp_form_action'] = $instance['mailchimp_form_action'];
        $form['mailchimp_enable_popup'] = $instance['mailchimp_enable_popup'];
        $form['description'] = $instance['description'];
        $form['placeholder'] = $instance['placeholder'];
        $form['submit_btn_text'] = $instance['submit_btn_text'];
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', kopa_get_domain()); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('mailchimp_form_action'); ?>"><?php _e('Mailchimp Form Action:', kopa_get_domain()); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('mailchimp_form_action'); ?>" name="<?php echo $this->get_field_name('mailchimp_form_action'); ?>" type="text" value="<?php echo esc_attr($form['mailchimp_form_action']); ?>">
        </p>
        <p>
            <input type="checkbox" value="true" id="<?php echo $this->get_field_id( 'mailchimp_enable_popup' ); ?>" name="<?php echo $this->get_field_name( 'mailchimp_enable_popup' ); ?>" <?php checked( true, $form['mailchimp_enable_popup'] ); ?>>
            <label for="<?php echo $this->get_field_id( 'mailchimp_enable_popup' ); ?>"><?php _e( 'Enable <strong>evil</strong> popup mode', kopa_get_domain() ); ?></label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'description' ); ?>"><?php _e( 'Description', kopa_get_domain() ); ?></label>
            <textarea class="widefat" name="<?php echo $this->get_field_name('description') ?>" id="<?php echo $this->get_field_id('description') ?>" rows="5"><?php echo esc_textarea( $form['description'] ); ?></textarea>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('placeholder'); ?>"><?php _e('Email field placeholder:', kopa_get_domain()); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('placeholder'); ?>" name="<?php echo $this->get_field_name('placeholder'); ?>" type="text" value="<?php echo esc_attr($form['placeholder']); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('submit_btn_text'); ?>"><?php _e('Submit button text:', kopa_get_domain()); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('submit_btn_text'); ?>" name="<?php echo $this->get_field_name('submit_btn_text'); ?>" type="text" value="<?php echo esc_attr($form['submit_btn_text']); ?>">
        </p>
        <?php
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['mailchimp_form_action'] = $new_instance['mailchimp_form_action'];
        $instance['mailchimp_enable_popup'] = (bool) $new_instance['mailchimp_enable_popup'] ? true : false;
        $instance['description'] = strip_tags( $new_instance['description'] );
        $instance['placeholder'] = strip_tags( $new_instance['placeholder'] );
        $instance['submit_btn_text'] = strip_tags( $new_instance['submit_btn_text'] );

        return $instance;
    }
}

/**
 * FeedBurner Subscribe Widget Class
 * @since Ultra Mag 1.0
 */
class Kopa_Widget_Feedburner_Subscribe extends WP_Widget {
    function __construct() {
        $widget_ops = array('classname' => 'kp-newsletter-widget', 'description' => __('Display Feedburner subscription form', kopa_get_domain()));
        $control_ops = array('width' => '400', 'height' => 'auto');
        parent::__construct('kopa_widget_feedburner_subscribe', __('Kopa Feedburner Subscribe', kopa_get_domain()), $widget_ops, $control_ops);
    }

    function widget( $args, $instance ) {
        extract( $args );
        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
        $feedburner_id = $instance['feedburner_id'];
        $description = $instance['description'];
        $submit_btn_text = ! empty( $instance['submit_btn_text'] ) ? $instance['submit_btn_text'] : __( 'Subscribe', kopa_get_domain() );
        $placeholder = ! empty( $instance['placeholder'] ) ? $instance['placeholder'] : __( 'Enter your email', kopa_get_domain() );

        echo $before_widget;

        if ( ! empty( $title ) ) {
            echo $before_title . $title . $after_title;
        }

        if ( ! empty( $feedburner_id ) ) {

        ?>

        <form class="newsletter-form clearfix" action="http://feedburner.google.com/fb/a/mailverify" method="post" class="newsletter-form clearfix" target="popupwindow" onsubmit="window.open('http://feedburner.google.com/fb/a/mailverify?uri=<?php echo esc_attr( $feedburner_id ); ?>', 'popupwindow', 'scrollbars=yes,width=550,height=520');return true">
            <p><?php echo $description; ?></p>
            <input type="hidden" value="<?php echo esc_attr( $feedburner_id ); ?>" name="uri">
            <p class="input-email clearfix">
                <input type="email" onfocus="if(this.value==this.defaultValue)this.value='';" onblur="if(this.value=='')this.value=this.defaultValue;" name="email" value="<?php echo $placeholder; ?>" class="email" size="40">
                <input type="submit" value="<?php echo $submit_btn_text ?>" class="submit">
            </p>
        </form>

        <?php
        } // endif
        
        echo $after_widget;
    }

    function form( $instance ) {
        $defaults = array(
            'title'           => __( 'NEWSLETTER', kopa_get_domain() ) ,
            'feedburner_id'   => '',
            'description'     => '',
            'placeholder'     => __( 'Enter your email', kopa_get_domain() ),
            'submit_btn_text' => __( 'Subscribe', kopa_get_domain() ),
        );
        $instance = wp_parse_args( (array) $instance, $defaults );
        $title = strip_tags( $instance['title'] );
        $form['feedburner_id'] = $instance['feedburner_id'];
        $form['description'] = $instance['description'];
        $form['placeholder'] = $instance['placeholder'];
        $form['submit_btn_text'] = $instance['submit_btn_text'];
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', kopa_get_domain()); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('feedburner_id'); ?>"><?php _e('Feedburner ID (http://feeds.feedburner.com/<strong>wordpress/kopatheme</strong>):', kopa_get_domain()); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('feedburner_id'); ?>" name="<?php echo $this->get_field_name('feedburner_id'); ?>" type="text" value="<?php echo esc_attr($form['feedburner_id']); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'description' ); ?>"><?php _e( 'Description', kopa_get_domain() ); ?></label>
            <textarea class="widefat" name="<?php echo $this->get_field_name('description') ?>" id="<?php echo $this->get_field_id('description') ?>" rows="5"><?php echo esc_textarea( $form['description'] ); ?></textarea>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('placeholder'); ?>"><?php _e('Email field placeholder:', kopa_get_domain()); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('placeholder'); ?>" name="<?php echo $this->get_field_name('placeholder'); ?>" type="text" value="<?php echo esc_attr($form['placeholder']); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('submit_btn_text'); ?>"><?php _e('Submit button text:', kopa_get_domain()); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('submit_btn_text'); ?>" name="<?php echo $this->get_field_name('submit_btn_text'); ?>" type="text" value="<?php echo esc_attr($form['submit_btn_text']); ?>">
        </p>
        <?php
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['feedburner_id'] = strip_tags( $new_instance['feedburner_id'] );
        $instance['description'] = strip_tags( $new_instance['description'] );
        $instance['placeholder'] = strip_tags( $new_instance['placeholder'] );
        $instance['submit_btn_text'] = strip_tags( $new_instance['submit_btn_text'] );

        return $instance;
    }
}

/**
 * Combo Widget Class
 * @since Ultra Mag 1.0
 */
class Kopa_Widget_Combo extends WP_Widget {
    function __construct() {
        $widget_ops = array('classname' => 'kp-accordion-widget', 'description' => __('A widget displays popular articles, random articles, recent articles and most comment articles.', kopa_get_domain()));
        $control_ops = array('width' => 'auto', 'height' => 'auto');
        parent::__construct('kopa_widget_combo', __('Kopa Combo Widget', kopa_get_domain()), $widget_ops, $control_ops);
    }

    function widget( $args, $instance ) {
        extract( $args );
        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

        echo $before_widget;

        if ( ! empty( $title ) ) {
            echo $before_title . $title . $after_title;
        }

        $query_args['posts_per_page'] = $instance['number_of_article'];
        
        $tab_args = array();

        if ( $instance['popular_title'] ) {
            $tab_args[] = array(
                'label'   => $instance['popular_title'],
                'orderby' => 'popular',
            );
        }
        if ( $instance['random_title'] ) {
            $tab_args[] = array(
                'label'   => $instance['random_title'],
                'orderby' => 'random',
            );
        }
        if ( $instance['comment_title'] ) {
            $tab_args[] = array(
                'label'   => $instance['comment_title'],
                'orderby' => 'most_comment',
            );
        }
        if ( $instance['latest_title'] ) {
            $tab_args[] = array(
                'label'   => $instance['latest_title'],
                'orderby' => 'latest',
            );
        }

        ?>

        <div class="acc-wrapper">

        <?php foreach ( $tab_args as $tab_arg ) {
            $query_args['orderby'] = $tab_arg['orderby'];
            $posts = kopa_widget_posttype_build_query( $query_args );

            if ( $posts->have_posts() ) { ?>
            <div class="accordion-title">
                <h3><a href="#"><?php echo $tab_arg['label']; ?></a></h3>
                <span>+</span>
            </div>
            <div class="accordion-container">
                <ul>
                <?php while ( $posts->have_posts() ) {
                    $posts->the_post();
                ?>
                
                <li>
                    <article class="entry-item clearfix">
                    <?php if ( has_post_thumbnail() ) { ?>
                        <div class="entry-thumb">
                            <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'thumbnail' ); ?></a>
                        </div>
                    <?php } // endif ?>
                        <div class="entry-content">
                            <h6 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h6>

                            <?php if ( $instance['display_date_meta'] ) { ?>
                            <span class="entry-date clearfix"><?php echo KopaIcon::getIcon('fa fa-calendar-o entry-icon', 'span'); ?><span><?php the_time( get_option( 'date_format' ) ); ?>, <?php the_time( get_option( 'time_format' ) ); ?></span></span>
                            <?php } ?>
                        </div>
                    </article>
                </li>

                <?php } // endwhile ?>
                </ul>
            </div> <!-- .accordion-container -->
            <?php } // endif

            wp_reset_postdata();
        } // endforeach ?>

        </div> <!-- .acc-wrapper -->

        <?php
        echo $after_widget;
    }

    function form($instance) {
        $defaults = array(
            'title'             => '',
            'number_of_article' => 5,
            'popular_title'     => __( 'POPULAR', kopa_get_domain() ),
            'random_title'      => __( 'RANDOM', kopa_get_domain() ),
            'comment_title'     => __( 'COMMENT', kopa_get_domain() ),
            'latest_title'      => __( 'LATEST', kopa_get_domain() ),
            'display_date_meta' => true,
        );
        $instance = wp_parse_args( (array) $instance, $defaults );
        $title = strip_tags( $instance['title'] );
        $form['number_of_article'] = $instance['number_of_article'];
        $form['popular_title'] = $instance['popular_title'];
        $form['random_title'] = $instance['random_title'];
        $form['comment_title'] = $instance['comment_title'];
        $form['latest_title'] = $instance['latest_title'];
        $form['display_date_meta'] = $instance['display_date_meta'];
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', kopa_get_domain()); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('number_of_article'); ?>"><?php _e('Number of article:', kopa_get_domain()); ?></label>                
            <input class="widefat" type="number" min="1" id="<?php echo $this->get_field_id('number_of_article'); ?>" name="<?php echo $this->get_field_name('number_of_article'); ?>" value="<?php echo esc_attr( $form['number_of_article'] ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('popular_title'); ?>"><?php _e('Popular tab title:', kopa_get_domain()); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('popular_title'); ?>" name="<?php echo $this->get_field_name('popular_title'); ?>" type="text" value="<?php echo esc_attr($form['popular_title']); ?>">
            <small><?php _e( 'Leave it <strong>empty</strong> to hide popular tab.', kopa_get_domain() ); ?></small>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('random_title'); ?>"><?php _e('Random tab title:', kopa_get_domain()); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('random_title'); ?>" name="<?php echo $this->get_field_name('random_title'); ?>" type="text" value="<?php echo esc_attr($form['random_title']); ?>">
            <small><?php _e( 'Leave it <strong>empty</strong> to hide random tab.', kopa_get_domain() ); ?></small>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('comment_title'); ?>"><?php _e('Comment tab title:', kopa_get_domain()); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('comment_title'); ?>" name="<?php echo $this->get_field_name('comment_title'); ?>" type="text" value="<?php echo esc_attr($form['comment_title']); ?>">
            <small><?php _e( 'Leave it <strong>empty</strong> to hide comment tab.', kopa_get_domain() ); ?></small>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('latest_title'); ?>"><?php _e('Latest tab title:', kopa_get_domain()); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('latest_title'); ?>" name="<?php echo $this->get_field_name('latest_title'); ?>" type="text" value="<?php echo esc_attr($form['latest_title']); ?>">
            <small><?php _e( 'Leave it <strong>empty</strong> to hide latest tab.', kopa_get_domain() ); ?></small>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'display_date_meta' ); ?>"><?php _e( 'Display date metadata', kopa_get_domain() ); ?></label>
            <input type="checkbox" name="<?php echo $this->get_field_name( 'display_date_meta' ); ?>" id="<?php echo $this->get_field_id( 'display_date_meta' ); ?>" <?php checked( $form['display_date_meta'], true ); ?>>
        </p>
        <?php
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['number_of_article'] = (int) $new_instance['number_of_article'];
        // validate number of article
        if ( 0 >= $instance['number_of_article'] ) {
            $instance['number_of_article'] = 5;
        }
        $instance['popular_title'] = strip_tags( $new_instance['popular_title'] );
        $instance['random_title'] = strip_tags( $new_instance['random_title'] );
        $instance['comment_title'] = strip_tags( $new_instance['comment_title'] );
        $instance['latest_title'] = strip_tags( $new_instance['latest_title'] );
        $instance['display_date_meta'] = isset( $new_instance['display_date_meta'] ) ? true : false;

        return $instance;
    }
}

/**
 * Entries List Widget Class
 * @since Ultra Mag 1.0
 */
class Kopa_Widget_Entries_List extends WP_Widget {
    function __construct() {
        $widget_ops = array('classname' => 'kp-multimedia-widget clearfix', 'description' => __('Display 3 Latest Articles with Creative style', kopa_get_domain()));
        $control_ops = array('width' => 'auto', 'height' => 'auto');
        parent::__construct('kopa_widget_entries_list', __('Kopa Entries List', kopa_get_domain()), $widget_ops, $control_ops);
    }

    function widget( $args, $instance ) {
        extract( $args );
        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
        $query_args['categories'] = $instance['categories'];
        $query_args['relation'] = $instance['relation'];
        $query_args['tags'] = $instance['tags'];
        $query_args['posts_per_page'] = 3;
        $query_args['orderby'] = $instance['orderby'];

        echo $before_widget;

        if ( ! empty( $title ) ) {
            echo $before_title . $title . $after_title;
        }

        $posts = kopa_widget_posttype_build_query( $query_args );

        if ( $posts->have_posts() ) { 
            $post_index = 1;
            while ( $posts->have_posts() ) { 
                $posts->the_post();

                if ( 'audio' === get_post_format() || 'video' === get_post_format() ) {
                    $post_format_class = get_post_format() . '-post';
                } else {
                    $post_format_class = 'standard-post';
                }

                if ( has_post_thumbnail() ) {
                    $full_featured_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
                }

                if ( 1 === $post_index ) { ?>

                <article class="featured-item <?php echo $post_format_class; ?>">
                    <?php if ( has_post_thumbnail() ) { ?>
                    <div class="entry-thumb hover-effect">
                        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'kopa-entry-list-size' ); ?></a>
                        
                        <?php if ( get_post_format() !== 'video' ) { ?>
                            <a href="<?php echo esc_url( $full_featured_image[0] ); ?>" class="hover-icon" rel="prettyPhoto[<?php echo $this->get_field_id( 'gallery' ); ?>]"></a>
                        <?php } else {
                            $video = kopa_content_get_video( get_the_content() );
                            $is_displayed_video = false;
                            if ( isset( $video[0] ) && $video[0] ) {
                                $video = $video[0];

                                if ( isset( $video['url'] ) && $video['url'] ) { ?>
                                    <a href="<?php echo esc_url( $video['url'] ); ?>" class="hover-icon" rel="prettyPhoto[<?php echo $this->get_field_id( 'gallery' ); ?>]"></a>
                        <?php
                                    $is_displayed_video = true;
                                } // endif
                            } // endif 

                            if ( ! $is_displayed_video ) { ?>
                                <a href="<?php echo esc_url( $full_featured_image[0] ); ?>" class="hover-icon" rel="prettyPhoto[<?php echo $this->get_field_id( 'gallery' ); ?>]"></a>
                        <?php 
                            } // endif is_displayed_video
                        } // endif ?>
                    </div>
                    <?php } ?>
                    <div class="entry-content">
                        <?php if ( $instance['display_date_meta'] ) { ?>
                        <header>
                            <span class="entry-date clearfix"><?php echo KopaIcon::getIcon('fa fa-calendar-o entry-icon', 'span'); ?><span><?php the_time( get_option( 'date_format' ) ); ?>, <?php the_time( get_option( 'time_format' ) ); ?></span></span>
                        </header>
                        <?php } // endif ?>

                        <h6 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h6>
                    </div>
                </article>
    
                <?php } else { ?> 
                    <?php if ( 2 === $post_index ) { ?>
                    <ul class="older-post">
                    <?php } ?>

                    <li>
                        <article class="entry-item <?php echo $post_format_class; ?>">
                            <?php if ( has_post_thumbnail() ) { ?>
                            <div class="entry-thumb hover-effect">
                                <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'kopa-entry-list-sm-size' ); ?></a>
                                
                                <?php if ( get_post_format() !== 'video' ) { ?>
                                    <a href="<?php echo esc_url( $full_featured_image[0] ); ?>" class="hover-icon" rel="prettyPhoto[<?php echo $this->get_field_id( 'gallery' ); ?>]"></a>
                                <?php } else {
                                    $video = kopa_content_get_video( get_the_content() );
                                    $is_displayed_video = false;
                                    if ( isset( $video[0] ) && $video[0] ) {
                                        $video = $video[0];

                                        if ( isset( $video['url'] ) && $video['url'] ) { ?>
                                            <a href="<?php echo esc_url( $video['url'] ); ?>" class="hover-icon" rel="prettyPhoto[<?php echo $this->get_field_id( 'gallery' ); ?>]"></a>
                                <?php
                                            $is_displayed_video = true;
                                        } // endif
                                    } // endif 

                                    if ( ! $is_displayed_video ) { ?>
                                        <a href="<?php echo esc_url( $full_featured_image[0] ); ?>" class="hover-icon" rel="prettyPhoto[<?php echo $this->get_field_id( 'gallery' ); ?>]"></a>
                                <?php 
                                    } // endif is_displayed_video
                                } // endif ?>
                            </div>
                            <?php } ?>
                            <div class="entry-content">
                                <header>
                                    <span class="entry-date clearfix"><?php echo KopaIcon::getIcon('fa fa-calendar-o entry-icon', 'span'); ?><span><?php the_time( get_option( 'date_format' ) ); ?>, <?php the_time( get_option( 'time_format' ) ); ?></span></span>
                                </header>
                                <h6 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h6>
                            </div>
                        </article>
                    </li>

                    <?php if ( $post_index === $posts->post_count ) { ?>
                    </ul>
                    <?php } ?>

                <?php }
                $post_index++; // increment post index by 1
            } // endwhile
        } // endif
        
        wp_reset_postdata();

        echo $after_widget;
    }

    function form($instance) {
        $defaults = array(
            'title'             => '',
            'categories'        => array(),
            'relation'          => 'OR',
            'tags'              => array(),
            'orderby'           => 'latest',
            'display_date_meta' => true,
        );
        $instance = wp_parse_args( (array) $instance, $defaults );
        $title = strip_tags( $instance['title'] );

        $form['categories'] = $instance['categories'];
        $form['relation'] = esc_attr($instance['relation']);
        $form['tags'] = $instance['tags'];
        $form['orderby'] = $instance['orderby'];
        $form['display_date_meta'] = $instance['display_date_meta'];
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', kopa_get_domain()); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('categories'); ?>"><?php _e('Categories:', kopa_get_domain()); ?></label>                
            <select class="widefat" id="<?php echo $this->get_field_id('categories'); ?>" name="<?php echo $this->get_field_name('categories'); ?>[]" multiple="multiple" size="5" autocomplete="off">
                <option value=""><?php _e('-- None --', kopa_get_domain()); ?></option>
                <?php
                $categories = get_categories();
                foreach ($categories as $category) {
                    printf('<option value="%1$s" %4$s>%2$s (%3$s)</option>', $category->term_id, $category->name, $category->count, (in_array($category->term_id, $form['categories'])) ? 'selected="selected"' : '');
                }
                ?>
            </select>

        </p>
        <p>
            <label for="<?php echo $this->get_field_id('relation'); ?>"><?php _e('Relation:', kopa_get_domain()); ?></label>                
            <select class="widefat" id="<?php echo $this->get_field_id('relation'); ?>" name="<?php echo $this->get_field_name('relation'); ?>" autocomplete="off">
                <?php
                $relation = array(
                    'AND' => __('And', kopa_get_domain()),
                    'OR' => __('Or', kopa_get_domain())
                );
                foreach ($relation as $value => $title) {
                    printf('<option value="%1$s" %3$s>%2$s</option>', $value, $title, ($value === $form['relation']) ? 'selected="selected"' : '');
                }
                ?>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('tags'); ?>"><?php _e('Tags:', kopa_get_domain()); ?></label>                
            <select class="widefat" id="<?php echo $this->get_field_id('tags'); ?>" name="<?php echo $this->get_field_name('tags'); ?>[]" multiple="multiple" size="5" autocomplete="off">
                <option value=""><?php _e('-- None --', kopa_get_domain()); ?></option>
                <?php
                $tags = get_tags();
                foreach ($tags as $tag) {
                    printf('<option value="%1$s" %4$s>%2$s (%3$s)</option>', $tag->term_id, $tag->name, $tag->count, (in_array($tag->term_id, $form['tags'])) ? 'selected="selected"' : '');
                }
                ?>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e('Orderby:', kopa_get_domain()); ?></label>                
            <select class="widefat" id="<?php echo $this->get_field_id('orderby'); ?>" name="<?php echo $this->get_field_name('orderby'); ?>" autocomplete="off">
                <?php
                $orderby = array(
                    'latest' => __('Latest', kopa_get_domain()),
                    'popular' => __('Popular by View Count', kopa_get_domain()),
                    'most_comment' => __('Popular by Comment Count', kopa_get_domain()),
                    'random' => __('Random', kopa_get_domain()),
                );
                foreach ($orderby as $value => $title) {
                    printf('<option value="%1$s" %3$s>%2$s</option>', $value, $title, ($value === $form['orderby']) ? 'selected="selected"' : '');
                }
                ?>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'display_date_meta' ); ?>"><?php _e( 'Display date metadata', kopa_get_domain() ); ?></label>
            <input type="checkbox" name="<?php echo $this->get_field_name( 'display_date_meta' ); ?>" id="<?php echo $this->get_field_id( 'display_date_meta' ); ?>" <?php checked( $form['display_date_meta'], true ); ?>>
        </p>
        <?php
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['categories'] = (empty($new_instance['categories'])) ? array() : array_filter($new_instance['categories']);
        $instance['relation'] = $new_instance['relation'];
        $instance['tags'] = (empty($new_instance['tags'])) ? array() : array_filter($new_instance['tags']);
        $instance['orderby'] = $new_instance['orderby'];
        $instance['display_date_meta'] = isset( $new_instance['display_date_meta'] ) ? true : false;

        return $instance;
    }
}

class Kopa_Widget_Social_Links extends WP_Widget {
    function __construct() {
        $widget_ops = array('classname' => 'kp-socials-link-widget', 'description' => __('Display Social Links', kopa_get_domain()));
        $control_ops = array('width' => 'auto', 'height' => 'auto');
        parent::__construct('kopa_widget_social_links', __('Kopa Social Widget', kopa_get_domain()), $widget_ops, $control_ops);
    }

    function widget( $args, $instance ) {
        extract( $args );
        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

        echo $before_widget;

        if ( ! empty( $title ) ) {
            echo $before_title . $title . $after_title;
        }
        ?>

        <ul class="socials-link clearfix">
            <?php kopa_social_links(); ?>
        </ul>
        <!-- socials-link -->

        <?php

        echo $after_widget;
    }

    function form($instance) {
        $defaults = array(
            'title' => '',
        );
        $instance = wp_parse_args( (array) $instance, $defaults );
        $title = strip_tags( $instance['title'] );
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', kopa_get_domain()); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <?php
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']); 

        return $instance;
    }
}

/**
 * Advertising widget class
 * User can upload image and enter a link
 */
class Kopa_Widget_Advertising extends WP_Widget {
    function __construct() {
        $widget_ops = array('classname' => 'kopa-adv-widget', 'description' => __('A widget that displays an advertising image', kopa_get_domain()));
        $control_ops = array('width' => '400', 'height' => 'auto');
        parent::__construct('kopa_widget_advertising', __('Kopa Advertising Widget', kopa_get_domain()), $widget_ops, $control_ops);
    }

    function widget( $args, $instance ) {
        extract($args);
        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

        $target = $instance['target'] ? '_blank' : '_self';

        echo $before_widget;
        if ( !empty( $title ) ) { echo $before_title . $title . $after_title; } ?>
            
            <?php if ( ! empty( $instance['url'] ) ) { 
                echo  '<a href="'.$instance['url'].'" target="'.$target.'">';
            } ?>
            
            <img src="<?php echo esc_url( $instance['image'] ); ?>" alt="<?php echo $title; ?>">

            <?php if ( ! empty( $instance['url'] ) ) { 
                echo '</a>';
            } ?>
            
        <?php
        echo $after_widget;
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['image'] = $new_instance['image'];
        $instance['url'] = esc_url( $new_instance['url'] );
        $instance['target'] = isset( $new_instance['target'] ) ? $new_instance['target'] : null;
        return $instance;
    }

    function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, array( 
            'title'  => '',
            'image'  => '', 
            'url'    => '',
            'target' => null,
        ) );
        $title = strip_tags($instance['title']);
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', kopa_get_domain()); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>"></p>

        <p class="clearfix">
            <label for="<?php echo $this->get_field_id( 'image' ); ?>"><?php _e( 'Image:', kopa_get_domain() ) ?></label>
            <input class="widefat" type="url" value="<?php echo $instance['image']; ?>" id="<?php echo $this->get_field_id( 'image' ); ?>" name="<?php echo $this->get_field_name( 'image' ); ?>">
            <span>&nbsp;</span>
            <button class="left btn btn-success upload_image_button" alt="<?php echo $this->get_field_id( 'image' ); ?>"><i class="icon-circle-arrow-up"></i><?php _e('Upload', kopa_get_domain()); ?></button>
        </p>

        <p><label for="<?php echo $this->get_field_id( 'url' ); ?>"><?php _e('URL:', kopa_get_domain()); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'url' ); ?>" name="<?php echo $this->get_field_name( 'url' ); ?>" type="text" value="<?php echo esc_attr( $instance['url'] ); ?>"></p>

        <p>
            <input type="checkbox" name="<?php echo $this->get_field_name( 'target' ); ?>" id="<?php echo $this->get_field_id( 'target' ); ?>" <?php checked( $instance['target'], '1' ); ?> value="1">
            <label for="<?php echo $this->get_field_id( 'target' ); ?>"><?php _e( 'Open link in a new tab', kopa_get_domain() ); ?></label>
        </p>
    
        <?php
    }
}