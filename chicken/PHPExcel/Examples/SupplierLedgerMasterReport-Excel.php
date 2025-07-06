<?php
//SupplierLedgerMasterReport-Excel.php
session_start();
include "../../newConfig.php";
$dbname = $_SESSION['dbase'];
$az[0] = "A";
$az[1] = "B";
$az[2] = "C";
$az[3] = "D";
$az[4] = "E";
$az[5] = "F";
$az[6] = "G";
$az[7] = "H";
$az[8] = "I";
$az[9] = "J";
$az[10] = "K";
$az[11] = "L";
$az[12] = "M";
$az[13] = "N";
$az[14] = "O";
$az[15] = "P";
$az[16] = "Q";
$az[17] = "R";
$az[18] = "S";
$az[19] = "T";
$az[20] = "U";
$az[21] = "V";
$az[22] = "W";
$az[23] = "X";
$az[24] = "Y";
$az[25] = "Z";
$fdate = date("Y-m-d", strtotime($_GET['fromdate']));
$tdate = date("Y-m-d", strtotime($_GET['todate']));
$sname = $_GET['sname'];
$cid = $_GET['cid'];
$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE 'S' AND `active` = '1' ORDER BY `name` ASC";
$query = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($query)) {
	$sup_name[$row['code']] = $row['name'];
	$sup_code[$row['code']] = $row['code'];
	$cus_mobile[$row['code']] = $row['mobileno'];
	$cus_group[$row['code']] = $row['groupcode'];
	$obdate[$row['code']] = $row['obdate'];
	$obtype[$row['code']] = $row['obtype'];
	$obamt[$row['code']] = $row['obamt'];
}

$sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $cus_code = $cus_name = array();
while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; }

$sql = "SELECT * FROM `item_details` WHERE `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($query)) {
	$item_name[$row['code']] = $row['description'];
	$item_code[$row['code']] = $row['code'];
}
$sql = "SELECT * FROM `acc_coa` WHERE `active` = '1' AND `ctype` IN ('Cash','Bank') ORDER BY `description` ASC";
$query = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($query)) {
	$coaname[$row['code']] = $row['description'];
	$coacode[$row['code']] = $row['code'];
}

$sql = "SELECT * FROM `chicken_supplier_branch` WHERE `dflag` = '0' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $sbrh_code = $sbrh_name =  $bsup_name = array();
while($row = mysqli_fetch_assoc($query)){ $sbrh_code[$row['code']] = $row['code']; $sbrh_name[$row['code']] = $row['description']; $bsup_name[$row['code']] = $row['sup_code']; }

$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($query)) {
	$sector_name[$row['code']] = $row['description'];
	$sector_code[$row['code']] = $row['code'];
}
$sql = "SELECT * FROM `master_reportfields` WHERE `code` = '$cid' AND `active` = '1'";
$query = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($query)) {
	$type = "type";
	$code = "code";
	$pattern = "pattern";
	$field_details[$row['date_flag']] = "date_flag";
	$field_details[$row['inv_flag']] = "inv_flag";
	$field_details[$row['binv_flag']] = "binv_flag";
	$field_details[$row['vendor_flag']] = "vendor_flag";
	$field_details[$row['supbrh_flag']] = "supbrh_flag"; $sbrh = explode(":",$row['supbrh_flag']); if($sbrh[1] == 1 || $sbrh[1] == "1"){ $supbrh_flag = 1; }
	$field_details[$row['purcus_flag']] = "purcus_flag";
	$field_details[$row['item_flag']] = "item_flag";
	$field_details[$row['jals_flag']] = "jals_flag";
	$field_details[$row['birds_flag']] = "birds_flag";
	$field_details[$row['tweight_flag']] = "tweight_flag";
	$field_details[$row['eweight_flag']] = "eweight_flag";
	$field_details[$row['nweight_flag']] = "nweight_flag";
	$field_details[$row['aweight_flag']] = "aweight_flag";
	$field_details[$row['prate_flag']] = "prate_flag";
	$field_details[$row['price_flag']] = "price_flag";
	$field_details[$row['tcds_flag']] = "tcds_flag";
	$field_details[$row['discount_flag']] = "discount_flag";
	$field_details[$row['tamt_flag']] = "tamt_flag";
	$field_details[$row['sector_flag']] = "sector_flag";
	$field_details[$row['remarks_flag']] = "remarks_flag";
	$field_details[$row['vehicle_flag']] = "vehicle_flag";
	$field_details[$row['driver_flag']] = "driver_flag";
	//$field_details[$row['denom_flag']] = "denom_flag";
	$field_details[$row['cr_flag']] = "cr_flag";
	$field_details[$row['dr_flag']] = "dr_flag";
	$field_details[$row['rb_flag']] = "rb_flag";
	$note_flag = $row['note_flag'];
	$note_code = $row['note_code'];
	$vsign_flag = $row['vsign_flag'];
	$csign_flag = $row['csign_flag'];
	$qr_img_flag = $row['qr_img_flag'];
	$col_count = $row['count'];
}
$sql = "SELECT * FROM `main_disclaimer` WHERE `code` = '$note_code' AND `active` = '1'";
$query = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($query)) {
	$disclaimer = $row['note'];
}
if ($prate_flag == 1 || $prate_flag == "1") {
	$fdate = date("Y-m-d", strtotime($fromdate));
	$tdate = date("Y-m-d", strtotime($todate));
	$sql = "SELECT * FROM `main_dailypaperrate` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1'";
	$query = mysqli_query($conn, $sql);
	while ($row = mysqli_fetch_assoc($query)) {
		$prates[$row['date'] . "@" . $row['cgroup']] = $row['new_price'];
	}
}
$ob_purchases = $ob_payment = $ob_ccn = $ob_cdn = $rb_amt = $ob_cramt = $ob_dramt = $ob_rcv = $ob_pid = 0;
$obsql = "SELECT * FROM `pur_purchase` WHERE `date` < '$fdate' AND `vendorcode` = '$sname' AND `active` = '1' ORDER BY `invoice` ASC";
$obquery = mysqli_query($conn, $obsql);
$old_inv = "";
while ($obrow = mysqli_fetch_assoc($obquery)) {
	if ($old_inv != $obrow['invoice']) {
		$ob_purchases = $ob_purchases + $obrow['finaltotal'];
		$old_inv = $obrow['invoice'];
	}
}
$obsql = "SELECT * FROM `main_itemreturns` WHERE `date` < '$fdate' AND `vcode` = '$sname' AND `mode` = 'supplier' AND `active` = '1' AND `dflag` = '0'";
$obquery = mysqli_query($conn, $obsql);
while ($obrow = mysqli_fetch_assoc($obquery)) {
	$ob_returns = $ob_returns + $obrow['amount'];
}
$obsql = "SELECT * FROM `pur_payments` WHERE `date` < '$fdate' AND `ccode` = '$sname' AND `active` = '1'";
$obquery = mysqli_query($conn, $obsql);
while ($obrow = mysqli_fetch_assoc($obquery)) {
	$ob_payment = $ob_payment + $obrow['amount'];
}
$obsql = "SELECT * FROM `main_crdrnote` WHERE `date` < '$fdate' AND `ccode` = '$sname' AND `mode` IN ('SCN','SDN') AND `active` = '1'";
$obquery = mysqli_query($conn, $obsql);
while ($obrow = mysqli_fetch_assoc($obquery)) {
	if ($obrow['mode'] == "SCN") {
		$ob_ccn = $ob_ccn + $obrow['amount'];
	} else {
		$ob_cdn = $ob_cdn + $obrow['amount'];
	}
}
if ($obtype[$sname] == "Cr") {
	$ob_cramt = $obamt[$sname];
} else {
	$ob_dramt = $obamt[$sname];
}
$ob_rcv = $ob_purchases + $ob_ccn + $ob_cramt;
$ob_pid = $ob_payment + $ob_returns + $ob_cdn + $ob_dramt;

