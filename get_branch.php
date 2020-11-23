<?php
include 'config.php';

$company_code = $_POST['company_code'];
$company_id =$_POST['company_id'];
$user_id =$_POST['user_id'];
set_database($company_code);

	$get_branch = mysql_query("SELECT a.branch_id, b.branch from tbl_user_branches a inner join tbl_branch b where b.branch_id = a.branch_id and a.user_id = '$user_id' and a.visibility_status");
	$response_array['array_data'] = array();
	while ($data = mysql_fetch_array($get_branch)) {
        $response["branch_id"] = $data["branch_id"];
		$response["branch_name"] = $data["branch"];
		array_push($response_array['array_data'], $response);
	}

echo json_encode($response_array);






?>