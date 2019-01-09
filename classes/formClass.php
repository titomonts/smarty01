<?php
/*
	Class for manipulating forms
*/
class forms extends villa{
	/*
		Function name: display_reservation_form()
	*/
	public function display_reservation_form($vid,$vname,$cb="",$postvars="",$site_key,$ismobile=FALSE)
	{
		$countries = parent::curly_tops('DisplayAllCountries',"",TRUE,FALSE,"","","prod");
		$defBR = '';
		$preArrival = '';
		$preDeparture = '';
		
		if( $postvars != "" ):
			$defBR = $postvars['bdr']!=""?$postvars['bdr']:"";
			$preArrival = $postvars['txtPreArriveDate'];
			$preDeparture = $postvars['txtPreDepartDate'];
		endif;
		
		/* Get Country by IP */
		$timeTokenHash = parent::curly_tops('Security_GetTimeToken',"",TRUE,FALSE,"","","prod");
		if(!is_array($timeTokenHash))
			$timeTokenHash = html_entity_decode($timeTokenHash);
			
		$params['p_ToHash'] = 'villaprtl|Xr4g2RmU|'.$timeTokenHash[0];
		$hashString = parent::prepare_Security_GetMD5Hash($params);
		$md5Hash = parent::curly_tops('Security_GetMD5Hash',$hashString,TRUE,FALSE);
		
		$qString = 'p_Token='.$md5Hash[0].'&p_UserID=VILLAPRTL&p_IPAddress='.parent::get_ip();
		$ipByCountry = parent::curlIP($qString,TRUE);
		if(!is_array($ipByCountry))
			$ipByCountry = html_entity_decode($ipByCountry);
		
		$defaultCountry = $ipByCountry['ID'];
		$defaultCode = $ipByCountry['PhoneCode'];
		/* End get country by IP */
		
		/* Check if the villa will use checkboxes */
		$checkboxes = '';
		if(!empty($cb)):
			$scb = sizeof($cb);
			for($l=0; $l<$scb; $l++):
				$checkboxes .='<span class="checkbox"><input type="checkbox" class="res_form_checkbox" id="villaids_'.$l.'" name="villaids[]" value="'.$cb[$l]['villa_name'].'" /><label for="villaids_'.$l.'" class="left"></label> <label for="villaids_'.$l.'">'.$cb[$l]['villa_name'].'</label></span>';
			endfor;
		endif;
		/* End check if the villa will use checkboxes */
		
		$form = '<script src="https://www.google.com/recaptcha/api.js" async defer></script>
		<p class="teaser">
                    This is an enquiry only form. Please enter your details below and we’ll get back to you shortly. Once you’re happy with all the terms and a deposit has been paid we’ll confirm your reservation. No payment is required at this stage.
                </p>
                <form id="aspnetForm" name="aspnetForm" method="post" action="/reservation-sent.html">
				<input type="hidden" id="villaID" name="villaID" value="'.$vid.'" />
				<input type="hidden" id="hidVillaName" name="hidVillaName" value="'.$vname.'" />
				<input type="hidden" id="reserve" name="reserve" value="'.$site_key.'"/>
				<input type="hidden" id="hfrurl" name="hfrurl" value="" />
				<input type="hidden" id="hidToken" name="hidToken" value="'.$md5Hash[0].'" />
				<input type="hidden" id="hid_cip" name="hid_cip" value="'.parent::get_ip().'" />
                <h2>'.$vname.'</h2>';
					if($checkboxes != ''):
						$form .='<div class="form-row">';
						$form .='<div class="form-col form-label"><label>Select your villa(s):</label></div><div class="form-col form-field form-estate">'.$checkboxes.'</div>';
						$form .='</div>';
					endif;                            
                $form .= '<div class="form-row">
                    <div class="form-col form-label">
                        <label for="txtFirstname">Name:</label>
                    </div>
                    <div class="form-col form-field">
			<div class="input"><input name="txtFirstname" type="text" maxlength="25" id="txtFirstname" class="inputbox required" placeholder="Given Name" required="" pattern="[a-zA-Z0-9\s]+" oninvalid="this.setCustomValidity(\'Please enter alpha numeric characters\')" oninput="this.setCustomValidity(\'\')" /><span class="required">*</span></div>
                        <div class="input"><input name="txtLastName" type="text" maxlength="25" id="txtLastName" class="inputbox required" placeholder="Family Name" required="" pattern="[a-zA-Z0-9\s]+" oninvalid="this.setCustomValidity(\'Please enter alpha numeric characters\')" oninput="this.setCustomValidity(\'\')" /><span class="required">*</span></div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-col form-label">
                        <label for="txtEmail">Email:</label>
                    </div>
                    <div class="form-col form-field">
                        <div class="input"><input type="email" pattern="(?!(^[.-].*|[^@]*[.-]@|.*\.{2,}.*)|^.{254}.)([a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~.-]+@)(?!-.*|.*-\.)([a-zA-Z0-9-]{1,63}\.)+[a-zA-Z]{1,15}" id="txtEmail" name="txtEmail" value="" maxlenght="25" class="inputbox email required" placeholder="you@yourdomain.com" required="" oninvalid="this.setCustomValidity(\'Please enter a valid email address\')" oninput="this.setCustomValidity(\'\')" /><span class="required">*</span></div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-col form-label">
                        <label for="txtArrivalDate">Dates:</label>
                    </div>
                    <div class="form-col form-field">
                        <div class="input"><input name="txtArrivalDate" type="text" id="txtArrivalDate" class="inputbox" readonly="readonly" placeholder="Arrival Date" required="" value="'.($preArrival!=""?$preArrival:"").'" /><span class="required">*</span></div>
                        <div class="input"><input name="txtDepartDate" type="text" id="txtDepartDate" class="inputbox" readonly="readonly" placeholder="Departure Date" required="" value="'.($preDeparture!=""?$preDeparture:"").'" /><span class="required">*</span></div>
                        <div class="checkbox"><label for="date_flex">Dates are flexible:</label><input type="checkbox" id="date_flex" name="date_flex" value="Y"><label for="date_flex"></label></div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="checkbox"><label for="is_event">Do you intend to hold a function (wedding, special party, etc) during your stay?</label><input type="checkbox" id="is_event" name="is_event" value="Y"><label for="is_event"></label></div>
                </div>
                <div class="form-row">
                    <div class="form-col form-label">
                        <label for="numAdult">No. of guests:</label>
                    </div>
                    <div class="form-col form-field">
                        <div class="select"><label for="numAdult">Adult</label><select name="numAdult" id="numAdult">';
							for($i=1; $i<=20; $i++):
								$form .='<option value="'.$i.'">'.$i.'</option>';
							endfor;
                $form .='</select></div>
                        <div class="select"><label for="numChildren">Children <font size="1">(2-11yrs)</font></label><select name="numChildren" id="numChildren">';
							for($j=0; $j<=30; $j++):
								$form .='<option value="'.$j.'">'.$j.'</option>';
							endfor;
                $form .='</select></div>
                        <div class="select"><label for="numInfant">Infants</label><select id="numInfant" name="numInfant">';
							for($k=0; $k<=30; $k++):
								$form .='<option value="'.$k.'">'.$k.'</option>';
							endfor;
                $form .='</select></div>
                    </div>
                </div>
                <hr />
                <p>
                    Please provide a phone number we can reach you on to discuss your stay and make sure all your questions are answered.
                </p>
                <div class="form-row">
                    <div class="form-col form-label">
                        <label for="selCountry">Country:</label>
                    </div>
                    <div class="form-col form-field">
                        <div class="select"><select id="selCountry" name="selCountry" onchange="changeCode()">';
								for($c=0; $c<sizeof($countries['COUNTRY']); $c++):
									if($countries['COUNTRY'][$c]['CountryID'] == $defaultCountry):
										$form .= '<option value="'.$countries['COUNTRY'][$c]['CountryID'].'" selected="selected">'.$countries['COUNTRY'][$c]['Country'].'</option>';
									else:
										$form .= '<option value="'.$countries['COUNTRY'][$c]['CountryID'].'">'.$countries['COUNTRY'][$c]['Country'].'</option>';
									endif;
								endfor;
                $form .='</select></div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-col form-label">
                        <label for="txtPhoneNumber">Phone No.:</label>
                    </div>
                    <div class="form-col form-field">
                        <div class="input"><input type="text" id="txtPhoneAreaCode" name="txtPhoneAreaCode" value="'.$defaultCode.'" readonly="readonly" class="prefix"><input type="text" id="txtPhoneNumber" name="txtPhoneNumber" value="" pattern="\d*" maxlenght="25" class="inputbox required" placeholder="Enter your phone no." required=""><span class="required">*</span></div>
                    </div>
                    <div class="form-col form-field">
                        <div class="input"><label for="txtAltNumber">Alt. Phone:</label><input type="text" id="txtAltPhoneAreaCode" name="txtAltPhoneAreaCode" value="'.$defaultCode.'" readonly="readonly" class="prefix"><input type="text" id="txtAltNumber" name="txtAltNumber" value="" pattern="\d*" maxlenght="25" class="inputbox required" placeholder="Enter your alternate no."></div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="checkbox"><label for="alternatives">Should this villa not be available, would you like details on other similar villas?</label><input type="checkbox" id="alternatives" name="alternatives" value="Y"><label for="alternatives"></label></div>
                </div>
                <hr />
                <p><label for="txtMessage">Questions and special requests:</label></p>
                <div class="form-row">
                    <div class="textarea"><textarea name="txtMessage" rows="5" cols="20" id="txtMessage" class="inputbox_textarea" placeholder="Place your requests, message here..." pattern="[a-zA-Z0-9\s]+" maxlength="550">'.($defBR!=""?"Special request: ".$defBR.' bedrooms.':"").'</textarea></div>
                </div>
                <div class="form-row">
                    <div class="checkbox"><label for="selNewsLetter">Would you like to receive our newsletter detailing special promotions and offers?</label><input type="checkbox" id="selNewsLetter" name="selNewsLetter" value="Y"><label for="selNewsLetter"></label></div>
                </div>
		<div class="form-row">
                        <label>Apologies for the inconvenience but this is to prevent automated spam, and we appreciate your patience. Click on the tickbox below.</label>
                </div>
                <div class="form-row">
                        <div class="g-recaptcha" data-callback="callback" data-sitekey="'.$site_key.'"></div>
                </div>
                <div class="form-row">
                    <input type="submit" name="btnSend" value="Submit Enquiry" id="btnSend" disabled="disabled" />
                    <input type="reset" name="btnCancel" value="Cancel" id="btnCancel" style="display:none;">
                </div>
			</form>';
			
			$mform = '<form action="/mobile-sent.html" enctype="multipart/form-data" method="post" id="mform"> 
                    <input type="hidden" id="villaID" name="villaID" value="'.$vid.'" />
                    <input type="hidden" id="hidVillaName" name="hidVillaName" value="'.$vname.'" />
                    <input type="hidden" id="reserve" name="reserve" />
                    <input type="hidden" id="hfrurl" name="hfrurl" value="" />
                    <input type="hidden" id="hidPrefix" name="hidPrefix" value="" />
                    <input type="hidden" id="hidToken" name="hidToken" value="" />
                    <input type="hidden" id="hid_cip" name="hid_cip" value="" />
                    <input type="hidden" id="selCountry" name="selCountry" value="'.$defaultCountry.'" />
                    <label for="name">First name</label>
                    <input type="text" name="txtfirstname" id="txtfirstname" placeholder="First name" onFocus="';
			$mform .= "this.placeholder = ''"; 
			$mform .= 'onBlur="this.placeholder = ';
			$mform .= "'First name'";
			$mform .= 'required />
						<label for="name">Last name</label>
						<input type="text" name="txtlastname" id="txtlastname" placeholder="Last name" onFocus="';
			$mform .= "this.placeholder = ''";
			$mform .= 'onBlur="this.placeholder = ';
			$mform .= "'Last name'"; 
			$mform .= 'required />
						<label for="email">Email</label>
						<input type="email" pattern="(?!(^[.-].*|[^@]*[.-]@|.*\.{2,}.*)|^.{254}.)([a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~.-]+@)(?!-.*|.*-\.)([a-zA-Z0-9-]{1,63}\.)+[a-zA-Z]{1,15}" name="txtEmail" id="txtEmail" placeholder="you@yourdomain.com" onFocus="';
			$mform .= "this.placeholder = ''"; 
			$mform .= 'onBlur="this.placeholder = ';
			$mform .= "'you@yourdomain.com'"; 
			$mform .= 'required />
						<label for="phone">Phone</label>
						<input type="text" name="txtPhoneNumber" id="txtPhoneNumber" placeholder="" value="'.$defaultCode.'" />
						<label for="txtArrivalDate" class="from">From</label><label for="txtDepartDate" class="to">To</label>
						<input type="text" name="txtArrivalDate" id="txtArrivalDate" placeholder="Arrival Date" readonly class="from" required />
						<input type="text" name="txtDepartDate" id="txtDepartDate" placeholder="Departure Date" readonly class="to" required />
						<input type="submit" name="submit" id="submit" value="Submit" />
						<input type="hidden" name="page" value="mobilesent" />
					</form>';
			
			return (!$ismobile?$form:$mform);
	}
	
