<?php

// author: gumb@ nam3L3ss 2031086
// desc: class for trimming consecutive spaces


class stripv {
	
	function moneyFormat($str)
	 { $dot = 0;
	   $ctr2 = 0;
	   $ctr3 = 0;
	   $start = 0;
	   $f_str = "";
	   //check first if there is a cent value
	   for ( $ctr = 0; $ctr < strlen($str); $ctr++ )
	    { if ( $str[$ctr] == '.')
		   { $dot = 1;
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
	 
	function noSpace($str)
	 {if ( $str != "" )
	  {
	   $n_str = trim($str);
	   $f_str = "";
	   $ctr2 = 0;
	   for ( $ctr = 0; $ctr < strlen($n_str); $ctr++ )
	    { $new[$ctr] = $n_str{$ctr}; 
		}

	   for ( $ctr = 0; $ctr < sizeof(@$new); $ctr++ )
	    { //beginning of the string
		  if ( $ctr == 0 )
		   { $result[$ctr2] = $new[$ctr];
		     $ctr2++;
		   }
		  
		  //check if the character is a letter or a number
		  if ( $new[$ctr] != " " && $ctr > 0 )
		   { $result[$ctr2] = $new[$ctr];
		     $ctr2++; 
		   }
		}
	   
	   for( $ctr = 0; $ctr < sizeof(@$result); $ctr++ )
	    { $f_str .= $result[$ctr];
		}
	   return trim($f_str); 
	 } }
	 
	function removeSpace($str)
	 {if ( $str != "" )
	  {
	   $n_str = trim($str);
	   $f_str = "";
	   $ctr2 = 0;
	   for ( $ctr = 0; $ctr < strlen($n_str); $ctr++ )
	    { $new[$ctr] = $n_str{$ctr}; 
		}

	   for ( $ctr = 0; $ctr < sizeof(@$new); $ctr++ )
	    { //beginning of the string
		  if ( $ctr == 0 )
		   { $result[$ctr2] = $new[$ctr];
		     $ctr2++;
		   }
		   
		  //check if there are two or more consecutive spaces 
		  if ( $new[$ctr] == " " && $new[$ctr-1] != " " && $ctr > 0 )
		   { $result[$ctr2] = $new[$ctr];
		     $ctr2++;
		   }
		  
		  //check if the character is a letter or a number
		  if ( $new[$ctr] != " " && $ctr > 0 )
		   { $result[$ctr2] = $new[$ctr];
		     $ctr2++; 
		   }
		}
	   
	   for( $ctr = 0; $ctr < sizeof(@$result); $ctr++ )
	    { $f_str .= $result[$ctr];
		}
	   return trim($f_str); 
	 } }
 
}

// End of file stripv.php
