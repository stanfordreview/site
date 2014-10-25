<?php

$kopa_layout = array(
    'front-page' => array(
        'title'      => __( 'Front Page', kopa_get_domain() ),
        'thumbnails' => 'front-page.png',
        'positions'  => array(
            'position_1',
            'position_2',
            'position_3',
            'position_4',
            'position_5',
            'position_6',
            'position_7',
            'position_8',
            'position_9',
            'position_10',
            'position_11',
        ),
    ),
    'blog-2' => array(
        'title'      => __( 'Blog-2', kopa_get_domain() ),
        'thumbnails' => 'blog-2.png',
        'positions'  => array(
            'position_12',
            'position_13',
            'position_7',
            'position_8',
            'position_9',
            'position_10',
            'position_11',
        ),
    ),
    'blog-1' => array(
        'title'      => __( 'Blog 1', kopa_get_domain() ),
        'thumbnails' => 'blog-1.png',
        'positions'  => array(
            'position_12',
            'position_13',
            'position_7',
            'position_8',
            'position_9',
            'position_10',
            'position_11',
        ),
    ),
    'page-1' => array(
        'title'      => __( 'Page 1', kopa_get_domain() ),
        'thumbnails' => 'page-1.png',
        'positions'  => array(
            'position_12',
            'position_13',
            'position_7',
            'position_8',
            'position_9',
            'position_10',
            'position_11',
        ),
    ),
    'page-2' => array(
        'title'      => __( 'Page 2', kopa_get_domain() ),
        'thumbnails' => 'page-2.png',
        'positions'  => array(
            'position_12',
            'position_7',
            'position_8',
            'position_9',
            'position_10',
            'position_11',
        ),
    ),
    'single' => array(
        'title'      => __( 'Single', kopa_get_domain() ),
        'thumbnails' => 'single.png',
        'positions'  => array(
            'position_12',
            'position_13',
            'position_7',
            'position_8',
            'position_9',
            'position_10',
            'position_11',
        ),
    ),
    'error-404' => array(
        'title' => __( '404 Page', kopa_get_domain() ),
        'thumbnails' => 'error-404.png',
        'positions'  => array(
            'position_7',
            'position_8',
            'position_9',
            'position_10',
            'position_11',
        ),
    ),
    'shop' => array(
        'title' => __( 'Shop', kopa_get_domain() ),
        'thumbnails' => 'shop.png',
        'positions' => array(
            'position_12',
            'position_13',
            'position_7',
            'position_8',
            'position_9',
            'position_10',
            'position_11',
        )
    ),
    'single-product' => array(
        'title' => __( 'Single Product', kopa_get_domain() ),
        'thumbnails' => 'single.png',
        'positions' => array(
            'position_12',
            'position_13',
            'position_7',
            'position_8',
            'position_9',
            'position_10',
            'position_11',
        )
    ),
);

$kopa_sidebar_position = array(
    'position_1'  => array( 'title' => __( 'Widget Area 1', kopa_get_domain() ) ),
    'position_2'  => array( 'title' => __( 'Widget Area 2', kopa_get_domain() ) ),
    'position_3'  => array( 'title' => __( 'Widget Area 3', kopa_get_domain() ) ),
    'position_4'  => array( 'title' => __( 'Widget Area 4', kopa_get_domain() ) ),
    'position_5'  => array( 'title' => __( 'Widget Area 5', kopa_get_domain() ) ),
    'position_6'  => array( 'title' => __( 'Widget Area 6', kopa_get_domain() ) ),
    'position_7'  => array( 'title' => __( 'Widget Area 7', kopa_get_domain() ) ),
    'position_8'  => array( 'title' => __( 'Widget Area 8', kopa_get_domain() ) ),
    'position_9'  => array( 'title' => __( 'Widget Area 9', kopa_get_domain() ) ),
    'position_10' => array( 'title' => __( 'Widget Area 10', kopa_get_domain() ) ),
    'position_11' => array( 'title' => __( 'Widget Area 11', kopa_get_domain() ) ),
    'position_12' => array( 'title' => __( 'Widget Area 12', kopa_get_domain() ) ),
    'position_13' => array( 'title' => __( 'Widget Area 13', kopa_get_domain() ) ),
);