	/*
		Function name: mobile_reservation_form()
	*/
	public function mobile_reservation_form($vid,$vname)
	{
		$countries = parent::curly_tops('DisplayAllCountries',"",TRUE,FALSE,"","","prod");
		/* Get Country by IP */
		$timeTokenHash = parent::curly_tops('Security_GetTimeToken',"",TRUE,FALSE,"","","prod");
		if(!is_array($timeTokenHash))
			$timeTokenHash = html_entity_decode($timeTokenHash);
			
		$params['p_ToHash'] = 'villaprtl|Xr4g2RmU|'.$timeTokenHash[0];
		$hashString = parent::prepare_Security_GetMD5Hash($params);
		$md5Hash = parent::curly_tops('Security_GetMD5Hash',$hashString,TRUE,FALSE);
		
		$qString = 'p_Token='.$md5Hash[0].'&p_UserID=VILLAPRTL&p_IPAddress='.parent::get_ip();
		$ipByCountry = parent::curlIP($qString,TRUE);
		if(!is_array($ipByCountry))
			$ipByCountry = html_entity_decode($ipByCountry);
		
		$defaultCountry = $ipByCountry['ID'];
		$defaultCode = $ipByCountry['PhoneCode'];
		/* End get country by IP */
		
		$form = '<form action="/mobile-sent.html" enctype="multipart/form-data" method="post" id="mform"> 
                    <input type="hidden" id="villaID" name="villaID" value="'.$vid.'" />
                    <input type="hidden" id="hidVillaName" name="hidVillaName" value="'.$vname.'" />
                    <input type="hidden" id="reserve" name="reserve" />
                    <input type="hidden" id="hfrurl" name="hfrurl" value="" />
                    <input type="hidden" id="hidPrefix" name="hidPrefix" value="" />
                    <input type="hidden" id="hidToken" name="hidToken" value="" />
                    <input type="hidden" id="hid_cip" name="hid_cip" value="" />
                    <input type="hidden" id="selCountry" name="selCountry" value="'.$defaultCountry.'" />
                    <label for="name">First name</label>
                    <input type="text" name="txtfirstname" id="txtfirstname" placeholder="First name" onFocus="';
		$form .= "this.placeholder = ''"; 
		$form .= 'onBlur="this.placeholder = ';
		$form .= "'First name'";
	 	$form .= 'required />
                    <label for="name">Last name</label>
                    <input type="text" name="txtlastname" id="txtlastname" placeholder="Last name" onFocus="';
		$form .= "this.placeholder = ''";
		$form .= 'onBlur="this.placeholder = ';
		$form .= "'Last name'"; 
		$form .= 'required />
                    <label for="email">Email</label>
                    <input type="email" pattern="(?!(^[.-].*|[^@]*[.-]@|.*\.{2,}.*)|^.{254}.)([a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~.-]+@)(?!-.*|.*-\.)([a-zA-Z0-9-]{1,63}\.)+[a-zA-Z]{1,15}" name="txtEmail" id="txtEmail" placeholder="you@yourdomain.com" onFocus="';
		$form .= "this.placeholder = ''"; 
		$form .= 'onBlur="this.placeholder = ';
		$form .= "'you@yourdomain.com'"; 
		$form .= 'required />
                    <label for="phone">Phone</label>
                    <input type="text" name="txtPhoneNumber" id="txtPhoneNumber" placeholder="" value="'.$defaultCode.'" />
                    <label for="txtArrivalDate" class="from">From</label><label for="txtDepartDate" class="to">To</label>
                    <input type="text" name="txtArrivalDate" id="txtArrivalDate" placeholder="Arrival Date" readonly class="from" required />
                    <input type="text" name="txtDepartDate" id="txtDepartDate" placeholder="Departure Date" readonly class="to" required />
                    <input type="submit" name="submit" id="submit" value="Submit" />
                    <input type="hidden" name="page" value="mobilesent" />
                </form>';
		return $form;
	}
	
	
	/*
		Function name: display_general_enquiries_form()
	*/
	public function display_general_enquiries_form($params) 
	{
		$countries = parent::curly_tops('DisplayAllCountries', "", TRUE, FALSE);
	
		/* Get Country by IP */
		$timeTokenHash = parent::curly_tops('Security_GetTimeToken', "", TRUE, FALSE);
		if (!is_array($timeTokenHash))
			$timeTokenHash = html_entity_decode($timeTokenHash);
	
		$params['p_ToHash'] = 'villaprtl|Xr4g2RmU|' . $timeTokenHash[0];
		$hashString = parent::prepare_Security_GetMD5Hash($params);
		$md5Hash = parent::curly_tops('Security_GetMD5Hash', $hashString, TRUE, FALSE);
	
		$qString = 'p_Token=' . $md5Hash[0] . '&p_UserID=VILLAPRTL&p_IPAddress='.parent::get_ip();
		$ipByCountry = parent::curlIP($qString, TRUE);
		if (!is_array($ipByCountry))
			$ipByCountry = html_entity_decode($ipByCountry);
	
		$defaultCountry = $ipByCountry['ID'];
		$defaultCode = $ipByCountry['PhoneCode'];
		/* End get country by IP */
	
		if (!is_array($countries))
			$countries = html_entity_decode($countries);
	
		if ($params['isComplex']):
			$defaultText = 'Requested Villa: ' . $params['villaName'];
		else:
			$defaultText = '';
		endif;
		
		$form = '<script src="https://www.google.com/recaptcha/api.js" async defer></script>
		<div class="teaser">
            <p>'.$params['villaName'].' is managed and/or marketed on behalf of its owners by <a href="http://www.elitehavens.com" title="Go to The Elite Havens site" target="_blank">The Elite Havens Group</a>, who also handle all the villa reservations.</p>
            <p>To make a general enquiry, please complete the form below:</p>
        </div>                            
		<form action="/contact-sent.html" method="post" class="wpcf7-form" id="frmGenEnquiry" name="frmGenEnquiry">
            <input type="hidden" id="villaID" name="villaID" value="'.$params['villaID'].'" />
            <input type="hidden" id="hfrurl" name="hfrurl" />
            <input type="hidden" id="hidVillaName" name="hidVillaName" value="' . $params['villaName'] . '" />
            <input type="hidden" id="hid_cip" name="hid_cip" value="' . $_SERVER['REMOTE_ADDR'] . '" />
            <input type="hidden" name="sir_yes_sir" id="sir_yes_sir" />
            <div class="form-row">
                <div class="form-col form-label">
                    <label for="txtFirstname">First Name</label>
                </div>
                <div class="form-col form-field">
                    <div class="input">
			<input name="txtFirstname" type="text" maxlength="35" id="txtFirstname" class="require required" placeholder="First Name" pattern="[a-zA-Z0-9\s]+" oninvalid="this.setCustomValidity(\'Please enter alpha numeric characters\')" oninput="this.setCustomValidity(\'\')" />
                        <span class="required">*</span>
                    </div>
                </div>
            </div>
			<div class="form-row">
                <div class="form-col form-label">
                    <label for="txtLastName">Family Name</label>
                </div>
                <div class="form-col form-field">
                    <div class="input">
			<input name="txtLastName" type="text" maxlength="35" id="txtLastName" class="require inputbox" placeholder="Family Name" pattern="[a-zA-Z0-9\s]+" oninvalid="this.setCustomValidity(\'Please enter alpha numeric characters\')" oninput="this.setCustomValidity(\'\')" />
                        <span class="required">*</span>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col form-label">
                    <label for="txtEmail">Email</label>
                </div>
                <div class="form-col form-field">
                    <div class="input">
                        <input type="email" id="txtEmail" name="txtEmail" value="" maxlenght="35" class="required inputbox" placeholder="you@yourdomain.com" oninvalid="this.setCustomValidity(\'Please enter a valid email address\')" oninput="this.setCustomValidity(\'\')" />
                        <span class="required">*</span>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col form-label">
                    <label for="selCountry">Country</label>
                </div>
                <div class="form-col form-field">
                    <div class="select">
                        <select id="selCountry" name="selCountry" onchange="changeCode()">';
                        for ($c = 0; $c < sizeof($countries['COUNTRY']); $c++):
                            if ($countries['COUNTRY'][$c]['CountryID'] == $defaultCountry):
                                $form .= '<option value="' . $countries['COUNTRY'][$c]['CountryID'] . '" selected="selected">' . $countries['COUNTRY'][$c]['Country'] . '</option>';
                            else:
                                $form .= '<option value="' . $countries['COUNTRY'][$c]['CountryID'] . '">' . $countries['COUNTRY'][$c]['Country'] . '</option>';
                            endif;
                        endfor;
                $form .='</select>
                    </div>
                </div>
            </div>
			<div class="form-row">
                <div class="form-col form-label">
                    <label for="txtPhoneNumber">Phone number</label>            
                </div>
                <div class="form-col form-field">
                    <div class="input">
                        <input type="text" id="txtPhoneAreaCode" name="txtPhoneAreaCode" value="' . $defaultCode . '" class="inputbox prefix" readonly>
                        <input type="text" id="txtPhoneNumber" name="txtPhoneNumber" value="" pattern="\d*" maxlenght="25" class="required inputbox" placeholder="Enter your phone no." required="">
                        <span class="required">*</span>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col form-label">
                    <label for="messageSubject">Message subject</label>
                </div>
                <div class="form-col form-field">
                    <div class="select">
                        <select name="messageSubject" id="messageSubject">
                            <option value="Elite Havens - Luxury Villa Rentals">Luxury Villa Rentals</option>
                            <option value="Elite Havens - Villa Management">Villa Management</option>
                            <option value="General Enquiry" selected="selected">General Enquiry</option>
                            <option value="Media">Media and Press</option>
                        </select>
                    </div>
                </div>
            </div>
			<p><label for="txtMessage">Your message</label></p>
            <div class="form-row">
                <div class="textarea">
			<textarea name="txtMessage" rows="5" cols="20" id="txtMessage" class="inputbox_textarea" placeholder="Place your requests, message here..." pattern="[a-zA-Z0-9\s]+" maxlength="255"></textarea>
                </div>
            </div>
            <p>
                Apologies for the inconvenience but this is to prevent automated spam, and we appreciate your patience. Click on the tickbox below.
            </p>
	    <div class="form-row">
                <div class="g-recaptcha" data-callback="callback" data-sitekey="'.$params['google_site_key'].'"></div>
            </div>
            <div class="form-row">
                <input type="submit" name="btnGeneralEnquiries" value="Submit" id="btnGeneralEnquiries" disabled="disabled" />
            </div>			
		</form>';
		return $form;
	}
	
