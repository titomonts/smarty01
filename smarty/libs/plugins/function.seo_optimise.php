<?php
function smarty_function_seo_optimise($params, &$smarty)
{
	/* This is for three querystrings */
	$origin = $params['origin'];

	/* This is to recognise the page to which we are going to rewrite */
	if($params['ptype'] == 'villa_detail')
	{
		if($params['urls'] == 'Enable')
			return "villaID/".$origin.".html";
		else 
			return "villaDetails.php?villaID=".$origin;
	}

}

