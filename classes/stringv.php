<?php
/*
	=================================
	Class: stringv
	Description: string manipulations
	Monch 2006-2007
	=================================
*/
class stringv {
	/*
	Function name: formatName
	Parameters: 
		$lastName - last name
		$firstName - first name
		$middleName - middle name
		$mode - mode option
	Description: manipulates the name
	*/
	function formatName($lastName, $firstName, $middleName='', $mode=true) {
		$lastName = trim($lastName);
		$firstName = trim($firstName);
		$middleName = trim($middleName);

		$name = $lastName.", ";
		if ( strlen(trim($name)) > 18) {
			$name = substr($lastName,0,14)."..., ";
		}

		if ( strlen($firstName) > 18) {
			$name .= substr($firstName,0,15)."...";
		} else {
			$name .= $firstName;
		}

		if($mode) {
			if (strlen($middleName) != 0) {
				$name .= " ".substr($middleName,0,1).".";
			} else {
				if ( strlen($middleName) > 18) {
					$name .= substr($middleName,0,15)."...";
				} else {
					$name .= $middleName;
				}
			}
		} else {
			if ( strlen($middleName) > 18) {
				$name .= substr($middleName,0,15)."...";
			} else {
				$name .= $middleName;
			}
		}
		return $name;
	}

	/*
	Function name: formatString
	Parameters: 
		$theValue - value to be formates
		$theType - the type to convert the value to
		$theDefinedValue - default if $theType is defined
		$theNotDefinedValue - default if $theType is defined
	Description: formats the input string for the database
	*/
	function formatString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
	{
	  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;
	
	  switch ($theType) {
		case "text":
		  $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
		  break; 
		case "special_text":
		  $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
		  break;    
		case "long":
		case "int":
		  $theValue = ($theValue != "") ? intval($theValue) : "NULL";
		  break;
		case "double":
		  $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
		  break;
		case "date":
		  $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
		  break;
		case "defined":
		  $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
		  break;
	  }
	  return $theValue;
	}
	
	/*
	Function name: remove_invalid_characters
	Description: removes all invalid characters that stops the execution of saving/inserting or updating tables in the database
	*/
	function remove_invalid_characters($theValue)
	{
		$invalid_characters = array("'", 
									"''", 
									"/", 
									"'\'", 
									";", 
									"{", 
									"}", 
									"[", 
									"]", 
									"&", 
									"+", 
									">", 
									"<", 
									"`", 
									"#", 
									"(", 
									")", 
									",", 
									"?",
									"!",
									"|",
									"-",
									"*",
									":",
									"~",
									"`",
									"@",
									"^",
									"()",
									"%",
									"$");
		$newValue = str_replace($invalid_characters, "", $theValue);
		return $newValue;
	}
	
	function remove_ascii_chars($var)
	{
		 $chars = array(
						" " => "",
						"–" => "-",
						"À" => "A",
						"Â" => "A",
						"Ä" => "A",
						"Æ" => "AE",
						"È" => "E",
						"Ê" => "E",
						"Ì" => "I",
						"Î" => "I",
						"Ð" => "D",
						"Ò" => "O",
						"Ô" => "O",
						"Ö" => "O",
						"Ø" => "O",
						"Ú" => "U",
						"Ü" => "U",
						"à" => "a",
						"â" => "a",
						"ä" => "a",
						"æ" => "ae",
						"è" => "e",
						"ê" => "e",
						"ì" => "i",
						"î" => "i",
						"ð" => "o",
						"ò" => "o",
						"ô" => "o",
						"ö" => "o",
						"ø" => "o",
						"ú" => "u",
						"ü" => "u",
						"Á" => "A",
						"Ã" => "A",
						"Å" => "A",
						"Ç" => "C",
						"É" => "E",
						"Ë" => "E",
						"Í" => "I",
						"Ï" => "I",
						"Ñ" => "N",
						"Ó" => "O",
						"Õ" => "O",
						"Ù" => "U",
						"Û" => "U",
						"Ý" => "Y",
						"ß" => "B",
						"á" => "a",
						"ã" => "a",
						"å" => "a",
						"ç" => "c",
						"é" => "e",
						"ë" => "e",
						"í" => "i",
						"ï" => "i",
						"ñ" => "n",
						"ó" => "o",
						"õ" => "o",
						"ù" => "u",
						"û" => "u",
						"ý" => "y",
						"ÿ" => "y",
						"'" => "",
						"'" => "",
						"'" => "",
						"´" => "",
						"»" => "",
						"«" => "",
						"£" => "GBP",
						"€" => "EUR",
						'"' => "",
						'"' => "",
						"œ" => "e",
						"<BR>" => "\r\n",
						"…" => "...");
		$string =  str_replace(array_keys($chars),$chars,$var);
		return $string;
	}
	
