<?php
######################################################################
# remove backend options by removing them from the config array
######################################################################
add_filter('woocommerce_general_settings','kopa_woocommerce_general_settings_filter');
add_filter('woocommerce_page_settings','kopa_woocommerce_general_settings_filter');
add_filter('woocommerce_catalog_settings','kopa_woocommerce_general_settings_filter');
add_filter('woocommerce_inventory_settings','kopa_woocommerce_general_settings_filter');
add_filter('woocommerce_shipping_settings','kopa_woocommerce_general_settings_filter');
add_filter('woocommerce_tax_settings','kopa_woocommerce_general_settings_filter');

function kopa_woocommerce_general_settings_filter($options)
{  
    $remove   = array('woocommerce_enable_lightbox', 'woocommerce_frontend_css');
    //$remove = array('image_options', 'woocommerce_enable_lightbox', 'woocommerce_catalog_image', 'woocommerce_single_image', 'woocommerce_thumbnail_image', 'woocommerce_frontend_css');

    foreach ($options as $key => $option)
    {
        if( isset($option['id']) && in_array($option['id'], $remove) ) 
        {  
            unset($options[$key]); 
        }
    }

    return $options;
}

//on theme activation set default image size, disable woo lightbox and woo stylesheet. options are already hidden by previous filter function
function kopa_woocommerce_set_defaults()
{
    global $kopa_woocommerce;
    
    update_option('shop_catalog_image_size', $kopa_woocommerce['imgSize']['shop_catalog']);
    update_option('shop_single_image_size', $kopa_woocommerce['imgSize']['shop_single']);
    update_option('shop_thumbnail_image_size', $kopa_woocommerce['imgSize']['shop_thumbnail']);

    //set custom
    
    update_option('kopa_woocommerce_column_count', 3);
    update_option('kopa_woocommerce_product_count', 24);
    
    //set blank
    $set_false = array('woocommerce_enable_lightbox', 'woocommerce_frontend_css');
    foreach ($set_false as $option) { update_option($option, false); }
    
    //set blank
    $set_no = array('woocommerce_single_image_crop');
    foreach ($set_no as $option) { update_option($option, 'no'); }

}

if ( is_admin() && isset( $_GET['activated'] ) && $pagenow == 'themes.php' ) {
    add_action( 'init', 'kopa_woocommerce_set_defaults', 1);
}

//activate the plugin options when this file is included for the first time
add_action('admin_init', 'kopa_woocommerce_first_activation' , 45 );
function kopa_woocommerce_first_activation()
{
    if(!is_admin()) return;
    
    $themeNice = sanitize_title(KOPA_THEME_NAME);

    if(get_option("{$themeNice}_woocommerce_settings_enabled")) return;
    update_option("{$themeNice}_woocommerce_settings_enabled", '1');
    
    kopa_woocommerce_set_defaults();
}

//add new options to the catalog settings
add_filter('woocommerce_general_settings','kopa_woocommerce_page_settings_filter');

function kopa_woocommerce_page_settings_filter($options)
{  

    $options[] = array(
        'name' => 'Column and Product Count',
        'type' => 'title',
        'desc' => 'The following settings allow you to choose how many columns and items should appear on your default shop overview page and your product archive pages.<br/><small>Notice: These options are added by the <strong>'.KOPA_THEME_NAME.' Theme</strong> and wont appear on other themes</small>',
        'id'   => 'column_options'
    );
    
    $options[] = array(
        'name' => 'Column Count',
        'desc' => '',
        'id' => 'kopa_woocommerce_column_count',
        'css' => 'min-width:175px;',
        'std' => '3',
        'desc_tip' => "This controls how many columns should appear on overview pages.",
        'type' => 'select',
        'options' => array
            (
                '2' => '2',
                '3' => '3',
                '4' => '4',
            )
    );
    
    $itemcount = array('-1'=>'All');
    for($i = 3; $i<101; $i++) {
        $itemcount[$i] = $i;  
    } 
    
    $options[] = array(
        'name' => 'Product Count',
        'desc' => "",
        'id' => 'kopa_woocommerce_product_count',
        'css' => 'min-width:175px;',
        'desc_tip' => 'This controls how many products should appear on overview pages.',
        'std' => '24',
        'type' => 'select',
        'options' => $itemcount
    );
    
    $options[] = array(
        'type' => 'sectionend',
        'id' => 'column_options'
    );
    
    return $options;
}