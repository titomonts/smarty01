<?php
/*
	Author: Monch
	Description: Main script file
	Date: 2017 - Present
*/
require 'config/config.php';
require 'functions/vw-functions.php';
require 'mailjet-api/vendor/autoload.php';
use \Mailjet\Resources;

$data['page'] = isset($_GET['page'])?$_GET['page']:'index';
$data['page'] = $data['child']=='view-full-site'?'index':$data['page'];
$data['child'] = isset($_GET['child'])?$_GET['child']:'';
$data['sibling'] = isset($_GET['sibling'])?$_GET['sibling']:'';
$data['url_lvl_1'] = $data['page'];
$data['url_lvl_2'] = $data['child'];
$data['url_lvl_3'] = $data['sibling'];

/* Check if an existing redirect is present */
$data['requestUrl'] = $_SERVER['REQUEST_URI'];
$redirect = $data['requestUrl']!=''?redirects( $data ):NULL;
if( $redirect != NULL ):
	header("Location:".$redirect['re_destination_url'],TRUE,$redirect['status']);
    exit();
/*add for index.html*/
elseif( $data['requestUrl'] == '/index.html' ):
    header("HTTP/1.1 301 Moved Permanently"); 
	header("Location: /",TRUE,$redirect['status']);
	exit();
endif;
/* End check for existing redirect */

$cid = 'INTRO';
$data['cache'] = $data['villa_id'];
$data['origin'] = $data['page'];
$por_ow_por = TRUE;
for($ps=0; $ps<sizeof($page_structure); $ps++):
	
	if( !empty($data['page']) && !empty($page_structure[$ps]['page_identifier']) ):
		if( array_key_exists('villaID',$page_structure[$ps]) ):
			if( $data['origin'] == $page_structure[$ps]['page_identifier'] ):
				$data['cache'] = $page_structure[$ps]['villaID'];
				$cid = $page_structure[$ps]['content_id'];
				$template = $page_structure[$ps]['template'];
				$data['title'] = $page_structure[$ps]['page_title'];
				$data['heading'] = $page_structure[$ps]['page_header'];
				$por_ow_por = FALSE;
			endif;
		elseif( $data['page'] == $page_structure[$ps]['page_identifier']  ):
			$cid = $page_structure[$ps]['content_id'];
			$template = $page_structure[$ps]['template'];
			$data['title'] = $page_structure[$ps]['page_title'];
			$data['heading'] = $page_structure[$ps]['page_header'];
			$por_ow_por = FALSE;
		endif;
	endif;
	
	/* Check if a child exists */
	if( array_key_exists('children',$page_structure[$ps]) || !empty($data['child']) ):
		$data['child'] = empty($data['child'])?$data['page']:$data['child'];
		for( $sp=0; $sp<sizeof($page_structure[$ps]['children']); $sp++ ):
			if( array_key_exists('villaID',$page_structure[$ps]['children'][$sp]) && $data['origin'] == $page_structure[$ps]['children'][$sp]['parent'] && empty($data['sibling'])):
				if( $data['child'] == $page_structure[$ps]['children'][$sp]['page_identifier'] ):
					$cid = $page_structure[$ps]['children'][$sp]['content_id'];
					$template = $page_structure[$ps]['children'][$sp]['template'];
					$data['cache'] =  $page_structure[$ps]['children'][$sp]['villaID'];
					$data['page'] = $data['child'];
					$data['parent'] = $page_structure[$ps]['page_identifier'];
					$data['title'] = $page_structure[$ps]['children'][$sp]['page_title'];
					$data['heading'] = $page_structure[$ps]['children'][$sp]['page_header'];
					$por_ow_por = FALSE;
				endif;
			elseif( !array_key_exists('villaID',$page_structure[$ps]['children'][$sp]) && (array_key_exists('parent',$page_structure[$ps]['children'][$sp]) && $data['origin'] == $page_structure[$ps]['children'][$sp]['parent']) && $data['child'] == $page_structure[$ps]['children'][$sp]['page_identifier'] && empty($data['sibling']) ):
				$cid = $page_structure[$ps]['children'][$sp]['content_id'];
				$template = $page_structure[$ps]['children'][$sp]['template'];
				$data['page'] = $data['child'];
				$data['parent'] = $page_structure[$ps]['page_identifier'];
				$data['title'] = $page_structure[$ps]['children'][$sp]['page_title'];
				$data['heading'] = $page_structure[$ps]['children'][$sp]['page_header'];
				$por_ow_por = FALSE;
			elseif( !array_key_exists('villaID',$page_structure[$ps]['children'][$sp]) && !array_key_exists('parent',$page_structure[$ps]['children'][$sp]) && $data['child'] == $page_structure[$ps]['children'][$sp]['page_identifier'] && $data['origin'] == $data['child']  && empty($data['sibling']) ):
				$cid = $page_structure[$ps]['children'][$sp]['content_id'];
				$template = $page_structure[$ps]['children'][$sp]['template'];
				$data['page'] = $data['child'];
				$data['parent'] = $page_structure[$ps]['page_identifier'];
				$data['title'] = $page_structure[$ps]['children'][$sp]['page_title'];
				$data['heading'] = $page_structure[$ps]['children'][$sp]['page_header'];
				$por_ow_por = FALSE;
			endif;
			/* Check for sibling */
			if( array_key_exists('children',$page_structure[$ps]['children'][$sp]) || !empty($data['sibling']) ):
				$data['origin'] = $data['child'];
				for( $z=0; $z<sizeof($page_structure[$ps]['children'][$sp]['children']); $z++ ):
					if( $data['sibling'] == $page_structure[$ps]['children'][$sp]['children'][$z]['page_identifier'] ):
						$cid = $page_structure[$ps]['children'][$sp]['children'][$z]['content_id'];
						$template = $page_structure[$ps]['children'][$sp]['children'][$z]['template'];
						$data['cache'] =  $page_structure[$ps]['children'][$sp]['children'][$z]['villaID'];
						//$data['page'] = $data['sibling'];
						$data['parent'] = $page_structure[$ps]['children'][$sp]['children'][$z]['parent'];
						$data['title'] = $page_structure[$ps]['children'][$sp]['children'][$z]['page_title'];
						$data['heading'] = $page_structure[$ps]['children'][$sp]['children'][$z]['page_header'];
						$por_ow_por = FALSE;
					endif;
				endfor;
			endif;
			/* End check for sibling */
		endfor;
	endif;
	/* End Check if a child exists */
	
