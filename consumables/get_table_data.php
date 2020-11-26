<?php
include '../config.php';

$company_code =$_REQUEST['company_code'];
$company_id = $_REQUEST['company_id'];
$branch_id = $_REQUEST['branch_id'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
set_database($company_code);

$fetch_table_data = mysql_query("SELECT * from tbl_consumables_header where company_id = '$company_id' and branch_id = '$branch_id' AND (date_added >= '$start_date' and date_added <= '$end_date') order by date_added DESC");

// $sales_data =mysql_query("SELECT * FROM `tbl_dr_header` where dr_date ='2020-09-17' order by dr_date DESC");
	$response_array['array_data'] = array();
	while ($data = mysql_fetch_array($fetch_table_data)) {
        $response["consumables_header_id"] = $data["consumables_header_id"];
		$response["consumable_number"] = $data["consumable_number"];
		array_push($response_array['array_data'], $response);
	}
echo json_encode($response_array);


?>