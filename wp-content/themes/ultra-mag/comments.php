<?php
if ('comments.php' == basename($_SERVER['SCRIPT_FILENAME'])) {
    die(__('Please do not load this page directly. Thanks!', kopa_get_domain()));
}

// check if post is pwd protected
if ( post_password_required() ) {
    return;
} // endif check pwd

if ( have_comments() ) { ?>  
    <div id="comments">
        <h5><?php comments_number(__('NO COMMENTS', kopa_get_domain()), __('1 COMMENT', kopa_get_domain()), __('% COMMENTS', kopa_get_domain())); ?></h5>
        <ol class="comments-list clearfix">
            <?php
            wp_list_comments(array(
                'walker' => null,
                'style' => 'ol',
                'callback' => 'kopa_comments_callback',
                'end-callback' => null,
                'type' => 'all'
            ));
            ?>
        </ol>

        <?php 
        // whether or not display paginate comments link
        $prev_comments_link = get_previous_comments_link();
        $next_comments_link = get_next_comments_link();

        if ( '' !== $prev_comments_link . $next_comments_link ) { ?>
            <div class="pagination kopa-comment-pagination pull-right"><?php paginate_comments_links(); ?></div>
        <?php } // endif ?>
    </div>
<?php } elseif ( ! comments_open() && post_type_supports(get_post_type(), 'comments') ) {
    return;
} // endif

comment_form(kopa_comment_form_args());

/*
 * Comments call back function
 */
function kopa_comments_callback($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment;

    if ( 'pingback' == get_comment_type() || 'trackback' == get_comment_type() ) { ?>

        <li id="comment-<?php comment_ID(); ?>" <?php comment_class( 'comment clearfix' ); ?>>
            <article id="div-comment-<?php comment_ID(); ?>" class="comment-wrap clearfix">
                <div class="comment-body clearfix">
                    <header class="clearfix">                                
                        <h6><?php _e( 'Pingback', kopa_get_domain() ); ?></h6>
                        <span class="entry-date clearfix"><?php echo KopaIcon::getIcon('fa fa-calendar-o entry-icon', 'span'); ?><span><?php comment_date( get_option( 'date_format' ) ); ?></span></span>
                        <div class="comment-button pull-right">
                            <?php if ( current_user_can( 'moderate_comments' ) ) { 
                                edit_comment_link( __( 'Edit', kopa_get_domain() ) );
                            } ?>                                                
                        </div>
                    </header>
                    <div class="elements-box">
                        <p><?php comment_author_link(); ?></p>
                    </div>
                </div><!--comment-body -->
            </article>
        </li>

    <?php } elseif ( 'comment' == get_comment_type() ) { ?>
                       
        <li id="comment-<?php comment_ID(); ?>" <?php comment_class( 'clearfix' ); ?>>
            <article id="div-comment-<?php comment_ID(); ?>" class="comment-wrap clearfix">
                <div class="comment-avatar">
                    <?php if ( get_comment_author_url() ) { ?>
                        <a href="<?php comment_author_url(); ?>">
                    <?php } ?>

                    <?php echo get_avatar( $comment->comment_author_email, 36 ); ?>

                    <?php if ( get_comment_author_url() ) { ?>
                        </a>
                    <?php } ?>
                </div>
                <div class="comment-body clearfix">
                    <header class="clearfix">                                
                        <h6>
                            <?php if ( get_comment_author_url() ) { ?>
                                <a href="<?php comment_author_url(); ?>">
                            <?php } ?>

                            <?php comment_author(); ?>

                            <?php if ( get_comment_author_url() ) { ?>
                                </a>
                            <?php } ?>
                        </h6>
                        <span class="entry-date clearfix"><?php echo KopaIcon::getIcon('fa fa-calendar-o entry-icon', 'span'); ?><span><?php comment_date( get_option( 'date_format' ) ); ?></span></span>
                        <div class="comment-button pull-right">
                            <?php comment_reply_link(array_merge($args, array('depth' => $depth, 'max_depth' => $args['max_depth']))); ?>
                            
                            <?php if ( current_user_can( 'moderate_comments' ) ) { 
                                edit_comment_link( __( 'Edit', kopa_get_domain() ) );
                            } ?>                                                
                        </div>
                    </header>
                    <div class="elements-box">
                        <?php comment_text(); ?>
                    </div>
                </div><!--comment-body -->
            </article>
        </li>
    <?php
    } // endif check comment type
}

function kopa_comment_form_args() {
    global $user_identity;
    $commenter = wp_get_current_commenter();

    $fields = array(
        'author' => '<div class="comment-left pull-left">'.
                    '<p class="input-block">'.
                        '<label for="comment_name" class="required">'.__( 'Name', kopa_get_domain() ).'<span> (*):</span></label>'.
                        '<input type="text" id="comment_name" name="author" class="valid" value="'.esc_attr($commenter['comment_author']).'">
                    </p>',
        'email' => '<p class="input-block">
                        <label for="comment_email" class="kp-label">'.__( 'Email', kopa_get_domain() ).'<span> (*):</span></label>
                        <input type="email" id="comment_email" name="email" class="valid" value="'.esc_attr($commenter['comment_author_email']).'">
                    </p>',
        'url'   => '<p class="input-block">
                        <label for="comment_url" class="kp-label">'.__( 'Website', kopa_get_domain() ).'</label>
                        <input type="text" id="comment_url" name="url" class="valid" value="'.esc_attr($commenter['comment_author_url']).'">
                    </p>'.
                    '</div>',
    );

    if ( ! is_user_logged_in() ) {
        $comment_field = '<div class="comment-right pull-right"><p class="textarea-block"><label for="comment_message" class="required">'.__( 'Message', kopa_get_domain() ).'<span> (*)</span></label><textarea id="comment_message" name="comment" cols="88" rows="6"></textarea></p></div><div class="clear"></div>';
    } else {
        $comment_field = '<p class="textarea-block"><label for="comment_message" class="required">'.__( 'Message', kopa_get_domain() ).'<span> (*)</span></label><textarea id="comment_message" name="comment" cols="88" rows="6"></textarea></p><div class="clear"></div>';
    }

    $args = array(
        'fields' => apply_filters('comment_form_default_fields', $fields),
        'comment_field' => $comment_field,
        'comment_notes_before' => '<p class="c-note">'.__('Your email address will not be published. Required fields are marked', kopa_get_domain()).' <span>*</span></p>',
        'comment_notes_after' => '',
        'id_form' => 'comments-form',
        'id_submit' => 'submit-comment',
        'title_reply' => __('LEAVE YOUR COMMENT', kopa_get_domain()),
        // 'title_reply_to' => __('Reply to %s', kopa_get_domain()),
        // 'cancel_reply_link' => '<span class="title-text">'.__('Cancel', kopa_get_domain()).'</span>',
        'label_submit' => __('Post Comment', kopa_get_domain()),
    );

    return $args;
}