	/*
	Function Name: Capitalize
	Description: Capitalize all inputs, if the string is all capital letters, it will transform it to lower case then capitalize the string
	*/
	function capitalize_string($theValue)
	{
		$newValue = ucwords(strtolower($theValue));
		return $newValue;
	}
	
	/*
	Function name: todate
	Description: simply converts interger values (usually from the time() function to date format)
	Parameters: 
	$theValue - the integer value to be formated
	$theFormat - format of the date
	*/
	function todate($theValue, $theFormat)	
	{
		$newValue = date($theFormat, $theValue);
		return $newValue;
	}
	
	/*
	Function Name: moneyFormat
	Description: formats a string into money format
	*/
	function moneyFormat($str)
	 { 
	 $dot = 0;
	 $ctr2 = 0;
	 $ctr3 = 0;
	 $start = 0;
	 $f_str = "";
	 
	 //check first if there is a cent value
	 for ( $ctr = 0; $ctr < strlen($str); $ctr++ )
	 { 
	   if ( $str[$ctr] == '.')
		{ 
		   $dot = 1;
		}
	 }
	   
	 if ( $dot == 1 )
	    { for ( $ctr = (strlen($str) - 1 ); $ctr >= 0; $ctr-- )
		   { if ( $str{$ctr} >= 0 && $start == 0 && $str{$ctr} != '.' )
		      { $result[$ctr2] = $str{$ctr};
			    $ctr2++;
			  }
			 
			 if ( $str{$ctr} == "." )
		      { $result[$ctr2] = $str{$ctr};
			    $ctr2++;
				$start = 1;
			  }
			 
			 if ( $str{$ctr} >= 0 && $start == 1 && $str{$ctr} != '.' )
		      { $result[$ctr2] = $str{$ctr};
			    $ctr2++;
				$ctr3++;
			  }
			 
			 if ( $ctr3 % 3 == 0 && $ctr3 != 0 && $ctr != 0 )
		      { $result[$ctr2] = ',';
			    $ctr2++;
			  }
		   }
		  for ( $ctr = (sizeof($result) - 1); $ctr >= 0; $ctr-- )
		   { $f_str .= $result[$ctr];
		   }
		}
	   
	   return ($f_str);
	 }
	 
	 /*
	 Function Name: noSpace
	 Description: Removes spaces on a string
	 */
	 
	 
	function noSpace($str)
	 {if ( $str != "" )
	  {
	   $n_str = trim($str);
	   $f_str = "";
	   $ctr2 = 0;
	   for ( $ctr = 0; $ctr < strlen($n_str); $ctr++ )
	    { 
			$new[$ctr] = $n_str{$ctr}; 
		}

	   for ( $ctr = 0; $ctr < sizeof(@$new); $ctr++ )
	    { 
		//beginning of the string
		if ( $ctr == 0 )
		 { 
		 	$result[$ctr2] = $new[$ctr];
		    $ctr2++;
		 }
		  
		//check if the character is a letter or a number
		if ( $new[$ctr] != " " && $ctr > 0 )
		 { 
		 	$result[$ctr2] = $new[$ctr];
		    $ctr2++; 
		   }
		}
	   
	   for( $ctr = 0; $ctr < sizeof(@$result); $ctr++ )
	    { 
			$f_str .= $result[$ctr];
		}
	   return trim($f_str); 
	 } }
	 
	 /*
	 Function Name: removeSpace
	 Description: Literally removes spaces on a string
	 */
	 
	function removeSpace($str)
	 {if ( $str != "" )
	  {
	   $n_str = trim($str);
	   $f_str = "";
	   $ctr2 = 0;
	   for ( $ctr = 0; $ctr < strlen($n_str); $ctr++ )
	    { 
			$new[$ctr] = $n_str{$ctr}; 
		}

	   for ( $ctr = 0; $ctr < sizeof(@$new); $ctr++ )
	    { //beginning of the string
		  if ( $ctr == 0 )
		   { 
		   	 $result[$ctr2] = $new[$ctr];
		     $ctr2++;
		   }
		   
		  //check if there are two or more consecutive spaces 
		  if ( $new[$ctr] == " " && $new[$ctr-1] != " " && $ctr > 0 )
		   { 
		   	 $result[$ctr2] = $new[$ctr];
		     $ctr2++;
		   }
		  
		  //check if the character is a letter or a number
		  if ( $new[$ctr] != " " && $ctr > 0 )
		   { 
		     $result[$ctr2] = $new[$ctr];
		     $ctr2++; 
		   }
		}
	   
	   for( $ctr = 0; $ctr < sizeof(@$result); $ctr++ )
	    { 
		 	$f_str .= $result[$ctr];
		}
	   return trim($f_str); 
	 } }

	function subval_sort($a,$subkey) {
		foreach($a as $k=>$v) {
			$b[$k] = strtolower($v[$subkey]);
		}
		asort($b);
		foreach($b as $key=>$val) {
			$c[] = $a[$key];
		}
		return $c;
	}

}

// End of File stringv.php