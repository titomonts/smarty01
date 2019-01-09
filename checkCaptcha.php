<?php
session_start();
$captcha = $_POST['cap'];

if($captcha == $_SESSION['security_code']):
	echo "OK!";
else:
	echo "FAILED!";
endif;
/* End of ajax file */