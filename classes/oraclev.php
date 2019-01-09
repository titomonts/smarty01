<?php
/*
	==================================================================================	
	Class name: oraclev
	Description: Handles the connection to the database server and other SQL functions
	using Oracle
	Monch 2009
	==================================================================================
*/
class mysqlv {
	var $databaseLink = NULL;
	/*
		function name: connect
		parameters: $username (User Name attempting to connect), 
					$password (Password), 
					$database (Database to access)
	*/
	function connect($username,$password,$database){
		$this->databaseLink = oci_connect($username,$password,$database);
	}
	
	/*
		function name: close
		parameters: $connect (current database connection)
	*/
	function close($connect){
		oci_close($this->databaseLink);
	}
	
	/*
		function name: parse
		parameters: $statement (Query String to parse)
	*/
	function parse($statement){
		return oci_parse($this->databaseLink, $statement);
	}
	
	/*
		function name: execute
		parameters: $statement (Query string to execute)
	*/
	function execute($statement){
		return oci_execute($statement, OCI_DEFAULT);
	}
	
	/*
		function name: fetch
		parameters: $statement (Query string to fetch)
	*/
	function fetch($statement){
		return oci_fetch($statement);
	}
	
	/*
		function name: result
		parameters: $statement (Query String),
					$field (specific column to fetch)
	*/
	function result($statement, $field){
		return oci_result($statement, $field);
	}
	
	/*
		function name: commit
		parameters: none
	*/
	function commit(){
		oci_commit($this->databaseLink);
	}
}
// end of file oraclev.php