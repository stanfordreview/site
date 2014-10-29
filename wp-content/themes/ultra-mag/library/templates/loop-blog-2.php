<?php 
$curauth = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));
?>

<?php if (is_author()) : //BEGIN CUSTOM AUTHOR TEMPLATE BY JOHN LUTTIG ?>

<div class="author-page">
	    <div class="about-author clearfix">
            <header class="clearfix">
                <h1><?php the_author_posts_link(); ?></h1>
                <br/>
		<a class="author-article" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php printf( _n( 'Has %s Article', 'Has %s Articles', get_the_author_posts(), kopa_get_domain() ), get_the_author_posts() ); ?></a>
            </header>
            <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>" class="avatar-thumb"><?php echo get_avatar( get_the_author_meta( 'ID' ), 200 ); ?></a>
            <div class="author-content">
                <p><?php the_author_meta( 'description' ); ?></p>
            </div><!--author-content-->
        </div>
</div>
<?php endif; //END CUSTOM AUTHOR TEMPLATE BY JOHN LUTTIG ?>

<div class="article-list-box">
    <h1 style="text-align:center;">Articles</h1><br/>
    <ul class="article-list clearfix">
<?php if ( have_posts() ) { ?>   
    <?php while ( have_posts() ) {
        the_post(); 

        $post_format_class = 'entry-item';

        if ( 'audio' === get_post_format() || 'video' === get_post_format() ) {
            $post_format_class .= ' ' . get_post_format() . '-post';
        } else {
            $post_format_class .= ' ' . 'standard-post';
        }

        ?>

        <li id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <article class="<?php echo $post_format_class; ?>">
                <?php if ( has_post_thumbnail() ) { ?>
                <div class="entry-thumb hover-effect">
                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'kopa-article-list-size' ); ?></a>
                    <a href="<?php the_permalink(); ?>" class="hover-icon"></a>
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
