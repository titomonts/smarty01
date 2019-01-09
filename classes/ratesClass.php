<?php

class villaRates extends villa{
	/*
		Function Name: get_villa_rates()
	*/
	public function get_villa_rates($arg,$db="prod")
	{
		$fxCurrency = isset($_COOKIE['defCurrency'])?$_COOKIE['defCurrency']:'US$';
		$fxRate = isset($_COOKIE['defFXRate'])?$_COOKIE['defFXRate']:1.0;
		$fxRate = floatval(str_replace(',','',$fxRate));
		$vid = array("VillaID" => $arg['id']);
		
		/* Start Create folder for XML */
		$xpath = VILLA_XML_PATH.$arg['villa_theme']."/rates/";
		if (!is_dir($xpath))
			mkdir($xpath, 0777);
		/* End Create folder for XML */
		
		if( isset($arg['vid']) && $arg['vid'] != ''):
			$cacheName = $xpath.$arg['vid'].'.xml.cache';
		else:
			$cacheName = $xpath.$arg['villa_theme'].'.xml.cache';
		endif;
		$ageInSeconds = (3600 * 24);
		/* Start generate Token */
		$timeTokenHash = parent::cheeze_curls('Security_GetTimeToken', "", TRUE, FALSE,"","",'prod');
		
		if (!is_array($timeTokenHash))
			$timeTokenHash = html_entity_decode($timeTokenHash);

		$params['p_ToHash'] = 'villaprtl|Xr4g2RmU'.'|'.$timeTokenHash[0];
		$hashString = parent::prepare_Security_GetMD5Hash($params);
		$md5Hash = parent::cheeze_curls('Security_GetMD5Hash', $hashString, TRUE, FALSE,"","",'prod');
		/* End generate Token */

		/* Start Web Method Variables */
		$p_Params = json_encode($vid); /* json_encode the $vid array */
		$p_UserID = 'villaprtl';
		$p_Token = $md5Hash[0];
		/* End Web Method Variables */

		/* Start Method Query String */
		$request = 'p_Token='.$p_Token.'&p_UserID='.$p_UserID.'&p_Params='.$p_Params;
		/* End Method Query String */

		/* Start Fetch XML Data and save it in a physical XML */
		$rates = parent::cheeze_curls('getAllVillaRates', $request, TRUE, TRUE, (isset($arg['vid'])?$arg['vid']:$arg['villa_theme']), $xpath,$db);
		/* End Fetch XML Data */
		
		$data = array();
		$theVillaData = new DOMDocument();
		$theVillaData->load($cacheName);
		
		/* Start Parse Inclusions */
		$generalInclusions = (string)trim(html_entity_decode($theVillaData->getElementsByTagName('GenInclusions')->item(0)->nodeValue));
		$generalInclusions = str_replace('<br /><br />','<br />',$generalInclusions);
		/* End Parse Inclusions */
		
		/* Start Parse Promotions */
		$promoData = array();
		$pr = 0;
		$promotions = $theVillaData->getElementsByTagName('Promo');
		foreach ($promotions as $promo):
			$promoData[$pr]['PromoName'] = (string) trim(html_entity_decode($promo->getElementsByTagName('PromoName')->item(0)->nodeValue));
			$promoData[$pr]['Description'] = (string) trim(html_entity_decode($promo->getElementsByTagName('Description')->item(0)->nodeValue));
			$promoData[$pr]['ValidFrom'] = date('d M Y', strtotime($promo->getElementsByTagName('ValidFrom')->item(0)->nodeValue));
			$promoData[$pr]['ValidTo'] = date('d M Y', strtotime($promo->getElementsByTagName('ValidTo')->item(0)->nodeValue));
			$promoData[$pr]['Bedroom'] = $promo->getAttribute('bedrooms');
			$promoData[$pr]['displaytype'] = $promo->getAttribute('displaytype');
			$pr++;
		endforeach;
		/* End Parse Promotions */
		
		/* Start Early Bird Discount */
		$disArr = array();
		$disArrD = array();
		$da = 0;
		$ds = false;
		
		$disSum = $theVillaData->getElementsByTagName('DiscountSummary');
		foreach ($disSum as $dSa):
			$disArr = $dSa->getElementsByTagName('Discount');
			foreach ($disArr as $dSm):
				$disArrD[$da]['discounttypeid'] = $dSm->getAttribute('discounttypeid');
				$disArrD[$da]['advancedays'] = $dSm->getAttribute('advancedays');
				$disArrD[$da]['DiscountName'] = (string) trim(html_entity_decode($dSm ->getElementsByTagName('DiscountName')->item(0)->nodeValue));
				
				$rt = $dSm->getElementsByTagName('Rate');
				foreach ($rt as $rts):
					$disArrD[$da]['ratetype'] = $rts->getAttribute('ratetype');
				endforeach;
				$disArrD[$da]['Rate'] = $dSm->getElementsByTagName('Rate')->item(0)->nodeValue;
				$disArrD[$da]['Note'] = (string) trim(html_entity_decode($dSm ->getElementsByTagName('Note')->item(0)->nodeValue));
				$da++;
				
			endforeach;
		endforeach;
		
		/* Save EB String */
		$EBtxt = '';
		$EBnote = '';
		for($eb = 0; $eb <= sizeof($disArrD); $eb++):
			if( $disArrD[$eb]['discounttypeid'] == 'EB'):
				$EBrate = $disArrD[$eb]['Rate'] + 0;
				$EBtxt = '<li>Early bird enjoys up to '. $EBrate .''. $disArrD[$eb]['ratetype'] .' discount when booking '. $disArrD[$eb]['advancedays'] .' days in advance.';
				$EBtxt .= $disArrD[$eb]['Note'] == '' ? '' : '*';
				$EBtxt .= '</li>';
                //Edited by Ardian 05 June 2017
				//$EBnote = $disArrD[$eb]['Note'] == '' ? '' : '<li class="note" style="list-style:none;font-style:italic;">* '. $disArrD[$eb]['Note'] . '</li>';
				$EBnote = $disArrD[$eb]['Note'] == '' ? '' : '* '. $disArrD[$eb]['Note'];
				$ds = true;
			endif;
		endfor;
		/* END Early Bird Discount */
		
		/* Start Parse Discounts */
		$discountData = array();
		$d = 0;
		$discounts = $theVillaData->getElementsByTagName('Discount');
		
		$discYear = 0;
		$discYearNights = 0;
		$tmpYr = 0;
		$tmpNights = 0;
		$discyr = array();
		$yr = 0;
		
		foreach ($discounts as $dc):
			$discountData[$d]['discounttypeid'] = $dc->getAttribute('discounttypeid');
			$discountData[$d]['checkinwithin'] = $dc->getAttribute('checkinwithin');
			
			$discountData[$d]['advancedays'] = $dc->getAttribute('advancedays');
			$discountData[$d]['DiscountName'] = (string) trim(html_entity_decode($dc->getElementsByTagName('DiscountName')->item(0)->nodeValue));
			$discountData[$d]['Description'] = (string) trim(html_entity_decode($dc->getElementsByTagName('Description')->item(0)->nodeValue));
			$discountData[$d]['ValidFrom'] = date('d M Y', strtotime($dc->getElementsByTagName('ValidFrom')->item(0)->nodeValue));
			
			$discountData[$d]['ValidTo'] = date('d M Y', strtotime($dc->getElementsByTagName('ValidTo')->item(0)->nodeValue));
			if ($discountData[$d]['discounttypeid'] == 'LM'):
				$discountData[$d]['LastMinute'] = date('d M Y', strtotime(date('d M Y') . '+ ' . $discountData[$d]['checkinwithin'] . ' day'));
			endif;
			
			
			/*  Get Required Nights per Year  */
			if ($discountData[$d]['discounttypeid'] == 'LS'):
				
				if($dc->getElementsByTagName('ValidFrom')->item(0)->nodeValue):
				
					$discYear = date('Y', strtotime($dc->getElementsByTagName('ValidFrom')->item(0)->nodeValue));
					$discYearNights = $dc->getAttribute('requirednights');
					
					if ($tmpYr == 0):
					
						$discyr[$yr]['year'] = $discYear;
						$discyr[$yr]['requirednights'] = $discYearNights;
						
						$tmpYr = $discYear;
						$tmpNights = $discYearNights;
					else:
						if ( $discYear == $tmpYr ):
							$discyr[$yr]['year'] = $discYear;
							$tmpYr = $discYear;
							if ( $discYearNights < $tmpNights ):
								$discyr[$yr]['requirednights'] = $tmpNights;
								$tmpNights = $tmpNights;
							elseif ($discYearNights > $tmpNights):
								$discyr[$yr]['requirednights'] = $discYearNights;
								$tmpNights = $discYearNights;
							endif;
						else:
							$yr++;
							$discyr[$yr]['year'] = $discYear;
							$discyr[$yr]['requirednights'] = $discYearNights;
							$tmpYr = $discYear;
							$tmpNights = $discYearNights;
						endif;
					endif;
				endif;
			endif;
			/*  End -- Get Required Nights per Year  */
			
			$rtypes = $dc->getElementsByTagName('Rate');
			foreach ($rtypes as $rtype):
				$discountData[$d]['ratetype'] = $rtype->getAttribute('ratetype');
			endforeach;
			$discountData[$d]['Rate'] = $dc->getElementsByTagName('Rate')->item(0)->nodeValue;
			
			$d++;
		endforeach;
		/* End Parse Discounts */
		
		/* Start Parse Rooms and Rates */
		$rrData = array();
		$r = 0;
		$years = array(); /* Used for Standard Rates Array */
		$y = 0;
		$years_p = array(); /* Used for Packages Rates Array */
		$yp = 0;
		$rooms_rates = $theVillaData->getElementsByTagName('Room');
		foreach ($rooms_rates as $rr):
			$rrData[$r]['BedRooms'] = $rr->getAttribute('bedrooms');
			/* Start Standard Rates */
			$theRates = $rr->getElementsByTagName('StandardRate');
			$s = 0;
			foreach ($theRates as $tr):
				$thePeriods = $tr->getElementsByTagName('Period');
				$p = 0;
				foreach ($thePeriods as $tp):
					$years[$y] = $tp->getAttribute('year');
					$rrData[$r]['Rates'][$s]['Rate'][$p]['year'] = $tp->getAttribute('year');
					$rrData[$r]['Rates'][$s]['Rate'][$p]['Period'] = date('d M Y', strtotime($tp->getElementsByTagName('SDate')->item(0)->nodeValue)) . ' to ' . date('d M Y', strtotime($tp->getElementsByTagName('EDate')->item(0)->nodeValue));
					$rrData[$r]['Rates'][$s]['Rate'][$p]['NRate'] = $tp->getElementsByTagName('NRate')->item(0)->nodeValue;
					$rrData[$r]['Rates'][$s]['Rate'][$p]['MinDayStay'] = $tp->getElementsByTagName('MinDayStay')->item(0)->nodeValue;
					$rrData[$r]['Rates'][$s]['Rate'][$p]['Inclusions'] = '<ul>' . (string) trim(html_entity_decode($tp->getElementsByTagName('Inclusions')->item(0)->nodeValue)) . '</ul>';
					$rrData[$r]['Rates'][$s]['Rate'][$p]['Season'] = $tp->getElementsByTagName('Season')->item(0)->nodeValue;
					$plasplas = $tp->getElementsByTagName('DisplayRate');
					foreach ($plasplas as $plas):
						$rrData[$r]['Rates'][$s]['Rate'][$p]['plus_sign'] = $plas->getAttribute('is_sctaxincluded');
					endforeach;
					
					$rrData[$r]['Rates'][$s]['Rate'][$p]['added_rate'] = $tp->getElementsByTagName('TaxRate')->item(0)->nodeValue + $tp->getElementsByTagName('SCRate')->item(0)->nodeValue;
					if($rrData[$r]['Rates'][$s]['Rate'][$p]['plus_sign'] == 'N'):
						if($rrData[$r]['Rates'][$s]['Rate'][$p]['added_rate'] > 0):
							$rrData[$r]['Rates'][$s]['Rate'][$p]['DisplayRate'] = number_format((str_replace(',','',$tp->getElementsByTagName('DisplayRate')->item(0)->nodeValue))*$fxRate,2). '++';
							$rrData[$r]['Rates'][$s]['Rate'][$p]['LS14DiscountedRate'] = number_format((str_replace(',','',$tp->getElementsByTagName('LS14DiscountedRate')->item(0)->nodeValue))*$fxRate,2). '++';
							$rrData[$r]['Rates'][$s]['Rate'][$p]['LSMinDiscountedRate'] = number_format((str_replace(',','',$tp->getElementsByTagName('LSMinDiscountedRate')->item(0)->nodeValue))*$fxRate,2). '++';
						else:
							$rrData[$r]['Rates'][$s]['Rate'][$p]['DisplayRate'] = number_format((str_replace(',','',$tp->getElementsByTagName('DisplayRate')->item(0)->nodeValue))*$fxRate,2);
							$rrData[$r]['Rates'][$s]['Rate'][$p]['LS14DiscountedRate'] = number_format((str_replace(',','',$tp->getElementsByTagName('LS14DiscountedRate')->item(0)->nodeValue))*$fxRate,2);
							$rrData[$r]['Rates'][$s]['Rate'][$p]['LSMinDiscountedRate'] = number_format((str_replace(',','',$tp->getElementsByTagName('LSMinDiscountedRate')->item(0)->nodeValue))*$fxRate,2);
						endif;
					else:
						$rrData[$r]['Rates'][$s]['Rate'][$p]['DisplayRate'] = number_format((str_replace(',','',$tp->getElementsByTagName('DisplayRate')->item(0)->nodeValue))*$fxRate,2);
						$rrData[$r]['Rates'][$s]['Rate'][$p]['LS14DiscountedRate'] = number_format((str_replace(',','',$tp->getElementsByTagName('LS14DiscountedRate')->item(0)->nodeValue))*$fxRate,2);
						$rrData[$r]['Rates'][$s]['Rate'][$p]['LSMinDiscountedRate'] = number_format((str_replace(',','',$tp->getElementsByTagName('LSMinDiscountedRate')->item(0)->nodeValue))*$fxRate,2);
					endif;
					$rrData[$r]['Rates'][$s]['Rate'][$p]['TotalExtraChargesPercentage'] = ($rrData[$r]['Rates'][$s]['Rate'][$p]['plus_sign'] == 'N' ? '++' : "").'Villa rates are subject to '.$tp->getElementsByTagName('TotalExtraChargesPercentage')->item(0)->nodeValue.'% service charge, taxes, etc';
					$p++;
					$y++;
				endforeach;
				$s++;
			endforeach;
			/* End Standard Rates */
			$r++;
		endforeach;
		
		/* Start Get Packages */
		$rooms_packages = $theVillaData->getElementsByTagName('Room');
		$r = 0;
	
		$pYears = array();
		$py = 0;
		$packages = array();
		foreach ($rooms_packages as $rp):
			$packages[$r]['Bedroom'] = $rp->getAttribute('bedrooms');
			$thePackages = $rp->getElementsByTagName('Package');
			$pcount = 0;
			foreach ($thePackages as $tp):
				/* Start Extra Info */
				$packages[$r]['Package_Name'] = $tp->getElementsByTagName('RateName')->item(0)->nodeValue;
				$packages[$r]['Marketing_Desc'] = $tp->getElementsByTagName('MarketingDesc')->item(0)->nodeValue;
				/* End Extra Info */
				$pack_periods = $tp->getElementsByTagName('Period');
				$ycount = 0;
				foreach ($pack_periods as $pk):
					$pYears[$py] = $pk->getAttribute('year');
					$packages[$r]['Packages'][$pcount]['Package'][$ycount]['BR'] = $packages[$r]['Bedroom'];
					$packages[$r]['Packages'][$pcount]['Package'][$ycount]['year'] = $pk->getAttribute('year');
					$packages[$r]['Packages'][$pcount]['Package'][$ycount]['Period'] = date('d M Y', strtotime($pk->getElementsByTagName('SDate')->item(0)->nodeValue)) . ' to ' . date('d M Y', strtotime($pk->getElementsByTagName('EDate')->item(0)->nodeValue));
					$plasplas = $pk->getElementsByTagName('DisplayRate');
					foreach ($plasplas as $plas):
						$packages[$r]['Packages'][$pcount]['Package'][$ycount]['plus_sign'] = $plas->getAttribute('is_sctaxincluded');
					endforeach;
					$packages[$r]['Packages'][$pcount]['Package'][$ycount]['DisplayRate'] = ($pk->getElementsByTagName('DisplayRate')->item(0)->nodeValue*$fxRate) . ($packages[$r]['Packages'][$pcount]['Package'][$ycount]['plus_sign'] == 'N' ? '++' : "");
					$packages[$r]['Packages'][$pcount]['Package'][$ycount]['Inclusions'] = '<ul>' . (string) trim(html_entity_decode($pk->getElementsByTagName('Inclusions')->item(0)->nodeValue)) . '</ul>';
					$packages[$r]['Packages'][$pcount]['Package'][$ycount]['Season'] = $pk->getElementsByTagName('Season')->item(0)->nodeValue;
					$packages[$r]['Packages'][$pcount]['Package'][$ycount]['MinDayStay'] = $pk->getElementsByTagName('MinDayStay')->item(0)->nodeValue;
					$packages[$r]['Packages'][$pcount]['Package'][$ycount]['LS14DiscountedRate'] = ($pk->getElementsByTagName('LS14DiscountedRate')->item(0)->nodeValue*$fxRate) . ($packages[$r]['Packages'][$pcount]['Package'][$ycount]['plus_sign'] == 'N' ? '++' : "");
					$packages[$r]['Packages'][$pcount]['Package'][$ycount]['LSMinDiscountedRate'] = ($pk->getElementsByTagName('LSMinDiscountedRate')->item(0)->nodeValue * $fxRate);
			$packages[$r]['Packages'][$pcount]['Package'][$ycount]['TotalExtraChargesPercentage'] = ($packages[$r]['Packages'][$pcount]['Package'][$ycount]['plus_sign'] == 'N' ? '++' : "").'Package rates are subject to '.$pk->getElementsByTagName('TotalExtraChargesPercentage')->item(0)->nodeValue.'% service charge, taxes, etc';
					$py++;
					$ycount++;
				endforeach;
				$pcount++;
			endforeach;
			$r++;
		endforeach;
		/* End Get Packages */
		
		$years = array_values(array_unique($years)); /* Remove duplicate values from years array */
    	$pYears = array_values(array_unique($pYears)); /* Remove duplicate values from years_p array */
		/* End Parse Rooms, Rates and Packages */
		
		/* Start Build Output */
        //Edited by Ardian 05 June 2017
		/*$html = "<p class='formatScript'>
		<link rel='stylesheet' href='/resources/common/css/rates.css' />
		<script type='text/javascript' src='/resources/common/js/jquery-1.7.1.js'></script>
		<script type='text/javascript' src='/resources/common/js/jquery-ui-1.8.js'></script>
		<link href='/resources/common/css/jquery-ui-1.8.css' rel='stylesheet' type='text/css'>
		<script type='text/javascript' src='/resources/common/js/tooltips_functions.js'></script>
		</p>
		";*/
		
		/* Start General Inclusions */
		$html .='<p class="teaser">'.$generalInclusions.'</p>';
		/* End General Inclusions */
		
		
		/* Promo Display */
		$sp = sizeof($promoData);
		$rsize = sizeof($rrData);
		if ($sp >= 1):
			if ($rsize >= 1):
			$html .= '<div class="promotions-box">';
			$html .= '<div class="promotions">';
					$html .='<div id="' . $promoData[$b]['Bedroom'] . '">';
					$html .= '<table class="tblrates" width="100%" border="0">';
					$html .='<tr class="promotr">
								<td align="left">
									<strong class="rates-title">Promotions</strong><ul>';
									for ($b = 0; $b < $sp; $b++):		
										$html .='<li>'.$promoData[$b]['PromoName'].'<br />'.$promoData[$b]['Description'].'</li>';
									endfor;
								$html .='</ul></td>
							</tr>';
					$html .= '</table>';
					$html .='</div>';
				$html .= '</div>';
				$html .= '</div>';
			endif;
			unset($x);
		endif;
		/* End Promo Display */
	
		/* Discount Display */
		$sd = sizeof($discountData);
		/* Start Check for Last Minute Discounts */
		$numEB = 0;
		$numLM = 0;
		for ($x = 0; $x < $sd; $x++):
			if ($discountData[$x]['discounttypeid'] == 'EB'):
				$numEB++;
			endif;
			
			if ($discountData[$x]['discounttypeid'] == 'LM'):
				$numLM++;
			endif;
			
			$start = strtotime($discountData[$x]['ValidFrom']);
			$end = strtotime($discountData[$x]['ValidTo']);
			$now = strtotime(date('d M Y'));
			
			if ($discountData[$x]['discounttypeid'] == 'LM'):
				if( $now >= $start && $now <= $end ):
					$LM_title = $discountData[$x]['DiscountName'];
					$LM_text = '<li>Last minute bookings enjoy up to ' . (float) $discountData[$x]['Rate'] . $discountData[$x]['ratetype'] . ' discount when checking in before '. $discountData[$x]['LastMinute'] . '.</li>';
					$ds = true;
				endif;
			endif;
			
		endfor;
		/* End Check for Discounts */
		unset($x);
		$disc_text = '';
		if ($numEB >= 1 || $numLM >= 1):
			$html .= '<div class="discounts-box">';
			$html .= '<div class="discounts">';
			$html .= '<table class="tblrates" width="100%" border="0">';
			$now = strtotime(date('d M Y'));
			
			if($ds):
				$html .='<tr>
							<td>
								<strong class="rates-title">Discounts</strong>
								<ul>
								'.$EBtxt.'
								'.$LM_text.'
								</ul>
								'.$EBnote.'
							</td>
						</tr>';
			endif;		
		endif;
			$html .= '</table>';
			$html .= '</div>';
			$html .= '</div>';
		
		/* End Discount Display */
	
		/* Start Rates Display */
		$s_rrData = sizeof($rrData);
		$class = ($s_rrData > 1 ? 'tabs' : '');
		$html .= '<div id="' . $class . '" class="' . $class . '">';
	
		if ($s_rrData > 1):
			/* This is to generate the <ul> tabs */
			$html .= '<ul>';
			for ($br = 0; $br < $s_rrData; $br++):
					if( array_key_exists('Rates',$rrData[$br]) ):
						$html .='<li><a href="#' . $rrData[$br]['BedRooms'] . '">' . $rrData[$br]['BedRooms'] . '-Bedroom</a></li>';
			endif;
			endfor;
			$html .='</ul>';
			unset($br);
		/* End generate <ul> tabs */
		endif;
		/* Build Main Tabs */
		for ($br = 0; $br < $s_rrData; $br++):
			$html .= '<div id="' . $rrData[$br]['BedRooms'] . '"><!-- Start Bedroom Div -->';
			/* Start Display Rates */
			$sRates = sizeof($rrData[$br]['Rates']);
			for ($sr = 0; $sr < $sRates; $sr++):
				$sYear = sizeof($years);
				$html .='<div id="tabs" class="tabs">';
				$html .='<ul>';
				$syTabs = sizeof($rrData[$br]['Rates'][$sr]['Rate']);
				for ($sy = 0; $sy < $syTabs; $sy++):
					if ($rrData[$br]['Rates'][$sr]['Rate'][$sy]['year'] != $rrData[$br]['Rates'][$sr]['Rate'][$sy + 1]['year']):
						$html .='<li><a href="#' . $rrData[$br]['Rates'][$sr]['Rate'][$sy]['year'] . '">' . $rrData[$br]['Rates'][$sr]['Rate'][$sy]['year'] . '</a></li>';
					endif;
				endfor;
				$html .='</ul>';
				unset($sy);
				unset($yr);
				for ($sy = 0; $sy < $sYear; $sy++):
					$html .='<div id="' . $years[$sy] . '">';
					$sYear2 = sizeof($rrData[$br]['Rates'][$sr]['Rate']);
					$html .='<table class="tblrates" width="100%" border="0" align="left">';
					$html .='<tr align="left">
								<th><strong>Period</strong></th>
								<th><strong>'.$fxCurrency.' / night</strong></th>';
								
					/* Display No. of required nights */			
					for ($yr = 0; $yr <= sizeof($discyr); $yr++):
						if ($discyr[$yr]['year'] == $years[$sy]):
							$LongStay =  $discyr[$yr]['requirednights'];
						endif;
					endfor;
					/* End Display No. of required nights */			
								
					$html .='<th><strong>'.$fxCurrency.'/NIGHT ('. $LongStay .'+ NIGHTS)</strong></th>
								<th><strong>Min nights</strong></th>
								<th align="center"><strong>Inclusions</strong></th>
							</tr>';
			$extraChargeText = '';
					for ($sy2 = 0; $sy2 < $sYear2; $sy2++):
						if ($rrData[$br]['Rates'][$sr]['Rate'][$sy2]['year'] == $years[$sy]):
							$extraChargeText = $rrData[$br]['Rates'][$sr]['Rate'][$sy2]['TotalExtraChargesPercentage'];
							$periodReplace1 = str_replace($rrData[$br]['Rates'][$sr]['Rate'][$sy2]['year'], "", $rrData[$br]['Rates'][$sr]['Rate'][$sy2]['Period']);
							$html .='<tr>
								<td>' . $periodReplace1 . '</td>
								<td>' . $rrData[$br]['Rates'][$sr]['Rate'][$sy2]['DisplayRate'] . '</td>
								<td>' . $rrData[$br]['Rates'][$sr]['Rate'][$sy2]['LSMinDiscountedRate'] . '</td>
								<td>' . $rrData[$br]['Rates'][$sr]['Rate'][$sy2]['MinDayStay'] . '</td>
								<td align="center">' . 
									/*<img src="/resources/common/images/inclusions_blue.png" border="0" id="'.$rrData[$br]['BedRooms'].$years[$sy].'rates_inclusion'.$sy2.'-'.$id.'" href="javascript:void(0)" class="" rel="tooltip" style="cursor:pointer;" title="'.htmlentities($rrData[$br]['Rates'][$sr]['Rate'][$sy2]['Inclusions']).'" />*/
                                    '<div class="inclusions">
                                        <i class="fa fa-file-text-o" id="'.$rrData[$br]['BedRooms'].$years[$sy].'rates_inclusion'.$sy2.'-'.$id.'" aria-hidden="true" data-popover="'.htmlentities($rrData[$br]['Rates'][$sr]['Rate'][$sy2]['Inclusions']).'"></i> 
                                    </div>
								</td>
							</tr>';
						endif;
					endfor;
			$html .='<tr><td class="vrsubject" colspan="5" align="right" height="25" valign="middle">'.$extraChargeText.'</td></tr>';
					$html .='</table>';
					$html .='</div>';
				endfor;
				$html .='</div>';
			endfor;
			/* End Display Rates */
			$html .= '</div>';
		endfor;
		/* End Build Main Tabs */
		$html .='</div>';
		/* End Rates Display */
		
		/* Start Display Packages */
		$ps = sizeof($packages);
		for ($w = 0; $w < $ps; $w++):
			if (array_key_exists('Packages', $packages[$w])):
				$sp_1 = sizeof($packages[$w]['Packages']);
				$html .='<h2 class="ratesSubTitle">' . $packages[$w]['Package_Name'] . '</h2><br />';
				$html .='<div align="left">' . $packages[$w]['Marketing_Desc'] . '</div><br />';
				$pClass = $s_rrData > 1 ? 'tabs' : '';
				$html .='<div id="' . $pClass . '" class="' . $pClass . '">';
				if ($s_rrData > 1): /* If there are more than 1 bedrooms, show bedroom tabs else do not generate */
					$html .='<ul><li><a href="#' . $packages[$w]['Bedroom'] . '">' . $packages[$w]['Bedroom'] . '-Bedroom</a></li></ul>';
				endif;
				$html .='<div id="' . $packages[$w]['Bedroom'] . '">';
				$spy = sizeof($pYears);
				$html .='<div id="tabs" class="tabs">';
	
	
				/* If there are more than 1 season (year) for the package, show year tab else do not generate */
				$html .='<ul>';
				for ($ayu = 0; $ayu < $sp_1; $ayu++):
					$sp_2 = sizeof($packages[$w]['Packages'][$ayu]['Package']);
					if ($sp_2 > 1):
			$temp_year = array();
						for ($yah = 0; $yah < $sp_2; $yah++):
							$temp_year[$yah] = $packages[$w]['Packages'][$ayu]['Package'][$yah]['year'];
						endfor;
						$temp_year = array_values(array_unique($temp_year));
				$ansi = sizeof($temp_year);
						unset($yah);
						for ($yah = 0; $yah < $sp_2; $yah++):
							if ($packages[$w]['Packages'][$ayu]['Package'][$yah]['year'] != $packages[$w]['Packages'][$ayu]['Package'][$yah + 1]['year'] && $ansi > 1):
								$html .='<li><a href="#' . $packages[$w]['Packages'][$ayu]['Package'][$yah]['year'] . '">' . $packages[$w]['Packages'][$ayu]['Package'][$yah]['year'] . '</a></li>';
							endif;
						endfor;
					endif;
				endfor;
				$html .='</ul>';
	
				unset($ekis, $ayu, $sp_2, $yah);
				for ($ekis = 0; $ekis < $spy; $ekis++):
					$html .='<div id="' . $pYears[$ekis] . '">';
					$html .='<table class="tblrates" width="100%" align="left" border="0">
								<tr align="left">
									<th><strong>Period</strong></th>
									<th><strong>Price Per Night</strong></th>
									<th><strong>Min stay</strong></th>
									<th align="center"><strong>Inclusions</strong></th>
								</tr>';
					//$sp_1 = sizeof($packages[$w]['Packages']);
			$extraChargeText = '';
					for ($ayu = 0; $ayu < $sp_1; $ayu++):
						$sp_2 = sizeof($packages[$w]['Packages'][$ayu]['Package']);
						for ($yah = 0; $yah < $sp_2; $yah++):
							if ($pYears[$ekis] == $packages[$w]['Packages'][$ayu]['Package'][$yah]['year']):
					$extraChargeText = $packages[$w]['Packages'][$ayu]['Package'][$yah]['TotalExtraChargesPercentage'];
									$html .='<tr>
						<td>' . $packages[$w]['Packages'][$ayu]['Package'][$yah]['Period'] . '</td>
						<td>'.$fxCurrency.' ' . $packages[$w]['Packages'][$ayu]['Package'][$yah]['DisplayRate'] . '</td>
						<td>' . $packages[$w]['Packages'][$ayu]['Package'][$yah]['MinDayStay'] . ' Nights</td>
						<td align="center">' .
							/*<img src="/resources/common/images/inclusions_blue.png" border="0" id="'.$packages[$w]['Bedroom'].$pYears[$ekis].'pack_link'.$yah.'-'.$id.'" href="javascript:void(0)" rel="tooltip" class="" style="cursor:pointer;" title="'.htmlentities($packages[$w]['Packages'][$ayu]['Package'][$yah]['Inclusions']).'" />*/
                            '<div class="inclusions">
                                <i class="fa fa-file-text-o" id="'.$rrData[$br]['BedRooms'].$years[$sy].'rates_inclusion'.$sy2.'-'.$id.'" aria-hidden="true" data-popover="'.htmlentities($rrData[$br]['Rates'][$sr]['Rate'][$sy2]['Inclusions']).'"></i> 
                            </div>
						</td>
					</tr>';
							endif;
						endfor;
					endfor;
			$html .='<tr><class="vrsubject" colspan="5" align="right" height="25" valign="middle">'.$extraChargeText.'</td></tr>';
					$html .='</table>';
					$html .='</div>';
				endfor;
				$html .='</div>';
				$html .='</div>';
				$html .='</div>';
			endif;
		endfor;
		/* End Display Packages */
		
		/* Terms and Conditions */
		//$contents = file_get_contents('http://www.elitehavens.com/terms.aspx');
    	//file_put_contents('/tc.html', $contents);
        
        
		$html .= '<p class="rates-link" data-dialog="http://'.$_SERVER['HTTP_HOST'].'/tc.htm">Read our terms &amp; conditions</p>';
		/*$html .= '<p id="opener" class="pLink">Read our terms &amp; conditions</p>';
		$html .='<div id="dialog"></div>';
		$html .="<script type='text/javascript'>
					$(document).ready(function(){
						$('#dialog').load('http://vw01.aws-jp.marketingvillas.com/tc.html #terms');
					});
				</script>";*/
        
        
		/* End Terms and Conditions */
		
		/* End Build Output*/
		return $html;
		
	}
	
