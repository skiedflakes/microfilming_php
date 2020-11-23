<?php
include 'config.php';

$company_code =$_REQUEST['company_code'];
$company_id = $_REQUEST['company_id'];
$branch_id = $_REQUEST['branch_id'];
$id = $_REQUEST['id'];
set_database($company_code);
			$getImg = mysql_fetch_array(mysql_query("SELECT * FROM tbl_micro_filming_log WHERE company_id ='$company_id' AND id='$id'"));
			//$slugImg = str_replace("ajax/", "", $getImg['slug']); //OLD
			$raw_url = $getImg["slug"];
        	$cleaned_url = substr($raw_url, 2);
			$slugImg =  "../notes".$cleaned_url;
			$result = mysql_query("DELETE FROM tbl_micro_filming_log WHERE company_id ='$company_id' AND id='$id'");
			@unlink($slugImg);
	if($result){
		echo 1;
	}else{
		echo 0;
		// echo $val;
	}


	
