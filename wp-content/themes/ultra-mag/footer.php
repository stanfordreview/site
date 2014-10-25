<?php
$kopa_setting = kopa_get_template_setting(); 
$sidebars = $kopa_setting['sidebars'];
$total = count( $sidebars );
$footer_sidebar[0] = ($kopa_setting) ? $sidebars[$total - 5] : 'sidebar_7';
$footer_sidebar[1] = ($kopa_setting) ? $sidebars[$total - 4] : 'sidebar_8';
$footer_sidebar[2] = ($kopa_setting) ? $sidebars[$total - 3] : 'sidebar_9';
$footer_sidebar[3] = ($kopa_setting) ? $sidebars[$total - 2] : 'sidebar_10';
$footer_sidebar[4] = ($kopa_setting) ? $sidebars[$total - 1] : 'sidebar_11';

// get options
$kopa_theme_options_copyright = get_option( 'kopa_theme_options_copyright', sprintf( __( 'Copyright %1$s - Kopasoft. All Rights Reserved.', kopa_get_domain() ), date('Y') ) );
$kopa_theme_options_copyright = htmlspecialchars_decode( stripslashes( $kopa_theme_options_copyright ) );
$kopa_theme_options_copyright = wpautop( $kopa_theme_options_copyright );
?>

</div>
<!-- main-content -->

<div id="bottom-sidebar">
    <nav id="bottom-nav" class="text-center">
        <?php if ( has_nav_menu( 'bottom-nav' ) ) { 
            wp_nav_menu( array(
                'theme_location' => 'bottom-nav',
                'container'      => '',
                'menu_id'        => 'bottom-menu',
                'menu_class'     => 'clearfix',
                'depth'          => -1,
            ) ); 
        } // endif ?>
    </nav> <!-- bottom-nav -->

    <div class="row">
        <div class="col-md-8 col-sm-8">
            <div class="row">
                <?php if ( is_active_sidebar( $footer_sidebar[0] ) ) { ?>
                <div class="col-md-3 col-sm-3">
                    <?php dynamic_sidebar( $footer_sidebar[0] ); ?>
                </div> <!-- col-md-3 -->
                <?php } ?>

                <?php if ( is_active_sidebar( $footer_sidebar[1] ) ) { ?>
                <div class="col-md-3 col-sm-3">
                    <?php dynamic_sidebar( $footer_sidebar[1] ); ?>
                </div> <!-- col-md-3 -->
                <?php } ?>
                
                <?php if ( is_active_sidebar( $footer_sidebar[2] ) ) { ?>
                <div class="col-md-3 col-sm-3">
                    <?php dynamic_sidebar( $footer_sidebar[2] ); ?>
                </div> <!-- col-md-3 -->
                <?php } ?>

                <?php if ( is_active_sidebar( $footer_sidebar[3] ) ) { ?>
                <div class="col-md-3 col-sm-3">
                    <?php dynamic_sidebar( $footer_sidebar[3] ); ?>
                </div> <!-- col-md-3 -->
                <?php } ?>
            </div>
            <!-- row -->
        </div>
        <!-- col-md-8 -->
        
        <?php if ( is_active_sidebar( $footer_sidebar[4] ) ) { ?>
        <div class="col-md-4 col-sm-4">
            <?php dynamic_sidebar( $footer_sidebar[4] ); ?>
        </div> <!-- col-md-4 -->
        <?php } ?>
    </div>
    <!-- row -->

    <p id="back-top">
        <a href="#top"><?php _e( 'Back to Top', kopa_get_domain() ); ?></a>
    </p>

</div>
<!-- bottom-sidebar -->

<footer id="kp-page-footer" class="text-center">
    <div id="copyright"><?php echo $kopa_theme_options_copyright; ?></div>
</footer>
<!-- kp-page-footer -->
</div>
<!-- wrapper -->

<?php wp_footer(); ?>

</body>

</html>
