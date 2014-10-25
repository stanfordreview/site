<?php
if (!function_exists('save_general_setting')) {

    function save_general_setting() {
        if (!wp_verify_nonce($_POST['wpnonce_save_theme_options'], 'save_general_setting'))
            exit();
        $data = $_POST;
        foreach ($data as $key => $value) {
            if (strpos($key, 'kopa_theme_options_') === 0) {
                update_option($key, $value);
            }
        }
        exit();
    }

    add_action('wp_ajax_save_general_setting', 'save_general_setting');
}
/* ==============================================================================
 * Remove Sidebar
  =============================================================================== */
if (!function_exists('kopa_remove_sidebar')) {

    function kopa_remove_sidebar() {
        if (!wp_verify_nonce($_POST['wpnonce'], 'save_sidebar_setting'))
            exit();

        if (!empty($_POST['removed_sidebar_id'])) {
            $removed_sidebar_id = ($_POST['removed_sidebar_id']);
            if ($removed_sidebar_id === 'sidebar_hide') {
                echo json_encode(array("is_exist" => true, "error_message" => "You can not remove this sidebar!"));
            } else {
                $kopa_sidebar = get_option("kopa_sidebar", array());
                $found_sidebar = false;
                foreach ($kopa_sidebar as $e_sidebar_id => $e_sidebar_name) {
                    if ($removed_sidebar_id === $e_sidebar_id) {
                        $found_sidebar = true;
                    }
                }
                if ($found_sidebar) {
                    $kopa_setting = get_option('kopa_setting', array());
                    $found_setting = false;
                    foreach ($kopa_setting as $kopa_setting_key => $kopa_setting_value) {
                        foreach ($kopa_setting_value['sidebars'] as $key => $value) {
                            if ($removed_sidebar_id === $value) {
                                $found_setting = true;
                                $layout_id = $kopa_setting_key;
                            }
                        }
                    }
                    if ($found_setting) {
                        $kopa_template_hierarchy = unserialize(KOPA_TEMPLATE_HIERARCHY);
                        echo json_encode(array("is_exist" => true, "error_message" => "You can not remove this sidebar. It is in used for " . $kopa_template_hierarchy[$layout_id]['title'] . ' page'));
                    } else {
                        unset($kopa_sidebar[$removed_sidebar_id]);
                        update_option("kopa_sidebar", $kopa_sidebar);
                        echo json_encode(array("is_exist" => false, "error_message" => "successfull"));
                    }
                }
            }
        }
        exit();
    }

    add_action('wp_ajax_kopa_remove_sidebar', 'kopa_remove_sidebar');
}
////////////////////////////////////////////////////////
if (!function_exists('kopa_add_sidebar')) {

    function kopa_add_sidebar() {
        if (!wp_verify_nonce($_POST['wpnonce'], 'save_sidebar_setting'))
            exit();
        if (!empty($_POST['new_sidebar_name'])) {
            $kopa_sidebar_name = ($_POST['new_sidebar_name']);
            $kopa_sidebar = get_option("kopa_sidebar", array());
            $sidebar_id = strtolower(trim(str_replace(" ", "_", $kopa_sidebar_name)));
            $found_sidebar = false;
            foreach ($kopa_sidebar as $e_sidebar_id => $e_sidebar_name) {
                if ($sidebar_id === $e_sidebar_id) {
                    $found_sidebar = true;
                }
            }
            if ($found_sidebar) {
                $error_message = 'The sidebar name "' . $kopa_sidebar_name . '" already exist!';
                echo json_encode(array("is_exist" => true, "error_message" => $error_message, "sidebar_id" => $sidebar_id));
            } else {
                echo json_encode(array("is_exist" => false, "error_message" => "", "sidebar_id" => $sidebar_id));
                $kopa_sidebar[$sidebar_id] = $kopa_sidebar_name;
                update_option("kopa_sidebar", $kopa_sidebar);
            }
        }
        exit();
    }

    add_action('wp_ajax_kopa_add_sidebar', 'kopa_add_sidebar');
}
////////////////////////////////////////////////////////
if (!function_exists('save_sidebar_setting')) {

    function save_sidebar_setting() {
        if (!wp_verify_nonce($_POST['wpnonce'], 'save_sidebar_setting'))
            exit();
        if (!empty($_POST[kopa_sidebar])) {
            $kopa_sidebar_name_arr = ($_POST[kopa_sidebar]);
            $kopa_sidebar_existing = get_option("kopa_sidebar", array());

            foreach ($kopa_sidebar_name_arr as $key => $value) {
                $sidebar_id = trim(str_replace(" ", "_", $value)) . $key;
                if (in_array($sidebar_id, $kopa_sidebar_existing)) {
                    $sidebar_id = $sidebar_id . 'kopa';
                }
                $kopa_sidebar[$sidebar_id] = $value;
            }
            update_option("kopa_sidebar", $kopa_sidebar);
        }
        exit();
    }

    add_action('wp_ajax_save_sidebar_setting', 'save_sidebar_setting');
}
////////////////////////////////////////////////////////
if (!function_exists('save_layout')) {

    function save_layout() {
        $kopa_setting = get_option('kopa_setting');
        if (!wp_verify_nonce($_POST['wpnonce'], 'save_layout_setting'))
            exit();
        if (!empty($_POST)) {
            $new_kopa_setting = $_POST['kopa_setting'];
            $template_id = $_POST['template_id'];

            $kopa_setting[$template_id] = $new_kopa_setting[0];
            update_option("kopa_setting", $kopa_setting);
        }
        exit();
    }

    add_action('wp_ajax_save_layout', 'save_layout');
}