//purchases
$sii_count = array();
$sql = "SELECT * FROM `pur_purchase` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `vendorcode` = '$sname' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`invoice` ASC";
$query = mysqli_query($conn, $sql); $link_trnums = $inv_cus_code = array();
$i = 0;
while ($row = mysqli_fetch_assoc($query)) {
	$i = $i + 1;
	$purchases[$row['date'] . "@" . $i] = $row['date'] . "@" . $row['invoice'] . "@" . $row['bookinvoice'] . "@" . $row['vendorcode'] . "@" . $row['jals'] . "@" . $row['totalweight'] . "@" . $row['emptyweight'] . "@" . $row['itemcode'] . "@" . $row['birds'] . "@" . $row['netweight'] . "@" . $row['itemprice'] . "@" . $row['totalamt'] . "@" . $row['tcdsper'] . "@" . $row['tcdsamt'] . "@" . $row['roundoff'] . "@" . $row['finaltotal'] . "@" . $row['warehouse'] . "@" . $row['narration'] . "@" . $row['discountamt'] . "@" . $row['taxamount'] . "@" . $row['remarks'] . "@" . $row['vehiclecode'] . "@" . $row['drivercode'] . "@" . $row['supbrh_code'];
	if ($sii_count[$row['invoice']] == "" || $sii_count[$row['invoice']] == NULL || $sii_count[$row['invoice']] == 0) {
		$sii_count[$row['invoice']] = 1;
		if ($row['tcdsamt'] == "" || $row['tcdsamt'] == NULL) {
			$slc_tcdsamt[$row['invoice']] = 0.00;
		} else {
			$slc_tcdsamt[$row['invoice']] = $row['tcdsamt'];
		}
		if ($row['roundoff'] == "" || $row['roundoff'] == NULL) {
			$slc_roundoff[$row['invoice']] = 0.00;
		} else {
			if (($row['itotal'] + $row['tcdsamt']) <= $row['finaltotal']) {
				$slc_roundoff[$row['invoice']] = $row['roundoff'];
			} else {
				$slc_roundoff[$row['invoice']] = -1 * ($row['roundoff']);
			}
		}
		$slc_finaltotal[$row['invoice']] = $row['finaltotal'];
	} else {
		$sii_count[$row['invoice']] = $sii_count[$row['invoice']] + 1;
	}
	$link_trnums[$row['link_trnum']] = $row['link_trnum'];
}
//Sales
if(sizeof($link_trnums) > 0){
	$tr_list = implode("','",$link_trnums);
	$sql = "SELECT * FROM `customer_sales` WHERE `invoice` IN ('$tr_list') AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `invoice` ASC";
	$query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){
		$key = $row['link_trnum'];
		$inv_cus_code[$key] = $row['customercode'];
	}
}

