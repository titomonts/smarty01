<?php
function smarty_function_mobile_calendar( $data, &$smarty )
{
	/* Get Current Month and Year */
	$month = date('n');
	$year = date('o');
	/* End Get Current Month and Year */
	
	/* Let's build the display */
	$vars['monthOpts'] = "";
	$vars['yearOpts'] = "";
	if($year == date('Y'))
		$eq = date('n');
	else
		$eq = 1;

	for($m=$eq; $m<=12; $m++):
		if($m == $month):
			$vars['monthOpts'] .= '<option value="'.$m.'" selected="selected">'.date('M',mktime(0,0,0,$month,1,$year)).'</option>';
		else:
			$vars['monthOpts'] .= '<option value="'.$m.'">'.date('M',mktime(0,0,0,$m,1,$year)).'</option>';
		endif;
	endfor;
	$now = date('Y');

	for($y=$now; $y<=($year+2); $y++):
		if($y == $year):
			$vars['yearOpts'] .='<option value="'.$y.'" selected="selected">'.$y.'</option>';
		else:
			$vars['yearOpts'] .='<option value="'.$y.'">'.$y.'</option>';
		endif;
	endfor;
	$div = str_replace('-','',strtolower($data['villa_id']));
	$calendar = '
	<style>
.cal,.mh,.mdh,.ml,.ad,.ud,.iad,.ud,.ad,.pd,.ord,.ad-ud,.ad-ord,.ord-ad,.ord-ud,.ud-ad,.ud-ord {font-family:Arial,Verdana; font-size:11px;}
.cal {border-left:solid 1px #fff; border-top:solid 1px #fff;}
.mh {border-right:solid 1px #fff; border-bottom:solid 1px #fff; text-align:center; height:22px; font-weight:bold;}
.mdh,.ml,.ad,.ud,.iad,.ord,.ad-ud,.ad-ord,.ord-ad,.ord-ud,.ud-ad,.ud-ord {border-right:solid 1px #fff; border-bottom:solid 1px #fff; width:25px; height:25px; text-align:center;}
.mdh {color:#000; background-color:#ffe28b;}
.ml {width:48px; text-align:left;}
.ad,.ord,.ad-ud,.ad-ord,.ord-ad,.ord-ud,.ud-ad,.ud-ord {cursor:hand; cursor:pointer;}
.ad {background-color:#d5ff8b;}
.ud {background-color:#ff7070;}
.ord {background-color:#cbf4fd;}
td, th {display: table-cell;vertical-align: inherit;}
form.select, input.submit{font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;}
.ad-ud {background-image:url("/resources/common/images/ad-ud.png") !important;}
.ad-ord {background-image:url("/resources/common/images/ad-ord.png") !important;}
.ord-ad {background-image:url("/resources/common/images/ord-ad.png") !important;}
.ord-ud {background-image:url("/resources/common/images/ord-ud.png") !important;}
.ud-ad {background-image:url("/resources/common/images/ud-ad.png") !important;}
.ud-ord {background-image:url("/resources/common/images/ud-ord.png") !important;}
span.loader{font-family:inherit;}
	</style>			
	<div align="center">
	<div align="center" style="width:60%;">
		<form id="frmCal" name="frmCal" action="" method="">
			<input type="hidden" id="hidVid_'.$div.'" name="hidVid_'.$div.'" value="'.$data['villa_id'].'" />
			<table width="100%" align="left">
				<tr>
					<td width="17%">
						<select id="selMonth_'.$div.'" name="selMonth_'.$div.'" onChange="loadCal_'.$div.'()">
						'.$vars['monthOpts'].'
						</select>
					</td>
					<td width="14%">
						<select id="selYear_'.$div.'" name="selYear_'.$div.'" onChange="loadCal_'.$div.'()">
						'.$vars['yearOpts'].'
						</select>
					</td>
					
					<td width="69%">&nbsp;
						<span id="load_'.$div.'" class="loader"></span>
					</td>
				</tr>
			</table>
		</form>
	</div>';		
	$calendar .='
	<!-- Start mobileCalendar div -->
	<div id="mobileCalendar_'.$div.'">
	</div>
	<!-- End mobileCalendar div -->
	</div>';
	
	$calendar .='
	<script type="text/javascript">
		$(document).ready(function(){
			var v = $("#hidVid_'.$div.'").val();
			var m = $("#selMonth_'.$div.'").val();
			var y = $("#selYear_'.$div.'").val();
			$("#load_'.$div.'").html("Loading...");
			$("#mobileCalendar_'.$div.'").fadeTo("slow",0.15);
			$.ajax({
				type: "POST",
				url: "/json.php",
				data: "vid="+v+"&m="+m+"&y="+y,
				error: function(data) {
					alert(data)
				},
				success: function(data) {
					$("#mobileCalendar_'.$div.'").fadeTo("slow", 1).html(data);
					$("#load_'.$div.'").html("");
				}
			})
		});
		function loadCal_'.$div.'()
		{
			var v = $("#hidVid_'.$div.'").val();
			var m = $("#selMonth_'.$div.'").val();
			var y = $("#selYear_'.$div.'").val();
			$("#load_'.$div.'").html("Loading...");
			$("#mobileCalendar_'.$div.'").fadeTo("slow", 0.15);
			$.ajax({
				type: "POST",
				url: "/json.php",
				data: "vid="+v+"&m="+m+"&y="+y,
				error: function(data) {
					alert("Ajax Failed")
				},
				success: function(data) {
					$("#mobileCalendar_'.$div.'").fadeTo("slow", 1).html(data);
					$("#load_'.$div.'").html("");
				}
			});
			update_dropdown_'.$div.'("'.$div.'",y,m);
		}
		function update_dropdown_'.$div.'(div,y,m)
		{
			$.ajax({
				type: "POST",
				url: "/dd.php",
				data: "d="+div+"&m="+m+"&y="+y,
				error: function(data) {
					alert("Ajax Failed")
				},
				success: function(data) {
					$("#selMonth_'.$div.'").html(data);
				}
			});
		}
	</script>';
	
	/* all done, return result */
	echo $calendar;
}

function previous_month( $vid, $yesterday )
{
	$sabonCurl = new sabonCurl();
	
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
	$cal = $sabonCurl->cheeze_curls('getPropertyCalendar', $crequest, TRUE, TRUE, $vid."_calendar_data", $xpath, 'prod');

	$theVillaData = new DOMDocument();
	$theVillaData->load($xpath.$vid.'_calendar_data.xml.cache');
	
	$dates = $theVillaData->getElementsByTagName('Dates');
	
	$booked_dates = array();
	$b = 0;
	$hold_dates = array();
	$h = 0;
	
	foreach ($dates as $date):
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
	endforeach;
	
	$result = array();
	if( in_array( $yesterday, $booked_dates ) ):
		$result['where'] = 'booked';
		$result['date'] = $yesterday;
	elseif( in_array( $yesterday, $hold_dates ) ):
		$result['where'] = 'hold';
		$result['date'] = $yesterday;	
	endif;
	
	unlink($xpath.$vid.'_calendar_data.xml.cache');
	return $result;
}