<?php
//SalesReportMaster-Excel.php
session_start(); include "../../newConfig.php";
include "../../number_format_ind.php";
$today = date("Y-m-d");
$users_code = $_SESSION['userid'];
$dbname = $_SESSION['dbase'];
$az[0] = "A"; $az[1] = "B"; $az[2] = "C"; $az[3] = "D"; $az[4] = "E"; $az[5] = "F"; $az[6] = "G"; $az[7] = "H"; $az[8] = "I"; $az[9] = "J"; $az[10] = "K"; $az[11] = "L"; $az[12] = "M"; $az[13] = "N"; $az[14] = "O"; $az[15] = "P"; $az[16] = "Q"; $az[17] = "R"; $az[18] = "S"; $az[19] = "T"; $az[20] = "U"; $az[21] = "V"; $az[22] = "W"; $az[23] = "X"; $az[24] = "Y"; $az[25] = "Z";
$az[26] = "AA"; $az[27] = "AB"; $az[28] = "AC"; $az[29] = "AD"; $az[30] = "AE"; $az[31] = "AF"; $az[32] = "AG"; $az[33] = "AH"; $az[34] = "AI"; $az[35] = "AJ"; $az[36] = "AK"; $az[37] = "AL"; $az[38] = "AM"; $az[39] = "AN"; $az[40] = "AO"; $az[41] = "AP"; $az[42] = "AQ"; $az[43] = "AR"; $az[44] = "AS"; $az[45] = "AT"; $az[46] = "AU"; $az[47] = "AV"; $az[48] = "AW"; $az[49] = "AX"; $az[50] = "AY"; $az[51] = "AZ"; 

$users_code = $_SESSION['userid'];
$dbname = $_SESSION['dbase'];

$sql = "SELECT * FROM `main_access` WHERE `empcode` = '$users_code'";
$query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
	$loc_access = $row['loc_access'];
	$cgroup_access = $row['cgroup_access'];
	if($row['supadmin_access'] == 1 || $row['supadmin_access'] == "1"){ $utype = "S"; }
	else if($row['admin_access'] == 1 || $row['admin_access'] == "1"){ $utype = "A"; }
	else if($row['normal_access'] == 1 || $row['normal_access'] == "1"){ $utype = "N"; }
	else{ $utype = "N"; }
}
if($utype == "S" || $utype == "A"){
	$sql = "SELECT * FROM `log_useraccess` WHERE `dblist` = '$dbname'"; $query = mysqli_query($conns,$sql);
	while($row = mysqli_fetch_assoc($query)){ $user_name[$row['empcode']] = $row['username']; $user_code[$row['empcode']] = $row['empcode']; }
	$addedemp = "";
}
else{
	$sql = "SELECT * FROM `log_useraccess` WHERE `dblist` = '$dbname'"; $query = mysqli_query($conns,$sql);
	while($row = mysqli_fetch_assoc($query)){ $user_name[$row['empcode']] = $row['username']; $user_code[$row['empcode']] = $row['empcode']; }
	$addedemp = "";
}
//Usr access Based Sector Filter
if($loc_access == "all" || $loc_access == "All" || $loc_access == "" || $loc_access == NULL){ $user_sector_filter = ""; }
else{ $wcode = str_replace(",","','",$loc_access); $user_sector_filter = " AND code IN ('$wcode')"; }

//Usr access Based Customer Group Filter
if($cgroup_access == "all" || $cgroup_access == "All" || $cgroup_access == "" || $cgroup_access == NULL){ $user_cusgrp_filter = ""; }
else{ $gcode = str_replace(",","','",$cgroup_access); $user_cusgrp_filter = " AND code IN ('$gcode')"; }

$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Receipt Report' OR `type` = 'All' ORDER BY `id` DESC";
$query = mysqli_query($conn,$sql); $logopath = $cdetails = "";
while($row = mysqli_fetch_assoc($query)){ $logopath = $row['logopath']; $cdetails = $row['cdetails']; }

$sql = "SELECT * FROM `main_groups` WHERE `gtype` LIKE '%C%'".$user_cusgrp_filter." ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $grp_code = $grp_name = array();
while($row = mysqli_fetch_assoc($query)){ $grp_code[$row['code']] = $row['code']; $grp_name[$row['code']] = $row['description']; }

