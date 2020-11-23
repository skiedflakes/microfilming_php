<?php
include '../config.php';
$company_code =$_REQUEST['company_code'];
$company_id = $_REQUEST['company_id'];
$header_id = $_REQUEST['header_id'];
set_database($company_code);

$pc_details_data = mysql_query("SELECT * from tbl_pettycash_detail where pettycash_header_id = '$header_id'");
	$response_array['array_data'] = array();
	while ($data = mysql_fetch_array($pc_details_data)) {
		$gchart_id =$data["gchart_id"];

		if(isinsideMain($gchart_id,$company_id)){
			$chart = getMainAccount($gchart_id,$company_id);
		}else{
			$chart = getSubAccount($gchart_id,$company_id);
		}
		$response["pettycash_detail_id"] =$data["pettycash_detail_id"];
		$response["amount"] = $data["amount"];
		$response["doc_num"] = $data["doc_num"];
		$response["gchart_id"] = $gchart_id;
		$response["chart"] = $chart;


		array_push($response_array['array_data'], $response);
	}
echo json_encode($response_array);

function isinsideMain($id,$company_id){
		//$branch_id = get_branch();
	$result = mysql_query("SELECT * from tbl_gchart_main where company_id = '$company_id' and gchart_main_id = $id") or die (mysql_error());
	$count_row = mysql_num_rows($result);
	if($count_row>0){
		return true;
	}else{
		return false;
	}
}

function getMainAccount($id,$company_id){
	//$branch_id = get_branch();
	$result = mysql_query("SELECT * from tbl_gchart_main where company_id = '$company_id' and gchart_main_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["chart"];
}

function getSubAccount($id,$company_id){
	//$branch_id = get_branch();
	$result = mysql_query("SELECT * from tbl_gchart_sub where company_id = '$company_id' and gchart_sub_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["s_chart"];
}
?>