endfor;

$params['theme'] = $data['theme']=='ehteaser'?'ehteaser':$data['theme'];
$params['cacheName'] = $data['cache'];
$params['content'] = $cid;
$params['ismobile'] = ($mobile->isMobile() ? ($mobile->isTablet() ? FALSE : TRUE) : FALSE);
$data['content_id'] = $cid;
$content = $cid!=''?$website->get_contents($params):'';

if( $data['page'] == 'photo-gallery' || $data['page'] == 'gallery' ):

	$data['nggpage'] = 0;
	$data['num_pages'] = 1;
	$split = explode('?', basename($_SERVER['REQUEST_URI']));
	if(!empty($split[1])):
		$split_a = explode('=', $split[1]);
		$data['nggpage'] = (int)$split_a[1]-1;
	endif;
	
	if( $data['complex'] == 'yes' ):
		$counts = 0;
		$sg = sizeof($gallery);
		for( $x=0; $x<$sg; $x++ ):
			if( $data['cache'] == $gallery[$x]['villaID'] ):
				$counts++;
			elseif( $data['cache'] != $gallery[$x]['villaID'] ):
				unset($gallery[$x]);	
			endif;
		endfor;
		$gallery = array_values($gallery);
        if ($data['gallery_image_per_page'] > 0):
            $pgallery = array_chunk($gallery,$data['gallery_image_per_page']);
            $data['num_pages'] = ceil($counts/$data['gallery_image_per_page']);
        endif;
	else:
        if ($data['gallery_image_per_page'] > 0):
		  $data['num_pages'] = ceil(sizeof($gallery)/$data['gallery_image_per_page']);
        endif;
	endif;
	
	$data['nggpage'] = $data['nggpage']<=0?0:$data['nggpage'];
	$data['cur_page'] = $data['nggpage']+1;
	$smarty->assign("gallery",$pgallery[ $data['nggpage'] ]);
