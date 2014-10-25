<?php if ( have_posts() ) {
    while ( have_posts() ) {
        the_post(); ?>

    <div id="page-<?php the_ID(); ?>" <?php post_class( 'elements-box clearfix' ); ?>>
        <?php the_content(); ?>
    </div>

    <?php wp_link_pages( array(
        'before' => '<div class="wrap-page-links clearfix">
                          <div class="page-links pull-right">
                          <span class="page-links-title">'.__( 'Pages:', kopa_get_domain() ).'</span>',
        'after'  => '</div></div>',
    ) ); ?>

    <?php comments_template(); ?>
    
<?php } // endwhile
} // endif
?>