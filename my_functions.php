<?php

//EGG BROILER
function getTablePrefixEB(){
	return ($_SESSION['wdysolutions_program'] == 'wdysolutions_EGG')?"eggs":(($_SESSION['wdysolutions_program'] == 'wdysolutions_BROILER')?"broiler":"");
}
function getTablePrefixEBNOS(){
	return ($_SESSION['wdysolutions_program'] == 'wdysolutions_EGG')?"egg":(($_SESSION['wdysolutions_program'] == 'wdysolutions_BROILER')?"broiler":"");
}

function getTablePrefixEBNOEGGFILE(){
	return ($_SESSION['wdysolutions_program'] == 'wdysolutions_EGG')?"":(($_SESSION['wdysolutions_program'] == 'wdysolutions_BROILER')?"broiler_":"");
}

function getTablePrefixNOEGGtbl(){
	return ($_SESSION['wdysolutions_program'] == 'wdysolutions_EGG')?"":(($_SESSION['wdysolutions_program'] == 'wdysolutions_BROILER')?"_broiler":"");
}
//EGG BROILER END

function getUserAuth($module,$user_id = -1){
	$user_id = ($user_id==-1)?$_SESSION['system']['userid']:$user_id;
	$company_id = $_SESSION['system']['company_id'];
	if(isOwner($user_id) == 1){
		return 1;
	}else{
		$fetch = FM_SELECT_QUERY("a.status","tbl_user_privilege_item AS p , tbl_user_privilege_auth AS a","a.priv_id = p.priv_id AND a.company_id = '$company_id' AND p.module = '$module' AND a.user_id = '$user_id'");
		return $fetch[0] * 1;
	}
}
function isOwner($user_id = -1){
	$user_id = ($user_id==-1)?$_SESSION['system']['userid']:$user_id;
	$company_id = $_SESSION['system']['company_id'];

	if($user_id > 0){
		$fetch = FM_SELECT_QUERY("category_id","tbl_users","user_id = '$user_id' AND company_id = '$company_id'");
		return ($fetch[0] == 0)?1:0;
	}else{
		return 0;
	}
}
function url_page($page){
	if(HASH_URL == 'Y'){
		$url_page = md5($page);
	}else{
		$url_page = $page;
	}
	return $url_page;
}
function get_timeago($ptime)
{
    $estimate_time = time() - $ptime;

    if( $estimate_time < 1 )
    {
        return 'less than 1 second ago';
    }

    $condition = array( 
                12 * 30 * 24 * 60 * 60  =>  'year',
                30 * 24 * 60 * 60       =>  'month',
                24 * 60 * 60            =>  'day',
                60 * 60                 =>  'hour',
                60                      =>  'minute',
                1                       =>  'second'
    );

    foreach( $condition as $secs => $str )
    {
        $d = $estimate_time / $secs;

        if( $d >= 1 )
        {
            $r = round( $d );
            return $r . ' ' . $str . ( $r > 1 ? 's' : '' ) . ' ago';
        }
    }
}

function randChar($length = 6) {
	$str = "";
	$characters = array_merge(range('A','Z'), range('a','z'), range('0','9'));
	$max = count($characters) - 1;
	for ($i = 0; $i < $length; $i++) {
		$rand = mt_rand(0, $max);
		$str .= $characters[$rand];
	}
	return $str;
}

function getCompanyCode($company_id){
	$fetch = mysql_fetch_array(mysql_query("SELECT company_code FROM tbl_company WHERE company_id = '$company_id'"));
	return $fetch[0];
}
function FEEDS_updateFormulationHeaderUpdatedBy($formulation_header_id , $user_id = -1){
	$updated_by = ($user_id == -1) ? $_SESSION['system']['userid'] : $user_id;
	$updated_date = getCurrentDate();
	$sql = mysql_query("UPDATE tbl_formulation_header_feeds SET updated_by = '$updated_by' , updated_date = '$updated_date' WHERE formulation_header_id = '$formulation_header_id'");
	return $sql ? 1 : 0;
}
// ================================ START FEED MAX FUNCTIONS ==================================== //
			function FM_getSumRawQty($code){
				$query_nut = mysql_fetch_array(mysql_query("SELECT SUM(raw_qty) FROM `tbl_formula_matrix_table` WHERE code = '$code'"));
				return $query_nut[0] * 1;
			}
			function FM_getSumOldRawQty($code){
				$query_nut = mysql_fetch_array(mysql_query("SELECT SUM(old_raw_qty) FROM `tbl_formula_matrix_table` WHERE code = '$code'"));
				return $query_nut[0] * 1;
			}
			function FM_getSumStoredRawQty($code){
				$query_nut = mysql_fetch_array(mysql_query("SELECT SUM(stored_raw_qty) FROM `tbl_formula_matrix_table` WHERE code = '$code'"));
				return $query_nut[0] * 1;
			}
			function FM_getFormulaBatch($code){
				$fetch = mysql_fetch_array(mysql_query("SELECT batch_size FROM `tbl_formula_company_matrix` WHERE code = '$code'"));
				return $fetch[0] * 1;
			}

			function roundQty($qty,$round){
				if($round > 0){
					$ini_val =  round($qty / $round) * $round;
					return ($qty > 0 && $ini_val == 0)?$round:$ini_val;
					// if($qty > 0 && $ini_val == 0){
					// 	return $round;
					// }else{
					// 	return $ini_val;
					// }
				}else{
					return $qty;
				}
			}
			function getAnimalOpt(){
				$get_animal = mysql_query("SELECT * FROM `tbl_diy_animal`");
				while ($row_a = mysql_fetch_array($get_animal)) {
					$content .= "<option value='$row_a[0]'>$row_a[animal_name]</option>";
				}
				return $content;
			}

			function getAnimalName($id){
				if ($id > 0) {
					$fetch = mysql_fetch_array(mysql_query("SELECT animal_name FROM `tbl_diy_animal` WHERE animal_id = $id"));
					return $fetch[0];
				}else{
					return "General";
				}
			}

			function STORED_getFormulaCost($code){
				$fetch = mysql_fetch_array(mysql_query("SELECT SUM(raw_qty * raw_cost) / SUM(raw_qty) FROM `tbl_diy_stored_formula_details` WHERE code = '$code'"));
				return new_number_format($fetch[0],2);
			}
			function DIY_updateStoredCost($raw_id,$cost,$nutri_id){
				$company_id = $_SESSION['system']['company_id'];
				$count = mysql_fetch_array(mysql_query("SELECT COUNT(cc_id) FROM `tbl_formula_company_cost` WHERE raw_id = $raw_id AND company_id = $company_id AND nutri_id = $nutri_id"));
				if($count[0] > 0){
					mysql_query("UPDATE `tbl_formula_company_cost` SET cost = '$cost' WHERE raw_id = $raw_id AND company_id = $company_id AND nutri_id = $nutri_id");
				}else{
					mysql_query("INSERT INTO `tbl_formula_company_cost`(`cc_id`,`nutri_id`, `company_id`, `raw_id`, `cost`) VALUES (NULL,$nutri_id,$company_id,$raw_id,'$cost')");
				}
			}
		// ============================= STANDARD ============================== //
			function STD_getRawCode($raw_id){
				$fetch = mysql_fetch_array(mysql_query("SELECT raw_code FROM `tbl_diy_std_raw_mats` WHERE raw_id = '$raw_id'"));
				return $fetch[0];
			}
			function STD_getDefaultRawId($raw_id){
				$company_id = $_SESSION['system']['company_id'];
				$raw_code = mysql_fetch_array(mysql_query("SELECT raw_code FROM `tbl_diy_std_raw_mats` WHERE company_id = $company_id AND raw_id = $raw_id"));
				$raw_id_ini = mysql_fetch_array(mysql_query("SELECT raw_id FROM `tbl_diy_std_raw_mats` WHERE company_id = $company_id AND raw_code = '$raw_code[0]' AND default_status = 1"));
				$raw_id_new = $raw_id_ini[0] * 1;
				if($raw_id_new > 0){
					return $raw_id_new;
				}else{
					return $raw_id;
				}
			}
			function STD_updateStoredCost($raw_id,$raw_cost){
				$company_id = $_SESSION['system']['company_id'];
				$fetch = mysql_fetch_array(mysql_query("SELECT COUNT(cost_id) FROM `tbl_diy_company_cost` WHERE company_id = $company_id AND raw_id = $raw_id"));
				if($fetch[0] > 0){
					mysql_query("UPDATE tbl_diy_company_cost SET cost = '$raw_cost' WHERE company_id = '$company_id' AND raw_id = $raw_id");
				}else{
					mysql_query("INSERT INTO `tbl_diy_company_cost`(`cost_id`, `company_id`, `raw_id`, `cost`) VALUES (NULL,'$company_id','$raw_id','$raw_cost')");
				}
			}
			function STD_getAnimalOpt(){
				$company_id = $_SESSION['system']['company_id'];
				$get_animal = mysql_query("SELECT * FROM `tbl_diy_std_animal` WHERE company_id = $company_id");
				while ($row_a = mysql_fetch_array($get_animal)) {
					$content .= "<option value='$row_a[0]'>$row_a[animal_name]</option>";
				}
				return $content;
			}
			function STD_getAnimalOptU($nut_id){
				$company_id = $_SESSION['system']['company_id'];
				$my_nut_ini = mysql_query("SELECT animal_id FROM `tbl_diy_std_nut_animal` WHERE nut_id = $nut_id AND company_id = $company_id");
				if(mysql_num_rows($my_nut_ini)){
					while ($row_nut = mysql_fetch_array($my_nut_ini)) {
						$my_nut[] = $row_nut[0];
					}
				}else{
					$my_nut = array(0);
				}
				$get_animal = mysql_query("SELECT * FROM `tbl_diy_std_animal` WHERE company_id = $company_id");
				while ($row_a = mysql_fetch_array($get_animal)) {
					if(in_array($row_a[0], $my_nut)){
			 			$selected = "selected";
			  		}else{
			  			$selected = "";
			  		}
			  		$content .= "<option value='$row_a[0]' $selected>$row_a[animal_name]</option>";
				}
				return $content;
			}
			function STD_getAveIdSelected($raw_code){
				$ave_ids = mysql_fetch_array(mysql_query("SELECT ave_ids FROM tbl_diy_std_raw_mats WHERE raw_code = '$raw_code' AND original = 2"));
				$ave_id = explode(",",$ave_ids[0]);
				$r_q = mysql_query("SELECT raw_id,IF(original=1, 'Original', version_name) FROM tbl_diy_std_raw_mats WHERE raw_code = '$raw_code' AND original != 2");
				while($r_a = mysql_fetch_array($r_q)){
					$selected = (in_array($r_a[0], $ave_id))?"selected":"";
					$content .= "<option class='$raw_code' value='ss$r_a[0]' $selected>$r_a[1]</option>";
				}
				return $content;
			}
		// ============================= STANDARD ============================== //
// ================================ END FEED MAX FUNCTIONS ==================================== //


function new_number_format($val,$decimal){
	return number_format($val * 1,$decimal,".","");
}

function getAPType($ref_num){
	$company_id = $_SESSION["system"]["company_id"];
	
	if(SUBSTR($ref_num, 0, 2) == "PO"){
		$po_num = $ref_num;
	}else{
		$poNum = mysql_fetch_array(mysql_query("SELECT po_number FROM tbl_rr_header WHERE receiving_number='$ref_num' AND company_id='$company_id'"));
		$po_num = $poNum[0];
	}
	
	$reqNum = mysql_fetch_array(mysql_query("SELECT requisition_num FROM tbl_po_header WHERE po_number='$po_num' AND company_id='$company_id'"));
	$checkType = mysql_fetch_array(mysql_query("SELECT asset FROM tbl_requisition WHERE requisition_num='$reqNum[0]' AND company_id='$company_id'"));
	
	if($checkType[0] == "Yes"){
		$type = 2;
	}else{
		$type = 1;
	}
	
	return $type;
}

function saveDocTypeOverride($ref,$doc_type,$doc_num,$module){
	$company_id = $_SESSION["system"]["company_id"];
	$branch_id = get_branch();
	$date_added = date("Y-m-d H:i:s");
	$check_dup = mysql_fetch_array(mysql_query("SELECT count(override_id) FROM tbl_override_document WHERE ref_num = '$ref' AND doc_type_id = $doc_type AND doc_num='$doc_num' AND company_id = $company_id AND branch_id=$branch_id"));
		if($check_dup[0] > 0){
		}else{
			mysql_query("INSERT INTO tbl_override_document (`doc_type_id`,`doc_num`,`ref_num`,`module`,`company_id`,`branch_id`,`date_added`) VALUES ('$doc_type','$doc_num','$ref','$module','$company_id','$branch_id','$date_added')");
		}
}//Override document module 2018-02-28

function checkUsernameExist($username){
	$result = mysql_query("select * from tbl_users where username = '$username' ") or die (mysql_error());
	$count = mysql_num_rows($result);
	
	if($count > 0){
		$val = true;
	}else{ 
		$val = false;
	}
	return $val;
}

function getRegion($id){
	$region = mysql_fetch_array(mysql_query("SELECT region from tbl_branch where branch_id='$id'"));
	return $region[0];
}


function checkIfHasInternetConnection(){
	if ( @fopen("http://www.google.com", "r") ) {
		$response = 1;
	}else{
		$response = 0;
	}
	return $response;
}

function switchConnection($type){

	$company_id = $_SESSION['system']['company_id'];
	
	if($type == 1){
		// CONNECT ONLINE
		$host = $GLOBALS['local_config']['mysql']['host'];
		$username = $GLOBALS['local_config']['mysql']['username'];
		$password = $GLOBALS['local_config']['mysql']['password'];
		$database = $GLOBALS['config']['mysql']['db_prefix'].getCompanyCode($company_id);

		@mysql_connect($host, $username, $password) or die("Cannot connect to Online MySQL Server");
		@mysql_select_db($database) or die ("Cannot connect to Online Database");
		@mysql_query("SET SESSION sql_mode=''");
	}else{
		// CONNECT LOCAL
		$host = $GLOBALS['config']['mysql']['host'];
		$username = $GLOBALS['config']['mysql']['username'];
		$password = $GLOBALS['config']['mysql']['password'];
		$database = $GLOBALS['config']['mysql']['db_prefix'].getCompanyCode($company_id);

		@mysql_connect($host, $username, $password) or die("Cannot connect to Local MySQL Server");
		@mysql_select_db($database) or die ("Cannot connect to Local Database");
		@mysql_query("SET SESSION sql_mode=''");

	}
}

function priceWatchSaver($product_id, $purchase_date, $cost){

	// check if from local
	$company_id = $_SESSION['system']['company_id'];
	$isFromLocal = mysql_fetch_array(mysql_query("SELECT local_db_status from tbl_company where company_id='$company_id' "));
	if($isFromLocal[0] == 1){
		// check if has internet connection
		if(checkIfHasInternetConnection() == 1){
			// upload all products to server
			uploadProductsFromLocal();

			switchConnection(1);
			addDailyPriceWatch($product_id, $purchase_date, $cost);
			switchConnection(-1);
		}
		
	}else{
		// call price watch saver
		addDailyPriceWatch($product_id, $purchase_date, $cost);
	}

}

function uploadProductsFromLocal(){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	//switchConnection(1);

	// insert branch if not exists
	$fetch_branch = mysql_query("SELECT * from tbl_branch where branch_id='$branch_id' and company_id='$company_id' ") or die(mysql_error());
	$branch_row = mysql_fetch_array($fetch_branch);
	$br_branch_id = $branch_row['branch_id'];
	$br_island_group = $branch_row['island_group'];
	$br_region = $branch_row['region'];
	$br_province = $branch_row['province'];
	$br_company_id = $branch_row['company_id'];
	$br_branch = $branch_row['branch'];
	$br_remarks = $branch_row['remarks'];
	$br_status = $branch_row['status'];
	$br_visibility_status = $branch_row['visibility_status'];
	$br_encoded_by = $branch_row['encoded_by'];

	switchConnection(1);

	$count_branch_row = mysql_fetch_array(mysql_query("SELECT count(branch_id) from tbl_branch where branch_id='$branch_id' and company_id='$company_id' "));
	if($count_branch_row[0] == 0){
		mysql_query("INSERT INTO `tbl_branch` (`branch_id`, `island_group`, `region`, `province`, `company_id`, `branch`, `remarks`, `status`, `visibility_status`, `encoded_by`) VALUES ('$br_branch_id', '$br_island_group', '$br_region', '$br_province', '$br_company_id', '$br_branch', '$br_remarks', '$br_status', '$br_visibility_status', '$br_encoded_by')") or die(mysql_error());
	}


	switchConnection(-1);


	$fetch_local_products = mysql_query("SELECT * from tbl_productmaster where ((company_id='$company_id' and branch_id='$branch_id') or (company_id='0' and branch_id='0')) and (product_categ_id='2048' or product_categ_id='2039') ") or die(mysql_error());

	while($local_products_row = mysql_fetch_array($fetch_local_products)){

		$product_id = $local_products_row['product_id'];
		$product_categ_id = $local_products_row['product_categ_id'];
		$company_id = $local_products_row['company_id'];
		$branch_id = $local_products_row['branch_id'];
		$product = $local_products_row['product'];
		$description = $local_products_row['description'];
		$order_number = $local_products_row['order_number'];
		$product_type = $local_products_row['product_type'];
		$stock_code = $local_products_row['stock_code'];
		$product_code = $local_products_row['product_code'];
		$stock_brand = $local_products_row['stock_brand'];
		$formulation_id = $local_products_row['formulation_id'];
		$status = $local_products_row['status'];
		$egg_type = $local_products_row['egg_type'];
		$egg_weight_low = $local_products_row['egg_weight_low'];
		$egg_weight_high = $local_products_row['egg_weight_high'];
		$system_category = $local_products_row['system_category'];
		$encoded_by = $local_products_row['encoded_by'];

		switchConnection(1);
		$count_row = mysql_fetch_array(mysql_query("SELECT count(product_id) from tbl_productmaster where (product_id='$product_id' and product_categ_id='2048') or (product_id='$product_id' and product_categ_id='2039') "));
		if($count_row[0] == 0){
			mysql_query("INSERT INTO `tbl_productmaster` (`product_id`, `product_categ_id`, `company_id`, `branch_id`, `product`, `description`, `order_number`, `product_type`, `stock_code`, `product_code`, `stock_brand`, `formulation_id`, `status`, `egg_type`, `egg_weight_low`, `egg_weight_high`, `system_category`, `encoded_by`) VALUES ('$product_id', '$product_categ_id', '$company_id', '$branch_id', '$product', '$description', '$order_number', '$product_type', '$stock_code', '$product_code', '$stock_brand', '$formulation_id', '$status', '$egg_type', '$egg_weight_low', '$egg_weight_high', '$system_category', '$encoded_by') ") or die(mysql_error());
		}

		switchConnection(-1);

	}

	//$local_db = $GLOBALS['config']['mysql']['db_prefix'].getCompanyCode($company_id);

	
}

function addDailyPriceWatch($product_id, $purchase_date, $cost){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
	$region = getRegion($branch_id);


	// GET PRODUCT CODE
	$product_code = mysql_fetch_array(mysql_query("SELECT product_code from tbl_productmaster where product_id='$product_id'"));
	if($product_code[0] == ''){
		// check if added to price watch

		$main_db = $GLOBALS['local_config']['mysql']['orig_db_main'];
		mysql_query("USE $main_db") or die(mysql_error());


		$count_pwp = mysql_fetch_array(mysql_query("SELECT count(pwp_id), product_tag from tbl_price_watch_products where product_id='$product_id' and company_id='$company_id' "));
		if($count_pwp[0] > 0){

			$fetch_pwp = mysql_query("SELECT lowest_cost, highest_cost from tbl_daily_price_watch where product_tag='$count_pwp[product_tag]' and region='$region' and date_added = '$purchase_date' ");
			$daily_price_watch_row = mysql_fetch_array($fetch_pwp);

			if(mysql_num_rows($fetch_pwp) > 0){
				if($cost > $daily_price_watch_row['highest_cost']){
					// UPDATE HIGHEST COST
					mysql_query("UPDATE tbl_daily_price_watch set highest_cost='$cost' where region='$region' and product_tag='$count_pwp[product_tag]' and date_added='$purchase_date' ") or die(mysql_error());
				}

				if($cost < $daily_price_watch_row['lowest_cost']){
					// UPDATE LOWEST COST
					mysql_query("UPDATE tbl_daily_price_watch set lowest_cost='$cost' where region='$region' and product_tag='$count_pwp[product_tag]' and date_added='$purchase_date' ") or die(mysql_error());
				}

			}else{

				// fetch previous price watch
				$prev_price_watch_row = mysql_fetch_array(mysql_query("SELECT lowest_cost, highest_cost from tbl_daily_price_watch where product_tag='$count_pwp[product_tag]' and region='$region' ORDER BY date_added DESC LIMIT 1 "));

				$prev_low = $prev_price_watch_row['lowest_cost'];
				$prev_high = $prev_price_watch_row['highest_cost'];

				if($cost <= $prev_low AND ($prev_low != "" AND $prev_low != 0) ){
					$new_low_cost = $cost;
					$new_high_cost = $prev_price_watch_row['lowest_cost'];
				}else if($cost >= $prev_high AND ($prev_high != "" AND $prev_high != 0)){
					$new_low_cost = $prev_price_watch_row['highest_cost'];
					$new_high_cost = $cost;
				}else{
					$new_high_cost = $cost;
					$new_low_cost = $cost;
				}

				/*if($cost <= $prev_price_watch_row['lowest_cost']){
					$new_low_cost = $cost;
					$new_high_cost = $prev_price_watch_row['lowest_cost'];
				}else if($cost >= $prev_price_watch_row['highest_cost']){
					$new_low_cost = $prev_price_watch_row['highest_cost'];
					$new_high_cost = $cost;
				}else{
					if($cost <= $prev_price_watch_row['lowest_cost']){
						$new_low_cost = $cost;
						$new_high_cost = $prev_price_watch_row['lowest_cost'];
					}else{
						$new_low_cost = $prev_price_watch_row['lowest_cost'];
						$new_high_cost = $cost;
					}
				}*/

				mysql_query("INSERT INTO `tbl_daily_price_watch`(`product_tag`, `region`, `lowest_cost`, `highest_cost`, `date_added`) VALUES ('$count_pwp[product_tag]','$region','$new_low_cost','$new_high_cost','$purchase_date')") or die(mysql_error());

			}
		}


	}else{
		// fetch all same product code
		$fetch_products = mysql_query("SELECT product_id from tbl_productmaster where product_code='$product_code[0]' and (company_id='$company_id' or company_id='0')  ") or die(mysql_error());

		
		while($products_row = mysql_fetch_array($fetch_products)){
			// count if added to price watch


			$main_db = $GLOBALS['local_config']['mysql']['orig_db_main'];
			mysql_query("USE $main_db") or die(mysql_error());

			$count_pwp = mysql_fetch_array(mysql_query("SELECT count(pwp_id), product_tag from tbl_price_watch_products where product_id='$products_row[product_id]' and company_id='$company_id' "));

			if($count_pwp[0] > 0){
				//$region = getRegion($branch_id);

				$fetch_pwp = mysql_query("SELECT lowest_cost, highest_cost from tbl_daily_price_watch where product_tag='$count_pwp[product_tag]' and region='$region' and date_added = '$purchase_date' ");
				$daily_price_watch_row = mysql_fetch_array($fetch_pwp);

				if(mysql_num_rows($fetch_pwp) > 0){
					if($cost > $daily_price_watch_row['highest_cost']){
						// UPDATE HIGHEST COST
						mysql_query("UPDATE tbl_daily_price_watch set highest_cost='$cost' where region='$region' and product_tag='$count_pwp[product_tag]' and date_added='$purchase_date' ") or die(mysql_error());
					}

					if($cost < $daily_price_watch_row['lowest_cost']){
						// UPDATE LOWEST COST
						mysql_query("UPDATE tbl_daily_price_watch set lowest_cost='$cost' where region='$region' and product_tag='$count_pwp[product_tag]' and date_added='$purchase_date' ") or die(mysql_error());
					}

				}else{
					// fetch previous price watch
					$prev_price_watch_row = mysql_fetch_array(mysql_query("SELECT lowest_cost, highest_cost from tbl_daily_price_watch where product_tag='$count_pwp[product_tag]' and region='$region' ORDER BY date_added DESC LIMIT 1 "));

					$prev_low = $prev_price_watch_row['lowest_cost'];
					$prev_high = $prev_price_watch_row['highest_cost'];

					if($cost <= $prev_low AND ($prev_low != "" AND $prev_low != 0) ){
						$new_low_cost = $cost;
						$new_high_cost = $prev_price_watch_row['lowest_cost'];
					}else if($cost >= $prev_high AND ($prev_high != "" AND $prev_high != 0)){
						$new_low_cost = $prev_price_watch_row['highest_cost'];
						$new_high_cost = $cost;
					}else{
						$new_high_cost = $cost;
						$new_low_cost = $cost;
					}

					/*if($cost <= $prev_price_watch_row['lowest_cost']){
						$new_low_cost = $cost;
						$new_high_cost = $prev_price_watch_row['lowest_cost'];
					}else if($cost >= $prev_price_watch_row['highest_cost']){
						$new_low_cost = $prev_price_watch_row['highest_cost'];
						$new_high_cost = $cost;
					}else{
						if($cost <= $prev_price_watch_row['lowest_cost']){
							$new_low_cost = $cost;
							$new_high_cost = $prev_price_watch_row['lowest_cost'];
						}else{
							$new_low_cost = $prev_price_watch_row['lowest_cost'];
							$new_high_cost = $cost;
						}
					}*/

					mysql_query("INSERT INTO `tbl_daily_price_watch`(`product_tag`, `region`, `lowest_cost`, `highest_cost`, `date_added`) VALUES ('$count_pwp[product_tag]','$region','$new_low_cost','$new_high_cost','$purchase_date')") or die(mysql_error());
				}
			}
		}
	}

	$database = $GLOBALS['config']['mysql']['db_prefix'].getCompanyCode($company_id);
	mysql_query("USE $database ") or die(mysql_error());

}


// swine price watch saver

function priceWatchSaverSwine($swine_type, $delivery_date, $cost){

	// check if from local
	$company_id = $_SESSION['system']['company_id'];
	$isFromLocal = mysql_fetch_array(mysql_query("SELECT local_db_status from tbl_company where company_id='$company_id' "));
	if($isFromLocal[0] == 1){
		// check if has internet connection
		if(checkIfHasInternetConnection() == 1){

			switchConnection(1);
			addDailyPriceWatchSwine($swine_type, $delivery_date, $cost);
			switchConnection(-1);
		}
		
	}else{
		// call price watch saver
		addDailyPriceWatchSwine($swine_type, $delivery_date, $cost);
	}

}

function addDailyPriceWatchSwine($swine_type, $delivery_date, $cost){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
	$region = getRegion($branch_id);

	if($cost > 0){



		$main_db = $GLOBALS['local_config']['mysql']['orig_db_main'];
		mysql_query("USE $main_db") or die(mysql_error());


		$fetch_dpw = mysql_query("SELECT lowest_cost, highest_cost from tbl_daily_price_watch_pig where product_tag='$swine_type' and region='$region' and date_added = '$delivery_date' ");
		$daily_price_watch_row = mysql_fetch_array($fetch_dpw);

		if(mysql_num_rows($fetch_dpw) > 0){
			if($cost > $daily_price_watch_row['highest_cost']){
				// UPDATE HIGHEST COST
				mysql_query("UPDATE tbl_daily_price_watch_pig set highest_cost='$cost' where region='$region' and product_tag='$swine_type' and date_added='$delivery_date' ") or die(mysql_error());
			}


			if($cost < $daily_price_watch_row['lowest_cost']){
				// UPDATE LOWEST COST
				mysql_query("UPDATE tbl_daily_price_watch_pig set lowest_cost='$cost' where region='$region' and product_tag='$swine_type' and date_added='$delivery_date' ") or die(mysql_error());
			}
		}else{
			// fetch previous price watch
			$prev_price_watch_row = mysql_fetch_array(mysql_query("SELECT lowest_cost, highest_cost from tbl_daily_price_watch_pig where product_tag='$swine_type' and region='$region' ORDER BY date_added DESC LIMIT 1 "));

			$prev_low = $prev_price_watch_row['lowest_cost'];
			$prev_high = $prev_price_watch_row['highest_cost'];

			if($cost <= $prev_low AND ($prev_low != "" AND $prev_low != 0) ){
				$new_low_cost = $cost;
				$new_high_cost = $prev_price_watch_row['lowest_cost'];
			}else if($cost >= $prev_high AND ($prev_high != "" AND $prev_high != 0)){
				$new_low_cost = $prev_price_watch_row['highest_cost'];
				$new_high_cost = $cost;
			}else{
				/*if($cost <= $prev_price_watch_row['lowest_cost']){
					$new_low_cost = $cost;
					$new_high_cost = $prev_price_watch_row['lowest_cost'];
				}else{
					$new_low_cost = $prev_price_watch_row['lowest_cost'];
					$new_high_cost = $cost;
				}*/
				$new_high_cost = $cost;
				$new_low_cost = $cost;
			}
			
			mysql_query("INSERT INTO `tbl_daily_price_watch_pig`(`product_tag`, `region`, `lowest_cost`, `highest_cost`, `date_added`) VALUES ('$swine_type','$region','$new_low_cost','$new_high_cost','$delivery_date')") or die(mysql_error());
		}



		$database = $GLOBALS['config']['mysql']['db_prefix'].getCompanyCode($company_id);
		mysql_query("USE $database ") or die(mysql_error());

	}

}

// farm stats saver

function farmStatisticsPerRegionSaver(){
	// check if from local
	$company_id = $_SESSION['system']['company_id'];

	//$values = farmStatisticsValues();

	$isFromLocal = mysql_fetch_array(mysql_query("SELECT local_db_status from tbl_company where company_id='$company_id' "));
	if($isFromLocal[0] == 1){
		// check if has internet connection
		if(checkIfHasInternetConnection() == 1){



			switchConnection(1);
			addFarmStatisticsPerRegion();
			switchConnection(-1);
		}
		
	}else{
		// call farm stats saver
		addFarmStatisticsPerRegion();
	}


}

function farmStatisticsValues(){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
	$date = date('Y-m-d', strtotime(getCurrentDate()));


	// max date recorded
	$max_day_of_month_recorded = mysql_fetch_array(mysql_query("SELECT max(date_modified) from tbl_swine_counter where MONTH(date_modified)=MONTH('$date') and YEAR(date_modified) = YEAR('$date') and company_id='$company_id' and branch_id='$branch_id' "));

	// swine counter row
	$swine_counter_row = mysql_fetch_array(mysql_query("SELECT sow as total_sows, pregnant as total_pregnant, dry_sow as total_dry_sows, gilt as total_gilts, junior_boar as total_junior_boars, lactating as total_lactating, senior_boar as total_senior_boars, piglet as total_piglets, swine_population as total_population from tbl_swine_counter where company_id='$company_id' and branch_id='$branch_id' and date_modified='$max_day_of_month_recorded[0]'"));

	// expected to farrow standard
	$expected_days_to_farrow_standard = mysql_fetch_array(mysql_query("SELECT expected_due_days FROM tbl_alert_parameters WHERE company_id='$company_id'"));


	// total abnormal, total born alive, total born alive, ave birth weight, ave born alive, farrowing interval, ave litter size, ave gestation days
	$farrowing_row = mysql_fetch_array(mysql_query("SELECT sum(abnormal) as total_abnormal, sum(born_alive) as total_born_alive, AVG(ave_birth_wt) as ave_birth_wt, AVG(born_alive) as ave_born_alive, AVG(farrowing_interval) as ave_farrowing_interval, AVG(litter_size) as ave_litter_size, AVG(gestation_days) as ave_gestation_days, count(farrowing_id) as total_farrowed, sum(litter_size) as total_litter_size, sum(ave_birth_wt * born_alive) as total_litter_birth_wt, sum(undersize) as total_undersize from tbl_farrowing_pig where company_id='$company_id' and branch_id='$branch_id' and MONTH(date_farrowed)=MONTH('$date') and YEAR(date_farrowed)=YEAR('$date') "));

	// total matings
	$matings_count = mysql_fetch_array(mysql_query("SELECT count(breeding_id) from tbl_breeding_pig where company_id='$company_id' and branch_id='$branch_id' and MONTH(breeding_date) = MONTH('$date') and YEAR(breeding_date) = YEAR('$date') "));

	// abnormal percentage
	$abnormal_percentage = ($farrowing_row['total_abnormal']/$farrowing_row['total_born_alive'])*100;

	// total abortion
	$abortion = mysql_fetch_array(mysql_query("SELECT count(abort_id) from tbl_aborts_pig as ap, tbl_breeding_pig as bp where ap.breeding_id=bp.breeding_id and ap.company_id='$company_id' and ap.branch_id='$branch_id' and MONTH(ap.date_added)=MONTH('$date') and YEAR(ap.date_added) = YEAR('$date') "));

	// abortion percentage
	$abortion_percentage = ($abortion[0]/$matings_count[0])*100;
	

	// ave age gilt mated
	$ave_age_gilt_mated = mysql_fetch_array(mysql_query("SELECT AVG(swine_age) FROM `tbl_breeding_pig` where company_id='$company_id' and branch_id='$branch_id' and MONTH(breeding_date)=MONTH('$date') and YEAR(breeding_date)=YEAR('$date') and swine_type='G'"));

	// ave age sold
	$ave_age_sold = mysql_fetch_array(mysql_query("SELECT AVG(swine_age) from tbl_dr_header as dh, tbl_dr_detail as drd, tbl_swine as sw, tbl_pen_assignment as pa where MONTH(dh.dr_date)=MONTH('$date') and YEAR(dh.dr_date) = YEAR('$date') and dh.company_id='$company_id' and dh.branch_id='$branch_id' and (dh.status='P' or dh.status='F') and dh.delivery_number=drd.delivery_number and drd.swine_id != 0 and drd.swine_id=sw.swine_id and sw.company_id='$company_id' and (sw.swine_type = 'Weaner' or sw.swine_type='Finisher' or sw.swine_type ='Grower' or sw.swine_type ='Piglet') and sw.delivery_status = 1 "));

	// ave dry days
	$ave_dry_days = mysql_fetch_array(mysql_query("SELECT AVG(dry_days) from tbl_breeding_pig where company_id='$company_id' and branch_id = '$branch_id' and MONTH(breeding_date) = MONTH('$date') and YEAR(breeding_date) = YEAR('$date') and status = '1' and dry_days != '0'"));	

	// med cost for fatteners
	$med_cost_for_fatteners = mysql_fetch_array(mysql_query("SELECT AVG(cost * amount) as ave_med_cost_for_fatteners, sum(cost * amount) as total_med_cost_for_fatteners from tbl_medication_pig where company_id = '$company_id' and branch_id = '$branch_id' and MONTH(date)= MONTH('$date') and YEAR(date) = YEAR('$date') and swine_type != 'Piglet' and swine_type != 'Sow' and swine_type != 'Gilt' and (swine_type != 'Senior-boar' or swine_type != 'Junior-boar') "));

	// med cost for gilts
	$med_cost_for_gilts = mysql_fetch_array(mysql_query("SELECT AVG(cost * amount) as ave_med_cost_for_gilts, sum(cost * amount) as total_med_cost_for_gilts from tbl_medication_pig where company_id = '$company_id' and branch_id = '$branch_id' and MONTH(date)= MONTH('$date') and YEAR(date) = YEAR('$date') and swine_type = 'Gilt'"));

	// med cost for piglets
	$med_cost_for_piglets = mysql_fetch_array(mysql_query("SELECT AVG(cost * amount) as ave_med_cost_for_piglets, sum(cost * amount) as total_med_cost_for_piglets from tbl_medication_pig where company_id = '$company_id' and branch_id = '$branch_id' and MONTH(date)= MONTH('$date') and YEAR(date) = YEAR('$date') and swine_type = 'Piglet'"));

	// med cost for sows
	$med_cost_for_sows = mysql_fetch_array(mysql_query("SELECT AVG(cost * amount) as ave_med_cost_for_sows, sum(cost * amount) as total_med_cost_for_sows from tbl_medication_pig where company_id = '$company_id' and branch_id = '$branch_id' and MONTH(date)= MONTH('$date') and YEAR(date) = YEAR('$date') and swine_type = 'Sow'"));

	// vacc cost for fatteners
	$vacc_cost_for_fatteners = mysql_fetch_array(mysql_query("SELECT AVG(cost * v_dosage) as ave_vacc_cost_for_fatteners, sum(cost * v_dosage) as total_vacc_cost_for_fatteners from tbl_vaccine_pig where company_id = '$company_id' and branch_id = '$branch_id' and MONTH(v_date)= MONTH('$date') and YEAR(v_date) = YEAR('$date') and swine_type != 'Piglet' and swine_type != 'Sow' and swine_type != 'Gilt' and (swine_type != 'Senior-boar' or swine_type != 'Junior-boar')"));

	// vacc cost for gilts
	$vacc_cost_for_gilts = mysql_fetch_array(mysql_query("SELECT AVG(cost * v_dosage) as ave_vacc_cost_for_gilts, sum(cost * v_dosage) as total_vacc_cost_for_gilts from tbl_vaccine_pig where company_id = '$company_id' and branch_id = '$branch_id' and MONTH(v_date)=MONTH('$date') and YEAR(v_date) = YEAR('$date') and swine_type = 'Gilt'"));

	// vacc cost for piglets
	$vacc_cost_for_piglets = mysql_fetch_array(mysql_query("SELECT AVG(cost * v_dosage) as ave_vacc_cost_for_piglets, sum(cost * v_dosage) as total_vacc_cost_for_piglets from tbl_vaccine_pig where company_id = '$company_id' and branch_id = '$branch_id' and MONTH(v_date)=MONTH('$date') and YEAR(v_date) = YEAR('$date') and swine_type = 'Piglet'"));

	// vacc cost for sows
	$vacc_cost_for_sows = mysql_fetch_array(mysql_query("SELECT AVG(cost * v_dosage) as ave_vacc_cost_for_sows, sum(cost * v_dosage) as total_vacc_cost_for_sows from tbl_vaccine_pig where company_id = '$company_id' and branch_id = '$branch_id' and MONTH(v_date)=MONTH('$date') and YEAR(v_date) = YEAR('$date') and swine_type = 'Sow'"));

	// weaning
	$weaning_row = mysql_fetch_array(mysql_query("SELECT AVG(num_heads_weaned) as ave_number_of_weaned_piglets, AVG(days_weaned) as ave_days_weaned, AVG(ave_weaning_wt) as ave_weaning_wt from tbl_farrowing_pig where company_id='$company_id' and branch_id='$branch_id' and MONTH(weaning_date)=MONTH('$date') and YEAR(weaning_date)=YEAR('$date')"));

	// ave wt sold, total wt sold, total cost sold
	$dr_swine_row = mysql_fetch_array(mysql_query("SELECT AVG(swine_weight) as ave_weight_sold, sum(swine_weight) as total_weight_sold, sum(cost) as total_cost_sold, sum(amount) as total_price_sold, count(dr_detail_id) as total_fatteners_sold from tbl_dr_header as dh, tbl_dr_detail as drd, tbl_swine as sw where MONTH(dh.dr_date)=MONTH('$date') and YEAR(dh.dr_date) = YEAR('$date') and dh.company_id='$company_id' and dh.branch_id='$branch_id' and (dh.status='P' or dh.status='F') and dh.delivery_number=drd.delivery_number and drd.swine_id != 0 and drd.swine_id=sw.swine_id and sw.company_id='$company_id' and (sw.swine_type = 'Weaner' or sw.swine_type='Finisher' or sw.swine_type ='Grower' or sw.swine_type ='Piglet') and sw.delivery_status = 1"));

	// breeding rate
	if($swine_counter_row['total_sows'] > 0){
		$breeding_rate = ($swine_counter_row['total_pregnant']/$swine_counter_row['total_sows']) * 100;
	}else{
		$breeding_rate = 0;
	}

	// carcass recovery weight
	$carcass_recovery_wt = mysql_fetch_array(mysql_query("SELECT AVG(recovery_weight) from tbl_carcass_header_pig where company_id = '$company_id' and branch_id = '$branch_id' and MONTH(date_added)=MONTH('$date') and YEAR(date_added) = YEAR('$date') and status = 'F'"));

	// carcass swine
	$carcass_swine = mysql_fetch_array(mysql_query("SELECT count(cs_id) from tbl_carcass_swine where company_id = '$company_id' and branch_id = '$branch_id' and MONTH(date_added)=MONTH('$date') and YEAR(date_added) = YEAR('$date') and status = 'F'"));

	// confirmed to farrow
	$confirmed_to_farrow = mysql_fetch_array(mysql_query("SELECT count(breeding_id) from tbl_breeding_pig where company_id = '$company_id' and branch_id = '$branch_id' and MONTH(breeding_date) = MONTH('$date') and YEAR(breeding_date) = YEAR('$date') and confirmed_to_farrow >= 2 and status = 1 "));

	// conception rate
	$conception_rate = ($confirmed_to_farrow[0]/$matings_count[0]) * 100;

	// cost to produce
	$cost_to_produce = $ctp = ($dr_swine_row['total_cost_sold']/$dr_swine_row['total_weight_sold']);

	// culled sows
	$culled_sows = mysql_fetch_array(mysql_query("SELECT count(sw.swine_id) from  tbl_swine as sw, tbl_culling_pig as cul where cul.company_id='$company_id' and cul.branch_id='$branch_id' and MONTH(cul.date_added)=MONTH('$date') and YEAR(cul.date_added)=YEAR('$date') and sw.cull_status = '1' and cul.swine_id=sw.swine_id "));

	// dry sows mated
	$dry_sows_mated = mysql_fetch_array(mysql_query("SELECT count(breeding_id) from tbl_breeding_pig where company_id='$company_id' and branch_id='$branch_id' and swine_type='S' and MONTH(breeding_date) = MONTH('$date') and YEAR(breeding_date) = YEAR('$date') "));

	// expected to farrow
	$expected_to_farrow = mysql_fetch_array(mysql_query("SELECT count(breeding_id) from tbl_breeding_pig where company_id= '$company_id' and branch_id = '$branch_id' and MONTH(DATE_ADD(breeding_date, INTERVAL '$expected_days_to_farrow_standard[0]' DAY))=MONTH('$date') and YEAR(DATE_ADD(breeding_date, INTERVAL '$expected_days_to_farrow_standard[0]' DAY))=YEAR('$date') and status = 1 "));

	// farrowing rate
	$start_date = date('Y', strtotime($date)).'-'.date('m', strtotime($date)).'-01';
	$expected_days_to_farrow = $expected_days_to_farrow_standard[0] * -1;
	$total_matings_to_farrow = mysql_fetch_array(mysql_query("SELECT count(breeding_id) from tbl_breeding_pig where (breeding_date >= DATE_ADD('$start_date', INTERVAL '$expected_days_to_farrow' DAY) and breeding_date <= DATE_ADD(LAST_DAY('$start_date'), INTERVAL '$expected_days_to_farrow' DAY) ) and company_id='$company_id' and branch_id='$branch_id' "));

	$farrowing_rate = ($farrowing_row['total_farrowed']/$total_matings_to_farrow[0]) * 100;

	// fcr, adg
	$finisher_dr = mysql_fetch_array(mysql_query("SELECT AVG(swine_FCR) as fcr, AVG(swine_ADG) as finisher_adg from tbl_swine where company_id = '$company_id' and branch_id = '$branch_id' and MONTH(separation_date)=MONTH('$date') and YEAR(separation_date) = YEAR('$date') and delivery_status = 1 and (swine_type='Finisher' or swine_type='Grower') "));

	// gilt adg
	$gilt_adg = mysql_fetch_array(mysql_query("SELECT AVG(swine_ADG) from tbl_swine where company_id = '$company_id' and branch_id = '$branch_id' and MONTH(separation_date)=MONTH('$date') and YEAR(separation_date) = YEAR('$date') and delivery_status = '1' and swine_type='Gilt'"));

	// gilt age sold
	$gilt_age_sold = mysql_fetch_array(mysql_query("SELECT AVG(swine_age) from tbl_dr_header as dh, tbl_dr_detail as drd, tbl_swine as sw, tbl_pen_assignment as pa where MONTH(dh.dr_date)=MONTH('$date') and YEAR(dh.dr_date) = YEAR('$date') and  dh.company_id='$company_id' and dh.branch_id='$branch_id' and (dh.status='P' or dh.status='F') and dh.delivery_number=drd.delivery_number and drd.swine_id != 0 and drd.swine_id=sw.swine_id and sw.company_id='$company_id' and sw.swine_type = 'Gilt'  and sw.delivery_status = 1 "));

	// gilts mated
	$gilts_mated = mysql_fetch_array(mysql_query("SELECT count(breeding_id) from tbl_breeding_pig where company_id='$company_id' and branch_id='$branch_id' and swine_type='G' and MONTH(breeding_date) = MONTH('$date') and YEAR(breeding_date) = YEAR('$date') "));

	// live wt price
	$live_weight_price = ($dr_swine_row['total_price_sold']/$dr_swine_row['total_weight_sold']);

	// mortality sow/gilt
	$mortality_sow_and_gilt = mysql_fetch_array(mysql_query("SELECT count(mortality_id) from tbl_mortality_pig as mort, tbl_swine as sw where mort.company_id='$company_id' and mort.branch_id='$branch_id' and mort.swine_id=sw.swine_id and (sw.swine_type='Gilt' or sw.swine_type='Sow') and MONTH(mort.date_added) = MONTH('$date') and YEAR(mort.date_added) = YEAR('$date') and mort.status!='C' "));

	// mummified
	$mummified_count = mysql_fetch_array(mysql_query("SELECT count(mortality_id) from  tbl_mortality_farrow where company_id='$company_id' and branch_id='$branch_id' and MONTH(date_added)=MONTH('$date') and YEAR(date_added)=YEAR('$date') and status= '0'"));

	// mummified percentage
	$mummified_percentage = ($mummified_count[0]/$farrowing_row['total_litter_size']) * 100;

	// nip
	$not_in_pig = mysql_fetch_array(mysql_query("SELECT count(nip_id) from tbl_nip_pig as np, tbl_breeding_pig as bp where np.company_id='$company_id' and np.branch_id='$branch_id' and np.breeding_id=bp.breeding_id and MONTH(np.date_added)=MONTH('$date') and YEAR(np.date_added)=YEAR('$date') "));

	// nip percentage
	$nip_percentage = ($not_in_pig[0]/$matings_count[0]) * 100;

	// post wean mortality
	$post_wean_mortality = mysql_fetch_array(mysql_query("SELECT count(mortality_id) from tbl_mortality_pig as mp, tbl_swine as sw where mp.company_id = '$company_id' and mp.branch_id ='$branch_id' and MONTH(mp.date_added)=MONTH('$date') and YEAR(mp.date_added) = YEAR('$date') and mp.wean_categ = 'PO' and mp.status !='C' and mp.swine_id=sw.swine_id and sw.swine_origin='Raised' and (sw.swine_type = 'Weaner' or sw.swine_type = 'Grower' or sw.swine_type = 'Finisher' ) and sw.status = 0 and sw.mortality_status = 1 "));

	// post wean mortality percentage
	$post_wean_mortality_percentage = ($post_wean_mortality[0]/$farrowing_row['total_born_alive']) * 100;

	// pre wean mortality
	$pre_wean_mortality = mysql_fetch_array(mysql_query("SELECT count(mortality_id) from tbl_mortality_pig where company_id = '$company_id' and branch_id ='$branch_id' and MONTH(date_added)=MONTH('$date') and YEAR(date_added) = YEAR('$date') and wean_categ = 'PR' and status!='C' "));

	// pre wean mortality percentage
	$pre_wean_mortality_percentage = ($pre_wean_mortality[0]/$farrowing_row['total_born_alive']) * 100;

	// rebred gilts
	$rebred_gilts = mysql_fetch_array(mysql_query("SELECT count(breeding_failed_id) from tbl_breeding_failed_pig as bfp, tbl_breeding_pig as bp where bfp.breeding_id=bp.breeding_id and bfp.company_id='$company_id' and bfp.branch_id='$branch_id' and MONTH(bp.breeding_date) = MONTH('$date') and YEAR(bp.breeding_date) = YEAR('$date') and bp.swine_type='G' "));

	// rebred sows
	$rebred_sows = mysql_fetch_array(mysql_query("SELECT count(breeding_failed_id) from tbl_breeding_failed_pig as bfp, tbl_breeding_pig as bp where bfp.breeding_id=bp.breeding_id and bfp.company_id='$company_id' and bfp.branch_id='$branch_id' and MONTH(bp.breeding_date) = MONTH('$date') and YEAR(bp.breeding_date) = YEAR('$date') and bp.swine_type='S' "));

	$total_rebred = $rebred_gilts[0] + $rebred_sows[0];

	// rebred percentage
	$rebred_percentage = ($total_rebred/$matings_count[0]) * 100;

	// sow index
	$sow_index = 365 / ($farrowing_row['ave_farrowing_interval']/$farrowing_row['total_farrowed']);

	// stillbirth
	$still_birth_count = mysql_fetch_array(mysql_query("SELECT count(mortality_id) from  tbl_mortality_farrow where company_id='$company_id' and branch_id='$branch_id' and MONTH(date_added)= MONTH('$date') and YEAR(date_added)=YEAR('$date') and status= '-1'"));

	// percentage stillbirth
	$still_birth_percentage = ($still_birth_count[0]/$farrowing_row['total_litter_size']) * 100;

	// total number of weaned piglets
	$weaned_piglets = mysql_fetch_array(mysql_query("SELECT count(swine_id) as total_number_of_weaned_piglets, sum(weaning_wt) as total_weaning_wt from tbl_swine where company_id='$company_id' and branch_id='$branch_id' and MONTH(weaning_date)=MONTH('$date') and YEAR(weaning_date)=YEAR('$date') "));

	// total tons sold
	/*$total_sw_wt = 0;

	$fetch_sw_weight = mysql_query("SELECT sum(swine_weight) FROM tbl_dr_detail as dr, tbl_swine as sw, tbl_dr_header as dh WHERE dh.company_id='$company_id' and dh.branch_id='$branch_id' and MONTH(dh.dr_date)=MONTH('$date') and YEAR(dh.dr_date)=YEAR('$date') and dh.delivery_number=dr.delivery_number and dr.swine_id != 0 and sw.swine_id=dr.swine_id ORDER BY sw.farrowing_id ");

	while($sw_wt = mysql_fetch_array($fetch_sw_weight)){
		$total_sw_wt += $sw_wt[0];
	}*/

	$total_weight_sold_of_swine_with_parents = mysql_fetch_array(mysql_query("SELECT sum(swine_weight) from tbl_dr_header as dh, tbl_dr_detail as dd, tbl_swine as sw where dh.company_id='$company_id' and dh.branch_id='$branch_id' and MONTH(dh.dr_date)=MONTH('$date') and YEAR(dh.dr_date)=YEAR('$date') and dh.delivery_number=dd.delivery_number and dd.swine_id=sw.swine_id and sw.farrowing_id != 0 "));

	$total_sow_with_piglet_sold = mysql_fetch_array(mysql_query("SELECT count(DISTINCT(sw.dam)) from tbl_dr_header as dh, tbl_dr_detail as dd, tbl_swine as sw where dh.company_id='$company_id' and dh.branch_id='$branch_id' and MONTH(dh.dr_date)=MONTH('$date') and YEAR(dh.dr_date)=YEAR('$date') and dh.delivery_number=dd.delivery_number and dd.swine_id=sw.swine_id and sw.farrowing_id != 0 "));

	$total_tons_sold = ($total_sw_wt / $swine_counter_row['total_sows']) / 1000;

	// percentage undersize
	$under_size_percentage = ($farrowing_row['total_undersize']/$farrowing_row['total_born_alive']) * 100;


	$values = array($farrowing_row['total_abnormal'], $abnormal_percentage, $abortion[0], $abortion_percentage, $ave_age_gilt_mated[0], $ave_age_sold[0], $farrowing_row['ave_birth_wt'], $farrowing_row['ave_born_alive'], $ave_dry_days[0], $farrowing_row['ave_farrowing_interval'], $farrowing_row['ave_litter_size'], $farrowing_row['ave_gestation_days'], $med_cost_for_fatteners['ave_med_cost_for_fatteners'], $med_cost_for_gilts['ave_med_cost_for_gilts'], $med_cost_for_piglets['ave_med_cost_for_piglets'], $med_cost_for_sows['ave_med_cost_for_sows'], $weaning_row['ave_number_of_weaned_piglets'], $vacc_cost_for_fatteners['ave_vacc_cost_for_fatteners'], $vacc_cost_for_gilts['ave_vacc_cost_for_gilts'], $vacc_cost_for_piglets['ave_vacc_cost_for_piglets'], $vacc_cost_for_sows['ave_vacc_cost_for_sows'], $weaning_row['ave_days_weaned'], $weaning_row['ave_weaning_wt'], $$dr_swine_row['ave_weight_sold'], $breeding_rate, $carcass_recovery_wt[0], $carcass_swine[0], $confirmed_to_farrow[0], $conception_rate, $cost_to_produce, $culled_sows[0], $swine_counter_row['total_dry_sows'], $dry_sows_mated[0], $expected_to_farrow[0], $farrowing_rate, $finisher_dr['fcr'], $finisher_dr['finisher_adg'], $swine_counter_row['total_gilts'], $gilt_adg[0], $gilt_age_sold[0], $gilts_mated[0], $swine_counter_row['total_junior_boars'], $swine_counter_row['total_lactating'], $farrowing_row['total_litter_size'], $live_weight_price, $med_cost_for_fatteners['total_med_cost_for_fatteners'], $med_cost_for_gilts['total_med_cost_for_gilts'], $med_cost_for_piglets['total_med_cost_for_piglets'], $med_cost_for_sows['total_med_cost_for_sows'], $mortality_sow_and_gilt[0], $mummified_count[0], $mummified_percentage, $not_in_pig[0], $nip_percentage, $post_wean_mortality[0], $post_wean_mortality_percentage, $pre_wean_mortality[0], $pre_wean_mortality_percentage, $swine_counter_row['total_pregnant'], $rebred_gilts[0], $rebred_sows[0], $rebred_percentage, $swine_counter_row['total_senior_boars'], $sow_index, $swine_counter_row['total_sows'], $still_birth_count[0], $still_birth_percentage, $farrowing_row['total_born_alive'], $farrowing_row['total_farrowed'], $dr_swine_row['total_fatteners_sold'], $farrowing_row['total_litter_birth_wt'], $matings_count[0], $swine_counter_row['total_piglets'], $weaned_piglets['total_number_of_weaned_piglets'], $swine_counter_row['total_population'], $total_rebred, $total_tons_sold, $weaned_piglets['total_weaning_wt'], $dr_swine_row['total_weight_sold'], $farrowing_row['total_undersize'], $under_size_percentage, $vacc_cost_for_fatteners['total_vacc_cost_for_fatteners'], $vacc_cost_for_gilts['total_vacc_cost_for_gilts'], $vacc_cost_for_piglets['total_vacc_cost_for_piglets'], $vacc_cost_for_sows['total_vacc_cost_for_sows']);

	return $values;
}

function farmStatisticsAlowZero(){
	$values = array('Y', 'Y', 'Y', 'Y', 'Average Age Gilt Mated', 'Average Age Sold', 'Average Birth Wt', 'Average Born Alive', 'Average Dry Days', 'Average Farrowing Interval', 'Average Litter Size', 'Average Gestation Days', 'Average Medication Cost for Fatteners', 'Average Medication Cost for Gilts', 'Average Medication Cost for Piglets', 'Average Medication Cost for Sows', 'Average No. of Weaned Piglets', 'Average Vaccination Cost for Fatteners', 'Average Vaccination Cost for Gilts', 'Average Vaccination Cost for Piglets', 'Average Vaccination Cost for Sows', 'Average Weaning Days', 'Average Weaning Wt', 'Average Wt. Sold', 'Breeding Rate', 'Carcass Recovery Wt', 'Carcass Swine', 'Confirmed to Farrow', 'Conception Rate', 'CTP', 'Culled Sows', 'Dry Sows', 'Dry Sows Mated', 'Expected to Farrow', 'Farrowing Rate', 'FCR', 'Finisher ADG', 'Gilts', 'Gilt ADG', 'Gilt Age Sold', 'Gilts Mated', 'Junior Boars', 'Lactating', 'Litter Size', 'Live Wt. Price per kg', 'Medication Cost for Fatteners', 'Medication Cost for Gilts', 'Medication Cost for Piglets', 'Medication Cost for Sows', 'Mortality Sow/Gilt', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Pregnant', 'Rebred Gilts', 'Rebred Sows', 'Rebred %', 'Senior Boars', 'Sow Index', 'Sow Level', 'Y', 'Y', 'Total Born Alive', 'Total Farrowed', 'Total Fatteners Sold', 'Total Litter Birth Wt', 'Total Matings', 'Total No. of Piglets', 'Total No. of Weaned Piglets', 'Total Population', 'Total Rebred', 'Total Tons sold', 'Total Weaning Wt', 'Total Wt. Sold', 'Y', 'Y', 'Vaccination Cost for Fatteners', 'Vaccination Cost for Gilts', 'Vaccination Cost for Piglets', 'Vaccination Cost for Sows');

	return $values;
}

function addFarmStatisticsPerRegion(){

	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
	$region = getRegion($branch_id);
	$date = date('Y-m-d', strtotime(getCurrentDate()));
	$Cal = new StringCalculator();

	$stat_date_reference = date('m', strtotime(getCurrentDate()));
	$stat_year = date('Y', strtotime(getCurrentDate()));

	if($branch_id == 0){

	}else{

		// max date recorded
		$sc_max_day_of_month_recorded = mysql_fetch_array(mysql_query("SELECT max(date_modified) from tbl_swine_counter where MONTH(date_modified)=MONTH('$date') and YEAR(date_modified) = YEAR('$date') and company_id='$company_id' and branch_id='$branch_id' "));
		$max_day_of_month_recorded = $sc_max_day_of_month_recorded[0];

		// expected to farrow standard
		$expected_days_to_farrow_standard = mysql_fetch_array(mysql_query("SELECT expected_due_days FROM tbl_alert_parameters WHERE company_id='$company_id'"));
		$expected_days_to_farrow_standard = $expected_days_to_farrow_standard[0];
		$start_date = date('Y', strtotime($date)).'-'.date('m', strtotime($date)).'-01';
		$expected_days_to_farrow = $expected_days_to_farrow_standard[0] * -1;

		// weaning wt standard
		$weaning_weight_standard = mysql_fetch_array(mysql_query("SELECT * FROM tbl_weaning_wt_standards WHERE company_id='$company_id'"));
		$weaning_wt_standard_a_start = $weaning_weight_standard['start_range_a'];
		$weaning_wt_standard_a_end = $weaning_weight_standard['end_range_a'];
		$weaning_wt_standard_b_start = $weaning_weight_standard['start_range_b'];
		$weaning_wt_standard_b_end = $weaning_weight_standard['end_range_b'];
		$weaning_wt_standard_c_start = $weaning_weight_standard['start_range_c'];
		$weaning_wt_standard_c_end = $weaning_weight_standard['end_range_c'];

		// standard ADJ
		$fetch_adj_standard = mysql_query("SELECT * FROM tbl_farm_statistics_standard_adj_pig WHERE company_id='$company_id' ");
		$adj_standard = mysql_fetch_array($fetch_adj_standard);

		array($adj_at_28=$adj_standard['adj_28'], $adj_at_42=$adj_standard['adj_42'], $adj_at_56=$adj_standard['adj_56'], $adj_at_70=$adj_standard['adj_70'], $adj_at_84=$adj_standard['adj_84'], $adj_at_98=$adj_standard['adj_98'], $adj_at_112=$adj_standard['adj_112'], $adj_at_126=$adj_standard['adj_126'], $adj_at_140=$adj_standard['adj_140'], $adj_at_150=$adj_standard['adj_150'], $adj_at_165=$adj_standard['adj_165'], $adj_at_180=$adj_standard['adj_180'], $adj_at_195=$adj_standard['adj_195'], $adj_at_210=$adj_standard['adj_210'], $adj_at_225=$adj_standard['adj_225'], $adj_at_240=$adj_standard['adj_240']);

		$reference_range_query = 'MONTH';


		// fetch farm statistics
		$fetch_farm_statistics = mysql_query("SELECT * from tbl_farm_statistics where company_id='$company_id' and farm_statistics_type != 'header' ") or die(mysql_error());
		$farm_statistics = array();
		while($farm_statistics_row = mysql_fetch_array($fetch_farm_statistics)){
			$list = array();
			$list['farm_statistics'] = $farm_statistics_row['farm_statistics'];
			$list['farm_statistics_query1'] = $farm_statistics_row['farm_statistics_query1'];
			$list['farm_statistics_query2'] = $farm_statistics_row['farm_statistics_query2'];
			$list['farm_statistics_query3'] = $farm_statistics_row['farm_statistics_query3'];
			$list['farm_statistics_query4'] = $farm_statistics_row['farm_statistics_query4'];
			$list['allow_zero'] = $farm_statistics_row['allow_zero'];
			$list['farm_statistics_formula'] = $farm_statistics_row['farm_statistics_formula'];

			// calculate farm statistics value

			// calculate farm statistics value
			if($farm_statistics_row['farm_statistics_query1'] != ""){
				$fetch_query1 = $farm_statistics_row['farm_statistics_query1'];
				eval("\$fetch_query1 = \"$fetch_query1\";");
				$fq1 = mysql_query($fetch_query1) or die(mysql_error());
				if(mysql_num_rows($fq1) > 0){
					$query1_row = mysql_fetch_array($fq1) or die(mysql_error());
					$query1 = $query1_row[0] * 1;
				}else{
					$query1 = 0;
				}
			}

			if($farm_statistics_row['farm_statistics_query2'] != ""){
				$fetch_query2 = $farm_statistics_row['farm_statistics_query2'];
				eval("\$fetch_query2 = \"$fetch_query2\";");
				$fq2 = mysql_query($fetch_query2) or die(mysql_error());
				if(mysql_num_rows($fq2) > 0){
					$query2_row = mysql_fetch_array($fq2) or die(mysql_error());
					$query2 = $query2_row[0] * 1;
				}else{
					$query2 = 0;
				}
			}

			if($farm_statistics_row['farm_statistics_query3'] != ""){
				$fetch_query3 = $farm_statistics_row['farm_statistics_query3'];
				eval("\$fetch_query3 = \"$fetch_query3\";");
				$fq3 = mysql_query($fetch_query3) or die(mysql_error());
				if(mysql_num_rows($fq3) > 0){
					$query3_row = mysql_fetch_array($fq3) or die(mysql_error());
					$query3 = $query3_row[0] * 1;
				}else{
					$query3 = 0;
				}
			}

			if($farm_statistics_row['farm_statistics_query4'] != ""){
				$fetch_query4 = $farm_statistics_row['farm_statistics_query4'];
				eval("\$fetch_query4 = \"$fetch_query4\";");
				$fq4 = mysql_query($fetch_query4) or die(mysql_error());
				if(mysql_num_rows($fq4) > 0){
					$query4_row = mysql_fetch_array($fq4) or die(mysql_error());
					$query4 = $query4_row[0] * 1;
				}else{
					$query4 = 0;
				}
			}

			$farm_statistics_denominator1 = $farm_statistics_row['farm_statistics_denominator1'];
			$farm_statistics_denominator2 = $farm_statistics_row['farm_statistics_denominator2'];

			if($farm_statistics_row['farm_statistics_denominator1'] != ''){

				eval("\$farm_statistics_denominator1 = \"$farm_statistics_denominator1\";");
				if($farm_statistics_denominator1 == 0){
					$value = 0;
				}else{
					$ave_age_sold = $farm_statistics_row['farm_statistics_denominator1'];
					eval("\$ave_age_sold = \"$ave_age_sold\";");
					if($ave_age_sold <= 28){
						array($operators="+", $age_28=$ave_age_sold, $age_42=28, $age_56=42, $age_70=56, $age_84=70, $age_98=84, $age_112=98, $age_126=112, $age_140=126, $age_150=140, $age_165=150, $age_180=165, $age_195=180, $age_210=195, $age_225=210, $age_240=225);
					}else if($ave_age_sold <= 42){
						array($operators="+", $age_28=28, $age_42=$ave_age_sold, $age_56=42, $age_70=56, $age_84=70, $age_98=84, $age_112=98, $age_126=112, $age_140=126, $age_150=140, $age_165=150, $age_180=165, $age_195=180, $age_210=195, $age_225=210, $age_240=225);
					}else if($ave_age_sold <= 56){
						array($operators="+", $age_28=28, $age_42=42, $age_56=$ave_age_sold, $age_70=56, $age_84=70, $age_98=84, $age_112=98, $age_126=112, $age_140=126, $age_150=140, $age_165=150, $age_180=165, $age_195=180, $age_210=195, $age_225=210, $age_240=225);
					}else if($ave_age_sold <= 70){
						array($operators="+", $age_28=28, $age_42=42, $age_56=56, $age_70=$ave_age_sold, $age_84=70, $age_98=84, $age_112=98, $age_126=112, $age_140=126, $age_150=140, $age_165=150, $age_180=165, $age_195=180, $age_210=195, $age_225=210, $age_240=225);
					}else if($ave_age_sold <= 84){
						array($operators="+", $age_28=28, $age_42=42, $age_56=56, $age_70=70, $age_84=$ave_age_sold, $age_98=84, $age_112=98, $age_126=112, $age_140=126, $age_150=140, $age_165=150, $age_180=165, $age_195=180, $age_210=195, $age_225=210, $age_240=225);
					}else if($ave_age_sold <= 98){
						array($operators="+", $age_28=28, $age_42=42, $age_56=56, $age_70=70, $age_84=84, $age_98=$ave_age_sold, $age_112=98, $age_126=112, $age_140=126, $age_150=140, $age_165=150, $age_180=165, $age_195=180, $age_210=195, $age_225=210, $age_240=225);
					}else if($ave_age_sold <= 112){
						array($operators="+", $age_28=28, $age_42=42, $age_56=56, $age_70=70, $age_84=84, $age_98=98, $age_112=$ave_age_sold, $age_126=112, $age_140=126, $age_150=140, $age_165=150, $age_180=165, $age_195=180, $age_210=195, $age_225=210, $age_240=225);
					}else if($ave_age_sold <= 126){
						array($operators="+", $age_28=28, $age_42=42, $age_56=56, $age_70=70, $age_84=84, $age_98=98, $age_112=112, $age_126=$ave_age_sold, $age_140=126, $age_150=140, $age_165=150, $age_180=165, $age_195=180, $age_210=195, $age_225=210, $age_240=225);
					}else if($ave_age_sold <= 140){
						array($operators="+", $age_28=28, $age_42=42, $age_56=56, $age_70=70, $age_84=84, $age_98=98, $age_112=112, $age_126=126, $age_140=$ave_age_sold, $age_150=140, $age_165=150, $age_180=165, $age_195=180, $age_210=195, $age_225=210, $age_240=225);
					}else if($ave_age_sold <= 150){
						array($operators="+", $age_28=28, $age_42=42, $age_56=56, $age_70=70, $age_84=84, $age_98=98, $age_112=112, $age_126=126, $age_140=140, $age_150=$ave_age_sold, $age_165=150, $age_180=165, $age_195=180, $age_210=195, $age_225=210, $age_240=225);
					}else if($ave_age_sold <= 165){
						array($operators="-", $age_28=28, $age_42=42, $age_56=56, $age_70=70, $age_84=84, $age_98=98, $age_112=112, $age_126=126, $age_140=140, $age_150=150, $age_165=$ave_age_sold, $age_180=165, $age_195=180, $age_210=195, $age_225=210, $age_240=225);
					}else if($ave_age_sold <= 180){
						array($operators="-", $age_28=28, $age_42=42, $age_56=56, $age_70=70, $age_84=84, $age_98=98, $age_112=112, $age_126=126, $age_140=140, $age_150=150, $age_165=165, $age_180=$ave_age_sold, $age_195=180, $age_210=195, $age_225=210, $age_240=225);
					}else if($ave_age_sold <= 195){
						array($operators="-", $age_28=28, $age_42=42, $age_56  = 56, $age_70  = 70, $age_84=84, $age_98=98, $age_112=112, $age_126=126, $age_140=140, $age_150=150, $age_165=165, $age_180=180, $age_195=$ave_age_sold, $age_210=195, $age_225=210, $age_240=225);
					}else if($ave_age_sold <= 210){
						array($operators="-", $age_28=28, $age_42=42, $age_56  = 56, $age_70  = 70, $age_84=84, $age_98=98, $age_112=112, $age_126=126, $age_140=140, $age_150=150, $age_165=165, $age_180=180, $age_195=195, $age_210=$ave_age_sold, $age_225=210, $age_240=225);
					}else if($ave_age_sold <= 225){
						array($operators="-", $age_28=28, $age_42=42, $age_56  = 56, $age_70  = 70, $age_84=84, $age_98=98, $age_112=112, $age_126=126, $age_140=140, $age_150=150, $age_165=165, $age_180=180, $age_195=195, $age_210=210, $age_225=$ave_age_sold, $age_240=225);
					}else if($ave_age_sold <= 240){
						array($operators="-", $age_28=28, $age_42=42, $age_56  = 56, $age_70  = 70, $age_84=84, $age_98=98, $age_112=112, $age_126=126, $age_140=140, $age_150=150, $age_165=165, $age_180=180, $age_195=195, $age_210=210, $age_225=225, $age_240=$ave_age_sold);
					}

					if($ave_age_sold < 0 OR $ave_age_sold > 240){
						$value = 0;
					}else{
						$formula = $farm_statistics_row['farm_statistics_formula'];
						eval("\$formula = \"$formula\";");
						$value = $Cal->calculate($formula);
					}
				}
			}else if($farm_statistics_row['farm_statistics_denominator2'] != ''){

				eval("\$farm_statistics_denominator2 = \"$farm_statistics_denominator2\";");
				if($farm_statistics_denominator2 == 0){
					$value = 0;
				}else{
					$formula = $farm_statistics_row['farm_statistics_formula'];
					eval("\$formula = \"$formula\";");
					$value = $Cal->calculate($formula);
				}
			}else{
				$formula = $farm_statistics_row['farm_statistics_formula'];
				eval("\$formula = \"$formula\";");
				$value = $Cal->calculate($formula);
			}

			$result = $value * 1;


			/*$formula = $farm_statistics_row['farm_statistics_formula'];
			eval("\$formula = \"$formula\";");
			$result = $Cal->calculate($formula);*/

			$list['farm_statistics_value'] = $result;


			array_push($farm_statistics, $list);
		}

		//echo json_encode($farm_statistics);


		$main_db = $GLOBALS['local_config']['mysql']['orig_db_main'];
		mysql_query("USE $main_db") or die(mysql_error());

		// insert to regional farm statistics
		foreach ($farm_statistics as $value) {

			$farm_statistics = $value['farm_statistics'];
			$statistics_value = $value['farm_statistics_value'];


			$regional_farm_stats_row = mysql_fetch_array(mysql_query("SELECT count(id), lowest_value, highest_value from tbl_farm_statistics_per_region where region='$region' and farm_statistics='$farm_statistics' and MONTH(date_added)=MONTH('$date') and YEAR(date_added)=YEAR('$date') "));

			if($value['allow_zero'] == 1){
				$allow_query = 'Y';
			}else{
				if($statistics_value > 0){
					$allow_query = 'Y';
				}else{
					$allow_query = 'N';
				}
			}

			if($allow_query == 'Y'){
				if($regional_farm_stats_row[0] > 0){
					// set highest value
					if($statistics_value > $regional_farm_stats_row['highest_value']){
						mysql_query("UPDATE tbl_farm_statistics_per_region set highest_value='$statistics_value', date_added='$date' where region='$region' and MONTH(date_added)=MONTH('$date') and YEAR(date_added)=YEAR('$date') and farm_statistics='$farm_statistics' ") or die(mysql_error());
					}

					if($statistics_value < $regional_farm_stats_row['lowest_value'] or ( $regional_farm_stats_row['lowest_value'] == 0 AND $regional_farm_stats_row['highest_value'] > $statistics_value) ){
						mysql_query("UPDATE tbl_farm_statistics_per_region set lowest_value='$statistics_value', date_added='$date' where region='$region' and MONTH(date_added)=MONTH('$date') and YEAR(date_added)=YEAR('$date') and farm_statistics='$farm_statistics' ") or die(mysql_error());
					}
				
				}else{
					// insert new
					mysql_query("INSERT INTO tbl_farm_statistics_per_region (farm_statistics, region, lowest_value, highest_value, date_added) VALUES ('$farm_statistics', '$region', '$statistics_value', '$statistics_value', '$date')") or die(mysql_error());
				}
			}
		} // end for loop for fs

		$database = $GLOBALS['config']['mysql']['db_prefix'].getCompanyCode($company_id);
		mysql_query("USE $database ") or die(mysql_error());
	}

}


// end farm stats saver


// get average dry days per pen
function average_dry_days_per_pen($pen_assignment_id){

	$company_id = $_SESSION['system']['company_id'];
	$date_today = getCurrentDate();

	// check if has sow
	$fetch_sow = mysql_query("SELECT swine_id from tbl_swine where company_id='$company_id' and pen_code='$pen_assignment_id' and delivery_status=0 and cull_status=0 and mortality_status=0 and (swine_type = 'Gilt' or swine_type = 'Sow') and pregnant_status = 0 ") or die(mysql_error());

	$total_dry_days = 0;
	$swine_dry_days_counter = 0;

	if(mysql_num_rows($fetch_sow) > 0){
		while($sow_row = mysql_fetch_array($fetch_sow)){
			/*$fetch_farrowing = mysql_query("SELECT weaning_date, date_farrowed, date_separated, weaning_sched, breeding_id from tbl_farrowing_pig where company_id='$company_id' and swine_id='$sow_row[swine_id]' and status=0 ORDER BY farrowing_id DESC LIMIT 1 ") or die(mysql_error());

			if(mysql_num_rows($fetch_farrowing) > 0){
				$fetch_days = mysql_fetch_array($fetch_farrowing);

				if($fetch_days['weaning_date'] != '0000-00-00'){
					$dry_days_current = daysDifference(getCurrentDate(), $fetch_days['weaning_date']);
				}else{
					$dry_days_current = daysDifference(getCurrentDate(), $fetch_days['date_separated']);
				}

				$fetch_breeding_failed_id=mysql_query("SELECT breeding_id from tbl_breeding_failed_pig where swine_id='$sow_row[swine_id]' and company_id='$company_id' and next_success_breeding_id = '$fetch_days[breeding_id]' ") or die(mysql_error());
				$sum_dd = 0;

				while ($row_failed = mysql_fetch_array($fetch_breeding_failed_id)) {
					$dd = mysql_fetch_array(mysql_query("SELECT dry_days from tbl_breeding_pig where swine_id='$sow_row[swine_id]' and company_id='$company_id' and breeding_id = '$row_failed[breeding_id]' ")) or die(mysql_error());

					// echo "\n"."breeding_id".$row_failed['breeding_id']."dry_days ".$dd[0]."\n";
					$sum_dd+=$dd[0];
				}

				// fetch additional dry days from tbl_dry days
				$fetch_adoption_period = mysql_fetch_array(mysql_query("SELECT sum(DATEDIFF(end_date, start_date)) from tbl_dry_days where company_id='$company_id' and swine_id='$sow_row[swine_id]' and is_recorded=0 and dd_action='A' "));
				$fetch_begining_dd_period = mysql_fetch_array(mysql_query("SELECT sum(DATEDIFF('$date_today', start_date)) from tbl_dry_days where company_id='$company_id' and swine_id='$sow_row[swine_id]' and is_recorded=0 and dd_action='B' "));

				$total_dry_days += $dry_days_current + $sum_dd + $fetch_begining_dd_period[0] - $fetch_adoption_period[0];

			}else{
				// fetch additional dry days from tbl_dry days
				$fetch_adoption_period = mysql_fetch_array(mysql_query("SELECT sum(DATEDIFF(end_date, start_date)) from tbl_dry_days where company_id='$company_id' and swine_id='$sow_row[swine_id]' and is_recorded=0 and dd_action='A' "));
				$fetch_begining_dd_period = mysql_fetch_array(mysql_query("SELECT sum(DATEDIFF('$date_today', start_date)) from tbl_dry_days where company_id='$company_id' and swine_id='$sow_row[swine_id]' and is_recorded=0 and dd_action='B' "));
				$total_dry_days += $fetch_begining_dd_period[0] - $fetch_adoption_period[0];
			}*/

			$fetch_breeding = mysql_query("SELECT * from tbl_breeding_pig where company_id='$company_id' and swine_id='$sow_row[swine_id]' ORDER BY breeding_date DESC LIMIT 1 ") or die(mysql_error());

			$dry_days_count = 0;

			if(mysql_num_rows($fetch_breeding) > 0){
				$breeding_row = mysql_fetch_array($fetch_breeding);

				$count_dry_days = mysql_fetch_array(mysql_query("SELECT start_date from tbl_dry_days where swine_id='$sow_row[swine_id]' and is_recorded=0 and dd_action!='A'"));
				$dry_days = daysDifference(getCurrentDate(),$count_dry_days[0]);

			}else{
				// check bb dry days
				$dry_days = 0;
			}

			// fetch additional dry days from tbl_dry days
			$fetch_adoption_period = mysql_fetch_array(mysql_query("SELECT sum(DATEDIFF(end_date, start_date)) from tbl_dry_days where company_id='$company_id' and swine_id='$sow_row[swine_id]' and is_recorded=0 and dd_action='A' "));

			$dry_days_count = $dry_days + $sum_failed_dd - $fetch_adoption_period[0];

			$total_dry_days += $dry_days_count;

			if($dry_days_count > 0){
				$swine_dry_days_counter = $swine_dry_days_counter + 1;
			}

		}


		$dry_days = $total_dry_days/$swine_dry_days_counter;


	}else{
		$dry_days = 0;
	}

	return $dry_days;
}

function average_pregnancy_days_per_pen($pen_assignment_id){
	$company_id = $_SESSION['system']['company_id'];
	$date_today = getCurrentDate();

	// check if has sow
	$fetch_sow = mysql_query("SELECT swine_id from tbl_swine where company_id='$company_id' and pen_code='$pen_assignment_id' and delivery_status=0 and cull_status=0 and mortality_status=0 and (swine_type = 'Gilt' or swine_type = 'Sow') and pregnant_status = 1 ") or die(mysql_error());

	$total_days_pregnant = 0;

	if(mysql_num_rows($fetch_sow) > 0){

		while($sow_row = mysql_fetch_array($fetch_sow)){
			$days_pregnant = mysql_fetch_array(mysql_query("SELECT DATEDIFF('$date_today', breeding_date) from tbl_breeding_pig where swine_id='$sow_row[swine_id]' and company_id='$company_id' and farr_status=0 ORDER BY breeding_date DESC LIMIT 1 "));
			$total_days_pregnant += $days_pregnant[0];
		}

		$average_days_pregnant = $total_days_pregnant/mysql_num_rows($fetch_sow);
	}else{
		$average_days_pregnant = 0;
	}

	return $average_days_pregnant;

}


function archive_swine($swine_id){
	$company_id = $_SESSION['system']['company_id'];

	// count if existing
	$count_row = mysql_fetch_array(mysql_query("SELECT count(swine_id) from tbl_swine_archive where swine_id='$swine_id' and company_id='$company_id' "));

	if($count_row[0] == 0){

		$swine_row = mysql_fetch_array(mysql_query("SELECT * from tbl_swine where swine_id='$swine_id' and company_id='$company_id' "));
		
		$sql = mysql_query("INSERT INTO `tbl_swine_archive` (`swine_id`, `company_id`, `branch_id`, `swine_type`, `gender`, `swine_code`, `ear_tag_counter`, `swine_breed`, `genetic_line`, `swine_birthdate`, `swine_origin`, `swine_parity`, `sire`, `dam`, `weight`, `weaning_wt`, `weight_at_70`, `price`, `accumulated_piglet_cost`, `swine_class`, `swine_comment`, `pen_code`, `times_rebreed`, `abortion`, `date_assigned`, `status`, `rr_no`, `mortality_status`, `cull_status`, `delivery_status`, `pregnant_status`, `alert_parameter_status`, `alert_temp_off`, `swine_condition`, `farrowing_id`, `swine_ADG`, `swine_FCR`, `weaning_date`, `separation_date`, `isBB`, `date_added`, `alert_litter_size`, total_cost, market_weight, market_price) VALUES ('$swine_row[swine_id]' , '$swine_row[company_id]', '$swine_row[branch_id]', '$swine_row[swine_type]', '$swine_row[gender]', '$swine_row[swine_code]', '$swine_row[ear_tag_counter]', '$swine_row[swine_breed]', '$swine_row[genetic_line]', '$swine_row[swine_birthdate]', '$swine_row[swine_origin]', '$swine_row[swine_parity]', '$swine_row[sire]', '$swine_row[dam]', '$swine_row[weight]', '$swine_row[weaning_wt]', '$swine_row[weight_at_70]', '$swine_row[price]', '$swine_row[accumulated_piglet_cost]', '$swine_row[swine_class]', '$swine_row[swine_comment]', '$swine_row[pen_code]', '$swine_row[times_rebreed]' , '$swine_row[abortion]', '$swine_row[date_assigned]', '$swine_row[status]', '$swine_row[rr_no]', '$swine_row[mortality_status]', '$swine_row[cull_status]', '$swine_row[delivery_status]', '$swine_row[pregnant_status]', '$swine_row[alert_parameter_status]', '$swine_row[alert_temp_off]', '$swine_row[swine_condition]', '$swine_row[farrowing_id]', '$swine_row[swine_ADG]', '$swine_row[swine_FCR]', '$swine_row[weaning_date]', '$swine_row[separation_date]', '$swine_row[isBB]', '$swine_row[date_added]', '$swine_row[alert_litter_size]', '$swine_row[total_cost]', '$swine_row[market_weight]', '$swine_row[market_price]') ") or die(mysql_error());

		if($sql){
			// delete swine
			mysql_query("DELETE from tbl_swine where swine_id='$swine_id' and company_id='$company_id' ") or die(mysql_error());

			archive_swine_consumption($swine_id);
			
		}
	}

}


function reverse_archive_swine($swine_id){
	$company_id = $_SESSION['system']['company_id'];

	// count if existing
	$count_row = mysql_fetch_array(mysql_query("SELECT count(swine_id) from tbl_swine where swine_id='$swine_id' and company_id='$company_id' "));

	if($count_row[0] == 0){

		$swine_row = mysql_fetch_array(mysql_query("SELECT * from tbl_swine_archive where swine_id='$swine_id' and company_id='$company_id' "));
		
		$sql = mysql_query("INSERT INTO `tbl_swine` (`swine_id`, `company_id`, `branch_id`, `swine_type`, `gender`, `swine_code`, `ear_tag_counter`, `swine_breed`, `genetic_line`, `swine_birthdate`, `swine_origin`, `swine_parity`, `sire`, `dam`, `weight`, `weaning_wt`, `weight_at_70`, `price`, `accumulated_piglet_cost`, `swine_class`, `swine_comment`, `pen_code`, `times_rebreed`, `abortion`, `date_assigned`, `status`, `rr_no`, `mortality_status`, `cull_status`, `delivery_status`, `pregnant_status`, `alert_parameter_status`, `alert_temp_off`, `swine_condition`, `farrowing_id`, `swine_ADG`, `swine_FCR`, `weaning_date`, `separation_date`, `isBB`, `date_added`, `alert_litter_size`) VALUES ('$swine_row[swine_id]' , '$swine_row[company_id]', '$swine_row[branch_id]', '$swine_row[swine_type]', '$swine_row[gender]', '$swine_row[swine_code]', '$swine_row[ear_tag_counter]', '$swine_row[swine_breed]', '$swine_row[genetic_line]', '$swine_row[swine_birthdate]', '$swine_row[swine_origin]', '$swine_row[swine_parity]', '$swine_row[sire]', '$swine_row[dam]', '$swine_row[weight]', '$swine_row[weaning_wt]', '$swine_row[weight_at_70]', '$swine_row[price]', '$swine_row[accumulated_piglet_cost]', '$swine_row[swine_class]', '$swine_row[swine_comment]', '$swine_row[pen_code]', '$swine_row[times_rebreed]' , '$swine_row[abortion]', '$swine_row[date_assigned]', '$swine_row[status]', '$swine_row[rr_no]', '$swine_row[mortality_status]', '$swine_row[cull_status]', '$swine_row[delivery_status]', '$swine_row[pregnant_status]', '$swine_row[alert_parameter_status]', '$swine_row[alert_temp_off]', '$swine_row[swine_condition]', '$swine_row[farrowing_id]', '$swine_row[swine_ADG]', '$swine_row[swine_FCR]', '$swine_row[weaning_date]', '$swine_row[separation_date]', '$swine_row[isBB]', '$swine_row[date_added]', '$swine_row[alert_litter_size]') ") or die(mysql_error());

		if($sql){
			// delete swine
			mysql_query("DELETE from tbl_swine_archive where swine_id='$swine_id' and company_id='$company_id' ") or die(mysql_error());

			reverse_archive_swine_consumption($swine_id);
			reverse_archive_feeding($swine_id);
			reverse_archive_medication($swine_id);
			reverse_archive_vaccination($swine_id);
			reverse_archive_ai_materials_used($swine_id);
			reverse_archive_ai_semen_used($swine_id);

		}
	}

}


function archive_swine_consumption($swine_id){
	$company_id = $_SESSION['system']['company_id'];
	
	mysql_query("UPDATE tbl_feeding SET is_archived=1 where company_id='$company_id' and swine_id='$swine_id' ");
	mysql_query("UPDATE tbl_medication_pig SET is_archived=1 where company_id='$company_id' and swine_id='$swine_id' ");
	mysql_query("UPDATE tbl_vaccine_pig SET is_archived=1 where company_id='$company_id' and swine_id='$swine_id' ");
	mysql_query("UPDATE tbl_ai_materials_pig SET is_archived=1 where company_id='$company_id' and swine_id='$swine_id' ");
	mysql_query("UPDATE tbl_ai_semen_pig SET is_archived=1 where company_id='$company_id' and swine_id='$swine_id' ");
}


function reverse_archive_swine_consumption($swine_id){
	$company_id = $_SESSION['system']['company_id'];
	
	mysql_query("UPDATE tbl_feeding SET is_archived='' where company_id='$company_id' and swine_id='$swine_id' ");
	mysql_query("UPDATE tbl_medication_pig SET is_archived='' where company_id='$company_id' and swine_id='$swine_id' ");
	mysql_query("UPDATE tbl_vaccine_pig SET is_archived='' where company_id='$company_id' and swine_id='$swine_id' ");
	mysql_query("UPDATE tbl_ai_materials_pig SET is_archived='' where company_id='$company_id' and swine_id='$swine_id' ");
	mysql_query("UPDATE tbl_ai_semen_pig SET is_archived='' where company_id='$company_id' and swine_id='$swine_id' ");
}


function createFeedingArchiveTable($date){
	$feeding_table = "tbl_feeding_".date("m_y", strtotime($date));
	$feeding_per_pen = "tbl_feeding_per_pen_".date("m_y", strtotime($date));
	mysql_query("CREATE TABLE IF NOT EXISTS $feeding_table LIKE tbl_feeding_orig");
	mysql_query("CREATE TABLE IF NOT EXISTS $feeding_per_pen LIKE tbl_feeding_per_pen_orig");
}

function archive_feeding(){

	ini_set("max_execution_time", 300);

	$date_today = date("Y-m-d", strtotime(getCurrentDate()));
	$company_id = $_SESSION["system"]["company_id"];
	$branch_id = get_branch();


	$sql = mysql_query("INSERT INTO `tbl_feeding_per_pen_archive` (feeding_id, company_id, building_id, branch_id, pen_id, product_id, amount, cost, feed_to_piglets, feed_sequence, isFeedType2, date_added, feeding_guide_id, number_of_heads) SELECT feeding_id, company_id, building_id, branch_id, pen_id, product_id, amount, cost, feed_to_piglets, feed_sequence, isFeedType2, date_added, feeding_guide_id, number_of_heads FROM `tbl_feeding_per_pen` where DATEDIFF('$date_today', date_added) >= 120 and company_id='$company_id' and branch_id='$branch_id' ") or die(mysql_error());

	mysql_query("DELETE from tbl_feeding_per_pen where DATEDIFF('$date_today', date_added) >= 120 and company_id='$company_id' and branch_id='$branch_id' ") or die(mysql_error());

	// insert feeding
	mysql_query("INSERT INTO `tbl_feeding_archive` (feeding_id, company_id, branch_id, swine_id, product_id, feeding_date, quantity, cost, feeding_per_pen_id, include_to_piglet_cost, swine_type, breeding_id, swine_age, days_reference, swine_status) SELECT feeding_id, company_id, branch_id, swine_id, product_id, feeding_date, quantity, cost, feeding_per_pen_id, include_to_piglet_cost, swine_type, breeding_id, swine_age, days_reference, swine_status FROM `tbl_feeding` where DATEDIFF('$date_today', feeding_date) >= 120 and company_id='$company_id' and is_archived='1' ") or die(mysql_error());

	mysql_query("DELETE from tbl_feeding where DATEDIFF('$date_today', feeding_date) >= 120 and company_id='$company_id' and is_archived='1' ") or die(mysql_error());


	// archive other consumption here ...
	archive_medication();
	archive_vaccination();
	archive_ai_materials_used();
	archive_ai_semen_used();
}


function reverse_archive_feeding($swine_id){

	$company_id = $_SESSION["system"]["company_id"];

	// insert feeding
	$sql = mysql_query("INSERT INTO `tbl_feeding` (feeding_id, company_id, branch_id, swine_id, product_id, feeding_date, quantity, cost, feeding_per_pen_id, include_to_piglet_cost, swine_type, breeding_id, swine_age, days_reference, swine_status) SELECT feeding_id, company_id, branch_id, swine_id, product_id, feeding_date, quantity, cost, feeding_per_pen_id, include_to_piglet_cost, swine_type, breeding_id, swine_age, days_reference, swine_status FROM `tbl_feeding_archive` where swine_id='$swine_id' and company_id='$company_id' ") or die(mysql_error());

	if(!$sql){
		echo "Error in reverse archiving for feeding.";
	}else{
		mysql_query("DELETE from tbl_feeding_archive where swine_id='$swine_id' and company_id='$company_id' ") or die(mysql_error());
	}
}


function archive_medication(){
	ini_set("max_execution_time", 300);

	$date_today = date("Y-m-d", strtotime(getCurrentDate()));
	$company_id = $_SESSION["system"]["company_id"];
	$branch_id = get_branch();

	$sql = mysql_query("INSERT INTO `tbl_medication_pig_archive` (`medication_id`, `company_id`, `branch_id`, `date`, `diagnosis`, `product_id`, `amount`, `cost`, `swine_id`, `building_id`, `status`, `swine_type`, `breeding_id`, `is_manual`, `med_vacc_sched_id`, `reference_number`) SELECT `medication_id`, `company_id`, `branch_id`, `date`, `diagnosis`, `product_id`, `amount`, `cost`, `swine_id`, `building_id`, `status`, `swine_type`, `breeding_id`, `is_manual`, `med_vacc_sched_id`, `reference_number` FROM `tbl_medication_pig` where DATEDIFF('$date_today', date) >= 120 and company_id='$company_id' and branch_id='$branch_id' and is_archived='1' ") or die(mysql_error());

	if(!$sql){

	}else{
		mysql_query("DELETE from tbl_medication_pig where DATEDIFF('$date_today', date) >= 120 and company_id='$company_id' and branch_id='$branch_id' and is_archived='1' ") or die(mysql_error());
	}
}

function reverse_archive_medication($swine_id){
	
	$company_id = $_SESSION["system"]["company_id"];

	$sql = mysql_query("INSERT INTO `tbl_medication_pig` (`medication_id`, `company_id`, `branch_id`, `date`, `diagnosis`, `product_id`, `amount`, `cost`, `swine_id`, `building_id`, `status`, `swine_type`, `breeding_id`, `is_manual`, `med_vacc_sched_id`, `reference_number`) SELECT `medication_id`, `company_id`, `branch_id`, `date`, `diagnosis`, `product_id`, `amount`, `cost`, `swine_id`, `building_id`, `status`, `swine_type`, `breeding_id`, `is_manual`, `med_vacc_sched_id`, `reference_number` FROM `tbl_medication_pig_archive` where swine_id='$swine_id' and company_id='$company_id' ") or die(mysql_error());

	if(!$sql){

	}else{
		mysql_query("DELETE from tbl_medication_pig_archive where swine_id='$swine_id' and company_id='$company_id'") or die(mysql_error());
	}
}



function archive_vaccination(){
	ini_set("max_execution_time", 300);

	$date_today = date("Y-m-d", strtotime(getCurrentDate()));
	$company_id = $_SESSION["system"]["company_id"];
	$branch_id = get_branch();

	$sql = mysql_query("INSERT INTO `tbl_vaccine_pig_archive` (`vaccine_id`, `company_id`, `branch_id`, `swine_id`, `v_date`, `product_id`, `v_dosage`, `diagnosis`, `cost`, `building_id`, `status`, `swine_type`, `breeding_id`, `is_manual`, `med_vacc_sched_id`, `reference_number`) SELECT `vaccine_id`, `company_id`, `branch_id`, `swine_id`, `v_date`, `product_id`, `v_dosage`, `diagnosis`, `cost`, `building_id`, `status`, `swine_type`, `breeding_id`, `is_manual`, `med_vacc_sched_id`, `reference_number` FROM `tbl_vaccine_pig` where DATEDIFF('$date_today', v_date) >= 120 and company_id='$company_id' and branch_id='$branch_id' and is_archived='1' ") or die(mysql_error());

	if(!$sql){

	}else{
		mysql_query("DELETE from tbl_vaccine_pig where DATEDIFF('$date_today', v_date) >= 120 and company_id='$company_id' and branch_id='$branch_id' and is_archived='1' ") or die(mysql_error());
	}
}


function reverse_archive_vaccination($swine_id){
	
	$company_id = $_SESSION["system"]["company_id"];

	$sql = mysql_query("INSERT INTO `tbl_vaccine_pig` (`vaccine_id`, `company_id`, `branch_id`, `swine_id`, `v_date`, `product_id`, `v_dosage`, `diagnosis`, `cost`, `building_id`, `status`, `swine_type`, `breeding_id`, `is_manual`, `med_vacc_sched_id`, `reference_number`) SELECT `vaccine_id`, `company_id`, `branch_id`, `swine_id`, `v_date`, `product_id`, `v_dosage`, `diagnosis`, `cost`, `building_id`, `status`, `swine_type`, `breeding_id`, `is_manual`, `med_vacc_sched_id`, `reference_number` FROM `tbl_vaccine_pig_archive` where swine_id='$swine_id' and company_id='$company_id'") or die(mysql_error());

	if(!$sql){

	}else{
		mysql_query("DELETE from tbl_vaccine_pig_archive where swine_id='$swine_id' and company_id='$company_id'") or die(mysql_error());
	}
}


function archive_ai_materials_used(){
	ini_set("max_execution_time", 300);

	$date_today = date("Y-m-d", strtotime(getCurrentDate()));
	$company_id = $_SESSION["system"]["company_id"];
	$branch_id = get_branch();

	$sql = mysql_query("INSERT INTO `tbl_ai_materials_pig_archive` (`ai_material_id`, `company_id`, `branch_id`, `building_id`, `swine_id`, `breeding_id`, `product_id`, `source_boar`, `quantity`, `cost`, `status`, `date_added`, `reference_number`) SELECT `ai_material_id`, `company_id`, `branch_id`, `building_id`, `swine_id`, `breeding_id`, `product_id`, `source_boar`, `quantity`, `cost`, `status`, `date_added`, `reference_number` FROM `tbl_ai_materials_pig` where DATEDIFF('$date_today', date_added) >= 120 and company_id='$company_id' and branch_id='$branch_id' and is_archived='1' ") or die(mysql_error());

	if(!$sql){

	}else{
		mysql_query("DELETE from tbl_ai_materials_pig where DATEDIFF('$date_today', date_added) >= 120 and company_id='$company_id' and branch_id='$branch_id' and is_archived='1' ") or die(mysql_error());
	}
}


function reverse_archive_ai_materials_used($swine_id){
	
	$company_id = $_SESSION["system"]["company_id"];

	$sql = mysql_query("INSERT INTO `tbl_ai_materials_pig` (`ai_material_id`, `company_id`, `branch_id`, `building_id`, `swine_id`, `breeding_id`, `product_id`, `source_boar`, `quantity`, `cost`, `status`, `date_added`, `reference_number`) SELECT `ai_material_id`, `company_id`, `branch_id`, `building_id`, `swine_id`, `breeding_id`, `product_id`, `source_boar`, `quantity`, `cost`, `status`, `date_added`, `reference_number` FROM `tbl_ai_materials_pig_archive` where swine_id='$swine_id' and company_id='$company_id'") or die(mysql_error());

	if(!$sql){

	}else{
		mysql_query("DELETE from tbl_ai_materials_pig_archive where swine_id='$swine_id' and company_id='$company_id'") or die(mysql_error());
	}
}


function archive_ai_semen_used(){
	ini_set("max_execution_time", 300);

	$date_today = date("Y-m-d", strtotime(getCurrentDate()));
	$company_id = $_SESSION["system"]["company_id"];
	$branch_id = get_branch();


	$sql = mysql_query("INSERT INTO `tbl_ai_semen_pig_archive` (`ai_semen_id`, `company_id`, `branch_id`, `building_id`, `swine_id`, `breeding_id`, `product_id`, `quantity`, `cost`, `status`, `date_added`) SELECT `ai_semen_id`, `company_id`, `branch_id`, `building_id`, `swine_id`, `breeding_id`, `product_id`, `quantity`, `cost`, `status`, `date_added` FROM `tbl_ai_semen_pig` where DATEDIFF('$date_today', date_added) >= 120 and company_id='$company_id' and branch_id='$branch_id' and is_archived='1' ") or die(mysql_error());

	if(!$sql){

	}else{
		mysql_query("DELETE from tbl_ai_semen_pig where DATEDIFF('$date_today', date_added) >= 120 and company_id='$company_id' and branch_id='$branch_id' and is_archived='1' ") or die(mysql_error());
	}
}

function reverse_archive_ai_semen_used($swine_id){
	
	$company_id = $_SESSION["system"]["company_id"];

	$sql = mysql_query("INSERT INTO `tbl_ai_semen_pig` (`ai_semen_id`, `company_id`, `branch_id`, `building_id`, `swine_id`, `breeding_id`, `product_id`, `quantity`, `cost`, `status`, `date_added`) SELECT `ai_semen_id`, `company_id`, `branch_id`, `building_id`, `swine_id`, `breeding_id`, `product_id`, `quantity`, `cost`, `status`, `date_added` FROM `tbl_ai_semen_pig_archive` where swine_id='$swine_id' and company_id='$company_id'") or die(mysql_error());

	if(!$sql){

	}else{
		mysql_query("DELETE from tbl_ai_semen_pig_archive where swine_id='$swine_id' and company_id='$company_id'") or die(mysql_error());
	}
}


function feeding_summary($product_id, $building_id, $feeding_date){

	$company_id = $_SESSION["system"]["company_id"];
	$branch_id = get_branch();

	$count_row = mysql_fetch_array(mysql_query("SELECT count(id) from tbl_feeding_summary where company_id='$company_id' and branch_id='$branch_id' and date_added='$feeding_date' and building_id='$building_id' and product_id='$product_id' "));

	$feed_summary = mysql_fetch_array(mysql_query("SELECT sum(amount), sum(amount*cost)/sum(amount), sum(number_of_heads) from tbl_feeding_per_pen where company_id='$company_id' and branch_id='$branch_id' and building_id='$building_id' and product_id='$product_id' and date_added='$feeding_date' "));

	if($count_row[0] == 0){
		mysql_query("INSERT INTO `tbl_feeding_summary` (`company_id`, `branch_id`, `building_id`, `product_id`, `quantity`, average_cost, `date_added`, number_of_heads) VALUES ('$company_id', '$branch_id', '$building_id', '$product_id', '$feed_summary[0]', '$feed_summary[1]', '$feeding_date', '$feed_summary[2]')") or die(mysql_error());
	}else{
		mysql_query("UPDATE tbl_feeding_summary set quantity='$feed_summary[0]', average_cost='$feed_summary[1]', number_of_heads='$feed_summary[2]' where company_id='$company_id' and branch_id='$branch_id' and building_id='$building_id' and product_id='$product_id' and date_added='$feeding_date' ") or die(mysql_error());
	}
}



function summarize_farm_statistics_value($transaction_date){

	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$farm_statistics_list = array('finisher_adg', 'gilt_adg');

	foreach ($farm_statistics_list as $farm_statistics) {

		$weekly_value = 0;
		$monthly_value = 0;

		if($farm_statistics == 'finisher_adg'){
			// finisher adg
			$finisher_adg_week = mysql_fetch_array(mysql_query("SELECT AVG(swine_ADG) from tbl_dr_header as dh, tbl_dr_detail as drd, tbl_swine_archive as sw where WEEK(dh.dr_date)= WEEK('$transaction_date') and YEAR(dh.dr_date) = YEAR('$transaction_date') and dh.company_id='$company_id' and dh.branch_id='$branch_id' and (dh.status='P' or dh.status='F') and dh.delivery_number=drd.delivery_number and drd.swine_id != 0 and drd.swine_id=sw.swine_id and sw.company_id='$company_id' and sw.swine_type='Finisher' and sw.delivery_status = 1"));
			$finisher_adg_month = mysql_fetch_array(mysql_query("SELECT AVG(swine_ADG) from tbl_dr_header as dh, tbl_dr_detail as drd, tbl_swine_archive as sw where MONTH(dh.dr_date)= MONTH('$transaction_date') and YEAR(dh.dr_date) = YEAR('$transaction_date') and dh.company_id='$company_id' and dh.branch_id='$branch_id' and (dh.status='P' or dh.status='F') and dh.delivery_number=drd.delivery_number and drd.swine_id != 0 and drd.swine_id=sw.swine_id and sw.company_id='$company_id' and sw.swine_type='Finisher' and sw.delivery_status = 1"));

			$weekly_value = $finisher_adg_week[0] * 1;
			$monthly_value = $finisher_adg_month[0] * 1;

			setFarmStatisticsSummary($transaction_date, $weekly_value, $monthly_value, $farm_statistics);

		}else if($farm_statistics == 'gilt_adg'){
			// gilt adg
			$gilt_adg_week = mysql_fetch_array(mysql_query("SELECT AVG(swine_ADG) from tbl_dr_header as dh, tbl_dr_detail as drd, tbl_swine_archive as sw where WEEK(dh.dr_date)= WEEK('$transaction_date') and YEAR(dh.dr_date) = YEAR('$transaction_date') and dh.company_id='$company_id' and dh.branch_id='$branch_id' and (dh.status='P' or dh.status='F') and dh.delivery_number=drd.delivery_number and drd.swine_id != 0 and drd.swine_id=sw.swine_id and sw.company_id='$company_id' and sw.swine_type='Gilt' and sw.delivery_status = 1"));
			$gilt_adg_month = mysql_fetch_array(mysql_query("SELECT AVG(swine_ADG) from tbl_dr_header as dh, tbl_dr_detail as drd, tbl_swine_archive as sw where MONTH(dh.dr_date)= MONTH('$transaction_date') and YEAR(dh.dr_date) = YEAR('$transaction_date') and dh.company_id='$company_id' and dh.branch_id='$branch_id' and (dh.status='P' or dh.status='F') and dh.delivery_number=drd.delivery_number and drd.swine_id != 0 and drd.swine_id=sw.swine_id and sw.company_id='$company_id' and sw.swine_type='Gilt' and sw.delivery_status = 1"));

			$weekly_value = $gilt_adg_week[0] * 1;
			$monthly_value = $gilt_adg_month[0] * 1;

			setFarmStatisticsSummary($transaction_date, $weekly_value, $monthly_value, $farm_statistics);
		}

	}

}

function setFarmStatisticsSummary($transaction_date, $weekly_value, $monthly_value, $farm_statistics){

	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	// check summary data
	$row_count_week = mysql_fetch_array(mysql_query("SELECT count(id) from tbl_farm_statistics_summary where farm_statistics_reference_date = 'WEEK' and farm_statistics_reference_num = WEEK('$transaction_date') and farm_statistics_year = YEAR('$transaction_date') and company_id='$company_id' and branch_id='$branch_id' and farm_statistics='$farm_statistics' "));

	if($row_count_week[0] == 0){
		// insert
		mysql_query("INSERT INTO `tbl_farm_statistics_summary` (`company_id`, `branch_id`, `farm_statistics`, farm_statistics_reference_date, `farm_statistics_reference_num`, `farm_statistics_year`, `farm_statistics_summary`) VALUES ('$company_id', '$branch_id', '$farm_statistics', 'WEEK', WEEK('$transaction_date'), YEAR('$transaction_date'), '$weekly_value')") or die(mysql_error());
	}else{
		// update
		mysql_query("UPDATE tbl_farm_statistics_summary set farm_statistics_summary = '$weekly_value' where company_id='$company_id' and branch_id='$branch_id' and farm_statistics_reference_date='WEEK' and farm_statistics_reference_num=WEEK('$transaction_date') and farm_statistics_year=YEAR('$transaction_date') ") or die(mysql_error());
	}

	$row_count_month = mysql_fetch_array(mysql_query("SELECT count(id) from tbl_farm_statistics_summary where farm_statistics_reference_date = 'MONTH' and farm_statistics_reference_num = MONTH('$transaction_date') and farm_statistics_year = YEAR('$transaction_date') and company_id='$company_id' and branch_id='$branch_id' and farm_statistics='$farm_statistics' "));

	if($row_count_month[0] == 0){
		// insert
		mysql_query("INSERT INTO `tbl_farm_statistics_summary` (`company_id`, `branch_id`, `farm_statistics`, farm_statistics_reference_date, `farm_statistics_reference_num`, `farm_statistics_year`, `farm_statistics_summary`) VALUES ('$company_id', '$branch_id', '$farm_statistics', 'MONTH', MONTH('$transaction_date'), YEAR('$transaction_date'), '$monthly_value')") or die(mysql_error());
	}else{
		// update
		mysql_query("UPDATE tbl_farm_statistics_summary set farm_statistics_summary = '$monthly_value' where company_id='$company_id' and branch_id='$branch_id' and farm_statistics_reference_date='MONTH' and farm_statistics_reference_num=MONTH('$transaction_date') and farm_statistics_year=YEAR('$transaction_date') ") or die(mysql_error());
	}
}

function flockCurrentQty($flock_inventory_id,$date){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
	$get_flock_inv = mysql_fetch_array(mysql_query("SELECT quantity FROM `tbl_flock_inventory` WHERE flock_inventory_id='$flock_inventory_id' AND company_id='$company_id' AND branch_id='$branch_id'"));

	$actual_qty = ($get_flock_inv[0] + flockInventoryAdditions($flock_inventory_id, $date)) - flockInventoryDeductions($flock_inventory_id,$date);

	return $actual_qty;
}

function flockInventoryDeductions($flock_inventory_id,$date){
	$chosen_date = date("Y-m-d",strtotime($date));
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$get_flock_inv_details = mysql_fetch_array(mysql_query("SELECT SUM(quantity) FROM `tbl_flock_inventory_details` WHERE flock_inventory_id='$flock_inventory_id' AND company_id='$company_id' AND branch_id='$branch_id' AND date_added <= '$chosen_date'"));

	$get_product_conversion = mysql_fetch_array(mysql_query("SELECT SUM(actual_qty_converted_to) FROM `tbl_product_conversion` WHERE original_item_id='$flock_inventory_id' AND company_id='$company_id' AND branch_id='$branch_id' AND DATE_FORMAT(date,'%Y-%m-%d') <= '$chosen_date' AND flock_convert_status='1'"));

	$get_sales = mysql_fetch_array(mysql_query("SELECT sum(d.actual_qty) FROM `tbl_dr_detail` as d, `tbl_dr_header` as h WHERE h.delivery_number=d.delivery_number AND d.flock_id='$flock_inventory_id' AND DATE_FORMAT(h.date_added,'%Y-%m-%d') <= '$chosen_date' AND (h.status = 'F' OR h.status = 'P') AND h.company_id='$company_id' AND h.branch_id='$branch_id' "));

	$total_inventory_adjustment = mysql_fetch_array(mysql_query("SELECT sum(iad.qty) FROM tbl_inventory_adjustment_details AS iad, tbl_inventory_adjustment_header AS iah WHERE iah.company_id='$company_id' AND iah.branch_id='$branch_id' AND iah.flock_status='1' AND iad.flock_status='1' AND iad.qty < 0 AND iah.date < DATE_ADD('$date', INTERVAL 1 DAY) AND iah.status='F' AND iah.inv_adj_num=iad.inv_adj_num AND iad.product_id='$flock_inventory_id'"));
	$get_inv_adjustment = abs($total_inventory_adjustment[0]);

	$get_stock_transfer_branch_to_branch = mysql_fetch_array(mysql_query("SELECT SUM(qty) FROM tbl_stock_transfer_flock_branch_branch_eggs AS h, tbl_stock_transfer_flock_branch_branch_details_eggs AS d WHERE h.ref_id=d.ref_id AND h.source_branch='$branch_id' AND h.company_id='$company_id' AND h.status='F' AND d.product_id='$flock_inventory_id'"));

	return $get_flock_inv_details[0] + $get_product_conversion[0] + $get_sales[0] + $get_inv_adjustment + $get_stock_transfer_branch_to_branch[0];
}

function flockInventoryAdditions($flock_inventory_id, $date){
	$chosen_date = date("Y-m-d",strtotime($date));
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$get_sales_return = mysql_fetch_array(mysql_query("SELECT sum(d.actual_qty) FROM `tbl_sales_return_details` as d, `tbl_sales_return` as h WHERE h.sr_number=d.sr_number AND d.flock_id='$flock_inventory_id' AND DATE_FORMAT(h.date_added,'%Y-%m-%d') <= '$chosen_date' AND h.status = 'F' AND h.company_id='$company_id' AND h.branch_id='$branch_id' "));

	$total_inventory_adjustment = mysql_fetch_array(mysql_query("SELECT sum(iad.qty) FROM tbl_inventory_adjustment_details AS iad, tbl_inventory_adjustment_header AS iah WHERE iah.company_id='$company_id' AND iah.branch_id='$branch_id' AND iah.flock_status='1' AND iad.flock_status='1' AND iad.qty > 0 AND iah.date < DATE_ADD('$date', INTERVAL 1 DAY) AND iah.status='F' AND iah.inv_adj_num=iad.inv_adj_num AND iad.product_id='$flock_inventory_id'"));
	$get_inv_adjustment = $total_inventory_adjustment[0];

	return $get_sales_return[0] + $get_inv_adjustment;
}

function check_expiration($exp_id,$qty,$packaging_id,$warehouse_id,$ref_num){
	$company_id = $_SESSION["system"]["company_id"];
	$branch_id = get_branch();
	$pckQTY = mysql_fetch_array(mysql_query("SELECT `qty` FROM `tbl_package` WHERE `package_id`='$packaging_id'"));
	$exp_a_qty = $pckQTY[0]*$qty;
			
	$exp_query = mysql_query("UPDATE `tbl_expiry_products` SET remainig_qty=remainig_qty-'$qty',remainig_actual_qty=remainig_actual_qty-'$exp_a_qty' WHERE company_id = '$company_id' AND branch_id = '$branch_id' AND exp_id='$exp_id'")  or die (mysql_error());
	
	if($exp_query and $qty > 0){
		mysql_query("INSERT INTO `tbl_exp_details`(`ref_num`, `exp_id`, `qty`) VALUES ('$ref_num','$exp_id','$qty')") or die (mysql_error());
	}
	
	/*$fecth_exp = mysql_query("SELECT remainig_qty,exp_id from tbl_expiry_products WHERE company_id = '$company_id' AND branch_id = '$branch_id' AND remainig_qty > 0 AND warehouse_id='$warehouse_id' AND product_id='$product_id' AND packaging_id = '$packaging_id' ORDER BY expiry_date ASC")  or die (mysql_error());
	$drd_qty = $qty;
	if($drd_qty > 0){
		while($expRow = mysql_fetch_array($fecth_exp)){
			
			$exp_qty = $expRow['remainig_qty'];
			
			if($drd_qty > $exp_qty){
				$tu_qty = $exp_qty;
			}else{
				$tu_qty = $drd_qty;
			}
			
			$drd_qty = $drd_qty-$tu_qty;
			
			$pckQTY = mysql_fetch_array(mysql_query("SELECT `qty` FROM `tbl_package` WHERE `package_id`='$packaging_id'"));
			$exp_a_qty = $pckQTY[0];
			
			
			$exp_query = mysql_query("UPDATE `tbl_expiry_products` SET remainig_qty=remainig_qty-'$tu_qty',remainig_actual_qty=remainig_actual_qty-'$exp_a_qty' WHERE company_id = '$company_id' AND branch_id = '$branch_id' AND exp_id='$expRow[exp_id]'")  or die (mysql_error());
			
			if($exp_query and $tu_qty > 0){
				mysql_query("INSERT INTO `tbl_exp_details`(`ref_num`, `exp_id`, `qty`) VALUES ('$ref_num','$expRow[exp_id]','$tu_qty')") or die (mysql_error());
			}
		}
		
	}*/
	
}

function remainingExpQty($exp_id){
	
	$company_id = $_SESSION["system"]["company_id"];
	$branch_id = get_branch();
	
	$remaining_qty = mysql_fetch_array(mysql_query("SELECT remainig_qty FROM tbl_expiry_products WHERE exp_id='$exp_id' AND company_id='$company_id' AND branch_id='$branch_id'"));
	
	return $remaining_qty[0];
}


function buildingPopulationLimitEggs($id){
	$company_id = $_SESSION["system"]["company_id"];
	
	$result = mysql_fetch_array(mysql_query("SELECT building_population_limit FROM `tbl_building_eggs` WHERE building_id='$id' AND company_id='$company_id'"));

	return $result[0];
}
function buildingPopulationLimitBroiler($id){
	$company_id = $_SESSION["system"]["company_id"];
	
	$result = mysql_fetch_array(mysql_query("SELECT building_population_limit FROM `tbl_building_broiler` WHERE building_id='$id' AND company_id='$company_id'"));

	return $result[0];
}

function incubatorBroiler($id){
	$company_id = $_SESSION["system"]["company_id"];
	$branch_id = get_branch();
	
	$result = mysql_fetch_array(mysql_query("SELECT incubator_number FROM `tbl_incubator_broiler` WHERE incubator_id='$id' AND company_id='$company_id' AND branch_id='$branch_id'"));

	return $result[0];
}

function candlingDamageBroiler($id){
	$company_id = $_SESSION["system"]["company_id"];
	$branch_id = get_branch();
	
	$result = mysql_fetch_array(mysql_query("SELECT cause FROM `tbl_incubator_damage` WHERE damage_id='$id' AND company_id='$company_id' AND branch_id='$branch_id'"));

	return $result[0];
}

function incubationRef($id){
	$company_id = $_SESSION["system"]["company_id"];
	$branch_id = get_branch();
	
	$result = mysql_fetch_array(mysql_query("SELECT ref_number FROM `tbl_incubation_broiler` WHERE incubation_id='$id' AND company_id='$company_id' AND branch_id='$branch_id'"));

	return $result[0];
}


function insertToGLDetails($gl_tran_header, $chart_id, $debit, $credit){
	mysql_query("INSERT INTO `tbl_gltran_detail`(`gltran_header_id`, `gchart_id`, `debit`, `credit`) VALUES ('$gl_tran_header','$chart_id','$debit','$credit')");
}

function getJournalID($journal_code){
	$journal_id_q = mysql_fetch_array(mysql_query("SELECT journal_id from tbl_journal where company_id='0' and journal_code='$journal_code' "));
	return $journal_id_q[0];
}


function checkIfOneUserLogin(){
	$user_id = $_SESSION['system']['userid'];
	$login_status = mysql_fetch_array(mysql_query("SELECT status from tbl_users where user_id=$user_id"));
	if($login_status[0] == 0){
		header("location: auth/logout.php");
	}
}

function getUserCategoryId($company_id_ = -1,$user_id_ = -1)
{
	$company_id = ($company_id_ == -1)?$_SESSION['system']['company_id']:$company_id_;
	$user_id = ($user_id_ == -1)?$_SESSION['system']['userid']:$user_id_;
	$fetch = FM_SELECT_QUERY("category_id","tbl_users","company_id = '$company_id' AND user_id = '$user_id'");
	return $fetch[0];
}

function branch($branch_id,$company_id){
	$company_id = $_SESSION['system']['company_id'];
	$user_id = $_SESSION['system']['userid'];
	$branch_id = get_branch();

	$category_id = getUserCategoryId($company_id,$user_id);
	$category_restricts = array(0,-4);
	$content = "";
	if($category_id == -3){
		$content .= nut_branch($company_id,$branch_id);
	}else{
		$content .= ($category_id==-3)?"<option value=''> &mdash; Select Your Clients &mdash;</option>":"<option value=''>  &mdash; Select Farm Location &mdash; </option>";

		$column = in_array($category_id,$category_restricts)?"branch_id,branch":"branch_id";
		$table = in_array($category_id,$category_restricts)?"tbl_branch":"tbl_user_branches";
		$inject = in_array($category_id,$category_restricts)?"status = 1":"user_id='$user_id'";

		$loop_branch = FM_SELECT_LOOP_QUERY($column,$table,"company_id = '$company_id' AND visibility_status = '1'  AND $inject");
		if(count($loop_branch)>0){
			foreach($loop_branch as $result)
			{
				$selected = ($result[branch_id] == $branch_id)?"selected":"";
				$branch_name = (in_array($category_id,$category_restricts)?$result[branch]:getBranch($result[branch_id]));
				$content .= "<option $selected value='$result[branch_id]'>$branch_name</option>";
			}
		}
	}
	return $content;
}

function nut_branch($company_id,$branch_id){
	$content = "<option value=''> &mdash; Select Your Clients &mdash;</option>";

	$loop_branch = FM_SELECT_LOOP_QUERY("branch_id,client_name,is_subscriber","tbl_nut_branch_checker","company_id = '$company_id'");
	if(count($loop_branch)>0){
		foreach($loop_branch as $result)
		{
			$selected = ($result[branch_id] == $branch_id)?"selected":"";
			$content .= ($result[is_subscriber] == 1)?nut_branch_sub($branch_id,$result[branch_id],$result[client_name]):"<option $selected value='$result[branch_id],0'>$result[client_name]</option>";
		}
	}
	return $content;
}

function nut_branch_sub($branch_id_session,$branch_id,$client_name){
	$loop_ = FM_SELECT_LOOP_QUERY("*","tbl_nut_client_branch","nut_branch_id = '$branch_id' AND status = 1 ORDER BY branch ASC");
	if(count($loop_)>0){
		$branch_id_session = $branch_id_session.",".$_SESSION["nut"]["branch"];
		$content .= "<optgroup label='$client_name'>";
		foreach($loop_ as $row){
			$selected = ($branch_id_session == "$row[nut_branch_id],$row[branch_id]")?"selected":"";
			$content .= "<option $selected value='$row[nut_branch_id],$row[branch_id]'>$row[branch]</option>";
		}
		$content .= "</optgroup>";
	}else{
		$selected = ($branch_id_session == $branch_id)?"selected":"";
		$content .= "<option $selected value='$branch_id,0'>$client_name</option>";
	}
	return $content;
}

function Allbranch($company_id){
	$company_id = $_SESSION['system']['company_id'];
	$content = "";
	$content .="<option value=''> -- Select Farm Location -- </option>";
	$result = mysql_query("select * from tbl_branch where company_id = '$company_id' and status = '1'") or die(mysql_error());
	while($row = mysql_fetch_assoc($result)){
		if($row['branch_id'] == get_branch()){
			$content .="<option selected value=".$row['branch_id'].">".$row['branch']."</option>";
		}else{
			$content .="<option value=".$row['branch_id'].">".$row['branch']."</option>";
		}
	}
	return $content;
}

// function getCurrentDate(){
// 	ini_set('date.timezone','UTC');
// 	date_default_timezone_set('UTC');
// 	$today = date('H:i:s');
// 	$date = date('Y-m-d H:i:s', strtotime($today)+28800);
	
// 	return $date;
// }

function getWarehouseBranch($warehouse_id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_fetch_array(mysql_query("SELECT branch_id from tbl_warehouse where warehouse_id = '$warehouse_id' and company_id = '$company_id'"));
	
	return $result[0];
}

function getProdCat($product_id){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
	
	$result = mysql_query("SELECT * FROM `tbl_productmaster` WHERE product_id = '$product_id' and ((company_id = '$company_id'  or company_id = 0) and (branch_id = '$branch_id' or branch_id = 0))") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row['product_categ_id'];
}


function getSwineIdOfProduct($id){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
	$row = mysql_fetch_array(mysql_query("SELECT swine_id from tbl_productmaster where company_id='$company_id' and branch_id='$branch_id' and product_id='$id' "));

	return $row[0];
}

/****  s w i n e   c o u n t e r  ****/

function swineCounter($date){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
	
	if($company_id == 0 or $branch_id == 0){
		
	}else{
	
		$count_row = mysql_fetch_array(mysql_query("SELECT count(counter_id) from tbl_swine_counter where company_id='$company_id' and branch_id='$branch_id' and date_modified = '$date'"));
		
		//count sow
		$sow_count = mysql_fetch_array(mysql_query("SELECT count(swine_id) from tbl_swine where company_id='$company_id' and branch_id='$branch_id' and swine_type='Sow' and (mortality_status=0 and cull_status=0 and delivery_status=0) and date_added <= '$date' "));
		// gilt count
		$gilt_count = mysql_fetch_array(mysql_query("SELECT count(swine_id) from tbl_swine where company_id='$company_id' and branch_id='$branch_id' and swine_type='Gilt' and (mortality_status=0 and cull_status=0 and delivery_status=0) and date_added <= '$date' "));
		// junior boar count
		$jboar_count = mysql_fetch_array(mysql_query("SELECT count(swine_id) from tbl_swine where company_id='$company_id' and branch_id='$branch_id' and swine_type='Junior-boar' and (mortality_status=0 and cull_status=0 and delivery_status=0) and date_added <= '$date' "));
		// senior boar count
		$sboar_count = mysql_fetch_array(mysql_query("SELECT count(swine_id) from tbl_swine where company_id='$company_id' and branch_id='$branch_id' and swine_type='Senior-boar' and (mortality_status=0 and cull_status=0 and delivery_status=0) and date_added <= '$date' "));
		// grower count
		$grower_count = mysql_fetch_array(mysql_query("SELECT count(swine_id) from tbl_swine where company_id='$company_id' and branch_id='$branch_id' and swine_type='Grower' and (mortality_status=0 and cull_status=0 and delivery_status=0) and date_added <= '$date' "));
		// weaner count
		$weaner_count = mysql_fetch_array(mysql_query("SELECT count(swine_id) from tbl_swine where company_id='$company_id' and branch_id='$branch_id' and swine_type='Weaner' and (mortality_status=0 and cull_status=0 and delivery_status=0) and date_added <= '$date' "));
		// finisher count
		$finisher_count = mysql_fetch_array(mysql_query("SELECT count(swine_id) from tbl_swine where company_id='$company_id' and branch_id='$branch_id' and swine_type='Finisher' and (mortality_status=0 and cull_status=0 and delivery_status=0) and date_added <= '$date' "));
		// piglet count
		$piglet_count = mysql_fetch_array(mysql_query("SELECT count(swine_id) from tbl_swine where company_id='$company_id' and branch_id='$branch_id' and swine_type='Piglet' and (mortality_status=0 and cull_status=0 and delivery_status=0) and status=1 and date_added <= '$date' "));
		
		// pregnant count
		$pregnant_count = mysql_fetch_array(mysql_query("SELECT count(swine_id) from tbl_swine where company_id='$company_id' and branch_id='$branch_id' and (swine_type='Gilt' or swine_type='Sow') and pregnant_status=1 and (mortality_status=0 and cull_status=0 and delivery_status=0) "));
		
		//count dry sows
		//$dry_sows_count = mysql_fetch_array(mysql_query("SELECT count(swine_id) from tbl_swine as sw, tbl_pen_assignment as pa, tbl_pen_type as pt where sw.pen_code=pa.pen_assignment_id and pt.pen_type_id=pa.pen_type and pt.pen_type='Dry' and sw.company_id='$company_id' and sw.branch_id='$branch_id' and sw.mortality_status=0 and sw.cull_status=0 and sw.delivery_status=0 and sw.date_added <= '$date' "));

		//$dry_sows_count = mysql_fetch_array(mysql_query("SELECT count(swine_id) from tbl_swine where company_id='$company_id' and branch_id='$branch_id' and delivery_status=0 and cull_status=0 and mortality_status=0 and pregnant_status=0 and swine_type='Sow' "));

		//$dry_weaning = mysql_fetch_array(mysql_query("SELECT count(fp.farrowing_id) FROM tbl_swine as sw, `tbl_farrowing_pig` as fp WHERE sw.swine_id=fp.swine_id and sw.company_id='$company_id' and fp.swine_id = sw.swine_id  and sw.branch_id='$branch_id' and sw.delivery_status=0 and sw.cull_status=0 and sw.mortality_status=0  and sw.pregnant_status=0  and fp.weaning_date ='0000-00-00'")) or die (mysql_error());

		//$count_dry = $dry_sows_count[0] - $dry_weaning[0];

		$count_dry = 0;
		$count_lactating = 0;
		$fetch_dry_sows = mysql_query("SELECT pen_code from tbl_swine where company_id='$company_id' and branch_id='$branch_id' and delivery_status=0 and cull_status=0 and mortality_status=0 and pregnant_status=0 and swine_type='Sow' ");
		while($dry_sows_row = mysql_fetch_array($fetch_dry_sows)){
			$piglet_count = mysql_fetch_array(mysql_query("SELECT count(swine_id) from tbl_swine where pen_code='$dry_sows_row[0]' and delivery_status=0 and cull_status=0 and mortality_status=0 and status=1 and company_id='$company_id' and branch_id='$branch_id' "));

			if($piglet_count[0] == 0){
				$count_dry ++;
			}else{
				$count_lactating ++;
			}
		}
		
		//count lactating
		//$lactating_count = mysql_fetch_array(mysql_query("SELECT count(DISTINCT(sw.swine_id)) FROM tbl_swine as sw, `tbl_farrowing_pig` as fp WHERE (sw.swine_id=fp.swine_id and fp.weaning_date='0000-00-00' and sw.company_id='$company_id' and sw.branch_id='$branch_id' and sw.pregnant_status=0  and sw.mortality_status=0 and sw.delivery_status=0 and sw.cull_status=0 and fp.company_id='$company_id' and fp.branch_id='$branch_id')"));

		//$lactating_count = mysql_fetch_array(mysql_query("SELECT count(DISTINCT(sw.swine_id)) FROM tbl_swine as sw, `tbl_farrowing_pig` as fp, tbl_breeding_pig as bp WHERE (sw.swine_id=fp.swine_id and fp.weaning_date='0000-00-00' and sw.company_id='$company_id' and sw.branch_id='$branch_id' and sw.pregnant_status=0 and fp.company_id='$company_id' and fp.branch_id='$branch_id' and bp.swine_id=fp.swine_id) or (bp.swine_id=sw.swine_id and sw.pregnant_status=1 and sw.company_id='$company_id' and sw.branch_id='$branch_id' and DATEDIFF('$date', bp.breeding_date) >= 100 and bp.company_id='$company_id' and bp.branch_id='$branch_id' and bp.swine_id=fp.swine_id and bp.status=1)"));

		$current_population = mysql_fetch_array(mysql_query("SELECT count(swine_id) from tbl_swine where company_id='$company_id' and branch_id='$branch_id' and cull_status=0 and delivery_status=0 and mortality_status=0 ")) or die(mysql_error());

		//$confirmed_to_farrow_count = mysql_fetch_array(mysql_query("SELECT count(swine_id) FROM tbl_swine WHERE company_id='$company_id' and branch_id='$branch_id' and pregnant_status=1 and alert_parameter_status=2 "));
		
	
	
		if($count_row[0] > 0){
		// update record
			mysql_query("UPDATE `tbl_swine_counter` SET `sow`='$sow_count[0]',`gilt`='$gilt_count[0]',`weaner`='$weaner_count[0]',`grower`='$grower_count[0]',`finisher`='$finisher_count[0]',`junior_boar`='$jboar_count[0]',`senior_boar`='$sboar_count[0]',`piglet`='$piglet_count[0]',`dry_sow`='$count_dry',`lactating`='$count_lactating',`pregnant`='$pregnant_count[0]', swine_population='$current_population[0]' WHERE company_id='$company_id' and branch_id='$branch_id' and date_modified='$date' ") or die(mysql_error());
		}else{
			//insert record
			mysql_query("INSERT INTO `tbl_swine_counter`(`company_id`, `branch_id`, `sow`, `gilt`, `weaner`, `grower`, `finisher`, `junior_boar`, `senior_boar`, `piglet`, `dry_sow`, `lactating`, `pregnant`, swine_population , `date_modified`) VALUES ('$company_id','$branch_id','$sow_count[0]','$gilt_count[0]','$weaner_count[0]','$grower_count[0]','$finisher_count[0]','$jboar_count[0]','$sboar_count[0]','$piglet_count[0]','$count_dry','$count_lactating','$pregnant_count[0]', '$current_population[0]' ,'$date')") or die(mysql_error());
		}


		// delete entries not equal to max
		$max_date_for_week = mysql_fetch_array(mysql_query("SELECT max(date_modified) from tbl_swine_counter where company_id='$company_id' and branch_id='$branch_id' and WEEKOFYEAR('$date') = WEEKOFYEAR(date_modified) and MONTH('$date') = MONTH(date_modified) and YEAR(date_modified) = YEAR('$date') ")) or die(mysql_error());

		mysql_query("DELETE from tbl_swine_counter where company_id='$company_id' and branch_id='$branch_id' and WEEKOFYEAR('$date') = WEEKOFYEAR(date_modified) and MONTH(date_modified) = MONTH('$date') and YEAR(date_modified) = YEAR('$date') and date_modified != '$max_date_for_week[0]' ") or die(mysql_error());
		mysql_query("DELETE from tbl_swine_counter where branch_id=0 or company_id = 0") or die(mysql_error());

	}
}


/****  e n d   s w i n e   c o u n t e r  ****/


//  S W I N E   P O P U L A T I O N   C O U N T E R //
function swinePopulationCounter(){

	// check if from local
	$company_id = $_SESSION['system']['company_id'];
	$isFromLocal = mysql_fetch_array(mysql_query("SELECT local_db_status from tbl_company where company_id='$company_id' "));
	if($isFromLocal[0] == 1){
		// check if has internet connection
		if(checkIfHasInternetConnection() == 1){

			switchConnection(1);
			addSwinePopulationCounter();
			switchConnection(-1);
		}
		
	}else{
		addSwinePopulationCounter();
	}
}
//  E N D   S W I N E   P O P U L A T I O N   C O U N T E R


function addSwinePopulationCounter(){

	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
	$date = date('Y-m-d', strtotime(getCurrentDate()));

	if($branch_id == 0){

	}else{

		$region = getRegion($branch_id);

		// count 135
		$age_135 = mysql_fetch_array(mysql_query("SELECT count(swine_id) from tbl_swine where company_id='$company_id' and branch_id='$branch_id' and DATEDIFF('$date', swine_birthdate) <= 135 and (swine_type='Weaner' or swine_type='Grower' or swine_type='Finisher') and mortality_status=0 and cull_status=0 and delivery_status=0 "));
		// count 150
		$age_150 = mysql_fetch_array(mysql_query("SELECT count(swine_id) from tbl_swine where company_id='$company_id' and branch_id='$branch_id' and DATEDIFF('$date', swine_birthdate) >= 136 and DATEDIFF('$date', swine_birthdate) <= 150 and (swine_type='Weaner' or swine_type='Grower' or swine_type='Finisher') and mortality_status=0 and cull_status=0 and delivery_status=0 "));
		// count 165
		$age_165 = mysql_fetch_array(mysql_query("SELECT count(swine_id) from tbl_swine where company_id='$company_id' and branch_id='$branch_id' and DATEDIFF('$date', swine_birthdate) >= 151 and DATEDIFF('$date', swine_birthdate) <= 165 and (swine_type='Weaner' or swine_type='Grower' or swine_type='Finisher') and mortality_status=0 and cull_status=0 and delivery_status=0 "));
		// count 180
		$age_180 = mysql_fetch_array(mysql_query("SELECT count(swine_id) from tbl_swine where company_id='$company_id' and branch_id='$branch_id' and DATEDIFF('$date', swine_birthdate) >= 166 and DATEDIFF('$date', swine_birthdate) <= 180 and (swine_type='Weaner' or swine_type='Grower' or swine_type='Finisher') and mortality_status=0 and cull_status=0 and delivery_status=0 "));
		// count 180+
		$age_180_up = mysql_fetch_array(mysql_query("SELECT count(swine_id) from tbl_swine where company_id='$company_id' and branch_id='$branch_id' and DATEDIFF('$date', swine_birthdate) > 180 and (swine_type='Weaner' or swine_type='Grower' or swine_type='Finisher') and mortality_status=0 and cull_status=0 and delivery_status=0 "));

		// connect to main db

		$main_db = $GLOBALS['config']['mysql']['orig_db_main'];
		mysql_query("USE $main_db") or die(mysql_error());

		
		$count_row = mysql_fetch_array(mysql_query("SELECT count(id) from tbl_swine_population_counter where company_id='$company_id' and region='$region' and date_added = '$date'"));

		if($count_row[0] > 0){
			// UPDATE
			mysql_query("UPDATE tbl_swine_population_counter set age_135='$age_135[0]', age_150='$age_150[0]', age_165='$age_165[0]', age_180='$age_180[0]', age_180_up='$age_180_up[0]' where company_id='$company_id' and branch_id='$branch_id' and date_added='$date' ") or die(mysql_error());
		}else{
			// INSERT
			mysql_query("INSERT INTO tbl_swine_population_counter (company_id, branch_id, region, age_135, age_150, age_165, age_180, age_180_up, date_added) VALUES('$company_id', '$branch_id', '$region', '$age_135[0]', '$age_150[0]', '$age_165[0]', '$age_180[0]', '$age_180_up[0]', '$date') ") or die(mysql_error());
		}

		// delete entries not equal to max
		//$max_date = mysql_fetch_array(mysql_query("SELECT max(date_added) from tbl_swine_population_counter where company_id='$company_id' and branch_id='$branch_id' and MONTH('$date') = MONTH(date_added) and YEAR(date_added) = YEAR('$date') ")) or die(mysql_error());

		//mysql_query("DELETE from tbl_swine_population_counter where company_id='$company_id' and branch_id='$branch_id' and MONTH(date_added) = MONTH('$date') and YEAR(date_added) = YEAR('$date') and date_added != '$max_date[0]' ") or die(mysql_error());
		mysql_query("DELETE from tbl_swine_population_counter where branch_id=0 or company_id = 0") or die(mysql_error());


		$database = $GLOBALS['config']['mysql']['db_prefix'].getCompanyCode($company_id);
		mysql_query("USE $database ") or die(mysql_error());

	}
}




// c o s t  p e r  p i g ----

function costPerPig($swine_id){
	
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
	
	$total_cost_of_pig = 0;

	$count_row = mysql_fetch_array(mysql_query("SELECT count(swine_id) from tbl_swine where swine_id='$swine_id' and company_id='$company_id' and branch_id='$branch_id' "));

	if($count_row[0] == 0){
		//current cost
		$swine_price = mysql_fetch_array(mysql_query("SELECT price from tbl_swine_archive where swine_id='$swine_id'  and company_id='$company_id' "));
	}else{
		//current cost
		$swine_price = mysql_fetch_array(mysql_query("SELECT price from tbl_swine where swine_id='$swine_id'  and company_id='$company_id' "));
	}
	
	
	
	//vaccine consumption
	$vaccination_consumption = mysql_fetch_array(mysql_query("SELECT sum(cost * v_dosage) from tbl_vaccine_pig where swine_id='$swine_id' and company_id='$company_id' and status=0 "));
	
	//medicine consumption
	$medication_consumption = mysql_fetch_array(mysql_query("SELECT sum(cost * amount) from tbl_medication_pig where swine_id='$swine_id' and company_id='$company_id' and status=0  "));


	//Feeding consumption
	$feeding_consumption = mysql_fetch_array(mysql_query("SELECT sum(cost * quantity) from tbl_feeding where company_id='$company_id' and swine_id='$swine_id' and include_to_piglet_cost=0 "));

	//fetch swine type
	$swine_type = mysql_fetch_array(mysql_query("SELECT swine_type from tbl_swine where swine_id='$swine_id'  and company_id='$company_id' "));

	$swine_depreciation = 0;

	if($swine_type[0] == 'Sow'){
		//Swine Depreciation
		$swine_dep = mysql_fetch_array(mysql_query("SELECT sum(amount) FROM `tbl_swine_dep_amount` where  sow_id='$swine_id' and boar_id='0' and company_id='$company_id'"));
		$swine_depreciation = $swine_dep[0];

	}else{
		//Swine Depreciation
		$swine_dep = mysql_fetch_array(mysql_query("SELECT sum(amount) FROM `tbl_swine_dep_amount` where  boar_id='$swine_id' and company_id='$company_id'"));
		$aip_depreciation = mysql_fetch_array(mysql_query("SELECT sum(swine_depreciation_cost) from tbl_ai_semen_production_pig where company_id='$company_id' and swine_id='$swine_id' and status='F' "));
		$swine_depreciation = $swine_dep[0] + $aip_depreciation[0];
	}

	$total_cost_of_pig = ($swine_price[0] + $vaccination_consumption[0] + $medication_consumption[0] + $feeding_consumption[0]) - $swine_depreciation;
	return $total_cost_of_pig;
}

// e n d  c o s t  p e r  p i g ---


function swineTotalConsumption($swine_id){
	
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
	
	//vaccine consumption
	$vaccination_consumption = mysql_fetch_array(mysql_query("SELECT sum(cost * v_dosage) from tbl_vaccine_pig where swine_id='$swine_id' and company_id='$company_id' and status=1 "));
	
	//medicine consumption
	$medication_consumption = mysql_fetch_array(mysql_query("SELECT sum(cost * amount) from tbl_medication_pig where swine_id='$swine_id' and company_id='$company_id' and status=1  "));
	
	$feeding_consumption = mysql_fetch_array(mysql_query("SELECT sum(cost * quantity) from tbl_feeding where company_id='$company_id' and swine_id='$swine_id' and include_to_piglet_cost=1 "));
	
	$AIMAterials_used = mysql_fetch_array(mysql_query("SELECT sum(cost * quantity) from tbl_ai_materials_pig where company_id='$company_id' and swine_id='$swine_id' and status=1 "));

	$AISemen = mysql_fetch_array(mysql_query("SELECT sum(cost * quantity) from tbl_ai_semen_pig where company_id='$company_id' and swine_id='$swine_id' and status=1 "));
	
	$APC_total = mysql_fetch_array(mysql_query("SELECT accumulated_piglet_cost from tbl_swine where company_id='$company_id' and swine_id='$swine_id'"));

	// mortality
	$prewean_mortality_deduction = mysql_fetch_array(mysql_query("SELECT sum(apc) from tbl_mortality_pig where parent_sow='$swine_id' and is_recorded_to_weaning=1 and company_id='$company_id' and wean_categ='PR' and status != 'C' "));

	// cancelled mortality
	//$cancelled_mortality = mysql_fetch_array(mysql_query("SELECT sum(apc) from tbl_mortality_pig where swine_id='$swine_id' and company_id='$company_id' and status='C' and wean_categ != 'PR' "));

	// condemned piglets
	$condemned_piglets_deduction = mysql_fetch_array(mysql_query("SELECT sum(apc) from tbl_condemned_piglets_pig where parent_sow='$swine_id' and is_recorded_to_weaning=1 and company_id='$company_id' and status != 'C' "));

	// from other sow
	$adopted_apc_add = mysql_fetch_array(mysql_query("SELECT sum(apc) from tbl_adopt_pig where company_id='$company_id' and foster_sow_id='$swine_id' and is_weaned_foster_sow=0 and status!='C'"));

	// from this sow
	$adopted_apc_subtract = mysql_fetch_array(mysql_query("SELECT sum(apc) from tbl_adopt_pig where company_id='$company_id' and parent_sow_id='$swine_id' and is_weaned_parent_sow=0 and status!='C'"));

	// Cancelled adopt from other sow
	$cancelled_adopted_apc_subtract = mysql_fetch_array(mysql_query("SELECT sum(apc) from tbl_adopt_pig where company_id='$company_id' and foster_sow_id='$swine_id' and is_weaned_parent_sow=1 and status='C'"));

	// Cancelled adopt from this sow
	$cancelled_adopted_apc_add = mysql_fetch_array(mysql_query("SELECT sum(apc) from tbl_adopt_pig where company_id='$company_id' and parent_sow_id='$swine_id' and is_weaned_parent_sow=1 and status='C' "));


	//Swine Depreciation
	$swine_dep = mysql_fetch_array(mysql_query("SELECT sum(amount) FROM `tbl_swine_dep_amount` where status = 0 and sow_id='$swine_id' and company_id='$company_id'"));

	//ADDITIONAL APC if cancel abort, Nip
	$swine_apc = mysql_fetch_array(mysql_query("SELECT sum(amount) FROM `tbl_apc` where status = 1 and swine_id='$swine_id' and company_id='$company_id'"));



	$total_consumption = ($vaccination_consumption[0] + $medication_consumption[0] + $feeding_consumption[0] + $AIMAterials_used[0] + $APC_total[0] + $adopted_apc_add[0] + $swine_dep[0] + $swine_apc[0] + $AISemen[0] + $cancelled_adopted_apc_add[0]) - ($prewean_mortality_deduction[0] + $adopted_apc_subtract[0] + $condemned_piglets_deduction[0] +$cancelled_adopted_apc_subtract[0] );

	//$total_consumption = ($vaccination_consumption[0] + $medication_consumption[0] + $feeding_consumption[0] + $AIMAterials_used[0] + $APC_total[0]) - ($prewean_mortality_deduction[0]);
	return $total_consumption;
}
function swineUpdateAPC($swine_id){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
	// set apc to 0
	mysql_query("UPDATE tbl_swine set accumulated_piglet_cost=0 where swine_id='$swine_id' and company_id='$company_id' and branch_id='$branch_id' ");
	
	// set feeding status = 2
	mysql_query("UPDATE tbl_feeding set include_to_piglet_cost=2 where include_to_piglet_cost=1 and swine_id='$swine_id' and company_id='$company_id' and branch_id='$branch_id' ");
	
	//set vaccination status = 2
	mysql_query("UPDATE tbl_vaccine_pig set status=2 where status = 1 and swine_id='$swine_id' and company_id='$company_id' and branch_id='$branch_id' ");
	
	//set med status = 2
	mysql_query("UPDATE tbl_medication_pig set status=2 where status = 1 and swine_id='$swine_id' and company_id='$company_id' and branch_id='$branch_id' ");
	
	//set ai materials status = 2
	mysql_query("UPDATE tbl_ai_materials_pig set status=2 where status = 1 and swine_id='$swine_id' and company_id='$company_id' and branch_id='$branch_id' ");
	
	//set cancel apc status = 2
	mysql_query("UPDATE tbl_apc set status=2 where status = 1 and swine_id='$swine_id' and company_id='$company_id' and branch_id='$branch_id' ");
	
	//set swine_dep_amount status = 1
	mysql_query("UPDATE tbl_swine_dep_amount set status=1 where status = 0 and sow_id='$swine_id' and company_id='$company_id' and branch_id='$branch_id' ");

	//set AISEMEN status = 2
	mysql_query("UPDATE tbl_ai_semen_pig set status=2 where status = 1 and swine_id='$swine_id' and company_id='$company_id' and branch_id='$branch_id' ");

}

function checkProductionIfAmortized($production_id){

	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$result =  mysql_fetch_array(mysql_query("SELECT COUNT(amortization_id) FROM `tbl_amortization` WHERE company_id='$company_id' AND branch_id='$branch_id' AND production_batch='$production_id'"));

	return $result[0];
}

function checkProductionIfAmortizedBroiler($production_id){

	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$result =  mysql_fetch_array(mysql_query("SELECT COUNT(amortization_id) FROM `tbl_amortization_broiler` WHERE company_id='$company_id' AND branch_id='$branch_id' AND production_batch='$production_id'"));

	return $result[0];
}

function unweaned_piglets_consumption($current_pen_id){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
	
	$ear_tag = getSwine($parent_sow_id);
	//UCASE(dam) = UCASE('$ear_tag')
	$fetch_unweaned_piglets = mysql_query("SELECT swine_id from tbl_swine where company_id='$company_id' and swine_type='Piglet' and status=1 and mortality_status=0 and cull_status=0 and delivery_status=0 and pen_code='$current_pen_id' ");
	
	$vaccination_consumption = 0;
	$medication_consumption = 0;
	$feeding_consumption = 0;

	$total_cost = 0;
	
	if(mysql_num_rows($fetch_unweaned_piglets) > 0){
		//$pigletsConsumption = 0;
		while($piglets_row = mysql_fetch_array($fetch_unweaned_piglets)){
			$swine_id = $piglets_row['swine_id'];

			//vaccine consumption
			/*$vaccination_of_piglets = mysql_fetch_array(mysql_query("SELECT sum(cost * v_dosage) from tbl_vaccine_pig where swine_id='$swine_id' and company_id='$company_id' and status=0 "));
			
			//medicine consumption
			$medication_of_piglets = mysql_fetch_array(mysql_query("SELECT sum(cost * amount) from tbl_medication_pig where swine_id='$swine_id' and company_id='$company_id' and status=0  "));
			
			// feeding
			$feeding_of_piglets = mysql_fetch_array(mysql_query("SELECT sum(cost * quantity) from tbl_feeding where company_id='$company_id' and swine_id='$swine_id' and include_to_piglet_cost=0 "));

			$vaccination_consumption += $vaccination_of_piglets[0];
			$medication_consumption += $medication_of_piglets[0];
			$feeding_consumption += $feeding_of_piglets[0];*/
			$total_cost += costPerPig($swine_id);
			
		}
		
	}else{
		//$vaccination_consumption = 0;
		//$medication_consumption = 0;
		//$feeding_consumption = 0;

		$total_cost = 0;

	}
	
	return $total_cost; //$vaccination_consumption + $medication_consumption + $feeding_consumption;
	
	
}

// // inventory from database
// function getCurrentQtyByPackaging($product_id, $warehouse_id, $inventory_date, $packaging_id)
// {
// 	$company_id = $_SESSION['system']['company_id'];
// 	$branch_id = get_branch();

// 	$getQTYbypackage_add = mysql_fetch_array(mysql_query("SELECT sum(qty) FROM tbl_inventory_add WHERE company_id = '$company_id' AND branch_id = '$branch_id' AND prod_id = '$product_id' AND warehouse_id = '$warehouse_id' AND package_id = '$packaging_id' AND inv_date <= '$inventory_date'"));

// 	$getQTYbypackage_deduct = mysql_fetch_array(mysql_query("SELECT sum(qty) FROM tbl_inventory_deduct WHERE company_id = '$company_id' AND branch_id = '$branch_id' AND prod_id = '$product_id' AND warehouse_id = '$warehouse_id' AND package_id = '$packaging_id' AND inv_date <= '$inventory_date'"));

// 	$getQTYbypackage = $getQTYbypackage_add[0] - $getQTYbypackage_deduct[0];
// 	return $getQTYbypackage;
// }

function getCurrentQtyBAL($product_id, $warehouse_id, $inventory_date){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$excess = 0;
	$getProduct = mysql_query("SELECT * FROM tbl_productmaster WHERE product_id = '$product_id' AND ((company_id = '$company_id' AND branch_id = '$branch_id') OR (company_id = '0' AND branch_id = '0'))");
	while($getProdRows = mysql_fetch_assoc($getProduct)){
		$getPackageType = mysql_query("SELECT * FROM tbl_package WHERE ((company_id = '$company_id' AND branch_id = '$branch_id') OR (company_id = '0' AND branch_id = '0'))");
		while($getRows = mysql_fetch_assoc($getPackageType)){
			$productVal = getCurrentQtyByPackaging($getProdRows[product_id], $warehouse_id, $inventory_date, $getRows[package_id]);
			$qty += $getRows[qty] * $productVal;
		}
		$excess = getRemaining($getProdRows[product_id], $warehouse_id, $company_id, $branch_id);
	}

	return $qty + $excess;
}
// end inventory from database


// function getCurrentQty($product_id, $warehouse_id, $inventory_date){
// 	$company_id = $_SESSION['system']['company_id'];
// 	$branch_id = get_branch();

// 	$excess = 0;
// 	$getProduct = mysql_query("SELECT * FROM tbl_productmaster WHERE product_id = '$product_id' AND ((company_id = '$company_id' AND branch_id = '$branch_id') OR (company_id = '0' AND branch_id = '0'))");
// 	while($getProdRows = mysql_fetch_assoc($getProduct)){
// 		$getPackageType = mysql_query("SELECT * FROM tbl_package WHERE ((company_id = '$company_id' AND branch_id = '$branch_id') OR (company_id = '0' AND branch_id = '0'))");
// 		while($getRows = mysql_fetch_assoc($getPackageType)){
// 			$productVal = getCurrentQtyByPackaging($getProdRows[product_id], $warehouse_id, $inventory_date, $getRows[package_id]);
// 			$qty += $getRows[qty] * $productVal;
// 		}
// 		$excess = getRemaining($getProdRows[product_id], $warehouse_id, $company_id, $branch_id);
// 	}

// 	return $qty + $excess;
// }


function getCurrentQty($product_id, $warehouse_id, $inventory_date){
	$currQTY = getCurrentQtyBAL($product_id, $warehouse_id, $inventory_date);
	return $currQTY;
}

// ------------------------------------------ CURRENT QTY BY PACKAGING IN ------------------------------------------ //
function getQty_rr_in($product_id, $warehouse_id, $inventory_date, $packaging_id)
{
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$sum_of_receiving = 0;
	$sum_of_rr = mysql_fetch_array(mysql_query("SELECT sum(rrD.quantity) from tbl_rr_header as rrH, tbl_rr_details as rrD where rrH.receiving_number = rrD.receiving_number and rrH.company_id='$company_id' and rrH.branch_id='$branch_id' and rrH.status='F' and rrH.date_added <= '$inventory_date' and rrD.status = 'F' and rrD.warehouse_id = '$warehouse_id' and rrD.packaging_id='$packaging_id' and rrD.product_id='$product_id'"));
	$sum_of_receiving = $sum_of_rr[0];


	// $total_st_bldg_to_warehouse_broiler = 0;
	// $st_bldg_to_warehouse_qty_broiler = mysql_fetch_array(mysql_query("SELECT SUM(stDb.qty) from tbl_stock_transfer_bldg_warehouse_eggs as stHb, tbl_std_bldg_warehouse_eggs as stDb where stHb.ref_id = stDb.ref_id and stHb.company_id='$company_id' and stHb.branch_id= '$branch_id' and stHb.warehouse_id='$warehouse_id' and stHb.status='F' and stDb.status = 'F' and stDb.product_id = '$product_id' and stDb.packaging_id='$packaging_id' and stDb.date <= '$inventory_date'"));
	// $total_st_bldg_to_warehouse_broiler = $st_bldg_to_warehouse_qty_broiler[0];


	 //+ $total_stock_transfer[0] + $stock_destination_pc[0] + $total_SR + $job_order_new_inv[0] + $pcs + $total_st_bldg_to_warehouse + $total_st_bldg_to_warehouse_broiler + $fetch_bb[0] + $total_st_bldg_to_warehouse_eggs + $total_carcass_weight[0] + $total_inventory_adjustment[0];

	
		$summary = $sum_of_receiving;
	
	

	return $summary;
}


function getQty_st_in($product_id, $warehouse_id, $inventory_date, $packaging_id)
{
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$product_code =  mysql_fetch_array(mysql_query("SELECT product_code FROM `tbl_productmaster` where (company_id='$company_id' OR company_id='0') and product_id='$product_id'"));

	$total_stock_transfer = mysql_fetch_array(mysql_query("SELECT sum(qty) from tbl_stock_transfer_details where product_code='$product_code[0]' and company_id='$company_id' and destination_branch='$branch_id' and destination_location='$warehouse_id' and status='R' and packaging_id='$packaging_id' and `date` < DATE_ADD('$inventory_date', INTERVAL 1 DAY)"));
	
	$summary = $total_stock_transfer[0];

	return $summary;
}

function getEggPcsToTray($pcs, $packaging_id){
	$qtyByTray = ($pcs/30);
	$trays = explode(".", $qtyByTray);
	$pc = $pcs - ($trays[0] * 30);
	if($packaging_id == 13){
		//pcs
		$display_qty = $pc;
	}
	if($packaging_id == 12){
		//tray
		$display_qty = $trays[0];
	}
	return $pcs;
}

function getQty_pe_in($product_id, $warehouse_id, $inventory_date, $packaging_id)
{
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$eggs_production = mysql_fetch_array(mysql_query("SELECT sum(qty) from tbl_inventory_entry_production_eggs where item='$product_id' and company_id='$company_id' and branch_id='$branch_id' and warehouse_id='$warehouse_id' and `date` <= '$inventory_date'"));
	if($packaging_id == "13"){
		$eggs = $eggs_production[0];
	}
	return $eggs;
}

function getQty_CE_in($product_id, $warehouse_id, $inventory_date, $packaging_id)
{
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$eggs_production = mysql_fetch_array(mysql_query("SELECT sum(quantity) from tbl_incubator_details_candling_eggs where product_id='$product_id' and company_id='$company_id' and branch_id='$branch_id' and warehouse_id='$warehouse_id' and `date_added` <= '$inventory_date'"));
	if($packaging_id == "13"){
		$eggs = $eggs_production[0];
	}
	return $eggs;
}

function getQty_pb_in($product_id, $warehouse_id, $inventory_date, $packaging_id)
{
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$eggs_production = mysql_fetch_array(mysql_query("SELECT sum(qty) from tbl_inventory_entry_production_broiler where item='$product_id' and company_id='$company_id' and branch_id='$branch_id' and warehouse_id='$warehouse_id' and `date` <= '$inventory_date'"));
	if($packaging_id == "13"){
		$pcs = $eggs_production[0];
	}
	$summary = $pcs;

	return $summary;
}

function getQty_sr_in($product_id, $warehouse_id, $inventory_date, $packaging_id)
{
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$total_SR = 0;
	$sr_row = mysql_fetch_array(mysql_query("SELECT SUM(quantity) from tbl_sales_return_details as srd, tbl_sales_return as sr where srd.sr_number = sr.sr_number and sr.status = 'F' and srd.product_id = '$product_id' and sr.company_id = '$company_id' and sr.branch_id = '$branch_id' and sr.warehouse_id='$warehouse_id' and sr.sr_date < DATE_ADD('$inventory_date', INTERVAL 1 DAY) and srd.packaging_id='$packaging_id'"));
	//$total_SR = $sr_row[0];
	$summary = $sr_row[0];

	return $summary;
}

function getQty_stp_in($product_id, $warehouse_id, $inventory_date, $packaging_id)
{
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$total_st_bldg_to_warehouse = 0;
	$st_bldg_to_warehouse_qty = mysql_fetch_array(mysql_query("SELECT SUM(stDP.qty) from tbl_stock_transfer_bldg_warehouse_pig as stHP, tbl_std_bldg_warehouse_pig as stDP where stHP.ref_id = stDP.ref_id and stHP.company_id='$company_id' and stHP.branch_id= '$branch_id' and stHP.warehouse_id='$warehouse_id' and stHP.status='F' and stDP.status = 'F' and stDP.product_id = '$product_id' and stDP.packaging_id='$packaging_id' and stDP.date <= '$inventory_date'"));
	//$total_st_bldg_to_warehouse = $st_bldg_to_warehouse_qty[0];
	$summary = $st_bldg_to_warehouse_qty[0];

	return $summary;
}

function getQty_ste_in($product_id, $warehouse_id, $inventory_date, $packaging_id)
{
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$total_st_bldg_to_warehouse_eggs = 0;
	$st_bldg_to_warehouse_qty_eggs = mysql_fetch_array(mysql_query("SELECT SUM(stDE.qty) from tbl_stock_transfer_bldg_warehouse_eggs as stHE, tbl_std_bldg_warehouse_eggs as stDE where stHE.ref_id = stDE.ref_id and stHE.company_id='$company_id' and stHE.branch_id= '$branch_id' and stHE.warehouse_id='$warehouse_id' and stHE.status='F' and stDE.status = 'F' and stDE.product_id = '$product_id' and stDE.packaging_id='$packaging_id' and stDE.date <= '$inventory_date'"));
	//$total_st_bldg_to_warehouse_eggs = $st_bldg_to_warehouse_qty_eggs[0];
	$summary = $st_bldg_to_warehouse_qty_eggs[0];

	return $summary;
}

function getQty_stb_in($product_id, $warehouse_id, $inventory_date, $packaging_id)
{
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$total_st_bldg_to_warehouse_broiler = 0;
	$st_bldg_to_warehouse_qty_broiler = mysql_fetch_array(mysql_query("SELECT SUM(stDB.qty) from tbl_stock_transfer_bldg_warehouse_broiler as stHB, tbl_std_bldg_warehouse_broiler as stDB where stHB.ref_id = stDB.ref_id and stHB.company_id='$company_id' and stHB.branch_id= '$branch_id' and stHB.warehouse_id='$warehouse_id' and stHB.status='F' and stDB.status = 'F' and stDB.product_id = '$product_id' and stDB.packaging_id='$packaging_id' and stDB.date <= '$inventory_date'"));
	//$total_st_bldg_to_warehouse_eggs = $st_bldg_to_warehouse_qty_eggs[0];
	$summary = $st_bldg_to_warehouse_qty_broiler[0];

	return $summary;
}

function getQty_stbp_return_in($product_id, $warehouse_id, $inventory_date, $packaging_id)
{
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$stock_transfer_return = mysql_fetch_array(mysql_query("SELECT SUM(std.return_qty) FROM tbl_stock_transfer_bldg as st, tbl_st_bldg_to_receive as std WHERE st.warehouse_id = '$warehouse_id' AND st.ref_id = std.ref_id AND st.company_id = std.company_id AND st.branch_id = std.branch_id AND (st.status = 'F' OR st.status = 'R') AND (std.status = 'F' OR std.status = 'R') AND std.product_id = '$product_id' AND std.packaging_id='$packaging_id' AND st.date <= '$inventory_date'"));
	
	return $stock_transfer_return[0];
}

function getQty_stbeggs_return_in($product_id, $warehouse_id, $inventory_date, $packaging_id)
{
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$stock_transfer_return = mysql_fetch_array(mysql_query("SELECT SUM(std.return_qty) FROM tbl_stock_transfer_bldg_eggs as st, tbl_st_bldg_to_receive_eggs as std WHERE st.warehouse_id = '$warehouse_id' AND st.ref_id = std.ref_id AND st.company_id = std.company_id AND st.branch_id = std.branch_id AND (st.status = 'F' OR st.status = 'R') AND (std.status = 'F' OR std.status = 'R') AND std.product_id = '$product_id' AND std.packaging_id='$packaging_id' AND st.date <= '$inventory_date'"));
	
	return $stock_transfer_return[0];
}

function getQty_jo_in($product_id, $warehouse_id, $inventory_date, $packaging_id)
{
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$job_order_new_inv = mysql_fetch_array(mysql_query("SELECT sum(package_type_qty) FROM `tbl_joborder_header_feeds` WHERE fin_product='$product_id' AND status='F' and location='$warehouse_id' AND branch_id='$branch_id' AND company_id='$company_id' AND datefinished <= '$inventory_date' and package_type='$packaging_id' "));
	
	$summary = $job_order_new_inv[0];
	
	return $summary;
}

function getQty_ai_semen_prod_in($product_id, $warehouse_id, $inventory_date, $packaging_id)
{
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$aip_total = mysql_fetch_array(mysql_query("SELECT sum(actual_output) FROM `tbl_ai_semen_production_pig` WHERE finish_product_id='$product_id' AND status='F' and output_warehouse_id='$warehouse_id' AND branch_id='$branch_id' AND company_id='$company_id' AND date_finished <= '$inventory_date' and packaging_id='$packaging_id' "));
	$summary = $aip_total[0];

	return $summary;
}

function getQty_pc_in($product_id, $warehouse_id, $inventory_date, $packaging_id)
{
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$stock_destination_pc = mysql_fetch_array(mysql_query("SELECT sum(convert_to_qty) from tbl_product_conversion where convert_to_id='$product_id' and company_id='$company_id' and branch_id='$branch_id' and warehouse_id='$warehouse_id' and status='F' and `date` < DATE_ADD('$inventory_date', INTERVAL 1 DAY) and packaging_id_converted_to='$packaging_id' and flock_convert_status='0'"));

	$stock_destination_pc_flock = mysql_fetch_array(mysql_query("SELECT sum(convert_to_qty) from tbl_product_conversion where convert_to_id='$product_id' and company_id='$company_id' and branch_id='$branch_id' and warehouse_id='$warehouse_id' and status='F' and `date` < DATE_ADD('$inventory_date', INTERVAL 1 DAY) and packaging_id_converted_to='$packaging_id' and flock_convert_status='1'"));

	$total = $stock_destination_pc[0] + $stock_destination_pc_flock[0];

	$summary = $total;

	return $summary;
}

function getQty_bb_in($product_id, $warehouse_id, $inventory_date, $packaging_id)
{
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	//FROM BB (IN)
	$fetch_bb = mysql_fetch_array(mysql_query("SELECT sum(qty) FROM `tbl_beginning_balance` WHERE product_id='$product_id'  AND `date` <= '$inventory_date' AND company_id='$company_id' AND warehouse_id='$warehouse_id' AND  branch_id='$branch_id' AND package_id='$packaging_id' AND status='F'"));

	$summary = $fetch_bb[0];

	return $summary;
}

function getQty_cc_in($product_id, $warehouse_id, $inventory_date, $packaging_id)
{
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	// from carcass swine
	$total_carcass_weight = mysql_fetch_array(mysql_query("SELECT sum(c_weight) from tbl_carcass_details_pig as cd, tbl_carcass_header_pig as ch where ch.company_id='$company_id' and ch.branch_id='$branch_id' and ch.warehouse_id='$warehouse_id' and ch.date_added <= '$inventory_date' and ch.status='F' and ch.reference_number=cd.reference_number and cd.product_id='$product_id' and cd.packaging_id='$packaging_id'"));
	$summary = $total_carcass_weight[0];

	return $summary;
}

function getQty_ia_in($product_id, $warehouse_id, $inventory_date, $packaging_id)
{
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	// FROM INVENTORY ADJUSTMENT
	$total_inventory_adjustment = mysql_fetch_array(mysql_query("SELECT sum(iad.qty) from tbl_inventory_adjustment_details as iad, tbl_inventory_adjustment_header as iah where iah.company_id='$company_id' and iah.branch_id='$branch_id' and iah.warehouse_id='$warehouse_id' and iad.qty > 0 and iah.date < DATE_ADD('$inventory_date', INTERVAL 1 DAY) and iah.status='F' and iah.inv_adj_num=iad.inv_adj_num and iad.product_id='$product_id' and iad.package_id ='$packaging_id'"));
	
	$summary = $total_inventory_adjustment[0];

	return $summary;
}

// ------------------------------------------ CURRENT QTY BY PACKAGING IN END ------------------------------------------ //

// ------------------------------------------ CURRENT QTY BY PACKAGING OUT ------------------------------------------ //

function getQty_gme_out($product_id, $warehouse_id, $inventory_date, $packaging_id)
{
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	//growing
	$growing = mysql_fetch_array(mysql_query("SELECT sum(previous_qty) FROM tbl_growing_module_eggs WHERE stock_id='$product_id' AND company_id='$company_id' AND branch_id='$branch_id' AND tag = 'FRR' AND warehouse_id='$warehouse_id' AND growing_date < DATE_ADD('$inventory_date', INTERVAL 1 DAY)"));
	if($packaging_id == '34'){
		$total_growing = $growing[0];
	}
	$summary = $total_growing;

	return $summary;
}

function getQty_gmb_out($product_id, $warehouse_id, $inventory_date, $packaging_id)
{
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	//growing
	$growing = mysql_fetch_array(mysql_query("SELECT sum(previous_qty) FROM tbl_growing_module_broiler WHERE stock_id='$product_id' AND company_id='$company_id' AND branch_id='$branch_id' AND tag = 'FRR' AND warehouse_id='$warehouse_id' AND growing_date < DATE_ADD('$inventory_date', INTERVAL 1 DAY)"));
	if($packaging_id == '34'){
		$total_growing = $growing[0];
	}
	$summary = $total_growing;

	return $summary;
}

function getQty_pe_out($product_id, $warehouse_id, $inventory_date, $packaging_id)
{
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	//production
	$production = mysql_fetch_array(mysql_query("SELECT sum(previous_qty) from tbl_production_eggs where stock_id='$product_id' and company_id='$company_id' and branch_id='$branch_id' and tag='FRR' and warehouse_id='$warehouse_id' and start_date < DATE_ADD('$inventory_date', INTERVAL 1 DAY)"));

	if($packaging_id == '35'){
		$total_production = $production[0];
	}
	$summary = $total_production;

	return $summary;
}

function getQty_dr_out($product_id, $warehouse_id, $inventory_date, $packaging_id)
{
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$fetch_delivery = mysql_fetch_array(mysql_query("SELECT SUM(dd.quantity) from tbl_dr_detail as dd, tbl_dr_header as dh where dd.delivery_number = dh.delivery_number and (dh.status = 'P' or dh.status = 'F') and dd.stock_id = '$product_id' and dh.company_id = '$company_id' and dh.branch_id = '$branch_id' and dh.warehouse_id='$warehouse_id' and dh.dr_date <= '$inventory_date' and dd.packaging_id='$packaging_id' "));
	$summary = $fetch_delivery[0];

	return $summary;
}
function getQty_sr_out($product_id, $warehouse_id, $inventory_date, $packaging_id)
{
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$fetch_delivery = mysql_fetch_array(mysql_query("SELECT SUM(sd.qty) from tbl_stock_released_details as sd, tbl_stock_released as sh where sd.delivery_number = sh.delivery_number and (sh.status = 'R' or sh.status = 'F') and sd.product_id = '$product_id' and sh.company_id = '$company_id' and sh.release_branch_id = '$branch_id' and sh.release_warehouse='$warehouse_id' and sh.released_date <= '$inventory_date' and sd.packaging_id='$packaging_id' "));
	$summary = $fetch_delivery[0];

	return $summary;
}


function getQty_ai_materials_out($product_id, $warehouse_id, $inventory_date, $packaging_id){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$sum_of_ai_prod = 0;
	$sum_of_aip = mysql_fetch_array(mysql_query("SELECT sum(aid.qty) from tbl_ai_semen_production_pig as aih, tbl_ai_semen_production_details_pig as aid where aih.aip_number = aih.aip_number and aih.company_id='$company_id' and aih.branch_id='$branch_id' and aih.status='F' and aih.aip_date <= '$inventory_date' and aid.status = 'F' and aih.source_warehouse_id = '$warehouse_id' and aid.packaging_id='$packaging_id' and aid.product_id='$product_id'"));
	$sum_of_ai_prod = $sum_of_aip[0];

	return $sum_of_ai_prod;

}

function getQty_ai_pkg_out($product_id, $warehouse_id, $inventory_date, $packaging_id){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	// packaging id

	$sum_of_ai_pkg = 0;
	$sum_of_aip_pkg = mysql_fetch_array(mysql_query("SELECT sum(CASE WHEN material_id1='$product_id' THEN actual_output ELSE 0 end) + sum(CASE WHEN material_id2='$product_id' THEN actual_output ELSE 0 end) + sum(CASE WHEN material_id3='$product_id' THEN actual_output ELSE 0 end) from tbl_ai_semen_production_pig where company_id='$company_id' and branch_id='$branch_id' and status='F' and aip_date <= '$inventory_date' and source_warehouse_id = '$warehouse_id' and material_pkg_id='$packaging_id' "));
	$sum_of_ai_pkg = $sum_of_aip_pkg[0];

	return $sum_of_ai_pkg;

}

function getQty_st_out($product_id, $warehouse_id, $inventory_date, $packaging_id)
{
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$product_code =  mysql_fetch_array(mysql_query("SELECT product_code FROM `tbl_productmaster` where (company_id='$company_id' OR company_id='0') and product_id='$product_id'"));

	$total_with_request = mysql_fetch_array(mysql_query("SELECT sum(qty) from tbl_stock_transfer_details where product_code='$product_code[0]' and company_id='$company_id' and source_location='$warehouse_id' and transfer_type != '1' and (status='F' or status='R') and source_packaging_id='$packaging_id' and `date` < DATE_ADD('$inventory_date', INTERVAL 1 DAY)"));
	
	$total_without_request = mysql_fetch_array(mysql_query("SELECT sum(qty-return_qty) from tbl_stock_transfer_to_receive where product_code='$product_code[0]' and company_id='$company_id' and source_location='$warehouse_id' and (status='F' or status='R') and source_packaging_id='$packaging_id' and `date` < DATE_ADD('$inventory_date', INTERVAL 1 DAY)"));
	
	$summary = $total_without_request[0]+$total_with_request[0];

	return $summary;
}

function getQty_pc_out($product_id, $warehouse_id, $inventory_date, $packaging_id)
{
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$stock_source_pc = mysql_fetch_array(mysql_query("SELECT sum(original_item_qty) from tbl_product_conversion where original_item_id='$product_id' and company_id='$company_id' and branch_id='$branch_id' and warehouse_id='$warehouse_id' and status='F' and `date` < DATE_ADD('$inventory_date', INTERVAL 1 DAY) and packaging_id_original_item='$packaging_id' and flock_convert_status='0'"));

	$stock_source_pc_flock = mysql_fetch_array(mysql_query("SELECT sum(original_item_qty) from tbl_product_conversion where original_item_id='$product_id' and company_id='$company_id' and branch_id='$branch_id' and warehouse_id='$warehouse_id' and status='F' and `date` < DATE_ADD('$inventory_date', INTERVAL 1 DAY) and packaging_id_original_item='$packaging_id' and flock_convert_status='2'"));

	$summary = $stock_source_pc[0] + $stock_source_pc_flock[0];

	return $summary;
}

function getQty_pc_addmaterial_out($product_id, $warehouse_id, $inventory_date, $packaging_id)
{
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$stock_source_pc = mysql_fetch_array(mysql_query("SELECT sum(convert_to_qty) from tbl_product_conversion where material_id='$product_id' and company_id='$company_id' and branch_id='$branch_id' and warehouse_id='$warehouse_id' and status='F' and `date` < DATE_ADD('$inventory_date', INTERVAL 1 DAY) and material_packaging_id='17' "));
	$summary = $stock_source_pc[0];

	return $summary;
}

function getQty_pr_out($product_id, $warehouse_id, $inventory_date, $packaging_id)
{
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$pr_qty = mysql_fetch_array(mysql_query("SELECT sum(prD.qty) from tbl_purchase_return_header as prH, tbl_purchase_return_details as prD where prH.pr_num = prD.pr_num and prH.company_id='$company_id' and prH.branch_id='$branch_id' and prH.status='F' and prH.date < DATE_ADD('$inventory_date', INTERVAL 1 DAY) and prD.product_id='$product_id' and prD.warehouse_id='$warehouse_id' and prD.status = 'F' and prD.packaging_id='$packaging_id'"));
	$summary = $pr_qty[0];

	return $summary;
}

function getQty_st_w_b_out($product_id, $warehouse_id, $inventory_date, $packaging_id){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$total_without_receiving = mysql_fetch_array(mysql_query("SELECT SUM(std.qty) FROM tbl_stock_transfer_bldg as st, tbl_st_bldg_details as std WHERE st.ref_id = std.ref_id AND st.company_id = std.company_id AND st.branch_id = std.branch_id AND (st.status = 'F' OR st.status = 'R') AND (std.status = 'F' OR std.status = 'R') AND std.product_id = '$product_id' AND st.warehouse_id='$warehouse_id' AND std.transfer_type != '1' AND st.date <= '$inventory_date' AND std.packaging_id='$packaging_id'"));
	
	$total_with_receiving = mysql_fetch_array(mysql_query("SELECT SUM(std.qty) FROM tbl_stock_transfer_bldg as st, tbl_st_bldg_to_receive as std WHERE st.ref_id = std.ref_id AND st.company_id = std.company_id AND st.branch_id = std.branch_id AND (st.status = 'F' OR st.status = 'R') AND (std.status = 'F' OR std.status = 'R') AND std.product_id = '$product_id' AND st.warehouse_id='$warehouse_id' AND st.date <= '$inventory_date' AND std.packaging_id='$packaging_id'"));
	
	$summary = $total_without_receiving[0]+$total_with_receiving[0];

	return $summary;
}

function getQty_ste_out($product_id, $warehouse_id, $inventory_date, $packaging_id)
{
	/*$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$fetch_stock_bldg_eggs = mysql_fetch_array(mysql_query("SELECT SUM(stde.qty) from tbl_stock_transfer_bldg_eggs as ste, tbl_st_bldg_details_eggs as stde where ste.ref_id = stde.ref_id and ste.company_id = stde.company_id and ste.branch_id = stde.branch_id and ste.status = 'F' and stde.status = 'F' and stde.product_id = '$product_id' and ste.warehouse_id='$warehouse_id' and ste.date <= '$inventory_date' and stde.packaging_id='$packaging_id' "));
	$summary = $fetch_stock_bldg_eggs[0];

	return $summary;*/
	
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$total_without_receiving = mysql_fetch_array(mysql_query("SELECT SUM(std.qty) FROM tbl_stock_transfer_bldg_eggs as st, tbl_st_bldg_details_eggs as std WHERE st.ref_id = std.ref_id AND st.company_id = std.company_id AND st.branch_id = std.branch_id AND (st.status = 'F' OR st.status = 'R') AND (std.status = 'F' OR std.status = 'R') AND std.product_id = '$product_id' AND st.warehouse_id='$warehouse_id' AND std.transfer_type != '1' AND st.date <= '$inventory_date' AND std.packaging_id='$packaging_id'"));
	
	$total_with_receiving = mysql_fetch_array(mysql_query("SELECT SUM(std.qty) FROM tbl_stock_transfer_bldg_eggs as st, tbl_st_bldg_to_receive_eggs as std WHERE st.ref_id = std.ref_id AND st.company_id = std.company_id AND st.branch_id = std.branch_id AND (st.status = 'F' OR st.status = 'R') AND (std.status = 'F' OR std.status = 'R') AND std.product_id = '$product_id' AND st.warehouse_id='$warehouse_id' AND st.date <= '$inventory_date' AND std.packaging_id='$packaging_id'"));
	
	$summary = $total_without_receiving[0]+$total_with_receiving[0];

	return $summary;
}

function getQty_stb_out($product_id, $warehouse_id, $inventory_date, $packaging_id)
{
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$fetch_stock_bldg_broiler = mysql_fetch_array(mysql_query("SELECT SUM(stdb.qty) from tbl_stock_transfer_bldg_broiler as stb, tbl_st_bldg_details_broiler as stdb where stb.ref_id = stdb.ref_id and stb.company_id = stdb.company_id and stb.branch_id = stdb.branch_id and (stb.status = 'F' or stb.status = 'R') and (stdb.status = 'F' or stdb.status = 'R') and stdb.product_id = '$product_id' and stb.warehouse_id='$warehouse_id' and stb.date <= '$inventory_date' and stdb.packaging_id='$packaging_id' "));
	$summary = $fetch_stock_bldg_broiler[0];

	return $summary;
}

function getQty_jo_rm_out($product_id, $warehouse_id, $inventory_date, $packaging_id)
{
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$get_product_category = getProdCat($product_id);

	$package_id = mysql_fetch_array(mysql_query("SELECT pkg.qty AS lowest_unit, pkg.package_id AS package_id
	FROM tbl_package AS pkg, tbl_product_category AS pc
	WHERE pkg.qty = (SELECT MIN(pkg.qty)
	FROM tbl_package AS pkg, tbl_product_category AS pc
	WHERE pkg.category_id = pc.product_categ_id AND ((pkg.company_id = '$company_id' AND pkg.branch_id = '$branch_id') OR (pkg.company_id = 0 
	AND pkg.branch_id = 0)) AND pkg.visibility_status = 1 AND pc.product_categ_id = '$get_product_category') AND pkg.category_id = pc.product_categ_id AND ((pkg.company_id = '$company_id' AND pkg.branch_id = '$branch_id') OR (pkg.company_id = 0 
	AND pkg.branch_id = 0)) AND pkg.visibility_status = 1 AND pc.product_categ_id = '$get_product_category'"));
	$lowest_unit = $package_id[package_id];

	if($packaging_id == $lowest_unit){
		$joHeader = mysql_fetch_array(mysql_query("SELECT SUM(joD.quantity * joH.num_of_batches) FROM tbl_joborder_header_feeds AS joH, tbl_joborder_details_feeds AS joD WHERE joH.joborder_header_id = joD.joborder_header_id AND joH.status = 'F' AND joH.company_id = '$company_id' AND joH.branch_id = '$branch_id' AND joH.datefinished <= '$inventory_date' and joH.warehouse_id='$warehouse_id' AND joD.material = '$product_id'"));

		$summary = $joHeader[0];
		return $summary;
	}

	// //-- job order deductions raw materials --//
	// $joHeader = mysql_query("SELECT joborder_header_id, num_of_batches FROM tbl_joborder_header_feeds WHERE status = 'F' AND company_id = '$company_id' AND branch_id = '$branch_id' AND datefinished <= '$inventory_date' and warehouse_id='$warehouse_id'");
	// $joRM = 0;
	// while($johRow = mysql_fetch_assoc($joHeader)){
	// 	$joDetail = mysql_query("SELECT quantity FROM tbl_joborder_details_feeds WHERE joborder_header_id = '$johRow[joborder_header_id]' AND material = '$product_id'");
	// 	$jodRow = mysql_fetch_assoc($joDetail);
	// 			$joDqty = $jodRow[quantity];
	// 			$numOfBatches = $johRow[num_of_batches];
	// 		if($packaging_id == '31'){
	// 			$joRM += $joDqty * $numOfBatches;
	// 		}
	// }
	
	
}

// function getQty_ai_pig_out($product_id, $warehouse_id, $inventory_date, $packaging_id)
// {
// 	$company_id = $_SESSION['system']['company_id'];
// 	$branch_id = get_branch();


// 	$query_header =mysql_query("SELECT * FROM tbl_ai_semen_production_header_pig AS joH, tbl_ai_semen_production_details_pig AS joD WHERE joH.joborder_header_id = joD.joborder_header_id AND joH.status = 'F' AND joH.company_id = '$company_id' AND joH.branch_id = '$branch_id' AND joH.datefinished <= '$inventory_date' and joH.warehouse_id='$warehouse_id' AND joD.material = '$product_id'");

// 	while($joHeader_ = mysql_fetch_array($query_header))
// 	{
// 		// $categ_id = getBulkData("product_categ_id","tbl_productmaster","product_id = '$joHeader_[material]' and branch_id='$branch_id' and company_id='$company_id'");
// 		// $pkg_id = getBulkData("package_id","tbl_package","category_id = '$categ_id[product_categ_id]' and ((branch_id=0 and company_id=0) OR (branch_id='$branch_id' and company_id='$company_id')) ORDER BY qty DESC LIMIT 1");

// 		//$drTOTAL = getCurrentQtyByPackaging($joHeader_['material'], $joHeader_['warehouse_id'], $joHeader_['datefinished'], $pkg_id['package_id']);

// 		// if($drTOTAL > 0){

// 		// 92 PACKAGING ID BY PC
// 			if($packaging_id == $joHeader_[unit]){
// 				$joHeader = mysql_fetch_array(mysql_query("SELECT SUM(joD.quantity * joH.num_of_batches) FROM tbl_ai_semen_production_header_pig AS joH, tbl_ai_semen_production_details_pig AS joD WHERE joH.joborder_header_id = joD.joborder_header_id AND joH.status = 'F' AND joH.company_id = '$company_id' AND joH.branch_id = '$branch_id' AND joH.datefinished <= '$inventory_date' and joH.warehouse_id='$warehouse_id' AND joD.material = '$product_id'"));

// 				$summary = $joHeader[0];
// 				return $summary;
// 			}
// 		// }
// 	}

// }

// function getQty_ai_pkg_out($product_id, $warehouse_id, $inventory_date, $packaging_id)
// {
// 	$company_id = $_SESSION['system']['company_id'];
// 	$branch_id = get_branch();

// 	$pkm_1 = 0;
// 	$pkm_2 = 0;
// 	$pkm_3 = 0;

// 	//-- job order deductions packaging materials --//
// 	$PKM1 = getBulkData("SUM(package_type_qty) AS total","tbl_ai_semen_production_header_pig","status = 'F' AND company_id = '$company_id' AND branch_id = '$branch_id' AND datefinished <= '$inventory_date' AND pk_1 = '$product_id' AND warehouse_id='$warehouse_id'");

// 	$PKM2 = getBulkData("SUM(package_type_qty) AS total2","tbl_ai_semen_production_header_pig","status = 'F' AND company_id = '$company_id' AND branch_id = '$branch_id' AND datefinished <= '$inventory_date' AND pk_2 = '$product_id' AND warehouse_id='$warehouse_id'");

// 	$PKM3 = getBulkData("SUM(package_type_qty) AS total3","tbl_ai_semen_production_header_pig","status = 'F' AND company_id = '$company_id' AND branch_id = '$branch_id' AND datefinished <= '$inventory_date' AND pk_3 = '$product_id' AND warehouse_id='$warehouse_id'");
	
// 	$pkm_1 = $PKM1[total];
// 	$pkm_2 = $PKM2[total2];
// 	$pkm_3 = $PKM3[total3];

// 	if($packaging_id == '17'){
// 		$pkm = $pkm_1 + $pkm_2 + $pkm_3;
// 	}
// 	$summary = $pkm;

// 	return $summary;
// }

function getQty_jo_pkg_out($product_id, $warehouse_id, $inventory_date, $packaging_id)
{
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$pkm_1 = 0;
	$pkm_2 = 0;
	$pkm_3 = 0;

	//-- job order deductions packaging materials --//
	$PKM1 = getBulkData("SUM(package_type_qty) AS total","tbl_joborder_header_feeds","status = 'F' AND company_id = '$company_id' AND branch_id = '$branch_id' AND datefinished <= '$inventory_date' AND pk_1 = '$product_id' AND warehouse_id='$warehouse_id'");

	$PKM2 = getBulkData("SUM(package_type_qty) AS total2","tbl_joborder_header_feeds","status = 'F' AND company_id = '$company_id' AND branch_id = '$branch_id' AND datefinished <= '$inventory_date' AND pk_2 = '$product_id' AND warehouse_id='$warehouse_id'");

	$PKM3 = getBulkData("SUM(package_type_qty) AS total3","tbl_joborder_header_feeds","status = 'F' AND company_id = '$company_id' AND branch_id = '$branch_id' AND datefinished <= '$inventory_date' AND pk_3 = '$product_id' AND warehouse_id='$warehouse_id'");
	
	$pkm_1 = $PKM1[total];
	$pkm_2 = $PKM2[total2];
	$pkm_3 = $PKM3[total3];

	if($packaging_id == '17'){
		$pkm = $pkm_1 + $pkm_2 + $pkm_3;
	}
	$summary = $pkm;

	return $summary;
}

function getQty_ia_out($product_id, $warehouse_id, $inventory_date, $packaging_id)
{
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$total_inventory_adjustment = mysql_fetch_array(mysql_query("SELECT sum(iad.qty) from tbl_inventory_adjustment_details as iad, tbl_inventory_adjustment_header as iah where iah.company_id='$company_id' and iah.branch_id='$branch_id' and iah.warehouse_id='$warehouse_id' and iad.qty < 0 and iah.date < DATE_ADD('$inventory_date', INTERVAL 1 DAY) and iah.status='F' and iah.inv_adj_num=iad.inv_adj_num and iad.product_id='$product_id' and iad.package_id ='$packaging_id'"));
	$summary = abs($total_inventory_adjustment[0]);

	return $summary;
}

function getQty_spoilage_out($product_id, $warehouse_id, $inventory_date, $packaging_id)
{
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$spoilage = mysql_fetch_array(mysql_query("SELECT sum(quantity) from tbl_spoiled_eggs where product_id='$product_id' and company_id='$company_id' and branch_id='$branch_id' and warehouse_id='$warehouse_id' and `date` < DATE_ADD('$inventory_date', INTERVAL 1 DAY) and packaging_id='$packaging_id' "));
	$summary = $spoilage[0];

	return $summary;
}

function getQty_csbs_out($product_id, $warehouse_id, $inventory_date, $packaging_id)
{
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$consumables = mysql_fetch_array(mysql_query("SELECT sum(qty) from tbl_consumables_details where product_id='$product_id' and company_id='$company_id' and branch_id='$branch_id' and warehouse_id='$warehouse_id' and `c_date` < DATE_ADD('$inventory_date', INTERVAL 1 DAY) and package_id='$packaging_id'"));
	$summary = $consumables[0];

	return $summary;
}
function getQty_cip_out($product_id, $warehouse_id, $inventory_date, $packaging_id){

	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$cip = mysql_fetch_array(mysql_query("SELECT sum(product_qty) from tbl_construction_in_progress_detail as d , tbl_construction_in_progress as h where d.cip_id = h.cip_id AND d.product_id='$product_id' and h.company_id='$company_id' and h.branch_id='$branch_id' and d.source_warehouse='$warehouse_id' and d.transfer_date < DATE_ADD('$inventory_date', INTERVAL 1 DAY) and d.packaging_id='$packaging_id'"));

	$summary = $cip[0];

	return $summary;
}

function getQty_spoilage_out_broiler($product_id, $warehouse_id, $inventory_date, $packaging_id)
{
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$spoilage = mysql_fetch_array(mysql_query("SELECT sum(quantity) from tbl_spoiled_broiler where product_id='$product_id' and company_id='$company_id' and branch_id='$branch_id' and warehouse_id='$warehouse_id' and `date` < DATE_ADD('$inventory_date', INTERVAL 1 DAY) and packaging_id='$packaging_id' "));
	$summary = $spoilage[0];

	return $summary;
}



// ------------------------------------------ CURRENT QTY BY PACKAGING OUT END ------------------------------------------ //

// INVENTORY IN BY PACKAGING //
function inventory_IN($product_id,$warehouse_id, $inventory_date, $packaging_id)
{
	//QTY IN
	$qty_RR_IN = getQty_rr_in($product_id, $warehouse_id, $inventory_date, $packaging_id);
	$qty_ST_IN = getQty_st_in($product_id, $warehouse_id, $inventory_date, $packaging_id);
	$qty_PE_IN = getQty_pe_in($product_id, $warehouse_id, $inventory_date, $packaging_id);
	$qty_PB_IN = getQty_pb_in($product_id, $warehouse_id, $inventory_date, $packaging_id);
	$qty_SR_IN = getQty_sr_in($product_id, $warehouse_id, $inventory_date, $packaging_id);
	$qty_STP_IN = getQty_stp_in($product_id, $warehouse_id, $inventory_date, $packaging_id);
	$qty_STE_IN = getQty_ste_in($product_id, $warehouse_id, $inventory_date, $packaging_id);
	$qty_JO_IN = getQty_jo_in($product_id, $warehouse_id, $inventory_date, $packaging_id);
	$qty_AI_IN_PIG = getQty_ai_semen_prod_in($product_id, $warehouse_id, $inventory_date, $packaging_id);
	$qty_PC_IN = getQty_pc_in($product_id, $warehouse_id, $inventory_date, $packaging_id);
	$qty_BB_IN = getQty_bb_in($product_id, $warehouse_id, $inventory_date, $packaging_id);
	$qty_CC_IN = getQty_cc_in($product_id, $warehouse_id, $inventory_date, $packaging_id);
	$qty_IA_IN = getQty_ia_in($product_id, $warehouse_id, $inventory_date, $packaging_id);
	$qty_STB_IN = getQty_stb_in($product_id, $warehouse_id, $inventory_date, $packaging_id);
	$qty_STB_return = getQty_stbp_return_in($product_id, $warehouse_id, $inventory_date, $packaging_id);
	$getQty_stbeggs_return_in = getQty_stbeggs_return_in($product_id, $warehouse_id, $inventory_date, $packaging_id);
	$qty_CANDLING_EGG_IN = getQty_CE_in($product_id, $warehouse_id, $inventory_date, $packaging_id);

	$current_IN = ($qty_RR_IN + $qty_ST_IN + $qty_PE_IN + $qty_SR_IN + $qty_STP_IN + $qty_STE_IN + $qty_JO_IN + $qty_AI_IN_PIG + $qty_PC_IN + $qty_BB_IN + $qty_CC_IN + $qty_IA_IN + $qty_STB_IN + $qty_PB_IN + $qty_STB_return+$getQty_stbeggs_return_in + $qty_CANDLING_EGG_IN);

	return $current_IN;
}

// INVENTORY OUT BY PACKAGING //
function inventory_OUT($product_id,$warehouse_id, $inventory_date, $packaging_id)
{
	//QTY out
	$qty_GME_OUT = getQty_gme_out($product_id, $warehouse_id, $inventory_date, $packaging_id);
	$qty_GMB_OUT = getQty_gmb_out($product_id, $warehouse_id, $inventory_date, $packaging_id);
	$qty_PE_OUT = getQty_pe_out($product_id, $warehouse_id, $inventory_date, $packaging_id);
	$qty_DR_OUT = getQty_dr_out($product_id, $warehouse_id, $inventory_date, $packaging_id);
	$qty_ST_OUT = getQty_st_out($product_id, $warehouse_id, $inventory_date, $packaging_id);
	$qty_PC_OUT = getQty_pc_out($product_id, $warehouse_id, $inventory_date, $packaging_id);
	$qty_PC_ADDMATERIAL_OUT = getQty_pc_addmaterial_out($product_id, $warehouse_id, $inventory_date, $packaging_id);
	$qty_PR_OUT = getQty_pr_out($product_id, $warehouse_id, $inventory_date, $packaging_id);
	$qty_ST_W_B_OUT = getQty_st_w_b_out($product_id, $warehouse_id, $inventory_date, $packaging_id);
	$qty_STE_OUT = getQty_ste_out($product_id, $warehouse_id, $inventory_date, $packaging_id);
	$qty_JO_RM_OUT = getQty_jo_rm_out($product_id, $warehouse_id, $inventory_date, $packaging_id);
	$qty_JO_PKG_OUT = getQty_jo_pkg_out($product_id, $warehouse_id, $inventory_date, $packaging_id);

	$qty_AI_PIG_OUT = getQty_ai_materials_out($product_id, $warehouse_id, $inventory_date, $packaging_id);
	$qty_AI_PKG_OUT = getQty_ai_pkg_out($product_id, $warehouse_id, $inventory_date, $packaging_id);
	
	// $qty_AI_PIG_OUT = getQty_ai_pig_out($product_id, $warehouse_id, $inventory_date, $packaging_id);
	// $qty_AI_PKG_OUT = getQty_ai_pkg_out($product_id, $warehouse_id, $inventory_date, $packaging_id);
	$qty_IA_OUT = getQty_ia_out($product_id, $warehouse_id, $inventory_date, $packaging_id);
	$qty_SPOILAGE_OUT = getQty_spoilage_out($product_id, $warehouse_id, $inventory_date, $packaging_id);
	$qty_SPOILAGE_BROILER_OUT = getQty_spoilage_out_broiler($product_id, $warehouse_id, $inventory_date, $packaging_id);
	$qty_STB_OUT = getQty_stb_out($product_id, $warehouse_id, $inventory_date, $packaging_id);
	$qty_CSBS_OUT = getQty_csbs_out($product_id, $warehouse_id, $inventory_date, $packaging_id);
	$qty_CIP_OUT = getQty_cip_out($product_id, $warehouse_id, $inventory_date, $packaging_id);
	$qty_SR_OUT = getQty_sr_out($product_id, $warehouse_id, $inventory_date, $packaging_id);
	$current_OUT = ($qty_GME_OUT + $qty_PE_OUT + $qty_DR_OUT + $qty_ST_OUT + $qty_PC_OUT + $qty_PC_ADDMATERIAL_OUT + $qty_PR_OUT + $qty_ST_W_B_OUT + $qty_STE_OUT + $qty_JO_RM_OUT + $qty_AI_PIG_OUT + $qty_AI_PKG_OUT + $qty_JO_PKG_OUT + $qty_IA_OUT + $qty_SPOILAGE_OUT + $qty_SPOILAGE_BROILER_OUT + $qty_STB_OUT + $qty_GMB_OUT + $qty_CSBS_OUT + $qty_CIP_OUT+$qty_SR_OUT);

	return $current_OUT;
}

// --- GET CURRENT QTY BY PACKAGE --- //
function getCurrentQtyByPackaging($product_id, $warehouse_id, $inventory_date, $packaging_id)
{	
	//$current_quantity = getCurrentQtyByPackaging_V2($product_id, $warehouse_id, $inventory_date, $packaging_id);
	$qtyIN = inventory_IN($product_id,$warehouse_id, $inventory_date, $packaging_id);
	$qtyOUT = inventory_OUT($product_id,$warehouse_id, $inventory_date, $packaging_id);
	$current_quantity = $qtyIN - $qtyOUT;

 	return $current_quantity;
}

function computeINVLedger2($company_id, $branch_id, $warehouse_id, $date, $product_id, $package_id, $qty)
{
    
    $data = array(
    	'product_id' => $product_id,
        'company_id' => $company_id,
        'branch_id' => $branch_id,
        'packaging_id' => $package_id,
        'warehouse_id' => $warehouse_id,
        'com_quantity' => $qty,
        'inventory_date' => $date

    );
    $res = FM_INSERT_QUERY("tbl_inventory_qty",$data);

    //accum_ahead_updater($company_id, $branch_id, $warehouse_id, $date, $product_id, $package_id, $qty, $status ,$allow_equals);

    return $res;
}

function computeINVLedger($company_id, $branch_id, $warehouse_id, $date, $product_id, $package_id, $qty, $status)
{
    $ifExist = FM_SELECT_QUERY("count(product_id)","tbl_inventory_qty","company_id = '$company_id' AND branch_id = '$branch_id' AND warehouse_id = '$warehouse_id' AND inventory_date = '$date' AND product_id = '$product_id' AND packaging_id = '$package_id'");

    if($ifExist[0] < 1){
        // insert
        $current_qty = accum_qty_per_date($company_id, $branch_id, $warehouse_id, $date, $product_id, $package_id);
        $newComQty = ($status == "IN")?$current_qty + $qty:$current_qty - $qty;
        $data = array(
        	'product_id' => $product_id,
            'company_id' => $company_id,
            'branch_id' => $branch_id,
            'packaging_id' => $package_id,
            'warehouse_id' => $warehouse_id,
            'com_quantity' => $newComQty,
            'inventory_date' => $date
   
        );
        $res = FM_INSERT_QUERY("tbl_inventory_qty",$data);
    	$allow_equals = 0;
    }else{
    	$allow_equals = 1;
    }

    accum_ahead_updater($company_id, $branch_id, $warehouse_id, $date, $product_id, $package_id, $qty, $status ,$allow_equals);

    return $res;
}

function accum_ahead_updater($company_id, $branch_id, $warehouse_id, $date, $product_id, $package_id, $qty, $status , 
    	$allow_equals){
		$op = ($allow_equals == 1) ? ">=":">";

	 	$newComQty = ($status == "IN")?"com_quantity + $qty":"com_quantity - $qty";
        $res = mysql_query("UPDATE tbl_inventory_qty set prev_quantity = com_quantity ,com_quantity = $newComQty WHERE company_id = '$company_id' AND branch_id = '$branch_id' AND warehouse_id = '$warehouse_id' AND inventory_date $op '$date' AND product_id = '$product_id' AND packaging_id = '$package_id'");

        return $res;
}

function accum_qty_per_date($company_id, $branch_id, $warehouse_id, $inventory_date, $product_id, $package_id)
{	
    // GET CURRENT QTY
    $getBalance = FM_SELECT_QUERY("com_quantity ","tbl_inventory_qty","company_id = '$company_id' AND branch_id = '$branch_id' AND warehouse_id = '$warehouse_id' AND inventory_date < '$inventory_date' AND product_id = '$product_id' AND packaging_id = '$package_id' ORDER BY inventory_date DESC LIMIT 1");
    $current_qty = $getBalance[0] * 1;

    return $current_qty;
}

// FOR INVENTORY VIEW
function current_qty_view_inv($company_id, $branch_id, $warehouse_id, $inventory_date, $product_id, $package_id)
{	
	$ifExist = FM_SELECT_QUERY("count(product_id), com_quantity","tbl_inventory_qty","company_id = '$company_id' AND branch_id = '$branch_id' AND warehouse_id = '$warehouse_id' AND inventory_date = '$inventory_date' AND product_id = '$product_id' AND packaging_id = '$package_id'");

	if($ifExist[0] > 0){
		return $ifExist[com_quantity];
	}else{
		return accum_qty_per_date($company_id, $branch_id, $warehouse_id, $inventory_date, $product_id, $package_id);
	}	

}

function getCurrentQtyByPackaging_V2($product_id, $warehouse_id, $date, $package_id)
{
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

    return current_qty_view_inv($company_id, $branch_id, $warehouse_id, $date, $product_id, $package_id);
}

function lowestJO_pkg($product_id){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$get_product_category = getProdCat($product_id);

	$package_id = mysql_fetch_array(mysql_query("SELECT pkg.qty AS lowest_unit, pkg.package_id AS package_id
	FROM tbl_package AS pkg, tbl_product_category AS pc
	WHERE pkg.qty = (SELECT MIN(pkg.qty)
	FROM tbl_package AS pkg, tbl_product_category AS pc
	WHERE pkg.category_id = pc.product_categ_id AND ((pkg.company_id = '$company_id' AND pkg.branch_id = '$branch_id') OR (pkg.company_id = 0 
	AND pkg.branch_id = 0)) AND pkg.visibility_status = 1 AND pc.product_categ_id = '$get_product_category') AND pkg.category_id = pc.product_categ_id AND ((pkg.company_id = '$company_id' AND pkg.branch_id = '$branch_id') OR (pkg.company_id = 0 
	AND pkg.branch_id = 0)) AND pkg.visibility_status = 1 AND pc.product_categ_id = '$get_product_category'"));
	$lowest_unit = $package_id[package_id];

	return $lowest_unit;
}


//inventory deduction for pig
function getConsumedLastMonth($start_date,$end_date,$warehouse,$b_id,$company_id,$stock_id){
		$result = mysql_query("select sum(jd.quantity * jh.num_of_batches) as total  from tbl_joborder_header_feeds as jh, tbl_joborder_details_feeds as jd where jh.joborder_header_id = jd.joborder_header_id and jh.jobdate between '$start_date' and '$end_date' and jh.status != 'C' and jh.warehouse_id = '$warehouse' and jh.branch_id = '$b_id' and jh.company_id = '$company_id' and jd.material = '$stock_id' ") or die(mysql_error());
		$row = mysql_fetch_assoc($result);
		
		return $row["total"];
	}


function getAverageCost($product_id, $new_quantity ,$new_cost,$warehouse_id){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = getWarehouseBranch($warehouse_id);//get_branch();
	
	$fetch_prod_cost = mysql_query("SELECT cost from tbl_warehouse_product_cost where product_id='$product_id' and company_id='$company_id' and branch_id='$branch_id' and warehouse_id='$warehouse_id'");
	$product_cost = mysql_fetch_array($fetch_prod_cost);
	
	$date = date('Y-m-d', strtotime(getCurrentDate()));
	$current_quantity = getCurrentQty($product_id,$warehouse_id,$date);
	
	if($current_quantity <= 0){
		$average_cost = $new_cost;
	}else{
		$average_cost = (($new_quantity * $new_cost) + ($current_quantity * $product_cost[0]))/($current_quantity + $new_quantity);
	}
	
	
	if(mysql_num_rows($fetch_prod_cost) == 0){
		mysql_query("INSERT INTO `tbl_warehouse_product_cost`(`company_id`, `branch_id`, `warehouse_id`, `product_id`, `cost`) VALUES ('$company_id','$branch_id','$warehouse_id','$product_id','$average_cost')") or die(mysql_error());
	}else{
		mysql_query("UPDATE `tbl_warehouse_product_cost` SET `cost`='$average_cost' WHERE product_id='$product_id' and company_id='$company_id' and branch_id='$branch_id' and warehouse_id='$warehouse_id'") or die(mysql_error());
	}
	
}

function returnProdExpirationQty($ref_id){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
	
	$fetchEXD = mysql_query("SELECT *  FROM `tbl_exp_details` WHERE ref_num='$ref_id'");
	while($exdRow = mysql_fetch_array($fetchEXD)){
		
		$exRow = mysql_fetch_array(mysql_query("SELECT packaging_id FROM tbl_expiry_products WHERE exp_id='$exdRow[exp_id]' AND company_id='$company_id' AND branch_id='$branch_id'"));
		
		$pckQTY = mysql_fetch_array(mysql_query("SELECT `qty` FROM `tbl_package` WHERE `package_id`='$exRow[packaging_id]'"));
		$exp_a_qty = $pckQTY[0]*$exdRow[qty];
		
		$exp_query = mysql_query("UPDATE `tbl_expiry_products` SET remainig_qty=remainig_qty+'$exdRow[qty]', remainig_actual_qty=remainig_actual_qty+'$exp_a_qty'  WHERE exp_id='$exdRow[exp_id]' AND company_id='$company_id' AND branch_id='$branch_id'");
		if($exp_query){
			mysql_query("DELETE FROM `tbl_exp_details` WHERE exp_id='$exdRow[exp_id]'");
		}
	}
}

function getLotNumber($exp_id){
	$company_id = $_SESSION['system']['company_id'];
	
	$lot_number = mysql_fetch_array(mysql_query("SELECT lot_no FROM tbl_expiry_products WHERE exp_id='$exp_id' AND company_id='$company_id'")) or die (mysql_error());
	
	return $lot_number[0];
}

function getExpTotal($product_id,$warehouse_id,$inv_date){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
	
	$exp_total = mysql_fetch_array(mysql_query("SELECT SUM(remainig_qty) FROM `tbl_expiry_products` WHERE product_id='$product_id' AND warehouse_id='$warehouse_id' AND company_id='$company_id' AND branch_id='$branch_id' AND expiry_date <= '$inv_date'"));
	
	return $exp_total[0];
	
}

function geJO_tAverageCost($product_id, $new_quantity ,$new_cost,$warehouse_id, $excess=NULL){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = getWarehouseBranch($warehouse_id);//get_branch();

	if(!empty($excess)){
		$invExcess = $excess;
	}else{
		$invExcess = 0;
	}
	
	$fetch_prod_cost = mysql_query("SELECT cost from tbl_warehouse_product_cost where product_id='$product_id' and company_id='$company_id' and branch_id='$branch_id' and warehouse_id='$warehouse_id'");
	$product_cost = mysql_fetch_array($fetch_prod_cost);
	
	$date = date('Y-m-d', strtotime(getCurrentDate()));
	$current_quantity = getCurrentQtyBAL($product_id,$warehouse_id,$date) - $invExcess;
	
	if($current_quantity <= 0){
		$average_cost = $new_cost;
	}else{
		$average_cost = (($new_quantity * $new_cost) + ($current_quantity * $product_cost[0]))/($current_quantity + $new_quantity);
	}
	
	
	if(mysql_num_rows($fetch_prod_cost) == 0){
		mysql_query("INSERT INTO `tbl_warehouse_product_cost`(`company_id`, `branch_id`, `warehouse_id`, `product_id`, `cost`) VALUES ('$company_id','$branch_id','$warehouse_id','$product_id','$average_cost')") or die(mysql_error());
	}else{
		mysql_query("UPDATE `tbl_warehouse_product_cost` SET `cost`='$average_cost' WHERE product_id='$product_id' and company_id='$company_id' and branch_id='$branch_id' and warehouse_id='$warehouse_id'") or die(mysql_error());
	}
	
}

function getPenNameEggs($id){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$fetch_pen_name = mysql_fetch_array(mysql_query("SELECT pen_name FROM tbl_pen_assignment_eggs WHERE pen_assignment_id='$id' AND company_id='$company_id' AND branch_id='$branch_id' "));

	return $fetch_pen_name[0];
}


//get average in building for egg
function getAverageCostBuildingEggs($product_id, $new_quantity ,$new_cost,$building_id){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
	
	$fetch_prod_cost = mysql_query("SELECT cost FROM `tbl_building_products_eggs` WHERE building_id='$building_id' AND branch_id='$branch_id' AND company_id='$company_id' AND product_id='$product_id'");
	$product_cost = mysql_fetch_array($fetch_prod_cost);
	
	$date = date('Y-m-d', strtotime(getCurrentDate()));
	
	$current_quantity = buildingProdQty_eggs($building_id, $product_id, $date);

	if(($current_quantity - $new_quantity) <= 0){
		$average_cost = $new_cost;
	}else{
		$average_cost = (($new_quantity * $new_cost) + ($current_quantity * $product_cost[0]))/($current_quantity + $new_quantity);
	}
	
	if(mysql_num_rows($fetch_prod_cost) == 0){
		mysql_query("INSERT INTO `tbl_building_products_eggs`(`building_id`, `branch_id`, `company_id`, `product_id`, `cost`) VALUES ('$building_id','$branch_id','$company_id','$product_id','$average_cost')");
		
	}else{
		mysql_query("UPDATE `tbl_building_products_eggs` SET `cost`='$average_cost' WHERE building_id='$building_id' AND company_id='$company_id' AND branch_id='$branch_id' AND product_id='$product_id'");
	}
	
}

function getAverageCostBuildingBroiler($product_id, $new_quantity ,$new_cost,$building_id){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
	
	$fetch_prod_cost = mysql_query("SELECT cost FROM `tbl_building_products_broiler` WHERE building_id='$building_id' AND branch_id='$branch_id' AND company_id='$company_id' AND product_id='$product_id'");
	$product_cost = mysql_fetch_array($fetch_prod_cost);
	
	$date = date('Y-m-d', strtotime(getCurrentDate()));
	
	$current_quantity = buildingProdQty_broiler($building_id, $product_id, $date);

	if(($current_quantity - $new_quantity) <= 0){
		$average_cost = $new_cost;
	}else{
		$average_cost = (($new_quantity * $new_cost) + ($current_quantity * $product_cost[0]))/($current_quantity + $new_quantity);
	}
	
	if(mysql_num_rows($fetch_prod_cost) == 0){
		mysql_query("INSERT INTO `tbl_building_products_broiler`(`building_id`, `branch_id`, `company_id`, `product_id`, `cost`) VALUES ('$building_id','$branch_id','$company_id','$product_id','$average_cost')");
		
	}else{
		mysql_query("UPDATE `tbl_building_products_broiler` SET `cost`='$average_cost' WHERE building_id='$building_id' AND company_id='$company_id' AND branch_id='$branch_id' AND product_id='$product_id'");
	}
	
}

//get average in building for pig
function getAverageCostBuilding_pig($product_id, $quantity ,$cost,$building_id){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
		
	$fetch_prod_cost = mysql_query("SELECT cost FROM `tbl_building_products` WHERE building_id='$building_id' AND branch_id='$branch_id' AND company_id='$company_id' AND product_id='$product_id'");
	$product_cost = mysql_fetch_array($fetch_prod_cost);
	
	$date = date('Y-m-d', strtotime(getCurrentDate()));
	
	$current_quantity = buildingProdQty_pig($building_id, $product_id, $date);
	$product_categ_id = getProdCat($product_id);

	if($current_quantity <= 0){
		$average_cost = $cost;
	}else{
		$average_cost = (($quantity * $cost) + ($current_quantity * $product_cost[0]))/($current_quantity + $quantity);
	}
	
	if(mysql_num_rows($fetch_prod_cost) <= 0){
		mysql_query("INSERT INTO `tbl_building_products`(`building_id`, `branch_id`, `company_id`, `product_id`, `cost`, product_categ_id) VALUES ('$building_id','$branch_id','$company_id','$product_id','$average_cost', '$product_categ_id')");
		
	}else{
		mysql_query("UPDATE `tbl_building_products` SET `cost`='$average_cost' WHERE building_id='$building_id' AND company_id='$company_id' AND branch_id='$branch_id' AND product_id='$product_id'");
	}
	
}


function getAverageCostBuilding_eggs($product_id, $quantity ,$cost,$building_id){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
		
	$fetch_prod_cost = mysql_query("SELECT cost FROM `tbl_building_products_eggs` WHERE building_id='$building_id' AND branch_id='$branch_id' AND company_id='$company_id' AND product_id='$product_id'");
	$product_cost = mysql_fetch_array($fetch_prod_cost);
	
	$date = date('Y-m-d', strtotime(getCurrentDate()));
	
	$current_quantity = buildingProdQty_eggs($building_id, $product_id, $date);
	$product_categ_id = getProdCat($product_id);

	if($current_quantity <= 0){
		$average_cost = $cost;
	}else{
		$average_cost = (($quantity * $cost) + ($current_quantity * $product_cost[0]))/($current_quantity + $quantity);
	}
	
	if(mysql_num_rows($fetch_prod_cost) == 0){
		mysql_query("INSERT INTO `tbl_building_products_eggs`(`building_id`, `branch_id`, `company_id`, `product_id`, `cost`, product_categ_id) VALUES ('$building_id','$branch_id','$company_id','$product_id','$average_cost', '$product_categ_id')");
		
	}else{
		mysql_query("UPDATE `tbl_building_products_eggs` SET `cost`='$average_cost' WHERE building_id='$building_id' AND company_id='$company_id' AND branch_id='$branch_id' AND product_id='$product_id'");
	}
	
}

function averageCostMinus($product_id, $new_quantity ,$new_cost,$warehouse_id){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = getWarehouseBranch($warehouse_id);//get_branch();
	$date = getCurrentDate();
	$product_cost = mysql_fetch_array(mysql_query("SELECT cost from tbl_warehouse_product_cost where product_id='$product_id' and company_id='$company_id' and branch_id='$branch_id' and warehouse_id='$warehouse_id'"));
	
	$current_quantity = getCurrentQty($product_id,$warehouse_id,$date);
	
	if(($current_quantity - $new_quantity) <= 0){
		$average_cost = $new_cost;
	}else{
		$average_cost = (($current_quantity * $product_cost[0]) - ($new_quantity * $new_cost))/($current_quantity - $new_quantity);
	}
	
	if($product_cost[0] == 0){
		mysql_query("INSERT INTO `tbl_warehouse_product_cost`(`company_id`, `branch_id`, `warehouse_id`, `product_id`, `cost`) VALUES ('$company_id','$branch_id','$warehouse_id','$product_id','$average_cost')") or die(mysql_error());
	}else{
		mysql_query("UPDATE `tbl_warehouse_product_cost` SET `cost`='$average_cost' WHERE product_id='$product_id' and company_id='$company_id' and branch_id='$branch_id' and warehouse_id='$warehouse_id'") or die(mysql_error());
	}
	
}

//Building reversed cost
function bldgAverageReversed_pig($product_id, $deleted_qty ,$deleted_cost,$building_id){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
	$date = getCurrentDate();
	$stock_cost = mysql_fetch_array(mysql_query("SELECT cost from tbl_building_products where product_id='$product_id' and company_id='$company_id' and branch_id='$branch_id' and building_id='$building_id'"));
	
	$current_quantity = buildingProdQty_pig($building_id, $product_id, $date);
	
	if(($current_quantity - $deleted_qty) <= 0){
		$reversed_cost = $deleted_cost;
	}else{
		$reversed_cost = (($current_quantity * $stock_cost[0]) - ($deleted_qty * $deleted_cost))/($current_quantity - $deleted_qty);
	}
	
	if($stock_cost[0] == 0){
		mysql_query("INSERT INTO `tbl_building_products`(`company_id`, `branch_id`, `building_id`, `product_id`, `cost`) VALUES ('$company_id','$branch_id','$building_id','$product_id','$reversed_cost')");
	}else{
		mysql_query("UPDATE `tbl_building_products` SET `cost`='$reversed_cost' WHERE product_id='$product_id' and company_id='$company_id' and branch_id='$branch_id' and building_id='$building_id'");
	}
}

//Building reversed cost
function bldgAverageReversed_eggs($product_id, $deleted_qty ,$deleted_cost,$building_id){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
	$date = getCurrentDate();
	$stock_cost = mysql_fetch_array(mysql_query("SELECT cost from tbl_building_products_eggs where product_id='$product_id' and company_id='$company_id' and branch_id='$branch_id' and building_id='$building_id'"));
	
	$current_quantity = buildingProdQty_eggs($building_id, $product_id, $date);
	
	if(($current_quantity - $deleted_qty) <= 0){
		$reversed_cost = $deleted_cost;
	}else{
		$reversed_cost = (($current_quantity * $stock_cost[0]) - ($deleted_qty * $deleted_cost))/($current_quantity - $deleted_qty);
	}
	
	if($stock_cost[0] == 0){
		mysql_query("INSERT INTO `tbl_building_products_eggs`(`company_id`, `branch_id`, `building_id`, `product_id`, `cost`) VALUES ('$company_id','$branch_id','$building_id','$product_id','$reversed_cost')");
	}else{
		mysql_query("UPDATE `tbl_building_products_eggs` SET `cost`='$reversed_cost' WHERE product_id='$product_id' and company_id='$company_id' and branch_id='$branch_id' and building_id='$building_id'");
	}
}

function bldgAverageReversed_broiler($product_id, $deleted_qty ,$deleted_cost,$building_id){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
	$date = getCurrentDate();
	$stock_cost = mysql_fetch_array(mysql_query("SELECT cost from tbl_building_products_broiler where product_id='$product_id' and company_id='$company_id' and branch_id='$branch_id' and building_id='$building_id'"));
	
	$current_quantity = buildingProdQty_broiler($building_id, $product_id, $date);
	
	if(($current_quantity - $deleted_qty) <= 0){
		$reversed_cost = $deleted_cost;
	}else{
		$reversed_cost = (($current_quantity * $stock_cost[0]) - ($deleted_qty * $deleted_cost))/($current_quantity - $deleted_qty);
	}
	
	if($stock_cost[0] == 0){
		mysql_query("INSERT INTO `tbl_building_products_broiler`(`company_id`, `branch_id`, `building_id`, `product_id`, `cost`) VALUES ('$company_id','$branch_id','$building_id','$product_id','$reversed_cost')");
	}else{
		mysql_query("UPDATE `tbl_building_products_broiler` SET `cost`='$reversed_cost' WHERE product_id='$product_id' and company_id='$company_id' and branch_id='$branch_id' and building_id='$building_id'");
	}
}


// --- building inventory ---
function buildingProdQty_pig($building_id, $product_id, $date){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
	
	// S T  W a r e h o u s e  -  B u i l d i n g
	$stock_transfer_qty = mysql_fetch_array(mysql_query("SELECT SUM(std.actual_qty) FROM tbl_stock_transfer_bldg as st, tbl_st_bldg_details as std WHERE st.building_id = '$building_id' AND st.ref_id = std.ref_id AND st.company_id = std.company_id AND st.branch_id = std.branch_id AND (st.status = 'F' OR st.status = 'R') AND (std.status = 'F' OR std.status = 'R') AND std.product_id = '$product_id' AND st.date <= '$date'"));
	
	// S T  B u i l d i n g  -  B u i l d i n g
	$total_st_bldg_bldg = mysql_fetch_array(mysql_query("SELECT SUM(std.actual_qty) FROM tbl_stock_transfer_bldg_bldg_pig as st, tbl_std_bldg_bldg_pig as std WHERE st.destination_bldg = '$building_id' AND st.ref_id = std.ref_id AND st.company_id = '$company_id' AND st.branch_id = '$branch_id' AND st.status = 'F' AND std.product_id = '$product_id' AND st.date <= '$date'"));
	
	$total_qty = $stock_transfer_qty[0]+$total_st_bldg_bldg[0];
	
	return $total_qty - buildingInvDeductions_pig($building_id, $product_id, $date);
}

function bldg_balance_fowarded_pig($building_id, $product_id, $date, $include_saved, $include_finished){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
	
	// I N V E N T O R Y   I N
	$fetch_st_bldg = mysql_query("SELECT ref_id from tbl_stock_transfer_bldg where company_id = '$company_id' and building_id = '$building_id' AND (status='R' OR status='$include_finished' OR status='$include_saved') and date < '$date'");
	$stock_transfer_qty = 0;
	while($st_row = mysql_fetch_array($fetch_st_bldg)){
		$ref_id = $st_row['ref_id'];
		$st_bldg_qty = mysql_fetch_array(mysql_query("SELECT sum(actual_qty) from tbl_st_bldg_details where product_id = '$product_id' and company_id = '$company_id' and (status='R' OR status='$include_finished' OR status='$include_saved') and ref_id = '$ref_id'"));
		
		$stock_transfer_qty += $st_bldg_qty[0];
	}
	
	$fetch_st_bldg_bldg = mysql_query("SELECT * from tbl_stock_transfer_bldg_bldg_pig where destination_bldg = '$building_id' and company_id = '$company_id' and status = 'F' AND (status='$include_finished' OR status='$include_saved')");
	$total_st_bldg_bldg = 0;
	while($row_st_bldg_bldg = mysql_fetch_array($fetch_st_bldg_bldg)){
		$st_bldg_bldg_ref_id = $row_st_bldg_bldg['ref_id'];
		
		$st_bldg_bldg_qty = mysql_fetch_array(mysql_query("SELECT sum(actual_qty) from tbl_std_bldg_bldg_pig where ref_id = '$st_bldg_bldg_ref_id' and product_id = '$product_id' and date < '$date' and (status='$include_finished' OR status='$include_saved')"));
		
		$total_st_bldg_bldg += $st_bldg_bldg_qty[0];
	}
	
	$total_IN_qty = $stock_transfer_qty + $total_st_bldg_bldg;
	
	
	// I N V E N T O R Y   O U T
	$total_medication = mysql_fetch_array(mysql_query("SELECT sum(amount) from tbl_medication_pig where product_id = '$product_id' and building_id = '$building_id' and date < '$date' and company_id = '$company_id'"));
	
	$total_vaccination = mysql_fetch_array(mysql_query("SELECT sum(v_dosage) from tbl_vaccine_pig where product_id = '$product_id' and building_id = '$building_id' and v_date <= '$date' and company_id = '$company_id'"));
	
	$total_feeding = mysql_fetch_array(mysql_query("SELECT sum(amount) from tbl_feeding_per_pen where company_id='$company_id' and building_id='$building_id' and product_id='$product_id' and date_added <= '$date' "));
	
	$total_ai_materials = mysql_fetch_array(mysql_query("SELECT sum(quantity) from tbl_ai_materials_pig where company_id='$company_id' and building_id='$building_id' and product_id='$product_id' and date_added <= '$date' "));
	
	$total_st_bldg_warehouse = 0;
	$fetch_st_bldg_warehouse = mysql_query("SELECT * from tbl_stock_transfer_bldg_warehouse_pig where building_id = '$building_id' and company_id = '$company_id' and (status='$include_finished' OR status='$include_saved')");
	while($row_st_bldg_warehouse = mysql_fetch_array($fetch_st_bldg_warehouse)){
		$st_bldg_ref_id = $row_st_bldg_warehouse['ref_id'];
		
		$st_bldg_warehouse_qty = mysql_fetch_array(mysql_query("SELECT sum(actual_qty) from tbl_std_bldg_warehouse_pig where ref_id = '$st_bldg_ref_id' and product_id = '$product_id' and date < '$date' and (status='$include_finished' OR status='$include_saved')"));
		
		$total_st_bldg_warehouse += $st_bldg_warehouse_qty[0];
	}
	
	$total_st_bldg_bldg = 0;
	$fetch_st_bldg_bldg = mysql_query("SELECT * from tbl_stock_transfer_bldg_bldg_pig where source_bldg = '$building_id' and company_id = '$company_id' and (status='$include_finished' OR status='$include_saved')");
	while($row_st_bldg_bldg = mysql_fetch_array($fetch_st_bldg_bldg)){
		$st_bldg_bldg_ref_id = $row_st_bldg_bldg['ref_id'];
		
		$st_bldg_bldg_qty = mysql_fetch_array(mysql_query("SELECT sum(actual_qty) from tbl_std_bldg_bldg_pig where ref_id = '$st_bldg_bldg_ref_id' and product_id = '$product_id' and date < '$date' and (status='$include_finished' OR status='$include_saved')"));
		
		$total_st_bldg_bldg += $st_bldg_bldg_qty[0];
	}
	
	$total_OUT_qty = $total_medication[0] + $total_vaccination[0] + $total_feeding[0] + $total_ai_materials[0] + $total_st_bldg_warehouse + $total_st_bldg_bldg;
	
	
	return $total_IN_qty - $total_OUT_qty;
}

function bldg_balance_fowarded_eggs($building_id, $product_id, $date, $include_saved, $include_finished){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
	
	// I N V E N T O R Y   I N  ! !
	$fetch_st_bldg = mysql_query("SELECT ref_id from tbl_stock_transfer_bldg_eggs where company_id = '$company_id' and building_id = '$building_id' AND (status='R' OR status='$include_finished' OR status='$include_saved') and date < '$date'");
	$stock_transfer_qty = 0;
	while($st_row = mysql_fetch_array($fetch_st_bldg)){
		$ref_id = $st_row['ref_id'];
		$st_bldg_qty = mysql_fetch_array(mysql_query("SELECT sum(actual_qty) from tbl_st_bldg_details_eggs where product_id = '$product_id' and company_id = '$company_id' and (status='R' OR status='$include_finished' OR status='$include_saved') and ref_id = '$ref_id'"));
		
		$stock_transfer_qty += $st_bldg_qty[0];
	}
	
	$fetch_st_bldg_bldg = mysql_query("SELECT * from tbl_stock_transfer_bldg_bldg_eggs where destination_bldg = '$building_id' and company_id = '$company_id' and (status='$include_finished' OR status='$include_saved')");
	$total_st_bldg_bldg = 0;
	while($row_st_bldg_bldg = mysql_fetch_array($fetch_st_bldg_bldg)){
		$st_bldg_bldg_ref_id = $row_st_bldg_bldg['ref_id'];
		
		$st_bldg_bldg_qty = mysql_fetch_array(mysql_query("SELECT sum(actual_qty) from tbl_std_bldg_bldg_eggs where ref_id = '$st_bldg_bldg_ref_id' and product_id = '$product_id' and date <= '$date' and (status='$include_finished' OR status='$include_saved')"));
		
		$total_st_bldg_bldg += $st_bldg_bldg_qty[0];
	}
	
	// I N V E N T O R Y   O U T  ! !	
	$total_feeds_growing = mysql_fetch_array(mysql_query("SELECT sum(qty) from tbl_feeds_entry_eggs where item = '$product_id' and building_id = '$building_id' and DATE_FORMAT(date,'%Y-%m-%d') <= '$date' and company_id = '$company_id' and branch_id = '$branch_id' "));
	
	$total_madication_and_vaccination_growing = mysql_fetch_array(mysql_query("SELECT sum(qty) from tbl_medication_and_vaccination_entry_eggs where item = '$product_id' and building_id = '$building_id' and DATE_FORMAT(date,'%Y-%m-%d') <= '$date' and company_id = '$company_id' and branch_id = '$branch_id'"));
	
	$total_feeds_production = mysql_fetch_array(mysql_query("SELECT sum(qty) from tbl_feeds_entry_production_eggs where item = '$product_id' and building_id = '$building_id' and DATE_FORMAT(date,'%Y-%m-%d') <= '$date' and company_id = '$company_id' and branch_id = '$branch_id' "));

	$brooding_expense = mysql_fetch_array(mysql_query("SELECT sum(quantity) from tbl_expense_entry_eggs where product_id = '$product_id' and building_id = '$building_id' and DATE_FORMAT(date,'%Y-%m-%d') <= '$date' and company_id = '$company_id' and branch_id = '$branch_id' "));
	
	$total_madication_and_vaccination_production = mysql_fetch_array(mysql_query("SELECT sum(qty) from tbl_medication_and_vaccination_entry_production_eggs where item = '$product_id' and building_id = '$building_id' and DATE_FORMAT(date,'%Y-%m-%d') <= '$date' and company_id = '$company_id' and branch_id = '$branch_id'"));
	
	$total_st_bldg_warehouse = 0;
	$fetch_st_bldg_warehouse = mysql_query("SELECT * from tbl_stock_transfer_bldg_warehouse_eggs where building_id = '$building_id' and company_id = '$company_id' and (status='$include_finished' OR status='$include_saved')");
	while($row_st_bldg_warehouse = mysql_fetch_array($fetch_st_bldg_warehouse)){
		$st_bldg_ref_id = $row_st_bldg_warehouse['ref_id'];
		
		$st_bldg_warehouse_qty = mysql_fetch_array(mysql_query("SELECT sum(actual_qty) from tbl_std_bldg_warehouse_eggs where ref_id = '$st_bldg_ref_id' and product_id = '$product_id' and DATE_FORMAT(date,'%Y-%m-%d') <= '$date' and (status='$include_finished' OR status='$include_saved')"));
		
		$total_st_bldg_warehouse += $st_bldg_warehouse_qty[0];
	}
	
	$total_st_bldg_bldg_out = 0;
	$fetch_st_bldg_bldg_out = mysql_query("SELECT * from tbl_stock_transfer_bldg_bldg_eggs where source_bldg = '$building_id' and company_id = '$company_id' and (status='$include_finished' OR status='$include_saved')");
	while($row_st_bldg_bldg = mysql_fetch_array($fetch_st_bldg_bldg_out)){
		$st_bldg_bldg_ref_id = $row_st_bldg_bldg['ref_id'];
		
		$st_bldg_bldg_qty = mysql_fetch_array(mysql_query("SELECT sum(actual_qty) from tbl_std_bldg_bldg_eggs where ref_id = '$st_bldg_bldg_ref_id' and product_id = '$product_id' and DATE_FORMAT(date,'%Y-%m-%d') <= '$date' and (status='$include_finished' OR status='$include_saved')"));
		
		$total_st_bldg_bldg_out += $st_bldg_bldg_qty[0];
	}
	
	$total_IN_qty = $stock_transfer_qty+$total_st_bldg_bldg;
	$total_OUT_qty = $total_feeds_growing[0]+$total_madication_and_vaccination_growing[0]+$total_feeds_production[0]+$brooding_expense[0]+$total_madication_and_vaccination_production[0]+$total_st_bldg_warehouse+$total_st_bldg_bldg_out;
	
	
	return $total_IN_qty-$total_OUT_qty;
}

function bldg_cost_fowarded_pig($building_id, $stock_name, $date_from, $include_saved, $include_finished){
		$company_id = $_SESSION['system']['company_id'];
		$branch_id = get_branch();
		
		//STOCK TRANSFER DESTINATION WAREHOUSE TO BLDG (IN)
			$fetch_st_wb = mysql_query("SELECT * FROM `tbl_st_bldg_details` AS std, `tbl_stock_transfer_bldg` AS sth WHERE std.product_id='$stock_name' AND std.date <= '$date_from' AND sth.company_id='$company_id' AND sth.branch_id='$branch_id' AND(std.status='$include_finished' OR std.status='R' OR std.status='$include_saved') AND sth.building_id='$building_id' AND sth.ref_id=std.ref_id") or die(mysql_error());
			while($rr_row = mysql_fetch_array($fetch_st_wb)){
				$data[] = array(
					"in" => $rr_row["actual_qty"],
					"out" => "",
					"unit_price" => $rr_row["cost"]
				);
			}
			
			
			//STOCK TRANSFER DESTINATION BLDG TO BLDG (IN)
			$fetch_st_bb = mysql_query("SELECT * FROM `tbl_std_bldg_bldg_pig` AS std, `tbl_stock_transfer_bldg_bldg_pig` AS sth WHERE std.product_id='$stock_name' AND sth.date <= '$date_from' AND sth.company_id='$company_id' AND sth.branch_id='$branch_id' AND(std.status='$include_finished' OR std.status='$include_saved') AND sth.destination_bldg='$building_id' AND sth.ref_id=std.ref_id") or die(mysql_error());
			while($rr_row = mysql_fetch_array($fetch_st_bb)){
				$data[] = array(
					"in" => $rr_row["actual_qty"],
					"out" => "",
					"unit_price" => $rr_row["cost"]
				);
			}
			
			
		/* 	I N V E N T O R Y   O U T  */
					
			//STOCK TRANSFER DESTINATION BLDG TO WAREHOUSE (Out)
			$fetch_st_bw = mysql_query("SELECT * FROM `tbl_std_bldg_warehouse_pig` AS std, `tbl_stock_transfer_bldg_warehouse_pig` AS sth WHERE std.product_id='$stock_name' AND std.date <= '$date_from' AND sth.company_id='$company_id' AND sth.branch_id='$branch_id' AND(std.status='$include_finished' OR std.status='$include_saved') AND sth.building_id='$building_id' AND sth.ref_id=std.ref_id") or die(mysql_error());
			while($rr_row = mysql_fetch_array($fetch_st_bw)){
				$data[] = array(
					"in" => "",
					"out" => $rr_row["actual_qty"],
					"unit_price" => $rr_row["cost"]
				);
			}
			
			//STOCK TRANSFER DESTINATION BLDG TO BLDG (OUT)
			$fetch_st_bb_out = mysql_query("SELECT * FROM `tbl_std_bldg_bldg_pig` AS std, `tbl_stock_transfer_bldg_bldg_pig` AS sth WHERE std.product_id='$stock_name' AND std.date <= '$date_from' AND sth.company_id='$company_id' AND sth.branch_id='$branch_id' AND(std.status='$include_finished' OR std.status='$include_saved') AND sth.source_bldg='$building_id' AND sth.ref_id=std.ref_id") or die(mysql_error());
			while($rr_row = mysql_fetch_array($fetch_st_bb_out)){
				$data[] = array(
					"in" => "",
					"out" => $rr_row["actual_qty"],
					"unit_price" => $rr_row["cost"]
				);
			}
			
			//FROM MEDICATION (OUT)/
			$fetch_medication = mysql_query("SELECT * FROM `tbl_medication_pig` WHERE product_id='$stock_name'  AND `date` <= '$date_from' AND company_id='$company_id' AND building_id='$building_id'") or die(mysql_error());
			while($rr_row = mysql_fetch_array($fetch_medication)){
				
				$data[] = array(
					"in" => "",
					"out" =>  $rr_row["amount"],
					"unit_price" => $rr_row["cost"]
				);
			}
			
			//FROM VACINATION (OUT)/
			$fetch_medication = mysql_query("SELECT * FROM `tbl_vaccine_pig` WHERE product_id='$stock_name'  AND `v_date` <= '$date_from' AND company_id='$company_id' AND building_id='$building_id'") or die(mysql_error());
			while($rr_row = mysql_fetch_array($fetch_medication)){
				
				$data[] = array(
					"in" => "",
					"out" =>  $rr_row["v_dosage"],
					"unit_price" => $rr_row["cost"]
				);
			}
			
			//FROM FEEDING (OUT)/
			$fetch_medication = mysql_query("SELECT * FROM `tbl_feeding_per_pen` WHERE product_id='$stock_name'  AND `date_added` <= '$date_from' AND company_id='$company_id' AND building_id='$building_id'") or die(mysql_error());
			while($rr_row = mysql_fetch_array($fetch_medication)){
				
				$data[] = array(
					"in" => "",
					"out" =>  $rr_row["amount"],
					"unit_price" => $rr_row["cost"]
				);
			}
			
			//FROM AI Materials (OUT)/
			$fetch_ai_mat = mysql_query("SELECT * FROM `tbl_ai_materials_pig` WHERE product_id='$stock_name'  AND `date_added` <= '$date_from' AND company_id='$company_id' AND building_id='$building_id'") or die(mysql_error());
			while($rr_row = mysql_fetch_array($fetch_ai_mat)){
				
				$data[] = array(
					"in" => "",
					"out" =>  $rr_row["quantity"],
					"unit_price" => $rr_row["cost"]
				);
			}
			
	$date = array();
	if($data){
		foreach($data as $key => $rr_row){
			$date[] = $rr_row['date'];
		}
		array_multisort($data,SORT_ASC,$date);
			
		foreach($data as $key => $rr_row){
			$balance +=$rr_row[in];
			$balance -=$rr_row[out];	

		$total_amount +=$rr_row[in] * $rr_row["unit_price"];
		$total_amount -=$rr_row[out] * $rr_row["unit_price"];
		
		if($balance == 0){}else{

		$average_price = $total_amount / $balance;
		$final_total_amount =  $balance * $average_price;
		}
		}
	}
	
	return $average_price;
}


function bldg_cost_fowarded_eggs($building_id, $stock_name, $date_from, $include_saved, $include_finished){
		$company_id = $_SESSION['system']['company_id'];
		$branch_id = get_branch();
		
		/* 	I N V E N T O R Y   I N  */
			
		//STOCK TRANSFER DESTINATION WAREHOUSE TO BLDG // O L D  T R A N S  (IN)
		$fetch_st_wb = mysql_query("SELECT * FROM `tbl_st_bldg_details_eggs` AS std, `tbl_stock_transfer_bldg_eggs` AS sth WHERE std.product_id='$stock_name' AND sth.date <= '$date_from' AND sth.company_id='$company_id' AND sth.branch_id='$branch_id' AND(std.status='R' OR std.status='$include_finished' OR std.status='$include_saved') AND sth.building_id='$building_id' AND sth.ref_id=std.ref_id") or die(mysql_error());
		while($rr_row = mysql_fetch_array($fetch_st_wb)){
			$data[] = array(
				"in" => $rr_row["actual_qty"],
				"out" => "",
				"unit_price" => $rr_row["cost"]
			);
		}
		
		//STOCK TRANSFER DESTINATION BLDG TO BLDG (IN)
		$fetch_st_bb = mysql_query("SELECT * FROM `tbl_std_bldg_bldg_eggs` AS std, `tbl_stock_transfer_bldg_bldg_eggs` AS sth WHERE std.product_id='$stock_name' AND sth.date <= '$date_from' AND sth.company_id='$company_id' AND sth.branch_id='$branch_id' AND(std.status='$include_finished' OR std.status='R' OR std.status='$include_saved') AND sth.destination_bldg='$building_id' AND sth.ref_id=std.ref_id") or die(mysql_error());
		while($rr_row = mysql_fetch_array($fetch_st_bb)){
			$data[] = array(
				"in" => $rr_row["actual_qty"],
				"out" => "",
				"unit_price" => $rr_row["cost"]
			);
		}
	
	
	/* 	I N V E N T O R Y   O U T  */
	
		//STOCK TRANSFER DESTINATION BLDG TO WAREHOUSE (Out)
		$fetch_st_bw = mysql_query("SELECT * FROM `tbl_std_bldg_warehouse_eggs` AS std, `tbl_stock_transfer_bldg_warehouse_eggs` AS sth WHERE std.product_id='$stock_name' AND sth.date <= '$date_from' AND sth.company_id='$company_id' AND sth.branch_id='$branch_id' AND(std.status='$include_finished' OR std.status='$include_saved') AND sth.building_id='$building_id' AND sth.ref_id=std.ref_id") or die(mysql_error());
		while($rr_row = mysql_fetch_array($fetch_st_bw)){
			$data[] = array(
				"in" => "",
				"out" => $rr_row["actual_qty"],
				"unit_price" => $rr_row["cost"]
			);
		}
		
		//STOCK TRANSFER DESTINATION BLDG TO BLDG (OUT)
		$fetch_st_bb_out = mysql_query("SELECT * FROM `tbl_std_bldg_bldg_eggs` AS std, `tbl_stock_transfer_bldg_bldg_eggs` AS sth WHERE std.product_id='$stock_name' AND sth.date <= '$date_from' AND '$date_to' AND sth.company_id='$company_id' AND sth.branch_id='$branch_id' AND(std.status='$include_finished' OR std.status='$include_saved') AND sth.source_bldg='$building_id' AND sth.ref_id=std.ref_id") or die(mysql_error());
		while($rr_row = mysql_fetch_array($fetch_st_bb_out)){
			$data[] = array(
				"in" => "",
				"out" => $rr_row["actual_qty"],
				"unit_price" => $rr_row["cost"]
			);
		}
		 
		
		//FROM FEEDING GROWER (OUT)/
		$fetch_feeding_grower = mysql_query("SELECT * FROM `tbl_feeds_entry_eggs` WHERE item = '$stock_name' AND date <= '$date_from' AND company_id='$company_id' AND building_id='$building_id'") or die(mysql_error());
		while($rr_row = mysql_fetch_array($fetch_feeding_grower)){
			
			$data[] = array(
				"in" => "",
				"out" =>  $rr_row["qty"],
				"unit_price" => $rr_row["cost"]
			);
		}
		
		//FROM FEEDING PRODUCTION (OUT)/
		$fetch_feeding_production = mysql_query("SELECT * FROM `tbl_feeds_entry_production_eggs` WHERE item = '$stock_name' AND date <= '$date_from' AND company_id='$company_id' AND building_id='$building_id'") or die(mysql_error());
		while($rr_row = mysql_fetch_array($fetch_feeding_production)){
			
			$data[] = array(
				"in" => "",
				"out" =>  $rr_row["qty"],
				"unit_price" => $rr_row["cost"]
			);
		}
		
		//FROM FEEDING PRODUCTION (OUT)/
		$fetch_medication_grower = mysql_query("SELECT * FROM `tbl_medication_and_vaccination_entry_eggs` WHERE item = '$stock_name' AND date <= '$date_from' AND company_id='$company_id' AND building_id='$building_id'") or die(mysql_error());
		while($rr_row = mysql_fetch_array($fetch_medication_grower)){
			
			$data[] = array(
				"in" => "",
				"out" =>  $rr_row["qty"],
				"unit_price" => $rr_row["cost"]
			);
		}
		
		//FROM FEEDING PRODUCTION (OUT)/
		$fetch_medication_production = mysql_query("SELECT * FROM `tbl_medication_and_vaccination_entry_production_eggs` WHERE item = '$stock_name' AND date <= '$date_from' AND company_id='$company_id' AND building_id='$building_id'") or die(mysql_error());
		while($rr_row = mysql_fetch_array($fetch_medication_production)){
			
			$data[] = array(
				"in" => "",
				"out" =>  $rr_row["qty"],
				"unit_price" => $rr_row["cost"]
			);
		}
		
		//FROM BROODING EXPENSE (OUT)/
		$fetch_medication_production = mysql_query("SELECT * FROM `tbl_expense_entry_eggs` WHERE product_id = '$product_id' AND date <= '$date_from' AND company_id='$company_id' AND building_id='$building_id'") or die(mysql_error());
		while($rr_row = mysql_fetch_array($fetch_medication_production)){
			
			$data[] = array(
				"in" => "",
				"out" =>  $rr_row["quantity"],
				"unit_price" => $rr_row["cost"]
			);
		}
	
	$date = array();
	if($data){
		foreach($data as $key => $rr_row){
			$date[] = $rr_row['date'];
		}
		array_multisort($data,SORT_ASC,$date);
			
		foreach($data as $key => $rr_row){
			$balance +=$rr_row[in];
			$balance -=$rr_row[out];	

		$total_amount +=$rr_row[in] * $rr_row["unit_price"];
		$total_amount -=$rr_row[out] * $rr_row["unit_price"];
		
		if($balance == 0){}else{

		$average_price = $total_amount / $balance;
		$final_total_amount =  $balance * $average_price;
		}
		}
	}
	
	return $average_price;
}

// --- building inventory ---
function pendingBldgProdQty_pig($building_id, $product_id, $date){
		$company_id = $_SESSION['system']['company_id'];
		$branch_id = get_branch();
		
		$fetch_st_bldg = mysql_query("SELECT ref_id from tbl_stock_transfer_bldg where company_id = '$company_id' and building_id = '$building_id' and status = 'F' and date <= '$date' ");
		$stock_transfer_qty = 0;
		
		while($st_row = mysql_fetch_array($fetch_st_bldg)){
			$ref_id = $st_row['ref_id'];
			$st_bldg_qty = mysql_fetch_array(mysql_query("SELECT sum(actual_qty) from tbl_st_bldg_details where product_id = '$product_id' and company_id = '$company_id' and status = 'F' and ref_id = '$ref_id' "));
			
			$stock_transfer_qty += $st_bldg_qty[0];
		}
		
		
		$fetch_st_bldg_bldg = mysql_query("SELECT * from tbl_stock_transfer_bldg_bldg_pig where destination_bldg = '$building_id' and company_id = '$company_id' and status = 'F'");
		$total_st_bldg_bldg = 0;
		
		while($row_st_bldg_bldg = mysql_fetch_array($fetch_st_bldg_bldg)){
			$st_bldg_bldg_ref_id = $row_st_bldg_bldg['ref_id'];
			
			$st_bldg_bldg_qty = mysql_fetch_array(mysql_query("SELECT sum(actual_qty) from tbl_std_bldg_bldg_pig where ref_id = '$st_bldg_bldg_ref_id' and product_id = '$product_id' and date <= '$date' and status = 'F'"));
			
			$total_st_bldg_bldg += $st_bldg_bldg_qty[0];
		}
		
		$total_qty = $stock_transfer_qty + $total_st_bldg_bldg;
		
		return $total_qty;
}

function buildingProdQty_eggs($building_id, $product_id, $date){
		$company_id = $_SESSION['system']['company_id'];
		$branch_id = get_branch();
		
		/*$fetch_st_bldg = mysql_query("SELECT ref_id from tbl_stock_transfer_bldg_eggs where status = 'F' and company_id = '$company_id' and branch_id = '$branch_id' and  building_id = '$building_id' and status = 'F' and date <= '$date' ");
		$stock_transfer_qty = 0;
		
		while($st_row = mysql_fetch_array($fetch_st_bldg)){
			$ref_id = $st_row['ref_id'];
			$st_bldg_qty = mysql_fetch_array(mysql_query("SELECT sum(actual_qty) from tbl_st_bldg_details_eggs where product_id = '$product_id' and company_id = '$company_id' and branch_id = '$branch_id' and status = 'F' and ref_id = '$ref_id' "));
			
			$stock_transfer_qty += $st_bldg_qty[0];
		}*/
		
		// S T  W a r e h o u s e  -  B u i l d i n g
		$stock_transfer_qty = mysql_fetch_array(mysql_query("SELECT SUM(actual_qty) FROM tbl_stock_transfer_bldg_eggs as st, tbl_st_bldg_details_eggs as std WHERE st.building_id = '$building_id' AND st.ref_id = std.ref_id AND st.company_id = std.company_id AND st.branch_id = std.branch_id AND (st.status = 'F' OR st.status = 'R') AND (std.status = 'F' OR std.status = 'R') AND std.product_id = '$product_id' AND st.date <= '$date'"));
		
		$fetch_st_bldg_bldg = mysql_query("SELECT * from tbl_stock_transfer_bldg_bldg_eggs where destination_bldg = '$building_id' and company_id = '$company_id' and status = 'F'");
		$total_st_bldg_bldg = 0;
		
		while($row_st_bldg_bldg = mysql_fetch_array($fetch_st_bldg_bldg)){
			$st_bldg_bldg_ref_id = $row_st_bldg_bldg['ref_id'];
			
			$st_bldg_bldg_qty = mysql_fetch_array(mysql_query("SELECT sum(actual_qty) from tbl_std_bldg_bldg_eggs where ref_id = '$st_bldg_bldg_ref_id' and product_id = '$product_id' and date <= '$date' and status = 'F'"));
			
			$total_st_bldg_bldg += $st_bldg_bldg_qty[0];
		}
		
		$total_in_bldg = $stock_transfer_qty[0] + $total_st_bldg_bldg;
		
		$final_total = $total_in_bldg - buildingInvDeductions_eggs($building_id, $product_id, $date);
		
		return  $final_total;
}

function allbuildingProdQty_eggs($product_id, $date, $branch_id){
		$company_id = $_SESSION['system']['company_id'];
		
		$fetch_st_bldg = mysql_query("SELECT ref_id from tbl_stock_transfer_bldg_eggs where status = 'F' and company_id = '$company_id' and branch_id = '$branch_id' and status = 'F' and date <= '$date' ");
		$stock_transfer_qty = 0;
		
		while($st_row = mysql_fetch_array($fetch_st_bldg)){
			$ref_id = $st_row['ref_id'];
			$st_bldg_qty = mysql_fetch_array(mysql_query("SELECT sum(actual_qty) from tbl_st_bldg_details_eggs where product_id = '$product_id' and company_id = '$company_id' and branch_id = '$branch_id' and status = 'F' and ref_id = '$ref_id' "));
			
			$stock_transfer_qty += $st_bldg_qty[0];
		}
		
		$fetch_st_bldg_bldg = mysql_query("SELECT * from tbl_stock_transfer_bldg_bldg_eggs where branch_id='$branch_id' AND company_id = '$company_id' and status = 'F'");
		$total_st_bldg_bldg = 0;
		
		while($row_st_bldg_bldg = mysql_fetch_array($fetch_st_bldg_bldg)){
			$st_bldg_bldg_ref_id = $row_st_bldg_bldg['ref_id'];
			
			$st_bldg_bldg_qty = mysql_fetch_array(mysql_query("SELECT sum(actual_qty) from tbl_std_bldg_bldg_eggs where ref_id = '$st_bldg_bldg_ref_id' and product_id = '$product_id' and date <= '$date' and status = 'F'"));
			
			$total_st_bldg_bldg += $st_bldg_bldg_qty[0];
		}
		
		$total_in_bldg = $stock_transfer_qty + $total_st_bldg_bldg;
		
		$final_total = $total_in_bldg - allbuildingInvDeductions_eggs($product_id, $date, $branch_id);
		
		return  $final_total;
}

function allbuildingCompanyProdQty_eggs($product_id, $date){
		$company_id = $_SESSION['system']['company_id'];
		$branch_id = get_branch();
		
		$fetch_st_bldg = mysql_query("SELECT ref_id from tbl_stock_transfer_bldg_eggs where (status = 'F' or status = 'R') and company_id = '$company_id' and status = 'F' and date <= '$date' ");
		$stock_transfer_qty = 0;
		
		while($st_row = mysql_fetch_array($fetch_st_bldg)){
			$ref_id = $st_row['ref_id'];
			$st_bldg_qty = mysql_fetch_array(mysql_query("SELECT sum(actual_qty) from tbl_st_bldg_details_eggs where product_id = '$product_id' and company_id = '$company_id' and (status = 'F' or status = 'R') and ref_id = '$ref_id' "));
			
			$stock_transfer_qty += $st_bldg_qty[0];
		}
		
		$fetch_st_bldg_bldg = mysql_query("SELECT * from tbl_stock_transfer_bldg_bldg_eggs where company_id = '$company_id' and status = 'F'");
		$total_st_bldg_bldg = 0;
		
		while($row_st_bldg_bldg = mysql_fetch_array($fetch_st_bldg_bldg)){
			$st_bldg_bldg_ref_id = $row_st_bldg_bldg['ref_id'];
			
			$st_bldg_bldg_qty = mysql_fetch_array(mysql_query("SELECT sum(actual_qty) from tbl_std_bldg_bldg_eggs where ref_id = '$st_bldg_bldg_ref_id' and product_id = '$product_id' and date <= '$date' and status = 'F'"));
			
			$total_st_bldg_bldg += $st_bldg_bldg_qty[0];
		}
		
		$total_in_bldg = $stock_transfer_qty + $total_st_bldg_bldg;
		
		$final_total = $total_in_bldg - allbuildingCompanyInvDeductions_eggs($product_id, $date);
		
		return  $final_total;
}

// --- end building inventory ---
function buildingProdQty_broiler($building_id, $product_id, $date){
		$company_id = $_SESSION['system']['company_id'];
		$branch_id = get_branch();
		
		$fetch_st_bldg = mysql_query("SELECT ref_id from tbl_stock_transfer_bldg_broiler where status = 'F' and company_id = '$company_id' and branch_id = '$branch_id' and  building_id = '$building_id' and status = 'F' and DATE_FORMAT(date,'%Y-%m-%d') <= '$date' ");
		$stock_transfer_qty = 0;
		
		while($st_row = mysql_fetch_array($fetch_st_bldg)){
			$ref_id = $st_row['ref_id'];
			$st_bldg_qty = mysql_fetch_array(mysql_query("SELECT sum(actual_qty) from tbl_st_bldg_details_broiler where product_id = '$product_id' and company_id = '$company_id' and branch_id = '$branch_id' and status = 'F' and ref_id = '$ref_id' "));
			
			$stock_transfer_qty += $st_bldg_qty[0];
		}
		
		$fetch_st_bldg_bldg = mysql_query("SELECT * from tbl_stock_transfer_bldg_bldg_broiler where destination_bldg = '$building_id' and company_id = '$company_id' and status = 'F'");
		$total_st_bldg_bldg = 0;
		
		while($row_st_bldg_bldg = mysql_fetch_array($fetch_st_bldg_bldg)){
			$st_bldg_bldg_ref_id = $row_st_bldg_bldg['ref_id'];
			
			$st_bldg_bldg_qty = mysql_fetch_array(mysql_query("SELECT sum(actual_qty) from tbl_std_bldg_bldg_broiler where ref_id = '$st_bldg_bldg_ref_id' and product_id = '$product_id' and DATE_FORMAT(date,'%Y-%m-%d') <= '$date' and status = 'F'"));
			
			$total_st_bldg_bldg += $st_bldg_bldg_qty[0];
		}
		
		$total_in_bldg = $stock_transfer_qty + $total_st_bldg_bldg;
		
		$final_total = $total_in_bldg - buildingInvDeductions_broiler($building_id, $product_id, $date);
		
		return  $final_total;
}

// --- end building inventory broiler ---


// DEDUCTIONS FROM PIG //
function buildingInvDeductions_pig($building_id, $product_id, $inventory_date){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
	
	$total_medication = mysql_fetch_array(mysql_query("SELECT sum(amount) from tbl_medication_pig where product_id = '$product_id' and building_id = '$building_id' and date <= '$inventory_date' and company_id = '$company_id'"));
	
	$total_vaccination = mysql_fetch_array(mysql_query("SELECT sum(v_dosage) from tbl_vaccine_pig where product_id = '$product_id' and building_id = '$building_id' and v_date <= '$inventory_date' and company_id = '$company_id'"));
	
	//$total_feeding = mysql_fetch_array(mysql_query("SELECT sum(amount) from tbl_feeding_per_pen where company_id='$company_id' and building_id='$building_id' and product_id='$product_id' and date_added <= '$inventory_date' "));

	// feeding summary archive
	$total_feeding = mysql_fetch_array(mysql_query("SELECT sum(quantity) from tbl_feeding_summary where company_id='$company_id' and branch_id='$branch_id' and product_id='$product_id' and building_id='$building_id' and date_added <= '$inventory_date' "));
	
	$total_ai_materials = mysql_fetch_array(mysql_query("SELECT sum(quantity) from tbl_ai_materials_pig where company_id='$company_id' and building_id='$building_id' and product_id='$product_id' and date_added <= '$inventory_date' "));

	$total_ai_semen = mysql_fetch_array(mysql_query("SELECT sum(quantity) from tbl_ai_semen_pig where company_id='$company_id' and building_id='$building_id' and product_id='$product_id' and date_added <= '$inventory_date' "));

	//$total_ai_semen = mysql_fetch_array(mysql_query("SELECT sum(CASE WHEN ai_semen1='$product_id' THEN qty1 ELSE 0 end) + sum(CASE WHEN ai_semen2='$product_id' THEN qty2 ELSE 0 end) + sum(CASE WHEN ai_semen3='$product_id' THEN qty3 ELSE 0 end) from tbl_breeding_pig where company_id='$company_id' and branch_id='$branch_id' and breeding_date <= '$inventory_date' and building_id = '$building_id' "));
	
	/*$total_st_bldg_warehouse = 0;
	$fetch_st_bldg_warehouse = mysql_query("SELECT * from tbl_stock_transfer_bldg_warehouse_pig where building_id = '$building_id' and company_id = '$company_id' and status = 'F'");
	while($row_st_bldg_warehouse = mysql_fetch_array($fetch_st_bldg_warehouse)){
		$st_bldg_ref_id = $row_st_bldg_warehouse['ref_id'];
		
		$st_bldg_warehouse_qty = mysql_fetch_array(mysql_query("SELECT sum(actual_qty) from tbl_std_bldg_warehouse_pig where ref_id = '$st_bldg_ref_id' and product_id = '$product_id' and date <= '$inventory_date' and status = 'F'"));
		
		$total_st_bldg_warehouse += $st_bldg_warehouse_qty[0];
	}*/

	$total_st_bldg_warehouse = mysql_fetch_array(mysql_query("SELECT sum(std.actual_qty) from tbl_stock_transfer_bldg_warehouse_pig as sth, tbl_std_bldg_warehouse_pig as std where sth.building_id = '$building_id' and sth.company_id = '$company_id' and sth.status = 'F' and sth.ref_id=std.ref_id and std.product_id = '$product_id' and std.date <= '$inventory_date' and std.status = 'F'"));
	
	/*$total_st_bldg_bldg = 0;
	$fetch_st_bldg_bldg = mysql_query("SELECT * from tbl_stock_transfer_bldg_bldg_pig where source_bldg = '$building_id' and company_id = '$company_id' and status = 'F'");
	while($row_st_bldg_bldg = mysql_fetch_array($fetch_st_bldg_bldg)){
		$st_bldg_bldg_ref_id = $row_st_bldg_bldg['ref_id'];
		
		$st_bldg_bldg_qty = mysql_fetch_array(mysql_query("SELECT sum(actual_qty) from tbl_std_bldg_bldg_pig where ref_id = '$st_bldg_bldg_ref_id' and product_id = '$product_id' and date <= '$inventory_date' and status = 'F'"));
		
		$total_st_bldg_bldg += $st_bldg_bldg_qty[0];
	}*/

	$total_st_bldg_bldg = mysql_fetch_array(mysql_query("SELECT sum(std.actual_qty) from tbl_stock_transfer_bldg_bldg_pig as sth, tbl_std_bldg_bldg_pig as std where sth.source_bldg = '$building_id' and sth.company_id = '$company_id' and sth.status = 'F' and sth.ref_id=std.ref_id and std.product_id = '$product_id' and std.date <= '$inventory_date' and std.status = 'F'"));

	
	return $total_medication[0] + $total_vaccination[0] + $total_feeding[0] + $total_ai_materials[0] + $total_st_bldg_warehouse[0] + $total_st_bldg_bldg[0] + $total_ai_semen[0];
	
}

function buildingInvDeductions_eggs($building_id, $product_id, $inventory_date){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
	
	$total_feeds_growing = mysql_fetch_array(mysql_query("SELECT sum(qty) from tbl_feeds_entry_eggs where item = '$product_id' and building_id = '$building_id' and DATE_FORMAT(date,'%Y-%m-%d') <= '$inventory_date' and company_id = '$company_id' and branch_id = '$branch_id' "));
	
	$total_madication_and_vaccination_growing = mysql_fetch_array(mysql_query("SELECT sum(qty) from tbl_medication_and_vaccination_entry_eggs where item = '$product_id' and building_id = '$building_id' and DATE_FORMAT(date,'%Y-%m-%d') <= '$inventory_date' and company_id = '$company_id' and branch_id = '$branch_id'"));
	
	$total_feeds_production = mysql_fetch_array(mysql_query("SELECT sum(qty) from tbl_feeds_entry_production_eggs where item = '$product_id' and building_id = '$building_id' and DATE_FORMAT(date,'%Y-%m-%d') <= '$inventory_date' and company_id = '$company_id' and branch_id = '$branch_id' "));

	$brooding_expense = mysql_fetch_array(mysql_query("SELECT sum(quantity) from tbl_expense_entry_eggs where product_id = '$product_id' and building_id = '$building_id' and DATE_FORMAT(date,'%Y-%m-%d') <= '$inventory_date' and company_id = '$company_id' and branch_id = '$branch_id' "));
	
	$total_madication_and_vaccination_production = mysql_fetch_array(mysql_query("SELECT sum(qty) from tbl_medication_and_vaccination_entry_production_eggs where item = '$product_id' and building_id = '$building_id' and DATE_FORMAT(date,'%Y-%m-%d') <= '$inventory_date' and company_id = '$company_id' and branch_id = '$branch_id'"));
	
	$total_st_bldg_warehouse = 0;
	$fetch_st_bldg_warehouse = mysql_query("SELECT * from tbl_stock_transfer_bldg_warehouse_eggs where building_id = '$building_id' and company_id = '$company_id' and status = 'F'");
	while($row_st_bldg_warehouse = mysql_fetch_array($fetch_st_bldg_warehouse)){
		$st_bldg_ref_id = $row_st_bldg_warehouse['ref_id'];
		
		$st_bldg_warehouse_qty = mysql_fetch_array(mysql_query("SELECT sum(actual_qty) from tbl_std_bldg_warehouse_eggs where ref_id = '$st_bldg_ref_id' and product_id = '$product_id' and DATE_FORMAT(date,'%Y-%m-%d') <= '$inventory_date' and status = 'F'"));
		
		$total_st_bldg_warehouse += $st_bldg_warehouse_qty[0];
	}
	
	$total_st_bldg_bldg = 0;
	$fetch_st_bldg_bldg = mysql_query("SELECT * from tbl_stock_transfer_bldg_bldg_eggs where source_bldg = '$building_id' and company_id = '$company_id' and status = 'F'");
	while($row_st_bldg_bldg = mysql_fetch_array($fetch_st_bldg_bldg)){
		$st_bldg_bldg_ref_id = $row_st_bldg_bldg['ref_id'];
		
		$st_bldg_bldg_qty = mysql_fetch_array(mysql_query("SELECT sum(actual_qty) from tbl_std_bldg_bldg_eggs where ref_id = '$st_bldg_bldg_ref_id' and product_id = '$product_id' and DATE_FORMAT(date,'%Y-%m-%d') <= '$inventory_date' and status = 'F'"));
		
		$total_st_bldg_bldg += $st_bldg_bldg_qty[0];
	}

	

	return $total_feeds_growing[0] + $total_madication_and_vaccination_growing[0] + $total_feeds_production[0] + $total_madication_and_vaccination_production[0] + $total_st_bldg_warehouse + $total_st_bldg_bldg + $brooding_expense[0];
	
}

function allbuildingInvDeductions_eggs($product_id, $inventory_date, $branch_id){
	$company_id = $_SESSION['system']['company_id'];
	
	$total_feeds_growing = mysql_fetch_array(mysql_query("SELECT sum(qty) from tbl_feeds_entry_eggs where item = '$product_id' and DATE_FORMAT(date,'%Y-%m-%d') <= '$inventory_date' and company_id = '$company_id' and branch_id = '$branch_id' "));
	
	$total_madication_and_vaccination_growing = mysql_fetch_array(mysql_query("SELECT sum(qty) from tbl_medication_and_vaccination_entry_eggs where item = '$product_id' and DATE_FORMAT(date,'%Y-%m-%d') <= '$inventory_date' and company_id = '$company_id' and branch_id = '$branch_id'"));
	
	$total_feeds_production = mysql_fetch_array(mysql_query("SELECT sum(qty) from tbl_feeds_entry_production_eggs where item = '$product_id' and DATE_FORMAT(date,'%Y-%m-%d') <= '$inventory_date' and company_id = '$company_id' and branch_id = '$branch_id' "));

	$brooding_expense = mysql_fetch_array(mysql_query("SELECT sum(quantity) from tbl_expense_entry_eggs where product_id = '$product_id' and DATE_FORMAT(date,'%Y-%m-%d') <= '$inventory_date' and company_id = '$company_id' and branch_id = '$branch_id' "));
	
	$total_madication_and_vaccination_production = mysql_fetch_array(mysql_query("SELECT sum(qty) from tbl_medication_and_vaccination_entry_production_eggs where item = '$product_id' and DATE_FORMAT(date,'%Y-%m-%d') <= '$inventory_date' and company_id = '$company_id' and branch_id = '$branch_id'"));
	
	$total_st_bldg_warehouse = 0;
	$fetch_st_bldg_warehouse = mysql_query("SELECT * from tbl_stock_transfer_bldg_warehouse_eggs where branch_id='$branch_id' AND company_id = '$company_id' and status = 'F'");
	while($row_st_bldg_warehouse = mysql_fetch_array($fetch_st_bldg_warehouse)){
		$st_bldg_ref_id = $row_st_bldg_warehouse['ref_id'];
		
		$st_bldg_warehouse_qty = mysql_fetch_array(mysql_query("SELECT sum(actual_qty) from tbl_std_bldg_warehouse_eggs where ref_id = '$st_bldg_ref_id' and product_id = '$product_id' and DATE_FORMAT(date,'%Y-%m-%d') <= '$inventory_date' and status = 'F'"));
		
		$total_st_bldg_warehouse += $st_bldg_warehouse_qty[0];
	}
	
	$total_st_bldg_bldg = 0;
	$fetch_st_bldg_bldg = mysql_query("SELECT * from tbl_stock_transfer_bldg_bldg_eggs where branch_id='$branch_id' AND company_id = '$company_id' and status = 'F'");
	while($row_st_bldg_bldg = mysql_fetch_array($fetch_st_bldg_bldg)){
		$st_bldg_bldg_ref_id = $row_st_bldg_bldg['ref_id'];
		
		$st_bldg_bldg_qty = mysql_fetch_array(mysql_query("SELECT sum(actual_qty) from tbl_std_bldg_bldg_eggs where ref_id = '$st_bldg_bldg_ref_id' and product_id = '$product_id' and DATE_FORMAT(date,'%Y-%m-%d') <= '$inventory_date' and status = 'F'"));
		
		$total_st_bldg_bldg += $st_bldg_bldg_qty[0];
	}

	

	return $total_feeds_growing[0] + $total_madication_and_vaccination_growing[0] + $total_feeds_production[0] + $total_madication_and_vaccination_production[0] + $total_st_bldg_warehouse + $total_st_bldg_bldg + $brooding_expense[0];
	
}

function allbuildingCompanyInvDeductions_eggs($product_id, $inventory_date){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
	
	$total_feeds_growing = mysql_fetch_array(mysql_query("SELECT sum(qty) from tbl_feeds_entry_eggs where item = '$product_id' and DATE_FORMAT(date,'%Y-%m-%d') <= '$inventory_date' and company_id = '$company_id' "));
	
	$total_madication_and_vaccination_growing = mysql_fetch_array(mysql_query("SELECT sum(qty) from tbl_medication_and_vaccination_entry_eggs where item = '$product_id' and DATE_FORMAT(date,'%Y-%m-%d') <= '$inventory_date' and company_id = '$company_id'"));
	
	$total_feeds_production = mysql_fetch_array(mysql_query("SELECT sum(qty) from tbl_feeds_entry_production_eggs where item = '$product_id' and DATE_FORMAT(date,'%Y-%m-%d') <= '$inventory_date' and company_id = '$company_id' "));

	$brooding_expense = mysql_fetch_array(mysql_query("SELECT sum(quantity) from tbl_expense_entry_eggs where product_id = '$product_id' and DATE_FORMAT(date,'%Y-%m-%d') <= '$inventory_date' and company_id = '$company_id' "));
	
	$total_madication_and_vaccination_production = mysql_fetch_array(mysql_query("SELECT sum(qty) from tbl_medication_and_vaccination_entry_production_eggs where item = '$product_id' and DATE_FORMAT(date,'%Y-%m-%d') <= '$inventory_date' and company_id = '$company_id'"));
	
	$total_st_bldg_warehouse = 0;
	$fetch_st_bldg_warehouse = mysql_query("SELECT * from tbl_stock_transfer_bldg_warehouse_eggs where company_id = '$company_id' and status = 'F'");
	while($row_st_bldg_warehouse = mysql_fetch_array($fetch_st_bldg_warehouse)){
		$st_bldg_ref_id = $row_st_bldg_warehouse['ref_id'];
		
		$st_bldg_warehouse_qty = mysql_fetch_array(mysql_query("SELECT sum(actual_qty) from tbl_std_bldg_warehouse_eggs where ref_id = '$st_bldg_ref_id' and product_id = '$product_id' and DATE_FORMAT(date,'%Y-%m-%d') <= '$inventory_date' and status = 'F'"));
		
		$total_st_bldg_warehouse += $st_bldg_warehouse_qty[0];
	}
	
	$total_st_bldg_bldg = 0;
	$fetch_st_bldg_bldg = mysql_query("SELECT * from tbl_stock_transfer_bldg_bldg_eggs where company_id = '$company_id' and status = 'F'");
	while($row_st_bldg_bldg = mysql_fetch_array($fetch_st_bldg_bldg)){
		$st_bldg_bldg_ref_id = $row_st_bldg_bldg['ref_id'];
		
		$st_bldg_bldg_qty = mysql_fetch_array(mysql_query("SELECT sum(actual_qty) from tbl_std_bldg_bldg_eggs where ref_id = '$st_bldg_bldg_ref_id' and product_id = '$product_id' and DATE_FORMAT(date,'%Y-%m-%d') <= '$inventory_date' and status = 'F'"));
		
		$total_st_bldg_bldg += $st_bldg_bldg_qty[0];
	}

	

	return $total_feeds_growing[0] + $total_madication_and_vaccination_growing[0] + $total_feeds_production[0] + $total_madication_and_vaccination_production[0] + $total_st_bldg_warehouse + $total_st_bldg_bldg + $brooding_expense[0];
	
}

// END DEDUCTIONS FROM PIG AND EGGS//
function buildingInvDeductions_broiler($building_id, $product_id, $inventory_date){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
	
	$total_feeds_growing = mysql_fetch_array(mysql_query("SELECT sum(qty) from tbl_feeds_entry_broiler where item = '$product_id' and building_id = '$building_id' and DATE_FORMAT(date,'%Y-%m-%d') <= '$inventory_date' and company_id = '$company_id' and branch_id = '$branch_id' "));
	
	$total_madication_and_vaccination_growing = mysql_fetch_array(mysql_query("SELECT sum(qty) from tbl_medication_and_vaccination_entry_broiler where item = '$product_id' and building_id = '$building_id' and DATE_FORMAT(date,'%Y-%m-%d') <= '$inventory_date' and company_id = '$company_id' and branch_id = '$branch_id'"));
	
	$total_feeds_production = mysql_fetch_array(mysql_query("SELECT sum(qty) from tbl_feeds_entry_production_broiler where item = '$product_id' and building_id = '$building_id' and DATE_FORMAT(date,'%Y-%m-%d') <= '$inventory_date' and company_id = '$company_id' and branch_id = '$branch_id' "));
	
	$brooding_expense = mysql_fetch_array(mysql_query("SELECT sum(quantity) from tbl_expense_entry_broiler where product_id = '$product_id' and building_id = '$building_id' and DATE_FORMAT(date,'%Y-%m-%d') <= '$inventory_date' and company_id = '$company_id' and branch_id = '$branch_id' "));

	$total_madication_and_vaccination_production = mysql_fetch_array(mysql_query("SELECT sum(qty) from tbl_medication_and_vaccination_entry_production_broiler where item = '$product_id' and building_id = '$building_id' and DATE_FORMAT(date,'%Y-%m-%d') <= '$inventory_date' and company_id = '$company_id' and branch_id = '$branch_id'"));
	
	$total_st_bldg_warehouse = 0;
	$fetch_st_bldg_warehouse = mysql_query("SELECT * from tbl_stock_transfer_bldg_warehouse_broiler where building_id = '$building_id' and company_id = '$company_id' and status = 'F'");
	while($row_st_bldg_warehouse = mysql_fetch_array($fetch_st_bldg_warehouse)){
		$st_bldg_ref_id = $row_st_bldg_warehouse['ref_id'];
		
		$st_bldg_warehouse_qty = mysql_fetch_array(mysql_query("SELECT sum(actual_qty) from tbl_std_bldg_warehouse_broiler where ref_id = '$st_bldg_ref_id' and product_id = '$product_id' and DATE_FORMAT(date,'%Y-%m-%d') <= '$inventory_date' and status = 'F'"));
		
		$total_st_bldg_warehouse += $st_bldg_warehouse_qty[0];
	}
	
	$total_st_bldg_bldg = 0;
	$fetch_st_bldg_bldg = mysql_query("SELECT * from tbl_stock_transfer_bldg_bldg_broiler where source_bldg = '$building_id' and company_id = '$company_id' and status = 'F'");
	while($row_st_bldg_bldg = mysql_fetch_array($fetch_st_bldg_bldg)){
		$st_bldg_bldg_ref_id = $row_st_bldg_bldg['ref_id'];
		
		$st_bldg_bldg_qty = mysql_fetch_array(mysql_query("SELECT sum(actual_qty) from tbl_std_bldg_bldg_broiler where ref_id = '$st_bldg_bldg_ref_id' and product_id = '$product_id' and DATE_FORMAT(date,'%Y-%m-%d') <= '$inventory_date' and status = 'F'"));
		
		$total_st_bldg_bldg += $st_bldg_bldg_qty[0];
	}
	

	return $total_feeds_growing[0] + $total_madication_and_vaccination_growing[0] + $total_feeds_production[0] + $total_madication_and_vaccination_production[0] + $total_st_bldg_warehouse + $total_st_bldg_bldg + $brooding_expense[0];
	
}
// END DEDUCTIONS FROM Broiler//

function insertLog($user_id,$action,$module, $auth = NULL){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
	$date = getCurrentDate();
	if (!empty($auth)){
		$isAuth = $auth;
	}else{
		$isAuth = 0;
	}
	//mysql_query("USE main_notes_logs") or die(mysql_error());
	$insertMe = mysql_query("INSERT INTO `logs`( `user_id`, `action`, `module`, `date_added`, `company_id`, `branch_id`, isAuthentication) VALUES ($user_id,'$action' ,'$module','$date','$company_id','$branch_id', '$isAuth')");
	
	//$database = database;
	//mysql_query("USE $database") or die(mysql_error());
}

function insertSwineLog($user_id,$action,$module,$reference_id, $transaction_date){


	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
	$date = getCurrentDate();


	//mysql_query("USE main_notes_logs") or die(mysql_error());
	
	$count_row = mysql_fetch_array(mysql_query("SELECT count(log_id) from tbl_swine_logs where company_id='$company_id' and branch_id='$branch_id' and module='$module' and reference_id='$reference_id' and transaction_date='$transaction_date' "));

	if($count_row[0] == 0){
		mysql_query("INSERT INTO `tbl_swine_logs`(`company_id`, `branch_id`, `module`, `reference_id`, `action`, `user_id`, transaction_date ,`date_added`) VALUES ('$company_id','$branch_id','$module','$reference_id','$action','$user_id', '$transaction_date' ,'$date')") or die(mysql_error());
	}

	//$database = database;
	//mysql_query("USE $database") or die(mysql_error());
	
}

function insertINV_add($company_id, $branch_id, $warehouse_id, $ref_number, $prod_id, $cost, $package_id, $qty, $inv_date, $building_id = NULL){

	if(!empty($building_id)){
		$buildingID = $building_id;
	}else{
		$buildingID = 0;
	}
	
	//mysql_query("INSERT INTO tbl_inventory_add SET company_id = '$company_id', branch_id = '$branch_id', warehouse_id = '$warehouse_id', building_id = '$buildingID', ref_number = '$ref_number', prod_id = '$prod_id', cost = '$cost', package_id = '$package_id', qty = '$qty', inv_date = '$inv_date'");
}

function insertINV_deduct($company_id, $branch_id, $warehouse_id, $ref_number, $prod_id, $cost, $package_id, $qty, $inv_date, $building_id = NULL){

	if(!empty($building_id)){
		$buildingID = $building_id;
	}else{
		$buildingID = 0;
	}
	
	//mysql_query("INSERT INTO tbl_inventory_deduct SET company_id = '$company_id', branch_id = '$branch_id', warehouse_id = '$warehouse_id', building_id = '$buildingID', ref_number = '$ref_number', prod_id = '$prod_id', cost = '$cost', package_id = '$package_id', qty = '$qty', inv_date = '$inv_date'");
}

function getLogged($id){
	$db = $GLOBALS['config']['mysql']['database'];
	$user_CODE = $_SESSION['system']['user_code'];
	mysql_query("USE $db") or die(mysql_error());
	
	$result = mysql_query("SELECT name, category_id FROM tbl_users WHERE user_code = '$user_CODE'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["name"];
}

function getRSNumber($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT requisition_num from tbl_requisition where requisition_id = '$id' and company_id='$company_id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["requisition_num"];
}

function countUsers($id){
	$result = mysql_query("select count(*) as total from `tbl_users` where company_id = '$id'") or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["total"];
}

function checkLoggedUser($id){
	$result = mysql_query("select * from  tbl_users where user_id ='$id' and category_id = 0") or die (mysql_error());
	$count = mysql_num_rows($result);
	$row  = mysql_fetch_assoc($result);
	
	if($count > 0){
		return "ROOT";
	}else{
		return "OWNER";
	}
}

function getCompany($id){
	$result = mysql_query("select * from tbl_users as s,tbl_company as c where s.company_id = c.company_id and s.user_id = '$id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["company_name"];
}

function getCompanyName($id){
	$result = mysql_query("select company_name from tbl_company where company_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["company_name"];
}

function getCompanyAdd($id){
	$result = mysql_query("SELECT company_address from tbl_company where company_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["company_address"];
}
function getCompanyEmailAdd($id){
	$result = mysql_query("SELECT email_address from tbl_company where company_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["email_address"];
}
function getCompanyTel($id){
	$result = mysql_query("SELECT tel_number from tbl_company where company_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["tel_number"];
}
function getCompanyFax($id){
	$result = mysql_query("SELECT fax_number from tbl_company where company_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["fax_number"];
}
function getCompanyTin($id){
	$result = mysql_query("SELECT tin_number from tbl_company where company_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["tin_number"];
}
function getCompanyZipCode($id){
	$result = mysql_query("SELECT zip_code from tbl_company where company_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["zip_code"];
}
//image
function getImage($id){
	$result = mysql_query("SELECT image from tbl_company where company_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["image"];
}
//image
function getLayerBreedName($id){
	$result = mysql_query("select * from tbl_breed_eggs where breed_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["breed"];
}

function getLayerBreedNameBroiler($id){
	$company_id = $_SESSION['system']['company_id'];
	//$branch_id = get_branch();
	$result = mysql_query("select * from tbl_breed_broiler where company_id = '$company_id' and breed_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["breed"];
}

function getGchartMainWithCode($id){
	$company_id = $_SESSION['system']['company_id'];
	//$branch_id = get_branch();
	$result = mysql_query("select * from tbl_gchart_main where company_id = '$company_id' and gchart_main_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["acode"]." - ".$row["chart"];
}

function getMainAccount($id){
	$company_id = $_SESSION['system']['company_id'];
	//$branch_id = get_branch();
	$result = mysql_query("select * from tbl_gchart_main where company_id = '$company_id' and gchart_main_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["acode"]." - ".$row["chart"];
}

function getSubAccount($id){
	$company_id = $_SESSION['system']['company_id'];
	//$branch_id = get_branch();
	$result = mysql_query("select * from tbl_gchart_sub where company_id = '$company_id' and gchart_sub_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["s_code"]." - ".$row["s_chart"];
}

function getFeedsEntryName($id){
	$result = mysql_query("select * from tbl_feeds_entry where feeds_entry_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return getProdName($row["item"]);
}

function getFarmStandardsName($id){
	$result = mysql_query("select * from tbl_farm_standards where farm_standard_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return getLayerBreedName($row["breed_id"]);
}
function getFeedsEntryProduction($id){
	$result = mysql_query("select * from tbl_feeds_entry_production_eggs where feeds_entry_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return getProdName($row["item"]);
}

function getFeedsEntryProductionGlRef($id){
	$result = mysql_query("select * from tbl_feeds_entry_production_eggs where gl_ref_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return getProdName($row["item"]);
}

function getFeedsEntryProductionBroiler($id){
	$result = mysql_query("select * from tbl_feeds_entry_production_broiler where feeds_entry_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return getProdName($row["item"]);
}

function getMedicationEntry($id){
	$result = mysql_query("select * from tbl_medication_and_vaccination_entry_eggs where medication_entry_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return getProdName($row["item"]);
}

function getMedicationEntryP($id){
	$result = mysql_query("select * from tbl_medication_and_vaccination_entry_production_eggs where medication_entry_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return getProdName($row["item"]);
}

function getMedicationEntryBroiler($id){
	$result = mysql_query("select * from tbl_medication_and_vaccination_entry_broiler where medication_entry_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return getProdName($row["item"]);
}

function getMedicationEntryPBroiler($id){
	$result = mysql_query("select * from tbl_medication_and_vaccination_entry_production_broiler where medication_entry_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return getProdName($row["item"]);
}

function getMortEntry($id){
	$result = mysql_query("select * from tbl_mortality_entry_eggs where mortality_entry_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["qty"];
}

function getMortEntryP($id){
	$result = mysql_query("select * from tbl_mortality_entry_production_eggs where mortality_entry_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["qty"];
}

function getMortEntryBroiler($id){
	$result = mysql_query("select * from tbl_mortality_entry_broiler where mortality_entry_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["qty"];
}

function getMortEntryPBroiler($id){
	$result = mysql_query("select * from tbl_mortality_entry_production_broiler where mortality_entry_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["qty"];
}

function getDepEntry($id){
	$result = mysql_query("select * from tbl_depopulate_entry_eggs where depopulate_entry_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["qty"];
}

function getDepEntryP($id){
	$result = mysql_query("select * from tbl_depopulate_entry_production_eggs where depopulate_entry_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["qty"];
}

function getDepEntryBroiler($id){
	$result = mysql_query("select * from tbl_depopulate_entry_broiler where depopulate_entry_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["qty"];
}

function getDepEntryPBroiler($id){
	$result = mysql_query("select * from tbl_depopulate_entry_production_broiler where depopulate_entry_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["qty"];
}

function getBodyWeightEntry($id){
	$result = mysql_query("select * from tbl_body_weight_entry_eggs where body_weight_entry_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["weight"];
}

function getBodyWeightEntryP($id){
	$result = mysql_query("select * from tbl_body_weight_entry_production_eggs where body_weight_entry_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["weight"];
}

function getBodyWeightEntryBroiler($id){
	$result = mysql_query("select * from tbl_body_weight_entry_broiler where body_weight_entry_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["weight"];
}

function getBodyWeightEntryPBroiler($id){
	$result = mysql_query("select * from tbl_body_weight_entry_production_broiler where body_weight_entry_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["weight"];
}

function getWaterEntry($id){
	$result = mysql_query("select * from tbl_water_entry_eggs where water_entry_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["qty"];
}

function getWaterEntryP($id){
	$result = mysql_query("select * from tbl_water_entry_production_eggs where water_entry_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["qty"];
}

function getWaterEntryBroiler($id){
	$result = mysql_query("select * from tbl_water_entry_broiler where water_entry_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["qty"];
}

function getWaterEntryPBroiler($id){
	$result = mysql_query("select * from tbl_water_entry_production_broiler where water_entry_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["qty"];
}


function getBWEntry($id){
	$result = mysql_query("select * from tbl_body_weight_entry where body_weight_entry_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return date('F d, Y',strtotime($row["date"]));
}

function getBuildingName($id){
	$result = mysql_query("select * from tbl_building_eggs where building_id = '$id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["building_name"];
}
function getBuildingNameBroiler($id){
	$result = mysql_query("select * from tbl_building_broiler where building_id = '$id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["building_name"];
}

function getBuildingName_pig($id){
	$result = mysql_query("SELECT building_name from tbl_building_pig where building_id = '$id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["building_name"];
}

function getProduction($id){
	$result = mysql_query("select * from tbl_production_eggs where production_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["batch_name"];
}

function getProductionBroiler($id){
	$result = mysql_query("select * from tbl_production_broiler where production_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["batch_name"];
}

function getCompanyId($id){
	$result = mysql_query("select * from tbl_users where user_id = '$id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["company_id"];
}

function getCategory($id){
	$result = mysql_query("select * from tbl_category where category_id = '$id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["category"];
}


function getCategory_name($company_id, $category_id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("select * from tbl_category where category_id = $category_id and company_id=$company_id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["category"];
}

function getSwine($id){
	$company_id = $_SESSION['system']['company_id'];
	//$branch_id = get_branch();
	
	$result = mysql_query("SELECT swine_code from tbl_swine where swine_id = '$id' and company_id='$company_id' ") or die (mysql_error());

	if(mysql_num_rows($result) > 0){
		$row = mysql_fetch_array($result);
	}else{
		$result1 = mysql_query("SELECT swine_code from tbl_swine_archive where swine_id = '$id' and company_id='$company_id' ") or die (mysql_error());
		$row = mysql_fetch_array($result1);
	}
	
	
	return $row[0];
}

function getSwineArchive($id){
	$company_id = $_SESSION['system']['company_id'];
	//$branch_id = get_branch();
	
	$result = mysql_query("SELECT swine_code from tbl_swine_archive where swine_id = '$id' and company_id='$company_id' ") or die (mysql_error());
	$row = mysql_fetch_array($result);
	
	return $row[0];
}

function getLinkSwine($id,$pen_code){
	$company_id = $_SESSION['system']['company_id'];

	$fetch_swine = mysql_query("SELECT swine_id FROM tbl_swine WHERE company_id='$company_id'  and swine_id='$id' ");
	if(mysql_num_rows($fetch_swine)>0){
		if($pen_code != 0){ // non-piglet
			$swine_info = '<a href="index.php?page='.url_page('view-piglets').'&id='.$id.'&p='.$pen_code.'"  target="blank" >'.getSwine($id).'</a>';
		}else{
			$swine_info = '<a href="index.php?page='.url_page('view-swine').'&id='.$id.'" target="blank">'.getSwine($id).'</a>';
		}
	}else{
		if($pen_code != 0){ // non-piglet
			$swine_info = '<a href="index.php?page='.url_page('view-piglets').'&id='.$id.'&p='.$pen_code.'"  target="blank">'.getSwineArchive($id).'</a>';

		}else{
			$swine_info = '<a href="index.php?page='.url_page('view-swine').'&id='.$id.'" target="blank">'.getSwineArchive($id).'</a>';
		}
	}

	return $swine_info;
}

function swineID($swine_code){
	$company_id = $_SESSION['system']['company_id'];
	//$branch_id = get_branch();
	
	$result = mysql_query("SELECT swine_id from tbl_swine where swine_code = '$swine_code' and company_id='$company_id' ") or die (mysql_error());
	$row = mysql_fetch_array($result);
	
	return $row[0];
}

function getExpenseName($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT expense_category_name FROM `tbl_expense_category` WHERE expense_id = '$id' and company_id='$company_id'") or die (mysql_error());
	$row = mysql_fetch_array($result);
	
	return $row[0];
}

function getExpenseAccount($id){
	$company_id = $_SESSION['system']['company_id'];
	
	$result = mysql_query("SELECT account_name FROM `tbl_expense_account` WHERE account_id = '$id' and company_id='$company_id'") or die (mysql_error());
	$row = mysql_fetch_array($result);
	
	return $row[0];
}

function getProductCateg($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT * from tbl_product_category where product_categ_id = '$id' and (company_id='$company_id' OR company_id='0')") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["category"];
}

function getProductCategByProductID($id){
	$company_id = $_SESSION['system']['company_id'];

	$product_categ_id = mysql_fetch_array(mysql_query("SELECT product_categ_id FROM `tbl_productmaster` WHERE product_id='$id' AND (company_id='$company_id' OR company_id='0')"));

	$result = mysql_query("SELECT * from tbl_product_category where product_categ_id = '$product_categ_id[0]' and (company_id='$company_id' OR company_id='0')") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["category"];
}


function getGenetic_pig($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT genetic from tbl_genetic_pig where genetic_id = '$id' and company_id='$company_id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["genetic"];
}

function getGeneticLine_pig($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT genetic_line from tbl_genetic_line_pig where genetic_line_id = '$id' and company_id='$company_id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["genetic_line"];
}


function getProgeny_pig($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT * from tbl_progeny_pig where classification_id = '$id' and (company_id='$company_id' or company_id=0) ") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["classification"];
}

function getPenName_pig($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT pen_name from tbl_pen_assignment where pen_assignment_id = '$id' and company_id='$company_id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["pen_name"];
}

function getSwinePen($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT pen_code from tbl_swine where swine_id = '$id' and company_id='$company_id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row['pen_code'];
}

function getPenName_broiler($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT pen_name from tbl_pen_assignment_broiler where pen_assignment_id = '$id' and company_id='$company_id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["pen_name"];
}

function getUserCode($id,$com_id = -1){
	$company_id = ($company_id == -1) ? $_SESSION['system']['company_id'] : $com_id;
	$result = mysql_query("SELECT user_code from tbl_users where user_id = '$id' and company_id='$company_id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["user_code"];
}

function getUser($id){
	// $database = database;
	// mysql_query("USE $database") or die(mysql_error());
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT name from tbl_users where user_id = '$id' and company_id='$company_id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["name"];
}

function getCustomer($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT * from tbl_customer where customer_id = '$id' and company_id = '$company_id' ") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["customer"];
}

function getCustomerCombo($company, $branch){
	$result = mysql_query("select * from tbl_customer where company_id = '$company' and branch_id = '$branch'") or die (mysql_error());
	$content = "<option value='0'>All Customers</option>";
	while($row = mysql_fetch_assoc($result)){
	$content .= "<option value='".$row["customer_id"]."'>". $row["customer"] ."</option>";
	}
	return $content;
}

function getGrowingModule($id){
	$result = mysql_query("select * from tbl_growing_module_eggs where growing_id = '$id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["batch_name"];
}

function getGrowingModuleBroiler($id){
	$result = mysql_query("select * from tbl_growing_module_broiler where growing_id = '$id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["batch_name"];
}

function getEmployee($id){
	$branch_id = get_branch();
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT * from tbl_employee where employee_id='$id' and company_id='$company_id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);

	if(mysql_num_rows($result) > 0){
		return $row["emp_lastname"].", ".$row["emp_firstname"]." ".$row["emp_middlename"];
	}else{
		return "Employee not found";
	}
}

function getEmployeePosition($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT * from tbl_emp_class where emp_class_id='$id' and (company_id='$company_id' OR company_id=0)") or die (mysql_error());
	$row = mysql_fetch_assoc($result);

	if(mysql_num_rows($result) > 0){
		return $row["emp_class"];
	}else{
		return "Employee not found";
	}
}

function getEmployeeTin($id){
	$branch_id = get_branch();
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT * from tbl_employee where employee_id='$id' and company_id='$company_id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);

	if(mysql_num_rows($result) > 0){
		return $row["emp_tin"];
	}else{
		return "Employee not found";
	}
}

function getDiseases($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT * from tbl_diseases where disease_id = '$id' and company_id='$company_id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["cause"];
}

function getDiseasesEggs($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT * from tbl_diseases_eggs where disease_id = '$id' and company_id='$company_id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["cause"];
}

function getDiseasesBroiler($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT * from tbl_diseases_broiler where disease_id = '$id' and company_id='$company_id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["cause"];
}


function getFeedingGuide($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT * from tbl_feeding_guide_pig where feeding_guide_id = '$id' and company_id='$company_id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return getPTypeName($row["pen_type"]).' - Feed type: '. getProdName($row['product_id']);
}

function getBranch($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT branch from tbl_branch where branch_id = '$id' and company_id='$company_id' ") or die (mysql_error());
	$row = mysql_fetch_array($result);
	
	return $row[0];
}

function getEmpClass($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT * from tbl_emp_class where emp_class_id = '$id' and (company_id='$company_id' or company_id=0) ") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["emp_class"];
}

function getExpCateg($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT * from tbl_expense_category where expense_id = '$id' and company_id='$company_id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["expense_category_name"];
}

function getProdCost($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT * from tbl_productmaster where product_id = '$id' and company_id='$company_id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["cost"];
}

function getProdName($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT product from tbl_productmaster where product_id = '$id' AND (company_id='$company_id' or company_id='0')") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["product"];
}

function getProdNameByCode($prod_code){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT product from tbl_productmaster where product_code = '$prod_code' AND (company_id='$company_id' or company_id='0')") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["product"];
}


function getSupplier($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT supplier FROM tbl_supplier WHERE supplier_id = '$id' AND company_id = '$company_id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["supplier"];
}

function getWarehouse($id){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
	$result = mysql_query("SELECT warehouse_name from tbl_warehouse where warehouse_id = '$id' and company_id='$company_id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	return $row["warehouse_name"];
}

function getWH($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT warehouse_name from tbl_warehouse where warehouse_id = '$id' and company_id='$company_id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	return $row["warehouse_name"];
}

function getWarehouseCombo(){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
	$result = mysql_query("SELECT * from tbl_warehouse where company_id='$company_id' and branch_id='$branch_id' ") or die (mysql_error());
	echo "<option value=''>Select Warehouse:</option>";
	while($row = mysql_fetch_assoc($result)){
		echo "<option value='".$row["warehouse_id"]."'>". $row["warehouse_name"] ."</option>";
	}
}

function categoryDropdown($id,$company_id){
	$result = mysql_query("select * from tbl_category where company_id = '$company_id'") or die (mysql_error());
	while($row = mysql_fetch_assoc($result)){
		if($row["category_id"] == $id){
			echo "<option selected value='".$row["category_id"]."'>".$row["category"]."</option>";
		}else{
			echo "<option value='".$row["category_id"]."'>".$row["category"]."</option>";
		}
	}
}

function getAccessType($category_id, $company_id){
	$company_id = $_SESSION['system']['company_id'];
	$access_type_ses = mysql_fetch_array(mysql_query("SELECT access_type from tbl_category where category_id='$category_id' and company_id='$company_id' "));
	return $access_type_ses[0];
}

function getUserMembeshipDate($user_id){
	$user_membership = mysql_fetch_array(mysql_query("SELECT date_added from tbl_users where user_id='$user_id'"));
	
	return $user_membership[0];
}

function classDropdown($id,$val){
	$result = mysql_query("select * from tbl_classification where company_id = '$id'") or die(mysql_error());
	while($row = mysql_fetch_assoc($result)){
		if($row["classification_id"] == $val){
			echo "<option value=".$row["classification_id"]." selected>".$row["classification"]."</option>";
		}else{
			echo  "<option value=".$row["classification_id"].">".$row["classification"]."</option>";
		}
	}
}

function genDropdown($id,$val){
	$result = mysql_query("select * from tbl_genetic where company_id = '$id'") or die(mysql_error());
	while($row = mysql_fetch_assoc($result)){
		if($row["genetic_id"] == $val){
			echo  "<option value=".$row["genetic_id"]." selected>".$row["genetic"]."</option>";
		}else{
			echo  "<option value=".$row["genetic_id"].">".$row["genetic"]."</option>";
		}
	}
}


function genDropdown_pig($id, $val){
	$result = mysql_query("select * from tbl_genetic_pig where company_id = '$id'") or die(mysql_error());
	while($row = mysql_fetch_assoc($result)){
		if($row["genetic_id"] == $val){
			echo  "<option value=".$row["genetic_id"]." selected>".$row["genetic"]."</option>";
		}else{
			echo  "<option value=".$row["genetic_id"].">".$row["genetic"]."</option>";
		}
	}
}

function geneticBreed_pig($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_fetch_array(mysql_query("SELECT genetic from tbl_genetic_pig where genetic_id = '$id' and company_id = '$company_id'"));
	
	return $result[0];
}

function geneticLine_pig($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_fetch_array(mysql_query("SELECT genetic_line from tbl_genetic_line_pig where genetic_line_id = '$id' and company_id = '$company_id'"));
	
	return $result[0];
}

function genLineDropdown_pig($id,$val){
	$result = mysql_query("select * from tbl_genetic_line_pig where company_id = '$id'") or die(mysql_error());
	while($row = mysql_fetch_assoc($result)){
		if($row["genetic_line_id"] == $val){
			echo  "<option value=".$row["genetic_line_id"]." selected>".$row["genetic_line"]."</option>";
		}else{
			echo  "<option value=".$row["genetic_line_id"].">".$row["genetic_line"]."</option>";
		}
	}
}


function progenyDropdown_pig(){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT * from tbl_progeny_pig where (company_id = '$company_id' or company_id = 0)") or die(mysql_error());
	while($row = mysql_fetch_assoc($result)){
		if($row["classification_id"] == $val){
			echo "<option value=".$row["classification_id"]." selected>".$row["classification"]."</option>";
		}else{
			echo  "<option value=".$row["classification_id"].">".$row["classification"]."</option>";
		}
	}
}
function AcessTypeDropdown($id,$val){
	//$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT * from tbl_category where company_id = '$id' ORDER by category ASC") or die(mysql_error());
	while($row = mysql_fetch_assoc($result)){
		if($row["category_id"] == $val){
			echo  "<option value=".$row["category_id"]." selected>".$row["category"]."</option>";
		}else{
			echo  "<option value=".$row["category_id"].">".$row["category"]."</option>";
		}
	}
}

function get_branch(){
	$company_id = $_SESSION['system']['company_id'];
	$category_id = $_SESSION['system']['category_id'];
	$get_access_type = getAccessType($category_id, $company_id);
	if($get_access_type == "all" or ($category_id == 0 and $company_id != 0)){
		$branch_id = $_SESSION["branch"];
	}else if($company_id == 0 and $category_id == 0){
		$branch_id = $_SESSION["branch"];
	}else{
		$branch_id = $_SESSION["branch"];
	}
	
	return $branch_id;
}

function checkPO($id){
	$result =mysql_query ("select * from tbl_po_details where po_header_id = '$id'") or die (mysql_error());
	while($row = mysql_fetch_assoc($result)){
		$stock_id = $row["stock_id"];
		$po_qty = $row["quantity"];
		
		$r_id = getRr($row["po_header_id"]);
		$result2 = mysql_query ("select * from tbl_rr_details where rr_header_id = '$r_id' and stock_id = '$stock_id'") or die (mysql_error());
		$row2 = mysql_fetch_assoc($result2);
		$rr_qty += $row2["qty"];
		
		if($po_qty < $rr_qty){
			echo 1;
		}else{
			echo 0;
		}
	}
}

function getRr($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT * from tbl_rr_header where po_header_id = '$id' and company_id='$company_id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["rr_header_id"];
}

function getRRnum($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT * from tbl_rr_header where rr_header_id = '$id' and company_id='$company_id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["receiving_number"];
}

function getPurchaseNumber($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT * from tbl_po_header where po_header_id = '$id' and company_id='$company_id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["po_number"];
}
function getDeliveryNumber($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT * from tbl_dr_header where dr_header_id = '$id' and company_id='$company_id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["delivery_number"];
}
function getSalesReturnNumber($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT * from tbl_sales_return where sales_return_id = '$id' and company_id='$company_id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["sr_number"];
}

function getRefStockTransfer($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT ref_id from tbl_stock_transfer_header where stock_transfer_header_id = '$id' and company_id='$company_id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row[0];
}

function getRefSTBldg_pig($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT * from tbl_stock_transfer_bldg where stock_transfer_bldg_id = '$id' and company_id='$company_id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["ref_id"];
}

function getRefSTBldgToBldg_pig($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT * from tbl_stock_transfer_bldg_bldg_pig where st_bldg_id = '$id' and company_id='$company_id' ") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["ref_id"];
}

function getRefSTBldgToBldg_eggs($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT * from tbl_stock_transfer_bldg_bldg_eggs where st_bldg_id = '$id' and company_id='$company_id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["ref_id"];
}

function getRefSTBldgToBldg_broiler($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT * from tbl_stock_transfer_bldg_bldg_broiler where st_bldg_id = '$id' and company_id='$company_id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["ref_id"];
}

function getRefSTBldgToWarehouse_pig($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT * from tbl_stock_transfer_bldg_warehouse_pig where st_bldg_warehouse_id = '$id' and company_id='$company_id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["ref_id"];
}


function getRefSTBldgToWarehouse_eggs($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT * from tbl_stock_transfer_bldg_warehouse_eggs where st_bldg_warehouse_id = '$id' and company_id='$company_id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["ref_id"];
}
function getRefSTBldgToWarehouse_broiler($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT * from tbl_stock_transfer_bldg_warehouse_broiler where st_bldg_warehouse_id = '$id' and company_id='$company_id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["ref_id"];
}

function getSTBldg_egg($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT * from tbl_stock_transfer_bldg_eggs where stock_transfer_bldg_id = '$id' and company_id='$company_id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["ref_id"];
}
function getSTBldg_broiler($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT * from tbl_stock_transfer_bldg_broiler where stock_transfer_bldg_id = '$id' and company_id='$company_id' ") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["ref_id"];
}

function getPOdetails($id){
	$result = mysql_query("SELECT * from tbl_po_details where po_detail_id = '$id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return getProdName($row["stock_id"]);
}

function getPurchaseSupplier($id){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT * from tbl_po_header where po_header_id = '$id' and company_id='$company_id' ") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["supplier_id"];
}


function daysDifference($endDate, $beginDate){

	// if($beginDate == "" || $beginDate == "0000-00-00" || $beginDate == NULL){ $beginDate = ""; }

   $date_parts1 = explode("-", $beginDate);
   $date_parts2 = explode("-", $endDate);
   $start_date = gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
   $end_date = gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
   $diff = abs($end_date - $start_date);
   //$years = floor($diff / 365.25);
   return $diff;
}

function getArea($id){
	$result = mysql_query("select * from tbl_pen_assignment where pen_id = '$id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["building_category_id"];
}
function stockName($company_id,$branch_id,$date_from,$date_to){
	
	
	$fetch = mysql_query("SELECT rd.stock_id AS stock_id FROM tbl_growing_module AS gm, tbl_rr_details AS rd WHERE gm.branch_id='$branch_id' AND gm.company_id='$company_id' AND gm.growing_date BETWEEN '$date_from' AND '$date_to' AND gm.rr_num=rd.rr_header_id");
	while($result = mysql_fetch_array($fetch)){
		
		$stock_id = $result["stock_id"];
	}
	return $stock_id;
}

function getPenTypeId($id){
	$company_id = $_SESSION['system']['company_id'];
	$row = mysql_fetch_assoc(mysql_query("SELECT pen_type from tbl_pen_assignment where pen_assignment_id='$id' and company_id='$company_id' "));
	return $row['pen_type'];
}

function getPTypeName($id){
	$result = mysql_query("SELECT pen_type from tbl_pen_type where pen_type_id = '$id' ") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["pen_type"];
}

//packaging

function getPackagingName($id, $branch = NULL){
	$branch_id = get_branch();
	$company_id = $_SESSION['system']['company_id'];

	if($branch != ""){
		$br = $branch;	
	}else{
		$br = $branch_id;
	}

	$package_name = mysql_fetch_array(mysql_query("SELECT package_name from tbl_package where package_id='$id' and ((company_id='$company_id' and branch_id='$br') or (company_id=0 and branch_id=0)) "));
	return $package_name[0];
}

/// GET INVENTORY COST
function InvCost($stock_id,$company_id,$branch_id){
	$result = mysql_query("select * from tbl_productmaster where product_id = '$stock_id' and company_id='$company_id' and branch_id='$branch_id'") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["cost"];
}

//GET INVENTORY PRODUCTION QUANTITY
function InvEggsQtyp($stock_id,$company_id,$branch_id,$date){
	
	$fetch = mysql_query("SELECT * FROM tbl_inventory_entry_production WHERE company_id='$company_id' AND branch_id='$branch_id' AND item='$stock_id' AND date < '$date'");
	while($result = mysql_fetch_array($fetch)){
		$qty2 += $result["qty"];
	}
	$fetch = mysql_query("SELECT * FROM tbl_inventory_entry_rtl WHERE company_id='$company_id' AND branch_id='$branch_id' AND item='$stock_id' AND date < '$date'");
	while($result = mysql_fetch_array($fetch)){
		$qty3 += $result["qty"];
	}
	
	$totalegg = $qty3 + $qty2;
	
	return $totalegg;
}

///GET QUANTITY COST
function InvQty($stock_id,$company_id,$branch_id,$rr_date){
	
	$result = mysql_query("SELECT *, SUM(qty) AS qty FROM `tbl_rr_header` AS rh,tbl_rr_details AS rd WHERE rd.stock_id='$stock_id' AND rh.rr_header_id = rd.rr_header_id AND rh.company_id='$company_id' AND rh.branch_id='$branch_id' AND rh.status='F'  AND rh.rr_date < '$rr_date' GROUP BY rd.stock_id") or die (mysql_error());
	//$total = array();
	while ($row = mysql_fetch_array($result)){
		$subtotal = $row[qty];
		
			///GROWING 
			
			$fetch = mysql_query("SELECT *, SUM(no_of_chick) AS qty FROM tbl_growing_module WHERE company_id='$company_id' AND branch_id='$branch_id' AND stock_id='$row[stock_id]'");
			while($result2 = mysql_fetch_array($fetch)){
				$gm_qty = $result2[qty];
			}
			$fetch_stock_g_feeds = mysql_query("SELECT *, SUM(qty) as qty FROM tbl_feeds_entry WHERE item='$row[stock_id]'");
			while($stock_row_g_feeds = mysql_fetch_array($fetch_stock_g_feeds)){
				$stock_qty_g_feeds = $stock_row_g_feeds[qty];
			}
			$fetch_stock_g_med = mysql_query("SELECT *, SUM(qty) as qty FROM tbl_medication_entry WHERE item='$row[stock_id]'");
			while($stock_row_g_med = mysql_fetch_array($fetch_stock_g_med)){
				$stock_qty_g_med = $stock_row_g_med[qty];
			}
			$fetch_stock_g_mortality = mysql_query("SELECT *, SUM(qty) as qty FROM tbl_mortality_entry WHERE item='$row[stock_id]'");
			while($stock_row_g_mortality = mysql_fetch_array($fetch_stock_g_mortality)){
				$stock_qty_g_mortality = $stock_row_g_mortality[qty];
			}
			
			$fetch_stock_g_depopulate = mysql_query("SELECT *, SUM(qty) as qty FROM tbl_depopulate_entry WHERE item='$row[stock_id]'");
			while($stock_row_g_depopulate = mysql_fetch_array($fetch_stock_g_depopulate)){
				$stock_qty_g_depopulate = $stock_row_g_depopulate[qty];
			}
			
			////PRODUCTION
			
			$fetch_stock_p_feeds = mysql_query("SELECT *, SUM(qty) as qty FROM tbl_feeds_entry_production WHERE item='$row[stock_id]'");
			while($stock_row_p_feeds = mysql_fetch_array($fetch_stock_p_feeds)){
				$stock_qty_p_feeds = $stock_row_p_feeds[qty];
			}
			$fetch_stock_p_med = mysql_query("SELECT *, SUM(qty) as qty FROM tbl_medication_entry_production WHERE item='$row[stock_id]'");
			while($stock_row_p_med = mysql_fetch_array($fetch_stock_p_med)){
				$stock_qty_p_med = $stock_row_p_med[qty];
			}
			$fetch_production = mysql_query("SELECT *, SUM(qty) AS qty FROM tbl_production WHERE company_id='$company_id' AND branch_id='$branch_id' AND stock_id='$row[stock_id]' AND tag ='FRR'");
			while($result_result = mysql_fetch_array($fetch_production)){
				$production_qty = $result_result[qty];
			}
			
			$fetch_stock_p_mortality = mysql_query("SELECT *, SUM(qty) as qty FROM tbl_mortality_entry_production WHERE item='$row[stock_id]'");
			while($stock_row_p_mortality = mysql_fetch_array($fetch_stock_p_mortality)){
				$stock_qty_p_mortality = $stock_row_p_mortality[qty];
			}
			
			$fetch_stock_p_depopulate = mysql_query("SELECT *, SUM(qty) as qty FROM tbl_depopulate_entry_production WHERE item='$row[stock_id]'");
			while($stock_row_p_depopulate = mysql_fetch_array($fetch_stock_p_depopulate)){
				$stock_qty_p_depopulate = $stock_row_p_depopulate[qty];
			}
			
			////READY TO LAY
		/*	$fetch_rtl = mysql_query("SELECT *, SUM(qty) AS qty FROM tbl_rtl WHERE company_id='$company_id' AND branch_id='$branch_id' AND stock_id='$row[stock_id]'");
			while($result_rtl = mysql_fetch_array($fetch_rtl)){
				$rtl_qty = $result_rtl[qty];
			}
			
			$fetch_stock_r_feeds = mysql_query("SELECT *, SUM(qty) as qty FROM tbl_feeds_entry_rtl WHERE item='$row[stock_id]'");
			while($stock_row_r_feeds = mysql_fetch_array($fetch_stock_r_feeds)){
				$stock_qty_r_feeds = $stock_row_r_feeds[qty];
			}
			$fetch_stock_r_med = mysql_query("SELECT *, SUM(qty) as qty FROM tbl_medication_entry_rtl WHERE item='$row[stock_id]'");
			while($stock_row_r_med = mysql_fetch_array($fetch_stock_r_med)){
				$stock_qty_r_med = $stock_row_r_med[qty];
			}
			$fetch_stock_r_mor = mysql_query("SELECT *, SUM(qty) as qty FROM tbl_mortality_entry_rtl WHERE item='$row[stock_id]'");
			while($stock_row_r_mor = mysql_fetch_array($fetch_stock_r_mor)){
				$stock_qty_r_mor = $stock_row_r_mor[qty];
			}
			$fetch_stock_r_dep = mysql_query("SELECT *, SUM(qty) as qty FROM tbl_depopulate_entry_rtl WHERE item='$row[stock_id]'");
			while($stock_row_r_dep = mysql_fetch_array($fetch_stock_r_dep)){
				$stock_qty_r_dep = $stock_row_r_dep[qty];
			} */
			
			
			 $sbtotal = $subtotal - ($gm_qty + $stock_qty_g_feeds + $stock_qty_g_med + $stock_qty_g_mortality + $stock_qty_g_depopulate + $stock_qty_p_feeds + $stock_qty_p_med + $production_qty + $stock_qty_p_mortality + $stock_qty_p_depopulate);
	}
		//$total[] = $sbtotal;
	
	return $sbtotal;
}


////BALANCE FORWARD
function getBalanceForwarded($company_id,$branch_id,$warehouse_id,$date_from,$product_id,$package_id,$include_finished,$include_saved,$include_paid){
	// ---------------------------------------------------------- I N ----------------------------------------------------------
	//(IN)1
	$receiving_qty = mysql_fetch_array(mysql_query("SELECT SUM(rrd.quantity) FROM `tbl_rr_details` AS rrd, `tbl_rr_header` AS rrh WHERE rrh.receiving_number=rrd.receiving_number AND rrd.product_id='$product_id' AND rrd.warehouse_id='$warehouse_id' AND DATE_FORMAT(rrh.date_added, '%Y-%m-%d') <= '$date_from' AND rrd.company_id='$company_id' AND rrd.branch_id='$branch_id' AND rrd.packaging_id='$package_id' AND (rrd.status='$include_finished' OR rrd.status='$include_saved')"));

	//(IN)2
	 $job_order_qty = mysql_fetch_array(mysql_query("SELECT SUM(package_type_qty) FROM `tbl_joborder_header_feeds` WHERE fin_product='$product_id' AND location='$warehouse_id' AND DATE_FORMAT(datefinished, '%Y-%m-%d') <= '$date_from' AND company_id='$company_id' AND branch_id='$branch_id' AND package_type='$package_id' AND (status='$include_finished' OR status='$include_saved')"));

	 //(IN)3
	$ai_semen_qty = mysql_fetch_array(mysql_query("SELECT SUM(package_type_qty) FROM `tbl_ai_semen_production_header_pig` WHERE fin_product='$product_id' AND location='$warehouse_id' AND DATE_FORMAT(datefinished, '%Y-%m-%d') <= '$date_from' AND company_id='$company_id' AND branch_id='$branch_id' AND package_type='$package_id' AND (status='$include_finished' OR status='$include_saved')"));

	 //(IN)4
	$product_conversion_ctq = mysql_fetch_array(mysql_query("SELECT SUM(convert_to_qty) FROM `tbl_product_conversion` WHERE convert_to_id='$product_id' AND warehouse_id='$warehouse_id' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$date_from' AND company_id='$company_id' AND branch_id='$branch_id' AND packaging_id_converted_to='$package_id' AND (status='$include_finished' OR status='$include_saved') AND flock_convert_status='0'"));

	//(IN)4.1
	$product_conversion_ctq_flock = mysql_fetch_array(mysql_query("SELECT SUM(convert_to_qty) FROM `tbl_product_conversion` WHERE convert_to_id='$product_id' AND warehouse_id='$warehouse_id' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$date_from' AND company_id='$company_id' AND branch_id='$branch_id' AND packaging_id_converted_to='$package_id' AND (status='$include_finished' OR status='$include_saved') AND flock_convert_status='1'"));

	//(IN)5
	$sales_return_qty = mysql_fetch_array(mysql_query("SELECT SUM(quantity) from tbl_sales_return_details AS sd, tbl_sales_return AS sh where sd.sr_number = sh.sr_number AND sd.product_id = '$product_id' AND DATE_FORMAT(sh.date_added, '%Y-%m-%d') <= '$date_from' AND sh.company_id = '$company_id' AND sh.branch_id = '$branch_id' AND sh.warehouse_id='$warehouse_id' AND sd.packaging_id='$package_id' AND (sd.status='$include_finished' OR sd.status='$include_saved')"));

	//(IN)6
	$egg_qty = mysql_fetch_array(mysql_query("SELECT SUM(qty) FROM tbl_inventory_entry_production_eggs WHERE item='$product_id' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$date_from'  AND warehouse_id='$warehouse_id' AND company_id='$company_id' AND branch_id='$branch_id'"));

	//(IN)7
	$stock_transfer_ww_in = mysql_fetch_array(mysql_query("SELECT SUM(qty) FROM tbl_stock_transfer_details WHERE product_id='$product_id' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$date_from' AND company_id='$company_id' AND destination_branch='$branch_id'  AND destination_location='$warehouse_id' AND packaging_id='$package_id'  AND(status='R' OR status='$include_saved')"));

	//(IN)8
	$stock_transfer_bw_pig_in = mysql_fetch_array(mysql_query("SELECT SUM(qty) FROM `tbl_std_bldg_warehouse_pig` AS std, `tbl_stock_transfer_bldg_warehouse_pig` AS sth WHERE std.product_id='$product_id' AND DATE_FORMAT(std.date, '%Y-%m-%d') <= '$date_from' AND sth.company_id='$company_id' AND sth.branch_id='$branch_id' AND std.packaging_id='$package_id' AND(std.status='$include_finished' OR std.status='$include_saved') AND sth.warehouse_id='$warehouse_id' AND sth.ref_id=std.ref_id"));

	//(IN)9
	$beginning_balance_qty = mysql_fetch_array(mysql_query("SELECT SUM(qty) FROM `tbl_beginning_balance` WHERE product_id='$product_id'  AND DATE_FORMAT(date, '%Y-%m-%d') <= '$date_from' AND company_id='$company_id' AND warehouse_id='$warehouse_id' AND  branch_id='$branch_id' AND package_id='$package_id' AND (status='$include_finished' OR status='$include_saved')"));

	//(IN)10
	$inventory_adjusment_add = mysql_fetch_array(mysql_query("SELECT sum(iad.qty) from tbl_inventory_adjustment_details as iad, tbl_inventory_adjustment_header as iah where iah.company_id='$company_id' and iah.branch_id='$branch_id' and iah.warehouse_id='$warehouse_id' and iad.qty > 0 and DATE_FORMAT(iah.date, '%Y-%m-%d') <= '$date_from' and iah.inv_adj_num=iad.inv_adj_num and iad.product_id='$product_id' and iad.package_id ='$package_id' and (iah.status='$include_finished' OR iah.status='$include_saved')"));
	

	//$inventory_adjusment_add = mysql_fetch_array(mysql_query("SELECT SUM(qty) FROM `tbl_inventory_adjustment_details` WHERE product_id='$product_id'  AND DATE_FORMAT(date, '%Y-%m-%d') <= '$date_from' AND company_id='$company_id' AND warehouse_id='$warehouse_id' AND  branch_id='$branch_id' AND package_id='$package_id' AND (status='$include_finished' OR status='$include_saved') AND qty > 0"));

	//(IN)11
	$candling_egg_qty = mysql_fetch_array(mysql_query("SELECT SUM(quantity) FROM tbl_incubator_details_candling_eggs WHERE product_id='$product_id' AND DATE_FORMAT(date_added, '%Y-%m-%d') <= '$date_from' AND warehouse_id='$warehouse_id' AND company_id='$company_id' AND branch_id='$branch_id'"));

	//(IN)12
	$broiler_qty = mysql_fetch_array(mysql_query("SELECT SUM(qty) FROM tbl_inventory_entry_production_broiler WHERE item='$product_id' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$date_from'  AND warehouse_id='$warehouse_id' AND company_id='$company_id' AND branch_id='$branch_id'"));
	//(IN)13
	$st_b_to_w_qty_eggs = mysql_fetch_array(mysql_query("SELECT SUM(stDE.qty) from tbl_stock_transfer_bldg_warehouse_eggs as stHE, tbl_std_bldg_warehouse_eggs as stDE where stHE.ref_id = stDE.ref_id and stHE.company_id='$company_id' and stHE.branch_id= '$branch_id' and stHE.warehouse_id='$warehouse_id' and stDE.product_id = '$product_id' and stDE.packaging_id='$package_id' and DATE_FORMAT(stDE.date, '%Y-%m-%d') <= '$date_from' and (stDE.status = '$include_finished' OR stDE.status = '$include_saved')"));

	//(IN)14
	$carcass_weight_qty = mysql_fetch_array(mysql_query("SELECT sum(c_weight) from tbl_carcass_details_pig as cd, tbl_carcass_header_pig as ch where ch.company_id='$company_id' and ch.branch_id='$branch_id' and ch.warehouse_id='$warehouse_id' and ch.reference_number=cd.reference_number and cd.product_id='$product_id' and cd.packaging_id='$package_id' and DATE_FORMAT(ch.date_added, '%Y-%m-%d') <= '$date_from' and (ch.status='$include_finished' OR ch.status = '$include_saved')"));

	//(IN)15
	$st_b_to_w_qty_broiler = mysql_fetch_array(mysql_query("SELECT SUM(stDB.qty) from tbl_stock_transfer_bldg_warehouse_broiler as stHB, tbl_std_bldg_warehouse_broiler as stDB where stHB.ref_id = stDB.ref_id and stHB.company_id='$company_id' and stHB.branch_id= '$branch_id' and stHB.warehouse_id='$warehouse_id' and stDB.product_id = '$product_id' and stDB.packaging_id='$package_id' and DATE_FORMAT(stDB.date, '%Y-%m-%d') <= '$date_from' and (stDB.status = '$include_finished' OR stDB.status = '$include_saved') and (stHB.status='$include_finished' OR stHB.status='$include_saved')"));

	//(IN)16
	$stock_transfer_return = mysql_fetch_array(mysql_query("SELECT SUM(std.return_qty) FROM tbl_stock_transfer_bldg as st, tbl_st_bldg_to_receive as std WHERE st.warehouse_id = '$warehouse_id' AND st.ref_id = std.ref_id AND st.company_id = std.company_id AND st.branch_id = std.branch_id AND std.product_id = '$product_id' AND std.packaging_id='$package_id' AND DATE_FORMAT(st.date, '%Y-%m-%d') <= '$date_from' AND (st.status = '$include_finished' OR st.status = '$include_saved' OR st.status = 'R') AND (std.status = '$include_finished' OR std.status = '$include_saved' OR std.status = 'R')"));

	//(IN)17

	$stock_transfer_return_egg = mysql_fetch_array(mysql_query("SELECT SUM(std.return_qty) FROM tbl_stock_transfer_bldg_eggs as st, tbl_st_bldg_to_receive_eggs as std WHERE st.warehouse_id = '$warehouse_id' AND st.ref_id = std.ref_id AND st.company_id = std.company_id AND st.branch_id = std.branch_id AND std.product_id = '$product_id' AND std.packaging_id='$package_id' AND DATE_FORMAT(st.date,'%Y-%m-%d') <= '$date_from' and (st.status = '$include_finished' OR st.status = '$include_saved' OR st.status = 'R') AND (std.status = '$include_finished' OR std.status = '$include_saved' OR std.status = 'R') "));

	// ---------------------------------------------------------- I N ----------------------------------------------------------

	// ---------------------------------------------------------- O U T --------------------------------------------------------

	//(OUT)1
	$product_conversion_oiq = mysql_fetch_array(mysql_query("SELECT SUM(original_item_qty) FROM `tbl_product_conversion` WHERE original_item_id='$product_id' AND warehouse_id='$warehouse_id' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$date_from' AND company_id='$company_id' AND branch_id='$branch_id' AND packaging_id_original_item='$package_id' AND (status='$include_finished' OR status='$include_saved') AND flock_convert_status='0'"));

	//(OUT)1.1
	$product_conversion_oiq_flock = mysql_fetch_array(mysql_query("SELECT SUM(original_item_qty) FROM `tbl_product_conversion` WHERE original_item_id='$product_id' AND warehouse_id='$warehouse_id' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$date_from' AND company_id='$company_id' AND branch_id='$branch_id' AND packaging_id_original_item='$package_id' AND (status='$include_finished' OR status='$include_saved') AND flock_convert_status='2'"));

	//(OUT)2
	$purchase_return_qty = mysql_fetch_array(mysql_query("SELECT SUM(qty) FROM `tbl_purchase_return_header` as prh,`tbl_purchase_return_details` as prd WHERE prd.product_id='$product_id' AND prd.warehouse_id='$warehouse_id' AND DATE_FORMAT(prd.date, '%Y-%m-%d') <= '$date_from' AND prd.company_id='$company_id' AND prd.branch_id='$branch_id' AND prd.packaging_id='$package_id' AND (prd.status='$include_finished' OR prd.status='$include_saved') AND prh.pr_num=prd.pr_num"));

	//(OUT)3
	$sales_qty = mysql_fetch_array(mysql_query("SELECT SUM(quantity) from tbl_dr_detail AS dd, tbl_dr_header AS dh where dd.delivery_number = dh.delivery_number AND dd.stock_id = '$product_id' AND DATE_FORMAT(dh.dr_date, '%Y-%m-%d') <= '$date_from' AND dh.company_id = '$company_id' AND dh.branch_id = '$branch_id' AND dh.warehouse_id='$warehouse_id' AND dd.packaging_id='$package_id' AND (dh.status = '$include_paid' OR dh.status = '$include_finished' OR dh.status = '$include_saved')"));

	//(OUT)4
	// $growing_qty = mysql_fetch_array(mysql_query("SELECT SUM(previous_qty) FROM tbl_growing_module_eggs WHERE growing_date < '$date_from' AND stock_id='$product_id' AND warehouse_id='$warehouse_id' AND branch_id='$branch_id' AND company_id='$company_id' AND tag='FRR'"));
	
	//(OUT)5
	// $production_qty = mysql_fetch_array(mysql_query("SELECT SUM(previous_qty) FROM tbl_production_eggs WHERE start_date < '$date_from' AND stock_id='$product_id' AND warehouse_id='$warehouse_id' AND branch_id='$branch_id' AND company_id='$company_id' AND tag='FRR'"));

	//(OUT)6
	$get_product_code_sts = mysql_fetch_array(mysql_query("SELECT product_code FROM `tbl_productmaster` where company_id='$company_id' and product_id='$product_id'"));

	$stock_transfer_ww_out_old = mysql_fetch_array(mysql_query("SELECT SUM(qty) FROM tbl_stock_transfer_details WHERE product_code='$get_product_code_sts[0]' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$date_from' AND company_id='$company_id' AND source_branch='$branch_id' AND transfer_type != '1' AND source_location='$warehouse_id' AND source_packaging_id='$package_id'  AND (status='$include_finished' OR status='R' OR status='$include_saved')"));
	
	$stock_transfer_ww_out = mysql_fetch_array(mysql_query("SELECT SUM(qty-return_qty) FROM tbl_stock_transfer_to_receive WHERE product_code='$get_product_code_sts[0]' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$date_from' AND company_id='$company_id' AND source_branch='$branch_id' AND source_location='$warehouse_id' AND source_packaging_id='$package_id'  AND (status='F' OR status='R' OR status='$include_saved')"));

	//(OUT)7.0
	$stock_transfer_wb_egg_out = mysql_fetch_array(mysql_query("SELECT SUM(qty) FROM `tbl_st_bldg_details_eggs` AS std, `tbl_stock_transfer_bldg_eggs` AS sth WHERE std.product_id='$product_id' AND DATE_FORMAT(std.date, '%Y-%m-%d') <= '$date_from' AND std.company_id='$company_id' AND std.branch_id='$branch_id' AND std.transfer_type != '1' AND std.packaging_id='$package_id' AND(std.status='R' OR std.status='$include_finished' OR std.status='$include_saved') AND sth.warehouse_id='$warehouse_id' AND sth.ref_id=std.ref_id"));
	
	//(OUT)7.1
	$stock_transfer_wb_egg_wr_out = mysql_fetch_array(mysql_query("SELECT SUM(qty-return_qty) FROM `tbl_st_bldg_to_receive_eggs` AS std, `tbl_stock_transfer_bldg_eggs` AS sth WHERE std.product_id='$product_id' AND DATE_FORMAT(std.date, '%Y-%m-%d') <= '$date_from' AND std.company_id='$company_id' AND std.branch_id='$branch_id' AND std.packaging_id='$package_id' AND(std.status='R' OR std.status='$include_finished' OR std.status='$include_saved') AND sth.warehouse_id='$warehouse_id' AND sth.ref_id=std.ref_id"));

	//(OUT)8.0
	$stock_transfer_wb_pig_out = mysql_fetch_array(mysql_query("SELECT SUM(qty) FROM `tbl_st_bldg_details` AS std, `tbl_stock_transfer_bldg` AS sth WHERE std.product_id='$product_id' AND DATE_FORMAT(std.date, '%Y-%m-%d') <= '$date_from' AND std.company_id='$company_id' AND std.branch_id='$branch_id' AND std.transfer_type != '1' AND std.packaging_id='$package_id' AND(std.status='R' OR std.status='$include_finished' OR std.status='$include_saved') AND sth.warehouse_id='$warehouse_id' AND sth.ref_id=std.ref_id"));
	
	//(OUT)8.1
	$stock_transfer_wb_pig_wr_out = mysql_fetch_array(mysql_query("SELECT SUM(qty-return_qty) FROM `tbl_st_bldg_to_receive` AS std, `tbl_stock_transfer_bldg` AS sth WHERE std.product_id='$product_id' AND DATE_FORMAT(std.date, '%Y-%m-%d') <= '$date_from' AND std.company_id='$company_id' AND std.branch_id='$branch_id' AND std.packaging_id='$package_id' AND(std.status='R' OR std.status='$include_finished' OR std.status='$include_saved') AND sth.warehouse_id='$warehouse_id' AND sth.ref_id=std.ref_id"));

	//(OUT)9
	$consumables_qty = mysql_fetch_array(mysql_query("SELECT SUM(qty) FROM tbl_consumables_details WHERE DATE_FORMAT(c_date, '%Y-%m-%d') <= '$date_from' AND product_id='$product_id' AND branch_id='$branch_id' AND company_id='$company_id' AND package_id = '$package_id' AND (status='$include_finished' OR status='$include_saved')"));


	//(OUT)10
	$product_conversion_addon_material = mysql_fetch_array(mysql_query("SELECT SUM(convert_to_qty) FROM `tbl_product_conversion` WHERE material_id='$product_id' AND warehouse_id='$warehouse_id' AND DATE_FORMAT(date, '%Y-%m-%d') <= '$date_from' AND company_id='$company_id' AND branch_id='$branch_id' AND material_packaging_id='$package_id' AND (status='$include_finished' OR status='$include_saved')"));
	
	//OUT(11)
	$sc_fc_get_product_category = getProdCat($product_id);

	$sc_bf_package_id = mysql_fetch_array(mysql_query("SELECT pkg.qty AS lowest_unit, pkg.package_id AS package_id
	FROM tbl_package AS pkg, tbl_product_category AS pc
	WHERE pkg.qty = (SELECT MIN(pkg.qty)
	FROM tbl_package AS pkg, tbl_product_category AS pc
	WHERE pkg.category_id = pc.product_categ_id AND ((pkg.company_id = '$company_id' AND pkg.branch_id = '$branch_id') OR (pkg.company_id = 0 
	AND pkg.branch_id = 0)) AND pkg.visibility_status = 1 AND pc.product_categ_id = '$sc_fc_get_product_category') AND pkg.category_id = pc.product_categ_id AND ((pkg.company_id = '$company_id' AND pkg.branch_id = '$branch_id') OR (pkg.company_id = 0 
	AND pkg.branch_id = 0)) AND pkg.visibility_status = 1 AND pc.product_categ_id = '$sc_fc_get_product_category'"));
	$sc_bf_lowest_unit = $sc_bf_package_id[package_id];

	if($package_id == $sc_bf_lowest_unit){
		$job_order_rm_out = mysql_fetch_array(mysql_query("SELECT SUM(joD.quantity * joH.num_of_batches) FROM tbl_joborder_header_feeds AS joH, tbl_joborder_details_feeds AS joD WHERE joH.joborder_header_id = joD.joborder_header_id AND joH.company_id = '$company_id' AND joH.branch_id = '$branch_id' AND DATE_FORMAT(joH.datefinished, '%Y-%m-%d') <= '$date_from' and joH.warehouse_id='$warehouse_id' AND joD.material = '$product_id' AND (joH.status='$include_finished' OR joH.status='$include_saved')"));
		$job_order_qty_rm_out = $job_order_rm_out[0];
	}

	//OUT(12)
	if($package_id == 17){
		$pkm_1 = 0;
		$pkm_2 = 0;
		$pkm_3 = 0;

		//-- job order deductions packaging materials --//
		$PKM1 = getBulkData("SUM(package_type_qty) AS total","tbl_joborder_header_feeds","(status='$include_finished' OR status='$include_saved') AND company_id = '$company_id' AND branch_id = '$branch_id' AND DATE_FORMAT(datefinished, '%Y-%m-%d') <= '$date_from' AND pk_1 = '$product_id' AND warehouse_id='$warehouse_id'");

		$PKM2 = getBulkData("SUM(package_type_qty) AS total2","tbl_joborder_header_feeds","(status='$include_finished' OR status='$include_saved') AND company_id = '$company_id' AND branch_id = '$branch_id' AND DATE_FORMAT(datefinished, '%Y-%m-%d') <= '$date_from' AND pk_2 = '$product_id' AND warehouse_id='$warehouse_id'");

		$PKM3 = getBulkData("SUM(package_type_qty) AS total3","tbl_joborder_header_feeds","(status='$include_finished' OR status='$include_saved') AND company_id = '$company_id' AND branch_id = '$branch_id' AND DATE_FORMAT(datefinished, '%Y-%m-%d') <= '$date_from' AND pk_3 = '$product_id' AND warehouse_id='$warehouse_id'");
		
		$pkm_1 = $PKM1[total];
		$pkm_2 = $PKM2[total2];
		$pkm_3 = $PKM3[total3];

		$job_order_qty_sack_out = $pkm_1 + $pkm_2 + $pkm_3;
	}

	//OUT(13)
	$construction_in_progress_qty = mysql_fetch_array(mysql_query("SELECT SUM(product_qty) FROM tbl_construction_in_progress_detail as d , tbl_construction_in_progress as h WHERE h.cip_id = d.cip_id AND h.company_id = '$company_id' AND h.branch_id = '$branch_id' AND d.product_id = '$product_id' AND d.packaging_id = '$package_id' AND d.source_warehouse = '$warehouse_id' AND DATE_FORMAT(d.transfer_date, '%Y-%m-%d') <= '$date_from'AND (d.transfer_status='$include_finished' OR d.transfer_status='$include_saved')"));

	//(OUT)14
	$inventory_adjusment_minus = mysql_fetch_array(mysql_query("SELECT sum(iad.qty) from tbl_inventory_adjustment_details as iad, tbl_inventory_adjustment_header as iah where iah.company_id='$company_id' and iah.branch_id='$branch_id' and iah.warehouse_id='$warehouse_id' and iad.qty < 0 and DATE_FORMAT(iah.date, '%Y-%m-%d') <= '$date_from' and iah.inv_adj_num=iad.inv_adj_num and iad.product_id='$product_id' and iad.package_id ='$package_id' and (iah.status='$include_finished' OR iah.status='$include_saved')"));
	
	//(OUT)15
	$spoilage_qty_egg = mysql_fetch_array(mysql_query("SELECT sum(quantity) from tbl_spoiled_eggs where product_id='$product_id' and company_id='$company_id' and branch_id='$branch_id' and warehouse_id='$warehouse_id' and DATE_FORMAT(date, '%Y-%m-%d') <= '$date_from' and packaging_id='$packaging_id' "));

	//(OUT)16
	$spoilage_qty_broiler = mysql_fetch_array(mysql_query("SELECT sum(quantity) from tbl_spoiled_broiler where product_id='$product_id' and company_id='$company_id' and branch_id='$branch_id' and warehouse_id='$warehouse_id' and DATE_FORMAT(date, '%Y-%m-%d') <= '$date_from' and packaging_id='$packaging_id' "));

	//(OUT)17
	$fetch_stock_bldg_broiler = mysql_fetch_array(mysql_query("SELECT SUM(stdb.qty) from tbl_stock_transfer_bldg_broiler as stb, tbl_st_bldg_details_broiler as stdb where stb.ref_id = stdb.ref_id and stb.company_id = stdb.company_id and stb.branch_id = stdb.branch_id and (stb.status = 'F' or stb.status = 'R') and (stdb.status = 'F' or stdb.status = 'R') and stdb.product_id = '$product_id' and stb.warehouse_id='$warehouse_id' and DATE_FORMAT(stb.date, '%Y-%m-%d') <= '$date_from' and stdb.packaging_id='$package_id' "));

	//(OUT)18
	//$fetch_stock_bldg_broiler = mysql_fetch_array(mysql_query("SELECT SUM(stdb.qty) from tbl_stock_transfer_bldg_broiler as stb, tbl_st_bldg_details_broiler as stdb where stb.ref_id = stdb.ref_id and stb.company_id = stdb.company_id and stb.branch_id = stdb.branch_id  and stdb.product_id = '$product_id' and stb.warehouse_id='$warehouse_id' and DATE_FORMAT(stb.date, '%Y-%m-%d') <= '$date_from' and stdb.packaging_id='$package_id' and (stb.status = '$include_finished' or  stb.status = 'R') and (stdb.status = '$include_finished' or stdb.status = 'R') "));

	// ---------------------------------------------------------- O U T --------------------------------------------------------

	//IN
	$quantity_in = $receiving_qty[0] + $job_order_qty[0] + $ai_semen_qty[0] + $product_conversion_ctq[0] + $sales_return_qty[0] + $egg_qty[0] + $stock_transfer_ww_in[0] + $stock_transfer_bw_pig_in[0] + $beginning_balance_qty[0] + $inventory_adjusment_add[0] + $product_conversion_ctq_flock[0] + $candling_egg_qty[0] + $broiler_qty[0] + $st_b_to_w_qty_eggs[0] + $carcass_weight_qty[0] + $st_b_to_w_qty_broiler[0] + $stock_transfer_return[0] + $stock_transfer_return_egg[0];
	
	//OUT
	$quantity_out =  $job_order_qty_rm_out + $job_order_qty_sack_out + $product_conversion_oiq[0] + $purchase_return_qty[0] + $sales_qty[0] + $stock_transfer_ww_out_old[0] + $stock_transfer_ww_out[0] + $stock_transfer_wb_egg_out[0] + $stock_transfer_wb_egg_wr_out[0] + $stock_transfer_wb_pig_out[0] + $stock_transfer_wb_pig_wr_out[0] + $consumables_qty[0] + abs($inventory_adjusment_minus[0]) + $product_conversion_addon_material[0] + $construction_in_progress_qty[0] + $product_conversion_oiq_flock[0] + $spoilage_qty_egg[0] + $spoilage_qty_broiler[0] + $fetch_stock_bldg_broiler[0];

	$final_quantity = $quantity_in - $quantity_out;

	return $final_quantity;
}


function getBalanceForwardedCost($company_id,$branch_id,$date_from,$stock_name,$package_id,$warehouse_id,$include_finished,$include_saved,$include_paid){

	//FROM RECEIVING (IN)/
	$fetch_rr = mysql_query("SELECT * FROM `tbl_rr_details` AS rrd,`tbl_rr_header` AS rrh WHERE rrh.receiving_number=rrd.receiving_number AND rrd.product_id='$stock_name' AND rrd.warehouse_id='$warehouse_id' AND rrh.date_added <= '$date_from' AND rrd.company_id='$company_id' AND rrd.branch_id='$branch_id' AND rrd.packaging_id='$package_id' AND (rrd.status='$include_finished' OR rrd.status='$include_saved')");
	while($rr_row = mysql_fetch_array($fetch_rr)){
		
		$data[] = array(
			"in" => $rr_row["quantity"],
			"out" =>"",
			"unit_price" => $rr_row["supplier_price"],
		);
	}
	
	//FROM JO (IN)/
	$fetch_jo = mysql_query("SELECT * FROM `tbl_joborder_header_feeds` WHERE fin_product='$stock_name' AND location='$warehouse_id' AND datefinished <= '$date_from' AND company_id='$company_id' AND branch_id='$branch_id' AND package_type='$package_id' AND (status='$include_finished' OR status='$include_saved')");
	while($rr_row = mysql_fetch_array($fetch_jo)){
		
		$data[] = array(
			"in" => $rr_row["package_type_qty"],
			"out" =>"",
			"unit_price" => $rr_row["jo_cost"],
		);
	}
	
	//FROM JO (IN)/
	$fetch_ai = mysql_query("SELECT * FROM `tbl_ai_semen_production_header_pig` WHERE fin_product='$stock_name' AND location='$warehouse_id' AND datefinished <= '$date_from' AND company_id='$company_id' AND branch_id='$branch_id' AND package_type='$package_id' AND (status='$include_finished' OR status='$include_saved')");
	while($rr_row = mysql_fetch_array($fetch_ai)){
		
		$data[] = array(
			"in" => $rr_row["package_type_qty"],
			"out" =>"",
			"unit_price" => $rr_row["jo_cost"],
		);
	}
	
	//FROM PC CONVERTED TO (IN)/
	$fetch_pcct = mysql_query("SELECT * FROM `tbl_product_conversion` WHERE convert_to_id='$stock_name' AND warehouse_id='$warehouse_id' AND date <= '$date_from' AND company_id='$company_id' AND branch_id='$branch_id' AND packaging_id_converted_to='$package_id' AND (status='$include_finished' OR status='$include_saved') AND flock_convert_status='0'");
	while($rr_row = mysql_fetch_array($fetch_pcct)){
		
		$data[] = array(
			"in" => $rr_row["convert_to_qty"],
			"out" =>"",
			"unit_price" => $rr_row["convert_to_cost"],
		);
	}

	//FROM PC CONVERTED TO (IN)/ flock
	$fetch_pcct = mysql_query("SELECT * FROM `tbl_product_conversion` WHERE convert_to_id='$stock_name' AND warehouse_id='$warehouse_id' AND date <= '$date_from' AND company_id='$company_id' AND branch_id='$branch_id' AND packaging_id_converted_to='$package_id' AND (status='$include_finished' OR status='$include_saved') AND flock_convert_status='1'");
	while($rr_row = mysql_fetch_array($fetch_pcct)){
		
		$data[] = array(
			"in" => $rr_row["convert_to_qty"],
			"out" =>"",
			"unit_price" => $rr_row["convert_to_cost"],
		);
	}
	
	//FROM PC ORIGINAL ITEM (OUT)/
	$fetch_pcoi = mysql_query("SELECT * FROM `tbl_product_conversion` WHERE original_item_id='$stock_name' AND warehouse_id='$warehouse_id' AND date <= '$date_from' AND company_id='$company_id' AND branch_id='$branch_id' AND packaging_id_original_item='$package_id' AND (status='$include_finished' OR status='$include_saved') AND flock_convert_status='0'");
	while($rr_row = mysql_fetch_array($fetch_pcoi)){
		
		$data[] = array(
			"in" =>"",
			"out" => $rr_row["original_item_qty"],
			"unit_price" => $rr_row["original_item_cost"],
		);
	}

	//FROM PC ORIGINAL ITEM (OUT)/ flock
	$fetch_pcoi = mysql_query("SELECT * FROM `tbl_product_conversion` WHERE original_item_id='$stock_name' AND warehouse_id='$warehouse_id' AND date <= '$date_from' AND company_id='$company_id' AND branch_id='$branch_id' AND packaging_id_original_item='$package_id' AND (status='$include_finished' OR status='$include_saved') AND flock_convert_status='2'");
	while($rr_row = mysql_fetch_array($fetch_pcoi)){
		
		$data[] = array(
			"in" =>"",
			"out" => $rr_row["original_item_qty"],
			"unit_price" => $rr_row["original_item_cost"],
		);
	}
	
	
	//FROM PURCHASE RETURN (OUT)/
	$fetch_pr = mysql_query("SELECT * FROM `tbl_purchase_return_header` as prh,`tbl_purchase_return_details` as prd WHERE prd.product_id='$stock_name' AND prd.warehouse_id='$warehouse_id' AND prd.date <= '$date_from' AND prd.company_id='$company_id' AND prd.branch_id='$branch_id' AND prd.packaging_id='$package_id' AND (prd.status='$include_finished' OR prd.status='$include_saved') AND prh.pr_num=prd.pr_num");
	while($rr_row = mysql_fetch_array($fetch_pr)){
		
		$data[] = array(
			"in" =>"",
			"out" => $rr_row["qty"],
			"unit_price" => $rr_row["cost"],
		);
	}
	
	//SALES (OUT)
	$fetch_sales = mysql_query("SELECT * from tbl_dr_detail AS dd, tbl_dr_header AS dh where dd.delivery_number = dh.delivery_number AND dd.stock_id = '$stock_name' AND dh.dr_date <= '$date_from' AND dh.company_id = '$company_id' AND dh.branch_id = '$branch_id' AND dh.warehouse_id='$warehouse_id' AND dd.packaging_id='$package_id' AND (dh.status = '$include_paid' OR dh.status = '$include_finished' OR dh.status = '$include_saved')");
	while($rr_row = mysql_fetch_array($fetch_sales)){
		$data[] = array(
			"in" => "",
			"out" =>$rr_row["quantity"],
			"unit_price" => $rr_row["cost"]
		);
	}
	
	//SALES RETURN(IN)
	$fetch_sr = mysql_query("SELECT * from tbl_sales_return_details AS sd, tbl_sales_return AS sh where sd.sr_number = sh.sr_number AND sd.product_id = '$stock_name' AND sh.sr_date <= '$date_from' AND sh.company_id = '$company_id' AND sh.branch_id = '$branch_id' AND sh.warehouse_id='$warehouse_id' AND sd.packaging_id='$package_id' AND (sd.status='$include_finished' OR sd.status='$include_saved')");
	while($rr_row = mysql_fetch_array($fetch_sr)){
		$data[] = array(
			"in" => $rr_row["quantity"],
			"out" =>"",
			"unit_price" => $rr_row["cost"]
		);
	}
	
	
	//GROWING MODULE - farm operations (OUT)
	$fetch_gm = mysql_query("SELECT * FROM tbl_growing_module_eggs WHERE growing_date <= '$date_from' AND stock_id='$stock_name' AND warehouse_id='$warehouse_id' AND branch_id='$branch_id' AND company_id='$company_id' AND tag='FRR'");
		while($rr_row = mysql_fetch_array($fetch_gm)){
			$data[] = array(
					"in" => "",
					"out" =>$rr_row["previous_qty"],
					"unit_price" => $rr_row["previous_cost"]
			);
		}

	$fetch_gmb = mysql_query("SELECT * FROM tbl_growing_module_broiler WHERE growing_date <= '$date_from' AND stock_id='$stock_name' AND warehouse_id='$warehouse_id' AND branch_id='$branch_id' AND company_id='$company_id' AND tag='FRR'");
		while($rr_row = mysql_fetch_array($fetch_gmb)){
			$data[] = array(
					"in" => "",
					"out" =>$rr_row["previous_qty"],
					"unit_price" => $rr_row["previous_cost"]
			);
		}	
		
	///PRODUCTION - farm operations (OUT)
	$fetch_production = mysql_query("SELECT * FROM tbl_production_eggs WHERE start_date <= '$date_from' AND stock_id='$stock_name' AND warehouse_id='$warehouse_id' AND branch_id='$branch_id' AND company_id='$company_id' AND tag='FRR'");
		while($rr_row = mysql_fetch_array($fetch_production)){
			$data[] = array(
					"in" => "",
					"out" =>$rr_row["previous_qty"],
					"unit_price" => $rr_row["previous_cost"]
			);
		}
		
	//EGG INV PRODUCTION - farm operations (IN)
	$fetch_eggs = mysql_query("SELECT * FROM tbl_inventory_entry_production_eggs WHERE item='$stock_name' AND date <= '$date_from'  AND warehouse_id='$warehouse_id' AND company_id='$company_id' AND branch_id='$branch_id'");
	while($rr_row = mysql_fetch_array($fetch_eggs)){
		
		$data[] = array(
			"in" => $rr_row["qty"],
			"out" =>"",
			"unit_price" => $rr_row["cost"]
		);
	}

	//EGG CANDLING - INCUBATION (IN)
	$fetch_eggs = mysql_query("SELECT * FROM tbl_incubator_details_candling_eggs WHERE product_id='$stock_name' AND date_added <= '$date_from'  AND warehouse_id='$warehouse_id' AND company_id='$company_id' AND branch_id='$branch_id'");
	while($rr_row = mysql_fetch_array($fetch_eggs)){
		
		$data[] = array(
			"in" => $rr_row["quantity"],
			"out" =>"",
			"unit_price" => $rr_row["unit_price"]
		);
	}
	
	//STOCK TRANSFER DESTINATION (IN)
	$fetch_std = mysql_query("SELECT * FROM tbl_stock_transfer_details WHERE product_id='$stock_name' AND date <= '$date_from' AND company_id='$company_id' AND destination_branch='$branch_id'  AND destination_location='$warehouse_id' AND packaging_id='$package_id'  AND(status='R' OR status='$include_saved')");
	while($rr_row = mysql_fetch_array($fetch_std)){
		$data[] = array(
			"in" => $rr_row["qty"],
			"out" =>"",
			"unit_price" => $rr_row["cost"]
		);
	}
	
	//STOCK TRANSFER SOURCE (OUT)
	$get_product_code_sts = mysql_fetch_array(mysql_query("SELECT product_code FROM `tbl_productmaster` where company_id='$company_id' and product_id='$stock_name'"));

	$fetch_sts = mysql_query("SELECT * FROM tbl_stock_transfer_details WHERE product_code='$get_product_code_sts[0]' AND date <= '$date_from' AND company_id='$company_id' AND source_branch='$branch_id'  AND source_location='$warehouse_id' AND source_packaging_id='$package_id'  AND(status='F' OR status='R' OR status='$include_saved')");
	while($rr_row = mysql_fetch_array($fetch_sts)){
		$data[] = array(
			"in" => "",
			"out" =>$rr_row["qty"],
			"unit_price" => $rr_row["cost"]
		);
	}
	
	//STOCK TRANSFER DESTINATION WAREHOUSE TO BLDG EGGS(OUT)
	$fetch_stdb = mysql_query("SELECT * FROM `tbl_st_bldg_details_eggs` AS std, `tbl_stock_transfer_bldg_eggs` AS sth WHERE std.product_id='$stock_name' AND std.date <= '$date_from' AND std.company_id='$company_id' AND std.branch_id='$branch_id' AND std.packaging_id='$package_id' AND std.transfer_type!='1' AND (std.status='R' OR std.status='$include_finished' OR std.status='$include_saved') AND sth.warehouse_id='$warehouse_id' AND sth.ref_id=std.ref_id");
	while($rr_row = mysql_fetch_array($fetch_stdb)){
		$data[] = array(
			"in" => "",
			"out" => $rr_row["qty"],
			"unit_price" => $rr_row["cost"]
		);
	}
	
	//STOCK TRANSFER DESTINATION WAREHOUSE TO BLDG EGGS(OUT)
	$fetch_stdb = mysql_query("SELECT * FROM `tbl_st_bldg_to_receive_eggs` AS std, `tbl_stock_transfer_bldg_eggs` AS sth WHERE std.product_id='$stock_name' AND std.date <= '$date_from' AND std.company_id='$company_id' AND std.branch_id='$branch_id' AND std.packaging_id='$package_id' AND (std.status='R' OR std.status='$include_finished' OR std.status='$include_saved') AND sth.warehouse_id='$warehouse_id' AND sth.ref_id=std.ref_id");
	while($rr_row = mysql_fetch_array($fetch_stdb)){
		$data[] = array(
			"in" => "",
			"out" => ($rr_row["qty"]-$rr_row["return_qty"]),
			"unit_price" => $rr_row["cost"]
		);
	}
	
	//STOCK TRANSFER DESTINATION WAREHOUSE TO BLDG PIG(OUT)
	$fetch_stdb = mysql_query("SELECT * FROM `tbl_st_bldg_details` AS std, `tbl_stock_transfer_bldg` AS sth WHERE std.product_id='$stock_name' AND std.date <= '$date_from' AND std.company_id='$company_id' AND std.branch_id='$branch_id' AND std.packaging_id='$package_id' AND std.transfer_type!='1' AND (std.status='R' OR std.status='$include_finished' OR std.status='$include_saved') AND sth.warehouse_id='$warehouse_id' AND sth.ref_id=std.ref_id");
	while($rr_row = mysql_fetch_array($fetch_stdb)){
		$data[] = array(
			"in" => "",
			"out" => $rr_row["qty"],
			"unit_price" => $rr_row["cost"]
		);
	}
	
	//STOCK TRANSFER DESTINATION WAREHOUSE TO BLDG PIG(OUT)
	$fetch_stdb = mysql_query("SELECT * FROM `tbl_st_bldg_to_receive` AS std, `tbl_stock_transfer_bldg` AS sth WHERE std.product_id='$stock_name' AND std.date <= '$date_from' AND std.company_id='$company_id' AND std.branch_id='$branch_id' AND std.packaging_id='$package_id' AND (std.status='R' OR std.status='$include_finished' OR std.status='$include_saved') AND sth.warehouse_id='$warehouse_id' AND sth.ref_id=std.ref_id");
	while($rr_row = mysql_fetch_array($fetch_stdb)){
		$data[] = array(
			"in" => "",
			"out" => ($rr_row["qty"]-$rr_row["return_qty"]),
			"unit_price" => $rr_row["cost"]
		);
	}
	
	//STOCK TRANSFER DESTINATION BLDG TO WAREHOUSE PIG(IN)
	$fetch_stdb = mysql_query("SELECT * FROM `tbl_std_bldg_warehouse_pig` AS std, `tbl_stock_transfer_bldg_warehouse_pig` AS sth WHERE std.product_id='$stock_name' AND std.date <= '$date_from' AND sth.company_id='$company_id' AND sth.branch_id='$branch_id' AND std.packaging_id='$package_id' AND(std.status='$include_finished' OR std.status='$include_saved') AND sth.warehouse_id='$warehouse_id' AND sth.ref_id=std.ref_id");
	while($rr_row = mysql_fetch_array($fetch_stdb)){
		$data[] = array(
			"in" => $rr_row["qty"],
			"out" => "",
			"unit_price" => $rr_row["cost"]
		);
	}
	
	//FROM BB (IN)/
	$fetch_bb = mysql_query("SELECT * FROM `tbl_beginning_balance` WHERE product_id='$stock_name'  AND `date` <= '$date_from' AND company_id='$company_id' AND warehouse_id='$warehouse_id' AND  branch_id='$branch_id' AND package_id='$package_id' AND (status='$include_finished' OR status='$include_saved')");
	while($rr_row = mysql_fetch_array($fetch_bb)){
		
		$data[] = array(
			"in" => $rr_row["qty"],
			"out" =>"",
			"unit_price" => $rr_row["cost"],
		);
	}

	//CONSUMABLES(OUT)
	$fetch_consumables = mysql_query("SELECT * FROM tbl_consumables_details as cd, tbl_warehouse_product_cost as wpc WHERE cd.c_date BETWEEN '$date_from' AND '$date_to' AND cd.product_id='$stock_name' AND wpc.product_id = cd.product_id AND cd.branch_id='$branch_id' AND cd.company_id='$company_id' AND cd.package_id='$package_id' AND (cd.status='$include_finished' OR cd.status='$include_saved')  and cd.company_id = wpc.company_id and cd.branch_id and wpc.branch_id ");
		while($rr_row = mysql_fetch_array($fetch_consumables)){
			$data[] = array(
				"in" => "",
				"out" =>$rr_row["qty"],
				"unit_price" => $rr_row["cost"]
			);
		}

	//INVENTORY ADJUSTMENT (IN)
	$fetch_ia_add = mysql_query("SELECT * FROM `tbl_inventory_adjustment_details` WHERE company_id='$company_id' AND branch_id='$branch_id' AND product_id='$stock_name' AND package_id='$package_id' AND warehouse_id='$warehouse_id' AND `date` <= '$date_from' AND (status='$include_finished' OR status='$include_saved') AND qty > 0");
	while($rr_row = mysql_fetch_array($fetch_ia_add)){
		
		$data[] = array(
			"in" => $rr_row["qty"],
			"out" =>"",
			"unit_price" => 0
		);
	}

	//INVENTORY ADJUSTMENT (OUT)
	$fetch_ia_minus = mysql_query("SELECT * FROM `tbl_inventory_adjustment_details` WHERE company_id='$company_id' AND branch_id='$branch_id' AND product_id='$stock_name' AND package_id='$package_id' AND warehouse_id='$warehouse_id' AND `date` <= '$date_from' AND (status='$include_finished' OR status='$include_saved') AND qty < 0");
	while($rr_row = mysql_fetch_array($fetch_ia_minus)){
		
		$data[] = array(
			"in" => "",
			"out" => abs($rr_row["qty"]),
			"unit_price" => 0
		);
	}

	// CONSTRUCTION IN PROGRESS(OUT)
	// $fetch_cip_minus = mysql_query("SELECT * FROM tbl_construction_in_progress_detail as d , tbl_construction_in_progress as h WHERE h.cip_id = d.cip_id AND h.company_id = '$company_id' AND h.branch_id = '$branch_id' AND d.product_id = '$stock_name' AND d.packaging_id = '$package_id' AND d.source_warehouse = '$warehouse_id' AND d.transfer_date <= '$date_from' AND (d.transfer_status='$include_finished' OR d.transfer_status='$include_saved')");
	// while($rr_row = mysql_fetch_array($fetch_cip_minus)){
	// 	$data[] = array(
	// 		"in" => "",
	// 		"out" => $rr_row["product_qty"],
	// 		"unit_price" => $rr_row["product_cost"]
	// 	);
	// }

	
	$date = array();
	if($data){
		foreach($data as $key => $rr_row){
			$date[] = $rr_row['date'];
		}
		array_multisort($data,SORT_ASC,$date);
			
		foreach($data as $key => $rr_row){
			$balance +=$rr_row[in];
			$balance -=$rr_row[out];	

		$total_amount +=$rr_row[in] * $rr_row["unit_price"];
		$total_amount -=$rr_row[out] * $rr_row["unit_price"];
		
		if($balance == 0){}else{

		$average_price = $total_amount / $balance;
		$final_total_amount =  $balance * $average_price;
		}
		}
	}
	return $average_price;
	
}



//Job ORDER formulas!

	function getPackageQty($id){
		
		//Package qty
		$query="SELECT * FROM tbl_package WHERE package_id='$id' AND (hide_fp = '1' OR hide_pm = '1' OR hide_rm = '1')";
		$result=mysql_query($query);
		$r=mysql_fetch_assoc($result);
		return $r[qty];
	}

	//package QTY IN Jo
	function getPackageQtyInJO($actualoutput,$typeofpackage){
		$qtypackagetype=getPackageQty($typeofpackage);
		
		$packageqty=intval($actualoutput/$qtypackagetype);
		$remainder=number_format($actualoutput%$qtypackagetype,0,'','');
		
		return $packageqty;
	}
	
	//get Remainder
	function getRemainder($actualoutput,$typeofpackage){
		$qtypackagetype=getPackageQty($typeofpackage);
		
		$packageqty=number_format($actualoutput/$qtypackagetype,0,'','');
		$remainder=number_format($actualoutput%$qtypackagetype,0,'','');
		
		return $remainder;
	}
		
	//number of package
	function getNumberOfPackage($p1, $p2, $p3){
		$totalpackagecost = $p1 + $p2 + $p3;
		return $totalpackagecost;
	}
	
	//bags produce
	function bagsproduce($typeID){
		$q = mysql_query("SELECT * FROM tbl_package WHERE package_id='$typeID'");
		$rowQ = mysql_fetch_assoc($q);
		
		return $rowQ[qty];
	}
	
	//packaging material cost
	function getPkmatCost($wh,$pk1,$pk2,$pk3, $bags){
		if(!empty($pk1)){
			$pk_1 = getBulkData("cost","tbl_warehouse_product_cost","warehouse_id = '$wh' AND product_id='$pk1'");
		}
		if(!empty($pk2)){
			$pk_2 = getBulkData("cost","tbl_warehouse_product_cost","warehouse_id = '$wh' AND product_id='$pk2'");
		}
		if(!empty($pk3)){
			$pk_3 = getBulkData("cost","tbl_warehouse_product_cost","warehouse_id = '$wh' AND product_id='$pk3'");
		}

		$pk1_cost = ($pk_1[cost] * $bags) + ($pk_2[cost] * $bags) + ($pk_3[cost] * $bags);
		$totalPk = $pk1_cost;
		
		return $totalPk;
	}

	//job order cost
	function getJoborderCost($numOfBatch){
		
	}

	//canceling job order
	function cancelJobOrder(){
		
	}

	//Ai expected output
	function getAiExpectedOutput($id, $batch, $excess, $joID){
		$getQty = mysql_query("SELECT SUM(quantity) AS total FROM tbl_ai_semen_production_details_pig WHERE formulation_id ='$id' AND joborder_header_id = '$joID'");
		
		$rowqty = mysql_fetch_assoc($getQty);
		//$rowamnt = mysql_fetch_assoc($getAmnt);
		//$f_tcost = $rowamnt[ftotal]/$rowqty[total];
		
		$grnd = ($rowqty[total]*$batch) + $excess;
		return $grnd;
	}

	//expected output
	function getExpectedOutput($id, $batch, $excess, $joID){
		$getQty = mysql_query("SELECT SUM(quantity) AS total FROM tbl_joborder_details_feeds WHERE formulation_id ='$id' AND joborder_header_id = '$joID'");
		
		$rowqty = mysql_fetch_assoc($getQty);
		//$rowamnt = mysql_fetch_assoc($getAmnt);
		//$f_tcost = $rowamnt[ftotal]/$rowqty[total];
		
		$grnd = ($rowqty[total]*$batch) + $excess;
		return $grnd;
	}

	//Ai Formulation Cost
	function AiFC($id, $batch, $joID, $wh = NULL){
		$getJOstats = getBulkData("status","tbl_ai_semen_production_header_pig","joborder_header_id = '$joID'");
		$getQty = mysql_query("SELECT * FROM tbl_ai_semen_production_details_pig WHERE formulation_id ='$id' AND joborder_header_id = '$joID'");
		while ($getRows = mysql_fetch_assoc($getQty)) {
			if($getJOstats[status] == "F"){
				$getCost = $getRows[cost];
			}else{
				$getwhcst = getBulkData("cost","tbl_warehouse_product_cost","warehouse_id = '$wh' AND product_id = '$getRows[material]'");
				$getCost = $getwhcst[cost];
			}
			$getQTY += $getRows[quantity] * $getCost;
		}

		$grnd = $getQTY * $batch;
		return $grnd;
	}

	//Formulation Cost
	function FC($id, $batch, $joID, $wh = NULL){
		$getJOstats = getBulkData("status","tbl_joborder_header_feeds","joborder_header_id = '$joID'");
		$getQty = mysql_query("SELECT * FROM tbl_joborder_details_feeds WHERE formulation_id ='$id' AND joborder_header_id = '$joID'");
		while ($getRows = mysql_fetch_assoc($getQty)) {
			if($getJOstats["status"] == "F"){
				$getCost = $getRows[cost];
			}else{
				$getwhcst = getBulkData("cost","tbl_warehouse_product_cost","warehouse_id = '$wh' AND product_id = '$getRows[material]'");
				$getCost = $getwhcst[cost];
			}
			
			$getQTY += $getRows[quantity] * $getCost;
		}

		$grnd = $getQTY * $batch;
		return $grnd;
	}
	
	// Job Order averaging
	function joAveraging($actualOut, $joCost, $currINV, $currCost, $package_type, $jo_id, $fnprod){
		
		//$CurrInv = $currINV;

		if(!empty($currCost)){
			$CurrCost = $currCost;
		}else{
			$CurrCost = 0;
		}

		if(!empty($currINV)){
			$oldINV = $currINV;
		}else{
			$oldINV = 0;
		}
		
		$sec = getsec($package_type, $fnprod);
		$td = getbgs($package_type, $fnprod);

		//$jo_Cost_x = number_format($joCost,2);
		$jo_Cost = $joCost;

		$babaw = (($jo_Cost*$actualOut) + ($oldINV*$CurrCost));
		if($babaw >= 1){
			$first = $babaw / ($actualOut + $oldINV);
		}else{
			$first = 0;
		}

		$fin = $first;
		//$test = "((". $jo_Cost . " * " . $actualOut. ") + (" .$oldINV."*".$CurrCost. ")) / (" . $actualOut . " + " . $oldINV . ") = " . $fin;
		return $fin;
	}

	// Ai Semen Job Order averaging
	function AiJoAveraging($actualOut, $joCost, $currINV, $currCost, $package_type, $jo_id, $fnprod){
		
		//$CurrInv = $currINV;

		if(!empty($currCost)){
			$CurrCost = $currCost;
		}else{
			$CurrCost = 0;
		}

		if(!empty($currINV)){
			$oldINV = $currINV;
		}else{
			$oldINV = 0;
		}
		
		$sec = getsec($package_type, $fnprod);
		$td = getbgs($package_type, $fnprod);

		//$jo_Cost_x = number_format($joCost,2);
		$jo_Cost = $joCost;

		$babaw = (($jo_Cost*$actualOut) + ($oldINV*$CurrCost));
		if($babaw >= 1){
			$first = $babaw / ($actualOut + $oldINV);
		}else{
			$first = 0;
		}

		$fin = $first;
		//$test = "((". $jo_Cost . " * " . $actualOut. ") + (" .$oldINV."*".$CurrCost. ")) / (" . $actualOut . " + " . $oldINV . ") = " . $fin;
		return $fin;
	}
	
	function joAveragingtest($actualOut, $joCost, $currINV, $currCost, $package_type, $jo_id, $fnprod){
		
		$CurrInv = $currINV;
		$CurrCost = $currCost;

		$sec = getsec($package_type, $fnprod);
		$td = getbgs($package_type, $fnprod);

		$jo_Cost_x = number_format($joCost,2);
		$jo_Cost = $joCost;
		//$first = ($actualOut * $jo_Cost) + $sec;
		//$third = ($actualOut + $td); 
		$first_x = (($jo_Cost*$actualOut) + ($td*$CurrCost)) / ($actualOut + $td);
		$first = ((2*$actualOut) + ($td*$CurrCost)) / ($actualOut + $td);
		//$third = 
		
		$fin = $first;//$first / $third;
		// old formula (($actualOut * $jo_Cost) + $sec)/($actualOut + $val);
		$test = "(". 2 . " * " . $actualOut. ") + ((" .$td."*".$CurrCost. "))) / (" . $actualOut . " + " . $td . ") = " . $fin;
		return $test;
	}
	
//Job ORDER formulas end!


//get the current inventory of packaging types (ex: 50kg, 25kg, 10kg) in kilos!
function getpkgInv($fnprod, $package_type, $jo_id){
	
	$query = mysql_query("SELECT SUM(jh.actual_output) AS pkgtotal FROM tbl_joborder_header_feeds AS jh, tbl_package AS pkg WHERE jh.package_type = pkg.package_id AND pkg.package_id = '$package_type' AND fin_product = '$fnprod' AND jh.status = 'F'") or die(mysql_error());
	$getQRow = mysql_fetch_assoc($query);
		
	return $getQRow['pkgtotal'];
}

function getbgs($package_type, $fnprod){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$qpkg = mysql_query("SELECT * FROM tbl_package WHERE ((company_id = '$company_id' AND branch_id = '$branch_id') OR (company_id = '0' AND branch_id = '0')) AND package_id = '$package_type'") or die(mysql_error());
		while($rQ = mysql_fetch_assoc($qpkg)){
			$pkgQTY = getpkgInv($fnprod, $rQ[package_id], "1");
			//$getbags = explode(".", $pkgQTY/$rQ[qty]);
			$bags = floor($pkgQTY/$rQ[qty]);
			//$sec += ($bags * $rQ[qty]) * $CurrCost;
			$td += $bags * $rQ[qty];
		}
		
		return $td;
}

function getsec($package_type, $fnprod){
	$qpkg = mysql_query("SELECT * FROM tbl_package") or die(mysql_error());
		while($rQ = mysql_fetch_assoc($qpkg)){
			$pkgQTY = getpkgInv($fnprod, $rQ[package_id], "1");
			//$getbags = explode(".", $pkgQTY/$rQ[qty]);
			$bags = floor($pkgQTY/$rQ[qty]);
			$sec = $bags * $rQ[qty];
			$fnn = $sec * $CurrCost;
			//$td += $bags * $rQ[qty];
		}
		return $fnn;
}

function getRemaining($product_id, $wh = NULL, $company_id = NULL, $branch_id = NULL){
	if(!empty($wh)){ // if the wh parameter is not empty
		$WH = "AND location = '$wh'";
	}else{
		$WH = "";
	}
	$inventory_date = getCurrentDate();

	$stock_destination_pc = 0;
	$stock_destination_pc_flock = 0;
	$stock_source_pc = 0;
	$stock_source_pc_flock = 0;
	$deduction = 0;
	$getBBexcess = 0;

	//add
	$stock_destination_pc = mysql_fetch_array(mysql_query("SELECT sum(actual_qty_converted_to) from tbl_product_conversion where convert_to_id='$product_id' and company_id='$company_id' and branch_id='$branch_id' and warehouse_id='$wh' and status='F' and packaging_id_converted_to='-1' and date <= DATE_ADD('$inventory_date', INTERVAL 1 DAY) and flock_convert_status='0'"));
	
	//add flock
	$stock_destination_pc_flock = mysql_fetch_array(mysql_query("SELECT sum(actual_qty_converted_to) from tbl_product_conversion where convert_to_id='$product_id' and company_id='$company_id' and branch_id='$branch_id' and warehouse_id='$wh' and status='F' and packaging_id_converted_to='-1' and date <= DATE_ADD('$inventory_date', INTERVAL 1 DAY) and flock_convert_status='1'"));

	//minus
	$stock_source_pc = mysql_fetch_array(mysql_query("SELECT sum(actual_qty_original_item) from tbl_product_conversion where original_item_id='$product_id' and company_id='$company_id' and branch_id='$branch_id' and warehouse_id='$wh' and status='F' and packaging_id_original_item='-1' and date <= DATE_ADD('$inventory_date', INTERVAL 1 DAY) and flock_convert_status='0'"));

	//minus flock
	$stock_source_pc_flock = mysql_fetch_array(mysql_query("SELECT sum(actual_qty_original_item) from tbl_product_conversion where original_item_id='$product_id' and company_id='$company_id' and branch_id='$branch_id' and warehouse_id='$wh' and status='F' and packaging_id_original_item='-1' and date <= DATE_ADD('$inventory_date', INTERVAL 1 DAY) and flock_convert_status='2'"));

	$getExcess = mysql_query("SELECT SUM(remaining) AS total FROM tbl_joborder_header_feeds WHERE fin_product = '$product_id' $WH AND status = 'F' AND company_id = '$company_id' AND branch_id = '$branch_id'") or die(mysql_error());
	$rowExcess = mysql_fetch_assoc($getExcess);
	
	$deduction = getBulkData("SUM(excess_used) AS totalEx","tbl_joborder_header_feeds","fin_product = '$product_id' AND status = 'F' $WH AND company_id = '$company_id' AND branch_id = '$branch_id'");

	$getBBexcess = getBulkData("SUM(excess) AS bbExcess","tbl_beginning_balance","product_id = '$product_id' AND status = 'F' AND warehouse_id = '$wh' AND company_id = '$company_id' AND branch_id = '$branch_id'");

	$finExcess = (($rowExcess[total] + $getBBexcess[bbExcess] + $stock_destination_pc[0] + $stock_destination_pc_flock[0]) - ($deduction[totalEx] + $stock_source_pc[0] + $stock_source_pc_flock[0]));
	return $finExcess;
}

function buildingProdCost_pig($building_id, $product_id){
		$company_id = $_SESSION['system']['company_id'];
		$branch_id = get_branch();
		$result = mysql_query("SELECT cost from tbl_building_products where company_id = '$company_id' and branch_id = '$branch_id' and building_id = '$building_id' and product_id = '$product_id' ");
		$row = mysql_fetch_assoc($result);

		return $row["cost"];

}

function buildingProdCost_eggs($building_id, $product_id){
		$company_id = $_SESSION['system']['company_id'];
		$branch_id = get_branch();
		$result = mysql_query("SELECT cost from tbl_building_products_eggs where company_id = '$company_id' and branch_id = '$branch_id' and building_id = '$building_id' and product_id = '$product_id' ");
		$row = mysql_fetch_assoc($result);

		return $row["cost"];

}
function buildingProdCost_broiler($building_id, $product_id){
		$company_id = $_SESSION['system']['company_id'];
		$branch_id = get_branch();
		$result = mysql_query("SELECT cost from tbl_building_products_broiler where company_id = '$company_id' and branch_id = '$branch_id' and building_id = '$building_id' and product_id = '$product_id' ");
		$row = mysql_fetch_assoc($result);

		return $row["cost"];

}

function getBuildingId_pig($id){
	$company_id = $_SESSION['system']['company_id'];
	// $branch_id = get_branch();
	$result = mysql_query("select building_id from tbl_pen_assignment where pen_assignment_id = '$id' and company_id = '$company_id'") or die (mysql_error());
	$row = mysql_fetch_array($result);
	
	return $row[0];
}

function getBulkData($col, $table, $params){
	$query = mysql_query("SELECT $col FROM $table WHERE $params") or die(mysql_error());
	$row = mysql_fetch_assoc($query);
	return $row;
}

function getCurrentBalance($stock_name,$warehouse_id,$date_from,$date_to){
		//FROM RECEIVING (IN)
		$fetch_rr = mysql_query("SELECT * FROM `tbl_rr_details` WHERE status='F' AND product_id='$stock_name' AND warehouse_id='$warehouse_id' AND date_added BETWEEN '$date_from' AND '$date_to'");
		while($rr_row = mysql_fetch_array($fetch_rr)){
			$data[] = array(
				"date" => $rr_row["date_added"],
				"transaction" => "Added to Inventory - " .$rr_row["receiving_number"],
				"in" => $rr_row["quantity"],
				"out" =>"",
				"unit_price" => $rr_row["supplier_price"]
			);
		}
		
		//FEEDING GROWING	(OUT)
		$fetch_rr = mysql_query("SELECT * FROM tbl_feeds_entry_eggs WHERE item='$stock_name'  AND warehouse_id='$warehouse_id' AND date BETWEEN '$date_from' AND '$date_to'");
		while($rr_row = mysql_fetch_array($fetch_rr)){
			
			$fetch_gm = mysql_query("SELECT * FROM tbl_growing_module_eggs WHERE growing_id = '$rr_row[growing_id]'");
			$gm_row = mysql_fetch_array($fetch_gm);
			
			$data[] = array(
				"date" => $rr_row["date"],
				"transaction" => "Growing Module - " .$gm_row["batch_name"],
				"in" => "",
				"out" =>$rr_row["qty"],
				"unit_price" => $rr_row["cost"]
			);
		}
		
		//MED AND VAC GROWING (OUT)
		$fetch_rr = mysql_query("SELECT * FROM tbl_medication_entry_eggs WHERE item='$stock_name'  AND warehouse_id='$warehouse_id' AND date BETWEEN '$date_from' AND '$date_to'");
		while($rr_row = mysql_fetch_array($fetch_rr)){
			
			$fetch_gm = mysql_query("SELECT * FROM tbl_growing_module_eggs WHERE growing_id = '$rr_row[growing_id]'");
			$gm_row = mysql_fetch_array($fetch_gm);
			
			$data[] = array(
				"date" => $rr_row["date"],
				"transaction" => "Growing Module - " .$gm_row["batch_name"],
				"in" => "",
				"out" =>$rr_row["qty"],
				"unit_price" => $rr_row["cost"]
			);
		}
			
		//GROWING MODULE (OUT)
		$branch_id = get_branch();

			$fetch_rr = mysql_query("SELECT * FROM tbl_growing_module_eggs WHERE growing_date BETWEEN '$date_from' AND '$date_to' AND stock_id='$stock_name'  AND warehouse_id='$warehouse_id'");
				while($rr_row = mysql_fetch_array($fetch_rr)){
					$data[] = array(
						"date" => $rr_row["growing_date"],
							"transaction" => "Added to Growing Module",
							"in" => "",
							"out" =>$rr_row["previous_qty"],
							"unit_price" => $rr_row["previous_cost"]
					);
				}


			$fetch_rr = mysql_query("SELECT * FROM tbl_growing_module_broiler WHERE growing_date BETWEEN '$date_from' AND '$date_to' AND stock_id='$stock_name'  AND warehouse_id='$warehouse_id'");
				while($rr_row = mysql_fetch_array($fetch_rr)){
					$data[] = array(
						"date" => $rr_row["growing_date"],
							"transaction" => "Added to Growing Module",
							"in" => "",
							"out" =>$rr_row["previous_qty"],
							"unit_price" => $rr_row["previous_cost"]
					);
				}	
				
		///PRODUCTION (OUT)
		$fetch_rr = mysql_query("SELECT * FROM tbl_production_eggs WHERE start_date BETWEEN '$date_from' AND '$date_to'  AND warehouse_id='$warehouse_id' AND stock_id='$stock_name' AND tag='FRR' AND warehouse_id='$warehouse_id'");
				while($rr_row = mysql_fetch_array($fetch_rr)){
					$data[] = array(
						"date" => $rr_row["start_date"],
							"transaction" => "Added to Growing Module",
							"in" => "",
							"out" =>$rr_row["previous_qty"],
							"unit_price" => $rr_row["previous_cost"]
					);
				}
		
		
		//FEEDING PRODUCTION (OUT)	
		$fetch_rr = mysql_query("SELECT * FROM tbl_feeds_entry_production_eggs WHERE item='$stock_name' AND date BETWEEN '$date_from' AND '$date_to'  AND warehouse_id='$warehouse_id'");
		while($rr_row = mysql_fetch_array($fetch_rr)){
			
			$fetch_production = mysql_query("SELECT * FROM tbl_production_eggs WHERE production_id = '$rr_row[production_id]'");
			$production_row = mysql_fetch_array($fetch_production);
			
			$data[] = array(
				"date" => $rr_row["date"],
				"transaction" => "Production - " .$production_row["batch_name"],
				"in" => "",
				"out" =>$rr_row["qty"],
				"unit_price" => $rr_row["cost"]
			);
		}
		
		//MED AND VAC PRODUCTION (OUT)
		$fetch_rr = mysql_query("SELECT * FROM tbl_medication_entry_production_eggs WHERE item='$stock_name' AND date BETWEEN '$date_from' AND '$date_to'  AND warehouse_id='$warehouse_id'");
		
			$fetch_production = mysql_query("SELECT * FROM tbl_production_eggs WHERE production_id = '$rr_row[production_id]'");
			$production_row = mysql_fetch_array($fetch_production);
		
		while($rr_row = mysql_fetch_array($fetch_rr)){
			$data[] = array(
				"date" => $rr_row["date"],
				"transaction" => "Production - " .$production_row["batch_name"],
				"in" => "",
				"out" =>$rr_row["qty"],
				"unit_price" => $rr_row["cost"]
			);
		}
		//EGG INV PRODUCTION (IN)
		$fetch_rr = mysql_query("SELECT * FROM tbl_inventory_entry_production_eggs WHERE item='$stock_name' AND date BETWEEN '$date_from' AND '$date_to'  AND warehouse_id='$warehouse_id'");
		while($rr_row = mysql_fetch_array($fetch_rr)){
			$fetch_production = mysql_query("SELECT * FROM tbl_production_eggs WHERE production_id = '$rr_row[production_id]'");
			$production_row = mysql_fetch_array($fetch_production);
			$data[] = array(
				"date" => $rr_row["date"],
				"transaction" => "Production Module -"." ".$production_row["batch_name"],
				"in" => $rr_row["qty"],
				"out" =>"",
				"unit_price" => $rr_row["cost"]
			);
		}

		
		///STOCK TRANSFER SOURCE (OUT)
		$fetch_rr = mysql_query("SELECT * FROM tbl_stock_transfer_details WHERE product_id='$stock_name' AND date BETWEEN '$date_from' AND '$date_to'  AND source_location='$warehouse_id'  AND status='F'");
		while($rr_row = mysql_fetch_array($fetch_rr)){
			$data[] = array(
				"date" => $rr_row["date"],
				"transaction" => "Stock Transfer -"." ".$rr_row["ref_id"],
				"in" => "",
				"out" =>$rr_row["qty"],
				"unit_price" => $rr_row["cost"]
			);
		}
		
		//STOCK TRANSFER DESTINATION (IN)
		$fetch_rr = mysql_query("SELECT * FROM tbl_stock_transfer_details WHERE product_id='$stock_name' AND date BETWEEN '$date_from' AND '$date_to'  AND destination_location='$warehouse_id' AND status='F'");
		while($rr_row = mysql_fetch_array($fetch_rr)){
			$data[] = array(
				"date" => $rr_row["date"],
				"transaction" => "Stock Transfer -"." ".$rr_row["ref_id"],
				"in" => $rr_row["qty"],
				"out" =>"",
				"unit_price" => $rr_row["cost"]
			);
		}
		
		//CONVERT TO (IN)
		$fetch_rr = mysql_query("SELECT * FROM tbl_product_conversion WHERE convert_to_id='$stock_name' AND date BETWEEN '$date_from' AND '$date_to'  AND warehouse_id='$warehouse_id' AND status='F' AND flock_convert_status='0'");
		while($rr_row = mysql_fetch_array($fetch_rr)){
			$data[] = array(
				"date" => $rr_row["date"],
				"transaction" => "Product Conversion From ".getProdName($rr_row["original_item_id"])."",
				"in" => $rr_row["original_item_qty"],
				"out" =>"",
				"unit_price" => $rr_row["original_item_cost"]
			);
		}	

		//CONVERT TO (IN) flock
		$fetch_rr = mysql_query("SELECT * FROM tbl_product_conversion WHERE convert_to_id='$stock_name' AND date BETWEEN '$date_from' AND '$date_to'  AND warehouse_id='$warehouse_id' AND status='F' AND flock_convert_status='1'");
		while($rr_row = mysql_fetch_array($fetch_rr)){
			$data[] = array(
				"date" => $rr_row["date"],
				"transaction" => "Product Conversion From ".getProdName($rr_row["original_item_id"])."",
				"in" => $rr_row["original_item_qty"],
				"out" =>"",
				"unit_price" => $rr_row["original_item_cost"]
			);
		}	
		
		//CANCEL CONVERT TO (OUT)
		//$fetch_rr = mysql_query("SELECT * FROM tbl_product_conversion WHERE convert_to_id='$stock_name' AND date BETWEEN '$date_from' AND '$date_to'  AND warehouse_id='$warehouse_id' AND status='C'");
		//while($rr_row = mysql_fetch_array($fetch_rr)){
			//$data[] = array(
				//"date" => $rr_row["date"],
				//"transaction" => "Cancelled Product Conversion ".getProdName($rr_row["original_item_id"])."",
				//"in" => "",
				//"out" =>$rr_row["original_item_qty"],
				//"unit_price" => $rr_row["original_item_cost"]
			//);
		//}
		
		
		//ORIGINAL ITEM (OUT)
		$fetch_rr = mysql_query("SELECT * FROM tbl_product_conversion WHERE original_item_id='$stock_name' AND date BETWEEN '$date_from' AND '$date_to'  AND warehouse_id='$warehouse_id' AND status='F' AND flock_convert_status='0'");
		while($rr_row = mysql_fetch_array($fetch_rr)){
			$data[] = array(
				"date" => $rr_row["date"],
				"transaction" => "Product Conversion to ".getProdName($rr_row["convert_to_id"])."",
				"in" => "",
				"out" =>$rr_row["original_item_qty"],
				"unit_price" => $rr_row["original_item_cost"]
			);
		}

		//ORIGINAL ITEM (OUT) flock
		$fetch_rr = mysql_query("SELECT * FROM tbl_product_conversion WHERE original_item_id='$stock_name' AND date BETWEEN '$date_from' AND '$date_to'  AND warehouse_id='$warehouse_id' AND status='F' AND flock_convert_status='2'");
		while($rr_row = mysql_fetch_array($fetch_rr)){
			$data[] = array(
				"date" => $rr_row["date"],
				"transaction" => "Product Conversion to ".getProdName($rr_row["convert_to_id"])."",
				"in" => "",
				"out" =>$rr_row["original_item_qty"],
				"unit_price" => $rr_row["original_item_cost"]
			);
		}
		
		//PURCHASE RETURN
		$fetch_rr = mysql_query("SELECT * FROM tbl_purchase_return_details WHERE product_id='$stock_name' AND date BETWEEN '$date_from' AND '$date_to'  AND warehouse_id='$warehouse_id' AND status='F'");
		while($rr_row = mysql_fetch_array($fetch_rr)){
			$data[] = array(
				"date" => $rr_row["date"],
				"transaction" => "Purchase Return -"." ".$rr_row["pr_num"],
				"in" => "",
				"out" =>$rr_row["qty"],
				"unit_price" => $rr_row["cost"]
			);
		}
		
		
		//DELIVERIES (OUT)
		$fetch_rr = mysql_query("SELECT * from tbl_dr_detail as dd, tbl_dr_header as dh where dd.delivery_number = dh.delivery_number and (dh.status = 'P' or dh.status = 'F') and dd.stock_id = '$stock_name' and dh.dr_date BETWEEN '$date_from' AND '$date_to' and dh.company_id = '$company_id' and dh.branch_id = '$branch_id' and dh.warehouse_id='$warehouse_id'");
		while($rr_row = mysql_fetch_array($fetch_rr)){
			$data[] = array(
				"date" => $rr_row["dr_date"],
				"transaction" => "Deliveries -"." ".$rr_row["delivery_number"],
				"in" => "",
				"out" =>$rr_row["quantity"],
				"unit_price" => $rr_row["cost"]
			);
		}
		
		//SALES (IN)
		$fetch_rr = mysql_query("SELECT * from tbl_sales_return_details as sd, tbl_sales_return as sh where sd.sr_number = sh.sr_number and  sh.status = 'F' and sd.product_id = '$stock_name' and sh.sr_date BETWEEN '$date_from' AND '$date_to' and sh.company_id = '$company_id' and sh.branch_id = '$branch_id' and sh.warehouse_id='$warehouse_id'");
		while($rr_row = mysql_fetch_array($fetch_rr)){
			$data[] = array(
				"date" => $rr_row["date_added"],
				"transaction" => "Sales Return -"." ".$rr_row["sr_number"],
				"in" => $rr_row["quantity"],
				"out" =>"",
				"unit_price" => $rr_row["cost"]
			);
		}
		
			$date = array();
		if($data){
			foreach($data as $key => $rr_row){
				$date[] = $rr_row['date'];
			}
			array_multisort($data,SORT_ASC,$date);
				
			foreach($data as $key => $rr_row){
				$balance +=$rr_row[in];
				$balance -=$rr_row[out];	
				
			}
			
			return $balance;
		}
						
	}
	
	function getRawMatCost($stock_id,$warehouse_id,$company_id,$b_id){
		$company_id = $_SESSION['system']['company_id'];
		$result = mysql_query("select * from tbl_warehouse_product_cost where company_id = '$company_id' and branch_id = '$b_id' and warehouse_id = '$warehouse_id' and product_id = '$stock_id' ") or die(mysql_error());
		$row = mysql_fetch_assoc($result);
		
		return $row["cost"];
	}

	function getPKGCost($stock_id,$warehouse_id,$company_id,$b_id){
		$company_id = $_SESSION['system']['company_id'];
		$result = mysql_query("select * from tbl_warehouse_product_cost where company_id = '$company_id' and branch_id = '$b_id' and warehouse_id = '$warehouse_id' and product_id = '$stock_id' ") or die(mysql_error());
		$row = mysql_fetch_assoc($result);
		
		return $row["cost"];
	}

	function getAllBranch($company_id){
		echo "<option value=''>Select Branch:</option>";
		$b = mysql_query("SELECT * FROM tbl_branch WHERE company_id = '$company_id'") or die(mysql_error());
		while($rowB = mysql_fetch_assoc($b)){
			echo "<option value='".$rowB[branch_id]."'>".$rowB[branch]."</option>";
		}
	}

	function getAllAccounts($company_id, $branch_id){
		$content = "<option value=''>Select Accounts:</option>";

		//suppliers
		$content .= "<optgroup label='Suppliers'>";
		$b = mysql_query("SELECT * FROM tbl_supplier WHERE company_id = '$company_id' AND branch_id = '$branch_id' and hide_unhide_status != 1") or die(mysql_error());
		while($rowB = mysql_fetch_assoc($b)){
			$content .= "<option value='s-".$rowB[supplier_id]."'>".$rowB[supplier]."</option>";
		}
		$content .= "</optgroup>";

		//employees
		$content .= "<optgroup label='Employees'>";
		$e = mysql_query("SELECT * FROM tbl_employee WHERE company_id = '$company_id' AND branch_id = '$branch_id' AND emp_terminate != 'Y' ") or die(mysql_error());
		while($rowE = mysql_fetch_assoc($e)){

			$content .= "<option value='e-".$rowE[employee_id]."'>".$rowE[emp_firstname]. " " .$rowE[emp_lastname]."</option>";
			
		}
		$content .= "</optgroup>";

		//customer
		$content .= "<optgroup label='Customers'>";
		$c = mysql_query("SELECT * FROM tbl_customer WHERE company_id = '$company_id' AND branch_id = '$branch_id'") or die(mysql_err());
		while($rowC = mysql_fetch_assoc($c)){

			$content .= "<option value='c-".$rowC[customer_id]."'>".$rowC[customer]."</option>";
			
		}
		$content .= "</optgroup>";


		return $content;
	}

	function getAllChart($company_id, $branch_id){
		
		$content = "<option value=''>Select Accounts:</option>";
		$ch = mysql_query("SELECT * FROM tbl_gchart_main WHERE (company_id = '$company_id') OR (company_id = 0) AND enabled = 'Yes' order by chart asc") or die(mysql_error());
		while($rowCH = mysql_fetch_assoc($ch)){

			$content .= "<option value='".$rowCH[gchart_main_id]."'>".$rowCH[chart]."</option>";
			
		}
		$sch = mysql_query("SELECT * FROM tbl_gchart_sub WHERE ((branch_id = '$branch_id' AND company_id = '$company_id') OR (branch_id = 0 AND company_id = 0)) AND s_enabled != 'No'") or die(mysql_error());
		while($rowSUBCH = mysql_fetch_assoc($sch)){

			$content .= "<option value='".$rowSUBCH[gchart_sub_id]."'>".$rowSUBCH[s_chart]."</option></optgroup>";
			
		}
		
		return $content;
	}

	function getAllChart_FND($company_id){
		
		$content = "<option value=''>Select Accounts:</option>";
		$ch = mysql_query("SELECT * FROM tbl_gchart_main WHERE (company_id = '$company_id' OR company_id = 0) AND enabled = 'Yes' order by chart asc") or die(mysql_error());
		while($rowCH = mysql_fetch_assoc($ch)){

			$content .= "<option value='".$rowCH[gchart_main_id]."'>".$rowCH[chart]."</option>";
			
		}
		$sch = mysql_query("SELECT * FROM tbl_gchart_sub WHERE (company_id = '$company_id' OR company_id = 0) AND s_enabled != 'No'") or die(mysql_error());
		while($rowSUBCH = mysql_fetch_assoc($sch)){

			$content .= "<option value='".$rowSUBCH[gchart_sub_id]."'>".$rowSUBCH[s_chart]."</option></optgroup>";
			
		}
		
		return $content;
	}

		function getMainChart($company_id, $branch_id){
		
		// $content = "<option value=''>Select Accounts:</option>";
		$ch = mysql_query("SELECT * FROM tbl_gchart_main WHERE enabled = 'Yes' AND visibility_status !='1'  AND  (company_id = '$company_id') OR (company_id = 0)  order by acode asc") or die(mysql_error());
		while($rowCH = mysql_fetch_assoc($ch)){

			$content .= "<option value='".$rowCH[gchart_main_id]."'>".$rowCH[chart]."</option>";
			
		}
		
		return $content;
	}
	function getSubChart($company_id, $branch_id){
		
		$sch = mysql_query("SELECT * FROM tbl_gchart_sub WHERE (( company_id = '$company_id') OR (company_id = 0)) AND s_enabled != 'No' AND visibility_status !='1' order by s_code asc") or die(mysql_error());
		while($rowSUBCH = mysql_fetch_assoc($sch)){

			$content1 .= "<option value='".$rowSUBCH[gchart_sub_id]."'>".$rowSUBCH[s_chart]."</option>";
			
		}
		
		return $content1;
	}

	function getCHart($company, $branch, $chart_id){
		$ch = mysql_query("SELECT * FROM tbl_gchart_main WHERE ((company_id = '$company') OR (company_id = 0)) AND enabled != 'No' AND gchart_main_id = '$chart_id'") or die(mysql_error());
		$rowCH = mysql_fetch_assoc($ch);

		if(!empty($rowCH[chart])){
			$chrt = $rowCH[chart];
		}else{
			$sch = mysql_query("SELECT * FROM tbl_gchart_sub WHERE ((company_id = '$company') OR (company_id = 0)) AND s_enabled != 'No' AND gchart_sub_id = '$chart_id'") or die(mysql_error());
			$rowSUBCH = mysql_fetch_assoc($sch);
			$chrt = $rowSUBCH[s_chart];
		}
		return $chrt;
	}

	function GLEntryDetailChecker(){
		$company_id = $_SESSION["system"]["company_id"];
		$branch_id = get_branch();
		$get_header = mysql_query("select * from tbl_gltran_header where company_id = '$company_id' and branch_id = '$branch_id'") or die(mysql_error());
		while($row = mysql_fetch_assoc($get_header)){
			$gltran_header_id = $row["gltran_header_id"];
			$get_detail = mysql_query("SELECT count(*) as total FROM tbl_gltran_header WHERE tbl_gltran_header.gltran_header_id NOT IN (SELECT gltran_header_id FROM tbl_gltran_detail) and company_id = '$company_id' and branch_id = '$branch_id'") or die(mysql_error());
			$total_detail = mysql_fetch_assoc($get_detail);
		}

		return $total_detail["total"];
	}

	function getPricePerkiloFormulation($f_id, $wh){
		$getForm_Details = mysql_fetch_array(mysql_query("SELECT q1.sub/q2.totalQty AS pricePerkilo FROM (SELECT SUM(fD.form_qty * wc.cost) AS sub FROM tbl_formulation_details_feeds AS fD, tbl_warehouse_product_cost AS wc WHERE fD.material_id = wc.product_id AND fD.formulation_header_id = '$f_id' AND wc.warehouse_id = '$wh' AND (fD.form_type = 'micro' OR fD.form_type = 'macro') ) AS q1, (SELECT SUM(fD.form_qty) AS totalQty FROM tbl_formulation_details_feeds AS fD, tbl_warehouse_product_cost AS wc WHERE fD.material_id = wc.product_id AND fD.formulation_header_id = '$f_id' AND wc.warehouse_id = '$wh' AND (fD.form_type = 'micro' OR fD.form_type = 'macro') ) AS q2"));
		
		return $getForm_Details[pricePerkilo];
	}

	function getPricePerkiloJobOrder($jo_id, $wh){
		$getForm_Details = mysql_fetch_array(mysql_query("SELECT q1.sub/q2.totalQty AS pricePerkilo FROM (SELECT SUM(fD.quantity * wc.cost) AS sub FROM tbl_joborder_details_feeds AS fD, tbl_warehouse_product_cost AS wc WHERE fD.material = wc.product_id AND fD.joborder_header_id = '$jo_id' AND wc.warehouse_id = '$wh') AS q1, (SELECT SUM(fD.quantity) AS totalQty FROM tbl_joborder_details_feeds AS fD, tbl_warehouse_product_cost AS wc WHERE fD.material = wc.product_id AND fD.joborder_header_id = '$jo_id' AND wc.warehouse_id = '$wh') AS q2"));
		
		return $getForm_Details[pricePerkilo];
	}

	function getPricePerkiloSimulation($f_id){
		$getForm_Details = mysql_fetch_array(mysql_query("SELECT q1.sub/q2.totalQty AS pricePerkilo FROM (SELECT SUM(fD.form_qty * wc.cost) AS sub FROM tbl_formulation_details_feeds AS fD, tbl_feeds_simulation_cost AS wc WHERE fD.material_id = wc.product_id AND fD.formulation_header_id = '$f_id') AS q1, (SELECT SUM(fD.form_qty) AS totalQty FROM tbl_formulation_details_feeds AS fD, tbl_feeds_simulation_cost AS wc WHERE fD.material_id = wc.product_id AND fD.formulation_header_id = '$f_id') AS q2"));
		
		return $getForm_Details[pricePerkilo];
	}

	function getPricePerkiloAiFormulation($f_id, $wh){
		$getFormDetails = mysql_query("select * from tbl_ai_formulation_details_pig where formulation_header_id = '$f_id'") or die (mysql_error());
		while ($getRowFormDetails = mysql_fetch_assoc($getFormDetails)) {
			//-- GET WAREHOUSE PRODUCT COST --//
			$getWhCost = getBulkData("cost","tbl_warehouse_product_cost","product_id = '$getRowFormDetails[material_id]' AND warehouse_id = '$wh'");
			$subtotal += ($getRowFormDetails[form_qty] * $getWhCost[cost]);
			$sumQty += $getRowFormDetails[form_qty];
			$total = $subtotal / $sumQty;
		}
		return $total;
	}
	
function insertGLperCompany($company_id){
	$insert_gl_company = mysql_query("SELECT * FROM `tbl_gchart_main` WHERE preset_status = 1");
	while($row = mysql_fetch_assoc($insert_gl_company)){
		mysql_query("INSERT INTO `tbl_gchart_main`(`company_id`, `acode`, `chart`, `classification`, `enabled`) VALUES ('$company_id','$row[acode]','$row[chart]','$row[classification]','$row[enabled]')");
	}
}
	
function insertAssignChartAccount($company_id){
	$insert_coa = mysql_query("SELECT * FROM `tbl_assign_chart_of_account` WHERE preset_status = 1");
	$date = getCurrentDate();
	while($row = mysql_fetch_assoc($insert_coa)){
		
		$chart = mysql_query("SELECT * FROM `tbl_gchart_main` WHERE preset_status = 1 AND gchart_main_id = '$row[chart_id]'");
		$Chartrow = mysql_fetch_assoc($chart);
		
		$companychartid = mysql_fetch_assoc(mysql_query("SELECT * FROM `tbl_gchart_main` WHERE UCASE(chart) = UCASE('$Chartrow[chart]') AND company_id = '$company_id'"));
		
		
		mysql_query("INSERT INTO `tbl_assign_chart_of_account`(`company_id`, `product_category_id`,`chart_id`, `account_type`, `module`, `payment_type`, `date_added`) VALUES ('$company_id','$row[product_category_id]','$companychartid[gchart_main_id]','$row[account_type]','$row[module]','$row[payment_type]','$date')");
	}
	
}

function convertNumber($number)
{
	// USAGE ::
	// $num = 500254.89;
	// $test = convertNumber($num);
	// echo $test;

    list($integer, $fraction) = explode(".", $number);

    $output = "";

    if ($integer{0} == "-")
    {
        $output = "negative ";
        $integer    = ltrim($integer, "-");
    }
    else if ($integer{0} == "+")
    {
        $output = "positive ";
        $integer    = ltrim($integer, "+");
    }

    if ($integer{0} == "0")
    {
        $output .= "zero";
    }
    else
    {
        $integer = str_pad($integer, 36, "0", STR_PAD_LEFT);
        $group   = rtrim(chunk_split($integer, 3, " "), " ");
        $groups  = explode(" ", $group);

        $groups2 = array();
        foreach ($groups as $g)
        {
            $groups2[] = convertThreeDigit($g{0}, $g{1}, $g{2});
        }

        for ($z = 0; $z < count($groups2); $z++)
        {
            if ($groups2[$z] != "")
            {
                $output .= $groups2[$z] . convertGroup(11 - $z) . (
                        $z < 11
                        && !array_search('', array_slice($groups2, $z + 1, -1))
                        && $groups2[11] != ''
                        && $groups[11]{0} == '0'
                            ? " and "
                            : " "
                    );
            }
        }

        $output = rtrim($output, ", ");
    }

    if ($fraction > 0)
    {
        $output .= " pesos and";
        $fraction_digit = strlen($fraction);

        $x = 1;
        while ($x <= $fraction_digit) {
            $frctn = str_split( (string) $fraction);
        	if($x == 1){
        		$d1 = $frctn[0];
        	}

        	if($x == 2){
        		$d2 = $frctn[1];
        	}

        	$x++;
    	}
        $output .= " " . convertTwoDigit($d1, $d2);
        $output .= " centavos";

        // for ($i = 0; $i < strlen($fraction); $i++)
        // {
        // $output .= " " . convertDigit($fraction{$i});
    	// }
    }else{
    	$output .= " pesos";
    }

    return $output;
}

function convertGroup($index)
{
    switch ($index)
    {
        case 11:
            return " decillion";
        case 10:
            return " nonillion";
        case 9:
            return " octillion";
        case 8:
            return " septillion";
        case 7:
            return " sextillion";
        case 6:
            return " quintrillion";
        case 5:
            return " quadrillion";
        case 4:
            return " trillion";
        case 3:
            return " billion";
        case 2:
            return " million";
        case 1:
            return " thousand";
        case 0:
            return "";
    }
}

function convertThreeDigit($digit1, $digit2, $digit3)
{
    $buffer = "";

    if ($digit1 == "0" && $digit2 == "0" && $digit3 == "0")
    {
        return "";
    }

    if ($digit1 != "0")
    {
        $buffer .= convertDigit($digit1) . " hundred";
        if ($digit2 != "0" || $digit3 != "0")
        {
            $buffer .= " and ";
        }
    }

    if ($digit2 != "0")
    {
        $buffer .= convertTwoDigit($digit2, $digit3);
    }
    else if ($digit3 != "0")
    {
        $buffer .= convertDigit($digit3);
    }

    return $buffer;
}

function convertTwoDigit($digit1, $digit2)
{
    if ($digit2 == "0")
    {
        switch ($digit1)
        {
            case "1":
                return "ten";
            case "2":
                return "twenty";
            case "3":
                return "thirty";
            case "4":
                return "forty";
            case "5":
                return "fifty";
            case "6":
                return "sixty";
            case "7":
                return "seventy";
            case "8":
                return "eighty";
            case "9":
                return "ninety";
        }
    } else if ($digit1 == "1")
    {
        switch ($digit2)
        {
            case "1":
                return "eleven";
            case "2":
                return "twelve";
            case "3":
                return "thirteen";
            case "4":
                return "fourteen";
            case "5":
                return "fifteen";
            case "6":
                return "sixteen";
            case "7":
                return "seventeen";
            case "8":
                return "eighteen";
            case "9":
                return "nineteen";
        }
    } else
    {
        $temp = convertDigit($digit2);
        switch ($digit1)
        {
            case "2":
                return "twenty $temp";
            case "3":
                return "thirty $temp";
            case "4":
                return "forty $temp";
            case "5":
                return "fifty $temp";
            case "6":
                return "sixty $temp";
            case "7":
                return "seventy $temp";
            case "8":
                return "eighty $temp";
            case "9":
                return "ninety $temp";
        }
    }
}

function convertDigit($digit)
{
    switch ($digit)
    {
        case "0":
            return "zero";
        case "1":
            return "one";
        case "2":
            return "two";
        case "3":
            return "three";
        case "4":
            return "four";
        case "5":
            return "five";
        case "6":
            return "six";
        case "7":
            return "seven";
        case "8":
            return "eight";
        case "9":
            return "nine";
    }
}

function postGLBB($bb_id, $id, $datePosted, $glReference = NULL, $crossRef = NULL, $sub_id = NULL){
	$company_id = $_SESSION["system"]["company_id"];
	$branch_id = get_branch();
	$date = getCurrentDate();

	$getJcode = getBulkData("*","tbl_journal","journal_code = 'BB'");
	
	if(!empty($sub_id)){
		$g_chart = $sub_id;
	}else{
		$g_chart = $id;
	}

	//-- get Chart Classification --//
	$getChart = getBulkData("classification","tbl_gchart_main","gchart_main_id = '$id'");
	$getChartClass = getBulkData("expense_code","tbl_expense_category","expense_id = '$getChart[classification]'");

	$dec_status = getBulkData("declared_status","tbl_beginning_balance","begin_bal_id = '$bb_id'");

	//-- UPDATE STATUS TO "F" IN BEGINNING BALANCE TABLE --//
	$result = mysql_query("UPDATE tbl_beginning_balance SET status = 'F', posted_date = '$datePosted' WHERE begin_bal_id = '$bb_id' AND company_id = '$company_id' AND branch_id = '$branch_id'") or die(mysql_error());

	//-- INSERT GLTRAN HEADER --//
	$header = mysql_query("INSERT INTO tbl_gltran_header SET company_id = '$company_id', branch_id = '$branch_id', general_reference = '$glReference', cross_reference = '$crossRef', gltran_date = '$datePosted', journal_id = '$getJcode[journal_id]', status='S', dec_status='$dec_status[declared_status]'") or die(mysql_error());
	$last_id = mysql_insert_id();

	//-- get amount --//
	$getBegBalStatus = getBulkData("status","tbl_beginning_balance","begin_bal_id = '$bb_id' AND gchart_main_id='$id' AND company_id ='$company_id' AND branch_id = '$branch_id'");
	$getBeginningDR = getBulkData("SUM(dr) AS drTotal","tbl_beginning_balance","begin_bal_id = '$bb_id' AND gchart_main_id='$id' AND company_id ='$company_id' AND branch_id = '$branch_id'");
	$getBeginningCR = getBulkData("SUM(cr) AS crTotal","tbl_beginning_balance","begin_bal_id = '$bb_id' AND gchart_main_id='$id' AND company_id ='$company_id' AND branch_id = '$branch_id'");
	$getBeginningQTY = getBulkData("SUM((qty + excess) * cost) AS bal","tbl_beginning_balance","begin_bal_id = '$bb_id' AND gchart_main_id='$id' AND company_id ='$company_id' AND branch_id = '$branch_id'");
	
	
	$CheckIfentryis1 = getBulkData("COUNT(*) AS count","tbl_beginning_balance","begin_bal_id = '$bb_id' AND gchart_main_id='$id' AND company_id ='$company_id' AND branch_id = '$branch_id'");

	if($getBeginningQTY[bal] < 1){
		if($getChartClass[expense_code] == 'CA' || $getChartClass[expense_code] == 'NCA' || $getChartClass[expense_code] == 'CE' || $getChartClass[expense_code] == 'OE'){
			$SubtotalBeginningBalance = $getBeginningDR[drTotal] - $getBeginningCR[crTotal];
		}else{
			$SubtotalBeginningBalance = $getBeginningCR[crTotal] - $getBeginningDR[drTotal];
		}

		if($CheckIfentryis1[count] > 1){
			$totalBeginningBalance = $SubtotalBeginningBalance;
		}else{
			$totalBeginningBalance = abs($SubtotalBeginningBalance);
		}
	}else{
		$totalBeginningBalance = $getBeginningQTY[bal];
	}

	//-- Update Product warehouse Cost --//
	$getBegBal = getBulkData("*","tbl_beginning_balance","begin_bal_id = '$bb_id' AND gchart_main_id='$id' AND company_id ='$company_id' AND branch_id = '$branch_id'");

	//-- get package type --//
	$getpKG = getBulkData("qty","tbl_package","package_id = '$getBegBal[package_id]'");
	$getpKGtype = $getpKG[qty];
		
	//COST DIVIDE BY PACKAGING :: to get the cost per kilo
	$costPerKilo = 0;
	if($getpKGtype > 0){
		$costPerKilo = $getBegBal[cost] / $getpKGtype;
	}

	$getFinProdCost = getBulkData("cost","tbl_warehouse_product_cost","product_id = '$getBegBal[product_id]' AND company_id ='$company_id' AND branch_id = '$branch_id' AND warehouse_id = '$getBegBal[warehouse_id]'");
	if(empty($getFinProdCost[cost])){
		$insertCost = mysql_query("INSERT INTO tbl_warehouse_product_cost SET cost = '$costPerKilo', product_id = '$getBegBal[product_id]', company_id ='$getBegBal[company_id]', branch_id = '$getBegBal[branch_id]', warehouse_id = '$getBegBal[warehouse_id]'") or die(mysql_error());
	}else{
		$updateCost = mysql_query("UPDATE tbl_warehouse_product_cost SET cost = '$costPerKilo' WHERE product_id = '$getBegBal[product_id]' AND company_id ='$getBegBal[company_id]' AND branch_id = '$getBegBal[branch_id]' AND warehouse_id = '$getBegBal[warehouse_id]'") or die(mysql_error());
	}

	//-- GET RETAINED EARNINGS --//
	$getChartRE = getBulkData("gchart_main_id","tbl_gchart_main","UCASE(chart) = 'RETAINED EARNINGS' AND company_id = '$company_id'");

	//-- INSERT GLTRAN DETAILS --//
	if($getChartClass[expense_code] == 'CA' || $getChartClass[expense_code] == 'NCA' || $getChartClass[expense_code] == 'CE' || $getChartClass[expense_code] == 'OE'){
		$det = mysql_query("INSERT INTO tbl_gltran_detail SET gltran_header_id = '$last_id', gchart_id = '$g_chart', debit = '$totalBeginningBalance', credit = '0'") or die(mysql_error());
		$detCred = mysql_query("INSERT INTO tbl_gltran_detail SET gltran_header_id = '$last_id', gchart_id = '$getChartRE[gchart_main_id]', debit = '0', credit = '$totalBeginningBalance'") or die(mysql_error());
	}else{
		$detCred = mysql_query("INSERT INTO tbl_gltran_detail SET gltran_header_id = '$last_id', gchart_id = '$g_chart', debit = '0', credit = '$totalBeginningBalance'") or die(mysql_error());
		$det = mysql_query("INSERT INTO tbl_gltran_detail SET gltran_header_id = '$last_id', gchart_id = '$getChartRE[gchart_main_id]', debit = '$totalBeginningBalance', credit = '0'") or die(mysql_error());
	}
}


function getProductCode($product_id){
	$company_id = $_SESSION["system"]["company_id"];
	
	$product_code = mysql_fetch_array(mysql_query("SELECT product_code from tbl_productmaster where product_id = '$product_id'"));
	
	return $product_code[0];
	
}


function getMedVacSchedId($med_and_vac_sched_id){

	$med_and_vac_sched_id = $_POST['med_and_vac_sched_id'];

	$product_id = mysql_fetch_array(mysql_query("SELECT med_and_vac_sched_id FROM tbl_med_and_vac_sched_eggs WHERE med_and_vac_sched_id = '$med_and_vac_sched_id'"));

	return $med_and_vac_sched_id[0];
}

function insert_med_vacc_sched($swine_id,$swine_birthdate,$pen_id,$swine_type_info,$branch_id){
	$company_id = $_SESSION["system"]["company_id"];
	delete_med_vacc_sched($swine_id);

	if($swine_type_info == 'B' || $swine_type_info == 'W' || $swine_type_info == 'F' || $swine_type_info == 'B1'){
		$add_query = "and sow_status='".$swine_type_info."'";
	}else{
		$add_query = "and swine_type='".$swine_type_info."'";
	}
	$count=mysql_fetch_array(mysql_query("SELECT count(*) FROM tbl_med_and_vacc_standard_pig where company_id='$company_id' and branch_id='$branch_id' $add_query"));
	if ($count[0] <= 0 ){
		
	}else{

	$fetch_medvacc_standard=mysql_query("SELECT * FROM tbl_med_and_vacc_standard_pig where company_id='$company_id' and branch_id='$branch_id' $add_query");
		while ($row=mysql_fetch_array($fetch_medvacc_standard)){
			$add_med_vacc_id=$row['add_med_vacc_id'];
			$swine_age=$row['swine_age'];
			$st=$row['swine_type'];

			$dob=$swine_birthdate;

			$date=strtotime($dob);
			$date1 = strtotime("+".$swine_age." day", $date);
			$sched_date=date('Y-m-d', $date1);

			if($st==1){
				mysql_query("INSERT INTO `tbl_med_vacc_sched_pig` (`swine_id`,`add_med_vacc_id`,`company_id`,`branch_id`,`status`,`pen_code`,`dob`,`sched`,`sched_date`) VALUES('$swine_id','$add_med_vacc_id','$company_id','$branch_id','0','$pen_id','$dob','0','$sched_date')");
			}else{
				$count_sched = mysql_fetch_array(mysql_query("SELECT count(*) FROM tbl_med_vacc_sched_pig where company_id='$company_id'  and swine_id='$swine_id' and add_med_vacc_id='$add_med_vacc_id'"));

				if($count_sched[0]==0){
					mysql_query("INSERT INTO `tbl_med_vacc_sched_pig` (`swine_id`,`add_med_vacc_id`,`company_id`,`branch_id`,`status`,`pen_code`,`dob`,`sched`,`sched_date`) VALUES('$swine_id','$add_med_vacc_id','$company_id','$branch_id','0','$pen_id','$dob','0','$sched_date')");
				}
			}

			
		}
	}
}

// function insert_med_vacc_sched_sow($swine_id,$swine_birthdate,$pen_id,$sow_type){
// 	$company_id = $_SESSION["system"]["company_id"];
// 	$branch_id = get_branch();
// 	$count=mysql_fetch_array(mysql_query("SELECT count(*) FROM tbl_med_and_vacc_standard_pig where company_id='$company_id' and branch_id='$branch_id' and sow_status='$sow_type'"));
// 	if ($count[0] <= 0 ){
// 	}else{
// 	$fetch_medvacc_standard=mysql_query("SELECT * FROM tbl_med_and_vacc_standard_pig where company_id='$company_id' and branch_id='$branch_id' and sow_status='$sow_type'");
// 		while ($row=mysql_fetch_array($fetch_medvacc_standard)){
// 			$add_med_vacc_id=$row['add_med_vacc_id'];
// 			$swine_age=$row['swine_age'];
// 			$dob=$swine_birthdate;

// 			$date=strtotime($dob);
// 			$date1 = strtotime("+".$swine_age." day", $date);
// 			$sched_date=date('Y-m-d', $date1);

// 			mysql_query("INSERT INTO `tbl_med_vacc_sched_pig` (`swine_id`,`add_med_vacc_id`,`company_id`,`branch_id`,`status`,`pen_code`,`dob`,`sched`,`sched_date`) VALUES('$swine_id','$add_med_vacc_id','$company_id','$branch_id','0','$pen_id','$dob','0','$sched_date')");
// 		}
// 	}
// }

// function insert_med_vacc_sched_sow_transfer($swine_id,$swine_birthdate,$pen_id,$sow_type,$branch_id){
// 	$company_id = $_SESSION["system"]["company_id"];
// 	$count=mysql_fetch_array(mysql_query("SELECT count(*) FROM tbl_med_and_vacc_standard_pig where company_id='$company_id' and branch_id='$branch_id' and sow_status='$sow_type'"));
// 	if ($count[0] <= 0 ){
// 		delete_med_vacc_sched($swine_id);
// 	}else{
// 		$fetch_medvacc_standard=mysql_query("SELECT * FROM tbl_med_and_vacc_standard_pig where company_id='$company_id' and branch_id='$branch_id' and sow_status='$sow_type'");
// 		while ($row=mysql_fetch_array($fetch_medvacc_standard)){
// 			$add_med_vacc_id=$row['add_med_vacc_id'];
// 			$swine_age=$row['swine_age'];
// 			$dob=$swine_birthdate;

// 			$date=strtotime($dob);
// 			$date1 = strtotime("+".$swine_age." day", $date);
// 			$sched_date=date('Y-m-d', $date1);

			
// 			mysql_query("INSERT INTO `tbl_med_vacc_sched_pig` (`swine_id`,`add_med_vacc_id`,`company_id`,`branch_id`,`status`,`pen_code`,`dob`,`sched`,`sched_date`) VALUES('$swine_id','$add_med_vacc_id','$company_id','$branch_id','0','$pen_id','$dob','0','$sched_date')");

// 		}
// 	}
	
// }
// function insert_med_vacc_sched_transfer($swine_id,$swine_birthdate,$pen_id,$swine_type_info,$branch_id){
// 	$company_id = $_SESSION["system"]["company_id"];
	
// 	$count=mysql_fetch_array(mysql_query("SELECT count(*) FROM tbl_med_and_vacc_standard_pig where company_id='$company_id' and branch_id='$branch_id' and swine_type='$swine_type_info'"));
// 	if ($count[0] <= 0 ){
// 		delete_med_vacc_sched($swine_id);
// 	}else{
// 	$fetch_medvacc_standard=mysql_query("SELECT * FROM tbl_med_and_vacc_standard_pig where company_id='$company_id' and branch_id='$branch_id' and swine_type='$swine_type_info'");
// 		while ($row=mysql_fetch_array($fetch_medvacc_standard)){
// 			$add_med_vacc_id=$row['add_med_vacc_id'];
// 			$swine_age=$row['swine_age'];
// 			$dob=$swine_birthdate;

// 			$date=strtotime($dob);
// 			$date1 = strtotime("+".$swine_age." day", $date);
// 			$sched_date=date('Y-m-d', $date1);

// 			$count_sched = mysql_fetch_array(mysql_query("SELECT count(*) FROM tbl_med_vacc_sched_pig where company_id='$company_id'  and swine_id='$swine_id'  and add_med_vacc_id='$add_med_vacc_id'"));

// 			if($count_sched[0]==0){

// 				mysql_query("INSERT INTO `tbl_med_vacc_sched_pig` (`swine_id`,`add_med_vacc_id`,`company_id`,`branch_id`,`status`,`pen_code`,`dob`,`sched`,`sched_date`) VALUES('$swine_id','$add_med_vacc_id','$company_id','$branch_id','0','$pen_id','$dob','0','$sched_date')");
// 			}
// 		}
// 	}
// }

// EGGS GROWING ENTRY COUNTER
function batchEntryCounterBrooding($growing_id){
	$company_id = $_SESSION["system"]["company_id"];

	$count_feeds = mysql_fetch_array(mysql_query("SELECT COUNT(feeds_entry_id) FROM `tbl_feeds_entry_eggs` WHERE growing_id='$growing_id' AND company_id='$company_id'"));

		$count_med_vac = mysql_fetch_array(mysql_query("SELECT COUNT(medication_entry_id) FROM `tbl_medication_and_vaccination_entry_eggs` WHERE growing_id='$growing_id' AND company_id='$company_id'"));

		$count_depopulate = mysql_fetch_array(mysql_query("SELECT COUNT(depopulate_entry_id) FROM `tbl_depopulate_entry_eggs` WHERE growing_id='$growing_id' AND company_id='$company_id'"));

		$count_mortality = mysql_fetch_array(mysql_query("SELECT COUNT(mortality_entry_id) FROM `tbl_mortality_entry_eggs` WHERE growing_id='$growing_id' AND company_id='$company_id'"));

		$count_body_weight = mysql_fetch_array(mysql_query("SELECT COUNT(body_weight_entry_id) FROM `tbl_body_weight_entry_eggs` WHERE growing_id='$growing_id' AND company_id='$company_id'"));

		$count_water = mysql_fetch_array(mysql_query("SELECT COUNT(water_entry_id) FROM `tbl_water_entry_eggs` WHERE growing_id='$growing_id' AND company_id='$company_id'"));

		$count_entries = $count_feeds[0] + $count_med_vac[0] + $count_depopulate[0] + $count_mortality[0] + $count_body_weight[0] + $count_water[0];

		return $count_entries;
}

function batchEntryCounterProduction($production_id){
	$company_id = $_SESSION["system"]["company_id"];

	$count_feeds = mysql_fetch_array(mysql_query("SELECT COUNT(feeds_entry_id) FROM `tbl_feeds_entry_production_eggs` WHERE production_id='$production_id' AND company_id='$company_id'"));

		$count_med_vac = mysql_fetch_array(mysql_query("SELECT COUNT(medication_entry_id) FROM `tbl_medication_and_vaccination_entry_production_eggs` WHERE production_id='$production_id' AND company_id='$company_id'"));

		$count_depopulate = mysql_fetch_array(mysql_query("SELECT COUNT(depopulate_entry_id) FROM `tbl_depopulate_entry_production_eggs` WHERE production_id='$production_id' AND company_id='$company_id'"));

		$count_mortality = mysql_fetch_array(mysql_query("SELECT COUNT(mortality_entry_id) FROM `tbl_mortality_entry_production_eggs` WHERE production_id='$production_id' AND company_id='$company_id'"));

		$count_inventory = mysql_fetch_array(mysql_query("SELECT COUNT(inventory_entry_id) FROM `tbl_inventory_entry_production_eggs` WHERE production_id='$production_id' AND company_id='$company_id'"));

		$count_body_weight = mysql_fetch_array(mysql_query("SELECT COUNT(body_weight_entry_id) FROM `tbl_body_weight_entry_production_eggs` WHERE production_id='$production_id' AND company_id='$company_id'"));

		$count_water = mysql_fetch_array(mysql_query("SELECT COUNT(water_entry_id) FROM `tbl_water_entry_production_eggs` WHERE production_id='$production_id' AND company_id='$company_id'"));

		$count_entries = $count_feeds[0] + $count_med_vac[0] + $count_depopulate[0] + $count_mortality[0] + $count_inventory[0] + $count_body_weight[0] + $count_water[0];

		return $count_entries;
}

function batchEntryCounterBrooding_broiler($growing_id){
	$company_id = $_SESSION["system"]["company_id"];

	$count_feeds = mysql_fetch_array(mysql_query("SELECT COUNT(feeds_entry_id) FROM `tbl_feeds_entry_broiler` WHERE growing_id='$growing_id' AND company_id='$company_id'"));

		$count_med_vac = mysql_fetch_array(mysql_query("SELECT COUNT(medication_entry_id) FROM `tbl_medication_and_vaccination_entry_broiler` WHERE growing_id='$growing_id' AND company_id='$company_id'"));

		$count_depopulate = mysql_fetch_array(mysql_query("SELECT COUNT(depopulate_entry_id) FROM `tbl_depopulate_entry_broiler` WHERE growing_id='$growing_id' AND company_id='$company_id'"));

		$count_mortality = mysql_fetch_array(mysql_query("SELECT COUNT(mortality_entry_id) FROM `tbl_mortality_entry_broiler` WHERE growing_id='$growing_id' AND company_id='$company_id'"));

		$count_body_weight = mysql_fetch_array(mysql_query("SELECT COUNT(body_weight_entry_id) FROM `tbl_body_weight_entry_broiler` WHERE growing_id='$growing_id' AND company_id='$company_id'"));

		$count_water = mysql_fetch_array(mysql_query("SELECT COUNT(water_entry_id) FROM `tbl_water_entry_broiler` WHERE growing_id='$growing_id' AND company_id='$company_id'"));

		$count_entries = $count_feeds[0] + $count_med_vac[0] + $count_depopulate[0] + $count_mortality[0] + $count_body_weight[0] + $count_water[0];

		return $count_entries;
}

function batchEntryCounterProduction_broiler($production_id){
	$company_id = $_SESSION["system"]["company_id"];

	$count_feeds = mysql_fetch_array(mysql_query("SELECT COUNT(feeds_entry_id) FROM `tbl_feeds_entry_production_broiler` WHERE production_id='$production_id' AND company_id='$company_id'"));

		$count_med_vac = mysql_fetch_array(mysql_query("SELECT COUNT(medication_entry_id) FROM `tbl_medication_and_vaccination_entry_production_broiler` WHERE production_id='$production_id' AND company_id='$company_id'"));

		$count_depopulate = mysql_fetch_array(mysql_query("SELECT COUNT(depopulate_entry_id) FROM `tbl_depopulate_entry_production_broiler` WHERE production_id='$production_id' AND company_id='$company_id'"));

		$count_mortality = mysql_fetch_array(mysql_query("SELECT COUNT(mortality_entry_id) FROM `tbl_mortality_entry_production_broiler` WHERE production_id='$production_id' AND company_id='$company_id'"));

		$count_inventory = mysql_fetch_array(mysql_query("SELECT COUNT(inventory_entry_id) FROM `tbl_inventory_entry_production_broiler` WHERE production_id='$production_id' AND company_id='$company_id'"));

		$count_body_weight = mysql_fetch_array(mysql_query("SELECT COUNT(body_weight_entry_id) FROM `tbl_body_weight_entry_production_broiler` WHERE production_id='$production_id' AND company_id='$company_id'"));

		$count_water = mysql_fetch_array(mysql_query("SELECT COUNT(water_entry_id) FROM `tbl_water_entry_production_broiler` WHERE production_id='$production_id' AND company_id='$company_id'"));

		$count_entries = $count_feeds[0] + $count_med_vac[0] + $count_depopulate[0] + $count_mortality[0] + $count_inventory[0] + $count_body_weight[0] + $count_water[0];

		return $count_entries;
}


function delete_med_vacc_sched($swine_id){
	$company_id = $_SESSION["system"]["company_id"];
	$branch_id = get_branch();
	mysql_query("DELETE from tbl_med_vacc_sched_pig where company_id ='$company_id' and swine_id='$swine_id' and status='0'") or die(mysql_error());
}


//insert swine History
function insert_swine_history($swine_id,$remarks){
	$company_id = $_SESSION["system"]["company_id"];
	$user_id = $_SESSION['system']['userid'];
	$branch_id = get_branch();
	$date=getCurrentDate();
	
	mysql_query("INSERT into tbl_location_history_pig set from_pen = '0', to_pen = '0', sow_id = '$swine_id', date_transfer = '$date', remarks = '$remarks', company_id = '$company_id', branch_id = '$branch_id',user_id='$user_id'");
}


/* backup the db OR just a table */
function backup_tables($host,$user,$pass,$name,$tables = '*'){
	//usage : backup_tables('host','user','pass','dbName');
	
	$link = mysql_connect($host,$user,$pass);
	mysql_select_db($name,$link);
	
	//get all of the tables
	if($tables == '*')
	{
		$tables = array();
		$result = mysql_query('SHOW TABLES');
		while($row = mysql_fetch_row($result))
		{
			$tables[] = $row[0];
		}
	}
	else
	{
		$tables = is_array($tables) ? $tables : explode(',',$tables);
	}
	
	//cycle through
	foreach($tables as $table)
	{
		$result = mysql_query('SELECT * FROM '.$table);
		$num_fields = mysql_num_fields($result);
		
		$return.= 'DROP TABLE '.$table.';';
		$row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
		$return.= "\n\n".$row2[1].";\n\n";
		
		for ($i = 0; $i < $num_fields; $i++) 
		{
			while($row = mysql_fetch_row($result))
			{
				$return.= 'INSERT INTO '.$table.' VALUES(';
				for($j=0; $j < $num_fields; $j++) 
				{
					$row[$j] = addslashes($row[$j]);
					$row[$j] = ereg_replace("\n","\\n",$row[$j]);
					if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
					if ($j < ($num_fields-1)) { $return.= ','; }
				}
				$return.= ");\n";
			}
		}
		$return.="\n\n\n";
	}

	$dateN = date("mdY-His",strtotime(getCurrentDate()));
	$time = "BCKUP-".$dateN;

	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=".$time."-".$name.".sql");
	header("Pragma: no-cache");
	header("Expires: 0");
	
	echo $return;
	//save file
	// $handle = fopen('db-backup-'.time().'-'.(md5(implode(',',$tables))).'.sql','w+');
	// fwrite($handle,$return);
	// fclose($handle);
}

function getJOQty($start_date,$end_date,$warehouse,$b_id,$company_id,$stock_id)
{
	$result = mysql_query("select sum(jd.quantity * jh.num_of_batches) as total  from tbl_joborder_header_feeds as jh, tbl_joborder_details_feeds as jd where jh.joborder_header_id = jd.joborder_header_id and jh.jobdate between '$start_date' and '$end_date' and jh.status != 'C' and jh.warehouse_id = '$warehouse' and jh.branch_id = '$b_id' and jh.company_id = '$company_id' and jd.material = '$stock_id' ") or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["total"];
}

function getPOPendingQty($start_date,$company_id,$branch_id,$stock_id,$end_date, $warehouse)
{

	// if($end_date != 0)
	// {
	// 	// joborder
	// 	$dr = " and h.date between '$start_date' and '$end_date' ";
	// 	$rr = " and h.date_added between '$start_date' and '$end_date' ";
	// 	$pr = " and prH.date between '$start_date' and '$end_date' ";
	// }else{
	// 	// formulation
	// 	$dr = " and h.date between '0000-00-00' and '$start_date' ";
	// 	$rr = " and h.date_added between '0000-00-00' and '$start_date' ";
	// 	$pr = " and prH.date between '0000-00-00' and '$start_date' ";
	// }

	$fetchPOdetails = mysql_query("SELECT h.`po_number`,d.po_detail_id AS po_detail_id,d.product_id,d.actual_qty,d.rs_detail_id,d.supplier_price AS supplier_price FROM tbl_po_details AS d, tbl_po_header AS h WHERE h.`po_number` = d.`po_number` AND h.company_id = '$company_id' AND h.branch_id = '$branch_id' AND d.product_id = '$stock_id' and d.status = 'F' AND h.`status` != 'C'") or die(mysql_error());
	while($dRow = mysql_fetch_array($fetchPOdetails)){
		$received_qty = mysql_fetch_array(mysql_query("SELECT sum(actual_qty) FROM tbl_rr_details WHERE po_detail_id='$dRow[po_detail_id]' and product_id = '$stock_id' and status='F'"));
		$unreceived_qty = $dRow['actual_qty']-$received_qty[0];
		$total_pending += $unreceived_qty;
	}
	return $total_pending;
}

function poPendingQty($start_date,$company_id,$branch_id,$stock_id,$end_date,$poNumber){
	if($end_date != 0)
	{
		// joborder
		$dr = " and date between '$start_date' and '$end_date' ";
		$rr = " and date_added between '$start_date' and '$end_date' ";
	}else{
		// formulation
		$dr = " and date <= '$start_date' ";
		$rr = " and date_added <= '$start_date' ";
	}

	$fetchRR = mysql_query("SELECT receiving_number from tbl_rr_header where po_number = '$poNumber' and status = 'F' and company_id = '$company_id' and branch_id = '$branch_id'");
	$total_rr = 0;
	while($rrRow = mysql_fetch_array($fetchRR)){
		$sumTotalRR = mysql_fetch_array(mysql_query("SELECT sum(actual_qty) from tbl_rr_details where receiving_number='$rrRow[0]' $rr and status='F' and product_id = '$stock_id'"));
		$total_rr  += $sumTotalRR[0];
	}
	
	$sumTotalPO = mysql_fetch_array(mysql_query("SELECT sum(actual_qty) from tbl_po_details where po_number = '$poNumber' and product_id = '$stock_id'"));

	$total_ = $sumTotalPO[0]-$total_rr;
	return $total_;
}


function getFormDetailQty($f_id)
{
	$result = mysql_query("select sum(form_qty) as total from tbl_formulation_details_feeds where formulation_header_id = '$f_id'") or die(mysql_error());

	$row = mysql_fetch_assoc($result);

	return $row["total"];
}

//PROJECTED BASED BY RAWMATERIALS
function getFormulationQty($company_id,$material_id,$b_id,$wh)
{
	$get_set_Product_ = mysql_query("SELECT * FROM tbl_formulation_details_feeds AS fd, tbl_formulation_header_feeds AS fh WHERE fh.company_id = '$company_id' AND fh.formulation_header_id = fd.formulation_header_id AND fd.material_id = '$material_id' AND fh.isDefault = 1 AND fh.warehouse_id = '$wh'");

	$a = array();
	$b = array();
	while ($row = mysql_fetch_array($get_set_Product_))
	{

		$getRowProjQTY = mysql_fetch_array(mysql_query("SELECT quantity FROM tbl_fp_projection_feeds WHERE company_id = '$company_id' AND product_id = '$row[finish_stock_id]'"));
		//while ($getRowProjQTY = mysql_fetch_array($getFormulation)) 
		//{
			$formulation_id =  $row[formulation_header_id];
			$fp_qty = $getRowProjQTY[quantity]; //REQUIRED QTY
			$formula_qty = getFormDetailQty($formulation_id);

			$qty_in_kg = 50 * $fp_qty;
		
			if($formula_qty != 0){
				$qty_divide = $qty_in_kg / $formula_qty;
				$sum_qty = $qty_in_kg / $formula_qty;
			}

			$a[] =  $qty_in_kg . " / " . $formula_qty . " * " . $row["form_qty"] . " = " .(($sum_qty) * $row["form_qty"]);
			$b[] = $qty_divide * $row[form_qty];
		//}

			//return getProdName($row[finish_stock_id]).": ".$row["form_qty"];
	}

	//foreach($a as $val) {	
	 	return array_sum($b);
	 	//return $val;
	//}
}


//PROJECTED BASED BY MUCH NEEDED
function getFormulationQty_MN($company_id,$material_id,$b_id,$wh)
{
	$get_set_Product_ = mysql_query("SELECT * FROM tbl_formulation_details_feeds AS fd, tbl_formulation_header_feeds AS fh WHERE fh.company_id = '$company_id' AND fh.formulation_header_id = fd.formulation_header_id AND fd.material_id = '$material_id' AND fh.isDefault = 1 AND fh.warehouse_id = '$wh'");

	$a = array();
	$b = array();
	while ($row = mysql_fetch_array($get_set_Product_))
	{

		$getRowProjQTY = mysql_fetch_array(mysql_query("SELECT quantity FROM tbl_fp_projection_mn WHERE company_id = '$company_id' AND product_id = '$row[finish_stock_id]' AND branch_id = '$b_id'"));
		//while ($getRowProjQTY = mysql_fetch_array($getFormulation)) 
		//{
			$formulation_id =  $row[formulation_header_id];
			$fp_qty = $getRowProjQTY[quantity]; //REQUIRED QTY
			$formula_qty = getFormDetailQty($formulation_id);

			$qty_in_kg = 50 * $fp_qty;
		
			if($formula_qty != 0){
				$qty_divide = $qty_in_kg / $formula_qty;
				$sum_qty = $qty_in_kg / $formula_qty;
			}

			$a[] =  $qty_in_kg . " / " . $formula_qty . " * " . $row["form_qty"] . " = " .(($sum_qty) * $row["form_qty"]);
			$b[] = $qty_divide * $row[form_qty];
		//}

			//return getProdName($row[finish_stock_id]).": ".$row["form_qty"];
	}

	//foreach($a as $val) {	
	 	return array_sum($b);
	 	//return $val;
	//}
}

//PROJECTED BASED BY PACKAGING

function getFormulationQtyPKG($company_id,$material_id,$b_id,$wh){
	$Get_set_PKG = mysql_query("SELECT * FROM `tbl_formulation_header_feeds` WHERE company_id = '$company_id' AND branch_id = '$b_id' AND `packaging_material_id` = '$material_id' AND isDefault = '1' AND warehouse_id = '$wh'");

	while ($row = mysql_fetch_array($Get_set_PKG)) {
		$getFPJ = mysql_fetch_array(mysql_query("SELECT * FROM tbl_fp_projection_feeds WHERE company_id = '$company_id' AND product_id = '$row[finish_stock_id]'"));
		$FPJqty = $getFPJ['quantity'];
	}

	return $FPJqty;

}

function getDebitCredit($gltran_header_id,$company_id,$branch_id){
	$fetch_value = mysql_fetch_array(mysql_query("SELECT sum(debit) as total_debit, sum(credit) as total_credit FROM tbl_gltran_detail WHERE gltran_header_id = '$gltran_header_id'"));

	if(!empty($fetch_value['total_debit'])){
		$amount = $fetch_value['total_debit'];
	}

	if(!empty($fetch_value['total_credit'])){
		$amount = $fetch_value['total_credit'];
	}

	return $amount;

}

function getDRInvoice($delivery_number){
	if(substr($delivery_number,0,2) == "AT"){
		$getInvoice = $delivery_number;
	}else{
		$get_invoice = mysql_fetch_array(mysql_query("SELECT invoice_no FROM tbl_dr_header WHERE delivery_number='$delivery_number'"));
		$getInvoice = $get_invoice[0];
	}
	
	return $getInvoice;
}

function getDRCR($pm_number){
	$getCR = mysql_fetch_array(mysql_query("SELECT receipt_id,provisionary_receipt,receipt_type FROM tbl_payment WHERE payment_number='$pm_number'"));
	
	if($getCR['receipt_type'] == "C"){
		$ref_num = $getCR['receipt_id'];
	}else{
		$ref_num = $getCR['provisionary_receipt'];
	}
	
	return $ref_num;
}

function getDRInAdvance($pm_number){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
	
	$checkAdv = mysql_query("SELECT * FROM tbl_advance_payment WHERE transaction_ref='$pm_number' AND company_id='$company_id' AND branch_id='$branch_id'");
	if(mysql_num_rows($checkAdv) > 0){
		$fetchDRNum = mysql_query("SELECT delivery_number FROM tbl_payment_details WHERE payment_number='$pm_number' GROUP BY delivery_number");
		$dr_number = "";
		while($rowDR = mysql_fetch_array($fetchDRNum)){
			$dr_number .= $rowDR[0].", ";
		}
		
		$drNum = substr($dr_number, 0, -2);
	}else{
		$drNum = "";
	}
	
	return $drNum;
	
}

function aiSemenChecker($product_id){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$fetchWPC = mysql_fetch_array(mysql_query("SELECT count(warehouse_product_cost_id) FROM tbl_warehouse_product_cost WHERE product_id='$product_id' AND branch_id='$branch_id' AND visibility_status = '0'"));
	if($fetchWPC[0] > 0){
		$stat = 0;
	}else{
		$stat = 1;
	}

	mysql_query("UPDATE tbl_productmaster SET hide_unhide_status = '$stat' WHERE product_id='$product_id' AND branch_id='$branch_id'");
}

function ARBalance($dr_number,$customer_id){
	$company_id = $_SESSION['system']['company_id'];
	
	$account_id = "C-".$customer_id;
	$ref_number = SUBSTR($dr_number, 0, 2);
	
	
	if($ref_number == "BB"){
		$getgchartMainId = getBulkData("gchart_main_id","tbl_gchart_main","company_id = '$company_id' AND chart = 'Accounts Receivable'");
		$sum_bb_amount = mysql_fetch_array(mysql_query("SELECT sum(dr) from tbl_beginning_balance where bbnum='$dr_number' and company_id = '$company_id' and gchart_main_id = '$getgchartMainId[gchart_main_id]' and status = 'F' and account_id = '$account_id'"));
		$total_debit = $sum_bb_amount[0];
		
	}else if($ref_number == "AT"){
		$sum_at_amount = mysql_fetch_array(mysql_query("SELECT amount FROM `tbl_accounts_transfer` where to_account = '$customer_id' and delivery_number = '$dr_number' and company_id = '$company_id' and type='AR' AND status='F'"));
		$total_debit = $sum_at_amount[0];
		
	}else if($ref_number == "ST"){
		$sum_str_amount = mysql_fetch_array(mysql_query("SELECT sum(price*qty) FROM `tbl_stock_released_details` where delivery_number = '$dr_number' AND status='R'"));
		$total_debit = $sum_str_amount[0];
		
	}else{
		
		$fetchSR = mysql_query("SELECT * FROM `tbl_sales_return` where delivery_number = '$dr_number' and pay_type = 'H' and company_id = '$company_id' and status = 'F'");
		$total_SR = 0;
		while($srRow = mysql_fetch_array($fetchSR)){
			$sr_number = $srRow['sr_number'];
			
			$SRSum = mysql_fetch_array(mysql_query("SELECT sum(amount) FROM `tbl_sales_return_details` where sr_number = '$sr_number' and status = 'F'"));
			
			$total_SR += $SRSum[0];
		}
		
		$count_dr_amount = mysql_fetch_array(mysql_query("SELECT sum(amount) from tbl_dr_detail where delivery_number = '$dr_number' and company_id = '$company_id' and status='F' "));
		$sales_discount = mysql_fetch_array(mysql_query("SELECT discount from tbl_dr_header where delivery_number = '$dr_number' and company_id = '$company_id'"));
		
		$total_debit = ($count_dr_amount[0]-$sales_discount[0])-$total_SR;
	}
	
	//for finished pm
	$fetchFinished = mysql_query("SELECT ph.payment_number from tbl_payment as ph, tbl_payment_details as pd where ph.company_id = '$company_id' and ph.status='F' and ph.cr_stat != '2' and (ph.delivery_number = '$dr_number' or pd.delivery_number = '$dr_number') and ph.payment_number = pd.payment_number GROUP BY ph.payment_number");
	$totalp = 0;
	while($fRow = mysql_fetch_array($fetchFinished)){
		$sumPaid = mysql_fetch_array(mysql_query("SELECT sum(debit) from tbl_payment_details where payment_number='$fRow[0]' and delivery_number='$dr_number' and status = 'F'"));
		$totalp += $sumPaid[0];
	}
	
	$sumOldPaid = mysql_fetch_array(mysql_query("SELECT sum(amount) from tbl_payment where delivery_number='$dr_number' and company_id = '$company_id' and status='F' and cr_stat != '2' and old_trans = '1'"));
	
	$sumAccountsTransfer = mysql_fetch_array(mysql_query("SELECT sum(amount) FROM tbl_accounts_transfer WHERE from_account = '$customer_id' AND status='F' AND type='AR'"));
	
	$sumCredit = mysql_fetch_array(mysql_query("SELECT sum(credit) from tbl_payment_details where delivery_number='$dr_number' AND status='F'"));
	
	$totalAmountPaid = $totalp+$sumOldPaid[0];
	
	$sumCreditMemo = mysql_fetch_array(mysql_query("SELECT sum(amount) FROM tbl_credit_memo_details WHERE delivery_number='$dr_number' AND status='F'")) or die(mysql_error());
	
	$sumDebitMemo = mysql_fetch_array(mysql_query("SELECT sum(amount) FROM tbl_debit_memo_details WHERE delivery_number='$dr_number' AND status='F'")) or die(mysql_error());
	
	$total = ($total_debit+$sumCredit[0]+$sumDebitMemo[0])-($totalAmountPaid+$sumCreditMemo[0]+$sumAccountsTransfer[0]);
	
	return $total;
	
}

function CPTotalPayment($dr_number){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
	
	//for finished pm
	$fetchFinished = mysql_query("SELECT ph.payment_number from tbl_payment as ph, tbl_payment_details as pd where ph.company_id = '$company_id' and ph.branch_id = '$branch_id' and ph.status='F' and ph.cr_stat != '2' and (ph.delivery_number = '$dr_number' or pd.delivery_number = '$dr_number') and ph.payment_number = pd.payment_number GROUP BY ph.payment_number");
	$totalp = 0;
	while($fRow = mysql_fetch_array($fetchFinished)){
		$sumPaid = mysql_fetch_array(mysql_query("SELECT sum(debit) from tbl_payment_details where payment_number='$fRow[0]' and delivery_number='$dr_number' and status = 'F'"));
		$totalp += $sumPaid[0];
	}
	
	$sumOldPaid = mysql_fetch_array(mysql_query("SELECT sum(amount) from tbl_payment where delivery_number='$dr_number' and company_id = '$company_id' and branch_id = '$branch_id' and status='F' and cr_stat != '2' and old_trans = '1'"));
	
	$sumCredit = mysql_fetch_array(mysql_query("SELECT sum(credit) from tbl_payment_details where delivery_number='$dr_number' AND status='F')"));
	
	$totalAmountPaid = $totalp+$sumOldPaid[0];
	
	return $totalAmountPaid;
}

function APBalance($rr_number,$supplier_id){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
	
	$ref_number = SUBSTR($rr_number, 0, 2);
	$nr_ref_num = SUBSTR($rr_number, 0, 5);
	$account_id = "S-".$supplier_id;
	$ref_id = SUBSTR($rr_number, 3);
	
	$getgchartMainId = getBulkData("gchart_main_id","tbl_gchart_main","company_id = '$company_id' AND chart = 'Accounts Payable'") or die(mysql_error());
	
	if($ref_number == 'BB'){
		$count_balance = mysql_fetch_array(mysql_query("SELECT sum(dr) from tbl_beginning_balance where bbnum='$rr_number' and company_id = '$company_id' and status = 'F' and account_id = '$account_id' and gchart_main_id = '$getgchartMainId[gchart_main_id]'")) or die(mysql_error());
		$refNo = $rr_number;
		$sumBalance = $count_balance[0]*1;
		$e_type = "BB";
		$type = "B";
		
	}else if($ref_number == 'RR' || $nr_ref_num == 'NR-RR'){
		$count_balance = mysql_fetch_array(mysql_query("SELECT sum(quantity * supplier_price) from tbl_rr_details where receiving_number = '$rr_number' and supplier_id = '$supplier_id' and company_id = '$company_id' and status = 'F' ")) or die(mysql_error());
		$fetch_pr = mysql_query("SELECT pr_num from tbl_purchase_return_header where receiving_number = '$rr_number' and company_id = '$company_id' and status= 'F'") or die(mysql_error());
		$sumpr = 0;
		while($pr_num = mysql_fetch_array($fetch_pr)){
			$sum_pr = mysql_fetch_array(mysql_query("SELECT sum(qty * cost) from tbl_purchase_return_details where company_id = '$company_id' and status='F' and pr_num = '$pr_num[0]'")) or die(mysql_error());
			$sumpr += $sum_pr[0];
		}
		
		$fetch_swine_pr = mysql_query("SELECT pr_num from tbl_swine_purchase_return where receiving_number = '$rr_number' and company_id = '$company_id' and status= 'F'") or die(mysql_error());
		$sum_swine_pr = 0;
		while($prNum = mysql_fetch_array($fetch_swine_pr)){
			$sumSwinePR = mysql_fetch_array(mysql_query("SELECT sum(price) from tbl_swine_pr_details where status='F' and pr_num = '$prNum[0]'")) or die(mysql_error());
			$sum_swine_pr += $sumSwinePR[0];
		}
		
		$refNo = $rr_number;
		$sumBalance = ($count_balance[0]-($sumpr+$sum_swine_pr))*1;
		$e_type = "RR";
		$type = "R";
		
	}else if($ref_number == 'TR'){
		$getTR = mysql_fetch_array(mysql_query("SELECT tr_amount,wh_amount from tbl_rr_header as rr where rr.receiving_number='$ref_id' and rr.company_id = '$company_id' and rr.status='F' UNION ALL SELECT tr_amount,wh_amount from tbl_stock_transfer_header as st where st.ref_id='$ref_id' and st.company_id = '$company_id' and (st.status='F' or st.status='R') UNION ALL SELECT tr_amount,wh_amount from tbl_dr_header as dr where dr.delivery_number='$ref_id' and dr.company_id = '$company_id' and (dr.status='F' or dr.status='P')")) or die(mysql_error());
		$sumBalance = ($getTR['tr_amount']*1)-($getTR['wh_amount']*1);
		$e_type = "TR";
		$refNo = $ref_id;
		$type = "T";
		
	}else if($ref_number == 'IF'){
		$getIF = mysql_fetch_array(mysql_query("SELECT * FROM `tbl_incubator_details_fees_eggs` where supplier_id = '$supplier_id' and ref_num = '$rr_number' and company_id = '$company_id'")) or die(mysql_error());
		$sumBalance = $getIF['amount']*1;
		$e_type = "IF";
		$refNo = $rr_number;
		$type = "I";
		
	}else if($ref_number == 'AT'){
		$getAT = mysql_fetch_array(mysql_query("SELECT * FROM `tbl_accounts_transfer` where to_account = '$supplier_id' and ref_number = '$rr_number' and company_id = '$company_id' AND type='AP'")) or die(mysql_error());
		$sumBalance = $getAT['amount']*1;
		$e_type = "AT";
		$refNo = $rr_number;
		$type = "A";
	}
	
	
	$sumAccountsTransfer = mysql_fetch_array(mysql_query("SELECT sum(amount) FROM tbl_accounts_transfer WHERE from_account = '$supplier_id' AND status='F' AND type='AP'")) or die(mysql_error());
	
	$sumCreditMemo = mysql_fetch_array(mysql_query("SELECT sum(amount) FROM tbl_credit_memo_details WHERE receiving_number='$refNo' AND status='F' AND type='$type'")) or die(mysql_error());
	
	$sumDebitMemo = mysql_fetch_array(mysql_query("SELECT sum(amount) FROM tbl_debit_memo_details WHERE receiving_number='$refNo' AND status='F' AND type='$type'")) or die(mysql_error());
	
	$total_payment = mysql_fetch_array(mysql_query("SELECT sum(amount + tax_amount) from tbl_supplier_payment where receiving_number = '$refNo' and company_id = '$company_id' and status='F' and (e_type='$e_type' or e_type='')"));
	
	$total = ($sumBalance+$sumCreditMemo[0])-($total_payment[0]+$sumDebitMemo[0]+$sumAccountsTransfer[0]);
	
	return $total;
	
}

function backupAll_db($mode, $host, $user, $pass, $dbname, $dir, $tables = '*')
{
	$disableForeignKeyChecks = true;
	$charset = 'utf8';
	$batchSize = 1000;

	if($tables == '*') {
        $tables = array();
        $dbName = $dbname;
        $result = mysql_query("SELECT table_name FROM information_schema.tables WHERE table_schema = '$dbName' ORDER BY table_name DESC");
        while($row = mysql_fetch_row($result)) {
            $tables[] = $row[0];
        }
    } else {
        $tables = is_array($tables) ? $tables : explode(',', str_replace(' ', '', $tables));
    }

    $sql = 'CREATE DATABASE IF NOT EXISTS `'.$dbName."`;\n\n";
    $sql .= 'USE `'.$dbName."`;\n\n";

    /**
     * Disable foreign key checks 
     */
    if ($disableForeignKeyChecks === true) {
        $sql .= "SET foreign_key_checks = 0;\n\n";
    }

	/**
	 * Iterate tables
	 */
	foreach($tables as $table)
	{
		if($table == "ar_beginning_balance"){
    		$sql .= "\n\n";
    		$sql .= 'DROP VIEW IF EXISTS `ar_beginning_balance`;';
    		$sql .= "\n";
    		$sql .= "CREATE VIEW `ar_beginning_balance`
					AS SELECT
					   `tbl_beginning_balance`.`bbnum` AS `delivery_number`,'BB' AS `module`,
					   `tbl_beginning_balance`.`posted_date` AS `date_added`,
					   `tbl_beginning_balance`.`company_id` AS `company_id`,
					   `tbl_beginning_balance`.`branch_id` AS `branch_id`,
					   `tbl_beginning_balance`.`account_id` AS `account_id`,
					   `tbl_beginning_balance`.`status` AS `status`
					FROM `tbl_beginning_balance`;";
    	}else if($table == "bb_for_aging"){
    		$sql .= "\n\n";
    		$sql .= 'DROP VIEW IF EXISTS `bb_for_aging`;';
    		$sql .= "\n";
    		$sql .= "CREATE VIEW `bb_for_aging`
					AS SELECT
					   `tbl_beginning_balance`.`begin_bal_id` AS `begin_bal_id`,
					   `tbl_beginning_balance`.`company_id` AS `company_id`,
					   `tbl_beginning_balance`.`branch_id` AS `branch_id`,
					   `tbl_beginning_balance`.`bbnum` AS `delivery_number`,
					   `tbl_beginning_balance`.`account_id` AS `customer_id`,
					   `tbl_beginning_balance`.`status` AS `status`,
					   `tbl_beginning_balance`.`posted_date` AS `dr_date`,
					   `tbl_beginning_balance`.`dr` AS `amount`,
					   `tbl_beginning_balance`.`description` AS `description`
					FROM `tbl_beginning_balance` where (`tbl_beginning_balance`.`gchart_main_id` <> 0);";
    	}else if($table == "beginning_balance"){
    		$sql .= "\n\n";
    		$sql .= 'DROP VIEW IF EXISTS `beginning_balance`;';
    		$sql .= "\n";
    		$sql .= "CREATE VIEW `beginning_balance`
					AS SELECT
					   `tbl_beginning_balance`.`bbnum` AS `receiving_number`,'BB' AS `module`,
					   `tbl_beginning_balance`.`date` AS `date_added`,
					   `tbl_beginning_balance`.`company_id` AS `company_id`,
					   `tbl_beginning_balance`.`branch_id` AS `branch_id`,
					   `tbl_beginning_balance`.`gchart_main_id` AS `gchart_main_id`,
					   `tbl_beginning_balance`.`account_id` AS `account_id`,
					   `tbl_beginning_balance`.`dr` AS `dr`,
					   `tbl_beginning_balance`.`cr` AS `cr`,
					   `tbl_beginning_balance`.`date` AS `date`,
					   `tbl_beginning_balance`.`status` AS `status`
					FROM `tbl_beginning_balance`;";
    	}else{
    		/**
             * CREATE TABLE
             */
    		$sql .= 'DROP TABLE IF EXISTS `'.$table.'`;';
		 	$row = mysql_fetch_row(mysql_query('SHOW CREATE TABLE `'.$table.'`'));
            $sql .= "\n\n".$row[1].";\n\n";

            /**
	         * INSERT INTO
	         */
         	$row = mysql_fetch_row(mysql_query('SELECT COUNT(*) FROM `'.$table.'`'));
            $numRows = $row[0];
            // Split table in batches in order to not exhaust system memory 
            	// Number of while-loop calls to perform
            	$numBatches = intval($numRows / $batchSize) + 1; 
            for ($b = 1; $b <= $numBatches; $b++)
            {
        	 	$query = 'SELECT * FROM `' . $table . '` LIMIT ' . ($b * $batchSize - $batchSize) . ',' . $batchSize;
                $result = mysql_query($query);
                $realBatchSize = mysql_num_rows ($result); // Last batch size can be different from $this->batchSize
                $numFields = mysql_num_fields($result);
                if ($realBatchSize !== 0)
                {
                	$sql .= 'INSERT INTO `'.$table.'` VALUES ';
                	for ($i = 0; $i < $numFields; $i++)
                    {
                    	$rowCount = 1;
                    	while($row = mysql_fetch_row($result))
                    	{
                    		$sql.='(';
                    		for($j=0; $j<$numFields; $j++) 
                    		{
                                if (isset($row[$j])) {
                                    $row[$j] = addslashes($row[$j]);
                                    $row[$j] = str_replace("\n","\\n",$row[$j]);
                                    $row[$j] = str_replace("\r","\\r",$row[$j]);
                                    $row[$j] = str_replace("\f","\\f",$row[$j]);
                                    $row[$j] = str_replace("\t","\\t",$row[$j]);
                                    $row[$j] = str_replace("\v","\\v",$row[$j]);
                                    $row[$j] = str_replace("\a","\\a",$row[$j]);
                                    $row[$j] = str_replace("\b","\\b",$row[$j]);
                                    if ($row[$j] == 'true' or $row[$j] == 'false' or preg_match('/^-?[0-9]+$/', $row[$j]) or $row[$j] == 'NULL' or $row[$j] == 'null') {
                                        $sql .= $row[$j];
                                    } else {
                                        $sql .= "'".clean($row[$j])."'" ;
                                    }
                                } else {
                                    $sql.= 'NULL';
                                }

                                if ($j < ($numFields-1)) {
                                    $sql .= ',';
                                }
                            }

                            if ($rowCount == $realBatchSize) {
                                $rowCount = 0;
                                $sql.= ");\n"; //close the insert statement
                            } else {
                                $sql.= "),\n"; //close the row
                            }

                            $rowCount++;
                    	}
                    }
                }
            }
    	}
	}

	if($mode == "ALL"){
		$handle = fopen($dir.'/'.$dbname.'.sql','w+');
		fwrite($handle, $sql);
		fclose($handle);
	}else{
		return $sql;
	}
}

// function getAllAsset($start_date,$company_id,$chart_id){

// 	$getItems = mysql_query("SELECT * from tbl_requisition_details as rd, tbl_po_details as pd, tbl_rr_details as rr, tbl_rr_header as rh WHERE pd.rs_detail_id = rd.requisition_detail_id and rd.asset = 'Yes' and rr.po_detail_id = pd.po_detail_id and rr.company_id = '$company_id' and rh.date_added <= '$start_date' and rr.status = 'F' and rr.product_id = '0' and rd.asset_item != '' and pd.po_number = rh.po_number and rh.receiving_number = rr.receiving_number and rh.chart_id = '$chart_id'");

// 		while($data = mysql_fetch_array($getItems)){

// 			$data[] = array(
// 				"req_detail_id" => $row['requisition_detail_id'],
// 				"req_num" => $rr_row['requisition_num'],
// 				"asset_item" => $row['asset_item'],
// 			);
// 		}

// 		$fetch_bb = mysql_query("SELECT * FROM `tbl_beginning_balance` WHERE company_id='$company_id' and ");
// 		while($rr_row = mysql_fetch_array($fetch_bb)){

// 			$data[] = array(
// 				"module" => "Beginning Balance",
// 				"slug" => "index.php?page=beg-bal-details&id=".$rr_row[gchart_main_id],
// 				"company" => $rr_row['company_id'],
// 				"branch" => $rr_row['branch_id'],
// 				"amount" => "Amount: ".number_format($totalAmount[0],2),
// 				"transaction" => "<strong style='font-style: italic;'>" .$rr_row["bbnum"]."</strong> (".$rr_row["status"].")"
// 			);
// 		}

// 		return $data;

// }


function getFlockType($flock_id){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$flock_class=mysql_fetch_array(mysql_query("SELECT type FROM tbl_flock_inventory WHERE flock_inventory_id='$flock_id' AND company_id='$company_id' AND branch_id='$branch_id' "));
	if($flock_class[0] == 'LB'){
		$type = "Layer Breeder";
	}else if($flock_class[0] == 'LPS'){
		$type = "Layer Parent Stock";
	}else if($flock_class[0] == 'DOP'){
		$type = "Day Old Pullet";
	}else if($flock_class[0] == 'RTL'){
		$type = "Ready To Lay";
	}else{
		$type = "Day Old Chick";
	}
	return $type;
}

function getFlockName($flock_id){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

	$flock_name=mysql_fetch_array(mysql_query("SELECT flock_name FROM tbl_flock_inventory WHERE flock_inventory_id='$flock_id' AND company_id='$company_id' AND branch_id='$branch_id' "));
	return $flock_name[0];
}

function FM_checkError($min,$max,$valu,$old){
	$min   = new_number_format($min,4);
	$max   = new_number_format($max,4);
	$val   = new_number_format($valu,4);
	$old   = new_number_format($old,4);
	$val_r = new_number_format($valu,4);

	if($val == "0.0000"){
		$data = ($min > 0) ? "<span class='inf'></span>":"";
		return $data;
	}else{
		if($val > $old){
			$color_val = "color:#08841e";
		}else if($val < $old){
			$color_val = "color:red";
		}else{
			$color_val = "";
		}
		if(($min > 0) && ($max > 0)){
			if($val <= $min){
				$data = "<span class='fa fa-arrow-circle-o-down' style='color: red;font-size: 18px;float:left;'></span>";
				$data .= ($val < $min) ? "<span class='inf badge' style='color:white;background:red;float:right;font-size: 12px;'>$val_r</span>":"<span style='float:right;font-size: 12px;$color_val'>$val_r</span>";
			}else if($val >= $max){
				$data = "<span class='fa fa-arrow-circle-o-up' style='color: #08841e;font-size: 18px;float:left;'></span>";
				$data .= ($val > $max)?"<span class='inf badge' style='color:white;background:red;float:right;font-size: 12px;'>$val_r</span>":"<span style='float:right;font-size: 12px;$color_val'>$val_r</span>";
			}else{
				$data = "<span style='float:right;font-size: 12px;$color_val'>$val_r</span>";
			}
		}else if($min > 0){
			if($val <= $min){
				$data = "<span class='fa fa-arrow-circle-o-down' style='color: red;font-size: 18px;float:left;'></span>";
				$data .= ($val < $min)?"<span class='inf badge' style='color:white;background:red;float:right;font-size: 12px;'>$val_r</span>":"<span style='float:right;font-size: 12px;$color_val'>$val_r</span>";
			}else{
				$data = "<span style='float:right;font-size: 12px;$color_val'>$val_r</span>";
			}
		}else if($max > 0){
			if($val >= $max){
				$data = "<span class='fa fa-arrow-circle-o-up' style='color: #08841e;font-size: 18px;float:left;'></span>";
				$data .= ($val > $max)?"<span class='inf badge' style='color:white;background:red;float:right;font-size: 12px;'>$val_r</span>":"<span style='float:right;font-size: 12px;$color_val'>$val_r</span>";
			}else{
				$data = "<span style='float:right;font-size: 12px;$color_val'>$val_r</span>";
			}
		}else{
			$data = "<span style='float:right;font-size: 12px;$color_val'>$val_r</span>";
		}
		return $data;
	}
}

function farmStdData_eggs($breed_id,$age_by_weeks,$flock_type,$flock_gender,$select_data){
	$company_id = $_SESSION['system']['company_id'];

	$get_flock_type = ($flock_type == "RTL")?"DOP":$flock_type;

	$query_result = mysql_fetch_array(mysql_query("SELECT $select_data FROM tbl_farm_standards_eggs WHERE company_id='$company_id' AND week='$age_by_weeks' AND breed_id='$breed_id' AND flock_type='$get_flock_type' AND flock_gender='$flock_gender'"));
	
	return ($query_result[0] == "")?0:$query_result[0];
}


function getCompanyType($id){
	$result = mysql_query("SELECT company_type from tbl_company where company_id = $id") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["company_type"];
}

function getTotalKgProjection(){
	$company_id = $_SESSION['system']['company_id'];
	$result = mysql_query("SELECT sum(quantity) as qty FROM tbl_fp_projection_feeds WHERE company_id = '$company_id' ") or die (mysql_error());
	$row = mysql_fetch_assoc($result);
	
	return $row["qty"];
}

function addUsedAdvances($rr_number,$ref_advance_payment,$supplier_id){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
	$date = getCurrentDate();
	$apType = getAPType($rr_number);
	$account_id = "S-".$supplier_id;
	$dec_stat = clean($_POST['dec_stat']);

	$fetchAP = mysql_fetch_array(mysql_query("SELECT * FROM tbl_supplier_payment WHERE cv_number='$ref_advance_payment' and company_id='$company_id' and status ='F' and supplier_id = '$supplier_id'"));

	if($fetchAP['declared_status'] == 1){
		$dType = "-UD-";
	}else{
		$dType = "";
	}

	$num = date("mdyhis",strtotime($date));
	$cv_number = "CV-".$dType.get_branch()."-".$num;

	//added
	$amount_advance = mysql_fetch_array(mysql_query("SELECT sum(amount+tax_amount) FROM tbl_supplier_payment WHERE cv_number='$ref_advance_payment' and company_id='$company_id' and status ='F' and supplier_id = '$supplier_id'")) or die(mysql_error());
	//used
	//$amount_used = mysql_fetch_array(mysql_query("SELECT sum(amount+tax_amount) FROM tbl_supplier_payment WHERE receiving_number='$rr_number' AND cv_number != '$cv_number' AND company_id='$company_id' AND status ='F' and supplier_id = '$supplier_id'")) or die(mysql_error());
	$fetchRR = mysql_query("SELECT receiving_number FROM tbl_rr_header WHERE po_number='$fetchAP[receiving_number]' AND supplier_id='$supplier_id' AND status='F' AND company_id='$company_id' AND receiving_number!='$rr_number'") or die(mysql_error());
	$amount_used = 0;
	while($rrRow = mysql_fetch_array($fetchRR)){
		$total_used = mysql_fetch_array(mysql_query("SELECT sum(supplier_price*quantity) FROM tbl_rr_details WHERE receiving_number='$rrRow[0]'")) or die(mysql_error());
		$amount_used += $total_used[0];
	}

	//total rr
	$amount_rr = mysql_fetch_array(mysql_query("SELECT sum(supplier_price*quantity) FROM tbl_rr_details WHERE receiving_number='$rr_number'")) or die(mysql_error());
	$total_advance = $amount_advance[0]-$amount_used;
	
	if($total_advance > $amount_rr[0]){
		$total = $amount_rr[0];
	}else{
		$total = $total_advance;
	}

	$remarks = clean($fetchAP['remarks']);

	if($total_advance > 0){
		$query = mysql_query("INSERT INTO `tbl_supplier_payment`(`company_id`, `branch_id`, `trans_branch_id`, `payment_type`, `cv_number`, `receiving_number`, `check_number`, `supplier_id`, `apply_withholding_tax`, `amount`, `date_of_payment`, `date_added`, `status`, `remarks`, `module`, `cnb_gchart`, `cv_status`, `e_type`, `advance_payment`, encoded_byID,cv_date,ap_type) VALUES ('$company_id', '$branch_id', '$fetchAP[trans_branch_id]', '$fetchAP[payment_type]', '$cv_number', '$rr_number', '$fetchAP[check_number]', '$supplier_id', '$fetchAP[apply_withholding_tax]', '$total', '$fetchAP[date_of_payment]', '$date', 'F', '$remarks', '$fetchAP[module]','$fetchAP[cnb_gchart]','$fetchAP[cv_status]','RR', 'U','','$date','$apType')") or die(mysql_error());
		
		$sp_id = mysql_insert_id();
		if($query){
			
			mysql_query("INSERT INTO `tbl_advance_payment`(`company_id`, `branch_id`, transaction_branch_id, hr_id, transaction_ref, `ref_number`, `account_id`, `amount`, `module`, `status`) VALUES ('$company_id','$branch_id', '$branch_id', '$sp_id', '$cv_number', '$ref_advance_payment','$account_id','$total','S','F')") or die(mysql_error());
			
			$action = "Added Advance Payment(CV #: ". $cv_number . ")";
			$module = "Check Voucher";
			insertLog($user_id,$action,$module);
			
		}
	}
}

function sumtotalCreditMemo($ref_code,$type){
	$company_id = $_SESSION['system']['company_id'];
	if($type == "AR"){
		$query = "delivery_number='$ref_code'";
	}else{
		$query = "receiving_number='$ref_code'";
	}

	$result = mysql_query("SELECT sum(amount) FROM tbl_credit_memo_details WHERE $query AND status='F'") or die (mysql_error());
	$row = mysql_fetch_array($result);
	
	return $row[0];
}

function global_request_log($request_code, $log_desc, $createdBy, $log_date, $req_status = NULL)
{
	$req_status = ($req_status != "")?$req_status:0;
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();

    $data = array(
		'request_code' => $request_code,
		'log' => $log_desc,
		'updated_by' => $createdBy,
		'log_date' => $log_date,
		'request_status' => $req_status
	);
    $res = FM_INSERT_QUERY("tbl_global_request_log", $data);
    return $res;
}



function calculatedRisk($customer_id,$amount,$type){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
	$counter = mysql_fetch_array(mysql_query("SELECT count(id) from tbl_customer_calculated_risk WHERE customer_id = '$customer_id' AND branch_id='$branch_id'")) or die (mysql_error());
	if($counter[0] > 0){
		//update
		if($type == "add"){
			mysql_query("UPDATE tbl_customer_calculated_risk SET total=total+'$amount' WHERE customer_id = '$customer_id' AND branch_id='$branch_id'");
		}else{
			mysql_query("UPDATE tbl_customer_calculated_risk SET total=total-'$amount' WHERE customer_id = '$customer_id' AND branch_id='$branch_id'");
		}
	}else{
		//insert
		mysql_query("INSERT INTO tbl_customer_calculated_risk SET total='$amount',customer_id='$customer_id',branch_id='$branch_id',company_id='$company_id'");
	}
}

function customerBalance($customer_id){
	$company_id = $_SESSION['system']['company_id'];
	$branch_id = get_branch();
	$custID = "C-".$customer_id;

		$getBBChart = getBulkData("gchart_main_id","tbl_gchart_main","chart = 'Accounts Receivable' AND company_id = '$company_id'");
		$getBBAPCR = getBulkData("sum(cr) AS bbcr","tbl_beginning_balance","company_id='$company_id' and branch_id='$branch_id' and status = 'F' and account_id='$custID' AND  gchart_main_id = '$getBBChart[gchart_main_id]'");
		$getBBAPDR = getBulkData("sum(dr) AS bbdr","tbl_beginning_balance","company_id='$company_id' and branch_id='$branch_id' and status = 'F' and account_id='$custID' AND  gchart_main_id = '$getBBChart[gchart_main_id]'");

		if($getBBAPCR[bbcr] > 0){
			$bBAPAmnt = $getBBAPCR[bbcr];
		}else{
			$bBAPAmnt = $getBBAPDR[bbdr];
		}
		
		$sum_of_at_credit = mysql_fetch_array(mysql_query("SELECT sum(amount) from tbl_accounts_transfer where company_id='$company_id' AND status='F' AND from_branch='$branch_id' AND from_account = '$customer_id' AND type='AR'")) or die(mysql_error());
		
		$sum_of_at_debit = mysql_fetch_array(mysql_query("SELECT sum(amount) from tbl_accounts_transfer where company_id='$company_id' AND status='F' AND to_branch='$branch_id' AND to_account = '$customer_id' AND type='AR'")) or die(mysql_error());
		
		
		$debit_memo_total = mysql_fetch_array(mysql_query("SELECT sum(amount) FROM tbl_debit_memo_details as dd, tbl_debit_memo_header as dh WHERE dh.status = 'F' AND dd.account_id='$custID' and dh.ref_number=dd.ref_number AND dd.db_type='C'")) or die(mysql_error());
	
		$credit_memo_total = mysql_fetch_array(mysql_query("SELECT sum(amount) FROM tbl_credit_memo_details as cd, tbl_credit_memo_header as ch  WHERE ch.ref_number=cd.ref_number AND ch.status = 'F' AND cd.account_id='$custID' AND cd.cm_type='C' AND ch.cash_sales!='1' $query_cm")) or die(mysql_error());
		
		$fetch_dr = mysql_query("SELECT * FROM tbl_dr_header WHERE customer_id='$customer_id' and company_id='$company_id' and branch_id='$branch_id' and (status='F' or status='P') and pay_type = 'H'");
		$total_amount_ = 0;
		
		$total_sr = 0;
		//$totalDRCredit = 0;
		while($dr_row = mysql_fetch_array($fetch_dr)){
			$delivery_number = $dr_row['delivery_number'];
			$sum_of_delivery = mysql_fetch_array(mysql_query("SELECT sum(amount) FROM tbl_dr_detail WHERE delivery_number='$delivery_number' and company_id='$company_id' and branch_id='$branch_id'"));
			
			$total_amount_ += ($sum_of_delivery[0]-$dr_row['discount']);
			
			$fetch_sr = mysql_query("SELECT * FROM tbl_sales_return WHERE delivery_number='$delivery_number' and customer_id='$customer_id' and company_id='$company_id' and branch_id='$branch_id' and pay_type = 'H' and status='F'");
			while($sr_row = mysql_fetch_array($fetch_sr)){
				$sr_number = $sr_row['sr_number'];
				$sum_of_sr = mysql_fetch_array(mysql_query("SELECT sum(amount) FROM tbl_sales_return_details WHERE sr_number='$sr_number' and status = 'F'"));
				$total_sr += $sum_of_sr[0];
			}
		}
		
		$fetch_payment = mysql_query("SELECT * FROM tbl_payment WHERE receipt_type = 'C' and status='F' and customer_id='$customer_id' and company_id='$company_id' and advance_stat !='U' and branch_id='$branch_id'");
		$total_cp = 0;
		while($pmRow = mysql_fetch_array($fetch_payment)){
			$sum_cp = mysql_fetch_array(mysql_query("SELECT sum(debit) FROM tbl_payment_details WHERE payment_number ='$pmRow[payment_number]' and status='F' and customer_id='$customer_id'")) or die(mysql_error());
			$sumExpense = mysql_fetch_array(mysql_query("SELECT sum(debit) FROM tbl_payment_details WHERE payment_number='$pmRow[issue_ref_no]' and status='F' AND pay_status='E'"));
			$sumOldPayment = mysql_fetch_array(mysql_query("SELECT sum(amount) FROM tbl_payment WHERE payment_number ='$pmRow[payment_number]' and status='F' and customer_id='$customer_id' and old_trans='1'"));
			
			// d e b i t
			$sumPM = $sumOldPayment[0]+$sum_cp[0]+$sumExpense[0];
			$total_cp += $sumPM;
		}
		
		$fetchSRT = mysql_query("SELECT delivery_number FROM tbl_stock_released WHERE customer_id='$customer_id' AND status='R' AND pay_type = 'H' and company_id='$company_id' and branch_id='$branch_id'");
		$sumSRT = 0;
		while($rowSRT = mysql_fetch_array($fetchSRT)){
			$sum_srt = mysql_fetch_array(mysql_query("SELECT sum(price*qty) FROM tbl_stock_released_details WHERE delivery_number='$rowSRT[delivery_number]' AND (status = 'F' OR status='R')"));
			$sumSRT += $sum_srt[0];

		}

		// c r e d i t
		$sumDRCredit = mysql_fetch_array(mysql_query("SELECT sum(credit) from tbl_payment_details where status='F' and customer_id='$customer_id' and debit = '0'"));
		$totalDRCredit = $sumDRCredit[0];
		$total_amount = ($total_amount_+$bBAPAmnt+$totalDRCredit+$debit_memo_total[0]+$sum_of_at_debit[0]+$sumSRT) - ($total_sr+$total_cp+$credit_memo_total[0]+$sum_of_at_credit[0]);
		return $total_amount;
}
?>