$grp_list = implode("','",$grp_code);
$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' AND `groupcode` IN ('$grp_list') ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $cus_code = $cus_name = $cus_group = array();
while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; $cus_group[$row['code']] = $row['groupcode']; }

$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1'".$user_sector_filter." ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `item_category` WHERE `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $icat_code = $icat_name = array();
while($row = mysqli_fetch_assoc($query)){ $icat_code[$row['code']] = $row['code']; $icat_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $item_code = $item_name = array();
while($row = mysqli_fetch_assoc($query)){ $item_code[$row['code']] = $row['code']; $item_name[$row['code']] = $row['description']; $item_category[$row['code']] = $row['category']; }

//Fetch Master Details
$sql = "SELECT * FROM `master_itemfields` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $cus_jalsfreight_flag = $row['cus_jalsfreight_flag']; }
if($cus_jalsfreight_flag == ""){ $cus_jalsfreight_flag = 0; }

$sql = "SELECT *  FROM `main_linkdetails` WHERE `href` LIKE '%SalesReportMaster.php%'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $cid = $row['childid']; }

//Report Master Access Filter
$sql = "SELECT * FROM `master_reportfields` WHERE `code` = '$cid' AND `active` = '1'"; $query = mysqli_query($conn,$sql); $frt_amt_flag = 0;
while($row = mysqli_fetch_assoc($query)){
	$type = "type";
	$code = "code";
	$pattern = "pattern";
	$field_details[$row['date_flag']] = "date_flag";
	$field_details[$row['inv_flag']] = "inv_flag";
	$field_details[$row['binv_flag']] = "binv_flag";
	$field_details[$row['vendor_flag']] = "vendor_flag";
	$field_details[$row['item_flag']] = "item_flag";
	$field_details[$row['jals_flag']] = "jals_flag";
	$field_details[$row['birds_flag']] = "birds_flag";
	$field_details[$row['tweight_flag']] = "tweight_flag";
	$field_details[$row['eweight_flag']] = "eweight_flag";
	$field_details[$row['nweight_flag']] = "nweight_flag";
	$field_details[$row['farm_weight']] = "farm_weight"; $t1 = explode(":",$row['farm_weight']); if($t1[1] == 1 || $t1[1] == "1"){ $farm_wt_flag = 1; }
	$field_details[$row['aweight_flag']] = "aweight_flag";
	$field_details[$row['prate_flag']] = "prate_flag"; $pdet = explode(":",$row['prate_flag']); if($pdet[1] == 1 || $pdet[1] == "1"){ $prate_flag = 1; }
	$field_details[$row['price_flag']] = "price_flag";
	$field_details[$row['freightamt_flag']] = "freightamt_flag"; $t1 = explode(":",$row['freightamt_flag']); if($t1[1] == 1 || $t1[1] == "1"){ $frt_amt_flag = 1; }
	$field_details[$row['jfreight_flag']] = "jfreight_flag";
	$field_details[$row['tcds_flag']] = "tcds_flag";
	$field_details[$row['discount_flag']] = "discount_flag";
	$field_details[$row['tamt_flag']] = "tamt_flag";
	$field_details[$row['sector_flag']] = "sector_flag";
	$field_details[$row['remarks_flag']] = "remarks_flag";
	$field_details[$row['vehicle_flag']] = "vehicle_flag";
	$field_details[$row['driver_flag']] = "driver_flag";
	$field_details[$row['weighton_flag']] = "weighton_flag";
	$field_details[$row['cr_flag']] = "cr_flag";
	$field_details[$row['dr_flag']] = "dr_flag";
	$field_details[$row['rb_flag']] = "rb_flag";
	$field_details[$row['user_flag']] = "user_flag";
	$note_flag = $row['note_flag'];
	$note_code = $row['note_code'];
	$vsign_flag = $row['vsign_flag'];
	$csign_flag = $row['csign_flag'];
	$qr_img_flag = $row['qr_img_flag'];
	$col_count = $row['count'];
}

$fdate = date("Y-m-d",strtotime($_GET['fdate']));
$tdate = date("Y-m-d",strtotime($_GET['tdate']));
$customers = $_GET['customers'];
$billnos = $_GET['billnos'];
$sectors = $_GET['sectors']; if($sectors == "all"){ $sec_fltr = ""; } else{ $sec_list = implode("','",explode(",",$sectors)); $sec_fltr = " AND `warehouse` IN ('$sec_list')"; }
$item_cat = $_GET['item_cat'];
$items = $_GET['items'];
$users = $_GET['users'];
$exports = $_GET['exports'];
$prices = $_GET['prices'];

$group_alist = explode("@",$_GET['groups']);

$groups = array(); $grp_all_flag = 0;
foreach($group_alist as $grps){ $groups[$grps] = $grps; if($grps == "all"){ $grp_all_flag = 1; } }
$grp_list = implode("@",$groups);


if($prate_flag == 1 || $prate_flag == "1"){
	$sql = "SELECT * FROM `main_dailypaperrate` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $prates[$row['date']."@".$row['cgroup']."@".$row['code']] = $row['new_price']; }
}

$cus_filter = $cus_list = $sec_list = "";
if($customers != "all"){ $cus_filter = " AND `customercode` IN ('$customers')"; }
else if($grp_all_flag == 0){
	foreach($cus_code as $ccode){
		$gcode = $cus_group[$ccode];
		if(empty($groups[$gcode]) || $groups[$gcode] == ""){ }
		else{ if($cus_list == ""){ $cus_list = $ccode; } else{ $cus_list = $cus_list."','".$ccode; } }
	}
	$cus_filter = " AND `customercode` IN ('$cus_list')";
}
else{ $cus_list = implode("','",$cus_code); $cus_filter = " AND `customercode` IN ('$cus_list')"; }


if($items != "all"){ $item_filter = " AND `itemcode` IN ('$items')"; }
else if($item_cat == "all"){ $item_filter = ""; }
else{
	$icat_list = $item_filter = "";
	foreach($item_code as $icode){
		$item_category[$icode];
		if(!empty($item_category[$icode]) && $item_category[$icode] == $item_cat){
			if($icat_list == ""){ $icat_list = $icode; } else{ $icat_list = $icat_list."','".$icode; }
		}
	}
	$item_filter = " AND `itemcode` IN ('$icat_list')";
}

if($billnos == "") { $binv_filter = ""; } else { $binv_filter = " AND `bookinvoice` = '$billnos'"; }
if($prices == "") { $rate_filter = ""; } else { $rate_filter = " AND `itemprice` = '$prices'"; }
if($users == "all"){ $user_filter = ""; } else{ $user_filter = " AND `addedemp` IN ('$users')"; }

// if($sectors == "all"){ $sec_list = implode("','",$sector_code); $sector_filter = " AND `warehouse` IN ('$sec_list')"; }
// else{ $sector_filter = " AND `warehouse` IN ('$sectors')"; }

$sql = "SELECT * FROM `customer_sales` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$cus_filter."".$binv_filter."".$rate_filter."".$item_filter."".$sec_fltr."".$user_filter." AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`invoice` ASC";
$query = mysqli_query($conn,$sql); $i = 0; $sales = $inv_count = $slc_freightamt = $slc_tcdsamt = $slc_roundoff = $slc_finaltotal = array();
while($row = mysqli_fetch_assoc($query)){
	$i++;
	$sales[$row['date']."@".$i] = $row['date']."@".$row['invoice']."@".$row['bookinvoice']."@".$row['customercode']."@".$row['jals']."@".$row['totalweight']."@".$row['emptyweight']."@".$row['itemcode']."@".$row['birds']."@".$row['netweight']."@".$row['itemprice']."@".$row['totalamt']."@".$row['tcdsper']."@".$row['tcdsamt']."@".$row['roundoff']."@".$row['finaltotal']."@".$row['warehouse']."@".$row['narration']."@".$row['discountamt']."@".$row['taxamount']."@".$row['remarks']."@".$row['vehiclecode']."@".$row['drivercode']."@".$row['addedemp']."@".$row['freight_amt']."@".$row['farm_weight'];

	if(empty($inv_count[$row['invoice']]) || $inv_count[$row['invoice']] == ""){
		$inv_count[$row['invoice']] = 1;
		if($row['freight_amount_jal'] == "" || $row['freight_amount_jal'] == NULL){ $slc_freightamt[$row['invoice']] = 0; } else{ $slc_freightamt[$row['invoice']] = (float)$row['freight_amount_jal']; }
		if($row['tcdsamt'] == "" || $row['tcdsamt'] == NULL){ $slc_tcdsamt[$row['invoice']] = 0.00; } else{ $slc_tcdsamt[$row['invoice']] = $row['tcdsamt']; }
		if($row['roundoff'] == "" || $row['roundoff'] == NULL){ $slc_roundoff[$row['invoice']] = 0.00; } else{ if(($row['itotal'] + $row['tcdsamt']) <= $row['finaltotal']){ $slc_roundoff[$row['invoice']] = $row['roundoff']; } else{ $slc_roundoff[$row['invoice']] = -1 *($row['roundoff']); } }
		$slc_finaltotal[$row['invoice']] = $row['finaltotal'];
	}
	else{
		$inv_count[$row['invoice']] = $inv_count[$row['invoice']] + 1;
	}
}

require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';
$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("Mallikarjuna K")
->setLastModifiedBy("Mallikarjuna K")
->setTitle("Sales Report Master")
->setSubject("Sales Report")
->setDescription("Sales Report Master")
->setKeywords("Sales Report Master")
->setCategory("SalesReportMaster");
$bwtd_det_col = -1; $j = 0; $aflag = 1;
for($i = 1;$i <= $col_count;$i++){
	if($field_details[$i.":".$aflag] == "date_flag"){ $objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j]."1", 'Date'); $j++; $bwtd_det_col++; }
	else if($field_details[$i.":".$aflag] == "inv_flag"){ $objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j]."1", 'Invoice'); $j++; $bwtd_det_col++; }
	else if($field_details[$i.":".$aflag] == "binv_flag"){ $objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j]."1", 'Book Invoice'); $j++; $bwtd_det_col++; }
	else if($field_details[$i.":".$aflag] == "vendor_flag"){ $objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j]."1", 'Customer'); $j++; $bwtd_det_col++; }
	else if($field_details[$i.":".$aflag] == "item_flag"){ $objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j]."1", 'Item'); $j++; $bwtd_det_col++; }
	else if($field_details[$i.":".$aflag] == "jals_flag"){ $objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j]."1", 'Jals'); $j++; }
	else if($field_details[$i.":".$aflag] == "birds_flag"){ $objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j]."1", 'Birds'); $j++; }
	else if($field_details[$i.":".$aflag] == "tweight_flag"){ $objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j]."1", 'T. Weight'); $j++; }
	else if($field_details[$i.":".$aflag] == "eweight_flag"){ $objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j]."1", 'E. Weight'); $j++; }
	else if($field_details[$i.":".$aflag] == "nweight_flag"){ $objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j]."1", 'N. Weight'); $j++; }
	else if($field_details[$i.":".$aflag] == "aweight_flag"){ $objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j]."1", 'Avg. Weight'); $j++; }
	else if($field_details[$i.":".$aflag] == "prate_flag"){ $objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j]."1", 'Paper Rate'); $j++; }
	else if($field_details[$i.":".$aflag] == "price_flag"){ $objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j]."1", 'Price'); $j++; }
	else if($field_details[$i.":".$aflag] == "tcds_flag"){ $objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j]."1", 'TCS'); $j++; }
	else if($field_details[$i.":".$aflag] == "discount_flag"){ $objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j]."1", 'Discount'); $j++; }
	else if($field_details[$i.":".$aflag] == "tamt_flag"){ $objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j]."1", 'Total Amount'); $j++; }
	else if($field_details[$i.":".$aflag] == "sector_flag"){ $objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j]."1", 'Warehouse'); $j++; }
	else if($field_details[$i.":".$aflag] == "remarks_flag"){ $objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j]."1", 'Remarks'); $j++; }
	else if($field_details[$i.":".$aflag] == "vehicle_flag"){ $objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j]."1", 'Vehicle'); $j++; }
	else if($field_details[$i.":".$aflag] == "driver_flag"){ $objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j]."1", 'Driver'); $j++; }
	else if($field_details[$i.":".$aflag] == "cr_flag"){ $objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j]."1", 'Sales'); $j++; }
	else if($field_details[$i.":".$aflag] == "dr_flag"){ $objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j]."1", 'Receipts'); $j++; }
	else if($field_details[$i.":".$aflag] == "rb_flag"){ $objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j]."1", 'Running Balance'); $j++; }
	else if($field_details[$i.":".$aflag] == "user_flag"){ $objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j]."1", 'User'); $j++; }
	else{ }
}

