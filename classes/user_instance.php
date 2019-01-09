<?php 
class userObj extends mysqlv{
	var $dbLink = NULL;
		
	public function dbCon($host,$user,$pass,$db){
		$this->dbLink = parent::connect($host, $user, $pass, $db);
	}
	
	public function createUserInstance($arr){
		$username = $arr['username'];
		$password = $arr['password'];
		$email = $arr['email'];
		
		/* create new user instance */
		$newUser = "INSERT INTO account (username,password,user_email) VALUES($username,$password,$email)";
		$qry = parent::query($newUser, $this->dbLink) or die(mysql_error());
		return true;
	}
	
	public function loginUser($arr){
		$username = $arr['username'];
		$password = md5($arr['password']);
		
		$login = "SELECT username, password, idaccount FROM account WHERE username = ".$username." AND password = '".$password."'";
		$qry = parent::query($login, $this->dbLink) or die(mysql_error().'dito');
		$fetch = parent::fetch($qry);
		
		if(parent::total_rows($qry) > 0)
			return true;
		else
			return false;
	}
	
	public function getUserDetails($arr){
		$userName = $arr['username'];
		$details = "SELECT * FROM pgc_account WHERE username = $userName";
		$qry = parent::query($details) or die(mysql_error().'Here');
		$userDetails = parent::fetch($qry);
		
		return $userDetails;
	}
	
	public function insertNew($arr)
	{
		$dup = parent::query("SELECT COUNT(0) as cnt FROM pgc_members WHERE mem_username = ".$arr['username']."");
		$fdup = parent::fetch($dup);
		extract($fdup);
		if($cnt <= 0):
			$sql = parent::query("INSERT INTO pgc_members(mem_sponsor_id,
													  mem_lastname,
													  mem_firstname,
													  mem_gender,
													  mem_username,
													  mem_password,
													  mem_telephone,
													  mem_mobile_phone,
													  mem_city,
													  mem_country,
													  mem_zip_code,
													  mem_email,
													  mem_date_joined,
													  mem_account_activated,
													  mem_gifted)
								  VALUES(".$arr['sponsorid'].",
										 ".strtolower(strtoupper($arr['lastname'])).",
										 ".strtolower(strtoupper($arr['firstname'])).",
										 ".$arr['gender'].",
										 ".$arr['username'].",
										 '".md5(strtolower(strtoupper($arr['password'])))."',
										 ".$arr['tphone'].",
										 ".$arr['mobile'].",
										 ".strtolower(strtoupper($arr['city'])).",
										 ".strtolower(strtoupper($arr['country'])).",
										 ".$arr['zip'].",
										 ".$arr['email'].",
										 ".$arr['dateRegistered'].",
										 'no',
										 'no')") or die(mysql_error()."dito");
			return true;
		else:
			return false;								 
		endif;							 
	}	
	
	public function getMembers($offset, $upperlimit, $total_pages, $limit, $extraSQL){
		$members = array();
		$m = 0;
		
		if ($total_pages > $limit):
			$str = "SELECT * FROM pgc_members ".$extraSQL." LIMIT $offset, $upperlimit";
		else:
			$str = "SELECT * FROM pgc_members ".$extraSQL."";
		endif;	
		$qry = parent::query($str) or die(mysql_error().'Pungid');
		
		while( $mems = parent::fetch($qry) ):
			$members[$m]['mem_id'] = $mems['mem_id'];
			$members[$m]['mem_sponsor_id'] = $mems['mem_sponsor_id'];
			$members[$m]['mem_account_id'] = $mems['mem_account_id'];
			$members[$m]['mem_username'] = $mems['mem_username'];
			$members[$m]['mem_password'] = $mems['mem_password'];
			$members[$m]['mem_lastname'] = $mems['mem_lastname'];
			$members[$m]['mem_firstname'] = $mems['mem_firstname'];
			$members[$m]['mem_telephone'] = $mems['mem_telephone'];
			$members[$m]['mem_mobile_phone'] = $mems['mem_mobile_phone'];
			$members[$m]['mem_city'] = $mems['mem_city'];
			$members[$m]['mem_country'] = $mems['mem_country'];
			$members[$m]['mem_zip_code'] = $mems['mem_zip_code'];
			$members[$m]['mem_email'] = $mems['mem_email'];
			$members[$m]['mem_date_joined'] = $mems['mem_date_joined'];
			$members[$m]['mem_gifted'] = $mems['mem_gifted'];
			$m++;
		endwhile;
			
		return $members;
	}
	
	public function getMemberDetails($memid){
		$str = "SELECT * FROM pgc_members WHERE mem_id = $memid LIMIT 1";
		$qry = parent::query($str) or die(mysql_error().'Pungid');
		$member = parent::fetch($qry);
			
		return $member;
	}
	
