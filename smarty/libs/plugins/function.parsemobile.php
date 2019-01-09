<?php
function smarty_function_parsemobile($params, &$smarty)
{
	error_reporting(E_ALL ^ E_NOTICE);
	$theVillaData = new DOMDocument();
	$theVillaData->load( 'villa-xml/'.$params['folder'].'/'.$params['cache'].'.xml.cache' ) or die("Can't load XML Document");
	
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
    
        $maxRate = -9999999;
        $minRate = 9999999;
        $maxBr = -9999999;
        $minBr = 9999999;
    
        $rs = 0;
        $mR = array();
    
		foreach ($rooms as $room):
			$rm = $room->getElementsByTagName('Room');
			foreach ($rm as $r):
				$mR[$rs]['RoomName'] = $r->getElementsByTagName('RoomName')->item(0)->nodeValue;
				$mR[$rs]['MinRate'] = $r->getElementsByTagName('MinRate')->item(0)->nodeValue;
				$mR[$rs]['MaxRate'] = $r->getElementsByTagName('MinRate')->item(0)->nodeValue;
    
                if(floatval($r->getElementsByTagName('MaxRate')->item(0)->nodeValue)>$maxRate) {
                    $data['maxRate'] = $r->getElementsByTagName('MaxRate')->item(0)->nodeValue;
                    $maxRate = $data['maxRate'];
                }
                if(floatval($r->getElementsByTagName('MinRate')->item(0)->nodeValue)<$minRate) {
                    $data['minRate'] = $r->getElementsByTagName('MinRate')->item(0)->nodeValue;
                    $minRate = $data['minRate'];
                }
                if(floatval($r->getElementsByTagName('RoomName')->item(0)->nodeValue)>floatval($maxBr)) {
                    $data['maxBr'] = $r->getElementsByTagName('RoomName')->item(0)->nodeValue;
                    $maxBr = $data['maxBr'];
                }
                if(floatval($r->getElementsByTagName('RoomName')->item(0)->nodeValue)<floatval($minBr)) {
                    $data['minBr'] = $r->getElementsByTagName('RoomName')->item(0)->nodeValue;
                    $minBr = $data['minBr'];
                }
                $rs++;
			endforeach;
		endforeach;
	endforeach;
    
    $data['mobileImages'] = $mG;
    $data['Rooms'] = $mR;
    $smarty->assign('mobile', $data);
}