$ccount = sizeof($sales); $exi_inv = "";
$row = 1; $tbcount = $tjcount = $tncount = $twcount = $tecount = $tdcount = $ttcount = $tacount = $column = 0;
for($cdate = strtotime($fdate);$cdate <= strtotime($tdate);$cdate += (86400)){
	$adate = date('Y-m-d', $cdate);
	for($j = 0;$j <= $ccount;$j++){
		$sales[$adate."@".$j];
		if($sales[$adate."@".$j] != ""){
			$sales_details = explode("@",$sales[$adate."@".$j]);
			$row++; $column = 0;
			$tacount = $tacount + (float)$sales_details[11];
			if($exi_inv != $sales_details[1]){
				$exi_inv = $sales_details[1];
				if(number_format_ind($slc_finaltotal[$sales_details[1]]) == number_format_ind($rb_amt)){
					$rb_amt = 0;
				}
				else{
					$rb_amt = $rb_amt + $slc_finaltotal[$sales_details[1]];
				}
				$ft_jfrgt = (float)$ft_jfrgt + (float)$slc_freightamt[$sales_details[1]];
				$ft_tcds = $ft_tcds + $slc_tcdsamt[$sales_details[1]];
				$ft_roundoff = $ft_roundoff + $slc_roundoff[$sales_details[1]];
				$fst_famt = $fst_famt + $slc_finaltotal[$sales_details[1]];
				
				for($i = 1;$i <= $col_count;$i++){
					if($field_details[$i.":".$aflag] == "date_flag"){ $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, date("d.m.Y",strtotime($sales_details[0]))); $column++; }
					else if($field_details[$i.":".$aflag] == "inv_flag"){ $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $sales_details[1]); $column++; }
					else if($field_details[$i.":".$aflag] == "binv_flag"){ $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $sales_details[2]); $column++; }
					else if($field_details[$i.":".$aflag] == "vendor_flag"){ $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $cus_name[$sales_details[3]]); $column++; }
					else if($field_details[$i.":".$aflag] == "item_flag"){ $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $item_name[$sales_details[7]]); $column++; }
					else if($field_details[$i.":".$aflag] == "jals_flag"){ $p_jals = round($sales_details[4],2); $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $p_jals); $column++; }
					else if($field_details[$i.":".$aflag] == "birds_flag"){ $p_birds = round($sales_details[8],2); $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $p_birds); $column++; }
					else if($field_details[$i.":".$aflag] == "tweight_flag"){ $p_tweight = round($sales_details[5],2); $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $p_tweight); $column++; }
					else if($field_details[$i.":".$aflag] == "eweight_flag"){ $p_eweight = round($sales_details[6],2); $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $p_eweight); $column++; }
					else if($field_details[$i.":".$aflag] == "nweight_flag"){ $p_nweight = round($sales_details[9],2); $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $p_nweight); $column++; }
					else if($field_details[$i.":".$aflag] == "aweight_flag"){ if($sales_details[8] == 0 || $sales_details[8] == "" || $sales_details[8] == NULL){ $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, "0"); $column++; } else{ $p_aweight = round(($sales_details[9] / $sales_details[8]),2); $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $p_aweight); $column++; } }
					else if($field_details[$i.":".$aflag] == "prate_flag"){ $prate_index = $sales_details[0]."@".$cus_group[$sales_details[3]]; $p_prate = round($prates[$prate_index]); $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $p_prate); $column++; }
					else if($field_details[$i.":".$aflag] == "price_flag"){ $p_price = round($sales_details[10],2); $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $p_price); $column++; }
					else if($field_details[$i.":".$aflag] == "tamt_flag"){ $p_tamt = round($sales_details[11],2); $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $p_tamt); $column++; }
					else if($field_details[$i.":".$aflag] == "sector_flag"){ $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $sector_name[$sales_details[16]]); $column++; }
					else if($field_details[$i.":".$aflag] == "remarks_flag"){ $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $sales_details[20]); $column++; }
					else if($field_details[$i.":".$aflag] == "vehicle_flag"){ $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $sales_details[21]); $column++; }
					else if($field_details[$i.":".$aflag] == "driver_flag"){ $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $sales_details[22]); $column++; }
					else if($field_details[$i.":".$aflag] == "user_flag"){ $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $user_name[$sales_details[23]]); $column++; }
					else if($field_details[$i.":".$aflag] == "discount_flag"){ $p_discount = round($sales_details[18],2); $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $p_discount); $column++; }
					else if($field_details[$i.":".$aflag] == "tcds_flag"){ $p_tcds = round($slc_tcdsamt[$sales_details[1]],2); $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $p_tcds); $column++; }
					else if($field_details[$i.":".$aflag] == "cr_flag"){ $p_famt = round($slc_finaltotal[$sales_details[1]],2); $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $p_famt); $column++; }
					else if($field_details[$i.":".$aflag] == "dr_flag"){ $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, ""); $column++; }
					else if($field_details[$i.":".$aflag] == "rb_flag"){ $p_rb_amt = round($rb_amt,2); $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $p_rb_amt); $column++; }
					else{ }
				}
			}
			else{
				for($i = 1;$i <= $col_count;$i++){
					if($field_details[$i.":".$aflag] == "date_flag"){ $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, date("d.m.Y",strtotime($sales_details[0]))); $column++; }
					else if($field_details[$i.":".$aflag] == "inv_flag"){ $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $sales_details[1]); $column++; }
					else if($field_details[$i.":".$aflag] == "binv_flag"){ $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $sales_details[2]); $column++; }
					else if($field_details[$i.":".$aflag] == "vendor_flag"){ $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $cus_name[$sales_details[3]]); $column++; }
					else if($field_details[$i.":".$aflag] == "item_flag"){ $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $item_name[$sales_details[7]]); $column++; }
					else if($field_details[$i.":".$aflag] == "jals_flag"){ $p_jals = round($sales_details[4],2); $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $p_jals); $column++; }
					else if($field_details[$i.":".$aflag] == "birds_flag"){ $p_birds = round($sales_details[8],2); $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $p_birds); $column++; }
					else if($field_details[$i.":".$aflag] == "tweight_flag"){ $p_tweight = round($sales_details[5],2); $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $p_tweight); $column++; }
					else if($field_details[$i.":".$aflag] == "eweight_flag"){ $p_eweight = round($sales_details[6],2); $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $p_eweight); $column++; }
					else if($field_details[$i.":".$aflag] == "nweight_flag"){ $p_nweight = round($sales_details[9],2); $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $p_nweight); $column++; }
					else if($field_details[$i.":".$aflag] == "aweight_flag"){ if($sales_details[8] == 0 || $sales_details[8] == "" || $sales_details[8] == NULL){ $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, "0"); $column++; } else{ $p_aweight = round(($sales_details[9] / $sales_details[8]),2); $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $p_aweight); $column++; } }
					else if($field_details[$i.":".$aflag] == "prate_flag"){ $prate_index = $sales_details[0]."@".$cus_group[$sales_details[3]]; $p_prate = round($prates[$prate_index]); $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $p_prate); $column++; }
					else if($field_details[$i.":".$aflag] == "price_flag"){ $p_price = round($sales_details[10],2); $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $p_price); $column++; }
					else if($field_details[$i.":".$aflag] == "tamt_flag"){ $p_tamt = round($sales_details[11],2); $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $p_tamt); $column++; }
					else if($field_details[$i.":".$aflag] == "sector_flag"){ $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $sector_name[$sales_details[16]]); $column++; }
					else if($field_details[$i.":".$aflag] == "remarks_flag"){ $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $sales_details[20]); $column++; }
					else if($field_details[$i.":".$aflag] == "vehicle_flag"){ $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $sales_details[21]); $column++; }
					else if($field_details[$i.":".$aflag] == "driver_flag"){ $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $sales_details[22]); $column++; }
					else if($field_details[$i.":".$aflag] == "user_flag"){ $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $user_name[$sales_details[23]]); $column++; }
					else if($field_details[$i.":".$aflag] == "discount_flag"){ $p_discount = round($sales_details[18],2); $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $p_discount); $column++; }
					else if($field_details[$i.":".$aflag] == "tcds_flag"){ $p_tcds = round($slc_tcdsamt[$sales_details[1]],2); $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, ""); $column++; }
					else if($field_details[$i.":".$aflag] == "cr_flag"){ $p_famt = round($slc_finaltotal[$sales_details[1]],2); $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, ""); $column++; }
					else if($field_details[$i.":".$aflag] == "dr_flag"){ $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, ""); $column++; }
					else if($field_details[$i.":".$aflag] == "rb_flag"){ $p_rb_amt = round($rb_amt,2); $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, ""); $column++; }
					else{ }
				}
			}

			$tot_farm_wt += (float)$sales_details[25];
			$tot_net_wt += (float)$sales_details[9];

			$tbcount = $tbcount + (float)$sales_details[8];
			$tjcount = $tjcount + (float)$sales_details[4];
			$tncount = $tncount + (float)$sales_details[9];
			$twcount = $twcount + (float)$sales_details[5];
			$tecount = $tecount + (float)$sales_details[6];
			$tdcount = $tdcount + (float)$sales_details[18];
			$ttcount = $ttcount + (float)$sales_details[19];
			$tfritcount = $tfritcount + (float)$sales_details[24];
			
		}
	}
}

