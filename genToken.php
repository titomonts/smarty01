<?php
$timeTokenHash = go_CURL('Security_GetTimeToken',"",TRUE,FALSE);
if(!is_array($timeTokenHash))
	$timeTokenHash = html_entity_decode($timeTokenHash);
	
$params['p_ToHash'] = 'villaprtl|Xr4g2RmU|'.$timeTokenHash[0];
$hashString = prepare_Security_GetMD5Hash($params);
$md5Hash = cheeze_curls('Security_GetMD5Hash',$hashString,TRUE,FALSE);

echo $md5Hash[0];

function prepare_Security_GetMD5Hash($params)
{
	$xml_string = "p_ToHash=".$params['p_ToHash']."";
	return $xml_string;
}

function cheeze_curls($op, $arr="", $convToArray, $cacheThis, $switch="", $path="")
{
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_URL, 'http://ws.marketingvillas.com/portalapi.asmx/'.$op);
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

function go_CURL($op, $arr="", $convToArray, $saveToFile)
{
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_URL, 'http://ws.thevillaguide.com/service.asmx/'.$op);
	//curl_setopt($ch, CURLOPT_URL, 'http://uat-ws.thevillaguide.com/service.asmx/'.$op);
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