//Payments
$paysql = "SELECT * FROM `pur_payments` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `ccode` = '$sname' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'";
$payquery = mysqli_query($conn, $paysql);
$i = 0;
while ($row = mysqli_fetch_assoc($payquery)) {
	$i = $i + 1;
	$payments[$row['date'] . "@" . $i] = $row['trnum'] . "@" . $row['date'] . "@" . $row['ccode'] . "@" . $row['docno'] . "@" . $row['mode'] . "@" . $row['method'] . "@" . $row['type'] . "@" . $row['rcode'] . "@" . $row['cdate'] . "@" . $row['cno'] . "@" . $row['amount'] . "@" . $row['amtinwords'] . "@" . $row['vtype'] . "@" . $row['warehouse'] . "@" . $row['remarks'];
}
//Returns
$rtnsql = "SELECT * FROM `main_itemreturns` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `vcode` = '$sname' AND `mode` = 'supplier' AND `active` = '1' AND `dflag` = '0'";
$rtnquery = mysqli_query($conn, $rtnsql);
$i = 0;
while ($row = mysqli_fetch_assoc($rtnquery)) {
	$avgwt = 0;
	if ($row['birds'] != "" || $row['birds'] != 0 || $row['birds'] != "0.00") {
		$avgwt = $row['quantity'] / $row['birds'];
	} else {
		$avgwt = 0;
	}
	$i = $i + 1;
	$returns[$row['date'] . "@" . $i] = $row['trnum'] . "@" . $row['date'] . "@" . $row['vcode'] . "@" . $row['inv_trnum'] . "@" . $row['itemcode'] . "@" . $row['jals'] . "@" . $row['birds'] . "@" . $row['quantity'] . "@" . $avgwt . "@" . $row['price'] . "@" . $row['amount'] . "@" . $row['warehouse'];
}
//CRDR NOTE
$crdrsql = "SELECT * FROM `main_crdrnote` WHERE `mode` IN ('SDN','SCN') AND `date` >= '$fdate' AND `date` <= '$tdate' AND `ccode` = '$sname' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'";
$crdrquery = mysqli_query($conn, $crdrsql);
$i = $j = 0;
while ($row = mysqli_fetch_assoc($crdrquery)) {
	if ($row['mode'] == "SCN") {
		$i = $i + 1;
		$ccns[$row['date'] . "@" . $i] = $row['mode'] . "@" . $row['trnum'] . "@" . $row['date'] . "@" . $row['ccode'] . "@" . $row['docno'] . "@" . $row['coa'] . "@" . $row['crdr'] . "@" . $row['amount'] . "@" . $row['balance'] . "@" . $row['amtinwords'] . "@" . $row['vtype'] . "@" . $row['warehouse'] . "@" . $row['remarks'];
	} else if ($row['mode'] == "SDN") {
		$j = $j + 1;
		$cdns[$row['date'] . "@" . $j] = $row['mode'] . "@" . $row['trnum'] . "@" . $row['date'] . "@" . $row['ccode'] . "@" . $row['docno'] . "@" . $row['coa'] . "@" . $row['crdr'] . "@" . $row['amount'] . "@" . $row['balance'] . "@" . $row['amtinwords'] . "@" . $row['vtype'] . "@" . $row['warehouse'] . "@" . $row['remarks'];
	} else {
	}
}
require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';
$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
	->setLastModifiedBy("Maarten Balliauw")
	->setTitle("Office 2007 XLSX Test Document")
	->setSubject("Office 2007 XLSX Test Document")
	->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
	->setKeywords("office 2007 openxml php")
	->setCategory("SupplierLedgerMasterReportExcel");
