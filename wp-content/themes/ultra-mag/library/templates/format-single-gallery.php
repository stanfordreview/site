<div id="post-<?php the_ID(); ?>" <?php post_class( 'entry-box clearfix' ); ?>>
    <header>
        <?php if ( 'show' === get_option('kopa_theme_options_view_date_status', 'show') ) { ?>
        <span class="entry-date clearfix"><?php echo KopaIcon::getIcon('fa fa-calendar-o entry-icon', 'span'); ?><span><?php the_time( get_option( 'date_format' ) ); ?>, <?php the_time( get_option( 'time_format' ) ); ?></span></span>
        <?php } ?>

        <?php if ( comments_open() ) { ?>
        <span class="entry-comments clearfix"><?php echo KopaIcon::getIcon('fa fa-comment-o entry-icon', 'span'); ?><?php comments_popup_link( __( 'No Comments', kopa_get_domain() ), __( '1 Comment', kopa_get_domain() ), __( '% Comments', kopa_get_domain() ) ); ?></span>
        <?php } ?>

        <?php $total_view_count = (int) get_post_meta( get_the_ID(), 'kopa_' . kopa_get_domain() . '_total_view', true );

        if ( 'show' === get_option('kopa_theme_options_view_count_status', 'show') && $total_view_count ) { ?>
        <span class="entry-views clearfix"><?php echo KopaIcon::getIcon('fa fa-eye entry-icon', 'span'); ?><a href="<?php the_permalink(); ?>"><?php printf( _n( '1 View', '%s Views', $total_view_count, kopa_get_domain() ), $total_view_count ); ?></a></span>
        <?php } ?>

        <h1 class="entry-title"><?php the_title(); ?></h1>
    </header>
    
    <?php $attachment_ids = kopa_content_get_gallery_attachment_ids( get_the_content() );
    
    if ( $attachment_ids ) { 
        $main_slider_id = 'kp-single-slider-' . mt_rand(1, 10000);
        $carousel_slider_id = 'kp-single-carousel-' . mt_rand(1, 10000);
        ?>
        <div id="<?php echo $main_slider_id; ?>" class="flexslider kp-single-slider loading" data-carousel-id="#<?php echo $carousel_slider_id; ?>">
            <ul class="slides">
            <?php foreach ( $attachment_ids as $id ) {
                if ( wp_attachment_is_image( $id ) ) { ?>

                <li><?php echo wp_get_attachment_image( $id, 'large' ); ?></li>

                <?php } // endif
            } // endforeach ?>
            </ul>
        </div>

        <div id="<?php echo $carousel_slider_id; ?>" class="flexslider kp-single-carousel" data-main-slider-id="#<?php echo $main_slider_id; ?>">
            <ul class="slides">
            <?php foreach ( $attachment_ids as $id ) {
                if ( wp_attachment_is_image( $id ) ) { ?>

                <li><?php echo wp_get_attachment_image( $id, 'large' ); ?></li>

                <?php } // endif
            } // endforeach ?>
            </ul>
        </div>
    <?php } elseif ( 'show' === get_option( 'kopa_theme_options_featured_image_status', 'show' ) &&  has_post_thumbnail() ) { ?>
        <div class="entry-thumb"><?php the_post_thumbnail( 'kopa-single-featured-size' ); ?></div>
    <?php } ?>
    
    <div class="elements-box clearfix">
        <?php
        $content = get_the_content(); 
        $content = preg_replace('/\[gallery.*](.*\[gallery]){0,1}/', '', $content);
        $content = apply_filters( 'the_content', $content );
        $content = str_replace(']]>', ']]&gt;', $content);
        echo $content; 
        ?>
    </div>
    
    <?php wp_link_pages( array(
        'before'      => '<div class="wrap-page-links clearfix">
                          <div class="page-links pull-right">
                          <span class="page-links-title">'.__( 'Pages:', kopa_get_domain() ).'</span>',
        'after'       => '</div></div>',
    ) ); ?>

    <div class="clearfix">
        <?php kopa_social_sharing_links(); ?>

        <?php the_tags( '<div class="tag-box pull-right"><span>' . __( 'Tagged with:', kopa_get_domain() ) . ' </span>', ', ', '</div>' ); ?>

        <div class="clear"></div>
    </div>
    
    <?php if ( 'show' === get_option('kopa_theme_options_post_navigation_status', 'show') && ( get_next_post() || get_previous_post() ) ) { ?>
    <footer class="clearfix">
        <p class="prev-post pull-left clearfix">
            <?php previous_post_link( '<span class="fa fa-angle-left pull-left"></span>%link', __( 'Previous article', kopa_get_domain() ) ); ?>                           
        </p>
        <p class="next-post pull-right clearfix">
            <?php next_post_link( '%link<span class="fa fa-angle-right pull-right"></span>', __( 'Next article', kopa_get_domain() ) ); ?>
        </p>
    </footer>
    <?php } // endif ?>
</div>
<!-- entry-box -->

<?php kopa_about_author(); ?>