if (!function_exists('load_layout')) {

    function load_layout() {
        if (!wp_verify_nonce($_POST['wpnonce'], 'load_layout_setting'))
            exit();
        if (!empty($_POST)) {
            echo kopa_layout_page($_POST['kopa_template_id']);
        }
        exit();
    }

    add_action('wp_ajax_load_layout', 'load_layout');
}

function kopa_layout_page($_kopa_template_id) {
    $kopa_layout = unserialize(KOPA_LAYOUT);
    $kopa_template_hierarchy = unserialize(KOPA_TEMPLATE_HIERARCHY);
    $kopa_sidebar_position = unserialize(KOPA_SIDEBAR_POSITION);
    $kopa_setting = get_option('kopa_setting');
    $kopa_sidebar = get_option('kopa_sidebar');
    wp_nonce_field("load_layout_setting", "nonce_id");
    wp_nonce_field("save_layout_setting", "nonce_id_save");
    ?>
    <div id="kopa-admin-wrapper" class="clearfix">
        <div id="kopa-loading-gif"></div>
        <input type="hidden" id="kopa_template_id" value="<?php echo $_kopa_template_id; ?>">
        <?php
        if ($kopa_template_hierarchy) {
            echo '<div class="kopa-nav list-container">
                <ul class="tabs clearfix">';
            foreach ($kopa_template_hierarchy as $kopa_template_key => $kopa_template_value) {
                if ($kopa_template_key === $_kopa_template_id)
                    $_active = "class='active'";
                else {
                    $_active = '';
                }
                echo '<li ' . $_active . '><span title="' . $kopa_template_key . '" onclick="load_layout_setting(jQuery(this))">' . $kopa_template_value['title'] . '</span></li>';
            }
            echo '</ul><!--tabs--->
             </div><!--kopa-nav-->';
        }
        ?>
        <div class="kopa-content">
            <div class="kopa-page-header clearfix">
                <div class="pull-left">
                    <h4><i class="icon-cog"></i>Layout And Sidebar Manager</h4>
                </div>
                <div class="pull-right">
                    <div class="kopa-copyrights">
                        <span>Visit author URL: </span><a href="http://kopatheme.com">http://kopatheme.com</a>
                    </div><!--="kopa-copyrights-->
                </div>
            </div><!--kopa-page-header-->
            <div class="tab-container">
                <div class="kopa-content-box tab-content kopa-content-main-box" id="<?php echo $_kopa_template_id; ?>">
                    <div class="kopa-actions clearfix">
                        <div class="kopa-button">
                            <span class="btn btn-primary" onclick="save_layout_setting(jQuery(this))"><i class="icon-ok-circle"></i>Save</span>
                        </div>
                    </div><!--kopa-actions-->
                    <div class="kopa-box-head">
                        <i class="icon-hand-right"></i>
                        <span class="kopa-section-title"><?php echo $kopa_template_hierarchy[$_kopa_template_id]['title'] ?></span>
                    </div><!--kopa-box-head-->
                    <div class="kopa-box-body clearfix"> 
                        <div class="kopa-layout-box pull-left">
                            <div class="kopa-select-layout-box kopa-element-box">
                                <span class="kopa-component-title">Select the layout</span>
                                <select class="kopa-layout-select"  onchange="show_onchange(jQuery(this));" autocomplete="off">
                                    <?php
                                    foreach ($kopa_template_hierarchy[$_kopa_template_id]['layout'] as $keys => $value) {
                                        echo '<option value="' . $value . '"';
                                        /* foreach ($kopa_setting as $kopa_setting_key => $kopa_setting_value) {
                                          if ($kopa_setting_key == $_kopa_template_id && $kopa_setting_value[layout_id] == $value) {
                                          echo 'selected="selected"';
                                          }
                                          } */
                                        if ($value === $kopa_setting[$_kopa_template_id]['layout_id']) {
                                            echo 'selected="selected"';
                                        }
                                        echo '>' . $kopa_layout[$value]['title'] . '</option>';
                                    }
                                    ?>
                                </select>                          
                            </div><!--kopa-select-layout-box-->
                            <?php
                            foreach ($kopa_template_hierarchy[$_kopa_template_id]['layout'] as $keys => $value) {
                                foreach ($kopa_layout as $layout_key => $layout_value) {
                                    if ($layout_key == $value) {
                                        ?>
                                        <div class="<?php echo 'kopa-sidebar-box-wrapper sidebar-position-' . $layout_key; ?>">
                                            <?php
                                            foreach ($layout_value['positions'] as $postion_key => $postion_id) {
                                                ?>
                                                <div class="kopa-sidebar-box kopa-element-box">
                                                    <span class="kopa-component-title"><?php echo $kopa_sidebar_position[$postion_id]['title']; ?></span>
                                                    <label class="kopa-label">Select sidebars</label>
                                                    <?php
                                                    echo '<select class="kopa-sidebar-select" autocomplete="off">';
                                                    foreach ($kopa_sidebar as $sidebar_list_key => $sidebar_list_value) {
                                                        $__selected_sidebar = '';
                                                        if ($layout_key === $kopa_setting[$_kopa_template_id]['layout_id']) {
                                                            if ($sidebar_list_key === $kopa_setting[$_kopa_template_id]['sidebars'][$postion_key]) {
                                                                $__selected_sidebar = 'selected="selected"';
                                                            }
                                                        }
                                                        echo '<option value="' . $sidebar_list_key . '" ' . $__selected_sidebar . '>' . $sidebar_list_value . '</option>';
                                                        $__selected_sidebar = '';
                                                    }
                                                    echo '</select>';
                                                    ?>
                                                </div><!--kopa-sidebar-box-->
                                            <?php } ?>
                                        </div><!--kopa-sidebar-box-wrapper-->
                                        <?php
                                    }
                                }
                            }
                            ?>
                        </div><!--kopa-layout-box-->
                        <div class="kopa-thumbnails-box pull-right">
                            <?php
                            foreach ($kopa_template_hierarchy[$_kopa_template_id]['layout'] as $thumbnails_key => $thumbnails_value) {
                                ?>
                                <image class="responsive-img <?php echo ' kopa-cpanel-thumbnails kopa-cpanel-thumbnails-' . $thumbnails_value; ?>" src="<?php echo KOPA_CPANEL_IMAGE_DIR . $kopa_layout[$thumbnails_value]['thumbnails']; ?>" class="img-polaroid" alt="">
                                <?php
                            }
                            ?>
                        </div><!--kopa-thumbnails-box-->
                    </div><!--kopa-box-body-->
                    <div class="kopa-actions kopa-bottom-action-bar clearfix">
                        <div class="kopa-button">
                            <span class="btn btn-primary" onclick="save_layout_setting(jQuery(this))"><i class="icon-ok-circle"></i>Save</span>
                        </div>
                    </div>

                </div><!--kopa-content-box-->
            </div><!--tab-container-->
        </div><!--kopa-content-->
    </div><!--kopa-admin-wrapper-->
    <?php
}