	public function updateMember($arr)
	{										 
		$upd = parent::query("UPDATE pgc_members SET mem_sponsor_id = '".$arr['sponsorid']."', mem_lastname = '".$arr['lastname']."', mem_firstname = '".$arr['firstname']."', mem_gender = '".$arr['gender']."', mem_username = '".$arr['username']."', mem_password = '".md5(strtolower(strtoupper($arr['password'])))."', mem_email = '".$arr['email']."',
												 mem_telephone = '".$arr['tphone']."', mem_mobile_phone = '".$arr['mobile']."', mem_city = '".$arr['city']."', 
												 mem_country = '".$arr['country']."', mem_zip_code = '".$arr['zip']."' 
												 WHERE mem_id = ".$arr['hidMemId']."") or die(mysql_error());	
		
		$bobo = $mysqlv->query("UPDATE pgc_members SET mem_sponsor_id = '".$arr['username']."' WHERE mem_sponsor_id = '".$arr['hidOrigU']."'") or die(mysql_error());
					
		$pultit = $mysqlv->query("UPDATE pgc_sponsors SET sp_inviter = '".$arr['username']."' WHERE sp_inviter = '".$arr['hidOrigU']."'") or die(mysql_error());	
		
		$ukis = $mysqlv->query("UPDATE pgc_sponsors SET sp_invitee = '".$arr['username']."' WHERE sp_invitee = '".$arr['hidOrigU']."'") or die(mysql_error());
												 	
		$b= parent::query("SELECT *, MATCH(brd_pos_1,brd_pos_2,brd_pos_3,brd_pos_4,brd_pos_5,brd_pos_6,brd_pos_7,brd_pos_8,brd_pos_9,brd_pos_10,brd_pos_11,brd_pos_12,brd_pos_13,brd_pos_14,brd_pos_15) AGAINST('".$arr['hidOrigU']."') AS le_match, brd_level FROM pgc_boards") or die(mysql_error());										 
		while($row=parent::fetch($b)):	
			if($row['le_match'] > 0):
				$bn = parent::query("SELECT brd_pos_1,brd_pos_2,brd_pos_3,brd_pos_4,brd_pos_5,brd_pos_6,brd_pos_7,brd_pos_8,brd_pos_9,brd_pos_10,brd_pos_11,brd_pos_12,brd_pos_13,brd_pos_14,brd_pos_15 FROM pgc_boards WHERE brd_name = '".$row['brd_name']."'") or die(mysql_error());
				$fn = parent::fetch($bn);
				
				if($fn['brd_pos_1'] == $arr['hidOrigU']):
					$u1 = parent::query("UPDATE pgc_boards SET brd_pos_1 = '".$arr['username']."', brd_iscomplete = 'no' WHERE brd_name = '".$row['brd_name']."'") or die(mysql_error());
				endif;
				
				if($fn['brd_pos_2'] == $arr['hidOrigU']):
					$u2 = parent::query("UPDATE pgc_boards SET brd_pos_2 = '".$arr['username']."', brd_iscomplete = 'no' WHERE brd_name = '".$row['brd_name']."'") or die(mysql_error());
				endif;
				
				if($fn['brd_pos_3'] == $arr['hidOrigU']):
					$u3 = parent::query("UPDATE pgc_boards SET brd_pos_3 = '".$arr['username']."', brd_iscomplete = 'no' WHERE brd_name = '".$row['brd_name']."'") or die(mysql_error());
				endif;
				
				if($fn['brd_pos_4'] == $arr['hidOrigU']):
					$u4 = parent::query("UPDATE pgc_boards SET brd_pos_4 = '".$arr['username']."', brd_iscomplete = 'no' WHERE brd_name = '".$row['brd_name']."'") or die(mysql_error());
				endif;
				
				if($fn['brd_pos_5'] == $arr['hidOrigU']):
					$u5 = parent::query("UPDATE pgc_boards SET brd_pos_5 = '".$arr['username']."', brd_iscomplete = 'no' WHERE brd_name = '".$row['brd_name']."'") or die(mysql_error());
				endif;
				
				if($fn['brd_pos_6'] == $arr['hidOrigU']):
					$u6 = parent::query("UPDATE pgc_boards SET brd_pos_6 = '".$arr['username']."', brd_iscomplete = 'no' WHERE brd_name = '".$row['brd_name']."'") or die(mysql_error());
				endif;
				
				if($fn['brd_pos_7'] == $arr['hidOrigU']):
					$u7 = parent::query("UPDATE pgc_boards SET brd_pos_7 = '".$arr['username']."', brd_iscomplete = 'no' WHERE brd_name = '".$row['brd_name']."'") or die(mysql_error());
				endif;
				
				if($fn['brd_pos_8'] == $arr['hidOrigU']):
					$u1 = parent::query("UPDATE pgc_boards SET brd_pos_8 = '".$arr['username']."', brd_iscomplete = 'no', brd_left_iscomplete = 'no' WHERE brd_name = '".$row['brd_name']."'") or die(mysql_error());
				endif;
				
				if($fn['brd_pos_9'] == $arr['hidOrigU']):
					$u9 = parent::query("UPDATE pgc_boards SET brd_pos_9 = '".$arr['username']."', brd_iscomplete = 'no', brd_left_iscomplete = 'no' WHERE brd_name = '".$row['brd_name']."'") or die(mysql_error());
				endif;
				
				if($fn['brd_pos_10'] == $arr['hidOrigU']):
					$u10 = parent::query("UPDATE pgc_boards SET brd_pos_10 = '".$arr['username']."', brd_iscomplete = 'no', brd_left_iscomplete = 'no' WHERE brd_name = '".$row['brd_name']."'") or die(mysql_error());
				endif;
				
				if($fn['brd_pos_11'] == $arr['hidOrigU']):
					$u11 = parent::query("UPDATE pgc_boards SET brd_pos_11 = '".$arr['username']."', brd_iscomplete = 'no', brd_left_iscomplete = 'no' WHERE brd_name = '".$row['brd_name']."'") or die(mysql_error());
				endif;
				
				if($fn['brd_pos_12'] == $arr['hidOrigU']):
					$u12 = parent::query("UPDATE pgc_boards SET brd_pos_12 = '".$arr['username']."', brd_iscomplete = 'no', brd_right_iscomplete = 'no' WHERE brd_name = '".$row['brd_name']."'") or die(mysql_error());
				endif;
				
				if($fn['brd_pos_13'] == $arr['hidOrigU']):
					$u13 = parent::query("UPDATE pgc_boards SET brd_pos_13 = '".$arr['username']."', brd_iscomplete = 'no', brd_right_iscomplete = 'no' WHERE brd_name = '".$row['brd_name']."'") or die(mysql_error());
				endif;
				
				if($fn['brd_pos_14'] == $arr['hidOrigU']):
					$u14 = parent::query("UPDATE pgc_boards SET brd_pos_14 = '".$arr['username']."', brd_iscomplete = 'no', brd_right_iscomplete = 'no' WHERE brd_name = '".$row['brd_name']."'") or die(mysql_error());
				endif;
				
				if($fn['brd_pos_15'] == $arr['hidOrigU']):
					$u15 = parent::query("UPDATE pgc_boards SET brd_pos_15 = '".$arr['username']."', brd_iscomplete = 'no', brd_right_iscomplete = 'no' WHERE brd_name = '".$row['brd_name']."'") or die(mysql_error());
				endif;
				
			endif;
		endwhile;									 
												 
	}
	
	
	public function addNewAdminUser($arr)
	{
		putenv("TZ=Asia/Taipei");
		$sql = parent::query("INSERT INTO pgc_account(fname,
												  lname,
												  username,
												  password,
												  register,
												  staff,
												  banned,
												  atype)
							 VALUES(".strtolower(strtoupper($arr['firstname'])).", 
							 		".strtolower(strtoupper($arr['lastname'])).", 
									".strtolower(strtoupper($arr['username'])).", 
									'".md5(strtolower(strtoupper($arr['password'])))."', 
									".$arr['register'].", 
									".$arr['staff'].", 
									".$arr['banStatus'].",
									".$arr['actype'].")") or die(mysql_error()."Gorot ka");
		return true;							
	}
	
	public function getAdminUsers(){
		$members = array();
		$m = 0;
		$str = "SELECT * FROM pgc_account";
		$qry = parent::query($str) or die(mysql_error().'Mas Pungid');
		
		while( $mems = parent::fetch($qry) ):
			$members[$m]['idaccount'] = $mems['idaccount'];
			$members[$m]['fname'] = $mems['fname'];
			$members[$m]['lname'] = $mems['lname'];
			$members[$m]['username'] = $mems['username'];
			$members[$m]['register'] = $mems['register'];
			$members[$m]['updated'] = $mems['updated'];
			$members[$m]['staff'] = $mems['staff'];
			$members[$m]['banned'] = $mems['banned'];
			$members[$m]['atype'] = $mems['atype'];
			$m++;
		endwhile;
			
		return $members;
	}	
	
	public function getAdminUserDetails($uid)
	{
		$sql = parent::query("SELECT * FROM pgc_account WHERE idaccount = $uid LIMIT 1") or die(mysql_error()."Folayang");
		$adminuser = parent::fetch($sql);
		return $adminuser;
	}
	
	public function updateAdminUser($arr)
	{
		putenv("TZ=Asia/Taipei");
		$sql = parent::query("UPDATE pgc_account SET fname = ".$arr['firstname'].", lname = ".$arr['lastname'].", username = ".$arr['username'].", password = '".$arr['cpassword']."', updated = ".$arr['last_update'].", staff = ".$arr['staff'].", banned = ".$arr['banStatus'].", atype = ".$arr['actype']." WHERE idaccount = ".$arr['hidAdminUid']."")or die(mysql_error()."Nefut ka");
	}
	
	public function getUserLogs()
	{
		$logs = array();
		$l = 0;
	}
	
	public function delUser($arr)
	{
		$del = parent::query("DELETE FROM pgc_members WHERE mem_id = ".$arr['idmembers']."") or die(mysql_error()."Nef");
	}
	
	public function delAdminUser($arr)
	{
		$del = parent::query("DELETE FROM pgc_account WHERE idaccount = ".$arr['idaccount']."") or die(mysql_error()."Mas Nef");
	}
		
}

/* End of file user_instance.php */