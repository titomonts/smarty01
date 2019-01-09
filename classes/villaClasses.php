<?php
/*
	Essential Functions for Villa Website Simplified
	Version: 4.0 Star Production
	Author: Vegeta, Prince of all Saiyans
*/

class villa
{
	/*
		Function name: connect()
	*/
	public function connect()
	{
		$this->dbLink = new mysqli('localhost','root','hcnom2031055','aws_smarty');
		if ($this->dbLink->connect_errno):
			echo "Failed to connect to MySQLi: ".$this->dbLink->connect_error;
			exit();
		endif;
		return $this->dbLink;
	}
	
	/*
		Function name: cheeze_curls()
		Description: Uses MVL Web Services
	*/
	public function cheeze_curls($op, $arr="", $convToArray, $cacheThis, $filename="", $path="",$db="prod")
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://ws.marketingvillas.com/portalapi.asmx/'.$op);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		if($arr != ""):
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
		endif;
		
		$data = curl_exec($ch);
		if( $data === false ):
			$data = curl_error($ch);
			//echo stripslashes($data);
		endif;
		curl_close($ch);
		if( $cacheThis ):	
			/*
			$cacheName = $path.$filename.'.xml.cache';
			file_put_contents($cacheName, $data) or die('Error saving cache');
			chmod($cacheName,0777);
			*/
			$cacheName = $path.$filename.'.xml.cache';
			chmod($path,0777);
				
			$fp = fopen($cacheName, 'w');
			fwrite($fp, html_entity_decode($data));
			fclose($fp);
			
		endif;
		