$row++; $column = 0;
$objPHPExcel->setActiveSheetIndex()->mergeCells($az[$column]."".$row.":".$az[$bwtd_det_col]."".$row);
$objPHPExcel->setActiveSheetIndex()->setCellValue("A".$row, "Total");
$column = $bwtd_det_col + 1;
for($i = 1;$i <= $col_count;$i++){
	if($field_details[$i.":".$aflag] == "jals_flag"){ $tjcount = round($tjcount,2); $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $tjcount); $column++; }
	else if($field_details[$i.":".$aflag] == "birds_flag"){ $tbcount = round($tbcount,2); $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $tbcount); $column++; }
	else if($field_details[$i.":".$aflag] == "tweight_flag"){ $twcount = round($twcount,2); $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $twcount); $column++; }
	else if($field_details[$i.":".$aflag] == "eweight_flag"){ $tecount = round($tecount,2); $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $tecount); $column++; }
	else if($field_details[$i.":".$aflag] == "nweight_flag"){ $tncount = round($tncount,2); $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $tncount); $column++; }
	else if($field_details[$i.":".$aflag] == "aweight_flag"){
		if($tbcount > 0){
			$bwavgweight = round(($tncount / $tbcount),2);
		}
		else{
			$bwavgweight = 0;
		}
		
		$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $bwavgweight);
		$column++;
	}
	else if($field_details[$i.":".$aflag] == "prate_flag"){ $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, ""); $column++; }
	else if($field_details[$i.":".$aflag] == "price_flag"){
		if($tncount > 0){
			$bwavgprice = round(($fst_famt / $tncount),2);
		}
		else{
			$bwavgprice = 0;
		}
		
		$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $bwavgprice);
		$column++;
	}
	else if($field_details[$i.":".$aflag] == "tcds_flag"){ $ft_tcds = round($ft_tcds,2); $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $ft_tcds); $column++; }
	else if($field_details[$i.":".$aflag] == "user_flag"){ $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, ""); $column++; }
	else if($field_details[$i.":".$aflag] == "discount_flag"){ $tdcount = round($tdcount,2); $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $tdcount); $column++; }
	else if($field_details[$i.":".$aflag] == "tamt_flag"){ $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $tacount); $column++; }
	else if($field_details[$i.":".$aflag] == "sector_flag"){ $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, ""); $column++; }
	else if($field_details[$i.":".$aflag] == "remarks_flag"){ $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, ""); $column++; }
	else if($field_details[$i.":".$aflag] == "vehicle_flag"){ $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, ""); $column++; }
	else if($field_details[$i.":".$aflag] == "driver_flag"){ $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, ""); $column++; }
	else if($field_details[$i.":".$aflag] == "cr_flag"){ $bwcramt = round(($fst_famt + $fdt_famt),2); $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $bwcramt); $column++; }
	else if($field_details[$i.":".$aflag] == "dr_flag"){ $bwdramt = round(($frt_famt + $fct_famt),2); $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, $bwdramt); $column++; }
	else if($field_details[$i.":".$aflag] == "rb_flag"){ $objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column]."".$row, ""); $column++; }
	else{ }
}

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('SalesReportMaster');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex();
// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="SalesReport.xls"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
echo "<script> window.close(); </script>";
exit;

?>