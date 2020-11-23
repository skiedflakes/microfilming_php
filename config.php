<?php 

function set_database($database_new){

	
ini_set('date.timezone','UTC');
//error_reporting(E_ALL);
date_default_timezone_set('UTC');
$today = date('H:i:s');
$date = date('Y-m-d H:i:s', strtotime($today)+28800);

$host 	  = "localhost";
$username = "root";
$password = "";
$database = "wfh_".$database_new;

@mysql_connect($host, $username, $password) or die("Cannot connect to MySQL Server");
@mysql_select_db($database) or die ("Cannot connect to Database");
mysql_query("SET SESSION sql_mode=''");


}


function getCurrentDate(){
	ini_set('date.timezone','UTC');
	date_default_timezone_set('UTC');
	$today = date('H:i:s');
	$date = date('Y-m-d H:i:s', strtotime($today)+28800);
	
	return $date;
}