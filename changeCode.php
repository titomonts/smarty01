<?php
if(empty($_POST) && empty($_GET)):
	die("Direct files access is not allowed");
else:
	if(isset($_GET['c']))
		$country = $_GET['c'];
	
	if(isset($_POST['c']))
		$country = $_POST['c'];
	
	define('CONF_ABSOLUTE_PATH', getcwd());
	define('MAIN_FOLDER', CONF_ABSOLUTE_PATH.'/');
	define('XML_PATH', MAIN_FOLDER.'villa-xml/');
	
	$countries = file_get_contents(XML_PATH.'countries.xml.json');
	$countries = json_decode($countries,TRUE);
	$cc = count($countries['Country']);
	
	for($c=0; $c<$cc; $c++):
		if($country == $countries['Country'][$c]['CountryId']):
			echo $countries['Country'][$c]['PhoneCode'];
		endif;
	endfor;
endif;
