<?php 
$kopa_setting = kopa_get_template_setting(); 
$sidebars = $kopa_setting['sidebars'];
?>

<?php get_header(); ?>

<div id="main-col" class="pull-left">
    <?php kopa_breadcrumb(); ?>
    
    <?php get_template_part( 'library/templates/loop', 'blog-1' ); ?>

    <?php if ( is_active_sidebar( $sidebars[1] ) ) { ?>
    <div class="widget-area-4">
        <?php dynamic_sidebar( $sidebars[1] ); ?>
    </div>
    <!-- widget-area-4 -->
    <?php } ?>
</div>
<!-- main-col -->

<?php if ( is_active_sidebar( $sidebars[0] ) ) { ?>
<div id="sidebar" class="pull-left">
    <?php dynamic_sidebar( $sidebars[0] ); ?>
</div>
<!-- sidebar -->
<?php } ?>

<?php get_footer(); ?>