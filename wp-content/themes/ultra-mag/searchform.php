<div class="kp-search-widget">
	<form action="<?php echo esc_url( home_url() ); ?>" class="search-form clearfix" method="get">
		<input type="text" onBlur="if (this.value == '') this.value = this.defaultValue;" onFocus="if (this.value == this.defaultValue) this.value = '';" value="<?php echo get_search_query() ? esc_attr( get_search_query() ) : __( 'Search...', kopa_get_domain() ); ?>" name="s" class="search-text">
		<input type="submit" value="" class="search-submit">
	</form> <!-- search-form -->
</div>