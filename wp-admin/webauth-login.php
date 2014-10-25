<?php
require( '../wp-load.php' );

$sunetid = $_GET['sunetid'];
$hash = $_GET['hash'];
$secretval = 'o wryr487O&*R$&*TYr87g798837hysysd7yd$R*873TRa&*TT^QQ#%5AD%WQD@Qr';

$exphash = md5($secretval . $sunetid);

if ($exphash == $hash) {
	setcookie('sunetid', $sunetid, time()+60*60*24*30, '/');
	setcookie('hash', $hash, time()+60*60*24*30, '/');

	$userdata = get_userdatabylogin($sunetid);

	if ($userdata) {
		wp_set_auth_cookie($userdata->ID, true);
	}
}

wp_redirect("/wp-admin/");

?>
