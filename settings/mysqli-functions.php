<?php
$data['host'] = 'localhost';
$data['username'] = 'root';
$data['password'] = 'hcnom2031055';
$data['db'] = '';

$mysqli = new mysqli($data['host'],$data['username'],$data['password'],$data['db']);
  
if ($mysqli->connect_errno):
  echo "Failed to connect to MySQL: ".$mysqli->connect_error;
  exit();
endif;
