<?php
function smarty_function_seo_optimize($params, &$smarty)
{
	$page = $params['page'];
	$child = isset($params['child'])?$params['child']:'';
	$sibling = isset($params['sibling'])?$params['sibling']:'';
	/* Start universal URL processing */
	if($child !=''):
		if($params['urls'] == 'Enable'):
			$theURL = $page."/".$child.".html";
			if($sibling != ''):
				$theURL = $page."/".$child."/".$sibling.".html";
			endif;	
		else:
			$theURL = "index.php?page=".$page."&child=".$child;
			if($sibling != ''):
				$theURL = "index.php?page=".$page."&child=".$child."&sibling=".$sibling;
			endif;
		endif;
	else:
		if($params['urls'] == 'Enable'):
			$theURL = $page.".html";
		else:
			$theURL = "index.php?page=".$page;
		endif;	
	endif;
	/* End universal URL processing */
	
	return $theURL;
}