endif;

if( $data['page'] == 'availability-calendar' && $data['complex'] == 'yes' ):
	$vars['vids'] = $data['estate_villa_ids'];
	if(isset($_POST['btnGo'])):
		$vars['month'] = $_POST['selMonth'];
		$_SESSION['month'] = $vars['month'];
		$vars['year'] = $_POST['selYear'];
		$_SESSION['year'] = $vars['year'];
	endif;
	
	$vars['month'] = isset($_SESSION['month'])?$_SESSION['month']:date('n');
	$vars['year'] = isset($_SESSION['year'])?$_SESSION['year']:date('Y');
	
	switch($vars['month']):
		case 1:
			$selectedMonth = "January";
		break;
		case 2:
			$selectedMonth = "February";
		break;
		case 3:
			$selectedMonth = "March";
		break;
		case 4:
			$selectedMonth = "April";
		break;
		case 5:
			$selectedMonth = "May";
		break;
		case 6:
			$selectedMonth = "June";
		break;
		case 7:
			$selectedMonth = "July";
		break;
		case 8:
			$selectedMonth = "August";
		break;
		case 9:
			$selectedMonth = "September";
		break;
		case 10:
			$selectedMonth = "October";
		break;
		case 11:
			$selectedMonth = "November";
		break;
		case 12:
			$selectedMonth = "December";
		break;
	endswitch;
	
	$vars['monthOpts'] = "";
	$vars['yearOpts'] = "";
	for($m=1; $m<=12; $m++):
		switch($m):
			case 1:
				$monthName = "January";
			break;
			case 2:
				$monthName = "February";
			break;
			case 3:
				$monthName = "March";
			break;
			case 4:
				$monthName = "April";
			break;
			case 5:
				$monthName = "May";
			break;
			case 6:
				$monthName = "June";
			break;
			case 7:
				$monthName = "July";
			break;
			case 8:
				$monthName = "August";
			break;
			case 9:
				$monthName = "September";
			break;
			case 10:
				$monthName = "October";
			break;
			case 11:
				$monthName = "November";
			break;
			case 12:
				$monthName = "December";
			break;
		endswitch;
		if($m == $vars['month']):
			$vars['monthOpts'] .= '<option value="'.$m.'" selected="selected">'.$monthName.'</option>';
			$vars['selectedMonth'] = $monthName;
		else:
			$vars['monthOpts'] .= '<option value="'.$m.'">'.$monthName.'</option>';
		endif;
	endfor;
	$now = date('Y');
	for($y=$now; $y<=($vars['year']+2); $y++):
		if($y == $vars['year']):
			$vars['yearOpts'] .='<option value="'.$y.'" selected="selected">'.$y.'</option>';
		else:
			$vars['yearOpts'] .='<option value="'.$y.'">'.$y.'</option>';
		endif;
	endfor;
	
	$content = $forms->complex_calendar($vars);
endif;

if( $data['page'] == 'rates' ):
	$params['db'] = $data['api_db'];
	if( $data['complex'] == 'no' ):
		$params['id'] = $data['villa_id'];
		$params['villa_theme'] = $data['theme'];
		$content = $rates->get_villa_rates($params);
	elseif( $data['complex'] == 'yes' ):
		$params['villa_id'] = $data['villa_id'];
		$params['estate_id'] = $data['estate_id'];
		$params['villa_theme'] = $data['theme'];
		$params['promovillas'] = $data['promovillas'];
		$content = $rates->estate_rates($params);
	endif;
endif;

if( $data['page'] == 'reservations' || $data['child'] == 'reservations'):
	//$vName = $data['villa_name'];
	if( $data['theme'] == 'ehteaser' ):
		if( $data['complex'] == 'yes' ):
			$vName = $data['teaser_data']['Villa']['Info'][0]['Name'];
		else:
			$vName = $data['teaser_data']['Villa']['Info']['Name'];
		endif;
	else:
		$vName = $data['villa_name'];
	endif;
	if( $data['complex'] == 'yes' ):
		$villaArray = array();
		$ivids = explode(',',$data['estate_villa_ids']);
		$svids = sizeof($ivids);
		
		for( $c=0; $c<$svids; $c++ ):
			$vinfo = $website->ws_villa_info($ivids[$c]);
			$villaArray[$c]['id'] = $vinfo['gaData']['property']['id'];
			$villaArray[$c]['villa_name'] = $vinfo['gaData']['property']['name'];	
		endfor;
	endif;
	$args = [];
	$args['villa_id'] = $data['villa_id'];
	$args['villa_name'] = $vName;
	$args['google_site_key'] = $data['google_site_key'];
	$content = $forms->display_reservation_form($data['villa_id'],$vName,($data['complex']=='yes'?$villaArray:''),(isset($_POST)?$_POST:""), $data['google_site_key']);
