<?php
//CustomerLedgerMasterReport-Excel.php
session_start(); 
include "../../newConfig.php";
$dbname = $_SESSION['dbase'];
include "../../number_format_ind.php";

$today = date("Y-m-d");
$sql = "SELECT * FROM `master_itemfields` WHERE `type` = 'Birds' AND `id` = '1'";
$query = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($query)) {
	$ifwt = $row['wt'];
	$ifbw = $row['bw'];
	$ifjbw = $row['jbw'];
	$ifjbwen = $row['jbwen'];
	$ifctype = $row['ctype'];
}

if ($_GET['ctype'] == "on" || $_GET['ctype'] == true) {
	$con_type = " AND`contacttype` LIKE '%S%'";
	$con_code = "S&C";
} else {
	$con_type = " AND`contacttype` LIKE 'S'";
	$con_code = "S";
}

$sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1'" . $con_type . " ORDER BY `name` ASC";
$query = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($query)) {
	$pcode[$row['code']] = $row['code'];
	$pname[$row['code']] = $row['name'];
	$obdate[$row['code']] = $row['obdate'];
	$obtype[$row['code']] = $row['obtype'];
	$obamt[$row['code']] = $row['obamt'];
	$sup_name[$row['code']] = $row['name'];
	$sup_code[$row['code']] = $row['code'];
	$sup_type[$row['code']] = $row['contacttype'];
}

$sql = "SELECT * FROM `item_details` WHERE `active` = '1'";
$query = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($query)) {
	$itemname[$row['code']] = $row['description'];
}
$fromdate = $_GET['fromdate'];
$todate = $_GET['todate'];
if ($fromdate == "") {
	$fromdate = $todate = $today;
} else {
	$fromdate = $_GET['fromdate'];
	$todate = $_GET['todate'];
}
$cname = $_GET['cname'];
$iname = $_GET['iname'];
if ($cname == "all" || $cname == "select") {
	$cnames = "";
} else {
	$cnames = " AND `customercode` = '$cname'";
}

$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Purchase Invoice' OR `type` = 'All' ORDER BY `id` DESC";
$query = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($query)) {

	$cdetails = $row['cdetails'];
}


$ch = array();
$ch[1] = "A";
$ch[2] = "B";
$ch[3] = "C";
$ch[4] = "D";
$ch[5] = "E";
$ch[6] = "F";
$ch[7] = "G";
$ch[8] = "H";
$ch[9] = "I";
$ch[10] = "J";
$ch[11] = "K";
$ch[12] = "L";
$ch[13] = "M";
$ch[14] = "N";
$ch[15] = "O";
$ch[16] = "P";
$ch[17] = "Q";
$ch[18] = "R";
$ch[19] = "S";
$ch[20] = "T";
$ch[21] = "U";
$ch[22] = "V";
$ch[23] = "W";
$ch[24] = "X";
$ch[25] = "Y";
$ch[26] = "Z";
$ch[27] = "AA";
$ch[28] = "AB";
$ch[29] = "AC";
$ch[30] = "AD";
$ch[31] = "AE";
$ch[32] = "AF";
$ch[33] = "AG";
$ch[34] = "AH";
$ch[35] = "AI";
$ch[36] = "AJ";
$ch[37] = "AK";
$ch[38] = "AL";
$ch[39] = "AM";
$ch[40] = "AN";
$ch[41] = "AO";
$ch[42] = "AP";
$ch[43] = "AQ";
$ch[44] = "AR";
$ch[45] = "AS";
$ch[46] = "AT";
$ch[47] = "AU";
$ch[48] = "AV";
$ch[49] = "AW";
$ch[50] = "AX";
$ch[51] = "AY";
$ch[52] = "AZ";
$ch[53] = "BA";
$ch[54] = "BB";
$ch[55] = "BC";
$ch[56] = "BD";
$ch[57] = "BE";
$ch[58] = "BF";
$ch[59] = "BG";
$ch[60] = "BH";
$ch[61] = "BI";
$ch[62] = "BJ";
$ch[63] = "BK";
$ch[64] = "BL";
$ch[65] = "BM";
$ch[66] = "BN";
$ch[67] = "BO";
$ch[68] = "BP";
$ch[69] = "BQ";
$ch[70] = "BR";
$ch[71] = "BS";
$ch[72] = "BT";
$ch[73] = "BU";
$ch[74] = "BV";
$ch[75] = "BW";
$ch[76] = "BX";
$ch[77] = "BY";
$ch[78] = "BZ";
$ch[79] = "CA";
$ch[80] = "CB";
$ch[81] = "CC";
$ch[82] = "CD";
$ch[83] = "CE";
$ch[84] = "CF";
$ch[85] = "CG";
$ch[86] = "CH";
$ch[87] = "CI";
$ch[88] = "CJ";
$ch[89] = "CK";
$ch[90] = "CL";
$ch[91] = "CM";
$ch[92] = "CN";
$ch[93] = "CO";
$ch[94] = "CP";
$ch[95] = "CQ";
$ch[96] = "CR";
$ch[97] = "CS";
$ch[98] = "CT";
$ch[99] = "CU";
$ch[100] = "CV";
$ch[101] = "CW";
$ch[102] = "CX";
$ch[103] = "CY";
$ch[104] = "CZ";
$ch[105] = "DA";
$ch[106] = "DB";
$ch[107] = "DC";
$ch[108] = "DD";
$ch[109] = "DE";
$ch[110] = "DF";
$ch[111] = "DG";
$ch[112] = "DH";
$ch[113] = "DI";
$ch[114] = "DJ";
$ch[115] = "DK";
$ch[116] = "DL";
$ch[117] = "DM";
$ch[118] = "DN";
$ch[119] = "DO";
$ch[120] = "DP";
$ch[121] = "DQ";
$ch[122] = "DR";
$ch[123] = "DS";
$ch[124] = "DT";
$ch[125] = "DU";
$ch[126] = "DV";
$ch[127] = "DW";
$ch[128] = "DX";
$ch[129] = "DY";
$ch[130] = "DZ";
$ch[131] = "EA";
$ch[132] = "EB";
$ch[133] = "EC";
$ch[134] = "ED";
$ch[135] = "EE";
$ch[136] = "EF";
$ch[137] = "EG";
$ch[138] = "EH";
$ch[139] = "EI";
$ch[140] = "EJ";
$ch[141] = "EK";
$ch[142] = "EL";
$ch[143] = "EM";
$ch[144] = "EN";
$ch[145] = "EO";
$ch[146] = "EP";
$ch[147] = "EQ";
$ch[148] = "ER";
$ch[149] = "ES";
$ch[150] = "ET";
$ch[151] = "EU";
$ch[152] = "EV";
$ch[153] = "EW";
$ch[154] = "EX";
$ch[155] = "EY";
$ch[156] = "EZ";