if (!function_exists('kopa_ajax_send_contact')) {

    function kopa_ajax_send_contact() {
        if ( ! check_ajax_referer('kopa_send_contact_nicole_kidman', 'kopa_send_contact_nonce', false) ) {
            die( __('Oops! errors occured.', kopa_get_domain()) );
        }

        foreach ($_POST as $key => $value) {
            if (ini_get('magic_quotes_gpc')) {
                $_POST[$key] = stripslashes($_POST[$key]);
            }
            $_POST[$key] = htmlspecialchars(strip_tags($_POST[$key]));
        }

        $name = $_POST["name"];
        $email = $_POST["email"];
        $message = $_POST["message"];

        $message_body = "Name: {$name}" . PHP_EOL . "Message: {$message}";

        $to = get_bloginfo('admin_email');
        if ( isset( $_POST["subject"] ) && $_POST["subject"] != '' ) {
            $subject = "Contact Form: $name - {$_POST['subject']}";
        } else {
            $subject = "Contact Form: $name";
        }

        if ( isset( $_POST['url'] ) && $_POST['url'] != '' ) {
            $message_body .= PHP_EOL . __('Website:', kopa_get_domain()) . ' ' . $_POST['url'];
        }

        $headers[] = 'From: ' . $name . ' <' . $email . '>';
        $headers[] = 'Cc: ' . $name . ' <' . $email . '>';

        $result = __('Oops! errors occured.', kopa_get_domain());

        if (wp_mail($to, $subject, $message_body, $headers)) {
            $result = __('Success! Your email has been sent.', kopa_get_domain());
        }

        echo $result;
        die();
    }

    add_action('wp_ajax_kopa_send_contact', 'kopa_ajax_send_contact');
    add_action('wp_ajax_nopriv_kopa_send_contact', 'kopa_ajax_send_contact');
}

