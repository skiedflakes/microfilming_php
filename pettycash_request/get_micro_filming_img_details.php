<?php
include '../config.php';
$company_code =$_REQUEST['company_code'];
$company_id =$_REQUEST['company_id'];
$branch_id = $_REQUEST['branch_id'];
$reference_number = $_REQUEST['reference_number'];
$module = $_REQUEST['module'];
$primary_url =$_REQUEST['primary_url'];
$mf_reference_id =$_REQUEST['mf_reference_id'];
set_database($company_code);

$fetch_data = mysql_query("SELECT * FROM `tbl_micro_filming_log` where mf_reference ='$reference_number' and module='$module' and company_id ='$company_id' and gchart_id!='0' and mf_reference_id = '$mf_reference_id'");
	$response_array['array_data'] = array();
	while ($data = mysql_fetch_array($fetch_data)) {
        $raw_url = $data["slug"];
        $response["id"] =$data["id"];
        $cleaned_url = substr($raw_url, 2);
        $response["slug"] = $primary_url.$cleaned_url;
		array_push($response_array['array_data'], $response);
	}
echo json_encode($response_array);
?>