require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';
$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
->setLastModifiedBy("Maarten Balliauw")
->setTitle("Office 2007 XLSX Test Document")
->setSubject("Office 2007 XLSX Test Document")
->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
->setKeywords("office 2007 openxml php")
->setCategory("SupplierLedgerReportExcel");



for ($col = 'A'; $col !== 'CZ'; $col++) {
	$objPHPExcel->getActiveSheet()
		->getColumnDimension($col)
		->setAutoSize(true);
}

$rows++;
$rows++;

$diaply1 = "Supplier Ledger All";
$column_colspan = $ch[4] . $rows . ":" . $ch[7] . $rows;
$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[4] . $rows, $diaply1);
$objPHPExcel->getActiveSheet()->getStyle($ch[4] . $rows)->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getStyle($ch[4] . $rows)->getAlignment()->applyFromArray(
	array(
		"horizontal" => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		"vertical" => \PHPExcel_Style_Alignment::VERTICAL_CENTER
	)
);

$rows++;

$diaply2 = "From Date: " . $_GET['fromdate'] . "         To Date: " . $_GET['todate'];
$column_colspan = $ch[4] . $rows . ":" . $ch[7] . $rows;
$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[4] . $rows, $diaply2);
$objPHPExcel->getActiveSheet()->getStyle($ch[4] . $rows)->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getStyle($ch[4] . $rows)->getAlignment()->applyFromArray(
	array(
		"horizontal" => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		"vertical" => \PHPExcel_Style_Alignment::VERTICAL_CENTER
	)
);


if($coa != 'all'){
	$display_coa = "Supplier: ".$coaname[$coa];
}else{
	$display_coa = "Supplier: ".'All';
}

$rows++;

$column_colspan1 = "A1" . ":" . $ch[3] . $rows;
$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan1)->setCellValue("A1" . "", strip_tags($cdetails));
$objPHPExcel->getActiveSheet()->getStyle("A1")->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getStyle("A1")->getAlignment()->applyFromArray(
	array(
		"horizontal" => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		"vertical" => \PHPExcel_Style_Alignment::VERTICAL_CENTER
	)
);


