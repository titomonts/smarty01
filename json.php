<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
error_reporting(E_ERROR);
if( empty($_POST) ):
	die('Unauthorized file access!');
else:
	$xpath = realpath(dirname(__FILE__)).'/calendar/';
	if(!is_dir($xpath))
		mkdir($xpath,0777);

	$vid = $_POST['vid'];
	
	$year = isset($_POST['y'])?$_POST['y']:date('o');
	$month = isset($_POST['m'])?date('n',mktime(0,0,0,$_POST['m'],1,$year)):date('n');
	$sdate = $year.'-'.$month.'-'.'01';
	$edate = date('Y-m-d',strtotime('+1460 day', strtotime($sdate)));
	
	$params = array("VillaID" => $vid, "SDate" => $sdate, "EDate" => $edate);
	$p_Params = json_encode($params);
	$p_UserID = 'villaprtl';
	
	$crequest = 'p_UserID=villaprtl&p_Params='.$p_Params;
	$cal = cheeze_curls('getPropertyCalendar', $crequest, TRUE, TRUE, $vid."_calendar_data", $xpath, 'prod');
	
	$theVillaData = new DOMDocument();
	$theVillaData->load($xpath.$vid.'_calendar_data.xml.cache');
	
	/* Updated Version */
	$dates = $theVillaData->getElementsByTagName('Dates');
	
	$available_dates = array();$a = 0;
	$onrequest_dates = array();$r = 0;
	$booked_dates = array();$b = 0;
	$hold_dates = array();$h = 0;
	$booked_eci = array();$b_eci_count = 0;
	$booked_lco = array();$b_lco_count = 0; /* If "N", whole color, else half color */
	$hold_eci = array();$h_eci_count = 0;
	$hold_lco = array();$h_lco_count = 0;
	
	foreach ($dates as $date):
		if( $date->getAttribute('status') == 'BOOKED'):
			$start = strtotime($date->getElementsByTagName( 'From' )->item(0)->nodeValue);
			$end = strtotime($date->getElementsByTagName( 'To' )->item(0)->nodeValue);
			while($start <= $end):
				$booked_dates[$b] = date('j F Y', $start);
				$start = strtotime('+1 day', $start);
				$b++;
			endwhile;
			$froms = $date->getElementsByTagName( 'From' );
			foreach( $froms as $from ):
				if( $from->getAttribute('eci') == 'Y' ):
					$booked_eci[$b_eci_count] = date('j F Y', strtotime($date->getElementsByTagName( 'From' )->item(0)->nodeValue));
					$b_eci_count++;
				endif;
			endforeach;
			
			$tos = $date->getElementsByTagName( 'To' );
			foreach( $tos as $to ):
				if( $to->getAttribute('lco') == 'Y' ):
					$booked_lco[$b_lco_count] = date('j F Y', strtotime($date->getElementsByTagName( 'To' )->item(0)->nodeValue));
					$b_lco_count++;
				endif;
			endforeach;
			unset($froms,$from,$tos,$to);
		endif;
		
		if( $date->getAttribute('status') == 'HOLD'):
			$start = strtotime($date->getElementsByTagName( 'From' )->item(0)->nodeValue);
			$end = strtotime($date->getElementsByTagName( 'To' )->item(0)->nodeValue);
			while($start <= $end):
				$hold_dates[$h] = date('j F Y', $start);
				$start = strtotime('+1 day', $start);
				$h++;
			endwhile;
			$froms = $date->getElementsByTagName( 'From' );
			foreach( $froms as $from ):
				if( $from->getAttribute('eci') == 'Y' ):
					$hold_eci[$h_eci_count] = date('j F Y', strtotime($date->getElementsByTagName( 'From' )->item(0)->nodeValue));
					$h_eci_count++;
				endif;
			endforeach;
			
			$tos = $date->getElementsByTagName( 'To' );
			foreach( $tos as $to ):
				if( $to->getAttribute('lco') == 'Y' ):
					$hold_lco[$h_lco_count] = date('j F Y', strtotime($date->getElementsByTagName( 'To' )->item(0)->nodeValue));
					$h_lco_count++;
				endif;
			endforeach;
			unset($froms,$from,$tos,$to);
		endif;
		
		if( $date->getAttribute('status') == 'ONREQUEST'):
			$start = strtotime($date->getElementsByTagName( 'From' )->item(0)->nodeValue);
			$end = strtotime($date->getElementsByTagName( 'To' )->item(0)->nodeValue);
			while($start <= $end):
				$onrequest_dates[$r] = date('j F Y', $start);
				$start = strtotime('+1 day', $start);
				$r++;
			endwhile;
		endif;
		
		if( $date->getAttribute('status') == 'AVAILABLE'):
			$start = strtotime($date->getElementsByTagName( 'From' )->item(0)->nodeValue);
			$end = strtotime($date->getElementsByTagName( 'To' )->item(0)->nodeValue);
			while($start <= $end):
				$available_dates[$a] = date('j F Y', $start);
				$start = strtotime('+1 day', $start);
				$a++;
			endwhile;
		endif;
	endforeach;
	/* End Updated Version */
	
	$calendar = '<table width="60%" cellpadding="0" cellspacing="0" class="cal">';
	$calendar .= "<tr><td><table class='cal' cellpadding='0' cellspacing='0'><tr><td colspan='7' class='mh'>".date('F Y',mktime(0,0,0,$month,1,$year))."</td></tr>";
	/* table headings */
	$headings = array('Su','Mo','Tu','We','Th','Fr','Sa');
	$calendar.= '<tr><td class="mdh">'.implode('</td><td class="mdh">',$headings).'</td></tr>';

	/* days and weeks vars now ... */
	$running_day = date('w',mktime(0,0,0,$month,1,$year));
	$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
	$days_in_this_week = 1;
	$day_counter = 0;
	$dates_array = array();

	/* row for week one */
	$calendar.= '<tr>';

	/* print "blank" days until the first of the current week */
	for($x = 0; $x < $running_day; $x++):
		$calendar.= '<td class="iad"> </td>';
		$days_in_this_week++;
	endfor;

	$num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year); 

	/* keep going with days.... */
	for($list_day = 1; $list_day <= $days_in_month; $list_day++):
		$cell_class = 'ad';
		$yd = $list_day>1?$list_day-1:1;
		$yesterday = date( 'j F Y', mktime(0,0,0, $month, $yd, $year) );
		
		$from_unix_time = mktime(0, 0, 0, $month, $list_day, $year);
		$day_before = strtotime("yesterday", $from_unix_time);
		$formatted = date('j F Y', $day_before);
		
		if( $list_day == 1 ):
			$result = previous_month( $data['villa_id'], $yesterday );
			if( $result['where'] = 'available' ):
				end($available_dates);
				$lastId = key($available_dates);
				$available_dates[$lastId+1] = $result['date'];
			endif;
			
			if( $result['where'] == 'booked' ):
				end($booked_dates);
				$lastId = key($booked_dates);
				$booked_dates[$lastId+1] = $result['date'];
			endif;
			
			if( $result['where'] == 'hold' ):
				end($hold_dates);
				$lastId = key($hold_dates);
				$hold_dates[$lastId+1] = $result['date'];
			endif;
			
			if( $result['where'] == 'onrequest' ):
				end($onrequest_dates);
				$lastId = key($onrequest_dates);
				$onrequest_dates[$lastId+1] = $result['date'];
			endif;
		endif;
		$now = date( 'j F Y', mktime(0,0,0,$month,$list_day,$year) );
		$tm = $list_day<$num_days?(int)$list_day+1:$num_days;
		$tomorrow = date( 'j F Y', mktime(0,0,0,$month,$tm,$year) );
		
		if( in_array( $now, $booked_dates ) ):
			$cell_class = 'ud';
			if( !in_array($yesterday,$booked_dates) || in_array($yesterday, $booked_lco) ):
				if( in_array($now,$booked_lco) || in_array($yesterday,$available_dates) || !in_array($now,$booked_eci) ):
					$cell_class = 'ad-ud';
				endif;
				
				if( in_array($yesterday,$hold_dates) || in_array($yesterday,$onrequest_dates) ):
					$cell_class = 'ord-ud';
					if( in_array( $now, $available_dates ) ):
						$cell_class = 'ad';
					endif;
				endif;
			endif;
			if( in_array($now,$booked_eci) ):
				$cell_class = 'ud';
			endif;
		elseif( in_array($yesterday,$booked_dates) ):
			$cell_class = 'ud-ad'; 
			if( in_array($yesterday,$booked_lco) ):
				$cell_class = 'ad';	
			endif;
			/*
			if( in_array($tomorrow,$hold_dates) ):
				$cell_class = 'ad-ud';
			endif;
			*/
		endif;
		
		if( in_array( $now, $hold_dates ) ):
			$cell_class = 'ord';
			if( in_array($now,$hold_eci) ):
				$cell_class = 'ord';
			endif;
			if( !in_array($yesterday,$hold_dates) ):
				if( !in_array($now,$hold_eci) ):
					$cell_class = 'ad-ord';
				endif;
				if( in_array($yesterday, $hold_lco) ):
					$cell_class = 'ad';
				endif;
			endif;
			
			if( in_array($yesterday, $booked_dates) && !in_array($yesterday, $booked_lco) ):
				$cell_class = 'ud-ord';
			endif;
		elseif( in_array($yesterday,$hold_dates) ):
			$cell_class = 'ord-ad';
			if( in_array($yesterday,$hold_lco) ):
				$cell_class = 'ad';
			endif;
			if( in_array($now,$hold_eci) && !in_array($yesterday, $booked_lco) ):
				$cell_class = 'ad-ord';
			endif;
			/**/
			if( in_array($tomorrow,$booked_dates) && !in_array($tomorrow, $booked_eci) ):
				$cell_class = 'ord-ud';
				if( (in_array($yesterday,$hold_dates) || in_array($yesterday,$onrequest_dates)) && !in_array($now,$booked_dates) ):
					$cell_class = 'ord-ad';
				endif;
				if( (in_array($yesterday,$hold_dates) || in_array($yesterday,$onrequest_dates)) && !in_array($tomorrow,$booked_dates) ):
					$cell_class = 'ord-ad';
				endif;
			endif;
		endif;	
		
		if( in_array( $now, $onrequest_dates ) ):
			$cell_class = 'ord';
			if( in_array($yesterday, $booked_dates) && !in_array($yesterday, $booked_lco) ):
				$cell_class = 'ud-ord';
			endif;
		endif;
		
		$calendar.= '<td class="'.$cell_class.'">'.$list_day.'</td>';
		if($running_day == 6):
			$calendar.= '</tr>';
			if(($day_counter+1) != $days_in_month):
				$calendar.= '<tr>';
			endif;
			$running_day = -1;
			$days_in_this_week = 0;
		endif;
		$days_in_this_week++; 
		$running_day++; 
		$day_counter++;
		$cell_class = '';
	endfor;

	/* finish the rest of the days in the week */
	if($days_in_this_week < 8):
		for($x = 1; $x <= (8 - $days_in_this_week); $x++):
			$calendar.= '<td class="iad"> </td>';
		endfor;
	endif;

	/* final row */
	$calendar.= '</tr>';

	/* end the table */
	$calendar.= '</table>
		</td>
		</tr>
		</table>';
	echo $calendar;
