<?php
if(isset($_GET['c']))
	$country = $_GET['c'];

if(isset($_POST['c']))
	$country = $_POST['c'];

$countries = go_CURL('DisplayAllCountries',"",TRUE,FALSE);

for($c=0; $c<sizeof($countries['COUNTRY']); $c++):
	if($country == $countries['COUNTRY'][$c]['CountryID'] && !(is_array($countries['COUNTRY'][$c]['areacode']))):
		echo $countries['COUNTRY'][$c]['areacode'];
	endif;
endfor;

function go_CURL($op, $arr="", $convToArray, $saveToFile)
{
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_URL, 'http://ws.thevillaguide.com/service.asmx/'.$op);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	if($arr != ""):
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
	endif;
	
	$data = curl_exec($ch);
	curl_close($ch);
	
	if($saveToFile):
		$fp = fopen('wp-content/cache/'.$op.'.xml', 'w');
		fwrite($fp, html_entity_decode($data));
		fclose($fp);
	endif;
	
	if($convToArray):
		return json_decode(json_encode((array) simplexml_load_string($data)),1);
	else:
		return $data;
	endif;		
}