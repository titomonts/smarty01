<?php

class generateMixChars()
{
	function return_mix_chars ($minlength, $maxlength, $useupper, $usespecial, $usenumbers)
	{
		$charset = "abcdefghijklmnopqrstuvwxyz";
		if ($useupper) 
			$charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		if ($usenumbers) 
			$charset .= "0123456789";
		if ($usespecial) 
			$charset .= "~@#$%^*()_+-={}|]["; // Note: using all special characters this reads: "~!@#$%^&*()_+`-={}|\\]?[\":;'><,./";
		if ($minlength > $maxlength) 
			$length = mt_rand ($maxlength, $minlength);
		else 
			$length = mt_rand ($minlength, $maxlength);

		for ($i=0; $i<$length; $i++) 
		{
			$key .= $charset[(mt_rand(0,(strlen($charset)-1)))];
		}
		return $key;	
	}
}

// end of file generatemixchars.php