endif;

if( $data['page'] == 'reservation-sent' ):
	if( isset($_POST['reserve']) ):
		if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])):
			$verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$data['google_secret_key'].'&response='.$_POST['g-recaptcha-response']);
			$responseData = json_decode($verifyResponse);
		
			$v_email = array("email"=>trim($_POST['txtEmail']));
			$p_Params = json_encode($v_email);
			
			$request = "p_Params=".$p_Params;
			$isValidEmail = $website->cheeze_curls("isValidEmailAddress",$request,TRUE,FALSE,"","","prod");
			
			$fparams = array();
			$fparams = $_POST;
			
			if ( $isValidEmail[0] == 'True' && $responseData->success ):echo 'aw adi';
				$apikey = '502f4bbd2e9081a0342612a0207baef0';
				$apisecret = '117291b27298cd0944e3ec73cc0bf7db';
				$mailjet = new \Mailjet\Client($apikey, $apisecret);
				
				$filename = md5(strtotime(date('Y-m-d,H:i:s')));
				$fparams['hkey'] = $filename;
				$mail_tmp_file = '/var/www/html/@admin/bookings_log/'.$filename.'.txt';
				$fh = fopen($mail_tmp_file, 'x+') or die("can't open file");
				
				/* for complex villas */
				if( isset($_POST['villaids']) ): 
					$vills = "";
					$svills = sizeof($_POST['villaids']);
					for($c=0; $c<$svill; $c++):
						if(isset($_POST['villaids'][$c])):
							$vills .= $_POST['villaids'][$c].', ';
						endif;
					endfor;
					$vills = substr($vills,0,-1);
				endif;
				/* end for complex villas */
				$sReq = stripslashes( strip_tags($_POST['txtMessage']).(!empty($vills)?', Selected villas: '.$vills:'') );
				$sReq = preg_replace('/[^\00-\255]+/u','',$sReq);
				$sReq = preg_replace('/[^\\x20-\\x7E]/', '', $sReq);
				$adults = empty($_POST['numAdult'])?'2':$_POST['numAdult'];
				$telefoon = $_POST['txtPhoneAreaCode'].$_POST['txtPhoneNumber'];
				$altNum = empty($_POST['txtAltNumber'])?$_POST['txtPhoneNumber']:"";
				$referrer = isset($_SERVER['HTTP_REFERER'])?urlencode($_SERVER['HTTP_REFERER']):"";
				$email = trim($_POST['txtEmail']);
				$xml_string = '[
				{
					"hash_key":"'.$filename.'",
					"villa_id":"'.$_POST['villaID'].'",
					"arrival_date":"'.$_POST['txtArrivalDate'].'",
					"departure_date":"'.$_POST['txtDepartDate'].'",
					"firstname":"'.$_POST['txtFirstname'].'",
					"lastname":"'.$_POST['txtLastName'].'",
					"country":"'.$_POST['selCountry'].'",
					"email":"'.$email.'",
					"telephone_num":"'.$telefoon.'",
					"total_adults":"'.$adults.'",
					"bsid":"11",
					"alternate_number":"'.$altNum.'",
					"total_bedrooms":"1",
					"message":"'.$sReq.'",
					"returning_customer":"N",
					"total_children":"'.$_POST['numChildren'].'",
					"total_infants":"'.$_POST['numInfant'].'",
					"referrer":"'.$referrer.'"
				}
				]';
				fwrite($fh, $xml_string);
				fclose($fh);
				
				$recipient = 'backup-inquiry@marketingvillas.com';
				//$recipient = 'monchdacumos@gmail.com';
				$body = [
							'FromEmail' => "website@marketingvillas.com",
							'FromName' => "Villa Website Enquiry",
							'Subject' => "A new Villa Enquiry from ".$_POST['hidVillaName']."",
							'Text-part' => "A new villa enquiry!",
							'Html-part' => "<h3>Details</h3><br />".$xml_string,
							'Recipients' => [
								[
									'Email' => $recipient,
								]
							],
							'Attachments' => array(attachment($filename.'.txt', $mail_tmp_file))
						];
				
				$mailStat = '';
				$response = $mailjet->post(Mailjet\Resources::$Email, ['body' => $body]);
				if ($response->success()):
					$mailStat = $response->getStatus();
				else:
					echo 'ERROR! ERROR! '.print_r($response->getStatus(),true);
				endif;
		
				$fparams['rurl'] = $referrer;
				$fparams['db'] = $data['api_db'];
				$newBooking = $website->reserve_process($fparams);
                
				if ($newBooking['@attributes']['status'] != 'error'):
					$data['sendToAnalytics'] = TRUE;
					$content = $newBooking['thank_you_message'];
				endif;
			elseif( $isValidEmail[0] != 'True' && !empty($_POST) && !isset($_GET['VillabkID'])):
				$error['form_error'] = 'Enquiry Form';
				$error['return_url'] = '/reservations.html';
				$error['extra_msg'] = 'Invalid Email and/or Incomplete Information';
				$website->houston_we_have_a_problem($error);
			elseif( empty($_POST) && $isValidEmail[0] != 'True' && isset($_GET['VillabkID']) && $_GET['VillabkID'] != '' ):
				$content = '<p>Your Reservation Enquiry Form has been successfully sent for '.$data['villa_name'].'.</p>
					<p>The Elite Havens Group, luxury villa rentals, manage all the reservations for '.$data['villa_name'].'. One of our villa specialists will be in touch shortly.</p>
					<p>Your Reference I.D. is <strong>'.$_GET['VillabkID'].'</strong></p>
					<p>The Elite Havens Group presents a stunning portfolio of luxury private villas throughout Bali and Lombok in Indonesia, Thailand, Sri Lanka and Maldives. Staffed to the highest quality, each villa offers a blissfully relaxing and highly individual experience. Ranging in size from one to nine bedrooms and boasting private pools, luxurious living spaces, equipped kitchens (with chef) and tropical gardens, our villas are situated in the heart of the action, beside blissful beaches, upon jungle-clad hillsides and amongst idyllic rural landscapes ensuring the perfect holiday experience for all.</p>';
			endif;
		elseif( isset($_GET['VillabkID']) && $_GET['VillabkID'] != '' ):
			$content = '<p>Your Reservation Enquiry Form has been successfully sent for '.$data['villa_name'].'.</p>
					<p>The Elite Havens Group, luxury villa rentals, manage all the reservations for '.$data['villa_name'].'. One of our villa specialists will be in touch shortly.</p>
					<p>Your Reference I.D. is <strong>'.$_GET['VillabkID'].'</strong></p>
					<p>The Elite Havens Group presents a stunning portfolio of luxury private villas throughout Bali and Lombok in Indonesia, Thailand, Sri Lanka and Maldives. Staffed to the highest quality, each villa offers a blissfully relaxing and highly individual experience. Ranging in size from one to nine bedrooms and boasting private pools, luxurious living spaces, equipped kitchens (with chef) and tropical gardens, our villas are situated in the heart of the action, beside blissful beaches, upon jungle-clad hillsides and amongst idyllic rural landscapes ensuring the perfect holiday experience for all.</p>';
		else:
			$error['form_error'] = 'Enquiry Form';
			$error['return_url'] = '/reservations.html';
			$error['extra_msg'] = 'Captcha validation error!';
			$website->houston_we_have_a_problem($error);
		endif;
	elseif( empty($_POST) && isset($_GET['VillabkID']) && $_GET['VillabkID'] != '' ):
				$content = '<p>Your Reservation Enquiry Form has been successfully sent for '.$data['villa_name'].'.</p>
					<p>The Elite Havens Group, luxury villa rentals, manage all the reservations for '.$data['villa_name'].'. One of our villa specialists will be in touch shortly.</p>
					<p>Your Reference I.D. is <strong>'.$_GET['VillabkID'].'</strong></p>
					<p>The Elite Havens Group presents a stunning portfolio of luxury private villas throughout Bali and Lombok in Indonesia, Thailand, Sri Lanka and Maldives. Staffed to the highest quality, each villa offers a blissfully relaxing and highly individual experience. Ranging in size from one to nine bedrooms and boasting private pools, luxurious living spaces, equipped kitchens (with chef) and tropical gardens, our villas are situated in the heart of the action, beside blissful beaches, upon jungle-clad hillsides and amongst idyllic rural landscapes ensuring the perfect holiday experience for all.</p>';
    
    else:
        $error['form_error'] = 'Enquiry Form';
        $error['return_url'] = '/reservations.html';
        $error['extra_msg'] = 'Captcha validation error!';
        $website->houston_we_have_a_problem($error);
	endif;	
