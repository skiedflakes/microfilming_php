<?php
include '../config.php';

$company_code =$_REQUEST['company_code'];
$company_id = $_REQUEST['company_id'];
$branch_id = $_REQUEST['branch_id'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
set_database($company_code);


$pc_data = mysql_query("SELECT * FROM `tbl_revolving_header` WHERE company_id = '$company_id' and branch_id = '$branch_id' and (date_requested >= '$start_date' and date_requested <= '$end_date') ORDER BY status DESC, revolving_header_id DESC");

// $sales_data =mysql_query("SELECT * FROM `tbl_dr_header` where dr_date ='2020-09-17' order by dr_date DESC");
	$response_array['array_data'] = array();
	while ($data = mysql_fetch_array($pc_data)) {
		$revolving_header_id = $data["revolving_header_id"];
		$pc_details_data = mysql_query("SELECT * from tbl_revolving_detail where revolving_header_id = '$revolving_header_id'");
		$count_details = mysql_num_rows($pc_details_data);
		if($count_details>0){
			$details_status = '1';
		}else{
			$details_status = '0';
		}

        $response["header_id"] = $revolving_header_id;
		$response["ref_num"] = $data["revolving_num"];
		$response["details_status"] = $details_status;
		array_push($response_array['array_data'], $response);
	}
echo json_encode($response_array);

?>