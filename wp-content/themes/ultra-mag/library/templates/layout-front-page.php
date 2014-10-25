<?php 
$kopa_setting = kopa_get_template_setting();
$sidebars = $kopa_setting['sidebars'];
?>

<?php get_header(); ?>

<?php if ( is_active_sidebar( $sidebars[1] ) || is_active_sidebar( $sidebars[2] ) || is_active_sidebar( $sidebars[3] ) || is_active_sidebar( $sidebars[4] ) || is_active_sidebar( $sidebars[5] ) ) { ?>
<div id="primary-col">        
    <div id="center-col">
        <?php if ( is_active_sidebar( $sidebars[1] ) ) { ?>
        <div class="widget-area-3">
            <?php dynamic_sidebar( $sidebars[1] ); ?>
        </div> <!-- widget-area-3 -->
        <?php } ?>

        <?php if ( is_active_sidebar( $sidebars[2] ) ) { ?>
        <div class="widget-area-4">
            <?php dynamic_sidebar( $sidebars[2] ); ?>
        </div> <!-- widget-area-4 -->
        <?php } ?>

        <?php if ( is_active_sidebar( $sidebars[3] ) ) { ?>
        <div class="widget-area-5">
            <?php dynamic_sidebar( $sidebars[3] ); ?>
        </div> <!-- widget-area-5 -->
        <?php } ?>

        <?php if ( is_active_sidebar( $sidebars[4] ) ) { ?>
        <div class="widget-area-6">
            <?php dynamic_sidebar( $sidebars[4] ); ?>
        </div> <!-- widget-area-6 -->
        <?php } ?>

    </div> <!-- center-col -->

    <?php if ( is_active_sidebar( $sidebars[5] ) ) { ?>
    <div id="right-sidebar" class="widget-area-2">
        <?php dynamic_sidebar( $sidebars[5] ); ?>
    </div> <!-- right-sidebar -->
    <?php } ?>

    <div class="clear"></div>

</div> <!-- primary-col -->
<?php } ?>

<?php if ( is_active_sidebar( $sidebars[0] ) ) { ?>
<div id="left-sidebar" class="widget-area-1">
    <?php dynamic_sidebar( $sidebars[0] ); ?>
</div> <!-- left-sidebar -->
<?php } ?>

<div class="clear"></div>

<?php get_footer(); ?>