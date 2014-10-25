<form method="get" id="searchform" action="<?php bloginfo('url'); ?>/">
 
	<input type="text" onblur="if (this.value == '') {this.value = 'Search all articles...';}" onfocus="if (this.value == 'Search all articles...') {this.value = '';}" value="Search all articles..." name="s" id="s" />
	<input type="submit" id="searchsubmit" value="Search" />
</form>
