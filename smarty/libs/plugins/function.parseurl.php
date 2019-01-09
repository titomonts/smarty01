<?php
function smarty_function_parseurl($params, &$smarty)
{
	/* Parse web reference */
	error_reporting(E_ALL ^ E_NOTICE);
	$theLinks = new DOMDocument(); 
	$theLinks->load( 'cache/webRef.xml' ) or die("Can't load XML Document");
	$links = $theLinks->getElementsByTagName( "WEBURL" );
	foreach ( $links as $link ):	 
		$vname = $link->getElementsByTagName( "villa_url" )->item(0)->nodeValue;
		if($vname == $params['villa']):
			if(!empty($link->getElementsByTagName( "uri" )->item(0)->nodeValue))
				return $link->getElementsByTagName( "uri" )->item(0)->nodeValue;
			else
				return $params['orig_url'];	
		endif;
	endforeach;
	/* End parse web reference */
}

/*  */