		if($convToArray):
			return json_decode(json_encode((array) simplexml_load_string($data)),1);
		else:
			return $data;
		endif;
	}
	
	/*
		Function name: curly_tops()
		Description: Uses TVG Webservices
	*/
	public function curly_tops($op, $arr="", $convToArray, $saveToFile, $filename="", $path="", $getfrom="prod")
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
			if(file_exists($path.$filename.'.xml'))
				unlink($path.$filename.'.xml');
				
			$fp = fopen($path.$filename.'.xml', 'w');
			fwrite($fp, html_entity_decode($data));
			fclose($fp);
		endif;
		
		if($convToArray):
			return json_decode(json_encode((array) simplexml_load_string($data)),1);
		else:
			return $data;
		endif;	
	}

	/* 
		Function name: teaser_data()
		Description: Returns Basic villa info for teaser sites
	*/
	public function teaser_data( $vid, $op="getMVLVillaInfo" )
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://ws.marketingvillas.com/vsapi.asmx/'.$op);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'p_UserID=villaprtl&p_Params={"PropertyID":"'.$vid.'"}');
		
		$data = curl_exec($ch);
		curl_close($ch);
		
		return json_decode(json_encode((array) simplexml_load_string($data)),1);
	}
	
	/*
		Function name: get_contents()
	*/
	public function get_contents($params)
	{
		$data = array();
		$theVillaData = new DOMDocument();
		$xcache = VILLA_XML_PATH.$params['theme'].'/'.$params['cacheName'].'.xml.cache';
		$theVillaData->load($xcache);
		
		/* 
			Start Parse XML Data 
		*/
		
		/* Check if content is for mobile or for desktop site */
		if( !$params['ismobile'] ): /* Site is desktop/laptop environment */
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
			$pageData = $this->xSearch($data, 'contenttypeid', $params['content']);
			return $pageData;
		else: /* Site is tablet/mobile environment */
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
			$data['mobileImages'] = $mG;
			return $data;
		endif;
		/* End check if content is mobile or desktop site */
		
		/* 
			End Parse XML Data 
		*/
		
	}
	
	private function xSearch($array, $key, $value) {
		$results = array();
	
		if (is_array($array)):
			if (isset($array[$key]) && $array[$key] == $value):
				$results[] = $array;
			endif;
	
			foreach ($array as $subarray):
				$results = array_merge($results, $this->xSearch($subarray, $key, $value));
			endforeach;
		endif;
	
		return $results;
	}

	/*
		Function name: curlIP()
	*/
	public function curlIP($arr,$convToArray)
	{
		$ch = curl_init();
		
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

	/* Additional Function to by-pass proxy servers when getting IP addresses */
	public function get_ip() 
	{
		if ( function_exists( 'apache_request_headers' ) ) {
	
			$headers = apache_request_headers();
		} else {
	
			$headers = $_SERVER;
		}
		
		if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
			$_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
		}	
	
		/*
		if ( array_key_exists( 'X-Forwarded-For', $headers ) && filter_var( $headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
	
			$the_ip = $headers['X-Forwarded-For'];
			echo 'X-Forwarded-For';
		} elseif ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $headers ) && filter_var( $headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 )
		) {
	
			$the_ip = $headers['HTTP_X_FORWARDED_FOR'];
			echo 'HTTP';
		} else {
			
			$the_ip = filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );
			echo 'REMOTE_ADDR';
		}
		 */
		$the_ip = filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );
		return $the_ip;
	
	}


	/*
		Functiona name: ws_villa_info()
		Parameters: $villaid
	*/
	public function ws_villa_info($villaid)
	{
		$params = array("PropertyID"=>$villaid,"BookingSourceID"=>11);
		$p_Params = json_encode($params);
		$vrequest = 'p_UserID=villaprtl&p_Params='.$p_Params;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://ws.marketingvillas.com/portalapi.asmx/getPropertyGTMData');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $vrequest);
		$data = curl_exec($ch);
		curl_close($ch);
		
		return json_decode(json_encode((array) simplexml_load_string($data)),1);
	}
	
	/*
		Function name: gtm_render
	*/
	public function render_gtm($sendToAnalytics=FALSE,$villa_info,$gaInfo=array(),$post_vars=array())
	{
		$output = '<script type="text/javascript">
					dataLayer = [];
					';
		
		if($sendToAnalytics && !empty($gaInfo)):
			$output .= "dataLayer.push({
						'ecommerce': {
							'purchase': {
								'actionField': {
									'id': '".$gaInfo['gaData']['transactionId']."',
									'affiliation': '".$villa_info['ehgBookingSource']."',
									'revenue': '".$gaInfo['gaData']['price']."'
								},
								'products': [{
									'sku': '".$gaInfo['gaData']['sku']."',
									'name': '".$villa_info['villa_name']."',
									'id': '".$villa_info['villa_id']."',
									'price': '".$gaInfo['gaData']['unitPrice']."',
									'category': '".$villa_info['location'].', '.$villa_info['sublocation']."',
									'variant': '".$villa_info['bedrooms']."',
									'quantity': '".$gaInfo['gaData']['quantity']."',
									'coupon': ''
								}]
							}
						}
					});
					";
		endif;
		
		if( !empty($post_vars) && $sendToAnalytics ):
			$output .= "dataLayer.push({
					'dataEnquiry': {
						'enquStartDateYear': ". ( isset($post_vars['txtArrivalDate']) ? "'" . date('Y', strtotime($post_vars['txtArrivalDate'])) . "'" : '0000' ).",
						'enquStartDateMonth': ". ( isset($post_vars['txtArrivalDate']) ? "'" . date('m', strtotime($post_vars['txtArrivalDate'])) . "'" : '00' ).",
						'enquStartDateDay': ".( isset($post_vars['txtArrivalDate']) ? "'" . date('d', strtotime($post_vars['txtArrivalDate'])) . "'" : '00' ).",
						'enquEndDateYear': ". ( isset($post_vars['txtDepartDate']) ? "'" . date('Y', strtotime($post_vars['txtDepartDate'])) . "'" : '0000' ).",
						'enquEndDateMonth': ". ( isset($post_vars['txtDepartDate']) ? "'" . date('m', strtotime($post_vars['txtDepartDate'])) . "'" : '00' ).",
						'enquEndDateDay': ". ( isset($post_vars['txtDepartDate']) ? "'" . date('d', strtotime($post_vars['txtDepartDate'])) . "'" : '00' ).",
						'enquStayLenght': '". $gaInfo['gaData']['quantity']."'
					}
				});
				";
		endif;
		
		$output .= "dataLayer.push({
						'dataPageTrack': {
							'ehgPageType': '".$villa_info['ehgPageType']."',
							'ehgBookingSource': '".$villa_info['ehgBookingSource']."',
							'ehgBedrooms': '".$villa_info['bedrooms']."',
							'ehgLocation': '".$villa_info['ehgLocation']."',
							'hrental_id': [".$villa_info['hrental_id']."]
						}
					});
					";
		$output .='</script>
		';
		
		$output .='<!-- Google Tag Manager -->
				<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-MMM2GJ"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
				  ';
				  
		$output .= "<script>(function (w, d, s, l, i) {
					w[l] = w[l] || [];
					w[l].push({'gtm.start':
								new Date().getTime(), event: 'gtm.js'});
					var f = d.getElementsByTagName(s)[0],
							j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
					j.async = true;
					j.src =
							'//www.googletagmanager.com/gtm.js?id=' + i + dl;
					f.parentNode.insertBefore(j, f);
				})(window, document, 'script', 'dataLayer', 'GTM-MMM2GJ');</script>
				<!-- Google Tag Manager -->";
		return $output;		  		
	}
	
	/*
		Function name: classic_analytics
	*/
	public function classic_analytics($sendToAnalytics=FALSE,$villa_info,$gaInfo=array())
	{
		$output = '<script type="text/javascript">
					';
		$output .= "(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
					(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
					m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
					})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
					";
		$output .= "ga('create', '".$villa_info['google_analytics']."', 'auto');
					ga('send', 'pageview');
					";
					
		if( $sendToAnalytics ):
			if( $gaInfo['@attributes']['status'] != 'error' && $gaInfo['gaData']['transactionId'] != "" ):
				$output .= "ga('require', 'ecommerce');
							ga('ecommerce:addTransaction', {
								'id': '".$gaInfo['gaData']['transactionId']."',// Transaction ID. Required
								'affiliation': '',   // Affiliation or store name
								'revenue': '".$gaInfo['gaData']['price']."',// Grand Total
								'shipping': '',// Shipping
								'tax': ''// Tax
							});
							
							ga('ecommerce:addItem', {
								'id': '".$gaInfo['gaData']['transactionId']."',// Transaction ID. Required
								'name': '".$gaInfo['gaData']['name']."',// Product name. Required
								'sku': '".$gaInfo['gaData']['sku']."',// SKU/code
								'category': '".$gaInfo['gaData']['category']."',// Category or variation
								'price': '".$gaInfo['gaData']['unitPrice']."',// Unit price
								'quantity': '".$gaInfo['gaData']['quantity']."'// Quantity
							});
							ga('ecommerce:send');
							"; 
			endif;
		endif;
		$output .='</script>';
		return $output;			
	}
	
	/*
		Function name: reserve_process()
	*/
	public function reserve_process($reserve_post_data)
	{
		$villaName = $reserve_post_data['hidVillaName'];
		$params['VillaID'] = $reserve_post_data['villaID'];
		$params['CIDate'] = $reserve_post_data['txtArrivalDate'];
		$params['CODate'] = $reserve_post_data['txtDepartDate'];
		$params['GuestFirstName'] = stripslashes($reserve_post_data['txtFirstname']);
		$params['GuestLastName'] = stripslashes($reserve_post_data['txtLastName']);
		$params['CountryOfResidence'] = $reserve_post_data['selCountry'];
		$params['Email'] = $reserve_post_data['txtEmail'];
		$params['TelNo'] = $reserve_post_data['txtPhoneAreaCode'].$reserve_post_data['txtPhoneNumber'];
		$params['TotalAdults'] = empty($reserve_post_data['numAdult'])?'2':$reserve_post_data['numAdult'];
		$params['BookingSourceID'] = "11";
		$params['MobileNo'] = empty($reserve_post_data['txtAltNumber'])?$reserve_post_data['txtPhoneNumber']:'';
		$params['BedRooms'] = '1';
		/* for complex villas */
		if( isset($reserve_post_data['villaids']) ): 
			$vill = "";
			$svill = sizeof($reserve_post_data['villaids']);
			for($c=0; $c<$svill; $c++):
				if(isset($reserve_post_data['villaids'][$c])):
					$vill .= $reserve_post_data['villaids'][$c].', ';
				endif;
			endfor;
			$vill = substr($vill,0,-1);
		endif;
		/* end for complex villas */
		$params['SpecialRequest'] = stripslashes( strip_tags($reserve_post_data['txtMessage']).(!empty($vill)?', Selected villas: '.$vill:'') );
		$params['SpecialRequest'] = preg_replace('/[^\00-\255]+/u','',$params['SpecialRequest']);
		$params['SpecialRequest'] = preg_replace('/[^\\x20-\\x7E]/', '', $params['SpecialRequest']);
		if ($reserve_post_data['alternatives'] == 'Y'):
			$params['SuggestOtherVilla'] = 'Y';
		else:
			$params['SuggestOtherVilla'] = 'N';
		endif;
		$params['TotalChildren'] = $reserve_post_data['numChildren'];
		$params['TotalInfants'] = $reserve_post_data['numInfant'];
		$params['RURL'] = urlencode($reserve_post_data['rurl']);
		$params['IsGenInquiry'] = 'N';
		$params['CIPAddress'] = $reserve_post_data['hid_cip'];
		if ($reserve_post_data['is_event'] == 'Y'):
			$params['IsEvent'] = 'Y';
		else:
			$params['IsEvent'] = 'N';
		endif;
		if ($reserve_post_data['date_flex'] == 'Y'):
			$params['AreDatesFlexible'] = 'Y';
		else:
			$params['AreDatesFlexible'] = 'N';
		endif;
		$params['OptInMailList'] = 'Y';
		$params['LCID'] = 'en';
	
		$timeTokenHash = $this->cheeze_curls('Security_GetTimeToken', "", TRUE, FALSE,"","",$reserve_post_data['db']);
		if (!is_array($timeTokenHash))
			$timeTokenHash = html_entity_decode($timeTokenHash);
	
		$params['p_ToHash'] = 'villaprtl|Xr4g2RmU|'.$timeTokenHash[0];
		$hashString = $this->prepare_Security_GetMD5Hash($params);
		$md5Hash = $this->cheeze_curls('Security_GetMD5Hash', $hashString, TRUE, FALSE,"","",$reserve_post_data['db']);
		$p_Params = json_encode($params);
		$p_UserID = 'villaprtl';
		$p_Token = $md5Hash[0];
		$request = 'p_Token='.$p_Token.'&p_UserID='.$p_UserID.'&p_Params='.$p_Params;
		$newBooking =  $this->cheeze_curls('insertInquiry',$request,TRUE,FALSE,"","",$reserve_post_data['db']);
		if ($newBooking['@attributes']['status'] == 'error'):
			$error['form_error'] = 'Enquiry Form';
			$error['return_url'] = '/reservations.html';
			$this->houston_we_have_a_problem($error);
		else:
			$newBooking['thank_you_message'] = '<p>Your Reservation Enquiry Form has been successfully sent for '.$villaName.'.</p>
				<p>The Elite Havens Group, luxury villa rentals, manage all the reservations for '.$villaName.'. One of our villa specialists will be in touch shortly.</p>
				<p>Your Reference I.D. is <strong>'.$newBooking['Transactions']['InquiryID'].'</strong></p>
				<p>The Elite Havens Group presents a stunning portfolio of luxury private villas throughout Bali and Lombok in Indonesia, Thailand, Sri Lanka and Maldives. Staffed to the highest quality, each villa offers a blissfully relaxing and highly individual experience. Ranging in size from one to nine bedrooms and boasting private pools, luxurious living spaces, equipped kitchens (with chef) and tropical gardens, our villas are situated in the heart of the action, beside blissful beaches, upon jungle-clad hillsides and amongst idyllic rural landscapes ensuring the perfect holiday experience for all.</p>';
		endif;
		return $newBooking;
	}
	
	/*
		Function name: general_enquiry_process()
	*/
	function general_enquiry_process($gen_post_data)
	{
		$villaName = $gen_post_data['hidVillaName'];
		$params['VillaID'] = $gen_post_data['villaID'];
		$params['CIDate'] = '1 January 1900';
		$params['CODate'] = '3 January 1900';
		$params['GuestFirstName'] = stripslashes($gen_post_data['txtFirstname']);
		$params['GuestLastName'] = stripslashes($gen_post_data['txtLastName']);
		$params['CountryOfResidence'] = $gen_post_data['selCountry'];
		$params['Email'] = $gen_post_data['txtEmail'];
		$params['TelNo'] = $gen_post_data['txtPhoneAreaCode'].$gen_post_data['txtPhoneNumber'];
		$params['TotalAdults'] = '1';
		$params['BookingSourceID'] = "11";
		$params['MobileNo'] = '';
		$params['BedRooms'] = '1';
		$params['SpecialRequest'] = stripslashes('Subject:'.strip_tags($gen_post_data['messageSubject']).', Message: '.$gen_post_data['txtMessage']);
		$params['SuggestOtherVilla'] = 'N';
		$params['TotalChildren'] = 0;
		$params['TotalInfants'] = 0;
		$params['RURL'] = urlencode($gen_post_data['hfrurl']);
		$params['IsGenInquiry'] = 'Y';
		$params['CIPAddress'] = $gen_post_data['hid_cip'];
		$params['IsEvent'] = 'Y';
		$params['AreDatesFlexible'] = 'N';
		$params['OptInMailList'] = 'Y';
		$params['LCID'] = 'en';
			
		$timeTokenHash = $this->cheeze_curls('Security_GetTimeToken', "", TRUE, FALSE,"","","prod");
		if (!is_array($timeTokenHash))
			$timeTokenHash = html_entity_decode($timeTokenHash);
	
		$params['p_ToHash'] = 'villaprtl|Xr4g2RmU|'.$timeTokenHash[0];
		$hashString = $this->prepare_Security_GetMD5Hash($params);
		$md5Hash = $this->cheeze_curls('Security_GetMD5Hash', $hashString, TRUE, FALSE,"","","prod");
		$p_Params = json_encode($params);
		$p_UserID = 'villaprtl';
		$p_Token = $md5Hash[0];
		$request = 'p_Token='.$p_Token.'&p_UserID='.$p_UserID.'&p_Params='.$p_Params;
		$newBooking = $this->cheeze_curls('insertInquiry',$request,TRUE,FALSE,"","","prod");
		$newBooking['thank_you_message'] = '<p>Your Reservation Enquiry Form has been successfully sent for '.$villaName.'.</p>
			<p>The Elite Havens Group, luxury villa rentals, manage all the reservations for '.$villaName.'. One of our villa specialists will be in touch shortly.</p>
			<p>Your Reference I.D. is <strong>'.$newBooking['Transactions']['InquiryID'].'</strong></p>
			<p>The Elite Havens Group presents a stunning portfolio of luxury private villas throughout Bali and Lombok in Indonesia, Thailand, Sri Lanka and Maldives. Staffed to the highest quality, each villa offers a blissfully relaxing and highly individual experience. Ranging in size from one to nine bedrooms and boasting private pools, luxurious living spaces, equipped kitchens (with chef) and tropical gardens, our villas are situated in the heart of the action, beside blissful beaches, upon jungle-clad hillsides and amongst idyllic rural landscapes ensuring the perfect holiday experience for all.</p>';
		return $newBooking;
		
	}
	
	/*
		Function name: events_process()
	*/
	public function events_process($event_post_data)
	{
		$villaName = $event_post_data['hidVillaName'];
		$params['VillaID'] = $event_post_data['villaID'];
		$params['CIDate'] = '1 January 1900';
		$params['CODate'] = '3 January 1900';
		$params['GuestFirstName'] = stripslashes($event_post_data['txtFirstname']);
		$params['GuestLastName'] = stripslashes($event_post_data['txtLastName']);
		$params['CountryOfResidence'] = $event_post_data['selCountry'];
		$params['Email'] = $event_post_data['txtEmail'];
		$params['TelNo'] = $event_post_data['txtPhoneAreaCode'].$event_post_data['txtPhoneNumber'];
		$params['TotalAdults'] = !empty($event_post_data['txtGuests'])?$event_post_data['txtGuests']:'1';
		$params['BookingSourceID'] = "11";
		$params['MobileNo'] = '';
		$params['BedRooms'] = $event_post_data['selBedroom'];
		$params['SpecialRequest'] = stripslashes('Event Date:'.strip_tags($event_post_data['eventdate']).', No. of pax: '.$event_post_data['txtGuests'].', Message: '.$event_post_data['txtMessage']);
		$params['SuggestOtherVilla'] = 'N';
		$params['TotalChildren'] = 0;
		$params['TotalInfants'] = 0;
		$params['RURL'] = urlencode($event_post_data['hfrurl']);
		$params['IsGenInquiry'] = 'Y';
		$params['CIPAddress'] = $event_post_data['hid_cip'];
		$params['IsEvent'] = 'Y';
		$params['AreDatesFlexible'] = 'N';
		$params['OptInMailList'] = 'Y';
		$params['LCID'] = 'en';
			
		$timeTokenHash = $this->cheeze_curls('Security_GetTimeToken', "", TRUE, FALSE,"","","prod");
		if (!is_array($timeTokenHash))
			$timeTokenHash = html_entity_decode($timeTokenHash);
	
		$params['p_ToHash'] = 'villaprtl|Xr4g2RmU|'.$timeTokenHash[0];
		$hashString = $this->prepare_Security_GetMD5Hash($params);
		$md5Hash = $this->cheeze_curls('Security_GetMD5Hash', $hashString, TRUE, FALSE,"","","prod");
		$p_Params = json_encode($params);
		$p_UserID = 'villaprtl';
		$p_Token = $md5Hash[0];
		$request = 'p_Token='.$p_Token.'&p_UserID='.$p_UserID.'&p_Params='.$p_Params;
		$newBooking = $this->cheeze_curls('insertInquiry',$request,TRUE,FALSE,"","","prod");
		$newBooking['thank_you_message'] = '<p>Your Reservation Enquiry Form has been successfully sent for '.$villaName.'.</p>
			<p>The Elite Havens Group, luxury villa rentals, manage all the reservations for '.$villaName.'. One of our villa specialists will be in touch shortly.</p>
			<p>Your Reference I.D. is <strong>'.$newBooking['Transactions']['InquiryID'].'</strong></p>
			<p>The Elite Havens Group presents a stunning portfolio of luxury private villas throughout Bali and Lombok in Indonesia, Thailand, Sri Lanka and Maldives. Staffed to the highest quality, each villa offers a blissfully relaxing and highly individual experience. Ranging in size from one to nine bedrooms and boasting private pools, luxurious living spaces, equipped kitchens (with chef) and tropical gardens, our villas are situated in the heart of the action, beside blissful beaches, upon jungle-clad hillsides and amongst idyllic rural landscapes ensuring the perfect holiday experience for all.</p>';
		return $newBooking;
	
	}

	/* Start Error Handling */
	public function houston_we_have_a_problem($error)
	{
		$eych_ti_em_el = "<script type='text/javascript' src='/resources/common/js/jquery-1.7.1.js'></script>
		<script type='text/javascript' src='/resources/common/js/jquery-ui-1.8.js'></script>
		<link href='/resources/common/css/jquery-ui-1.8.css' rel='stylesheet' type='text/css'>";
		
		$eych_ti_em_el .='<script>
				$(function() {	
					$( "#error" ).dialog({ 
							open: function(event, ui) { $(".ui-dialog-titlebar-close", ui.dialog || ui).hide(); },
							modal: true,
							closeOnEscape: false,
							draggable: false,
							resizable: false,
							overlay: { opacity: 1}, 
							title: "'.$error['form_error'].'",
							buttons: [
								{
									text: "OK",
									click: function() {
										window.location.href="'.$error['return_url'].'"; 
									},
								}
							],
							
							width: 530,
							height: 200,
							close: function(event, ui) { 
								window.location.href="'.$error['return_url'].'";  
							} 
					});
					$(".ui-dialog-titlebar").hide();
					var overlay = $(".ui-widget-overlay");
					baseBackground = overlay.css("background");
					baseOpacity = overlay.css("opacity");
					overlay.css("background", "#999").css("opacity", "1");
			});
			</script>';
			$eych_ti_em_el .='<div id="error" align="left" style="padding-left:15px;display:none;">
				<strong>'.$error['form_error'].'</strong>
				<br />
				There was an error processing your request. 
				'.(isset($error['extra_msg'])?"<br /><strong>- ".$error['extra_msg']."</strong><br />":"").' 
				Click OK to return to the form and try again.
			</div>';
			echo $eych_ti_em_el;
	}
	/* End Error Handling */
	
	/* Array Sort */
	public function subval_sort($people,$sortKey) {
		$sortArray = array();
	
		foreach($people as $person){
			foreach($person as $key=>$value){
				if(!isset($sortArray[$key])){
					$sortArray[$key] = array();
				}
				$sortArray[$key][] = $value;
			}
		}
		
		$orderby = $sortKey; 
		array_multisort($sortArray[$orderby],SORT_DESC,$people); 
		return $people;
	}
	/* End Array Sort */
	
	/*
		Function name: getVillaReviews()
	*/
	public function getVillaReviews($params)
	{
		/* SET XML Folder for Reviews */
		$xpath = MAIN_FOLDER."/villa-xml/".$params['villa_theme']."/";
		if(!is_dir($xpath))
			mkdir($xpath,0775);
		/* End SET XML Folder for Reviews */
		
		$request = "strVillaID=".$params['id']."";
		$xml = $this->curly_tops('getVillaReviews',$request,TRUE,TRUE,$params['id'].'_reviews',$xpath,'prod');
		
		$reviews = array();
		$rc = 0;
		
		/* ANOTHER APPROACH */
		$rawiswar = '';
		$file = fopen( $xpath.$params['id'].'_reviews.xml', 'r' );
				
		while( $xd = fread( $file, 65535 ) ):
			$xd = trim($xd);
			$xd = preg_replace('/\s+/', ' ', $xd);
			$xd = str_replace('&nbsp;', ' ', $xd);
			$rawiswar .= $xd; 
		endwhile;
		fclose( $file );
		
		preg_match_all( "/\<REVIEW\>(.*?)\<\/REVIEW\>/s", $rawiswar, $vblocks );
	
		foreach( $vblocks[1] as $vblock ):
			preg_match_all( "/\<ApprStatusID\>(.*?)\<\/ApprStatusID\>/", $vblock, $status );
			preg_match_all( "/\<Name\>(.*?)\<\/Name\>/", $vblock, $name );
			
			if($status[1][0] == 'Approved' && $name[1][0] != 'Staff'):
				
				preg_match_all( "/\<SDate\>(.*?)\<\/SDate\>/", $vblock, $sdate );
				$reviews[$rc]['SDate'] = $sdate[1][0];
				
				preg_match_all( "/\<EDate\>(.*?)\<\/EDate\>/", $vblock, $edate );
				$reviews[$rc]['EDate'] = $edate[1][0];
				
				preg_match_all( "/\<ReviewByName\>(.*?)\<\/ReviewByName\>/", $vblock, $reviewbyname );
				$reviews[$rc]['ReviewByName'] = $reviewbyname[1][0];
				
				preg_match_all( "/\<ApprStatusID\>(.*?)\<\/ApprStatusID\>/", $vblock, $rstatus );
				$reviews[$rc]['ApprStatusID'] = $rstatus[1][0];
				
				preg_match_all( "/\<ROverallComments\>(.*?)\<\/ROverallComments\>/", $vblock, $rcomments );
				if( array_key_exists(0,$rcomments[1]) ):
					$reviews[$rc]['ROverallComments'] = $rcomments[1][0];
				else:
					$reviews[$rc]['ROverallComments'] = '';	
				endif;
				
				$rc++;
			endif;
		endforeach;
		/* END ANOTHER APPROACH */
		
		/* DISPLAY ARRAY RESULT */
		$hide_this = array();
		if( array_key_exists('hide_this_year', $params) ):
			if( strpos($params['hide_this_year'], ',') !== FALSE ):
				$hide_this = explode(',',$params['hide_this_year']);
			else:
				$hide_this[0] = $params['hide_this_year'];
			endif;
		endif;
		$sr = sizeof($reviews);
		$result = '';
		if( $sr > 0 ):
			for( $n = 0; $n < $sr; $n++ ):
				$reviews = $this->subval_sort($reviews,'SDate');
				if( $reviews[$n]['ROverallComments'] != '' ):
					$result .='<div class="review"><blockquote>';
					if( array_key_exists('SDate',$reviews[$n]) ):
						if( sizeof($hide_this) <= 0 ):
							$result .= '<p class="dates"><strong>'.date("F jS, Y", strtotime($reviews[$n]['SDate'].' + 1 day')).' - '.date("F jS, Y", strtotime($reviews[$n]['EDate'].' + 1 day')).'</strong></p>&ldquo;';
						else:
							$filter_this = date('Y', strtotime($reviews[$n]['EDate']));
							if( !in_array($filter_this,$hide_this) ):
								$result .= '<p class="dates"><strong>'.date("F jS, Y", strtotime($reviews[$n]['SDate'].' + 1 day')).' - '.date("F jS, Y", strtotime($reviews[$n]['EDate'].' + 1 day')).'</strong></p>&ldquo;';
							endif;
						endif;
					endif;
					if( sizeof($hide_this) <= 0 ):
						$result .= $reviews[$n]['ROverallComments'];
						$result .= '&rdquo;</blockquote><p class="name">'.$reviews[$n]['ReviewByName'].'</p></div>';
					else:
						$filter_this = date('Y', strtotime($reviews[$n]['EDate']));
						if( !in_array($filter_this,$hide_this) ):
							$result .= $reviews[$n]['ROverallComments'];
							$result .= '&rdquo;</blockquote><p class="name">'.$reviews[$n]['ReviewByName'].'</p></div>';
						endif;	
					endif;
				endif;
			endfor;
			return $result;
		else:
			return '<div class="review"><blockquote>No reviews yet at the moment</blockquote></div>';	
		endif;
		/* END DISPLAY ARRAY RESULT */
	}
	/* End Function getVillaReviews */
	
	/*
		Function name: getComplexVillaReviews
	*/
	public function getComplexVillaReviews($params)
	{
		/* SET XML Folder for Reviews */
		$xpath = MAIN_FOLDER."/villa-xml/".$params['villa_theme']."/";
		if(!is_dir($xpath))
			mkdir($xpath,0775);
		/* End SET XML Folder for Reviews */
		
		$request = "p_EstateID=".$params['id']."";
		$xml = $this->curly_tops('getVillaReviewsByEstate',$request,TRUE,TRUE,$params['id'].'_complex_reviews',$xpath,'prod');
		
		$reviews = array();
		$rc = 0;
		
		/* ANOTHER APPROACH */
		$rawiswar = '';
		$file = fopen( $xpath.$params['id'].'_complex_reviews.xml', 'r' ) or die('failed to open complex reviews cache file');
				
		while( $xd = fread( $file, 65535 ) ):
			$xd = trim($xd);
			$xd = preg_replace('/\s+/', ' ', $xd);
			$xd = str_replace('&nbsp;', ' ', $xd);
			$rawiswar .= $xd; 
		endwhile;
		fclose( $file );
		
		preg_match_all( "/\<REVIEW\>(.*?)\<\/REVIEW\>/s", $rawiswar, $vblocks );
	
		foreach( $vblocks[1] as $vblock ):
			preg_match_all( "/\<ApprStatusID\>(.*?)\<\/ApprStatusID\>/", $vblock, $status );
			preg_match_all( "/\<Name\>(.*?)\<\/Name\>/", $vblock, $name );
			
			if($status[1][0] == 'Approved' && $name[1][0] != 'Staff'):
				
				preg_match_all( "/\<SDate\>(.*?)\<\/SDate\>/", $vblock, $sdate );
				$reviews[$rc]['SDate'] = $sdate[1][0];
				
				preg_match_all( "/\<EDate\>(.*?)\<\/EDate\>/", $vblock, $edate );
				$reviews[$rc]['EDate'] = $edate[1][0];
				
				preg_match_all( "/\<ReviewByName\>(.*?)\<\/ReviewByName\>/", $vblock, $reviewbyname );
				$reviews[$rc]['ReviewByName'] = $reviewbyname[1][0];
				
				preg_match_all( "/\<ApprStatusID\>(.*?)\<\/ApprStatusID\>/", $vblock, $rstatus );
				$reviews[$rc]['ApprStatusID'] = $rstatus[1][0];
				
				preg_match_all( "/\<ROverallComments\>(.*?)\<\/ROverallComments\>/", $vblock, $rcomments );
				if( array_key_exists(0,$rcomments[1]) ):
					$reviews[$rc]['ROverallComments'] = $rcomments[1][0];
				else:
					$reviews[$rc]['ROverallComments'] = '';	
				endif;
				
				preg_match_all( "/\<Villa\>(.*?)\<\/Villa\>/", $vblock, $villa );
				if( array_key_exists(0,$villa[1]) ):
					$reviews[$rc]['StayedAt'] = $villa[1][0];
				else:
					$reviews[$rc]['StayedAt'] = '';
				endif;
				
				$rc++;
			endif;
		endforeach;
		/* END ANOTHER APPROACH */
		$reviews = $this->subval_sort($reviews,'SDate');
		/* DISPLAY ARRAY RESULT */
		$pages = array_chunk($reviews, 20);
		$index = (int)$params['page']-1;
		$spages = sizeof($pages[$index]);
		/* Let's build the pagiation links first */
		$pagination = '';
		$sp = sizeof($pages);
		if($sp > 1):
			$pagination .= 'Page: ';
			for($i=1; $i<($sp+1); $i++):
				if($i == $params['page'])
					$pagination .= '<strong>['.$i.']</strong> ';
				else
					$pagination .= '<a href="?page='.$i.'">'.$i.'</a> ';
			endfor;
		endif;
		/* End Pagination links */

		$result = '';
		if( $spages > 0 ):
			for( $n = 0; $n < $spages; $n++ ):
				$pages[$index] = $this->subval_sort($pages[$index],'SDate');
				if( $pages[$index][$n]['ROverallComments'] != '' ):
					$result .='<div class="review"><blockquote>';
					if( array_key_exists('SDate',$pages[$index][$n]) ):
						$result .= '<p class="dates"><strong>'.date("F jS, Y", strtotime($pages[$index][$n]['SDate'].' + 1 day')).' - '.date("F jS, Y", strtotime($pages[$index][$n]['EDate'].' + 1 day')).'</strong></p>&ldquo;';
					endif;
					$result .= $pages[$index][$n]['ROverallComments'];
					$result .= '&rdquo;</blockquote><p class="name">'.$pages[$index][$n]['ReviewByName'].' - Stayed at '.$pages[$index][$n]['StayedAt'].'</p></div>';
				endif;
			endfor;
			$result .='<br /><br /><div align="right" style="padding-right:15px;">'.$pagination.'</div>';
			return $result;
		else:
			return NULL;	
		endif;
		/* END DISPLAY ARRAY RESULT */
	}
	
	
	/*
		Function name: generate_links()
	*/
	public function generate_links($attr)
	{
		$source = "http://www.marketingvillas.com/links_rev.php";
		$source .= '?exc='.str_replace('uat.','www.',$_SERVER['SERVER_NAME']);
		
		if($attr['heading'] != '')
			$source .= '&heading='.$attr['heading'];
		
		if($attr['uriheading'] != '')
			$source .= '&url_heading='.$attr['uriheading'];
		else
			$source .= '&url_heading=h2';
		
		if($attr['what'] != '')
			$source .= '&what='.urlencode($attr['what']);
		
		$meme = $this->ret(ltrim($attr['sublocation']));
		$source .= '&location='.$meme['location'];
		$source .= '&area='.$meme['area'];
		//echo $source;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $source);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		
		return $output;
	}
	
	private function ret($where)
	{
		libxml_use_internal_errors(true);
		$xpath = VILLA_XML_PATH;
		$places = new DOMDocument();
		if( !$places->load(VILLA_XML_PATH.'countriesandcities.xml') ):
			$errors = libxml_get_errors();
			var_dump($errors);
		endif;
		$xpath = new DOMXpath($places);
		
		$loc = $xpath->evaluate("string(//COUNTRIES/COUNTRY/LOCATIONS/LOCATION/SUBLOCATIONS/SUBLOCATION[@name='$where']/@country)");
		$area = $xpath->evaluate("string(//COUNTRIES/COUNTRY/LOCATIONS/LOCATION/SUBLOCATIONS/SUBLOCATION[@name='$where']/@area)");
		if( $loc == '' ):
			$loc = $xpath->evaluate("string(//COUNTRIES/COUNTRY/LOCATIONS/LOCATION/SUBLOCATIONS/SUBLOCATION[@id='$where']/@country)");
			$area = $xpath->evaluate("string(//COUNTRIES/COUNTRY/LOCATIONS/LOCATION/SUBLOCATIONS/SUBLOCATION[@id='$where']/@area)");
		endif;
		
		return json_decode(trim( json_encode( array("location"=>$loc,"area"=>$area) ) ),true);
	}
	
	/*
		Function Name: mobile_calendar()
	*/
	function mobile_calendar( $data )
	{	
		/* Get Current Month and Year */
		$month = date('n');
		$year = date('o');
		$d = cal_days_in_month(CAL_GREGORIAN,$month,$year);
		$sdate = $year.'-'.$month.'-'.'01';
		//$edate = $year.'-'.$month.'-'.$d;
		$edate = date('Y-m-d',strtotime('+1460 day', strtotime($sdate)));
		/* End Get Current Month and Year */
		
		/* Let's build the params JSON string */
		$params = array("VillaID" => $data['villa_id'], "SDate" => $sdate, "EDate" => $edate);
		$p_Params = json_encode($params);
		$p_UserID = 'villaprtl';
		/* End params JSON string */
		
		$crequest = 'p_UserID=villaprtl&p_Params='.$p_Params;
		$cal = $this->cheeze_curls('getPropertyCalendar', $crequest, TRUE, FALSE, "", "", $data['db']);
		
		/* Start Build Date Range */
		$json_date_string = '';
		$ud_size = sizeof($cal['UnavailableDates']['UnavailableDate']);
		if(sizeof($cal) > 0 && array_key_exists('UnavailableDate',$cal['UnavailableDates'])):
			for($d=0; $d<$ud_size; $d++):
				if(array_key_exists($d,$cal['UnavailableDates']['UnavailableDate'])):
					$start = strtotime($cal['UnavailableDates']['UnavailableDate'][$d]['From']);
					$end = strtotime($cal['UnavailableDates']['UnavailableDate'][$d]['To']);
				else:
					$start = strtotime($cal['UnavailableDates']['UnavailableDate']['From']);
					$end = strtotime($cal['UnavailableDates']['UnavailableDate']['To']);	
				endif;
				while($start <= $end):
					$json_date_string .= date('j F Y', $start);
					if($d < sizeof($cal['UnavailableDates']['UnavailableDate']))
						$json_date_string .= ',';
						
					$start = strtotime('+1 day', $start);
				endwhile;
			endfor;
		endif;
		$json_date_string .= '';
		$json_date_string = substr($json_date_string,0,-1);
		/* End Build Date Range */
		
		/* Let's build the display */
		$html = '<style>
				a.ui-state-default {color: black !important;}
				.booked, .booked * {cursor: text !important;}
				.booked a.ui-state-default {background-color: #FF6666 !important;background: #FF6666 !important;}
				.avail a.ui-state-default {background-color: #BEF781 !important;background: #BEF781 !important;}
				.ui-datepicker-trigger{height:16px;width:16px;cursor:pointer;}
				
				.greenblack, .greenblackgreenblack
				{
					font-size:9px; 
					float:left; 
					width:19px; 
					height:23px;/*background-color:#FEFFE7;*/ 
					margin-right:1px; 
					text-align:center; 
					color:#000; 
					background-color:#BEF781; 
					border:solid 1px #BEF781;
				}
				
				.redblack
				{
					font-size:9px; 
					float:left; 
					width:19px; 
					height:23px;
					margin-right:1px; 
					text-align:center;  
					border:solid 1px #FF6666; 
					font-weight:bold;
					color:#000; /*background-image:url(../../images/calendar/red.png) */
					background-color:#FF6666
				}
				
				.redyellow
				{
					font-size:9px; 
					float:left; 
					width:19px; 
					height:23px;
					margin-right:1px; 
					text-align:center;  
					border:solid 1px #FF6666; 
					font-weight:bold;
					color:yellow; /*background-image:url(../../images/calendar/red.png)*/
					background-color:#FF6666
				}
				
				.rednavy
				{
					font-size:9px; 
					float:left; 
					width:19px; 
					height:23px;
					margin-right:1px; 
					text-align:center;  
					border:solid 1px #FF6666; 
					font-weight:bold;
					color:blue; /*background-image:url(../../images/calendar/red.png)*/
					background-color:#FF6666
				}
				
				
				.redyellowgreen
				{
					font-size:9px; 
					float:left; 
					width:19px; 
					height:23px;
					margin-right:1px; 
					text-align:center;  
					border:solid 1px #FF6666; 
					font-weight:bold;
					color:#00FF00; /*background-image:url(../../images/calendar/red.png) */
					background-color:#FF6666
				}
				
				.redred
				 {
					font-size:9px; 
					float:left; 
					width:19px; 
					height:23px;
					margin-right:1px; 
					text-align:center;  
					border:solid 1px #FF6666; 
					font-weight:bold;
					color:red; /*background-image:url(../../images/calendar/red.png) */
					background-color:#FF6666
				}
				
				.redwhite
				 {
					font-size:9px; 
					float:left; 
					width:19px; 
					height:23px;
					margin-right:1px; 
					text-align:center;  
					border:solid 1px #FF6666; 
					font-weight:bold;
					color:white; /*background-image:url(../../images/calendar/red.png)*/ 
					background-color:#FF6666
				}
				
				.hold
				{
					font-size:9px; 
					float:left; 
					width:19px; 
					height:23px;color:#000; 
					margin-right:1px; 
					text-align:center; 
					border:solid 1px #000; 
					background-color:yellow/*#CCCCFF*/
				}
				
				.yellowblack
				{
					font-size:9px; 
					float:left; 
					width:19px; 
					height:23px;
					margin-right:1px; 
					text-align:center;   
					border:solid 1px yellow; 
					font-weight:bold;
					color:#000; /*background-image:url(../../images/calendar/yellow.png)*/ 
					background-color:yellow
				}
				
				.yellowred
				{
					font-size:9px; 
					float:left; 
					width:19px; 
					height:23px;
					margin-right:1px; 
					text-align:center; 
					border:solid 1px yellow; 
					font-weight:bold;
					color:red; /*background-image:url(../../images/calendar/yellow.png)*/
					background-color:yellow
				}
				
				.blueblack
				{
					font-size:9px; 
					float:left; 
					width:19px; 
					height:23px;
					color:#000; 
					margin-right:1px; 
					text-align:center; 
					border:solid 1px #A9F5F2; 
					background-color:#A9F5F2/*#CCCCFF*/
				}
				</style>
				<script type="text/javascript" src="/wp-content/plugins/availability-calendar/js/jquery.availabilityCalendar.js"></script>';
				
		$html .= '<div align="center" >
					<input type="hidden" id="json_date_strings_'.$data['villa_id'].'" name="json_date_strings_'.$data['villa_id'].'" value="'.$json_date_string.'" />
					<input type="hidden" id="vid_'.$data['villa_id'].'" name="vid_'.$data['villa_id'].'" value="'.$data['villa_id'].'" />
					<div id="availCalendar_'.$data['villa_id'].'" align="center" style="width:100%"></div>';
		$html .= '<script type="text/javascript">kalendaryo("'.$data['villa_id'].'");</script>';			
		$dib = '<div style="width:50%">
					<fieldset>
						<legend>Colour Legends</legend>
						<table>
							<tr>
								<td style="background-color:#BEF781;" width="15%">&nbsp;</td>
								<td>Available/On Request</td>
							</tr>
							<tr>
								<td style="background-color:#FF6666;">&nbsp;</td>
								<td>Booked</td>
							</tr>
						</table>
					</fieldset>
				</div>';
		$html .='</div>';		
		/* End build display */				
		echo $html;
	}
	
	/* 
		======================================================================================================== 

		STRING MANIPULATION FUNCTIONS
		
		========================================================================================================
	*/
	public function prepare_FindAVilla($params)
	{
		$xml_string = "strCountryID=".$params['strCountryID']."&strLocationID=".$params['strLocationID']."&strSublocationID=".$params['strSublocationID']."&intBedRoom=".$params['intBedRoom']."&intMaxSleep=".$params['intMaxSleep']."&intMaxRate=".$params['intMaxRate']."&dteChkin=".$params['dteChkin']."&dteChkOut=".$params['dteChkOut']."&strCollection=".$params['strCollection']."&strAmenities=".$params['strAmenities']."&strCharacteristics=".$params['strCharacteristics']."&strCalUpdate=".$params['strCalUpdate']."";
		return $xml_string;
	}
	
	/* QuickSearch Request String */
	public function prepare_QuickSearch($params)
	{
		$xml_string = "strSearch=".$params['strSearch'];
		return $xml_string;				
	}
	
	/* Security_GetMD5Hash Request String */
	public function prepare_Security_GetMD5Hash($params)
	{
		$xml_string = "p_ToHash=".$params['p_ToHash']."";
		return $xml_string;
	}
	
	/* generateRatesBreakDown Request String */
	public function prepare_generateRatesBreakDown($params)
	{
		$xml_string = "p_VillaID=".$params['p_VillaID']."&p_CIDate=".$params['p_CIDate']."&p_CODate=".$params['p_CODate']."";
		return $xml_string;
	}
	
	/* getCalendarAvailability Request String */
	public function prepare_getCalendarAvailability($params)
	{
		$xml_string = "StartDate=".$params['StartDate']."&Days=".$params['Days']."&VillaGroup=".$params['VillaGroup']."&VillaSubGroup=".$params['VillaSubGroup']."&VillaID=".$params['VillaID']."&CountryID=".$params['CountryID']."&LocationID=".$params['LocationID']."&BedRm=".$params['BedRm']."&SubLoc=".$params['SubLoc']."&Agent=".$params['Agent']."";
		return $xml_string;
	}
	
	/* getFlipKeyAvailability Request String */
	public function prepare_getFlipKeyAvailability($params)
	{
		$xml_string = "strVillaID=".$params['strVillaID']."";
		return $xml_string;
	}
	
	/* getFlipKeyVillaInfo Request String */
	public function prepare_getFlipKeyVillaInfo($params)
	{
		$xml_string = "strVillaID=".$params['strVillaID']."";
		return $xml_string;
	}
	
	/* getUserInfo Request String */
	public function prepare_getUserInfo($params)
	{
		$xml_string = "strEmail=".$params['strEmail']."&strPassword=".$params['strPassword']."";
		return $xml_string;
	}
	
	/* getVilla Request String */
	public function prepare_getVilla($params)
	{
		$xml_string = "strURL=".$params['strURL']."";
		return $xml_string;
	}
	
	/* getVillaFacilities Request String */
	public function prepare_getVillaFacilities($params)
	{
		$xml_string = "strVillaID=".$params['strVillaID']."";
		return $xml_string;
	}
	
	/* getVillaFloorPlan Request String */
	public function prepare_getVillaFloorPlan($params)
	{
		$xml_string = "strVillaURL=".$params['strVillaURL']."";
		return $xml_string;
	}
	
	/* getVillaInfo Request String */
	public function prepare_getVillaInfo($params)
	{
		$xml_string = "strVillaID=".$params['strVillaID']."";
		return $xml_string;
	}
	
	/* getVillaLocation Request String */
	public function prepare_getVillaLocation($params)
	{
		$xml_string = "strVillaID=".$params['strVillaID']."";
		return $xml_string;
	}
	
	/* getVillaMap Request String */
	public function prepare_getVillaMap($params)
	{
		$xml_string = "strVillaURL=".$params['strVillaURL']."";
		return $xml_string;
	}
	
	/* getVillaRates Request String */
	public function prepare_getVillaRates($params)
	{
		$xml_string = "strVillaID=".$params['strVillaID']."";
		return $xml_string;
	}
	
	/* getVillaReviews Requst String */
	public function prepare_getVillaReviews($params)
	{
		$xml_string = "strVillaID=".$params['strVillaID']."";
		return $xml_string;
	}
	
	/* getVillaRoomList Request String */
	public function prepare_getVillaRoomList($params)
	{
		$xml_string = "strVillaID=".$params['strVillaID']."";
		return $xml_string;
	}
	
	/* getVillaUnavailableDates Request String */
	public function prepare_getVillaUnavailableDates($params)
	{
		$xml_string = "p_VillaID=".$params['p_VillaID']."&p_EquateHoldToBook=".$params['p_EquateHoldToBook']."";
		return $xml_string;
	}
	
	/* getVillaVideo Request String */
	public function prepare_getVillaVideo($params)
	{
		$xml_string = "strVillaURL=".$params['strVillaURL']."";
		return $xml_string;
	}
	
	/* getVillasByBedrooms Request String */
	public function prepare_getVillasByBedrooms($params)
	{
		$xml_string = "intBedrm=".$params['intBedrm']."";
		return $xml_string;
	}
	
	/* getVillasByCharacteristics Request String */
	public function prepare_getVillasByCharacteristics($params)
	{
		$xml_string = "strCharacteristic=".$params['strCharacteristic']."";
		return $xml_string;
	}
	
	/* getVillasByCollection Request String */
	public function prepare_getVillasByCollection($params)
	{
		$xml_string = "strCollectionID=".$params['strCollectionID']."";
		return $xml_string;
	}
	
	/* getVillasByCountry Request String */
	public function prepare_getVillasByCountry($params)
	{
		$xml_string = "strCountry=".$params['strCountry']."";
		return $xml_string;
	}
	
	/* getVillasByLocation Request String */
	public function prepare_getVillasByLocation($params)
	{
		$xml_string = "strLocation=".$params['strLocation']."";
		return $xml_string;
	}
	
	/* getVillasBySubLocation Request String */
	public function prepare_getVillasBySubLocation($params)
	{
		$xml_string = "strLocation=".$params['strLocation']."";
		return $xml_string;
	}
	
	/* insertNewBooking Request String */
	public function prepare_insertNewBooking($params)
	{
		$xml_string = "p_Token=".$params['p_Token']."&p_UserID=".$params['p_UserID']."&p_VillaID=".$params['p_VillaID']."&p_CIDate=".$params['p_CIDate']."&p_CODate=".$params['p_CODate']."&p_GuestFirstName=".$params['p_GuestFirstName']."&p_GuestLastName=".$params['p_GuestLastName']."&p_Email=".$params['p_Email']."&p_CountryOfResidence=".$params['p_CountryOfResidence']."&p_MobileNo=".$params['p_MobileNo']."&p_TelNo=".$params['p_TelNo']."&p_BookingSourceID=".$params['p_BookingSourceID']."&p_TotalPax=".$params['p_TotalPax']."&p_TotalChild=".$params['p_TotalChild']."&p_TotalInfant=".$params['p_TotalInfant']."&p_SpecialRequest=".$params['p_SpecialRequest']."&p_MarketingMediaID=".$params['p_MarketingMediaID']."&p_AffID=".$params['p_AffID']."";
		return $xml_string;
	}
	
	/* insertNewEmail Request String */
	public function prepare_insertNewEmail($params)
	{
		$xml_string = "p_Token=".$params['p_Token']."&p_UserID=".$params['p_UserID']."&p_SenderName=".$params['p_SenderName']."&p_SenderEmail=".$params['p_SenderEmail']."&p_RecipientName=".$params['p_RecipientName']."&p_RecipientEmail=".$params['p_RecipientEmail']."&p_Subject=".$params['p_Subject']."&p_Body=".$params['p_Body']."&p_BookingID=".$params['p_BookingID']."";
		return $xml_string;
	}
	
	/* sendAutoEmail Request String */
	public function prepare_sendAutoEmail($params)
	{
		$xml_string = "p_Token=".$params['p_Token']."&p_UserID=".$params['p_UserID']."&p_BookingID=".$params['p_BookingID']."";
		return $xml_string;
	}
	
	/* New bookings */
	public function prepare_newBooking($params)
	{
		$req_string = "villaid=".$params['villaid']."&cidate=".$params['cidate']."&codate=".$params['codate']."&firstname=".$params['firstname']."&lastname=".$params['lastname']."&countryid=".$params['countryid']."&emailaddress=".$params['emailaddress']."&telno=".$params['telno']."&totaladults=".$params['totaladults']."&bsid=".$params['bsid']."&message=".$params['message']."&rurl=".$params['rurl']."";
		return $req_string;
	}
}

// End of file
