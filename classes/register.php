<?php


class register extends mysqlv
{
	var $dbLink = NULL;
	
	/* Make a database connection instance */
	public function dbConnect($host,$user,$pass,$db)
	{
		$this->dbLink = parent::connect($host, $user, $pass, $db);
	}
	
	/*
	Function name: login
	Parameters: 
		$password - password to log in to system
	Desc: sets session variable to log in user
	*/
	public function verify($email) 
	{
		$chkDup = parent::query("SELECT COUNT(0) as cnt FROM tblregistration WHERE reg_email = '".$email."'") or die(mysql_error());
		$count = parent::fetch($chkDup);
		if($count['cnt'] >= 1)
			return false;
		else
			return true;
	
	}
	
}

// End of file