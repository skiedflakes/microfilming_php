<?php
include '../config.php';

$company_code =$_REQUEST['company_code'];
$company_id = $_REQUEST['company_id'];
$branch_id = $_REQUEST['branch_id'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
set_database($company_code);


$result = mysql_query("SELECT * from tbl_rr_header where company_id = '$company_id' and branch_id = '$branch_id' and (date_added >= '$start_date' and date_added <= '$end_date') ORDER BY date_added DESC") or die(mysql_error());

// $sales_data =mysql_query("SELECT * FROM `tbl_dr_header` where dr_date ='2020-09-17' order by dr_date DESC");
	$response_array['array_data'] = array();
	while ($data = mysql_fetch_array($result)) {
        $response["rr_header_id"] = $data["rr_header_id"];
        $response["delivery_number"] =  $data["receiving_number"];
		$response["invoice_no"] = $data["po_number"];
		array_push($response_array['array_data'], $response);
	}
echo json_encode($response_array);


?>