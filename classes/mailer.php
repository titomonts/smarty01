<?php
class mailerMonch{
	
	public function mailer($mail)
	{
		$fromClient = "admin@socialentrep.ateneodevstudies.net"; 
		$subjectClient = "Ateneo Philsen SE Conference Program 2011 Registration"; 
	
		/* HTML message */
		$messageClient = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml"><head></head><body>';
		$messageClient .= '<div style="border:1px solid #666;" align="left">';
		$messageClient .= '<br />Thank you for registering for our Ateneo Philsen SE Conference Program 2011';
		$messageClient .= '<br />Your registration will be confirmed after we have received payment.';
		$messageClient .= '<br /><br />';
		$messageClient .= 'Regards,<br />The Ateneo de Manila Development Studies';
		$messageClient .= '</div>';
		$messageClient .= "</body></html>";
	   /* End HTML Message */
	   
		$headersClient = "MIME-Version: 1.0"."\r\n"; 
		$headersClient .= "Content-type: text/html; charset=iso-8859-1"."\r\n"; 
		$headersClient .= "From: $fromClient\r\n";    
		mail($mail, $subjectClient, $messageClient, $headersClient);		
		//$res = ($mail == true) ? return true : return false;
	}
}

/**/