endif;

if (strpos($data['page'], 'subscribed') !== false):
	$s_email = array("email"=>trim($_POST['subscriber']));
	$s_Params = json_encode($s_email);
	
	$srequest = "p_Params=".$s_Params;
	$isValidEmail = $website->cheeze_curls("isValidEmailAddress",$srequest,TRUE,FALSE,"","","prod");
	
	if( isset($_POST['subscriber']) && $isValidEmail[0] == 'True' ):
		$apikey = '502f4bbd2e9081a0342612a0207baef0';
		$apisecret = '117291b27298cd0944e3ec73cc0bf7db';
		$mailjet = new \Mailjet\Client($apikey, $apisecret);
		
		//$recipient = 'reservations@elitehavens.com';
		$recipient = 'lidya.evelyn@elitehavens.com';
		$html_content = 'New subscriber:<strong> ' . htmlspecialchars($_POST["subscriber"]) . '</strong>';
		$body = [
					'FromEmail' => "website-subscription@elitehavens.com",
					'FromName' => $data['villa_name'],
					'Subject' => "A new subscriber ".$data['villa_name'],
					'Text-part' => "A new villa subscription!",
					'Html-part' => $html_content,
					'Recipients' => [
										['Email' => $recipient]
									]
				];
				
		$mailStat = '';
		$response = $mailjet->post(Mailjet\Resources::$Email, ['body' => $body]);
		if ($response->success()):
			$mailStat = $response->getStatus();
		else:
			echo 'ERROR! ERROR! '.print_r($response->getStatus(),true);
		endif;
		
		/* Send thank you email to subscriber */
		$srecipient = trim($_POST['subscriber']);
		$shtml_content = base64_decode($data['site_email_template']);
		$sbody = [
					'FromEmail' => "reservations@elitehavens.com",
					'FromName' => $data['villa_name']." Subscribe",
					'Subject' => "Thank you for signing up",
					'Text-part' => "Thank you for signing up",
					'Html-part' => $shtml_content,
					'Recipients' => [
										['Email' => $srecipient]
									]
				 ];
				 
		$smailStat = '';
		$sresponse = $mailjet->post(Mailjet\Resources::$Email, ['body' => $sbody]);
		if ($sresponse->success()):
			$smailStat = $sresponse->getStatus();
		else:
			echo 'ERROR! ERROR! '.print_r($sresponse->getStatus(),true);
		endif;
		/* End send thank you email to subscriber */
	else:
		header("Location:/");
	endif;
