<?php
function kopa_woocommerce_enabled() {
    if ( class_exists( 'woocommerce' ) ) { return true; }
    return false;
}

// product thumbnails
$kopa_woocommerce['imgSize']['shop_thumbnail'] = array('width'=>120, 'height'=>120);
$kopa_woocommerce['imgSize']['shop_catalog']   = array('width'=>348, 'height'=>522);
$kopa_woocommerce['imgSize']['shop_single']    = array('width'=>450, 'height'=>999, 'crop' => false);

/* Initialize admin settings */
include( get_template_directory() . '/woocommerce/admin-options.php' );

/* Register custom widgets */
include( get_template_directory() . '/woocommerce/widgets/kopa-wc-widgets.php' );

add_theme_support( 'woocommerce' );

// shop config
$kopa_woocommerce['shop_overview_column']  = get_option('kopa_woocommerce_column_count');  // columns for the overview page
$kopa_woocommerce['shop_overview_products']= get_option('kopa_woocommerce_product_count'); // products for the overview page

$kopa_woocommerce['shop_single_column'] = 3;
$kopa_woocommerce['shop_single_column_items'] = 3;

if(!$kopa_woocommerce['shop_overview_column']) {
    $kopa_woocommerce['shop_overview_column'] = 3;
}

//register my own styles, remove wootheme stylesheet
if(!is_admin()){
    add_action('wp_enqueue_scripts', 'kopa_woocommerce_register_assets');
}

function kopa_woocommerce_register_assets() {
    wp_enqueue_style( 'kopa-woocommerce-css', get_template_directory_uri() . '/woocommerce/css/woocommerce.css' );
}

// deregister woocommerce frontend css
// compatible since woocommerce version 2.1
add_action( 'wp_enqueue_scripts', 'kopa_deregister_woocommerce_frontend_css' );

function kopa_deregister_woocommerce_frontend_css() {
    wp_deregister_style('woocommerce-layout');
    wp_deregister_style('woocommerce-smallscreen');
    wp_deregister_style('woocommerce-general');
}

//remove woo defaults
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

/**
 * @hooked kopa_breadcrumb - 20 (in library/front.php)
 */
add_action('woocommerce_before_main_content' , 'kopa_breadcrumb', 20);

//add theme actions && filter
add_filter( 'loop_shop_columns', 'kopa_woocommerce_loop_columns');
add_filter( 'loop_shop_per_page', 'kopa_woocommerce_product_count' );

add_action ('woocommerce_before_main_content', 'kopa_woocommerce_output_content_wrapper', 10);
function kopa_woocommerce_output_content_wrapper() {
    global $kopa_woocommerce;
    $kopa_setting = kopa_get_template_setting();

    ?>
    <div id="primary-col">        
        <div id="center-col">

        <?php if ( ! is_singular() ) { ?>
            <div class="shop_columns_<?php echo $kopa_woocommerce['shop_overview_column']; ?>">
        <?php }
}

add_action ('woocommerce_after_main_content', 'kopa_woocommerce_output_content_wrapper_end', 10);
function kopa_woocommerce_output_content_wrapper_end() {
    $kopa_setting = kopa_get_template_setting();
    $sidebars = $kopa_setting['sidebars'];

    ?>
        <?php if ( ! is_singular() ) { ?>
            </div> <!-- .shop_columns_X -->
        <?php } ?>

        </div> <!-- center-col -->

        <?php if ( is_active_sidebar( $sidebars[1] ) ) { ?>
        <div id="right-sidebar" class="widget-area-2">
            <?php dynamic_sidebar( $sidebars[1] ); ?>
        </div> <!-- right-sidebar -->
        <?php } ?>

        <div class="clear"></div>

    </div>
    <!-- primary-col -->

    <?php if ( is_active_sidebar( $sidebars[0] ) ) { ?>
    <div id="left-sidebar" class="widget-area-1">
        <?php dynamic_sidebar( $sidebars[0] ); ?>
    </div> <!-- left-sidebar -->
    <?php } ?>

    <div class="clear"></div>

    <?php
}

function kopa_woocommerce_loop_columns() {
    global $kopa_woocommerce;
    return $kopa_woocommerce['shop_overview_column'];
}

function kopa_woocommerce_product_count() {
    global $kopa_woocommerce;
    return $kopa_woocommerce['shop_overview_products'];
}


// filter cross sells
add_filter('woocommerce_cross_sells_total', 'kopa_woocommerce_cross_sale_count');
add_filter('woocommerce_cross_sells_columns', 'kopa_woocommerce_cross_sale_count');

function kopa_woocommerce_cross_sale_count($count)
{
    return 2;
}

#
# display upsells and related products within dedicated div with different column and number of products
#
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products',20);
remove_action( 'woocommerce_after_single_product', 'woocommerce_output_related_products',10);
add_action( 'woocommerce_after_single_product_summary', 'kopa_woocommerce_output_related_products', 20);

function kopa_woocommerce_output_related_products()
{
    global $kopa_woocommerce;
    $output = "";

    ob_start();
    woocommerce_related_products($kopa_woocommerce['shop_single_column_items'],$kopa_woocommerce['shop_single_column']); // X products, X columns
    $content = ob_get_clean();
    if($content)
    {
        $output .= "<div class='product_column product_column_".$kopa_woocommerce['shop_single_column']."'>";
        $output .= $content;
        $output .= "</div>";
    }

    echo $output;

}

remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
remove_action( 'woocommerce_after_single_product', 'woocommerce_upsell_display',10);
add_action( 'woocommerce_after_single_product_summary', 'kopa_woocommerce_output_upsells', 21); // needs to be called after the "related product" function to inherit columns and product count

function kopa_woocommerce_output_upsells()
{
    global $kopa_woocommerce;

    $output = "";

    ob_start();
    woocommerce_upsell_display($kopa_woocommerce['shop_single_column_items'],$kopa_woocommerce['shop_single_column']); // 3 products, 3 columns
    $content = ob_get_clean();
    if($content)
    {
        $output .= "<div class='product_column product_column_".$kopa_woocommerce['shop_single_column']."'>";
        $output .= $content;
        $output .= "</div>";
    }

    echo $output;

}

/** 
 * add_to_cart css class
 */
add_filter( 'add_to_cart_class', 'kopa_woocommerce_add_to_cart_class' );

function kopa_woocommerce_add_to_cart_class( $classes ) {
    return $classes .= ' add-to-cart-button';
}