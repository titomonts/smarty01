<?php

require_once('search.php');


$search = new search();

?>


<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>

<form name="form1">
                  <div align="center"> 
                    <p> 
                      <input name="textfield" type="text">
					  <?php 
					  $keyword = "";

					  $field = "All";
					  if(isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != "")
					  {
							$keyword =  substr($_SERVER['QUERY_STRING'],10); 
					  }
					  ?> 
                      <input type="submit"  value="Search" onClick="<?php $returnValue = $search->searchWhat($field,$keyword)?>
					  ">
                    </p>
                  </div>
  </form>
  <?php
  echo '<center>Search results for <u>'.$keyword.'</u></center><br>';

  if($returnValue == "<b>News</b><hr>")
  {
  	echo "<b>News</b><hr>No matches found.";
  }
  else
  {
  	echo $returnValue;
  }
  
  ?>
  
</body>
</html>