$prev_bal_col = $item_det_col = $bwtd_det_col = $grnd_tot_col = $clsb_tot_col = -1;
$j = 0;
$active_flag = 1;
for ($i = 1; $i <= $col_count; $i++) {
	if ($field_details[$i . ":" . $active_flag] == "date_flag") {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j] . "1", 'Date');
		$j++;
		$prev_bal_col++;
		$bwtd_det_col++;
		$grnd_tot_col++;
		$clsb_tot_col++;
	} else if ($field_details[$i . ":" . $active_flag] == "inv_flag") {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j] . "1", 'Invoice');
		$j++;
		$prev_bal_col++;
		$bwtd_det_col++;
		$grnd_tot_col++;
		$clsb_tot_col++;
	} else if ($field_details[$i . ":" . $active_flag] == "binv_flag") {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j] . "1", 'Book Invoice');
		$j++;
		$prev_bal_col++;
		$bwtd_det_col++;
		$grnd_tot_col++;
		$clsb_tot_col++;
	}
	else if ($field_details[$i . ":" . $active_flag] == "vendor_flag") {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j] . "1", 'Supplier');
		$j++;
		$prev_bal_col++;
		$bwtd_det_col++;
		$grnd_tot_col++;
		$clsb_tot_col++;
	}
	else if ($field_details[$i . ":" . $active_flag] == "supbrh_flag") {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j] . "1", 'Branch');
		$j++;
		$prev_bal_col++;
		$bwtd_det_col++;
		$grnd_tot_col++;
		$clsb_tot_col++;
	}
	else if ($field_details[$i . ":" . $active_flag] == "purcus_flag") {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j] . "1", 'Customer');
		$j++;
		$prev_bal_col++;
		$bwtd_det_col++;
		$grnd_tot_col++;
		$clsb_tot_col++;
	}
	else if ($field_details[$i . ":" . $active_flag] == "item_flag") {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j] . "1", 'Item');
		$j++;
		$prev_bal_col++;
		$bwtd_det_col++;
		$grnd_tot_col++;
		$clsb_tot_col++;
	} else if ($field_details[$i . ":" . $active_flag] == "jals_flag") {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j] . "1", 'Jals');
		$j++;
		$item_det_col++;
		$grnd_tot_col++;
		$clsb_tot_col++;
	} else if ($field_details[$i . ":" . $active_flag] == "birds_flag") {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j] . "1", 'Birds');
		$j++;
		$item_det_col++;
		$grnd_tot_col++;
		$clsb_tot_col++;
	} else if ($field_details[$i . ":" . $active_flag] == "tweight_flag") {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j] . "1", 'T. Weight');
		$j++;
		$item_det_col++;
		$grnd_tot_col++;
		$clsb_tot_col++;
	} else if ($field_details[$i . ":" . $active_flag] == "eweight_flag") {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j] . "1", 'E. Weight');
		$j++;
		$item_det_col++;
		$grnd_tot_col++;
		$clsb_tot_col++;
	} else if ($field_details[$i . ":" . $active_flag] == "nweight_flag") {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j] . "1", 'N. Weight');
		$j++;
		$item_det_col++;
		$grnd_tot_col++;
		$clsb_tot_col++;
	} else if ($field_details[$i . ":" . $active_flag] == "aweight_flag") {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j] . "1", 'Avg. Weight');
		$j++;
		$item_det_col++;
		$grnd_tot_col++;
		$clsb_tot_col++;
	} else if ($field_details[$i . ":" . $active_flag] == "prate_flag") {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j] . "1", 'Paper Rate');
		$j++;
		$item_det_col++;
		$grnd_tot_col++;
		$clsb_tot_col++;
	} else if ($field_details[$i . ":" . $active_flag] == "price_flag") {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j] . "1", 'Price');
		$j++;
		$item_det_col++;
		$grnd_tot_col++;
		$clsb_tot_col++;
	} else if ($field_details[$i . ":" . $active_flag] == "tcds_flag") {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j] . "1", 'TCS');
		$j++;
		$item_det_col++;
		$grnd_tot_col++;
		$clsb_tot_col++;
	} else if ($field_details[$i . ":" . $active_flag] == "discount_flag") {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j] . "1", 'Discount');
		$j++;
		$item_det_col++;
		$grnd_tot_col++;
		$clsb_tot_col++;
	} else if ($field_details[$i . ":" . $active_flag] == "tamt_flag") {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j] . "1", 'Total Amount');
		$j++;
		$item_det_col++;
		$grnd_tot_col++;
		$clsb_tot_col++;
	} else if ($field_details[$i . ":" . $active_flag] == "sector_flag") {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j] . "1", 'Warehouse');
		$j++;
		$item_det_col++;
		$grnd_tot_col++;
		$clsb_tot_col++;
	} else if ($field_details[$i . ":" . $active_flag] == "remarks_flag") {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j] . "1", 'Remarks');
		$j++;
		$item_det_col++;
		$grnd_tot_col++;
		$clsb_tot_col++;
	} else if ($field_details[$i . ":" . $active_flag] == "vehicle_flag") {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j] . "1", 'Vehicle');
		$j++;
		$item_det_col++;
		$grnd_tot_col++;
		$clsb_tot_col++;
	} else if ($field_details[$i . ":" . $active_flag] == "driver_flag") {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j] . "1", 'Driver');
		$j++;
		$item_det_col++;
		$grnd_tot_col++;
		$clsb_tot_col++;
	} else if ($field_details[$i . ":" . $active_flag] == "cr_flag") {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j] . "1", 'Purchase');
		$j++;
	} else if ($field_details[$i . ":" . $active_flag] == "dr_flag") {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j] . "1", 'Payments');
		$j++;
	} else if ($field_details[$i . ":" . $active_flag] == "rb_flag") {
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($az[$j] . "1", 'Running Balance');
		$j++;
	} else {
	}
}
if ($ob_rcv >= $ob_pid) {
	$prev_balance = $ob_rcv - $ob_pid;
	$prev_col2 = $prev_bal_col + $item_det_col + 1;
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue("A2", 'Previous Balance')
		->mergeCells("A2:" . $az[$prev_bal_col] . "2")
		->mergeCells($az[$prev_bal_col + 1] . "2:" . $az[$prev_col2] . "2")
		->setCellValue($az[$prev_col2 + 1] . "2", $prev_balance)
		->setCellValue($az[$prev_col2 + 3] . "2", $prev_balance);
	$rb_amt = $rb_amt + ($ob_rcv - $ob_pid);
	$ob_rev_amt = $ob_rcv - $ob_pid;
	$ob_pid_amt = 0;
} else {
	$prev_balance = $ob_rcv - $ob_pid;
	$prev_col2 = $prev_bal_col + $item_det_col + 1;
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue("A2", 'Previous Balance')
		->mergeCells("A2:" . $az[$prev_bal_col] . "2")
		->mergeCells($az[$prev_bal_col + 1] . "2:" . $az[$prev_col2] . "2")
		->setCellValue($az[$prev_col2 + 2] . "2", $prev_balance)
		->setCellValue($az[$prev_col2 + 3] . "2", $prev_balance);
	$rb_amt = $rb_amt + ($ob_rcv - $ob_pid);
	$ob_pid_amt = $ob_pid - $ob_rcv;
	$ob_rev_amt = 0;
}
//echo $az[$prev_col2]."--".$prev_col2;
$fdate = strtotime($fdate);
$tdate = strtotime($tdate);
$i = 0;
$exi_inv = "";
$row = 2;
$column = 0;
for ($currentDate = $fdate; $currentDate <= $tdate; $currentDate += (86400)) {
	$date_asc = date('Y-m-d', $currentDate);
	if($purchases != null){
		$ccount = sizeof($purchases);
	}else{
		$ccount = 0;
	}
	
	for ($j = 0; $j <= $ccount; $j++) {
		if ($purchases[$date_asc . "@" . $j] != "") {
			$row++;
			$column = 0;
			$purchases_details = explode("@", $purchases[$date_asc . "@" . $j]);

			if(empty($inv_cus_code[$purchases_details[1]]) || $inv_cus_code[$purchases_details[1]] == ""){ $cname = ""; }
			else{ $cname = $cus_name[$inv_cus_code[$purchases_details[1]]]; }
			
			if ($exi_inv != $purchases_details[1]) {
				$exi_inv = $purchases_details[1];
				$rb_amt = $rb_amt + $slc_finaltotal[$purchases_details[1]];
				$ft_tcds = $ft_tcds + $slc_tcdsamt[$purchases_details[1]];
				$ft_roundoff = $ft_roundoff + $slc_roundoff[$purchases_details[1]];
				$fst_famt = $fst_famt + $slc_finaltotal[$purchases_details[1]];
				for ($i = 1; $i <= $col_count; $i++) {
					if ($field_details[$i . ":" . $active_flag] == "date_flag") {
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, date("d.m.Y", strtotime($purchases_details[0])));
						$column++;
					} else if ($field_details[$i . ":" . $active_flag] == "inv_flag") {
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $purchases_details[1]);
						$column++;
					} else if ($field_details[$i . ":" . $active_flag] == "binv_flag") {
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $purchases_details[2]);
						$column++;
					}
					else if ($field_details[$i . ":" . $active_flag] == "vendor_flag") {
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $sup_name[$purchases_details[3]]);
						$column++;
					}
					else if ($field_details[$i . ":" . $active_flag] == "supbrh_flag") {
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $sbrh_name[$purchases_details[23]]);
						$column++;
					}
					else if ($field_details[$i . ":" . $active_flag] == "purcus_flag") {
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $cname);
						$column++;
					}
					else if ($field_details[$i . ":" . $active_flag] == "item_flag") {
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $item_name[$purchases_details[7]]);
						$column++;
					} else if ($field_details[$i . ":" . $active_flag] == "jals_flag") {
						$p_jals = round($purchases_details[4], 2);
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $p_jals);
						$column++;
					} else if ($field_details[$i . ":" . $active_flag] == "birds_flag") {
						$p_birds = round($purchases_details[8], 2);
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $p_birds);
						$column++;
					} else if ($field_details[$i . ":" . $active_flag] == "tweight_flag") {
						$p_tweight = round($purchases_details[5], 2);
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $p_tweight);
						$column++;
					} else if ($field_details[$i . ":" . $active_flag] == "eweight_flag") {
						$p_eweight = round($purchases_details[6], 2);
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $p_eweight);
						$column++;
					} else if ($field_details[$i . ":" . $active_flag] == "nweight_flag") {
						$p_nweight = round($purchases_details[9], 2);
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $p_nweight);
						$column++;
					} else if ($field_details[$i . ":" . $active_flag] == "aweight_flag") {
						if ($purchases_details[8] == 0 || $purchases_details[8] == "" || $purchases_details[8] == NULL) {
							$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "0");
							$column++;
						} else {
							if((float)$purchases_details[8] > 0){ $p_aweight = round(($purchases_details[9] / $purchases_details[8]), 2); } else{ $p_aweight = 0; }
							$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $p_aweight);
							$column++;
						}
					} else if ($field_details[$i . ":" . $active_flag] == "prate_flag") {
						$prate_index = $purchases_details[0] . "@" . $cus_group[$purchases_details[3]];
						$p_prate = round($prates[$prate_index]);
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $p_prate);
						$column++;
					} else if ($field_details[$i . ":" . $active_flag] == "price_flag") {
						$p_price = round($purchases_details[10], 2);
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $p_price);
						$column++;
					} else if ($field_details[$i . ":" . $active_flag] == "tamt_flag") {
						$p_tamt = round($purchases_details[11], 2);
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $p_tamt);
						$column++;
					} else if ($field_details[$i . ":" . $active_flag] == "sector_flag") {
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $sector_name[$purchases_details[16]]);
						$column++;
					} else if ($field_details[$i . ":" . $active_flag] == "remarks_flag") {
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $purchases_details[20]);
						$column++;
					} else if ($field_details[$i . ":" . $active_flag] == "vehicle_flag") {
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $purchases_details[21]);
						$column++;
					} else if ($field_details[$i . ":" . $active_flag] == "driver_flag") {
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $purchases_details[22]);
						$column++;
					} else if ($field_details[$i . ":" . $active_flag] == "discount_flag") {
						$p_discount = round($purchases_details[18], 2);
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $p_discount);
						$column++;
					} else if ($field_details[$i . ":" . $active_flag] == "tcds_flag") {
						$p_tcds = round($slc_tcdsamt[$purchases_details[1]], 2);
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $p_tcds);
						$column++;
					} else if ($field_details[$i . ":" . $active_flag] == "cr_flag") {
						$p_famt = round($slc_finaltotal[$purchases_details[1]], 2);
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $p_famt);
						$column++;
					} else if ($field_details[$i . ":" . $active_flag] == "dr_flag") {
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
						$column++;
					} else if ($field_details[$i . ":" . $active_flag] == "rb_flag") {
						$p_rb_amt = round($rb_amt, 2);
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $p_rb_amt);
						$column++;
					} else {
					}
				}
			} else {
				for ($i = 1; $i <= $col_count; $i++) {
					if ($field_details[$i . ":" . $active_flag] == "date_flag") {
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, date("d.m.Y", strtotime($purchases_details[0])));
						$column++;
					} else if ($field_details[$i . ":" . $active_flag] == "inv_flag") {
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $purchases_details[1]);
						$column++;
					} else if ($field_details[$i . ":" . $active_flag] == "binv_flag") {
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $purchases_details[2]);
						$column++;
					}
					else if ($field_details[$i . ":" . $active_flag] == "vendor_flag") {
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $sup_name[$purchases_details[3]]);
						$column++;
					}
					else if ($field_details[$i . ":" . $active_flag] == "supbrh_flag") {
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
						$column++;
					}
					else if ($field_details[$i . ":" . $active_flag] == "purcus_flag") {
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
						$column++;
					}
					else if ($field_details[$i . ":" . $active_flag] == "item_flag") {
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $item_name[$purchases_details[7]]);
						$column++;
					} else if ($field_details[$i . ":" . $active_flag] == "jals_flag") {
						$p_jals = round($purchases_details[4], 2);
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $p_jals);
						$column++;
					} else if ($field_details[$i . ":" . $active_flag] == "birds_flag") {
						$p_birds = round($purchases_details[8], 2);
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $p_birds);
						$column++;
					} else if ($field_details[$i . ":" . $active_flag] == "tweight_flag") {
						$p_tweight = round($purchases_details[5], 2);
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $p_tweight);
						$column++;
					} else if ($field_details[$i . ":" . $active_flag] == "eweight_flag") {
						$p_eweight = round($purchases_details[6], 2);
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $p_eweight);
						$column++;
					} else if ($field_details[$i . ":" . $active_flag] == "nweight_flag") {
						$p_nweight = round($purchases_details[9], 2);
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $p_nweight);
						$column++;
					} else if ($field_details[$i . ":" . $active_flag] == "aweight_flag") {
						if ($purchases_details[8] == 0 || $purchases_details[8] == "" || $purchases_details[8] == NULL) {
							$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "0");
							$column++;
						} else {
							if((float)$purchases_details[8] > 0){ $p_aweight = round(($purchases_details[9] / $purchases_details[8]), 2); } else{ $p_aweight = 0; }
							$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $p_aweight);
							$column++;
						}
					} else if ($field_details[$i . ":" . $active_flag] == "prate_flag") {
						$prate_index = $purchases_details[0] . "@" . $cus_group[$purchases_details[3]];
						$p_prate = round($prates[$prate_index]);
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $p_prate);
						$column++;
					} else if ($field_details[$i . ":" . $active_flag] == "price_flag") {
						$p_price = round($purchases_details[10], 2);
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $p_price);
						$column++;
					} else if ($field_details[$i . ":" . $active_flag] == "tamt_flag") {
						$p_tamt = round($purchases_details[11], 2);
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $p_tamt);
						$column++;
					} else if ($field_details[$i . ":" . $active_flag] == "sector_flag") {
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $sector_name[$purchases_details[16]]);
						$column++;
					} else if ($field_details[$i . ":" . $active_flag] == "remarks_flag") {
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $purchases_details[20]);
						$column++;
					} else if ($field_details[$i . ":" . $active_flag] == "vehicle_flag") {
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $purchases_details[21]);
						$column++;
					} else if ($field_details[$i . ":" . $active_flag] == "driver_flag") {
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $purchases_details[22]);
						$column++;
					} else if ($field_details[$i . ":" . $active_flag] == "discount_flag") {
						$p_discount = round($purchases_details[18], 2);
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $p_discount);
						$column++;
					} else if ($field_details[$i . ":" . $active_flag] == "tcds_flag") {
						$p_tcds = round($slc_tcdsamt[$purchases_details[1]], 2);
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
						$column++;
					} else if ($field_details[$i . ":" . $active_flag] == "cr_flag") {
						$p_famt = round($slc_finaltotal[$purchases_details[1]], 2);
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
						$column++;
					} else if ($field_details[$i . ":" . $active_flag] == "dr_flag") {
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
						$column++;
					} else if ($field_details[$i . ":" . $active_flag] == "rb_flag") {
						$p_rb_amt = round($rb_amt, 2);
						$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
						$column++;
					} else {
					}
				}
			}
			$tbcount = $tbcount + $purchases_details[8];
			$tjcount = $tjcount + $purchases_details[4];
			$tncount = $tncount + $purchases_details[9];
			$twcount = $twcount + $purchases_details[5];
			$tecount = $tecount + $purchases_details[6];
			$tdcount = $tdcount + $purchases_details[18];
			$ttcount = $ttcount + $purchases_details[19];
			$tacount = $tacount + $purchases_details[11];
		}
	}
	if($payments != null){
		$ccount = sizeof($payments);
	}else{
		$ccount = 0;
	}
	for ($j = 0; $j <= $ccount; $j++) {
		if ($payments[$date_asc . "@" . $j] != "") {
			$row++;
			$column = 0;
			$payments_details = explode("@", $payments[$date_asc . "@" . $j]);
			$rb_amt = $rb_amt - $payments_details[10];
			$frt_famt = $frt_famt + $payments_details[10];
			for ($i = 1; $i <= $col_count; $i++) {
				if ($field_details[$i . ":" . $active_flag] == "date_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, date("d.m.Y", strtotime($payments_details[1])));
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "inv_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $payments_details[0]);
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "binv_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $payments_details[3]);
					$column++;
				}
				else if ($field_details[$i . ":" . $active_flag] == "vendor_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $sup_name[$payments_details[2]]);
					$column++;
				}
				else if ($field_details[$i . ":" . $active_flag] == "supbrh_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				}
				else if ($field_details[$i . ":" . $active_flag] == "purcus_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				}
				else if ($field_details[$i . ":" . $active_flag] == "item_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $coaname[$payments_details[5]]);
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "jals_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "birds_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "tweight_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "eweight_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "nweight_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "aweight_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "prate_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "price_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "tcds_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "discount_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "tamt_flag") {
					$pay_tamt = round($payments_details[10], 2);
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $pay_tamt);
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "sector_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $sector_name[$payments_details[13]]);
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "remarks_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $payments_details[14]);
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "vehicle_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "driver_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "cr_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "dr_flag") {
					$pay_dramt = round($payments_details[10], 2);
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $pay_dramt);
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "rb_flag") {
					$pay_rb_amt = round($rb_amt, 2);
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $pay_rb_amt);
					$column++;
				} else {
				}
			}
		} else {
		}
	}
	
	if($returns != null){
		$ccount = sizeof($returns);
	}else{
		$ccount = 0;
	}
	for ($j = 0; $j <= $ccount; $j++) {
		if ($returns[$date_asc . "@" . $j] != "") {
			$row++;
			$column = 0;
			$return_details = explode("@", $returns[$date_asc . "@" . $j]);
			$rb_amt = $rb_amt - $return_details[10];
			$frt_famt = $frt_famt + $return_details[10];
			for ($i = 1; $i <= $col_count; $i++) {
				if ($field_details[$i . ":" . $active_flag] == "date_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, date("d.m.Y", strtotime($return_details[1])));
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "inv_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $return_details[0]);
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "binv_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $return_details[3]);
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "vendor_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $sup_name[$return_details[2]]);
					$column++;
				}
				else if ($field_details[$i . ":" . $active_flag] == "supbrh_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				}
				else if ($field_details[$i . ":" . $active_flag] == "purcus_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				}
				else if ($field_details[$i . ":" . $active_flag] == "item_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $item_name[$return_details[4]]);
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "jals_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $return_details[5]);
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "birds_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $return_details[6]);
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "tweight_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "eweight_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "nweight_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $return_details[7]);
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "aweight_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $return_details[8]);
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "prate_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "price_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $return_details[9]);
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "tcds_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "discount_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "tamt_flag") {
					$pay_tamt = round($return_details[10], 2);
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $pay_tamt);
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "sector_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $sector_name[$return_details[11]]);
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "remarks_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "vehicle_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "driver_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "cr_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "dr_flag") {
					$pay_dramt = round($return_details[10], 2);
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $pay_dramt);
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "rb_flag") {
					$pay_rb_amt = round($rb_amt, 2);
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $pay_rb_amt);
					$column++;
				} else {
				}
			}
			$tjcount = $tjcount - $return_details[5];
			$tbcount = $tbcount - $return_details[6];
			$tncount = $tncount - $return_details[7];
			$tacount = $tacount - $return_details[10];
		} else {
		}
	}
	if($returns != null){
		$ccns = sizeof($ccns);
	}else{
		$ccount = 0;
	}
	for ($j = 0; $j <= $ccount; $j++) {
		if ($ccns[$date_asc . "@" . $j] != "") {
			$row++;
			$column = 0;
			$ccns_details = explode("@", $ccns[$date_asc . "@" . $j]);
			$rb_amt = $rb_amt + $ccns_details[7];
			$fct_famt = $fct_famt + $ccns_details[7];
			for ($i = 1; $i <= $col_count; $i++) {
				if ($field_details[$i . ":" . $active_flag] == "date_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, date("d.m.Y", strtotime($ccns_details[2])));
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "inv_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $ccns_details[1]);
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "binv_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $ccns_details[4]);
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "vendor_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $sup_name[$ccns_details[3]]);
					$column++;
				}
				else if ($field_details[$i . ":" . $active_flag] == "supbrh_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				}
				else if ($field_details[$i . ":" . $active_flag] == "purcus_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				}
				else if ($field_details[$i . ":" . $active_flag] == "item_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "Credit Note");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "jals_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "birds_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "tweight_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "eweight_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "nweight_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "aweight_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "prate_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "price_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "tcds_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "discount_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "tamt_flag") {
					$ccn_tamt = round($ccns_details[7], 2);
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $ccn_tamt);
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "sector_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $sector_name[$ccns_details[11]]);
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "remarks_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $ccns_details[12]);
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "vehicle_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "driver_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "cr_flag") {
					$ccn_cr_amt = round($ccns_details[7], 2);
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $ccn_cr_amt);
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "dr_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "rb_flag") {
					$ccn_rb_amt = round($rb_amt, 2);
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $ccn_rb_amt);
					$column++;
				} else {
				}
			}
		} else {
		}
	}
	if($cdns != null){
		$ccount = sizeof($cdns);
	}else{
		$ccount = 0;
	}
	for ($j = 0; $j <= $ccount; $j++) {
		if ($cdns[$date_asc . "@" . $j] != "") {
			$row++;
			$column = 0;
			$cdns_details = explode("@", $cdns[$date_asc . "@" . $j]);
			$rb_amt = $rb_amt - $cdns_details[7];
			$fdt_famt = $fdt_famt + $cdns_details[7];
			for ($i = 1; $i <= $col_count; $i++) {
				if ($field_details[$i . ":" . $active_flag] == "date_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, date("d.m.Y", strtotime($cdns_details[2])));
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "inv_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $cdns_details[1]);
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "binv_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $cdns_details[4]);
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "vendor_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $sup_name[$cdns_details[3]]);
					$column++;
				}
				else if ($field_details[$i . ":" . $active_flag] == "supbrh_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				}
				else if ($field_details[$i . ":" . $active_flag] == "purcus_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				}
				else if ($field_details[$i . ":" . $active_flag] == "item_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "Debit Note");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "jals_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "birds_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "tweight_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "eweight_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "nweight_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "aweight_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "prate_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "price_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "tcds_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "discount_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "tamt_flag") {
					$cdn_tamt = round($cdns_details[7], 2);
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $cdn_tamt);
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "sector_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $sector_name[$cdns_details[11]]);
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "remarks_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $cdns_details[12]);
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "vehicle_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "driver_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "cr_flag") {
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "dr_flag") {
					$cdn_dr_amt = round($cdns_details[7], 2);
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $cdn_dr_amt);
					$column++;
				} else if ($field_details[$i . ":" . $active_flag] == "rb_flag") {
					$cdn_rb_amt = round($rb_amt, 2);
					$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $cdn_rb_amt);
					$column++;
				} else {
				}
			}
		} else {
		}
	}
}
$row++;
$column = 0;
$objPHPExcel->setActiveSheetIndex()->mergeCells($az[$column] . "" . $row . ":" . $az[$bwtd_det_col] . "" . $row);
$objPHPExcel->setActiveSheetIndex()->setCellValue("A" . $row, "Between Days Total");
$column = $bwtd_det_col + 1;
for ($i = 1; $i <= $col_count; $i++) {
	if ($field_details[$i . ":" . $active_flag] == "jals_flag") {
		$tjcount = round($tjcount, 2);
		$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $tjcount);
		$column++;
	} else if ($field_details[$i . ":" . $active_flag] == "birds_flag") {
		$tbcount = round($tbcount, 2);
		$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $tbcount);
		$column++;
	} else if ($field_details[$i . ":" . $active_flag] == "tweight_flag") {
		$twcount = round($twcount, 2);
		$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $twcount);
		$column++;
	} else if ($field_details[$i . ":" . $active_flag] == "eweight_flag") {
		$tecount = round($tecount, 2);
		$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $tecount);
		$column++;
	} else if ($field_details[$i . ":" . $active_flag] == "nweight_flag") {
		$tncount = round($tncount, 2);
		$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $tncount);
		$column++;
	} else if ($field_details[$i . ":" . $active_flag] == "aweight_flag") {
		if($tbcount > 0){ $bwavgweight = round(($tncount / $tbcount), 2); } else{ $bwavgweight = 0; }
		$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $bwavgweight);
		$column++;
	} else if ($field_details[$i . ":" . $active_flag] == "prate_flag") {
		$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
		$column++;
	} else if ($field_details[$i . ":" . $active_flag] == "price_flag") {
		if($tncount > 0){ $bwavgprice = round(($fst_famt / $tncount), 2); } else{ $bwavgprice = 0; }
		$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $bwavgprice);
		$column++;
	} else if ($field_details[$i . ":" . $active_flag] == "tcds_flag") {
		$ft_tcds = round($ft_tcds, 2);
		$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $ft_tcds);
		$column++;
	} else if ($field_details[$i . ":" . $active_flag] == "discount_flag") {
		$tdcount = round($tdcount, 2);
		$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $tdcount);
		$column++;
	} else if ($field_details[$i . ":" . $active_flag] == "tamt_flag") {
		$bw_tamt = round(($tacount + $fdt_famt + $fct_famt + $frt_famt), 2);
		$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $bw_tamt);
		$column++;
	} else if ($field_details[$i . ":" . $active_flag] == "sector_flag") {
		$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
		$column++;
	} else if ($field_details[$i . ":" . $active_flag] == "remarks_flag") {
		$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
		$column++;
	} else if ($field_details[$i . ":" . $active_flag] == "vehicle_flag") {
		$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
		$column++;
	} else if ($field_details[$i . ":" . $active_flag] == "driver_flag") {
		$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
		$column++;
	} else if ($field_details[$i . ":" . $active_flag] == "cr_flag") {
		$bwcramt = round(($fst_famt + $fct_famt), 2);
		$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $bwcramt);
		$column++;
	} else if ($field_details[$i . ":" . $active_flag] == "dr_flag") {
		$bwdramt = round(($frt_famt + $fdt_famt), 2);
		$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $bwdramt);
		$column++;
	} else if ($field_details[$i . ":" . $active_flag] == "rb_flag") {
		$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
		$column++;
	} else {
	}
}
$row++;
$column = 0;
$objPHPExcel->setActiveSheetIndex()->mergeCells($az[$column] . "" . $row . ":" . $az[$grnd_tot_col] . "" . $row);
$objPHPExcel->setActiveSheetIndex()->setCellValue("A" . $row, "Grand Total");
$column = $grnd_tot_col + 1;
for ($i = 1; $i <= $col_count; $i++) {
	if ($field_details[$i . ":" . $active_flag] == "cr_flag") {
		$gcr_amt = round(($fst_famt + $fct_famt + $ob_rev_amt), 2);
		$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $gcr_amt);
		$column++;
	} else if ($field_details[$i . ":" . $active_flag] == "dr_flag") {
		$gdr_amt = round(($frt_famt + $fdt_famt + $ob_pid_amt), 2);
		$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $gdr_amt);
		$column++;
	} else if ($field_details[$i . ":" . $active_flag] == "rb_flag") {
		$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
		$column++;
	} else {
	}
}
$row++;
$column = 0;
$objPHPExcel->setActiveSheetIndex()->mergeCells($az[$column] . "" . $row . ":" . $az[$clsb_tot_col] . "" . $row);
$objPHPExcel->setActiveSheetIndex()->setCellValue("A" . $row, "Closing Balance");
$column = $clsb_tot_col + 1;
if ((($fst_famt + $fct_famt) + $ob_rev_amt) > (($frt_famt + $fdt_famt) + $ob_pid_amt)) {
	$famt = (($fst_famt + $fct_famt) + $ob_rev_amt) - (($frt_famt + $fdt_famt) + $ob_pid_amt);
	for ($i = 1; $i <= $col_count; $i++) {
		if ($field_details[$i . ":" . $active_flag] == "cr_flag") {
			$famt = round($famt, 2);
			$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $famt);
			$column++;
		} else if ($field_details[$i . ":" . $active_flag] == "dr_flag") {
			$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
			$column++;
		} else if ($field_details[$i . ":" . $active_flag] == "rb_flag") {
			$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
			$column++;
		} else {
		}
	}
} else {
	$famt = (($fst_famt + $fct_famt) + $ob_rev_amt) - (($frt_famt + $fdt_famt) + $ob_pid_amt);
	for ($i = 1; $i <= $col_count; $i++) {
		if ($field_details[$i . ":" . $active_flag] == "cr_flag") {
			$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
			$column++;
		} else if ($field_details[$i . ":" . $active_flag] == "dr_flag") {
			$famt = round($famt, 2);
			$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, $famt);
			$column++;
		} else if ($field_details[$i . ":" . $active_flag] == "rb_flag") {
			$objPHPExcel->setActiveSheetIndex()->setCellValue($az[$column] . "" . $row, "");
			$column++;
		} else {
		}
	}
}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('SupplierLedgerReport');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex();
// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="SupplierLedgerReport.xls"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
echo "<script> window.close(); </script>";
exit;