$kopa_template_hierarchy = array(
    'home'       => array(
        'title'  => __( 'Home', kopa_get_domain() ),
        'layout' => array('blog-1', 'blog-2')
    ),
    'front-page' => array(
        'title'  => __( 'Front Page', kopa_get_domain() ),
        'layout' => array('front-page', 'page-1', 'page-2')
    ),
    'post'       => array(
        'title'  => __( 'Post', kopa_get_domain() ),
        'layout' => array('single')
    ),
    'page'       => array(
        'title'  => __( 'Page', kopa_get_domain() ),
        'layout' => array('front-page', 'page-1', 'page-2')
    ),
    'taxonomy'   => array(
        'title'  => __( 'Taxonomy', kopa_get_domain() ),
        'layout' => array('blog-1', 'blog-2')
    ),
    'search'     => array(
        'title'  => __( 'Search', kopa_get_domain() ),
        'layout' => array('blog-1', 'blog-2')
    ),
    'archive'    => array(
        'title'  => __( 'Archive', kopa_get_domain() ),
        'layout' => array('blog-1', 'blog-2')
    ),
    'shop' => array(
        'title'  => __( 'Shop', kopa_get_domain() ),
        'layout' => array('shop')
    ),
    'single-product' => array(
        'title'  => __( 'Single Product', kopa_get_domain() ),
        'layout' => array('single-product')
    ),
    '_404'    => array(
        'title'  => __( '404', kopa_get_domain() ),
        'layout' => array('error-404')
    )
);

define('KOPA_INIT_VERSION', 'ultra-mag-setting-version-15');
define('KOPA_LAYOUT', serialize($kopa_layout));
define('KOPA_SIDEBAR_POSITION', serialize($kopa_sidebar_position));
define('KOPA_TEMPLATE_HIERARCHY', serialize($kopa_template_hierarchy));