if (!function_exists('kopa_ajax_set_view_count')) {

    function kopa_ajax_set_view_count() {
        check_ajax_referer('kopa_set_view_count', 'wpnonce');
        if (!empty($_POST['post_id'])) {
            $post_id = (int) $_POST['post_id'];
            $data['count'] = kopa_set_view_count($post_id);
            echo json_encode($data);
        }
        die();
    }

    add_action('wp_ajax_kopa_set_view_count', 'kopa_ajax_set_view_count');
    add_action('wp_ajax_nopriv_kopa_set_view_count', 'kopa_ajax_set_view_count');
}

/**
 * Handle ajax load more of article list widget
 * @since Ultra Mag 1.0.0
 */
if (!function_exists('kopa_article_list_ajax_load_more')) {
    add_action('wp_ajax_kopa_article_list_ajax_load_more', 'kopa_article_list_ajax_load_more');
    add_action('wp_ajax_nopriv_kopa_article_list_ajax_load_more', 'kopa_article_list_ajax_load_more');

    function kopa_article_list_ajax_load_more() {
        check_ajax_referer('kopa_article_list_ajax_load_more', 'kopa_ajax_load_more_nonce');

        $query_args = array();

        $output = '';
        $offset = $_POST['offset'];

        $tax_query = array();
        if ( $_POST['categories'] ) {
            $tax_query[] = array(
                'taxonomy' => 'category',
                'field'    => 'id',
                'terms'    => explode(',', $_POST['categories']),
            );
        }
        if ( $_POST['tags'] ) {
            $tax_query[] = array(
                'taxonomy' => 'post_tag',
                'field'    => 'id',
                'terms'    => explode(',', $_POST['tags']),
            );
        }
        if ( $_POST['relation'] && count( $tax_query ) == 2 ) {
            $tax_query['relation'] = $_POST['relation'];
        }

        if ( $tax_query ) {
            $query_args['tax_query'] = $tax_query;
        }

        $query_args['posts_per_page'] = $_POST['posts_per_page'];

        $orderby = $_POST['orderby'];
        $post__not_in = explode( ',', $_POST['post__not_in'] );

        if ( $orderby != 'random' ) {
            $query_args['offset'] = $offset;
        }

        switch ( $orderby ) {
            case 'popular':
                $query_args['meta_key'] = 'kopa_' . kopa_get_domain() . '_total_view';
                $query_args['orderby'] = 'meta_value_num';
                break;
            case 'most_comment':
                $query_args['orderby'] = 'comment_count';
                break;
            case 'random':
                $query_args['orderby'] = 'rand';
                $query_args['post__not_in'] = $post__not_in;
                break;
            default:
                $query_args['orderby'] = 'date';
                break;
        }

        $posts = new WP_Query( $query_args );

        if ( $posts->have_posts() ) {
            while( $posts->have_posts() ) {
                $posts->the_post();

                if ( 'audio' === get_post_format() || 'video' === get_post_format() ) {
                    $post_format_class = get_post_format() . '-post';
                } else {
                    $post_format_class = 'standard-post';
                }

                $output.= '<li><article class="entry-item '.$post_format_class.'">';
                if ( has_post_thumbnail() ) {
                    $full_featured_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
                    $output .= '<div class="entry-thumb hover-effect">';
                    $output .= '<a href="'.get_permalink().'">';
                    $output .= get_the_post_thumbnail( get_the_ID(), 'kopa-article-list-size' );
                    $output .= '</a>';
                    if ( get_post_format() !== 'video' ) {
                        $output .= '<a href="'.esc_url( $full_featured_image[0] ).'" class="hover-icon" rel="prettyPhoto['.$_POST['light_box_id'].']"></a>';
                    } else {
                        $video = kopa_content_get_video( get_the_content() );
                        $is_displayed_video = false;
                        if ( isset( $video[0] ) && $video[0] ) {
                            $video = $video[0];

                            if ( isset( $video['url'] ) && $video['url'] ) {
                                $output .= '<a href="'.esc_url( $video['url'] ).'" class="hover-icon" rel="prettyPhoto['.$_POST['light_box_id'].']"></a>';
                                $is_displayed_video = true;
                            }
                        }

                        if ( ! $is_displayed_video ) {
                            $output .= '<a href="'.esc_url( $full_featured_image[0] ).'" class="hover-icon" rel="prettyPhoto['.$_POST['light_box_id'].']"></a>';
                        }
                    }
                    $output .= '</div>';
                }

                $output .= '<div class="entry-content">';

                if ( $_POST['display_date_meta'] ) {
                    $output .= '<header><span class="entry-date clearfix">';
                    $output .= KopaIcon::getIcon('fa fa-calendar-o entry-icon', 'span');
                    $output .= '<span>'.get_the_time( get_option( 'date_format' ) ).', '.get_the_time( get_option( 'time_format' ) ).'</span></span></header>';
                } // endif

                $output .= '<h6 class="entry-title"><a href="'.get_permalink().'">'.get_the_title().'</a></h6>';

                /**
                 * get custom excerpt length
                 * @link: http://stackoverflow.com/questions/1112946/how-do-i-get-only-a-determined-number-of-words-from-a-string-in-php
                 */
                $the_excerpt = '';
                $org_excerpt = get_the_excerpt();
                $excerpt_length = get_option( 'kopa_theme_options_frontpage_excerpt_length', 15 );
                $org_excerpt = explode(' ', $org_excerpt);
                if ( is_array( $org_excerpt ) && $org_excerpt ) {
                    for ( $i = 0; $i < $excerpt_length; $i++ ) {
                        if ( isset( $org_excerpt[ $i ] ) ) {
                            $the_excerpt .= $org_excerpt[ $i ];
                        } else {
                            break;
                        }

                        if ( $i !== $excerpt_length - 1 ) {
                            $the_excerpt .= ' ';
                        }
                    }
                }
                /* end get custom excerpt length */

                $output .= apply_filters( 'the_excerpt', $the_excerpt );

                if ( $_POST['display_readmore'] ) { 
                    $output .= '<a href="'.get_permalink().'" class="more-link">'.__( 'Read more...', kopa_get_domain() ).'</a>';
                } // endif

                $output .= '</div>';
                $output .= '</article></li>';

                array_push( $post__not_in, get_the_ID() );
            }

            wp_reset_postdata();
        } else {
            echo null;
            die();
        }

        $responses_data = array(
            'output'       => $output,
            'post__not_in' => implode(',', $post__not_in),
        );

        echo json_encode( $responses_data );
        die();
    }
}
