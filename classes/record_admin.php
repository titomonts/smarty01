<?php
class records extends mysqlv
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
	public function getLogs($offset, $upperlimit, $total_pages, $limit) 
	{
		$records = array();
		$r = 0;
		
		if ($total_pages > $limit):
			$sql = parent::query("SELECT * FROM logs LIMIT $offset, $upperlimit") or die(mysql_error());
		else:
			$sql = parent::query("SELECT * FROM logs") or die(mysql_error());
		endif;	
		
		while($recordSet = parent::fetch($sql)):
			$records[$r]['idlogs'] = $recordSet['idlogs'];
			$records[$r]['name'] = $recordSet['name'];
			$records[$r]['username'] = $recordSet['username'];
			$records[$r]['login'] = $recordSet['login'];
			$records[$r]['logout'] = $recordSet['logout'];
			$records[$r]['error'] = $recordSet['error'];
			$r++;
		endwhile;
		return $records;
	}
	
	public function getDetailedLogs($offset, $upperlimit, $total_pages, $limit)
	{
		$logs = array();
		$l = 0;
		
		if($total_pages > $limit):
			$sql = parent::query("SELECT * FROM tbl_logs LIMIT $offset, $upperlimit ORDER BY col_logintime DESC") or die(mysql_error());
		else:
			$sql = parent::query("SELECT * FROM tbl_logs") or die(mysql_error());
		endif;	
		
		while($recordSet = parent::fetch($sql)):
			$logs[$l]['lid'] = $recordSet['lid'];
			$logs[$l]['col_user'] = $recordSet['col_user'];
			$logs[$l]['col_logintime'] = $recordSet['col_logintime'];
			$logs[$l]['col_logouttime'] = $recordSet['col_logouttime'];
			$logs[$l]['col_ip'] = $recordSet['col_ip'];
			$logs[$l]['col_activity'] = $recordSet['col_activity'];
			$l++;
		endwhile;
		return $logs;
	}
	
	public function getSiteHits()
	{
		$hits = array();
		$h = 0;
		$sql = parent::query("SELECT hit_id, hit_remote_add, hit_num_visit, hit_visit_date FROM tbl_hits") or die(mysql_error());
		while($hitSet = parent::fetch($sql)):
			$hits[$h]['hit_id'] = $hitSet['hit_id'];
			$hits[$h]['hit_remote_add'] = $hitSet['hit_remote_add'];
			$hits[$h]['hit_num_visit'] = $hitSet['hit_num_visit'];
			$hits[$h]['hit_visit_date'] = $hitSet['hit_visit_date'];
			$h++;
		endwhile;
		return $hits;
	}
	
	public function getDonorList()
	{
		$donors = array();
		$d = 0;
		$sql = parent::query("SELECT * FROM dc_comments") or die(mysql_error());
		while($dons = parent::fetch($sql)):
			$donors[$d]['name'] = $dons['name'];
			$donors[$d]['address'] = $dons['address'];
			$donors[$d]['organization'] = $dons['organization'];
			$donors[$d]['contact_number'] = $dons['contact_number'];
			$donors[$d]['ip_address'] = $dons['ip_address'];
			$donors[$d]['transaction_date'] = $dons['transaction_date'];
			$d++;
		endwhile;
		return $donors;
	}
	
	
}

// End of file