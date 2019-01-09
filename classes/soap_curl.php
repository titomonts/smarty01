<?php
/*
	Class Name sabonCurl
	Executes CURL Requests
				
*/
class sabonCurl{
	/*  
	Parameters: 1. $op = Operation to be executed on the web service
				2. $arr = Request String (i.e. strSearch=Indonesia). Get string format
				3. $convToArray = Boolean. Converts the result into array
				4. $saveToFile = Boolean. Outputs result to a corresponding XML
				
	Example usage: $xml = $soapCURL->go_CURL('FindAVilla', $request, TRUE, FALSE);
	*/
	public function go_CURL($op, $arr="", $convToArray, $saveToFile, $switch="", $path="")
	{
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, 'http://ws.thevillaguide.com/service.asmx/'.$op);
		//curl_setopt($ch, CURLOPT_URL, 'http://uat-ws.thevillaguide.com/service.asmx/'.$op);
		//curl_setopt($ch, CURLOPT_URL, 'http://tryme-ws.thevillaguide.com/service.asmx/'.$op);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		if($arr != ""):
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
		endif;
		
		$data = curl_exec($ch);
		curl_close($ch);
		
		if($saveToFile):
			if(file_exists($path.$switch.'.xml'))
				unlink($path.$switch.'.xml');
				
			$fp = fopen($path.$switch.'.xml', 'w');
			fwrite($fp, html_entity_decode($data));
			fclose($fp);
		endif;
		
		if($convToArray):
			return json_decode(json_encode((array) simplexml_load_string($data)),1);
		else:
			return $data;
		endif;		
	}
	
	public function cheeze_curls($op, $arr="", $convToArray, $cacheThis, $switch="", $path="",$db)
	{
		$ch = curl_init();
		/*
		switch($db):
			case 'prod':
				curl_setopt($ch, CURLOPT_URL, 'http://ws.marketingvillas.com/vsapi.asmx/'.$op);
			break;
			
			case 'tryme':
				curl_setopt($ch, CURLOPT_URL, 'http://tryme-ws.marketingvillas.com/vsapi.asmx/'.$op);
			break;
			
			case 'uat':
				curl_setopt($ch, CURLOPT_URL, 'http://uat-ws.marketingvillas.com/vsapi.asmx/'.$op);
			break;
			
			default:
				curl_setopt($ch, CURLOPT_URL, 'http://ws.marketingvillas.com/vsapi.asmx/'.$op);
				
		endswitch;
		*/
		curl_setopt($ch, CURLOPT_URL, 'http://ws.marketingvillas.com/vsapi.asmx/'.$op);
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
			file_put_contents($cacheName, $data) or die('Error saving cache');
			chmod($cacheName,0777);
		endif;
		
		if($convToArray):
			return json_decode(json_encode((array) simplexml_load_string($data)),1);
		else:
			return $data;
		endif;
	}
	
	public function go_new_booking($arr, $convToArray)
	{
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, 'http://www.privatehomesandvillas.com/phv/usertools/besa_rfi.aspx');
		//curl_setopt($ch, CURLOPT_URL, 'http://uat.privatehomesandvillas.com/phv/usertools/besa_rfi.aspx');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
		
		$response = curl_exec($ch);
		curl_close($ch);
		
		if($convToArray):
			$data = explode(":",$response);
			return $data;
		else:
			return $response;
		endif;
	}

    public function curlIP($arr,$convToArray)
	{
		$ch = curl_init();
		
		//curl_setopt($ch, CURLOPT_URL, 'http://uat-ws.thevillaguide.com/service.asmx/getIPAddressLocation');
        curl_setopt($ch, CURLOPT_URL, 'http://ws.thevillaguide.com/service.asmx/getIPAddressLocation');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
		
		$data = curl_exec($ch);
		curl_close($ch);
		
		if($convToArray):
			return json_decode(json_encode((array) simplexml_load_string($data)),1);
		else:
			return $data;
		endif;
	}
	
	public function curPageURL() {
	 $pageURL = 'http';
	 /*if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}*/
	 $pageURL .= "://";
	 if ($_SERVER["SERVER_PORT"] != "80") {
	  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	 } else {
	  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	 }
	 return $pageURL;
	}
	
	
}
/* End of Class */
?>
