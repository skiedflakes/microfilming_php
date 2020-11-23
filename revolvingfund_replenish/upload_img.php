<?php
include '../config.php';

$date = getCurrentDate();

$user_id = $_POST['user_id'];
$company_code = $_POST['company_code'];
$company_id = $_POST['company_id'];
$branch_id = $_POST['branch_id'];
set_database($company_code);

$mf_ref = $_POST['ref_num'];
$detailsId = $_POST['details_id'];
$chart_id = $_POST['chart_id'];
$mod = $_POST['module'];

$name = $_FILES['file']['name'];
$type = $_FILES['file']['type'];
$size = $_FILES['file']['size'];

$response['response_json'] = array();

$dep_ref = explode(":", $mf_ref);

if($dep_ref[0] == "DEP") { // if deposit
	$exp_ref = explode("-", $dep_ref[1]);
	$module = $dep_ref[0]; //DEP
	$refs 	= $dep_ref[0]; 
	$mfRef 	= $dep_ref[1];
}else{
	$exp_ref = explode("-", $mf_ref);
	$refs 	= $exp_ref[0];
	$mfRef 	= $mf_ref;

	if($mod == 'DBM'){
		$module = 'DBM'; //Debit Memo
	}else if($mod == 'CDM'){
		$module = 'CDM'; //Credit Memo
	}else{
		$module = $exp_ref[0]; //CP
	}
}


$img_name = "MF-".$refs."-".$branch_id."-".date("mdyHis",strtotime($date)).'.jpg';

if($size <= 500000){
	$quality = 50;
}else {
	$quality = 30;
}


$dir = "../../notes/assets/microfilming/".$img_name; //NEW

compressedImage($_FILES['file']['tmp_name'], $dir, $quality);


$slug = "../assets/microfilming/".$img_name; //NEW

$result = mysql_query("INSERT INTO `tbl_micro_filming_log`
		(gchart_id, company_id, branch_id, module, mf_reference, mf_reference_id, description, slug, date_added, encoded_by) 
		VALUES 
		('$chart_id','$company_id','$branch_id','$module','$mfRef','$detailsId','$mfRef','$slug','$date','$user_id')" );


if(!file_exists($dir)){
	if(!file_exists($dir)){
		$handle = fopen($dir,'w+');
		fclose($handle);
		chmod($dir, 0777);
	}
}

if($result){
	$list['success'] = '1';
} else{
	$list['success'] = '0';
}

array_push($response['response_json'], $list);


echo json_encode($response);

function compressedImage($source, $path, $quality) {

	$info = getimagesize($source);

	if ($info['mime'] == 'image/jpeg') 
		$image = imagecreatefromjpeg($source);
	elseif ($info['mime'] == 'image/gif') 
		$image = imagecreatefromgif($source);
	elseif ($info['mime'] == 'image/png') 
		$image = imagecreatefrompng($source);
	elseif($info['mime'] == 'image/jpg')
		$image = imagecreatefromjpeg($source);

	imagejpeg($image, $path, $quality);

	//return $info['mime'];
}

?>