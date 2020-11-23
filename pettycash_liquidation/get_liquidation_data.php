<?php
include '../config.php';

$company_code =$_REQUEST['company_code'];
$company_id = $_REQUEST['company_id'];
$branch_id = $_REQUEST['branch_id'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
set_database($company_code);


$pc_data = mysql_query("SELECT * FROM `tbl_pettycash_liquidation` WHERE company_id = '$company_id' and (date_liquidated >= '$start_date' and date_liquidated <= '$end_date') ORDER BY status DESC, pcv_liquidation_id DESC");

	$response_array['array_data'] = array();
	while ($data = mysql_fetch_array($pc_data)) {
        $response["pcv_liquidation_id"] = $data["pcv_liquidation_id"];
		$response["tracking_num"] = $data["tracking_num"];
		array_push($response_array['array_data'], $response);
	}
echo json_encode($response_array);

?>