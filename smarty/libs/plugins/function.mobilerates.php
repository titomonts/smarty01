<?php
function smarty_function_mobilerates($params, &$smarty)
{
	error_reporting(E_ALL ^ E_NOTICE);
	$fxCurrency = isset($_COOKIE['defCurrency'])?$_COOKIE['defCurrency']:'US$';
	$fxRate = isset($_COOKIE['defFXRate'])?$_COOKIE['defFXRate']:1.0;
	$fxRate = floatval(str_replace(',','',$fxRate));
	
	$theVillaData = new DOMDocument();
	$theVillaData->load( 'villa-xml/'.$params['folder'].'/rates/'.$params['vid'].'.xml.cache' ) or die("Can't load XML Document");
	
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
	
	/* Promo Display */
		$sp = sizeof($promoData);
		$rsize = sizeof($rrData);
		$promoData = $promoData;
		$promotxt = '';
		if ($sp >= 1):
			if ($rsize >= 1):
				$promotxt .='<div class="promotions">';
				$promotxt .='<div id="' . $promoData[$b]['Bedroom'] . '">';
				$promotxt .='<table class="tblrates" width="100%" border="0">';
				$promotxt .='<tr class="promotr">
							<td align="left">
								<strong class="ratesTitle">Promotions</strong><br /><ul>';
				for ($b = 0; $b < 1/*$sp*/; $b++):
					$promotxt .='<li>' . $promoData[$b]['PromoName'] . ' <br> ' . $promoData[$b]['Description'] . '</li>';
				endfor;
				$promotxt .='</ul></td></tr>';
				$promotxt .= '</table>';
				$promotxt .='</div>';
				$promotxt .= '</div>';
			endif;
			$promotxt .='<div class="spacing"></div>';
		endif;
	/* End Promo Display */
	
	/* Start Parse Discounts */
    $discountData = array();
    $d = 0;
    $discounts = $theVillaData->getElementsByTagName('Discount');
	
    foreach ($discounts as $dc):
		if($dc->getAttribute('discounttypeid') != 'EB'):
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
			if ($discountData[$d]['discounttypeid'] == 'LS'):
				$LongStay = $dc->getAttribute('requirednights');
			endif;
			$rtypes = $dc->getElementsByTagName('Rate');
			foreach ($rtypes as $rtype):
				$discountData[$d]['ratetype'] = $rtype->getAttribute('ratetype');
			endforeach;
			$discountData[$d]['Rate'] = $dc->getElementsByTagName('Rate')->item(0)->nodeValue;
	
			$d++;
		endif;
    endforeach;

	/* Get Discount Summary */	
	/* Start EB Discount */
	$disArr = array();
	$disArrD = array();
	$da = $d;
	
	$disSum = $theVillaData->getElementsByTagName('DiscountSummary');
	foreach ($disSum as $dSa):
		$disArr = $dSa->getElementsByTagName('Discount');
		foreach ($disArr as $dSm):
			if ($dSm->getAttribute('discounttypeid') == 'EB'):
			
				$discountData[$da]['discounttypeid'] = $dSm->getAttribute('discounttypeid');
				$discountData[$da]['checkinwithin'] = $dSm->getAttribute('checkinwithin');
				$discountData[$da]['advancedays'] = $dSm->getAttribute('advancedays');
				$discountData[$da]['DiscountName'] = (string) trim(html_entity_decode($dSm ->getElementsByTagName('DiscountName')->item(0)->nodeValue));
				$discountData[$da]['ValidFrom'] = (date('d M Y'));
				$discountData[$da]['ValidTo'] = (date('d M Y'));
				
				$rt = $dSm->getElementsByTagName('Rate');
				foreach ($rt as $rts):
					$discountData[$da]['ratetype'] = $rts->getAttribute('ratetype');
				endforeach;
				$discountData[$da]['Rate'] = $dSm->getElementsByTagName('Rate')->item(0)->nodeValue;
				$discountData[$da]['Note'] = (string) trim(html_entity_decode($dSm ->getElementsByTagName('Note')->item(0)->nodeValue));
				$discountData['EBNote'] = $discountData[$da]['Note'];
			endif;
		endforeach;
	endforeach;
	/* End Discount summary */
    /* End Parse Discounts */
	
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
			if ($now >= $start && $now <= $end):
				$LM_title = $discountData[$x]['DiscountName'];
				$LM_text = 'Last minute bookings enjoy ' . (float) $discountData[$x]['Rate'] . $discountData[$x]['ratetype'] . ' discount when checking in before '.$discountData[$x]['LastMinute'].'.';
				$ds = true;
			endif;
		endif;

	endfor;

	/* End Check for Early Bird Discounts */
	unset($x);
	$disc_text = '';
	$eb_note = '';
	$disctxt = '';
	if ($numEB >= 1 || $numLM >= 1):
		$disctxt .= '<div class="discounts">';
		$disctxt .= '<table class="tblrates" width="100%" border="0">';
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
			if ($discountData[$x]['discounttypeid'] == 'EB'):
			
				if ($now >= $start && $now <= $end):
					$decNum = $discountData[$x]['Rate'] + 0;
					$disc_text = '<li>Early bird enjoys' . $decNum . $discountData[$x]['ratetype'] . ' discount when booking '.$discountData[$x]['advancedays'].' days in advance.';
					$disc_text .= isset($discountData['EBNote']) ? '*</li>' : '</li>';
					$eb_note = isset($discountData['EBNote']) ? '<li style="list-style:none;font-style:italic;margin-top: 10px;font-size:12px;">* '.$discountData['EBNote'].'</li>' : '';
				endif;
			endif;
		endfor;
		if ($ds):
			$disctxt .='<tr>
						<td>
							<strong class="ratesTitle">Discounts</strong><br />
							<ul>' . $disc_text . '
								<li>' . $LM_text . '</li>
								'. $eb_note .'
							</ul>
						</td>
					</tr>';
		endif;
	endif;
	$disctxt .= '</table>';
	$disctxt .= '</div><div class="spacing"></div>';
	/* End Discount Display */
	
	/* Start Parse Rooms and Rates */
	$rrData = array();
	$r = 0;
	$years = array(); /* Used for Standard Rates Array */
	$y = 0;
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
	
	$years = array_values(array_unique($years));
	
	$gbed = sizeof($rrData);
	for ($q = 0; $q < $gbed; $q++):			
		$Rbed = sizeof($rrData[$q]['BedRooms']);
		$totbed = $totbed +$Rbed;
	endfor;
	
	if($totbed >=2):
		$html .= '<div id="rates">';
		$html .= '<div id="tabs" class="tabs ui-tabs">';
			$q = 0;
			$w = 0;
			$ratebedroom = sizeof($rrData);
			
			$html .= '<ul class="ui-tabs-nav">';					
				for ($q = 0; $q < $ratebedroom; $q++):				
					$Rsizebed = sizeof($rrData[$q]['BedRooms']);
					$html .= '<li><a href="#'.$rrData[$q]['BedRooms'].'bed">'.$rrData[$q]['BedRooms'].' - Bedroom</a></li>';				
				endfor;				
			$html .= '</ul>'; 
								
			for ($q = 0; $q < $ratebedroom; $q++):				
				$Rsizebedd = sizeof($rrData[$q]['BedRooms']);//get type bedroom
				for ($w = 0; $w < $Rsizebedd; $w++):
					$html .= '<div id="'.$rrData[$q]['BedRooms'].'bed" class="ui-tabs-panel">';
						$y = 0;
						$html .= '<ul class="ui-tabs-nav">'; 
							$yearsLength = sizeof($years);	
							for ($y = 0; $y < $yearsLength; $y++):
								$html .= '<li><a href="#' . $years[$y] . '">' . $years[$y] . '</a></li>';
							endfor;
						$html .= '</ul>'; 
						
						for ($z = 0; $z < $yearsLength; $z++):
							$html .= '<div class="content" id="' . $years[$z] . '"><table><tbody>';
							$html .= '<tr>';
							$html .= '<td class="colLeft period">Period</td>';
							$html .= '<td class="dispRate">US$/night</td>';
							$html .= '<td class="colRight minDayStay">Min nights</td>';
							$html .= '<td class="colRight inclusions">Inclussions</td>';
							$html .= '</tr>';
	
							
							$d = 0;
							$e = 0;
							$Rsize = sizeof($rrData[$q]['Rates']);
							for ($d = 0; $d < $Rsize; $d++):
								$RSsize = sizeof($rrData[$q]['Rates'][$d]['Rate']);
								for ($e = 0; $e < $RSsize; $e++):
									if ($rrData[$q]['Rates'][$d]['Rate'][$e]['year'] == $years[$z]):
										
										$periodeReplace1 = str_replace($years[$z], "", $rrData[$q]['Rates'][$d]['Rate'][$e]['Period']);
										$periodeReplace2 = str_replace("to", "to<br />", $periodeReplace1);
										$html .= '<tr>';
										$html .= '<td class="colLeft period">' . $periodeReplace2 . '</td>';
										$html .= '<td class="dispRate">' . $rrData[$q]['Rates'][$d]['Rate'][$e]['DisplayRate'] . '</td>';
										$html .= '<td class="colRight minDayStay">' . $rrData[$q]['Rates'][$d]['Rate'][$e]['MinDayStay'] . '</td>';
										$html .= '<td class="colRight inclusions">' . $rrData[$q]['Rates'][$d]['Rate'][$e]['Inclusions'] . '</td>';
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
	else:
		$html = '<div id="rates">';

		$y = 0;
		$yearsLength = sizeof($years);

		$html .= '<div id="tabs" class="tabs">';
		$html .= '<ul>';
		for ($y = 0; $y < $yearsLength; $y++):
			$html .= '<li><a href="#' . $years[$y] . '">' . $years[$y] . '</a></li>';
		endfor;
		$html .= '</ul>';

		for ($z = 0; $z < $yearsLength; $z++):
			$html .= '<div class="content" id="' . $years[$z] . '"><table><tbody>';
			$html .= '<tr>';
			$html .= '<td class="colLeft period">Period</td>';
			$html .= '<td class="dispRate">US$/night</td>';
			$html .= '<td class="colRight minDayStay">Min nights</td>';
			$html .= '<td class="colRight inclusions">Inclussions</td>';
			$html .= '</tr>';

			$rateSize = sizeof($rrData[0]);
			$c = 0;
			$d = 0;
			$e = 0;

			for ($c = 0; $c < $rateSize; $c++):
				$Rsize = sizeof($rrData[$c]['Rates']);
				for ($d = 0; $d < $Rsize; $d++):
					$RSsize = sizeof($rrData[$c]['Rates'][$d]['Rate']);
					for ($e = 0; $e < $RSsize; $e++):
						if ($rrData[$c]['Rates'][$d]['Rate'][$e]['year'] == $years[$z]):
							$periodeReplace1 = str_replace($mobileData['year'][$z], "", $rrData[$c]['Rates'][$d]['Rate'][$e]['Period']);
							$periodeReplace2 = str_replace("to", "to<br />", $periodeReplace1);
							$html .= '<tr>';
							$html .= '<td class="colLeft period">' . $periodeReplace2 . '</td>';
							$html .= '<td class="dispRate">' . $rrData[$c]['Rates'][$d]['Rate'][$e]['DisplayRate'] . '</td>';
							$html .= '<td class="colRight minDayStay">' . $rrData[$c]['Rates'][$d]['Rate'][$e]['MinDayStay'] . '</td>';
							$html .= '<td class="colRight inclusions">' . $rrData[$c]['Rates'][$d]['Rate'][$e]['Inclusions'] . '</td>';
							$html .= '</tr>';
						endif;
					endfor;
				endfor;
			endfor;
			$html .= '</tbody></table></div>';
		endfor;

		$html .= '</div></div>';	
	endif;
	
	$content = isset($params['show']) && $params['show'] == 'promo'? $promotxt.''.$disctxt: $html;
	$smarty->assign('rdata', $rrData);
	$smarty->assign('content',$content);
	
}