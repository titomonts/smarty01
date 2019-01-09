<?php
function smarty_function_rates($params, &$smarty)
{
	global $rates;
	
	if(!isset($params['estate'])):
		$data['id'] = $params['vid'];
		$data['villa_theme'] = $params['folder'];
		$content = $rates->get_villa_rates($data);
	else:
		$data['villa_id'] = $params['vid'];
		$data['estate_id'] = $params['estate'];
		$data['villa_theme'] = $params['folder'];
		$content = $rates->estate_rates($data);
	endif;    
    
	echo $content;
}