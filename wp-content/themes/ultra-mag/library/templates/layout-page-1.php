<?php 
$kopa_setting = kopa_get_template_setting(); 
$sidebars = $kopa_setting['sidebars'];
?>

<?php get_header(); ?>

<div id="primary-col">        
    <div id="center-col">

        <?php kopa_breadcrumb(); ?>

        <?php get_template_part( 'library/templates/loop', 'page' ); ?>

    </div> <!-- center-col -->

	<?php if ( is_active_sidebar( $sidebars[1] ) ) { ?>
    <div id="right-sidebar" class="widget-area-2">
    	<?php dynamic_sidebar( $sidebars[1] ); ?>
    </div> <!-- right-sidebar -->
    <?php } ?>

    <div class="clear"></div>

</div> <!-- primary-col -->

<?php if ( is_active_sidebar( $sidebars[0] ) ) { ?>
<div id="left-sidebar" class="widget-area-1">
	<?php dynamic_sidebar( $sidebars[0] ); ?>
</div> <!-- left-sidebar -->
<?php } ?>

<div class="clear"></div>

<?php get_footer(); ?>