endif;

if( $data['page'] == 'enquire-now' ):
	$eform = $forms->display_reservation_form($data['villa_id'], $data['villa_name'],'',TRUE);
	$smarty->assign('eform',$eform);
endif;

if( $data['page'] == 'mobile-sent' ):
	if (isset($_POST['submit'])):
		$newBooking = $website->reserve_process($_POST);
		if ($newBooking['@attributes']['status'] != 'error'):
			$sendToAnalytics = true;
			$content = $newBooking['thank_you_message'];
		endif;
	endif;
endif;

if( $data['page'] == 'general-enquiries' ):
	$params = array();
	$params['villaID'] = $data['villa_id'];
	$params['villaName'] = $data['villa_name'];
	$params['google_site_key'] = $data['google_site_key'];
	$params['isComplex'] = false;
	$content = $forms->display_general_enquiries_form($params);
endif;

if( $data['page'] == 'contact-sent' ):
	if( isset($_POST['sir_yes_sir']) ):
		if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])):
			$verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$data['google_secret_key'].'&response='.$_POST['g-recaptcha-response']);
			$responseData = json_decode($verifyResponse);
			
			$v_email = array("email"=>trim($_POST['txtEmail']));
			$p_Params = json_encode($v_email);
			$request = "p_Params=".$p_Params;
			$isValidEmail = $website->cheeze_curls("isValidEmailAddress",$request,TRUE,FALSE,"","","prod");
			
			if( $responseData->success && $isValidEmail[0] == 'True'  ):
				$newBooking = $website->general_enquiry_process($_POST);
				if ($newBooking['@attributes']['status'] != 'error'):
					$data['sendToAnalytics'] = true;
					$content = $newBooking['thank_you_message'];
				endif;
			else:
				$error['form_error'] = 'General Enquiry Form';
                		$error['return_url'] = '/general-enquiries.html';
                		$error['extra_msg'] = 'Invalid Email';
                		$website->houston_we_have_a_problem($error);	
			endif;
		endif;
	endif;
