<?php
function smarty_function_parsedata($params, &$smarty)
{
	error_reporting(E_ALL ^ E_NOTICE);

	/* Check if cache file is existing */
	$xcache = 'villa-xml/'.$params['folder'].'/'.$params['cache'].'.xml.cache';
	if( !file_exists('villa-xml/'.$params['folder'].'/'.$params['cache'].'.xml.cache') ):
		$contents = file_get_contents('http://ws.marketingvillas.com/vsapi.asmx/getContent?p_villaid='.$params['cache']);
		file_put_contents($xcache, $contents);
	endif;
	/* End check if cache file is existing.*/

	$theVillaData = new DOMDocument();
	$theVillaData->load( 'villa-xml/'.$params['folder'].'/'.$params['cache'].'.xml.cache' ) or die("Can't load XML Document");
	$villaData = $theVillaData->getElementsByTagName('Content');
	$d = 0;
	foreach ($villaData as $vData):
		$data[$d]['pageTitle'] = $vData->getAttribute('desc');
		$data[$d]['contentid'] = $vData->getAttribute('contentid');
		$data[$d]['contenttypeid'] = $vData->getAttribute('contenttypeid');
		$data[$d]['desc'] = (string) trim(html_entity_decode($vData->getElementsByTagName('Description')->item(0)->nodeValue));
		$villaSubContents = $vData->getElementsByTagName('SubContent');
		$s = 0;
		foreach ($villaSubContents as $subContents):
			$data[$d]['SubContents'][$s]['Heading'] = (string) trim($subContents->getElementsByTagName('Heading')->item(0)->nodeValue);
			$data[$d]['SubContents'][$s]['Description'] = (string) trim(html_entity_decode($subContents->getElementsByTagName('Description')->item(0)->nodeValue));
			$pageImages = $subContents->getElementsByTagName('Image');
			$i = 0;
			foreach ($pageImages as $pageImage):
				$data[$d]['SubContents'][$s]['Image'][$i]['Caption'] = (string) trim($pageImage->getElementsByTagName('Caption')->item(0)->nodeValue);
				$data[$d]['SubContents'][$s]['Image'][$i]['ThumbSizeUrl'] = (string) trim($pageImage->getElementsByTagName('ThumbSizeUrl')->item(0)->nodeValue);
				$data[$d]['SubContents'][$s]['Image'][$i]['FullSizeUrl'] = (string) trim($pageImage->getElementsByTagName('FullSizeUrl')->item(0)->nodeValue);
				$i++;
			endforeach;
			$s++;
		endforeach;
		$d++;
	endforeach;
	$pageData = xSearch($data, 'contenttypeid', $params['content']);
	
	$mobileData = $theVillaData->getElementsByTagName('ExtraInfo');
	foreach ($mobileData as $mData):
		$g = 0;
		$mG = array();
		$mobileImages = $mData->getElementsByTagName('Images');
		foreach ($mobileImages as $mblImages):
			$mblGallery = $mblImages->getElementsByTagName('Gallery');
			foreach ($mblGallery as $mi) :
				$Imgs = $mi->getElementsByTagName('Image');
				foreach ($Imgs as $Img):
					$mG[$g]['Caption'] = $Img->getElementsByTagName('Caption')->item(0)->nodeValue;
					$mG[$g]['ThumbSizeUrl'] = $Img->getElementsByTagName('ThumbSizeUrl')->item(0)->nodeValue;
					$mG[$g]['FullSizeUrl'] = $Img->getElementsByTagName('FullSizeUrl')->item(0)->nodeValue;
					$g++;
				endforeach;
			endforeach;
		endforeach;

		$data['VillaName'] = $mData->getElementsByTagName('VillaName')->item(0)->nodeValue;
		$data['SubLocation'] = $mData->getElementsByTagName('SubLocation')->item(0)->nodeValue;
		$data['Location'] = $mData->getElementsByTagName('Location')->item(0)->nodeValue;
		$data['Country'] = $mData->getElementsByTagName('Country')->item(0)->nodeValue;
		$data['Address'] = $mData->getElementsByTagName('Address')->item(0)->nodeValue;
		$data['ShortDesc'] = (string) trim(html_entity_decode($mData->getElementsByTagName('ShortDesc')->item(0)->nodeValue));

		$gpsCoordinates = $mData->getElementsByTagName('GPSCoordinates');
		foreach ($gpsCoordinates as $gps):
			$data['contenttypeid'] = $gps->getAttribute('contenttypeid');
			$decimal = $gps->getElementsByTagName('Decimal');
			foreach ($decimal as $value):
				$data['Latitude'] = $value->getElementsByTagName('Latitude')->item(0)->nodeValue;
				$data['Longitude'] = $value->getElementsByTagName('Longitude')->item(0)->nodeValue;
			endforeach;
		endforeach;

		$rooms = $mData->getElementsByTagName('Rooms');
		foreach ($rooms as $room):
			$rm = $room->getElementsByTagName('Room');
			foreach ($rm as $r):
				$data['RoomName'] = $r->getElementsByTagName('RoomName')->item(0)->nodeValue;
				$data['MinRate'] = $r->getElementsByTagName('MinRate')->item(0)->nodeValue;
				$data['MaxRate'] = $r->getElementsByTagName('MinRate')->item(0)->nodeValue;
			endforeach;
		endforeach;
	endforeach;
	if( isset($params['mobileData']) && $params['mobileData'] == 'yes' ):
		$coord['latitude'] = $data['Latitude'];
		$coord['longitude'] = $data['Longitude'];
		$data['mobileImages'] = $mG;
		$smarty->assign('mdata', $data);
		$smarty->assign('coordinates', $coord);
	endif;
	$smarty->assign('content', $pageData );
}

function xSearch($array, $key, $value) {
	$results = array();

	if (is_array($array)):
		if (isset($array[$key]) && $array[$key] == $value):
			$results[] = $array;
		endif;

		foreach ($array as $subarray):
			$results = array_merge($results, xSearch($subarray, $key, $value));
		endforeach;
	endif;

	return $results;
}