function kopa_initial_database() {
    $kopa_is_database_setup = get_option('kopa_is_database_setup');
    if ($kopa_is_database_setup !== KOPA_INIT_VERSION) {
        $kopa_setting = array(
            'home' => array(
                'layout_id' => 'blog-1',
                'sidebars'  => array(
                    'sidebar_12',
                    'sidebar_13',
                    'sidebar_7',
                    'sidebar_8',
                    'sidebar_9',
                    'sidebar_10',
                    'sidebar_11',
                )
            ),
            'front-page' => array(
                'layout_id' => 'front-page',
                'sidebars'  => array(
                    'sidebar_1',
                    'sidebar_2',
                    'sidebar_3',
                    'sidebar_4',
                    'sidebar_5',
                    'sidebar_6',
                    'sidebar_7',
                    'sidebar_8',
                    'sidebar_9',
                    'sidebar_10',
                    'sidebar_11',
                )
            ),
            'post' => array(
                'layout_id' => 'single',
                'sidebars'  => array(
                    'sidebar_12',
                    'sidebar_13',
                    'sidebar_7',
                    'sidebar_8',
                    'sidebar_9',
                    'sidebar_10',
                    'sidebar_11',
                )
            ),
            'page' => array(
                'layout_id' => 'page-1',
                'sidebars'  => array(
                    'sidebar_12',
                    'sidebar_13',
                    'sidebar_7',
                    'sidebar_8',
                    'sidebar_9',
                    'sidebar_10',
                    'sidebar_11',
                )
            ),
            'taxonomy' => array(
                'layout_id' => 'blog-1',
                'sidebars'  => array(
                    'sidebar_12',
                    'sidebar_13',
                    'sidebar_7',
                    'sidebar_8',
                    'sidebar_9',
                    'sidebar_10',
                    'sidebar_11',
                )
            ),
            'search' => array(
                'layout_id' => 'blog-1',
                'sidebars'  => array(
                    'sidebar_12',
                    'sidebar_13',
                    'sidebar_7',
                    'sidebar_8',
                    'sidebar_9',
                    'sidebar_10',
                    'sidebar_11',
                )
            ),
            'archive' => array(
                'layout_id' => 'blog-1',
                'sidebars'  => array(
                    'sidebar_12',
                    'sidebar_13',
                    'sidebar_7',
                    'sidebar_8',
                    'sidebar_9',
                    'sidebar_10',
                    'sidebar_11',
                )
            ),
            'shop' => array(
                'layout_id' => 'shop',
                'sidebars'  => array(
                    'sidebar_12',
                    'sidebar_13',
                    'sidebar_7',
                    'sidebar_8',
                    'sidebar_9',
                    'sidebar_10',
                    'sidebar_11',
                ),
            ),
            'single-product' => array(
                'layout_id' => 'single-product',
                'sidebars'  => array(
                    'sidebar_12',
                    'sidebar_13',
                    'sidebar_7',
                    'sidebar_8',
                    'sidebar_9',
                    'sidebar_10',
                    'sidebar_11',
                ),
            ),
            '_404' => array(
                'layout_id' => 'error-404',
                'sidebars'  => array(
                    'sidebar_7',
                    'sidebar_8',
                    'sidebar_9',
                    'sidebar_10',
                    'sidebar_11',
                )
            ),
        );
        $kopa_sidebar = array(
            'sidebar_hide' => __( '-- None --', kopa_get_domain() ),
            'sidebar_1'    => __( 'Sidebar 1', kopa_get_domain() ),
            'sidebar_2'    => __( 'Sidebar 2', kopa_get_domain() ),
            'sidebar_3'    => __( 'Sidebar 3', kopa_get_domain() ),
            'sidebar_4'    => __( 'Sidebar 4', kopa_get_domain() ),
            'sidebar_5'    => __( 'Sidebar 5', kopa_get_domain() ),
            'sidebar_6'    => __( 'Sidebar 6', kopa_get_domain() ),
            'sidebar_7'    => __( 'Sidebar 7', kopa_get_domain() ),
            'sidebar_8'    => __( 'Sidebar 8', kopa_get_domain() ),
            'sidebar_9'    => __( 'Sidebar 9', kopa_get_domain() ),
            'sidebar_10'   => __( 'Sidebar 10', kopa_get_domain() ),
            'sidebar_11'   => __( 'Sidebar 11', kopa_get_domain() ),
            'sidebar_12'   => __( 'Sidebar 12', kopa_get_domain() ),
            'sidebar_13'   => __( 'Sidebar 13', kopa_get_domain() ),
        );
        $kopa_sidebar_description = array(
            'sidebar_hide' => '',
            'sidebar_1'    => __( 'Front page left sidebar', kopa_get_domain() ),
            'sidebar_2'    => __( 'Front page center top content sidebar', kopa_get_domain() ),
            'sidebar_3'    => __( 'Front page first center middle content sidebar', kopa_get_domain() ),
            'sidebar_4'    => __( 'Front page second center middle content sidebar', kopa_get_domain() ),
            'sidebar_5'    => __( 'Front page center bottom content sidebar', kopa_get_domain() ),
            'sidebar_6'    => __( 'Front page right sidebar', kopa_get_domain() ),
            'sidebar_7'    => __( 'Footer column sidebar 1', kopa_get_domain() ),
            'sidebar_8'    => __( 'Footer column sidebar 2', kopa_get_domain() ),
            'sidebar_9'    => __( 'Footer column sidebar 3', kopa_get_domain() ),
            'sidebar_10'   => __( 'Footer column sidebar 4', kopa_get_domain() ),
            'sidebar_11'   => __( 'Footer column sidebar 5', kopa_get_domain() ),
            'sidebar_12'   => __( 'Blog, archive, search, product left sidebar', kopa_get_domain() ),
            'sidebar_13'   =>  __( 'Blog, archive, search, product right sidebar', kopa_get_domain() ),
        );
        update_option('kopa_setting', $kopa_setting);
        update_option('kopa_sidebar', $kopa_sidebar);
        update_option('kopa_sidebar_description', $kopa_sidebar_description);
        update_option('kopa_is_database_setup', KOPA_INIT_VERSION);
    }
}

/* Register widget areas */
add_action( 'widgets_init', 'kopa_sidebars_init' );

function kopa_sidebars_init() {
    $kopa_sidebar = get_option('kopa_sidebar');

    if ( empty( $kopa_sidebar ) || ! is_array( $kopa_sidebar ) ) {
        return;
    }

    $kopa_sidebar_description = get_option('kopa_sidebar_description');

    foreach ($kopa_sidebar as $key => $value) {
        $sidebar_args = array(
            'name'          => $value,
            'id'            => $key,
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h6 class="widget-title">',
            'after_title'   => '</h6>',
        );

        if ( isset( $kopa_sidebar_description[ $key ] ) ) {
            $sidebar_args['description'] = $kopa_sidebar_description[ $key ];
        }

        if ('sidebar_hide' != $key) {
            register_sidebar( $sidebar_args );
        }
    }
}