<?php
/*
	Class Name soapRequest
	Simply prepares request string for curl function to retrieve data from Marketing Villas Web Service API
	
	Web Service API Reference: http://ws.thevillaguide.com/service.asmx
	
*/
class soapRequests{
	/* FindAVilla Request String */
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

/* End of class */