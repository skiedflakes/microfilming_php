<?php
include '../config.php';

$company_code =$_REQUEST['company_code'];
$company_id = $_REQUEST['company_id'];
$branch_id = $_REQUEST['branch_id'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
set_database($company_code);


$pc_data = mysql_query("SELECT * FROM `tbl_revolving_replenish` WHERE company_id = '$company_id' and branch_id = '$branch_id ' and (date >= '$start_date' and date <= '$end_date') ORDER BY status DESC, rfe_replenish_id DESC");

	$response_array['array_data'] = array();
	while ($data = mysql_fetch_array($pc_data)) {
        $response["rfe_replenish_id"] = $data["rfe_replenish_id"];
		$response["tracking_num"] = $data["rplnsh_num"];
		array_push($response_array['array_data'], $response);
	}
echo json_encode($response_array);	

?>