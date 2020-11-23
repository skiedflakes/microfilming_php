<?php
include '../config.php';
$company_code =$_REQUEST['company_code'];
$company_id =$_REQUEST['company_id'];
$branch_id = $_REQUEST['branch_id'];
$reference_number = $_REQUEST['reference_number'];
$module = $_REQUEST['module'];
$primary_url =$_REQUEST['primary_url'];
set_database($company_code);


// $sales_data = "SELECT * from tbl_dr_header  where company_id = '$company_id' and branch_id = '$branch_id' AND (dr_date >= '$date' and dr_date <= '$date') order by dr_date DESC";

$response_array['array_data'] = array();
$delivery_data = mysql_query("SELECT * FROM `tbl_micro_filming_log` where mf_reference ='$reference_number' and module='$module' and branch_id ='$branch_id' and gchart_id='0'");

while ($data = mysql_fetch_array($delivery_data)) {
    $raw_url = $data["slug"];

    $cleaned_url = substr($raw_url, 2);
    $response["slug"] = $primary_url.$cleaned_url;
	$response["id"] =$data["id"];
	array_push($response_array['array_data'], $response);
}


$response_array['array_data_trucking'] = array();
$delivery_data = mysql_query("SELECT * FROM `tbl_micro_filming_log` where mf_reference ='$reference_number' and module='$module' and branch_id ='$branch_id' and gchart_id!='0'");

while ($data = mysql_fetch_array($delivery_data)) {
    $raw_url = $data["slug"];

    $cleaned_url = substr($raw_url, 2);
    $response["slug"] = $primary_url.$cleaned_url;
	$response["id"] =$data["id"];
	array_push($response_array['array_data_trucking'], $response);
}


echo json_encode($response_array);






?>