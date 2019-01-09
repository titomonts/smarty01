<?php
function smarty_function_reservations($params, &$smarty)
{
	global $forms, $website, $data;
    
    $villaArray = '';
    if( $params['complex'] == true ):
		$villaArray = array();
		$ivids = explode(',',$params['estate_villa_ids']);
		$svids = sizeof($ivids);
		
		for( $c=0; $c<$svids; $c++ ):
			$vinfo = $website->ws_villa_info($ivids[$c]);
			$villaArray[$c]['id'] = $vinfo['gaData']['property']['id'];
			$villaArray[$c]['villa_name'] = $vinfo['gaData']['property']['name'];	
		endfor;
	endif;
    
	echo $forms->display_reservation_form($data['villa_id'], $data['villa_name'], $villaArray, (isset($_POST) ? $_POST : ""), $data['google_site_key'] );
}
