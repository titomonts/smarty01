<?php
/*require 'classes/villaClasses.php';
$website = new villa();
$mysqli = $website->connect();
$chk = $mysqli->query( "CALL check_domain('".$_SERVER['HTTP_HOST']."')" ) or die($mysqli->error);
$row = $chk->fetch_assoc();
if( $row['num_domain']>=1 ):
	echo 'Domain is existing.';
endif;
*/
$Date1 = strtotime(date('j F o', strtotime('15 September 1970') ) ).' ';
$Date2 = strtotime(date('j F o'));

  if($Date1 < $Date2) {
        echo 'date is in the past';
    }
