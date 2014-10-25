<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">                   
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title( '|', true, 'right' ); ?></title>     
    <link rel="profile" href="http://gmpg.org/xfn/11">           
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
    
    <?php if ( get_option('kopa_theme_options_favicon_url') ) { ?>       
        <link rel="shortcut icon" type="image/x-icon"  href="<?php echo get_option('kopa_theme_options_favicon_url'); ?>">
    <?php } ?>
    
    <?php if ( get_option('kopa_theme_options_apple_iphone_icon_url') ) { ?>
        <link rel="apple-touch-icon" sizes="57x57" href="<?php echo get_option('kopa_theme_options_apple_iphone_icon_url'); ?>">
    <?php } ?>

    <?php if ( get_option('kopa_theme_options_apple_ipad_icon_url') ) { ?>
    <link rel="apple-touch-icon" sizes="72x72" href="<?php echo get_option('kopa_theme_options_apple_ipad_icon_url'); ?>">
    <?php } ?>

    <?php if ( get_option('kopa_theme_options_apple_iphone_retina_icon_url') ) { ?>
    <link rel="apple-touch-icon" sizes="114x114" href="<?php echo get_option('kopa_theme_options_apple_iphone_retina_icon_url'); ?>">
    <?php } ?>

    <?php if ( get_option('kopa_theme_options_apple_ipad_retina_icon_url') ) { ?>
        <link rel="apple-touch-icon" sizes="144x144" href="<?php echo get_option('kopa_theme_options_apple_ipad_retina_icon_url'); ?>">        
    <?php } ?>
    
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <div class="wrapper">
        <div id="kp-page-header" class="clearfix">
            <?php if ( has_nav_menu( 'top-nav' ) ) { ?>
                <div class="top-bar clearfix">
                    <?php wp_nav_menu(array(
                        'theme_location'  => 'top-nav',
                        'container'       => 'nav',
                        'container_id'    => 'top-nav',
                        'container_class' => 'clearfix',
                        'depth'           => -1,
                    )); ?>
                </div><!--top-bar-->
            <?php } // endif has_nav_menu ?>

            <div class="clear"></div>
            <div id="logo-image">
                <?php $logo_image = get_option( 'kopa_theme_options_logo_url' );

                if ( $logo_image ) { ?>
                <a href="<?php echo esc_url( home_url() ); ?>"><img src="<?php echo esc_url( $logo_image ); ?>" alt="<?php bloginfo('name'); ?>"></a>
                <?php } else { ?>
                <h1 class="site-title"><a href="<?php echo esc_url( home_url() ); ?>"><?php bloginfo('name'); ?></a></h1>
                <?php } ?>
            </div>
            <div class="menu-bar clearfix">
                <nav id="main-nav" class="clearfix">
                    <?php if ( has_nav_menu( 'main-nav' ) ) {
                        wp_nav_menu( array(
                            'theme_location' => 'main-nav',
                            'container'      => '',
                            'menu_id'        => 'main-menu',
                            'menu_class'     => 'clearfix',
                        ) );

                        $mobile_menu_walker = new kopa_mobile_menu();
                        wp_nav_menu( array(
                            'theme_location' => 'main-nav',
                            'container_id'   => 'mobile-menu',
                            'menu_id'        => 'toggle-view-menu',
                            'walker'         => $mobile_menu_walker,
                            'items_wrap'     => '<span>'.__( 'Menu', kopa_get_domain() ).'</span><ul id="%1$s" class="%2$s">%3$s</ul>'
                        ) );
                    } ?>
                </nav>
                <!-- main-nav -->
            </div><!--menu-bar-->
        </div>
        <!-- kp-page-header -->

        <div id="main-content" class="clearfix">