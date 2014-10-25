<div class="widget kp-search-list-widget">
    <ul class="clearfix">
<?php if ( have_posts() ) { ?>
    
    <?php while ( have_posts() ) {
        the_post(); 
        ?>

        <li id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <article>
                <?php if ( has_post_thumbnail() ) { ?>
                <div class="entry-thumb">
                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a>
                </div>
                <?php } ?>
                <div class="entry-content">
                    <?php if ( 'show' === get_option('kopa_theme_options_view_date_status', 'show') && 'post' === get_post_type() ) { ?>
                    <header><span class="entry-date clearfix"><?php echo KopaIcon::getIcon('fa fa-calendar-o entry-icon', 'span'); ?><span><?php the_time( get_option( 'date_format' ) ); ?>, <?php the_time( get_option( 'time_format' ) ); ?></span></span></header>
                    <?php } ?>
                    <h6 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h6>
                    <?php the_excerpt(); ?>

                    <?php if ( 'show' === get_option( 'kopa_theme_options_blog_readmore_status', 'show' ) ) { ?>
                    <a href="<?php the_permalink(); ?>" class="more-link"><?php _e( 'Read more...', kopa_get_domain() ); ?></a>
                    <?php } ?>
                </div>
            </article>
        </li>  

    <?php } // endwhile
} else { ?>
    <li>
        <h6 class="entry-title"><?php _e( 'Nothing Found', kopa_get_domain() ); ?></h6>
    </li>
<?php } ?>
    </ul>
</div>

<?php get_template_part('library/templates/template', 'pagination'); ?>