$column_colspan = $ch[4] . $rows . ":" . $ch[7] . $rows;
$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[4] . $rows, $display_coa);
$objPHPExcel->getActiveSheet()->getStyle($ch[4] . $rows)->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getStyle($ch[4] . $rows)->getAlignment()->applyFromArray(
	array(
		"horizontal" => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		"vertical" => \PHPExcel_Style_Alignment::VERTICAL_CENTER
	)
);

$rows++;$rows++;$rows1 = 7;

$column_colspan = $ch[1] . $rows . ":" . $ch[1] . $rows1;
$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[1] . $rows, "Name");
$objPHPExcel->getActiveSheet()->getStyle($ch[1] . $rows)->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getStyle($ch[1] . $rows)->getAlignment()->applyFromArray(
	array(
		"horizontal" => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		"vertical" => \PHPExcel_Style_Alignment::VERTICAL_CENTER
	)
);

$column_colspan = $ch[2] . $rows . ":" . $ch[2] . $rows1;
$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[2] . $rows, "Opening Balance");
$objPHPExcel->getActiveSheet()->getStyle($ch[2] . $rows)->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getStyle($ch[2] . $rows)->getAlignment()->applyFromArray(
	array(
		"horizontal" => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		"vertical" => \PHPExcel_Style_Alignment::VERTICAL_CENTER
	)
);

$column_colspan = $ch[3] . $rows . ":" . $ch[5] . $rows;
$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[3] . $rows, "Selected Period");
$objPHPExcel->getActiveSheet()->getStyle($ch[3] . $rows)->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getStyle($ch[3] . $rows)->getAlignment()->applyFromArray(
	array(
		"horizontal" => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		"vertical" => \PHPExcel_Style_Alignment::VERTICAL_CENTER
	)
);

$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[3] . $rows1, "Purchase Qty");
$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[4] . $rows1, "Purchase");
$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[5] . $rows1, "Payments");

