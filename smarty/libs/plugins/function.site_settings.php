<?php
function smarty_function_site_settings($params, &$smarty)
{
	global $mysqli, $website;
	$mysqli = $website->connect();
	/* Get universal settings */
	$uqry = $mysqli->query("SELECT * FROM universal_settings WHERE set_name = '".$params['what']."'") or die($mysqli->error);
	$urow = $uqry->fetch_assoc();
	$smarty->assign('set',$urow);
	/* End get universal settings */
}