	/*
		Function name: mobile_rates()
	*/
	public function mobile_rates($vid,$cacheName)
	{
		$mobileData = parent::get_contents($vid,$cacheName);
		$totbed = 0;
		$gbed = sizeof($mobileData['rrData']);
		for ($q = 0; $q < $gbed; $q++):			
			$Rbed = sizeof($mobileData['rrData'][$q]['BedRooms']);
			$totbed = $totbed +$Rbed;
		endfor;
		
		if($totbed >=2):
			// -------- GET RATES MUTLIBEDROOMS FOR MOBILE ----<div class="title">----
			$html .= '<div id="rates">';
			$html .= '<div id="tabs" class="tabs ui-tabs">';
				$q = 0;
				$w = 0;
				$ratebedroom = sizeof($mobileData['rrData']);
				
				$html .= '<ul class="ui-tabs-nav">';					
					for ($q = 0; $q < $ratebedroom; $q++):				
						$Rsizebed = sizeof($mobileData['rrData'][$q]['BedRooms']);
						$html .= '<li><a href="#' .  $mobileData['rrData'][$q]['BedRooms'] . 'bed">' .  $mobileData['rrData'][$q]['BedRooms'] . ' - Bedroom</a></li>';
						/* for ($w = 0; $w < $Rsizebed; $w++):						
							$html .= '<li><a href="#' .  $mobileData['rrData'][$q]['BedRooms'][$w] . 'bed">' .  $mobileData['rrData'][$q]['BedRooms'][$w] . ' - Bedroom</a></li>';
						endfor; */					
					endfor;				
				$html .= '</ul>'; 
									
				for ($q = 0; $q < $ratebedroom; $q++):				
					$Rsizebedd = sizeof($mobileData['rrData'][$q]['BedRooms']);//get type bedroom
					for ($w = 0; $w < $Rsizebedd; $w++):
						$html .= '<div id="'.$mobileData['rrData'][$q]['BedRooms'].'bed" class="ui-tabs-panel">';
							$y = 0;
							$html .= '<ul class="ui-tabs-nav">'; 
								$yearsLength = sizeof($mobileData['year']);	
								for ($y = 0; $y < $yearsLength; $y++):
									$html .= '<li><a href="#' . $mobileData['year'][$y] . '">' . $mobileData['year'][$y] . '</a></li>';
								endfor;
							$html .= '</ul>'; 
							
							for ($z = 0; $z < $yearsLength; $z++):
							$html .= '<div class="content" id="' . $mobileData['year'][$z] . '"><table><tbody>';
							$html .= '<tr>';
							$html .= '<td class="colLeft period">Period</td>';
							$html .= '<td class="dispRate">US$/night</td>';
							$html .= '<td class="colRight minDayStay">Min nights</td>';
							$html .= '<td class="colRight inclusions">Inclusions</td>';
							$html .= '</tr>';
	
							
							$d = 0;
							$e = 0;
							$Rsize = sizeof($mobileData['rrData'][$q]['Rates']);
							for ($d = 0; $d < $Rsize; $d++):
								$RSsize = sizeof($mobileData['rrData'][$q]['Rates'][$d]['Rate']);
								for ($e = 0; $e < $RSsize; $e++):
									if ($mobileData['rrData'][$q]['Rates'][$d]['Rate'][$e]['year'] == $mobileData['year'][$z]):
										
										$periodeReplace1 = str_replace($mobileData['year'][$z], "", $mobileData['rrData'][$q]['Rates'][$d]['Rate'][$e]['Period']);
										$periodeReplace2 = str_replace("to", "to<br />", $periodeReplace1);
										$html .= '<tr>';
										$html .= '<td class="colLeft period">' . $periodeReplace2 . '</td>';
										$html .= '<td class="dispRate">' . $mobileData['rrData'][$q]['Rates'][$d]['Rate'][$e]['DisplayRate'] . '</td>';
										$html .= '<td class="colRight minDayStay">' . $mobileData['rrData'][$q]['Rates'][$d]['Rate'][$e]['MinDayStay'] . '</td>';
										$html .= '<td class="colRight inclusions">' . $mobileData['rrData'][$q]['Rates'][$d]['Rate'][$e]['Inclusions'] . '</td>';
										$html .= '</tr>';
									endif;
								endfor;
							endfor;
							
							$html .= '</tbody></table></div>';
							endfor;
						$html .= '</div>'; 
					endfor;					
				endfor;	
			$html .= '</div></div>';
			
			// --------END // GET RATES MULTBEDROOMS FOR MOBILE --------
			
		else:
			// -------- GET RATES FOR  SINGLE BEDROOM  MOBILE ----<div class="title">----
			$html = '<div id="rates">';
	
			$y = 0;
			$yearsLength = sizeof($mobileData['year']);
	
			$html .= '<div id="tabs" class="tabs">';
			$html .= '<ul>';
			for ($y = 0; $y < $yearsLength; $y++):
				$html .= '<li><a href="#' . $mobileData['year'][$y] . '">' . $mobileData['year'][$y] . '</a></li>';
			endfor;
			$html .= '</ul>';
	
			for ($z = 0; $z < $yearsLength; $z++):
				/* $html .= '<h2>Rates ' . $mobileData['year'] . '</h2></div>'; */
				$html .= '<div class="content" id="' . $mobileData['year'][$z] . '"><table><tbody>';
				$html .= '<tr>';
				$html .= '<td class="colLeft period">Period</td>';
				$html .= '<td class="dispRate">US$/night</td>';
				$html .= '<td class="colRight minDayStay">Min nights</td>';
				$html .= '<td class="colRight inclusions">Inclusions</td>';
				$html .= '</tr>';
	
				$rateSize = sizeof($mobileData['rrData'][0]);
				$c = 0;
				$d = 0;
				$e = 0;
	
				for ($c = 0; $c < $rateSize; $c++):
					$Rsize = sizeof($mobileData['rrData'][$c]['Rates']);
					for ($d = 0; $d < $Rsize; $d++):
						$RSsize = sizeof($mobileData['rrData'][$c]['Rates'][$d]['Rate']);
						for ($e = 0; $e < $RSsize; $e++):
							if ($mobileData['rrData'][$c]['Rates'][$d]['Rate'][$e]['year'] == $mobileData['year'][$z]):
								$periodeReplace1 = str_replace($mobileData['year'][$z], "", $mobileData['rrData'][$c]['Rates'][$d]['Rate'][$e]['Period']);
								$periodeReplace2 = str_replace("to", "to<br />", $periodeReplace1);
								$html .= '<tr>';
								$html .= '<td class="colLeft period">' . $periodeReplace2 . '</td>';
								$html .= '<td class="dispRate">' . $mobileData['rrData'][$c]['Rates'][$d]['Rate'][$e]['DisplayRate'] . '</td>';
								$html .= '<td class="colRight minDayStay">' . $mobileData['rrData'][$c]['Rates'][$d]['Rate'][$e]['MinDayStay'] . '</td>';
								$html .= '<td class="colRight inclusions">' . $mobileData['rrData'][$c]['Rates'][$d]['Rate'][$e]['Inclusions'] . '</td>';
								$html .= '</tr>';
							endif;
						endfor;
					endfor;
				endfor;
				$html .= '</tbody></table></div>';
			endfor;
	
			$html .= '</div></div>';
			// --------END // GET RATES SINGLE BEDROOM FOR MOBILE --------		
		endif;
		
		return $html;
	}
	