endif;

if( $data['page'] == 'events-process' ):
	if (isset($_POST['inquire'])):
		$newBooking = $website->events_process($_POST);
		if ($newBooking['@attributes']['status'] != 'error'):
			$data['sendToAnalytics'] = true;
			$content = $newBooking['thank_you_message'];
		endif;
	endif;
endif;

if( $data['page'] == 'links' ):
	$params['exc'] = $data['villa_name'];
	$params['sublocation'] = $data['sublocation'];
	$params['heading'] = 'h2';
	$content = $website->generate_links($params);
endif;

if( $data['page'] == 'sitemap' ):
	//$template = 'sitemap.html';
endif;

if( $data['page'] == 'guest-reviews' ):
	$split = explode('?', basename($_SERVER['REQUEST_URI']));
	if(!empty($split[1])):
		$split_a = explode('=', $split[1]);
		$params['page'] = (int)$split_a[1];
	else:
		$params['page'] = 1;	
	endif;
	$params['id'] = $data['villa_id'];
	$params['villa_theme'] = $data['theme'];
	$params['hide_this_year'] = '';
	$content = $data['complex']=='no'?$website->getVillaReviews($params):$website->getComplexVillaReviews($params);
endif;

if (isset($_GET['VillabkID'])):
	$data['sendToAnalytics'] = true;
	$rfid = $_GET['VillabkID'];
else:
	$rfid =  $data['sendToAnalytics'] ? $newBooking['Transactions']['InquiryID'] : ( isset($_GET['rfid'])?$_GET['rfid']:"" );
endif;

if( $rfid != "" ):
	$params['p_UserID'] = 'villaprtl';
	$request = '&p_UserID='.$params['p_UserID'].'&p_Params='.json_encode( array("BookingID" => $rfid) );
	$gaInfo = $website->cheeze_curls('getBookingDataForGAnalytics',$request,TRUE,FALSE,'','','prod');
endif;

$gtm = $website->render_gtm($data['sendToAnalytics'], $data, ( !empty($gaInfo)?$gaInfo:'' ), ( !empty($_POST)?$_POST:'' ) );
$ga = $website->classic_analytics($data['sendToAnalytics'], $data, ( !empty($gaInfo)?$gaInfo:'' ) );

if($data['theme'] != 'ehteaser' || $data['theme'] != 'ehteaser_v1'):
         $smarty->configLoad(WEBSITE.$data['site_configuration']);
else:
        $smarty->configLoad(WEBSITE.'ehteaser.conf');
endif;

if(!$por_ow_por):
	$smarty->assign('data', $data);
	$smarty->assign('gtm', $gtm);
	$smarty->assign('ga', $ga);
	$smarty->assign('content', $content);
	$smarty->display(WEBSITE.$template);
else:
	header('HTTP/1.1 404 Not Found');
	$smarty->setTemplateDir(ERROR_TEMPLATE);
	$smarty->display(ERROR_TEMPLATE.'404.html');
endif;
