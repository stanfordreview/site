<?php 
$kopa_setting = kopa_get_template_setting(); 
$sidebars = $kopa_setting['sidebars'];
?>

<?php get_header(); ?>

<div id="primary-col" class="section-page-primary-col">        
    <div id="center-col">

        <?php kopa_breadcrumb(); ?>

        <?php get_template_part( 'library/templates/loop', 'blog-1' ); ?>

    </div> <!-- layout-blog-1 enter-col -->

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

<?php get_footer(); ?>