	/*
		Estate Rates
	*/
	public function estate_rates($data)
	{
		$timeTokenHash = parent::cheeze_curls('Security_GetTimeToken',"",TRUE,FALSE,"","","prod");
		if(!is_array($timeTokenHash))
			$timeTokenHash = html_entity_decode($timeTokenHash);
			
		$params['p_ToHash'] = 'villaprtl|Xr4g2RmU|'.$timeTokenHash[0];
		$hashString = parent::prepare_Security_GetMD5Hash($params);
		$md5Hash = parent::cheeze_curls('Security_GetMD5Hash',$hashString,TRUE,FALSE,"","","prod");
		
		$params['p_EstateID'] = $data['estate_id'];
		$params['p_UserID'] = 'villaprtl';
		$params['p_Token'] = $md5Hash[0];
		
		$xpath = VILLA_XML_PATH.$data['villa_theme']."/rates/";
		if(!is_dir($xpath))
			mkdir($xpath,0777);
		
		$cacheName = $xpath.'byseason_'.$params['p_EstateID'].'.xml.cache';
		$ageInSeconds = (3600 * 24);
		//if (!file_exists($cacheName) || filemtime($cacheName) > time() + $ageInSeconds):
			/* Get By Season Data */
			$params['p_ViewType'] = 'byseason';
			$request = 'p_Token='.$params['p_Token'].'&p_UserID='.$params['p_UserID'].'&p_Params='.json_encode( array("EstateID" => $data['estate_id'],"ViewType" =>$params['p_ViewType']) );
			$xml_byseason = parent::cheeze_curls('getEstateRates',$request,TRUE,TRUE,'byseason_'.$data['estate_id'],$xpath,"prod");
		//endif;
		
		/* Get Discount text. Added June 2015 */
		$vid = array("VillaID" => $data['villa_id']);
		$p_Params = json_encode($vid); /* json_encode the $vid array */
		$p_UserID = 'villaprtl';
		$p_Token = $md5Hash[0];
		$vrequest = 'p_Token='.$p_Token.'&p_UserID='.$p_UserID.'&p_Params='.$p_Params;
		$rates = parent::cheeze_curls('getAllVillaRates', $vrequest, TRUE, TRUE, $data['villa_id'], $xpath,"prod");
		
		$theVillaData = new DOMDocument();
		$theVillaData->load($xpath.$data['villa_id'].'.xml.cache');
	
		$generalInclusions = (string)trim(html_entity_decode($theVillaData->getElementsByTagName('GenInclusions')->item(0)->nodeValue));
		$generalInclusions = str_replace('<br /><br />','<br />',$generalInclusions);
		/* End Get Discount text */
		
		/* Start Parse Promotions */
		$promoData = array();
		$pr = 0;
		$promotions = $theVillaData->getElementsByTagName('Promo');
		foreach ($promotions as $promo):
			$promoData[$pr]['PromoName'] = (string) trim(html_entity_decode($promo->getElementsByTagName('PromoName')->item(0)->nodeValue));
			$promoData[$pr]['Description'] = (string) trim(html_entity_decode($promo->getElementsByTagName('Description')->item(0)->nodeValue));
			$promoData[$pr]['ValidFrom'] = date('d M Y', strtotime($promo->getElementsByTagName('ValidFrom')->item(0)->nodeValue));
			$promoData[$pr]['ValidTo'] = date('d M Y', strtotime($promo->getElementsByTagName('ValidTo')->item(0)->nodeValue));
			$promoData[$pr]['Bedroom'] = $promo->getAttribute('bedrooms');
			$promoData[$pr]['displaytype'] = $promo->getAttribute('displaytype');
			$pr++;
		endforeach;
		/* End Parse Promotions */
		
		/* CHECKING PROMOTIONS PER VILLA */
		$promo_counter = 0;
		if (isset($data['promovillas'])):
			/* SPLIT VILLAS */
			$pv_name = array();
			$pv_id = array();
			$pv = 0;
			foreach($data['promovillas'] as $id => $name) :
				$pv_id[$pv] = $id;
				$pv_name[$pv] = $name;
				$pv++;
			endforeach;
			/* END SPLITTING VILLAS */
			
			/* GET VILLA INFOS */
			$pv = 0;
			$pv_size = sizeof($pv_id);	
			for ($pv = 0; $pv < sizeof($pv_id); $pv++ ):
		
			
				/* CHECK FOR XML CACHE and IF IT IS OLDER THAN A MONTH */
				$pv_cache = $xpath.$pv_id[$pv].'.xml.cache';
				$ageInSeconds = (3600 * 24 * 30);
		
				//if (!file_exists($pv_cache) || filemtime($pv_cache) > time() + $ageInSeconds):
					/* GET VILLAINFOS FROM API */
					$vid = array("VillaID" => $pv_id[$pv]);
					$p_Params = json_encode($vid); /* json_encode the $vid array */
					$p_UserID = 'villaprtl';
					$p_Token = $md5Hash[0];
					$vrequest = 'p_Token='.$p_Token.'&p_UserID='.$p_UserID.'&p_Params='.$p_Params;
					$rates = parent::cheeze_curls('getAllVillaRates', $vrequest, TRUE, TRUE, $pv_id[$pv], $xpath,"prod");
					/* END GET VILLAINFOS FROM API */
				//endif;
				/* END CHECKING OF XML CACHE */
		
		
				$pVillaData = new DOMDocument();
				$pVillaData->load($pv_cache);
		
				
				/* Start Parse Promotions */
				$pvData = array();
				$pr = 0;
				
				$promotions = $pVillaData->getElementsByTagName('Promo');
				foreach ($promotions as $promo):
					$pvData[$pr]['discounttypeid'] = $promo->getAttribute('discounttypeid');
					//if ($pvData[$pr]['discounttypeid'] == "SD" || $pvData[$pr]['discounttypeid'] == "EB"):
						$pvData[$pr]['PromoName'] = (string) trim(html_entity_decode($promo->getElementsByTagName('PromoName')->item(0)->nodeValue));
						$pvData[$pr]['Description'] = (string) trim(html_entity_decode($promo->getElementsByTagName('Description')->item(0)->nodeValue));
						$pvData[$pr]['ValidFrom'] = date('d M Y', strtotime($promo->getElementsByTagName('ValidFrom')->item(0)->nodeValue));
						$pvData[$pr]['ValidTo'] = date('d M Y', strtotime($promo->getElementsByTagName('ValidTo')->item(0)->nodeValue));
						$pvData[$pr]['Bedroom'] = $promo->getAttribute('bedrooms');
						$pvData[$pr]['displaytype'] = $promo->getAttribute('displaytype');
						$promo_counter = $promo_counter + 1;
						$pr++;
					//endif;
				endforeach;
				/* End Parse Promotions */
		
		
				if (!empty($pvData)) :
				/* IF PROMO FOUND */
		
					$spv = sizeof($pvData);
		
					/* SHOW PROMO */		
					$promo_display .='<p><strong>'.$villa_info['villa_name'].' - '.$pv_name[$pv].'</strong></p>';
					$promo_display .='<div class="promotions-box">';
					$promo_display .='<div class="promotions">';
						$promo_display .='<div>';
						$promo_display .= '<table class="tblrates" width="100%" border="0">';
						$promo_display .='<tr class="promotr">
									<td align="left">
										<ul>';
										for ($b = 0; $b < $spv; $b++):		
											$promo_display .='<li><strong>'.$pvData[$b]['PromoName'] . '</strong></li><li>'.$pvData[$b]['Description'].'</li>';
										endfor;
									$promo_display .='</ul></td>
								</tr>';
						$promo_display .= '</table>';
						$promo_display .='</div>';
					$promo_display .= '</div>';
					$promo_display .= '</div>';
					$promo_display .='<!-- End Promotions Tab -->';
					/* END SHOW PROMO */
					
				endif;
		
		   endfor;
		else:
			$promo_counter = 0;
		endif;
		/* END CHECKING PROMOTIONS PER VILLA */

		/* Start Parse Discounts */
		$discountData = array();
		$d = 0;
		$discounts = $theVillaData->getElementsByTagName('Discount');
		foreach ($discounts as $dc):
			$discountData[$d]['discounttypeid'] = $dc->getAttribute('discounttypeid');
			$discountData[$d]['checkinwithin'] = $dc->getAttribute('checkinwithin');
			$discountData[$d]['advancedays'] = $dc->getAttribute('advancedays');
			$discountData[$d]['requirednights'] = $dc->getAttribute('requirednights');
			$discountData[$d]['DiscountName'] = (string) trim(html_entity_decode($dc->getElementsByTagName('DiscountName')->item(0)->nodeValue));
			$discountData[$d]['Description'] = (string) trim(html_entity_decode($dc->getElementsByTagName('Description')->item(0)->nodeValue));
			$discountData[$d]['ValidFrom'] = date('d M Y', strtotime($dc->getElementsByTagName('ValidFrom')->item(0)->nodeValue));
			$discountData[$d]['ValidTo'] = date('d M Y', strtotime($dc->getElementsByTagName('ValidTo')->item(0)->nodeValue));
			if ($discountData[$d]['discounttypeid'] == 'LM'):
				$discountData[$d]['LastMinute'] = date('d M Y', strtotime(date('d M Y') . '+ ' . $discountData[$d]['checkinwithin'] . ' day'));
			endif;
			$rtypes = $dc->getElementsByTagName('Rate');
			foreach ($rtypes as $rtype):
				$discountData[$d]['ratetype'] = $rtype->getAttribute('ratetype');
			endforeach;
			$discountData[$d]['Rate'] = $dc->getElementsByTagName('Rate')->item(0)->nodeValue;
			$d++;
		endforeach;
		/* End Parse Discounts */
		
		/* Start Parse Discount Summary */
		$dSummary = array();
		$dcount = 0;
		$da = 0;
		$de = 0;
		$discountSum = $theVillaData->getElementsByTagName('DiscountSummary');
		foreach( $discountSum as $discountSummary ):
			$discountSum = $discountSummary->getElementsByTagName('Discount');
			foreach( $discountSum as $dsum ):
				$dSummary[$de]['requirednights'] = $dsum->getAttribute('requirednights');
				$dSummary[$de]['discounttypeid'] = $dsum->getAttribute('discounttypeid');
				$dSummary[$de]['advancedays'] = $dsum->getAttribute('advancedays');
				
				$dSummary[$de]['DiscountName'] = (string) trim(html_entity_decode($dsum->getElementsByTagName('DiscountName')->item(0)->nodeValue));
				$rstypes = $dsum->getElementsByTagName('Rate');
				foreach ($rstypes as $rstype):
					$dSummary[$de]['ratetype'] = $rstype->getAttribute('ratetype');
				endforeach;
				$dSummary[$de]['Rate'] = $dsum->getElementsByTagName('Rate')->item(0)->nodeValue;
				$dSummary[$de]['Note'] = (string) trim(html_entity_decode($dsum->getElementsByTagName('Note')->item(0)->nodeValue));
				
				$de++;
			endforeach;
				
		endforeach;
		
		
		/* Save EB String */
		$EBtxt = '';
		$EBnote = '';
		for($eb = 0; $eb <= sizeof($dSummary); $eb++):
			if( $dSummary[$eb]['discounttypeid'] == 'EB'):
				$EBrate = $dSummary[$eb]['Rate'] + 0;
				$EBtxt = '<li>Early bird enjoys up to '. $EBrate .''. $dSummary[$eb]['ratetype'] .' discount when booking '. $dSummary[$eb]['advancedays'] .' days in advance.';
				$EBtxt .= $dSummary[$eb]['Note'] == '' ? '' : '*';
				$EBtxt .= '</li>';
				$EBnote = $dSummary[$eb]['Note'] == '' ? '' : '* '. $dSummary[$eb]['Note'];
			endif;
		endfor;
		
		/* END EB Discount */
		
		
		
		
	
		/*	======================
			Start Process XML Data
			======================
		*/
		$cacheName_byseason = $xpath.'byseason_'.$params['p_EstateID'].'.xml.cache';
		$bySeasonData = array();
		
		/* Load by Season Cache File */
		$theSeasonData = new DomDocument();
		$tsd=0;
		$theSeasonData->load($cacheName_byseason); 
		$estateBySeason = array();
		
		$loDatesAndNights = array();
		$ldn=0;
		
		$hiDatesAndNights = array();
		$mdn=0;
		
		$hiDatesAndNights = array();
		$hdn=0;
		
		$pkDatesAndNights = array();
		$pdn=0;
		
		/* Start Process By Season */
		$theYears = array();
		$ty = 0;
		$vids = $theSeasonData->getElementsByTagName('Villa');
		foreach( $vids as $vid ):
			$estateBySeason['Estate'][$tsd]['villaid'] = $vid->getAttribute('villaid');
			$estateBySeason['Estate'][$tsd]['villaname'] = $vid->getAttribute('villaname');
			$estateBySeason['Estate'][$tsd]['isinternaluse'] = $vid->getAttribute('isinternaluse');
			$rateName = $vid->getElementsByTagName('RateName');
			$rate_count=0;
			foreach( $rateName as $rn ):
				$estateBySeason['Estate'][$tsd]['RateName'][$rate_count]['bedrooms'] = $rn->getAttribute('bedrooms');
				$estateBySeason['Estate'][$tsd]['RateName'][$rate_count]['roomname'] = $rn->getAttribute('roomname');
				$estateBySeason['Estate'][$tsd]['RateName'][$rate_count]['ratename'] = $rn->getAttribute('ratename');
				$years = $rn->getElementsByTagName('Year');
				$y=0;
				foreach( $years as $year ):
					$estateBySeason['Estate'][$tsd]['RateName'][$rate_count]['Years'][$y]['Year'] = $year->getAttribute('year');
					$theYears[$ty] = $year->getAttribute('year');
					$ty++;
					$seasons = $year->getElementsByTagName('Season');
					$s=0;
					foreach( $seasons as $season ):
						$rate = $season->getElementsByTagName('Rate');
						$r=0;
						foreach( $rate as $rt ):
							$estateBySeason['Estate'][$tsd]['RateName'][$rate_count]['Years'][$y]['Season'][$s]['Rate'][$r]['sid'] = $season->getAttribute('seasonid');
	
							$estateBySeason['Estate'][$tsd]['RateName'][$rate_count]['Years'][$y]['Season'][$s]['Rate'][$r]['year'] = $year->getAttribute('year');
	
							$estateBySeason['Estate'][$tsd]['RateName'][$rate_count]['Years'][$y]['Season'][$s]['Rate'][$r]['period'] =  date('F j Y',strtotime($rt->getElementsByTagName('From')->item(0)->nodeValue)).'-'.date('F j Y',strtotime($rt->getElementsByTagName('To')->item(0)->nodeValue));
							
							$dps = $rt->getElementsByTagName('DisplayRate');
							foreach( $dps as $dp ):
								$estateBySeason['Estate'][$tsd]['RateName'][$rate_count]['Years'][$y]['Season'][$s]['Rate'][$r]['plus_sign'] = $dp->getAttribute('is_sctaxincluded');
							endforeach;
							
							$estateBySeason['Estate'][$tsd]['RateName'][$rate_count]['Years'][$y]['Season'][$s]['Rate'][$r]['DisplayRate'] = $rt->getElementsByTagName('DisplayRate')->item(0)->nodeValue.($estateBySeason['Estate'][$tsd]['RateName'][$rate_count]['Years'][$y]['Season'][$s]['Rate'][$r]['plus_sign'] == 'N' ? '' : "");
							
							$estateBySeason['Estate'][$tsd]['RateName'][$rate_count]['Years'][$y]['Season'][$s]['Rate'][$r]['MinimumNightStay'] = $rt->getElementsByTagName('MinimumNightStay')->item(0)->nodeValue;
							
							$estateBySeason['Estate'][$tsd]['RateName'][$rate_count]['Years'][$y]['Season'][$s]['Rate'][$r]['NRate'] = $rt->getElementsByTagName('NRate')->item(0)->nodeValue;
							
							$estateBySeason['Estate'][$tsd]['RateName'][$rate_count]['Years'][$y]['Season'][$s]['Rate'][$r]['Inclusions'] = '<ul>'.(string)trim(html_entity_decode($rt->getElementsByTagName('Inclusions')->item(0)->nodeValue)).'</ul>';
							
							$estateBySeason['Estate'][$tsd]['RateName'][$rate_count]['Years'][$y]['Season'][$s]['Rate'][$r]['TotalExtraChargesPercentage'] = ($estateBySeason['Estate'][$tsd]['RateName'][$rate_count]['Years'][$y]['Season'][$s]['Rate'][$r]['plus_sign'] == 'N' ? '++' : "").'Villa rates are subject to '.$rt->getElementsByTagName('TotalExtraChargesPercentage')->item(0)->nodeValue.'% service charge, taxes, etc';
	
							if( $estateBySeason['Estate'][$tsd]['RateName'][$rate_count]['Years'][$y]['Season'][$s]['Rate'][$r]['sid'] == 'LO' ):
								$loDatesAndNights[$ldn]['period'] = $estateBySeason['Estate'][$tsd]['RateName'][$rate_count]['Years'][$y]['Season'][$s]['Rate'][$r]['period'];
								$loDatesAndNights[$ldn]['MinimumNightStay'] = $estateBySeason['Estate'][$tsd]['RateName'][$rate_count]['Years'][$y]['Season'][$s]['Rate'][$r]['MinimumNightStay'];
								$loDatesAndNights[$ldn]['Year'] = $year->getAttribute('year');
								$ldn++;
							endif;
							if( $estateBySeason['Estate'][$tsd]['RateName'][$rate_count]['Years'][$y]['Season'][$s]['Rate'][$r]['sid'] == 'SH' ):
								$midDatesAndNights[$mdn]['period'] = $estateBySeason['Estate'][$tsd]['RateName'][$rate_count]['Years'][$y]['Season'][$s]['Rate'][$r]['period'];
								$midDatesAndNights[$mdn]['MinimumNightStay'] = $estateBySeason['Estate'][$tsd]['RateName'][$rate_count]['Years'][$y]['Season'][$s]['Rate'][$r]['MinimumNightStay'];
								$midDatesAndNights[$mdn]['Year'] = $year->getAttribute('year');
								$mdn++;
							endif;
							if( $estateBySeason['Estate'][$tsd]['RateName'][$rate_count]['Years'][$y]['Season'][$s]['Rate'][$r]['sid'] == 'HI' ):
								$hiDatesAndNights[$hdn]['period'] = $estateBySeason['Estate'][$tsd]['RateName'][$rate_count]['Years'][$y]['Season'][$s]['Rate'][$r]['period'];
								$hiDatesAndNights[$hdn]['MinimumNightStay'] = $estateBySeason['Estate'][$tsd]['RateName'][$rate_count]['Years'][$y]['Season'][$s]['Rate'][$r]['MinimumNightStay'];
								$hiDatesAndNights[$hdn]['Year'] = $year->getAttribute('year');
								$hdn++;
							endif;
							if( $estateBySeason['Estate'][$tsd]['RateName'][$rate_count]['Years'][$y]['Season'][$s]['Rate'][$r]['sid'] == 'PK' ):
								$pkDatesAndNights[$pdn]['period'] = $estateBySeason['Estate'][$tsd]['RateName'][$rate_count]['Years'][$y]['Season'][$s]['Rate'][$r]['period'];
								$pkDatesAndNights[$pdn]['MinimumNightStay'] = $estateBySeason['Estate'][$tsd]['RateName'][$rate_count]['Years'][$y]['Season'][$s]['Rate'][$r]['MinimumNightStay'];
								$pkDatesAndNights[$pdn]['Year'] = $year->getAttribute('year');
								$pdn++;
							endif;
							$r++;
						endforeach;
						$s++;
					endforeach;
					$y++;
				endforeach;
				$rate_count++;
			endforeach;
			
			$tsd++;
		endforeach;
		
		/* End Process By Season */
		
		$theYears = array_values(array_unique($theYears));
		
		$loDatesAndNights = $this->galick_gun($loDatesAndNights);
		$loDupNight = $this->special_beam_cannon($loDatesAndNights);
		
		$midDatesAndNights = $this->galick_gun($midDatesAndNights);
		$midDupNight = $this->special_beam_cannon($midDatesAndNights);
		
		$hiDatesAndNights = $this->galick_gun($hiDatesAndNights);
		$hiDupNight = $this->special_beam_cannon($hiDatesAndNights);
		
		$pkDatesAndNights = $this->galick_gun($pkDatesAndNights);
		$pkDupNight = $this->special_beam_cannon($pkDatesAndNights);
		
		
		/* COUNT COLUMNS PER YEAR */
		$yrColCount = array();
		
		$lcol = 0;
		$mcol = 0;
		$hcol = 0;
		$pcol = 0;
		$ycount = 0;
		
		for($yrc = 0; $yrc < sizeof($theYears); $yrc++):
			for($yc1 = 0; $yc1 <= sizeof($loDatesAndNights); $yc1++):
				if($loDatesAndNights[$yc1]['Year'] == $theYears[$yrc]) {$lcol = 1;}
			endfor;
			
			for($yc2 = 0; $yc2 <= sizeof($midDatesAndNights); $yc2++):
				if($midDatesAndNights[$yc2]['Year'] == $theYears[$yrc]) {$mcol = 1;}
			endfor;
			
			for($yc3 = 0; $yc3 <= sizeof($hiDatesAndNights); $yc3++):
				if($hiDatesAndNights[$yc3]['Year'] == $theYears[$yrc]) {$hcol = 1;}
			endfor;
			
			for($yc4 = 0; $yc4 <= sizeof($pkDatesAndNights); $yc4++):
				if($pkDatesAndNights[$yc4]['Year'] == $theYears[$yrc]) {$pcol = 1;}
			endfor;
			
			$yrColCount[$theYears[$yrc]] = $lcol + $mcol + $hcol + $pcol + 1;
			
			$lcol = 0;
			$mcol = 0;
			$hcol = 0;
			$pcol = 0;
			
		endfor;
		
		/**/
		
		
		
		/* Start Display */
		/*$html = '<style>
		.datesContainer{} 
		/*.season{float:left;height:30px;}*/
		/* .minstay{margin-top:25px !important;} 
		.minstay{margin-top:40px !important;}
		.tblrates3 th, .tblrates3 td {
			background: #696969 none repeat scroll 0% 0%;
			color: #FFF;
			text-align: center;
			padding: 7px 10px;
			font-weight: normal;
			line-height: 14px;
			font-size: 11px;
		}
		.tblrates3 th strong, .tblrates3 th.tblheading {
			text-align: center;
			padding: 7px 3px;
			font-weight: normal;
			font-size: 12px;
		}
		#tooltip
		{
			font-size: 0.875em;
			text-align: center;
			text-shadow: 0 1px rgba( 0, 0, 0, .5 );
			line-height: 1.5;
			color: #fff;
			background: #333;
			background: -webkit-gradient(linear,left top,left bottom, from( rgba( 0, 0, 0, .6 ) ), to( rgba( 0, 0, 0, .8 ) ) );
			background: -webkit-linear-gradient( top, rgba( 0, 0, 0, .6 ), rgba( 0, 0, 0, .8 ) );
			background: -moz-linear-gradient( top, rgba( 0, 0, 0, .6 ), rgba( 0, 0, 0, .8 ) );
			background: -ms-radial-gradient( top, rgba( 0, 0, 0, .6 ), rgba( 0, 0, 0, .8 ) );
			background: -o-linear-gradient( top, rgba( 0, 0, 0, .6 ), rgba( 0, 0, 0, .8 ) );
			background: linear-gradient( top, rgba( 0, 0, 0, .6 ), rgba( 0, 0, 0, .8 ) );
			-webkit-border-radius: 5px;
			-moz-border-radius: 5px;
			border-radius: 5px;
			border-top: 1px solid #fff;
			-webkit-box-shadow: 0 3px 5px rgba( 0, 0, 0, .3 );
			-moz-box-shadow: 0 3px 5px rgba( 0, 0, 0, .3 );
			box-shadow: 0 3px 5px rgba( 0, 0, 0, .3 );
			position: absolute;
			z-index: 100;
			padding: 15px;
		}
		#tooltip:after
		{
			width: 0;
			height: 0;
			border-left: 10px solid transparent;
			border-right: 10px solid transparent;
			border-top: 10px solid #333;
			border-top-color: rgba( 0, 0, 0, .7 );
			content: "";
			position: absolute;
			left: 50%;
			bottom: -10px;
			margin-left: -10px;
		}
		#tooltip.top:after
		{
			border-top-color: transparent;
			border-bottom: 10px solid #333;
			border-bottom-color: rgba( 0, 0, 0, .6 );
			top: -20px;
			bottom: auto;
		}
		#tooltip.left:after
		{
			left: 10px;
			margin: 0;
		}
		#tooltip.right:after
		{
			right: 10px;
			left: auto;
			margin: 0;
		}
		p.formatScript{display:block;}
		</style>
		';
		
		$html .= "<script type='text/javascript' src='/resources/common/js/jquery-1.7.1.js'></script>
		<script type='text/javascript' src='/resources/common/js/jquery-ui-1.8.js'></script>
		<link href='/resources/common/css/jquery-ui-1.8.css' rel='stylesheet' type='text/css'>
		<script type='text/javascript' src='/resources/common/js/tooltips_functions.js'></script>
		";*/
		
		/* General Inclusions */
		$html ='<p class="teaser">'.$generalInclusions.'</p>';
		/* End General Inclusions */
		
		/* Display anchor link if promo is available */
		if($promo_counter > 0)
			$html .='<p class="promo_anchor"><strong>Promotions available, please click <a href="#villaPromos">here</a>.</strong></p>';
		
		/* Promo Display */
		$sp = sizeof($promoData);
		$rsize = sizeof($estateBySeason);
		
		if ($sp >= 1):
			if ($rsize >= 1):
			$html .= '<div class="promotions-box">';
			$html .= '<div class="promotions">';
					$html .='<div id="' . $promoData[$b]['Bedroom'] . '">';
					$html .= '<table class="tblrates" width="100%" border="0">';
					$html .='<tr class="promotr">
								<td align="left">
									<strong class="rates-title">Promotions</strong><ul>';
									for ($b = 0; $b < $sp; $b++):		
										$html .='<li>'.$promoData[$b]['PromoName'] . '
										'.$promoData[$b]['Description'].'</li>';
									endfor;
								$html .='</ul></td>
							</tr>';
					$html .= '</table>';
					$html .='</div>';
				$html .= '</div>';
				$html .= '</div>';
			endif;
			unset($x);
		endif;
		/* End Promo Display */
	
		/* Discount Display */
		$sd = sizeof($discountData);
		/* Start Check for Early Bird Discounts */
		$numEB = 0;
		$numLM = 0;
		$ds = false;
		for ($x = 0; $x < $sd; $x++):
			if ($discountData[$x]['discounttypeid'] == 'EB'):
				$numEB++;
			endif;
			if ($discountData[$x]['discounttypeid'] == 'LM'):
				$numLM++;
			endif;
			$start = strtotime($discountData[$x]['ValidFrom']);
			$end = strtotime($discountData[$x]['ValidTo']);
			$now = strtotime(date('d M Y'));
			
			if ($discountData[$x]['discounttypeid'] == 'LM'):
				if( $now >= $start && $now <= $end ):
					$LM_title = $discountData[$x]['DiscountName'];
					$LM_text = 'Last minute bookings enjoy up to ' . (float) $discountData[$x]['Rate'] . $discountData[$x]['ratetype'] .' discount when checking in before '.  $discountData[$x]['LastMinute'] . '.';
					$ds = true;
				endif;
			endif;
		endfor;
		for($dsm = 0; $dsm <= sizeof($dSummary); $dsm++):
			/* Start Long Stay */
			if ( $dSummary[$dsm]['discounttypeid'] == 'LS' ):
				$LS_title = $dSummary[$dsm]['DiscountName'];
				$LS_text = 'Stay '.$dSummary[$dsm]['requirednights'].' nights or more and receive up to '.(int)$dSummary[$dsm]['Rate'].$dSummary[$dsm]['ratetype'].' discount.';
				$ds = true;
			endif;
			
			/* End Long Stay */
		endfor;
		/* End Check for Early Bird Discounts */
		unset($x);
		$disc_text = '';
		if ($numEB >= 1 || $numLM >= 1):
			$html .= '<div class="discounts-box">';
			$html .= '<div class="discounts">';
			$html .= '<table class="tblrates" width="100%" border="0">';
			$now = strtotime(date('d M Y'));
			for ($x = 0; $x < $sd; $x++):
				$dtxt = '';
				$hd = '';
				$start = strtotime($discountData[$x]['ValidFrom']);
				$end = strtotime($discountData[$x]['ValidTo']);
			
				if ($discountData[$x]['discounttypeid'] == 'LS'):
				//$dtxt = 'for at least '.$discountData[$x]['requirednights'].' day(s) stay.';
				//$hd = 'Long Stay ';
				endif;
				
				/*
				if ($discountData[$x]['discounttypeid'] == 'EB'):
					if( $now >= $start && $now <= $end ):
						$dtxt = 'when booking ' . $discountData[$x]['advancedays'] . ' day(s) in advance.';
						$hd = 'Early bird';
						$decNum = $discountData[$x]['Rate'] + 0;
						$disc_text .='<li>' . $hd . ' discount at ' . $decNum . $discountData[$x]['ratetype'] . ' ' . $dtxt . '</li><br />';
					endif;
				endif;
				*/
			endfor;
			if($ds):
				$html .='<tr>
							<td>
								<strong class="rates-title">Discounts</strong>
								
								<ul>
									'.$EBtxt.'
									<li>'.$LM_text.'</li>
									<li>'.$LS_text.'</li>
									'.$EBnote.'
								</ul>
							</td>
						</tr>';
			endif;		
		endif;
		$html .= '</table>';
		$html .= '</div>';
		$html .= '</div>';
		
		/* End Discount Display */
	
		$sYearTab = sizeof($theYears);
		$html .= '<div id="tabs" class="tabs">
					<ul>';
		for( $xyz=0; $xyz<$sYearTab; $xyz++ ):
			$html .= '<li><a href="#'.$theYears[$xyz].'">'.$theYears[$xyz].'</a></li>';
		endfor;			
		$html .='</ul>';
		for( $abc=0; $abc<$sYearTab; $abc++ ):
			$html .='<div id="'.$theYears[$abc].'">'; /* Start Main Year Tab */
			
			$html .='<table class="tblrates3 tbl'.$abc.'"" width="100%" border="0" cellspacing="0" cellpadding="0">
						<tbody>
						<tr class="bgdark">';
						$extraChargeText = '';
						$html .='<th valign="middle" rowspan="2"><strong>Villas and inclusions</strong></th>';
						
						
						/* LO SEASON TH */
						/* CHECK TH ROWS with NO VALUES */
						$countvalue = 0;
						$slo = sizeof($loDatesAndNights);
						for( $vv=0;$vv<$slo; $vv++ ):
							if($theYears[$abc] == $loDatesAndNights[$vv]['Year'] ):
								$countvalue++;
							endif;
						endfor;
						
							/* DISPLAY WHEN VALUES FOUND */
						if($countvalue!=0):
							$html .= '<th class="bdark">
								<div class="datesContainer">
									<div class="season">';
									
									for( $ww=0;$ww<$slo; $ww++ ):
										$ld = explode("-",$loDatesAndNights[$ww]['period']);
										if( date('Y', strtotime( $ld[0] )) == $theYears[$abc] ):
											$html .=date('M j',strtotime($ld[0])).' - '.date('M j',strtotime($ld[1])).'<br />';
											if( $loDatesAndNights[$ww]['MinimumNightStay'] != $loDupNight ):
												$html .= '<span>'.$loDatesAndNights[$ww]['MinimumNightStay'].' nights min<br /></span>';
											endif;
										endif;
									endfor;
						$html .='</div>';
						$html .='<div class="minstay">';
									for( $ll=0; $ll<$slo; $ll++ ):
										$asa = explode("-",$loDatesAndNights[$ll]['period']);
										$start = strtotime($asa[0]);
										$end = strtotime($asa[1]);
										$now = strtotime(date('F j Y'));
											
					/*if( $now <= $start || ($now >= $start && $now <= $end) && $theYears[$abc] == $loDatesAndNights[$ll]['Year'] && $loDatesAndNights[$ll]['MinimumNightStay'] == $loDupNight):*/
									
										
										if( date('Y', $start) == $theYears[$abc]  ):
											if( $now <= $start || ($now >= $start && $now <= $end) && $loDatesAndNights[$ll]['MinimumNightStay'] == $loDupNight):
												$html .= $loDatesAndNights[$ll]['MinimumNightStay'].' nights min<br />';
												break;
											endif;
										endif;
									endfor;
									
									
						$html .='</div></div>';			
						$html .='</th>';
						endif;
						
						
						
						/* MID SEASON TH */
						/* CHECK TH ROWS with NO VALUES */
						$countvalue = 0;
						$smd = sizeof($midDatesAndNights);
						for( $vv=0;$vv<$smd; $vv++ ):
							if($theYears[$abc] == $midDatesAndNights[$vv]['Year'] ):
								$countvalue++;
							endif;
						endfor;
						
							/* DISPLAY WHEN VALUES FOUND */
						if($countvalue!=0):
							$html .= '<th class="bdark">
								<div class="datesContainer">
									<div class="season">';
									
									for( $ww=0;$ww<$smd; $ww++ ):
										$md = explode("-",$midDatesAndNights[$ww]['period']);
										if( date('Y', strtotime( $md[0] )) == $theYears[$abc] ):
											$html .=date('M j',strtotime($md[0])).' - '.date('M j',strtotime($md[1])).'<br />';
											if( $midDatesAndNights[$ww]['MinimumNightStay'] != $midDupNight ):
												$html .= '<span>'.$midDatesAndNights[$ww]['MinimumNightStay'].' nights min<br /></span>';
											endif;
										endif;
									endfor;
						$html .='</div>';
						$html .='<div class="minstay">';
									for( $ll=0; $ll<$smd; $ll++ ):
										$asa = explode("-",$midDatesAndNights[$ll]['period']);
										$start = strtotime($asa[0]);
										$end = strtotime($asa[1]);
										$now = strtotime(date('F j Y'));
											
					/*if( $now <= $start || ($now >= $start && $now <= $end) && $theYears[$abc] == $loDatesAndNights[$ll]['Year'] && $loDatesAndNights[$ll]['MinimumNightStay'] == $loDupNight):*/
									
										
										if( date('Y', $start) == $theYears[$abc]  ):
											if( $now <= $start || ($now >= $start && $now <= $end) && $midDatesAndNights[$ll]['MinimumNightStay'] == $midDupNight):
												$html .= $midDatesAndNights[$ll]['MinimumNightStay'].' nights min<br />';
												break;
											endif;
										endif;
									endfor;
									
									
						$html .='</div></div>';			
						$html .='</th>';
						endif;
						
						
						
						/* HIGH SEASON TH */
						/* CHECK TH ROWS with NO VALUES */
						$countvalue = 0;
						$shi = sizeof($hiDatesAndNights);
						//print '<pre>';
						//print_r($hiDatesAndNights);exit;
						for( $vv=0;$vv<$shi; $vv++ ):
							if($theYears[$abc] == $hiDatesAndNights[$vv]['Year'] ):
								$countvalue++;
							endif;
						endfor;
						
							/* DISPLAY WHEN VALUES FOUND */
						if($countvalue!=0):
							$html .= '<th class="bdark">
									<div class="datesContainer">
										<div class="season">';
										
										for( $ss=0;$ss<$shi; $ss++ ):
											$hd = explode("-",$hiDatesAndNights[$ss]['period']);
											if( date('Y', strtotime( $hd[0] )) == $theYears[$abc] ):
										
												$html .=date('M j',strtotime($hd[0])).' - '.date('M j',strtotime($hd[1])).'<br />';
												if( $hiDatesAndNights[$ss]['MinimumNightStay'] != $hiDupNight ):
													
													$html .= '<div class="minstay">'.$hiDatesAndNights[$ss]['MinimumNightStay'].' nights min<br /></div>';
												endif;
											endif;
										endfor;
							$html .='</div>';
									
							$html .='<div class="minstay">';
										for( $hh=0; $hh<$shi; $hh++ ):
											$asa = explode("-",$hiDatesAndNights[$hh]['period']);
											$start = strtotime($asa[0]);
											$end = strtotime($asa[1]);
											$now = strtotime(date('F j Y'));
											$ynow = date('Y');
											
											//if( $now <= $start || ($now >= $start && $now <= $end) && $theYears[$abc] == $hiDatesAndNights[$hh]['Year'] && $hiDatesAndNights[$hh]['MinimumNightStay'] == $hiDupNight):
											if( date('Y', $start) == $theYears[$abc]  ):
												if( $now <= $start || ($now >= $start && $now <= $end) && $hiDatesAndNights[$hh]['MinimumNightStay'] == $hiDupNight):
													$html .= $hiDatesAndNights[$hh]['MinimumNightStay'].' nights min<br />';
													break;
												endif;
											endif;
										endfor;
							$html .='</div></div>';
							$html .= '</th>';
						endif;
						/* END DISPLAY OF VALUES FOUND*/
						
						
						/* PEAK SEASON TH */
						/* CHECK TH ROWS with NO VALUES */
						$countvalue = 0;
						$kk = sizeof($pkMinNightStay);
						$spk = sizeof($pkDatesAndNights);
					
						for( $vv=0;$vv<$spk; $vv++ ):
							if($theYears[$abc] == $pkDatesAndNights[$vv]['Year'] ):
								$countvalue++;
							endif;
						endfor;
						
							/* DISPLAY WHEN VALUES FOUND */
						if($countvalue!=0):	
							$html .= '<th class="bdark">
								<div class="datesContainer">
									<div class="season">';
									
									for( $xx=0;$xx<$spk; $xx++ ):
										$pd = explode("-",$pkDatesAndNights[$xx]['period']);
										if( date('Y', strtotime( $pd[0] )) == $theYears[$abc] ):
											$html .=date('M j',strtotime($pd[0])).' - ';
											$html .=date('Y',strtotime($pd[1])) == $theYears[$abc] ? date('M j',strtotime($pd[1])) : date('M j Y',strtotime($pd[1])).'<br />';
											if( $pkDatesAndNights[$xx]['MinimumNightStay'] != $pkDupNight ):
										
												$html .=$pkDatesAndNights[$xx]['MinimumNightStay'].' nights min<br />';
											endif;
											
										endif;
									endfor;
									
							$html .='</div>';
							$html .='<div class="minstay">';
										for( $pp=0; $pp<$spk; $pp++ ):
											$ay = explode("-",$pkDatesAndNights[$pp]['period']);
											$fyear = date('Y',strtotime($ay[0]));
											if( $pkDatesAndNights[$pp]['MinimumNightStay'] == $pkDupNight && ($fyear==$theYears[$abc]) ):
												$html .= $pkDatesAndNights[$pp]['MinimumNightStay'].' nights min<br />';
											endif;
										endfor;
							$html .='</div></div>';		
							$html .= '</th>';
						endif;
						/* END DISPLAY OF VALUES FOUND*/
						
						$html .= '</tr>';
						$rowspan = $yrColCount[$theYears[$abc]]-1;
						$html .= '<tr class="pernight"><th colspan="'. $rowspan .'">USD ++ / night</th></tr>';
			$sBySeason = sizeof($estateBySeason['Estate']);
			for( $e=0; $e<$sBySeason; $e++ ):
				if( $estateBySeason['Estate'][$e]['isinternaluse'] == 'Y' ):
					$html .='<tr class="bgdark">
						<th colspan="'.$yrColCount[$theYears[$abc]].'" class="tblheading"><strong>Connecting villas</strong>
						</th>
					</tr>';
				endif;
				$sRates = sizeof($estateBySeason['Estate'][$e]['RateName']);
				
				
				//$clsrow1 = "class='odd'";
				//$clsrow2 = "class=''";
				for( $a=0; $a<$sRates; $a++ ):			
					//$row_color = ($a % 2) ? $clsrow1 : $clsrow2 ;				
					$sYear = sizeof($estateBySeason['Estate'][$e]['RateName'][$a]['Years']);
					for( $ys=0; $ys<$sYear; $ys++ ):					
						if($estateBySeason['Estate'][$e]['RateName'][$a]['Years'][$ys]['Year'] == $theYears[$abc]):
							//$html .='<tr '.$row_color.' >
							/*$html .='<tr >
								<td>'.$estateBySeason['Estate'][$e]['villaname'].' '.$estateBySeason['Estate'][$e]['RateName'][$a]['roomname'].'&nbsp;<img src="/resources/common/images/inclusions_blue.png" border="0" id="'.$estateBySeason['Estate'][$e]['RateName'][$a]['bedrooms'].$theYears[$abc].'rates_inclusion'.$ys.'-'.$data['estate_id'].'" href="javascript:void(0)" class="" rel="tooltip" style="cursor:pointer;" title="'.htmlentities($estateBySeason['Estate'][$e]['RateName'][$a]['Years'][$ys]['Season'][0]['Rate'][0]['Inclusions']).'" /></td>';*/
                            $html .='<tr >
								<td>'.$estateBySeason['Estate'][$e]['villaname'].' '.$estateBySeason['Estate'][$e]['RateName'][$a]['roomname'].'<div class="inclusions">
                                        <i class="fa fa-file-text-o" id="'.$estateBySeason['Estate'][$e]['RateName'][$a]['bedrooms'].$theYears[$abc].'rates_inclusion'.$ys.'-'.$data['estate_id'].'" aria-hidden="true" data-popover="'.htmlentities($estateBySeason['Estate'][$e]['RateName'][$a]['Years'][$ys]['Season'][0]['Rate'][0]['Inclusions']).'"></i> 
                                    </div></td>';
							$sSeason = sizeof($estateBySeason['Estate'][$e]['RateName'][$a]['Years'][$ys]['Season']);
							for( $o=0; $o<$sSeason; $o++ ):
								$sRate = sizeof($estateBySeason['Estate'][$e]['RateName'][$a]['Years'][$ys]['Season'][$o]['Rate']);
								for( $n=0; $n<$sRate; $n++ ):
									$extraChargeText = $estateBySeason['Estate'][$e]['RateName'][$a]['Years'][$ys]['Season'][0]['Rate'][0]['TotalExtraChargesPercentage'];
									if( $estateBySeason['Estate'][$e]['RateName'][$a]['Years'][$ys]['Season'][$o]['Rate'][$n]['sid'] == 'LO' && ($theYears[$abc] == $estateBySeason['Estate'][$e]['RateName'][$a]['Years'][$ys]['Season'][$o]['Rate'][$n]['year'])  ):
										
										$low = $estateBySeason['Estate'][$e]['RateName'][$a]['Years'][$ys]['Season'][$o]['Rate'][$n]['DisplayRate']!=""? $estateBySeason['Estate'][$e]['RateName'][$a]['Years'][$ys]['Season'][$o]['Rate'][$n]['DisplayRate']:"-";
									endif;
									
									if( $estateBySeason['Estate'][$e]['RateName'][$a]['Years'][$ys]['Season'][$o]['Rate'][$n]['sid'] == 'SH' && ($theYears[$abc] == $estateBySeason['Estate'][$e]['RateName'][$a]['Years'][$ys]['Season'][$o]['Rate'][$n]['year'])  ):
										
										$mid = $estateBySeason['Estate'][$e]['RateName'][$a]['Years'][$ys]['Season'][$o]['Rate'][$n]['DisplayRate']!=""? $estateBySeason['Estate'][$e]['RateName'][$a]['Years'][$ys]['Season'][$o]['Rate'][$n]['DisplayRate']:"-";
									endif;
									
									if( $estateBySeason['Estate'][$e]['RateName'][$a]['Years'][$ys]['Season'][$o]['Rate'][$n]['sid'] == 'HI' && ($theYears[$abc] == $estateBySeason['Estate'][$e]['RateName'][$a]['Years'][$ys]['Season'][$o]['Rate'][$n]['year']) ):
										
										$high = $estateBySeason['Estate'][$e]['RateName'][$a]['Years'][$ys]['Season'][$o]['Rate'][$n]['DisplayRate']!=""? $estateBySeason['Estate'][$e]['RateName'][$a]['Years'][$ys]['Season'][$o]['Rate'][$n]['DisplayRate']:"-";
									endif;
									
									if( $estateBySeason['Estate'][$e]['RateName'][$a]['Years'][$ys]['Season'][$o]['Rate'][$n]['sid'] == 'PK' && ($theYears[$abc] == $estateBySeason['Estate'][$e]['RateName'][$a]['Years'][$ys]['Season'][$o]['Rate'][$n]['year']) ):
										$peak = $estateBySeason['Estate'][$e]['RateName'][$a]['Years'][$ys]['Season'][$o]['Rate'][$n]['DisplayRate']!=""? $estateBySeason['Estate'][$e]['RateName'][$a]['Years'][$ys]['Season'][$o]['Rate'][$n]['DisplayRate']:"-";
									
									endif;
								endfor;						
							endfor;				
							
							/*
							$html.='<td>'.$low.'</td>
									<td>'.$high.'</td>
									<td>'.$peak.'</td>';
							*/
						
								
							if( $low != "" || $low == "-" ):
								$low = substr($low,-3) == .00 ? number_format($low) : number_format($low,2);
								$html.='<td>'.$low.'++</td>';
							else:
								$html.='<td class="hide"></td>';
							endif;
							
							if( $mid != "" || $mid == "-" ):
								$mid = substr($mid,-3) == .00 ? number_format($mid) : number_format($mid,2);
								$html.='<td>'.$mid.'++</td>';
							//else:
								//$html.='<td class="hide"></td>';
							endif;
							
							if($high!=""):
								$high = substr($high,-3) == .00 ? number_format($high) : number_format($high,2);
								
								$html.='<td>'.$high.'++</td>';
							else:
								$html.='<td class="hide">N/A</td>';
							endif;
									
							if($peak!=""):
								$peak = substr($peak,-3) == .00 ? number_format($peak) : number_format($peak,2);
								$html.='<td>'.$peak.'++</td>';
							//else:
								//$html.='<td class="hide"></td>';
							endif;
							
							$html .='</tr>';
						endif;
					endfor;
					unset($low,$mid,$high,$peak);
				endfor;
							
			endfor;
			$html .='<tr><td class="vrsubject bdark" colspan="'.$yrColCount[$theYears[$abc]].'" height="25" valign="middle">'.$extraChargeText.'</td></tr>';
			$html .='</table>';
			$html .='</div>';
		endfor;
		$html .='</div>';							
		/* End Display */
		
		if($promo_counter > 0):
			$html .= '<div id="villaPromos" name="villaPromos" class="anchorpromo" style="margin-top:20px;"><h3>PROMOTIONS</h3><br />';
			$html .= $promo_display;
			$html .= '</div>';
		endif;
		
		$html .= '<p class="rates-link" data-dialog="http://vw01.aws-jp.marketingvillas.com/tc.html #terms">Read our terms &amp; conditions</p>';
		/*$html .= '<p id="opener" class="pLink">Read our terms &amp; conditions</p>';
		$html .='<div id="dialog"></div>';
		$html .="<script type='text/javascript'>
					$(document).ready(function(){
						$('#dialog').load('http://vw01.aws-jp.marketingvillas.com/tc.html #terms');
					});
				</script>";*/
        
        
		/* End Terms and Conditions */
		return $html;
	}
	