	/*
		Function name: complex_calendar()
	*/
	public function complex_calendar($args)
	{
		$html = '<div align="left">
				<form id="frmCal" name="frmCal" action="" method="post">
				<table width="40%" align="left">
					<tr>
						<td width="17%"><select id="selMonth" name="selMonth">'.$args['monthOpts'].'</select></td>
						<td width="14%"><select id="selYear" name="selYear">'.$args['yearOpts'].'</select></td>
						<td width="69%"><input type="submit" id="btnGo" name="btnGo" value="GO" /></td>
					</tr>
				</table>
				</form>
				</div>
				<div class="clear"></div>
				';
		$villas = explode(',',$args['vids']);
		$sv = sizeof($villas);
		for( $v=0; $v<$sv; $v++ ):
			$vn = parent::ws_villa_info($villas[$v]);		
			$html .= '<h2>'.$vn['gaData']['property']['name'].'</h2>
					'.$args['selectedMonth'].' '.$args['year'].'<br />
					<iframe id="iframeCalendar" src="http://www.privatehomesandvillas.com/phv/searchCalendar.aspx?villaid='.$vn['gaData']['property']['id'].'&month='.$args['month'].'&year='.$args['year'].'" height="60px" frameBorder="0" width="690px" allowtransparency="true" scrolling="no"></iframe>
					';
		endfor;	
		return $html;			
	}
}
