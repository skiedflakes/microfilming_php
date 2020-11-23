<?php
include '../config.php';

$company_code =$_REQUEST['company_code'];
$company_id = $_REQUEST['company_id'];
$branch_id = $_REQUEST['branch_id'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
set_database($company_code);


$sales_data = mysql_query("SELECT * from tbl_dr_header  where company_id = '$company_id' and branch_id = '$branch_id' AND (dr_date >= '$start_date' and dr_date <= '$end_date') order by dr_date DESC");

// $sales_data =mysql_query("SELECT * FROM `tbl_dr_header` where dr_date ='2020-09-17' order by dr_date DESC");
	$response_array['array_data'] = array();
	while ($data = mysql_fetch_array($sales_data)) {
        $response["dr_header_id"] = $data["dr_header_id"];
        $response["invoice_no"] = $data["invoice_no"];
		$response["delivery_number"] = $data["delivery_number"];
        $response["tr_status"] = $data["tr_status"]; 
        $response["te_id"] = $data["te_id"];
		array_push($response_array['array_data'], $response);
	}
echo json_encode($response_array);


?>