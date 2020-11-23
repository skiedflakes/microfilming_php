<?php
include '../config.php';
$company_code =$_REQUEST['company_code'];
$company_id =$_REQUEST['company_id'];
$branch_id = $_REQUEST['branch_id'];
$reference_number = $_REQUEST['reference_number'];
$module = $_REQUEST['module'];
$primary_url =$_REQUEST['primary_url'];
set_database($company_code);

$sales_data =mysql_query("SELECT * FROM `tbl_micro_filming_log` where mf_reference ='$reference_number' and module='$module' and company_id ='$company_id'");
	$response_array['array_data'] = array();
	while ($data = mysql_fetch_array($sales_data)) {
        $raw_url = $data["slug"];
        $cleaned_url = substr($raw_url, 2);
        $response["slug"] = $primary_url.$cleaned_url;
        $response["id"] =$data["id"];
		array_push($response_array['array_data'], $response);
	}
echo json_encode($response_array);

?>