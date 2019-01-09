<?php
/*
	Essential Functions for Villa Website Simplified
	Version: 1.0 Alpha Development
	Author: Vegeta, Prince of all Saiyans
*/

function redirects($params)
{
	global $mysqli, $website;
	$mysqli = $website->connect();
    
    /* Edited by Ardian 
    * To handle both 'http://xxxx.com/xxxx.html' and 'http://xxxx.com/xxxx/'
    */
    $uri_params = ''; 
    if (strpos($params['requestUrl'], '?') > -1) {
        $uri_parts = explode('?', $params['requestUrl']);
        $params['requestUrl'] = $uri_parts[0];
        $uri_params = '?' . $uri_parts[1]; 
    }
    
    $alternate = str_lreplace('/', '', str_replace('.html', '/', $params['requestUrl']));
    $rq = $alternate . '/';
    $tg = $alternate . '.html';
    if ($params['requestUrl'] == $rq) {
        $request = "(re_request_url = '".$params['requestUrl']."' OR re_request_url = '".$tg."')";
    } else {
        $request = "re_request_url = '".$params['requestUrl']."'";
    }

	$query = $mysqli->query( "SELECT re_request_url, re_destination_url, re_status FROM smarty_redirects WHERE ".$request." AND re_domain = '".$params['site']."'" ) or die($mysqli->error);
	$num_record = $query->num_rows;
	$row = $query->fetch_assoc();
	
	if( $num_record > 0 ):
        $row['re_destination_url'] = $row['re_destination_url'] . $uri_params;
		return $row;
	else:
		return NULL;	
	endif;
}

/* Attachment function connected to MailJet API */
function attachment($name, $path)
{
  $data = file_get_contents($path);
  $base64 = base64_encode($data);
  return array(
    'Filename' => $name,
    'Content-Type' => 'application/octet-stream',
    'content' => $base64
  );
}

function str_lreplace($search, $replace, $subject)
{
    $pos = strrpos($subject, $search);

    if($pos !== false)
    {
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }

    return $subject;
}