	private function final_flash($array)
	{
		$size = count($array);
		for ($i=0; $i<$size; $i++):
				for ($j=0; $j<$size-1-$i; $j++):
					$from1 = explode('-',$array[$j]);
					$from2 = explode('-',$array[$j+1]);
					if (strtotime($from2[0]) < strtotime($from1[0])):
						$tmp = $array[$j];
						$array[$j] = $array[$j+1];
						$array[$j+1] = $tmp;
					endif;
				endfor;
		endfor;
		return $array;
	}
	
	private function special_beam_cannon($array)
	{ 
		$duplicate_night = '';
		$size = sizeof($array);
		if(sizeof($array) == 1):
			$duplicate_night = $array[0]['MinimumNightStay'];
		endif;
		for( $s=0; $s<$size; $s++ ):
			for( $t=0; $t<$size-1-$s; $t++ ):
				if( $array[$s]['MinimumNightStay']==$array[$t]['MinimumNightStay'] ):
					$duplicate_night = $array[$s]['MinimumNightStay'];
				endif;
			endfor;
		endfor;
		return $duplicate_night;
	}
	
	private function galick_gun($arr)
	{
		foreach($arr as $key => $value):
			foreach($arr as $key2 => $value2):
				if($value2 == $value && $key != $key2):
					unset($arr[$key]);
				endif;
			endforeach;
		endforeach;
		return array_values($arr);
	}
	
