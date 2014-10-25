<?php

if ( kopa_woocommerce_enabled() ) {
	add_action( 'widgets_init', 'kopa_woocommerce_register_widgets' );

	function kopa_woocommerce_register_widgets() {
		include_once( 'class-kopa-wc-products.php' );

		register_widget( 'Kopa_Widget_Woocommerce_Products' );
	}
}