$column_colspan = $ch[6] . $rows . ":" . $ch[6] . $rows1;
$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[6] . $rows, "Balance");
$objPHPExcel->getActiveSheet()->getStyle($ch[6] . $rows)->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getStyle($ch[6] . $rows)->getAlignment()->applyFromArray(
	array(
		"horizontal" => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		"vertical" => \PHPExcel_Style_Alignment::VERTICAL_CENTER
	)
);
$rows++;$rows++;
$from = $ch[1] . "1"; // or any value
$to = $ch[6] . $rows; // or any value
$objPHPExcel->getActiveSheet()->getStyle("$from:$to")->getFont()->setBold(true);
if ($cname == "" || $cname == "all" || $cname == "select") {
	$fromdate = $_GET['fromdate'];
	$todate = $_GET['todate'];
	if ($fromdate == "") {
		$fromdate = $todate = $today;
	} else {
		$fromdate = date("Y-m-d", strtotime($_GET['fromdate']));
		$todate = date("Y-m-d", strtotime($_GET['todate']));
	}
	$cname = $_GET['cname'];
	$iname = $_GET['iname'];
	if ($cname == "all" || $cname == "select") {
		$cnames = "";
	} else {
		$cnames = " AND `vendorcode` = '$cname'";
	}

	//Supplier invoice
	$ob_purchases = array();
	$sql = "SELECT * FROM `pur_purchase` WHERE `date` < '$fromdate' AND `active` = '1' ORDER BY `date`,`invoice`,`vendorcode` ASC";
	$query = mysqli_query($conn, $sql);
	$old_inv = "";
	while ($row = mysqli_fetch_assoc($query)) {
		if ($old_inv != $row['invoice']) {
			$ob_purchases[$row['vendorcode']] = $ob_purchases[$row['vendorcode']] + $row['finaltotal'];
			$old_inv = $row['invoice'];
		}
	}
	//Supplier Receipt
	$ob_payments = array();
	$seq = "SELECT SUM(amount) as amount,ccode FROM `pur_payments` WHERE `date` < '$fromdate'";
	$active = " AND `active` = '1'";
	$orderby = " ORDER BY `ccode` ASC";
	$groupby = " GROUP BY `ccode`";
	$sql = $seq . "" . $active . "" . $groupby . "" . $orderby;
	$query = mysqli_query($conn, $sql);
	while ($row = mysqli_fetch_assoc($query)) {
		$ob_payments[$row['ccode']] = $row['amount'];
	}

	//Supplier Returns
	$ob_returns = array();
	$obsql = "SELECT * FROM `main_itemreturns` WHERE `date` < '$fromdate' AND `mode` = 'supplier' AND `active` = '1' AND `dflag` = '0'";
	$obquery = mysqli_query($conn, $obsql);
	while ($obrow = mysqli_fetch_assoc($obquery)) {
		$ob_returns[$obrow['vcode']] += (float)$obrow['amount'];
	}

	//Supplier Mortality
	$ob_smortality = array();
	$obsql = "SELECT * FROM `main_mortality` WHERE `date` < '$fromdate' AND `mtype` = 'supplier' AND `active` = '1' AND `dflag` = '0'";
	$obquery = mysqli_query($conn, $obsql);
	while ($obrow = mysqli_fetch_assoc($obquery)) {
		$ob_smortality[$obrow['ccode']] += (float)$obrow['amount'];
	}

	//Supplier CrDr Note
	$ob_scn = $ob_sdn = array();
	$seq = "SELECT SUM(amount) as amount,mode,ccode FROM `main_crdrnote` WHERE `date` < '$fromdate' AND `mode` IN ('SCN','SDN')";
	$active = " AND `active` = '1'";
	$orderby = " ORDER BY `ccode` ASC";
	$groupby = " GROUP BY `ccode`,`mode`";
	$sql = $seq . "" . $active . "" . $groupby . "" . $orderby;
	$query = mysqli_query($conn, $sql);
	while ($row = mysqli_fetch_assoc($query)) {
		if ($row['mode'] == "SCN") {
			$ob_scn[$row['ccode']] = $row['amount'];
		} else {
			$ob_sdn[$row['ccode']] = $row['amount'];
		}
	}


	//Supplier invoice
	$bt_purchases = $bt_purchases_qty = array();
	$sql = "SELECT * FROM `pur_purchase` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `active` = '1' ORDER BY `date`,`invoice`,`vendorcode` ASC";
	$query = mysqli_query($conn, $sql);
	$old_inv = "";
	while ($row = mysqli_fetch_assoc($query)) {
		if ($old_inv != $row['invoice']) {
			$bt_purchases[$row['vendorcode']] = $bt_purchases[$row['vendorcode']] + $row['finaltotal'];
			$old_inv = $row['invoice'];
		}
		$bt_purchases_qty[$row['vendorcode']] = $bt_purchases_qty[$row['vendorcode']] + $row['netweight'];
	}
	//Supplier Receipt
	$bt_payments = array();
	$seq = "SELECT SUM(amount) as amount,ccode FROM `pur_payments` WHERE `date` >= '$fromdate' AND `date` <= '$todate'";
	$active = " AND `active` = '1'";
	$orderby = " ORDER BY `ccode` ASC";
	$groupby = " GROUP BY `ccode`";
	$sql = $seq . "" . $active . "" . $groupby . "" . $orderby;
	$query = mysqli_query($conn, $sql);
	while ($row = mysqli_fetch_assoc($query)) {
		$bt_payments[$row['ccode']] = $row['amount'];
	}
	//Supplier Returns
	$bt_returns = array();
	$obsql = "SELECT * FROM `main_itemreturns` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `mode` = 'supplier' AND `active` = '1' AND `dflag` = '0'";
	$obquery = mysqli_query($conn, $obsql);
	while ($obrow = mysqli_fetch_assoc($obquery)) {
		$bt_returns[$obrow['vcode']] += (float)$obrow['amount'];
	}

	//Supplier Mortality
	$bt_smortality = array();
	$obsql = "SELECT * FROM `main_mortality` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `mtype` = 'supplier' AND `active` = '1' AND `dflag` = '0'";
	$obquery = mysqli_query($conn, $obsql);
	while ($obrow = mysqli_fetch_assoc($obquery)) {
		$bt_smortality[$obrow['ccode']] += (float)$obrow['amount'];
	}

	//Supplier CrDr Note
	$bt_scn = $bt_sdn = array();
	$seq = "SELECT SUM(amount) as amount,mode,ccode FROM `main_crdrnote` WHERE `date` >= '$fromdate' AND `date` <= '$todate' AND `mode` IN ('SCN','SDN')";
	$active = " AND `active` = '1'";
	$orderby = " ORDER BY `ccode` ASC";
	$groupby = " GROUP BY `ccode`,`mode`";
	$sql = $seq . "" . $active . "" . $groupby . "" . $orderby;
	$query = mysqli_query($conn, $sql);
	while ($row = mysqli_fetch_assoc($query)) {
		if ($row['mode'] == "SCN") {
			$bt_scn[$row['ccode']] = $row['amount'];
		} else {
			$bt_sdn[$row['ccode']] = $row['amount'];
		}
	}
	$ftotal = $ft_ob =  $ft_sq =  $ft_sa =  $ft_rt =  $ft_bb = 0;
	foreach ($pcode as $pcodes) {
		$rows++;
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[1] . $rows, $pname[$pcodes]);
	
		$ob_cramt = $ob_dramt = $ob_dr = $ob_cr = $ob_fcr = $ob_fdr = $bt_dr = $bt_cr = $bt_fcr = $bt_fdr = $balance = 0;
		if ($obtype[$pcodes] == "Cr") {
			$ob_dramt = $obamt[$pcodes];
		} else {
			$ob_cramt = $obamt[$pcodes];
		}
		$ft_ob = $ft_ob + (((float)$ob_purchases[$pcodes] + (float)$ob_scn[$pcodes] + (float)$ob_dramt) - ((float)$ob_payments[$pcodes] + (float)$ob_returns[$pcodes] + (float)$ob_smortality[$pcodes] + (float)$ob_sdn[$pcodes] + (float)$ob_cramt));
		$ft_sq = $ft_sq + (float)$bt_purchases_qty[$pcodes];
		$ft_sa = $ft_sa + ((float)$bt_purchases[$pcodes] + (float)$bt_scn[$pcodes]);
		$ft_rt = $ft_rt + ((float)$bt_payments[$pcodes] + (float)$bt_returns[$pcodes] + (float)$bt_smortality[$pcodes] + (float)$bt_sdn[$pcodes]);
		$ft_bb = $ft_bb + (((float)$bt_purchases[$pcodes] + (float)$bt_scn[$pcodes]) - ((float)$bt_payments[$pcodes] + (float)$bt_returns[$pcodes] + (float)$bt_smortality[$pcodes] + (float)$bt_sdn[$pcodes]));
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[2] . $rows, ((float)$ob_purchases[$pcodes] + (float)$ob_scn[$pcodes] + (float)$ob_dramt) - ((float)$ob_payments[$pcodes] + (float)$ob_returns[$pcodes] + (float)$ob_smortality[$pcodes] + (float)$ob_sdn[$pcodes] + (float)$ob_cramt));
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[3] . $rows, $bt_purchases_qty[$pcodes]);
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[4] . $rows, (float)$bt_purchases[$pcodes] + (float)$bt_scn[$pcodes]);
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[5] . $rows, (float)$bt_payments[$pcodes] + (float)$bt_returns[$pcodes] + (float)$bt_smortality[$pcodes] + (float)$bt_sdn[$pcodes]);
		//echo "<td>".number_format_ind(($bt_purchases[$pcodes] + $bt_scn[$pcodes]) - ($bt_payments[$pcodes] + $bt_sdn[$pcodes]))."</td>";
		$ob_dr = (float)$ob_purchases[$pcodes] + (float)$ob_scn[$pcodes] + (float)$ob_dramt;
		$ob_cr = (float)$ob_payments[$pcodes] + (float)$ob_returns[$pcodes] + (float)$ob_smortality[$pcodes] + (float)$ob_sdn[$pcodes] + (float)$ob_cramt;
		if ($ob_cr > $ob_dr) {
			$ob_fcr = (float)$ob_cr - (float)$ob_dr;
		} else {
			$ob_fdr = (float)$ob_dr - (float)$ob_cr;
		}
		$bt_dr = (float)$bt_purchases[$pcodes] + (float)$bt_scn[$pcodes];
		$bt_cr = (float)$bt_payments[$pcodes] + (float)$bt_returns[$pcodes] + (float)$bt_smortality[$pcodes] + (float)$bt_sdn[$pcodes];
		if ($bt_cr > $bt_dr) {
			$bt_fcr = (float)$bt_cr - (float)$bt_dr;
		} else {
			$bt_fdr = (float)$bt_dr - (float)$bt_cr;
		}
		$balance = ((float)$ob_fdr + (float)$bt_fdr) - ((float)$ob_fcr + (float)$bt_fcr);
		//echo "<br/>".$ob_fdr."+".$bt_fdr."-".$ob_fcr."+".$bt_fcr;
		$ftotal = (float)$ftotal + (float)$balance;
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[6] . $rows, $balance);
		
	}
} else {
}

$rows++;
$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[1] . $rows, "Total");
$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[2] . $rows, $ft_ob);
$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[3] . $rows, $ft_sq);
$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[4] . $rows, $ft_sa);
$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[5] . $rows, $ft_rt);
$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[6] . $rows, $ftotal);

$from = $ch[1] . $rows; // or any value
$to = $ch[6] . $rows; // or any value
$objPHPExcel->getActiveSheet()->getStyle("$from:$to")->getFont()->setBold(true);
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('SLAll');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex();
// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="SLAll.xls"');
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