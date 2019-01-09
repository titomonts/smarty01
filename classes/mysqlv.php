<?php
/*
	==================================================================================	
	Class name: mysqlv
	Description: Handles the connection to the database server and other SQL functions
	Monch 2006-2007
	==================================================================================
*/
class mysqlv {
	//reference variable to the database
	var $databaseLink = NULL;
	
	/*
	Function name: mysqlv
	Parameters: 
		$hostname - name of host
		$username - username to connect to server
		$password - password to connect to server
		$database - name of database
	Description: Creates a connection to the database
	*/
	public function connect ($hostname, $username, $password, $database) {	
		$this->databaseLink = mysql_connect($hostname, $username, $password);
		mysql_select_db($database, $this->databaseLink);		
	}
	
	/*
	Function name: close
	Parameters: None
	Desc: Close the connection to server
	*/
	public function close() {
		mysql_close($this->databaseLink);
	}
	
	/*
	Function name: query
	Parameters: 
		$query - the query string
	Desc: performs a query
	*/
	public function query($query) {
		return mysql_query($query, $this->databaseLink);
	}
	
	/*
	Function name: total_rows
	Parameters: 
		$recordset - reference variable to a recordser
	Desc: Total rows queried
	*/ 
	public function total_rows($recordset) {
		return mysql_num_rows($recordset);
	}
	
	/*
	Function name: fetch
	Parameters: 
		$recordset - reference variable to a recordser
	Desc: Fetch individual row result
	*/ 
	public function fetch($recordset) {
		return mysql_fetch_assoc($recordset);
	}
	
	/*
	Function name: move
	Parameters: 
		$recordset - reference variable to a recordser
		$position - position to move the pointer
	Desc: Moves current cursor to desired position
	*/
	public function move($recordset, $position) {
		return mysql_data_seek($recordset, $position);
	}
	
	/*
	Function name: moveFirst
	Parameters: 
		$recordset - reference variable to a recordser		
	Desc: Moves current cursor to first record
	*/
	public function moveFirst($recordset) {
		return mysql_data_seek($recordset,0);
	}

	/*
	Function name: moveLast
	Parameters: 
		$recordset - reference variable to a recordser		
	Desc: Moves current cursor to last record
	*/
	public function moveLast($recordset) {
		return mysql_data_seek($recordset,(mysql_num_rows($recordset)-1));
	}

	/*
	Function name: beginTrans
	Parameters: None
	Desc: Begin transaction
	*/
	public function beginTrans() {
		return mysql_query("start transaction", $this->databaseLink);
	}

	/*
	Function name: commitTrans
	Parameters: None
	Desc: Commit transaction
	*/ 
	public function commitTrans() {
		return mysql_query("commit", $this->databaseLink);
	}

	/*
	Function name: errorno
	Parameters: None
	Desc: return Error Number
	*/
	public function errno() {
		return mysql_errno($this->databaseLink);
	}

	/*
	Function name: errorDesc
	Parameters: None
	Desc: return error description
	*/
	public function errorDesc() {
		return error($this->databaseLink);
	}

	/*
	Function name: free_result
	Parameters: None
	Desc: Deallocate recordset
	*/
	public function free_result($recordset) {
		mysql_free_result($recordset);
	}

	/*
		function: login
		parameters: SQL String
		returns: true or false
	*/


}

// End of File mysqlv.php