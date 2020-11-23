<?php
include '../config.php';
include '../my_functions.php';

$user_id = $_POST['user_id']; //612
$branch_id = $_POST['branch_id']; //124
$company_id = $_POST['company_id']; //88
$company_code = $_POST['company_code']; //882018
set_database($company_code);

$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];

$_SESSION['system']['company_id'] = $company_id;

$response['data'] = array();


	$PFR_declare_status = "SELECT * FROM tbl_pettycash_replenish WHERE company_id = '$company_id' and (date >= '$start_date' and date <= '$end_date') ORDER BY pcv_replenish_id DESC";


$result = mysql_query($PFR_declare_status) or die(mysql_error());
$count = 1;
 
while($row = mysql_fetch_array($result)){

	$amount = 0;
	$createdByID = mysql_fetch_array(mysql_query("SELECT name FROM tbl_users WHERE user_id = '$row[user_id]'"));
	$getSubRF = getBulkData("*","tbl_gchart_sub","gchart_sub_id = '$row[credit_method]' AND company_id = '$company_id'");
	$getMainRF = getBulkData("UPPER(chart) AS chrts","tbl_gchart_main","gchart_main_id = '$getSubRF[gchart_main_id]' AND company_id = '$company_id'");

	$getReplenishDetail = mysql_query("SELECT * FROM tbl_pettycash_replenish_detail WHERE pcv_replenish_id = '$row[pcv_replenish_id]'");
	while ($rowPRdet = mysql_fetch_array($getReplenishDetail)) {
		$amount += $rowPRdet['amount'];
	}
			
	if($row["status"] == "0"){
		$status = "Saved";
		$status_span = "Saved";
		$status_color = "";
	}else if($row["status"]  == "1"){
		$status = "Finished";
		$status_span = "Finished";
		$status_color = "green";
	}else{
		$status = "Cancelled";
		$status_span = "Cancelled";
		$status_color = "blue";
	}

		/////---------------------------approve--------------------
		$approve_by = $row['approve_by'];

		if ($approve_by == "" && $getMainRF['chrts'] == 'CASH IN BANK' && $row['date'] > '2019-01-01' || $approve_by == "" && $row['rfr_status'] == '1' && $row['date'] > '2019-01-01' || $approve_by == "" && $row['rfr_status'] == '0' && $row['date'] > '2019-01-01') {
			//$approved_by = '<div class="icheck-default d-inline" style="width: 140px;"><input type="checkbox" onclick="ApprovalPFR('.$row['pcv_replenish_id'].')" name="approve" value="'.$user_id.'" class="1" id="cbPFR'.$row['pcv_replenish_id'].'"><label for="cbPFR'.$row['pcv_replenish_id'].'"></label><span class="label label-default" style="font-size: 10px;">Pending</span></div>';
			$approved_by = 'Pending';
		}else if($approve_by != "" && $approve_by == $user_id){
			//$approved_by = '<div class="icheck-success d-inline"  style="width: 140px;"><input type="checkbox" onclick="ApprovalPFR('.$row['pcv_replenish_id'].')" disabled="disabled" name="approve" value="'.$user_id.'" class="" id="cbPFR'.$row['pcv_replenish_id'].'"><label for="cbPFR'.$row['pcv_replenish_id'].'" style="display: none;"></label><span class="label label-primary" style="font-size: 10px;">'.getUser($row['approve_by']).'</span></div>';
			$approved_by = getUser($row['approve_by']);
		}else if($approve_by == "" && $getMainRF['chrts'] == 'CASH IN BANK' && $row['date'] < '2019-01-01' || $approve_by == "" && $row['rfr_status'] == '1' && $row['date'] > '2019-01-01' || $approve_by == "" && $row['rfr_status'] == '0' && $row['date'] > '2019-01-01'){
			
			//$approved_by = '<div class="icheck-success d-inline"  style="width: 140px;"><input type="checkbox" onclick="ApprovalRFR('.$row['rfe_replenish_id'].')" disabled="disabled" name="approve" value="'.$user_id.'" class="" id="cbRFR'.$row['rfe_replenish_id'].'"><label for="cbRFR'.$row['rfe_replenish_id'].'" style="display: none;"></label><span class="label label-info" style="font-size: 10px;">Notes Management</span></div>';
			$approved_by = 'Notes Management';
		}else{
			//$approved_by = '<div class="icheck-success d-inline"  style="width: 140px;"><input type="checkbox" onclick="ApprovalPFR('.$row['pcv_replenish_id'].')" disabled="disabled" name="approve" value="'.$user_id.'" class="" id="cbPFR'.$row['pcv_replenish_id'].'"><label for="cbPFR'.$row['pcv_replenish_id'].'" style="display: none;"></label><span class="label label-primary" style="font-size: 10px;">'.getUser($row['approve_by']).'</span></div>';
			$approved_by = getUser($row['approve_by']);
		}
		/////---------------------------approve--------------------

	$list = array();
	$getLiquidation = getBulkData("tracking_num","tbl_pettycash_liquidation","pcv_liquidation_id = '$row[pcv_liquidation_id]' AND company_id = '$company_id' AND branch_id = '$branch_id'");

	$list['pcv_replenish_id'] = $row["pcv_replenish_id"];
	$list['count']	= $count++;
	$list['br_id'] = $row["branch_id"];
	$list['rplnsh_num'] = $row["rplnsh_num"];
	$list['date'] = $row['date'];
	$list['amnt'] = number_format($amount, 2);
	$list['remarks'] = $row['remarks'];
	$list['status'] = $status;
	$list['status_db'] = $row['status'];
	$list['status_span'] = $status_span;
	$list['status_color'] = $status_color;
	$list['rfr_stats'] = $row['rfr_status'];
	if($row['rfr_status'] == "1"){
		$list['rfr_stat'] = "REPLENISHED";
		$list['rfr_stat_color'] = "green";
		$list['replenish_stats'] = "0";
	}else if($getMainRF['chrts'] == 'CASH IN BANK'){
		$list['rfr_stat'] = "N/A";
		$list['rfr_stat_color'] = "red";
		$list['replenish_stats'] = "1";
	}else{
		$list['rfr_stat'] = "PENDING";
		$list['rfr_stat_color'] = "orange";
		$list['replenish_stats'] = "2";
	}

	if($row['declared_status'] == 0){
		$list['dec_stat'] = "DECLARED";
		$list['dec_stat_color'] = "green";
	}else{
		$list['dec_stat'] = "UNDECLARED";
		$list['dec_stat_color'] = "orange";
	}
	$list['encodedBY'] = $createdByID['name'];
	$list['approved_by'] = $approved_by;
	
	array_push($response['data'],$list);
}

echo json_encode($response);

?>