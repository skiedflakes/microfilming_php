<?php
include '../config.php';

$company_code =$_REQUEST['company_code'];
$company_id = $_REQUEST['company_id'];
$branch_id = $_REQUEST['branch_id'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
set_database($company_code);

$pc_data = mysql_query("SELECT * FROM `tbl_pettycash_header` WHERE company_id = '$company_id' and branch_id = '$branch_id' and (date_requested >= '$start_date' and date_requested <= '$end_date') ORDER BY status DESC, pettycash_header_id DESC");
	$response_array['array_data'] = array();
	while ($data = mysql_fetch_array($pc_data)) {
		$pettycash_header_id = $data["pettycash_header_id"];
		$pc_details_data = mysql_query("SELECT * from tbl_pettycash_detail where pettycash_header_id = '$pettycash_header_id'");
		$count_details = mysql_num_rows($pc_details_data);
		if($count_details>0){
			$details_status = '1';
		}else{
			$details_status = '0';
		}

        $response["header_id"] = $pettycash_header_id;
		$response["ref_num"] = $data["pettycash_num"];
		$response["details_status"] = $details_status;
		array_push($response_array['array_data'], $response);
	}
echo json_encode($response_array);
?>