	/* Start Get Promo Only. Added September 2, 2015 */
	public function get_promo($data)
	{
		$sabonCurl = new sabonCurl();
		$sabonRequests = new sabonRequests();
		
		$timeTokenHash = $sabonCurl->cheeze_curls('Security_GetTimeToken',"",TRUE,FALSE,"","",$data['db']);
		if(!is_array($timeTokenHash))
			$timeTokenHash = html_entity_decode($timeTokenHash);
			
		$params['p_ToHash'] = 'villaprtl|'.(($data['db']=='uat'||$data['db']=='tryme')?'demo12345678':($data['db']=='prod'?'Xr4g2RmU':'')).'|'.$timeTokenHash[0];
		$hashString = $sabonRequests->prepare_Security_GetMD5Hash($params);
		$md5Hash = $sabonCurl->cheeze_curls('Security_GetMD5Hash',$hashString,TRUE,FALSE,"","",$data['db']);
		
		/* Get Discount text. */
		$vid = array("VillaID" => $data['villa_id']);
		$p_Params = json_encode($vid); /* json_encode the $vid array */
		$p_UserID = 'villaprtl';
		$p_Token = $md5Hash[0];
		$vrequest = 'p_Token='.$p_Token.'&p_UserID='.$p_UserID.'&p_Params='.$p_Params;
		$rates = $sabonCurl->cheeze_curls('getAllVillaRates', $vrequest, TRUE, TRUE, $data['villa_id'], $xpath,$data['db']);
		
		$theVillaData = new DOMDocument();
		$theVillaData->load($xpath.$data['villa_id'].'.xml.cache');
	
		$generalInclusions = (string)trim(html_entity_decode($theVillaData->getElementsByTagName('GenInclusions')->item(0)->nodeValue));
		$generalInclusions = str_replace('<br /><br />','<br />',$generalInclusions);
		/* End Get Discount text */
		
		/* Start Parse Promotions */
		$promoData = array();
		$pr = 0;
		$promotions = $theVillaData->getElementsByTagName('Promo');
		foreach ($promotions as $promo):
			$promoData[$pr]['PromoName'] = (string) trim(html_entity_decode($promo->getElementsByTagName('PromoName')->item(0)->nodeValue));
			$promoData[$pr]['Description'] = (string) trim(html_entity_decode($promo->getElementsByTagName('Description')->item(0)->nodeValue));
			$promoData[$pr]['ValidFrom'] = date('d M Y', strtotime($promo->getElementsByTagName('ValidFrom')->item(0)->nodeValue));
			$promoData[$pr]['ValidTo'] = date('d M Y', strtotime($promo->getElementsByTagName('ValidTo')->item(0)->nodeValue));
			$promoData[$pr]['Bedroom'] = $promo->getAttribute('bedrooms');
			$promoData[$pr]['displaytype'] = $promo->getAttribute('displaytype');
			$pr++;
		endforeach;
		/* End Parse Promotions */
		
		/* Start Parse Discounts */
		$discountData = array();
		$d = 0;
		$discounts = $theVillaData->getElementsByTagName('Discount');
		foreach ($discounts as $dc):
			$discountData[$d]['discounttypeid'] = $dc->getAttribute('discounttypeid');
			$discountData[$d]['checkinwithin'] = $dc->getAttribute('checkinwithin');
			$discountData[$d]['advancedays'] = $dc->getAttribute('advancedays');
			$discountData[$d]['requirednights'] = $dc->getAttribute('requirednights');
			$discountData[$d]['DiscountName'] = (string) trim(html_entity_decode($dc->getElementsByTagName('DiscountName')->item(0)->nodeValue));
			$discountData[$d]['Description'] = (string) trim(html_entity_decode($dc->getElementsByTagName('Description')->item(0)->nodeValue));
			$discountData[$d]['ValidFrom'] = date('d M Y', strtotime($dc->getElementsByTagName('ValidFrom')->item(0)->nodeValue));
			$discountData[$d]['ValidTo'] = date('d M Y', strtotime($dc->getElementsByTagName('ValidTo')->item(0)->nodeValue));
			if ($discountData[$d]['discounttypeid'] == 'LM'):
				$discountData[$d]['LastMinute'] = date('d M Y', strtotime(date('d M Y') . '+ ' . $discountData[$d]['checkinwithin'] . ' day'));
			endif;
			$rtypes = $dc->getElementsByTagName('Rate');
			foreach ($rtypes as $rtype):
				$discountData[$d]['ratetype'] = $rtype->getAttribute('ratetype');
			endforeach;
			$discountData[$d]['Rate'] = $dc->getElementsByTagName('Rate')->item(0)->nodeValue;
			$d++;
		endforeach;
		/* End Parse Discounts */
		
		/* Start Parse Discount Summary */
		$dSummary = array();
		$discountSummary = $theVillaData->getElementsByTagName('Discount');
		foreach( $discountSummary as $dsum ):
			$dSummary['requirednights'] = $dsum->getAttribute('requirednights');
			$dSummary['DiscountName'] = (string) trim(html_entity_decode($dsum->getElementsByTagName('DiscountName')->item(0)->nodeValue));
			$rstypes = $dsum->getElementsByTagName('Rate');
			foreach ($rstypes as $rstype):
				$dSummary['ratetype'] = $rstype->getAttribute('ratetype');
			endforeach;
			$dSummary['Rate'] = $dsum->getElementsByTagName('Rate')->item(0)->nodeValue;
		endforeach;
		/* End Parse Discount Summary */
		
		/* General Inclusions */
		$html = '<p class="teaser">'.$generalInclusions.'</p>';
		/* End General Inclusions */
		
		/* Promo Display */
		$sp = sizeof($promoData);
		$rsize = sizeof($rrData);
		if ($sp >= 1):
			if ($rsize >= 1):
			$html .= '<div class="promotions-box">';
			$html .= '<div class="promotions">';
					$html .='<div id="' . $promoData[$b]['Bedroom'] . '">';
					$html .= '<table class="tblrates" width="100%" border="0">';
					$html .='<tr class="promotr">
								<td align="left">
									<strong class="rates-title">Promotions</strong><ul>';
									for ($b = 0; $b < $sp; $b++):		
										$html .='<li>'.$promoData[$b]['PromoName'] . '
										'.$promoData[$b]['Description'].'</li>';
									endfor;
								$html .='</ul></td>
							</tr>';
					$html .= '</table>';
					$html .='</div>';
				$html .= '</div>';
				$html .= '</div>';
			endif;
			unset($x);
		endif;
		/* End Promo Display */
	
		/* Discount Display */
		$sd = sizeof($discountData);
		/* Start Check for Early Bird Discounts */
		$numEB = 0;
		$numLM = 0;
		$ds = false;
		for ($x = 0; $x < $sd; $x++):
			if ($discountData[$x]['discounttypeid'] == 'EB'):
				$numEB++;
			endif;
			if ($discountData[$x]['discounttypeid'] == 'LM'):
				$numLM++;
			endif;
			$start = strtotime($discountData[$x]['ValidFrom']);
			$end = strtotime($discountData[$x]['ValidTo']);
			$now = strtotime(date('d M Y'));
			if ($discountData[$x]['discounttypeid'] == 'LM'):
				if( $now >= $start && $now <= $end ):
					$LM_title = $discountData[$x]['DiscountName'];
					$LM_text = 'Last minute bookings, checking in before ' . $discountData[$x]['LastMinute'] . ', enjoy a ' . (float) $discountData[$x]['Rate'] . $discountData[$x]['ratetype'] . ' discount';
					$ds = true;
				endif;
			endif;
			
			/* Start Long Stay */	
			$LS_title = $dSummary['DiscountName'];
			$LS_text = 'Stay '.$dSummary['requirednights'].' nights or more and receive up to '.(int)$dSummary['Rate'].$dSummary['ratetype'].' discount depending on your dates';
			$ds = true;
			/* End Long Stay */
		endfor;
		/* End Check for Early Bird Discounts */
		unset($x);
		if ($numEB >= 1 || $numLM >= 1):
			$html .= '<div class="discounts-box">';
			$html .= '<div class="discounts">';
			$html .= '<table class="tblrates" width="100%" border="0">';
			$now = strtotime(date('d M Y'));
			for ($x = 0; $x < $sd; $x++):
				$dtxt = '';
				$hd = '';
				if ($discountData[$x]['discounttypeid'] == 'LS'):
				//$dtxt = 'for at least '.$discountData[$x]['requirednights'].' day(s) stay.';
				//$hd = 'Long Stay ';
				endif;
				if ($discountData[$x]['discounttypeid'] == 'EB'):
					$dtxt = 'when booking ' . $discountData[$x]['advancedays'] . ' day(s) in advance.';
					$hd = 'Early bird';
				endif;
				$disc_text = '';
				if ($discountData[$x]['discounttypeid'] == 'EB'):
					$disc_text .='<strong>&bull;</strong> ' . $hd . ' discount at ' . $discountData[$x]['Rate'] . $discountData[$x]['ratetype'] . ' ' . $dtxt . '<br />';
				endif;
			endfor;
			if($ds):
				$html .='<tr>
							<td>
								<strong class="rates-title">Discounts</strong>
								'.$disc_text.'
								<ul>
									<li>'.$LM_text.'</li>
									<li>'.$LS_text.'</li>
								</ul>
							</td>
						</tr>';
			endif;		
		endif;
		$html .= '</table>';
		$html .= '</div>';
		$html .= '</div>';
		
		/* End Discount Display */
		
		return $html;
	}
	/* End Get Promo Only */
}