endif;

function previous_month( $vid, $yesterday )
{	
	$xpath = realpath(dirname(__FILE__)).'/calendar/tmp/';
	if(!is_dir($xpath))
		mkdir($xpath,0777);

	/* Get Current Month and Year */
	$month  = date('m',strtotime($yesterday));
	$year = date('o', strtotime($yesterday));
	$sdate = $year.'-'.$month.'-'.'01';
	$edate = date('Y-m-d',strtotime('+1460 day', strtotime($sdate)));
	/* End Get Current Month and Year */
	
	/* Let's build the params JSON string */
	$params = array("VillaID" => $vid, "SDate" => $sdate, "EDate" => $edate);
	$p_Params = json_encode($params);
	$p_UserID = 'villaprtl';
	/* End params JSON string */
	
	$crequest = 'p_UserID=villaprtl&p_Params='.$p_Params;
	$cal = cheeze_curls('getPropertyCalendar', $crequest, TRUE, TRUE, $vid."_calendar_data", $xpath, 'prod');

	$theVillaData = new DOMDocument();
	$theVillaData->load($xpath.$vid.'_calendar_data.xml.cache');
	
	$dates = $theVillaData->getElementsByTagName('Dates');
	
	$available_dates = array();
	$a = 0;
	$booked_dates = array();
	$b = 0;
	$hold_dates = array();
	$h = 0;
	$onrequest_dates = array();
	$r = 0;
	
	foreach ($dates as $date):
		if( $date->getAttribute('status') == 'AVAILABLE'):
			$start = strtotime($date->getElementsByTagName( 'From' )->item(0)->nodeValue);
			$end = strtotime($date->getElementsByTagName( 'To' )->item(0)->nodeValue);
			while($start <= $end):
				$available_dates[$b] = date('j F Y', $start);
				$start = strtotime('+1 day', $start);
				$a++;
			endwhile;
		endif;
		
		if( $date->getAttribute('status') == 'BOOKED'):
			$start = strtotime($date->getElementsByTagName( 'From' )->item(0)->nodeValue);
			$end = strtotime($date->getElementsByTagName( 'To' )->item(0)->nodeValue);
			while($start <= $end):
				$booked_dates[$b] = date('j F Y', $start);
				$start = strtotime('+1 day', $start);
				$b++;
			endwhile;
		endif;
		
		if( $date->getAttribute('status') == 'HOLD'):
			$start = strtotime($date->getElementsByTagName( 'From' )->item(0)->nodeValue);
			$end = strtotime($date->getElementsByTagName( 'To' )->item(0)->nodeValue);
			while($start <= $end):
				$hold_dates[$h] = date('j F Y', $start);
				$start = strtotime('+1 day', $start);
				$h++;
			endwhile;
		endif;
		
		if( $date->getAttribute('status') == 'ONREQUEST'):
			$start = strtotime($date->getElementsByTagName( 'From' )->item(0)->nodeValue);
			$end = strtotime($date->getElementsByTagName( 'To' )->item(0)->nodeValue);
			while($start <= $end):
				$onrequest_dates[$r] = date('j F Y', $start);
				$start = strtotime('+1 day', $start);
				$r++;
			endwhile;
		endif;
	endforeach;
	
	$result = array();
	if( in_array( $yesterday, $available_dates) ):
		$result['where'] = 'available';
		$result['date'] = $yesterday;
	elseif( in_array( $yesterday, $booked_dates ) ):
		$result['where'] = 'booked';
		$result['date'] = $yesterday;
	elseif( in_array( $yesterday, $hold_dates ) ):
		$result['where'] = 'hold';
		$result['date'] = $yesterday;
	elseif( in_array( $yesterday, $onrequest_dates ) ):
		$result['where'] = 'onrequest';
		$result['date'] = $yesterday;		
	endif;
	
	unlink($xpath.$vid.'_calendar_data.xml.cache');
	return $result;
}

function cheeze_curls($op, $arr="", $convToArray, $cacheThis, $switch="", $path="",$db)
{
	$ch = curl_init();
	switch($db):
		case 'prod':
			curl_setopt($ch, CURLOPT_URL, 'http://ws.marketingvillas.com/portalapi.asmx/'.$op);
		break;
		
		case 'tryme':
			curl_setopt($ch, CURLOPT_URL, 'http://tryme-ws.marketingvillas.com/portalapi.asmx/'.$op);
		break;
		
		case 'uat':
			curl_setopt($ch, CURLOPT_URL, 'http://uat-ws.marketingvillas.com/portalapi.asmx/'.$op);
		break;
		
	endswitch;
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	if($arr != ""):
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
	endif;
	
	$data = curl_exec($ch);
	
	curl_close($ch);
	
	if( $cacheThis ):	
		$cacheName = $path.$switch.'.xml.cache';
		file_put_contents($cacheName, $data);
		chmod($cacheName,0777);
	endif;
	
	if($convToArray):
		return json_decode(json_encode((array) simplexml_load_string($data)),1);
	else:
		return $data;
	endif;
}