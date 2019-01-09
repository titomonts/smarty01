<?php
if( empty($_POST) ):
	die('Unauthorized file access');
else:
	$div = $_POST['d'];
	$y = $_POST['y'];
	$mo = $_POST['m'];
	if($y == date('Y'))
		$eq = date('n');
	else
		$eq = 1;
	
	$opts = '';
	for($m=$eq; $m<=12; $m++):
		if($m == $mo):
			$opts .= '<option value="'.$m.'" selected="selected">'.date('M',mktime(0,0,0,$mo,1,$year)).'</option>';
		else:
			$opts .= '<option value="'.$m.'">'.date('M',mktime(0,0,0,$m,1,$year)).'</option>';
		endif;
	endfor;
	echo $opts;
endif;
