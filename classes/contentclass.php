<?php
class contentsHandler extends mysqlv {
	var $dbLink = NULL;
	
	/* Make a database connection instance */
	public function dbConnect($host,$user,$pass,$db)
	{
		$this->dbLink = parent::connect($host, $user, $pass, $db);
	}
	
	/*
		Function Name: getContents
		Returns all page contents
	*/
	public function getContents()
	{
		$contents = array();
		$c = 0;
		$sql = "SELECT content_id, content_page, content_contents FROM tblpagecontents";
		$qry = parent::query($sql) or die(mysql_error());
		while($fetch = parent::fetch($qry)):
			$contents[$c]['content_id'] = $fetch['content_id'];
			$contents[$c]['content_page'] = $fetch['content_page'];
			$contents[$c]['content_contents'] = $fetch['content_contents'];
			$c++;
		endwhile;
		return $contents;
	}
	
	/*
		Function Name: getPageContent
		Parameter: $params contains page name
	*/
	public function getPageContents($page)
	{
		$sql = "SELECT content_id, content_page, content_contents FROM tblpagecontents WHERE content_page = '$page'";
		$qry = parent::query($sql) or die(mysql_error());
		$contents = parent::fetch($qry);
		return $contents;
	}
	
}

/**/