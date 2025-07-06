<?php
//CustomerLedgerMasterReport-Excel.php
session_start();
include "../../newConfig.php";
$dbname = $_SESSION['dbase'];
include "../../number_format_ind.php";

$sql = "SELECT * FROM `acc_coa` WHERE `active` = '1' AND `type` IN ('COA-0003') AND `categories` IN ('CAT-0008') ORDER BY `description` ASC";
$query = mysqli_query($conn, $sql);
$allCoA = "";
while ($row = mysqli_fetch_assoc($query)) {
	$coaname[$row['code']] = $row['description'];
	$coacode[$row['code']] = $row['code'];
	if ($allCoA == "") {
		$allCoA = $row['code'];
	} else {
		$allCoA = $allCoA . "','" . $row['code'];
	}
}
$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($query)) {
	$whname[$row['code']] = $row['description'];
	$whcode[$row['code']] = $row['code'];
}
$sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1' ORDER BY `name` ASC";
$query = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($query)) {
	$cpname[$row['code']] = $row['name'];
	$cpcode[$row['code']] = $row['code'];
}
$fromdate = $_GET['fromdate'];
$todate = $_GET['todate'];
$pcoa = $_GET['coa'];
$pwhname = $_GET['whname'];
// $sectors = $_GET['sectors']; if($sectors == "all"){ $sec_fltr = ""; } else{ $sec_list = implode("','",explode(",",$sectors)); $sec_fltr = " AND `warehouse` IN ('$sec_list')"; }

if ($fromdate == "") {
	$fromdate = $todate = $today;
} else {
	$fromdate = $_GET['fromdate'];
	$todate = $_GET['todate'];
}
if ($pcoa == "" ) {
	$pcoa = "all";
	
} else {
	$pcoa = $_GET['coa'];
	$diaply3 = "CoA: " .  $coaname[$pcoa];
}
if($pcoa == "all"){
	$diaply3 = "CoA: " ."All";
}
// if ($pwhname == "") {
// 	$pwhname = "all";
	
// } else {
// 	$pwhname = $_GET['whname'];
// 	$diaply4 = "Warehouse: " . $whname[$pwhname];
// }
// if($pwhname == "all"){
// 	$diaply4 = "Warehouse: " . "All";
// }
$sectors = $_GET['sectors'];

if ($sectors == "all") {
    $sec_fltr = "";
    $pwhname = "all";
    $diaply4 = "Warehouse: All";
} else {
    $sec_list = implode("','", explode(",", $sectors));
    $sec_fltr = " AND `warehouse` IN ('$sec_list')";

    // Use the first sector as the selected warehouse label if needed
    $pwhname = explode(",", $sectors)[0]; // or assign dynamically from a map
    $diaply4 = "Warehouse: " . $whname[$pwhname];
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
	->setCategory("ExpenseLedgerReportExcel");





for ($col = 'A'; $col !== 'CZ'; $col++) {
	$objPHPExcel->getActiveSheet()
		->getColumnDimension($col)
		->setAutoSize(true);
}

$rows++;
$rows++;

$diaply1 = "Expense Ledger";
$column_colspan = $ch[5] . $rows . ":" . $ch[10] . $rows;
$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[5] . $rows, $diaply1);
$objPHPExcel->getActiveSheet()->getStyle($ch[5] . $rows)->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getStyle($ch[5] . $rows)->getAlignment()->applyFromArray(
	array(
		"horizontal" => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		"vertical" => \PHPExcel_Style_Alignment::VERTICAL_CENTER
	)
);

$rows++;

$diaply2 = "From Date: " . $_GET['fromdate'] . "         To Date: " . $_GET['todate'];
$column_colspan = $ch[5] . $rows . ":" . $ch[10] . $rows;
$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[5] . $rows, $diaply2);
$objPHPExcel->getActiveSheet()->getStyle($ch[5] . $rows)->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getStyle($ch[5] . $rows)->getAlignment()->applyFromArray(
	array(
		"horizontal" => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		"vertical" => \PHPExcel_Style_Alignment::VERTICAL_CENTER
	)
);

$rows++;

$column_colspan = $ch[5] . $rows . ":" . $ch[10] . $rows;
$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[5] . $rows, $diaply3);
$objPHPExcel->getActiveSheet()->getStyle($ch[5] . $rows)->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getStyle($ch[5] . $rows)->getAlignment()->applyFromArray(
	array(
		"horizontal" => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		"vertical" => \PHPExcel_Style_Alignment::VERTICAL_CENTER
	)
);

$rows++;

$column_colspan1 = "A1" . ":" . $ch[4] . $rows;
$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan1)->setCellValue("A1" . "", strip_tags($cdetails));
$objPHPExcel->getActiveSheet()->getStyle("A1")->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getStyle("A1")->getAlignment()->applyFromArray(
	array(
		"horizontal" => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		"vertical" => \PHPExcel_Style_Alignment::VERTICAL_CENTER
	)
);


$column_colspan = $ch[5] . $rows . ":" . $ch[10] . $rows;
$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[5] . $rows, $diaply4);
$objPHPExcel->getActiveSheet()->getStyle($ch[5] . $rows)->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getStyle($ch[5] . $rows)->getAlignment()->applyFromArray(
	array(
		"horizontal" => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		"vertical" => \PHPExcel_Style_Alignment::VERTICAL_CENTER
	)
);



if ($_GET['coa'] == "all") {
	$fdate = date("Y-m-d", strtotime($_GET['fromdate']));
	$tdate = date("Y-m-d", strtotime($_GET['todate']));
	$coa = $allCoA;
	// if ($_GET['whname'] == "all") {
	// 	$wname = "";
	// } else {
	// 	$wname = "AND `warehouse` = '" . $_GET['whname'] . "'";
	// }
	$sectors = $_GET['sectors']; if($sectors == "all"){ $sec_fltr = ""; } else{ $sec_list = implode("','",explode(",",$sectors)); $sec_fltr = " AND `warehouse` IN ('$sec_list')"; }


	$c = $d = $e = $f = $g = 0;

	$bt_fpv_amt = $bt_frv_amt = $bt_fjv_amt = $bt_foth_vou_amt = $bt_tpv_amt = $bt_trv_amt = $bt_tjv_amt = $bt_toth_vou_amt = array();

	$sql = "SELECT * FROM `acc_vouchers` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1' AND `fcoa` IN ('$coa')" . $sec_fltr . " ORDER BY `fcoa` ASC";
	$query = mysqli_query($conn, $sql);
	while ($row = mysqli_fetch_assoc($query)) {
		if ($row['prefix'] == "PV") {
			$c = $c + 1;
			$bt_fpv_amt[$row['date'] . "@" . $c] = $row['trnum'] . "@" . $row['date'] . "@" . $row['tcoa'] . "@" . $row['dcno'] . "@" . $row['amount'] . "@" . $row['warehouse'] . "@" . $row['remarks'] . "@" . $row['fcoa'];
		} else if ($row['prefix'] == "RV") {
			$d = $d + 1;
			$bt_frv_amt[$row['date'] . "@" . $d] = $row['trnum'] . "@" . $row['date'] . "@" . $row['tcoa'] . "@" . $row['dcno'] . "@" . $row['amount'] . "@" . $row['warehouse'] . "@" . $row['remarks'] . "@" . $row['fcoa'];
		} else if ($row['prefix'] == "JV") {
			$e = $e + 1;
			$bt_fjv_amt[$row['date'] . "@" . $e] = $row['trnum'] . "@" . $row['date'] . "@" . $row['tcoa'] . "@" . $row['dcno'] . "@" . $row['amount'] . "@" . $row['warehouse'] . "@" . $row['remarks'] . "@" . $row['fcoa'];
		} else {
			$f = $f + 1;
			$bt_foth_vou_amt[$row['date'] . "@" . $f] = $row['trnum'] . "@" . $row['date'] . "@" . $row['tcoa'] . "@" . $row['dcno'] . "@" . $row['amount'] . "@" . $row['warehouse'] . "@" . $row['remarks'] . "@" . $row['fcoa'];
		}
	}
	$c = $d = $e = $f = $g = 0;
	$sql = "SELECT * FROM `acc_vouchers` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1' AND `tcoa` IN ('$coa')" . $sec_fltr . " ORDER BY `tcoa` ASC";
	$query = mysqli_query($conn, $sql);
	while ($row = mysqli_fetch_assoc($query)) {
		if ($row['prefix'] == "PV") {
			$c = $c + 1;
			$bt_tpv_amt[$row['date'] . "@" . $c] = $row['trnum'] . "@" . $row['date'] . "@" . $row['fcoa'] . "@" . $row['dcno'] . "@" . $row['amount'] . "@" . $row['warehouse'] . "@" . $row['remarks'] . "@" . $row['tcoa'];
		} else if ($row['prefix'] == "RV") {
			$d = $d + 1;
			$bt_trv_amt[$row['date'] . "@" . $d] = $row['trnum'] . "@" . $row['date'] . "@" . $row['fcoa'] . "@" . $row['dcno'] . "@" . $row['amount'] . "@" . $row['warehouse'] . "@" . $row['remarks'] . "@" . $row['tcoa'];
		} else if ($row['prefix'] == "JV") {
			$e = $e + 1;
			$bt_tjv_amt[$row['date'] . "@" . $e] = $row['trnum'] . "@" . $row['date'] . "@" . $row['fcoa'] . "@" . $row['dcno'] . "@" . $row['amount'] . "@" . $row['warehouse'] . "@" . $row['remarks'] . "@" . $row['tcoa'];
		} else {
			$f = $f + 1;
			$bt_toth_vou_amt[$row['date'] . "@" . $f] = $row['trnum'] . "@" . $row['date'] . "@" . $row['fcoa'] . "@" . $row['dcno'] . "@" . $row['amount'] . "@" . $row['warehouse'] . "@" . $row['remarks'] . "@" . $row['tcoa'];
		}
	}

	$rows++;
	$rows++;
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue("A" . "" . $rows, 'Sl.No.')
		->setCellValue("B" . "" . $rows, 'Date')
		->setCellValue("C" . "" . $rows, 'CoA')
		->setCellValue("D" . "" . $rows, 'Transaction No')
		->setCellValue("E" . "" . $rows, 'Transaction Type')
		->setCellValue("F" . "" . $rows, 'Doc. No.')
		->setCellValue("G" . "" . $rows, 'From Warehouse')
		->setCellValue("H" . "" . $rows, 'Narrations')
		->setCellValue("I" . "" . $rows, 'Debit')
		->setCellValue("J" . "" . $rows, 'Credit');

		
$from = "A1"; // or any value
$to = $ch[10] . $rows; // or any value
$objPHPExcel->getActiveSheet()->getStyle("$from:$to")->getFont()->setBold(true);

	$fdate = strtotime($fdate);
	$tdate = strtotime($tdate);
	$bt_paid = $bt_received = $c = 0;
	for ($currentDate = $fdate; $currentDate <= $tdate; $currentDate += (86400)) {
		$date_asc = date('Y-m-d', $currentDate);
		$ccount = sizeof($bt_fpv_amt);

		for ($i = 1; $i <= $ccount; $i++) {
			if ($bt_fpv_amt[$date_asc . "@" . $i] != "") {
				$c = $c + 1;
				$bt_fpv_val = explode("@", $bt_fpv_amt[$date_asc . "@" . $i]);

				$bt_paid = $bt_paid + $bt_fpv_val[4];


				$rows++;
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A" . "" . $rows, $sno++)
					->setCellValue("B" . "" . $rows, date("d.m.Y", strtotime($bt_fpv_val[1])))
					->setCellValue("C" . "" . $rows, $coaname[$bt_fpv_val[7]])
					->setCellValue("D" . "" . $rows, $bt_fpv_val[0])
					->setCellValue("E" . "" . $rows, 'Payment Voucher')
					->setCellValue("F" . "" . $rows, $bt_fpv_val[3])
					->setCellValue("G" . "" . $rows, $whname[$bt_fpv_val[5]])
					->setCellValue("H" . "" . $rows, $bt_fpv_val[6])
					->setCellValue("I" . "" . $rows, $bt_fpv_val[4])
					->setCellValue("J" . "" . $rows, '');
			} else {
			}
		}

		$ccount = sizeof($bt_frv_amt);

		for ($i = 1; $i <= $ccount; $i++) {
			if ($bt_frv_amt[$date_asc . "@" . $i] != "") {
				$c = $c + 1;
				$bt_frv_val = explode("@", $bt_frv_amt[$date_asc . "@" . $i]);

				$bt_paid = $bt_paid + $bt_frv_val[4];

				$rows++;
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A" . "" . $rows, $sno++)
					->setCellValue("B" . "" . $rows, date("d.m.Y", strtotime($bt_frv_val[1])))
					->setCellValue("C" . "" . $rows, $coaname[$bt_frv_val[7]])
					->setCellValue("D" . "" . $rows, $bt_frv_val[0])
					->setCellValue("E" . "" . $rows, 'Receipt Voucher')
					->setCellValue("F" . "" . $rows, $bt_frv_val[3])
					->setCellValue("G" . "" . $rows, $whname[$bt_frv_val[5]])
					->setCellValue("H" . "" . $rows, $bt_frv_val[6])
					->setCellValue("I" . "" . $rows, $bt_frv_val[4])
					->setCellValue("J" . "" . $rows, '');
			} else {
			}
		}

		$ccount = sizeof($bt_fjv_amt);

		for ($i = 1; $i <= $ccount; $i++) {
			if ($bt_fjv_amt[$date_asc . "@" . $i] != "") {
				$c = $c + 1;
				$bt_fjv_val = explode("@", $bt_fjv_amt[$date_asc . "@" . $i]);

				$bt_paid = $bt_paid + $bt_fjv_val[4];


				$rows++;
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A" . "" . $rows, $sno++)
					->setCellValue("B" . "" . $rows, date("d.m.Y", strtotime($bt_fjv_val[1])))
					->setCellValue("C" . "" . $rows, $coaname[$bt_fjv_val[7]])
					->setCellValue("D" . "" . $rows, $bt_fjv_val[0])
					->setCellValue("E" . "" . $rows, 'Journal Voucher')
					->setCellValue("F" . "" . $rows, $bt_fjv_val[3])
					->setCellValue("G" . "" . $rows, $whname[$bt_fjv_val[5]])
					->setCellValue("H" . "" . $rows, $bt_fjv_val[6])
					->setCellValue("I" . "" . $rows, $bt_fjv_val[4])
					->setCellValue("J" . "" . $rows, '');
			} else {
			}
		}

		$ccount = sizeof($bt_foth_vou_amt);

		for ($i = 1; $i <= $ccount; $i++) {
			if ($bt_foth_vou_amt[$date_asc . "@" . $i] != "") {
				$c = $c + 1;
				$bt_foth_vou_val = explode("@", $bt_foth_vou_amt[$date_asc . "@" . $i]);
				$bt_paid = $bt_paid + $bt_foth_vou_val[4];



				$rows++;
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A" . "" . $rows, $sno++)
					->setCellValue("B" . "" . $rows, date("d.m.Y", strtotime($bt_foth_vou_val[1])))
					->setCellValue("C" . "" . $rows, $coaname[$bt_foth_vou_val[7]])
					->setCellValue("D" . "" . $rows, $bt_foth_vou_val[0])
					->setCellValue("E" . "" . $rows, 'OTH Voucher')
					->setCellValue("F" . "" . $rows, $bt_foth_vou_val[3])
					->setCellValue("G" . "" . $rows, $whname[$bt_foth_vou_val[5]])
					->setCellValue("H" . "" . $rows, $bt_foth_vou_val[6])
					->setCellValue("I" . "" . $rows, $bt_foth_vou_val[4])
					->setCellValue("J" . "" . $rows, '');
			} else {
			}
		}

		$ccount = sizeof($bt_tpv_amt);

		for ($i = 1; $i <= $ccount; $i++) {
			if ($bt_tpv_amt[$date_asc . "@" . $i] != "") {
				$c = $c + 1;
				$bt_tpv_val = explode("@", $bt_tpv_amt[$date_asc . "@" . $i]);
				$bt_received = $bt_received + $bt_tpv_val[4];


				$rows++;
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A" . "" . $rows, $sno++)
					->setCellValue("B" . "" . $rows, date("d.m.Y", strtotime($bt_tpv_val[1])))
					->setCellValue("C" . "" . $rows, $coaname[$bt_tpv_val[7]])
					->setCellValue("D" . "" . $rows, $bt_tpv_val[0])
					->setCellValue("E" . "" . $rows, 'Payment Voucher')
					->setCellValue("F" . "" . $rows, $bt_tpv_val[3])
					->setCellValue("G" . "" . $rows, $whname[$bt_tpv_val[5]])
					->setCellValue("H" . "" . $rows, $bt_tpv_val[6])
					->setCellValue("I" . "" . $rows, '')
					->setCellValue("J" . "" . $rows, $bt_tpv_val[4]);
			} else {
			}
		}

		$ccount = sizeof($bt_trv_amt);

		for ($i = 1; $i <= $ccount; $i++) {
			if ($bt_trv_amt[$date_asc . "@" . $i] != "") {
				$c = $c + 1;
				$bt_trv_val = explode("@", $bt_trv_amt[$date_asc . "@" . $i]);
				$bt_received = $bt_received + $bt_trv_val[4];


				$rows++;
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A" . "" . $rows, $sno++)
					->setCellValue("B" . "" . $rows, date("d.m.Y", strtotime($bt_trv_val[1])))
					->setCellValue("C" . "" . $rows, $coaname[$bt_trv_val[7]])
					->setCellValue("D" . "" . $rows, $bt_trv_val[0])
					->setCellValue("E" . "" . $rows, 'Receipt Voucher')
					->setCellValue("F" . "" . $rows, $bt_trv_val[3])
					->setCellValue("G" . "" . $rows, $whname[$bt_trv_val[5]])
					->setCellValue("H" . "" . $rows, $bt_trv_val[6])
					->setCellValue("I" . "" . $rows, '')
					->setCellValue("J" . "" . $rows, $bt_trv_val[4]);
			} else {
			}
		}

		$ccount = sizeof($bt_tjv_amt);

		for ($i = 1; $i <= $ccount; $i++) {
			if ($bt_tjv_amt[$date_asc . "@" . $i] != "") {
				$c = $c + 1;
				$bt_tjv_val = explode("@", $bt_tjv_amt[$date_asc . "@" . $i]);
				$bt_received = $bt_received + $bt_tjv_val[4];


				$rows++;
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A" . "" . $rows, $sno++)
					->setCellValue("B" . "" . $rows, date("d.m.Y", strtotime($bt_tjv_val[1])))
					->setCellValue("C" . "" . $rows, $coaname[$bt_tjv_val[7]])
					->setCellValue("D" . "" . $rows, $bt_tjv_val[0])
					->setCellValue("E" . "" . $rows, 'Journal Voucher')
					->setCellValue("F" . "" . $rows, $bt_tjv_val[3])
					->setCellValue("G" . "" . $rows, $whname[$bt_tjv_val[5]])
					->setCellValue("H" . "" . $rows, $bt_tjv_val[6])
					->setCellValue("I" . "" . $rows, '')
					->setCellValue("J" . "" . $rows, $bt_tjv_val[4]);
			} else {
			}
		}

		$ccount = sizeof($bt_toth_vou_amt);

		for ($i = 1; $i <= $ccount; $i++) {
			if ($bt_toth_vou_amt[$date_asc . "@" . $i] != "") {
				$c = $c + 1;
				$bt_toth_vou_val = explode("@", $bt_toth_vou_amt[$date_asc . "@" . $i]);
				$bt_received = $bt_received + $bt_toth_vou_val[4];


				$rows++;
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A" . "" . $rows, $sno++)
					->setCellValue("B" . "" . $rows, date("d.m.Y", strtotime($bt_toth_vou_val[1])))
					->setCellValue("C" . "" . $rows, $coaname[$bt_toth_vou_val[7]])
					->setCellValue("D" . "" . $rows, $bt_toth_vou_val[0])
					->setCellValue("E" . "" . $rows, 'OTH Voucher')
					->setCellValue("F" . "" . $rows, $bt_toth_vou_val[3])
					->setCellValue("G" . "" . $rows, $whname[$bt_toth_vou_val[5]])
					->setCellValue("H" . "" . $rows, $bt_toth_vou_val[6])
					->setCellValue("I" . "" . $rows, '')
					->setCellValue("J" . "" . $rows, $bt_toth_vou_val[4]);
			} else {
			}
		}
	}
	$rows++;
	$column_colspan = $ch[1] . $rows . ":" . $ch[8] . $rows;
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[1] . $rows, 'Between Days Total');
	$objPHPExcel->getActiveSheet()->getStyle($ch[1] . $rows)->getAlignment()->applyFromArray(
		array(
			"horizontal" => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			"vertical" => \PHPExcel_Style_Alignment::VERTICAL_CENTER
		)
	);

	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($ch[9] . $rows, $bt_paid);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($ch[10] . $rows, $bt_received);

	$from = "A" . $rows; // or any value
	$to = "J" . $rows; // or any value
	$objPHPExcel->getActiveSheet()->getStyle("$from:$to")->getFont()->setBold(true);

	$rows++;
	$column_colspan = $ch[1] . $rows . ":" . $ch[8] . $rows;
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[1] . $rows, 'Closing Balance');
	$objPHPExcel->getActiveSheet()->getStyle($ch[1] . $rows)->getAlignment()->applyFromArray(
		array(
			"horizontal" => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			"vertical" => \PHPExcel_Style_Alignment::VERTICAL_CENTER
		)
	);

	$column_colspan = $ch[9] . $rows . ":" . $ch[10] . $rows;
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[9] . $rows, ($pre_received - $pre_paid) + ($bt_received - $bt_paid));
	
	$from = "A" . $rows; // or any value
	$to = "J" . $rows; // or any value
	$objPHPExcel->getActiveSheet()->getStyle("$from:$to")->getFont()->setBold(true);

} else if ($_GET['coa'] != "all" && in_array("all", $_GET['sectors'])) {
	$fdate = date("Y-m-d", strtotime($_GET['fromdate']));
	$tdate = date("Y-m-d", strtotime($_GET['todate']));
	if ($_GET['coa'] == "all") {
		$coa = $allCoA;
	} else {
		$coa = $_GET['coa'];
	}
	// $wname = $_GET['whname'];
	$sectors = $_GET['sectors']; if($sectors == "all"){ $sec_fltr = ""; } else{ $sec_list = implode("','",explode(",",$sectors)); $sec_fltr = " AND `warehouse` IN ('$sec_list')"; }

	$sql = "SELECT SUM(amount) as tamt,prefix FROM `acc_vouchers` WHERE `date` < '$fdate' AND `active` = '1' AND `fcoa` IN ('$coa') GROUP BY `prefix` ORDER BY `fcoa` ASC";
	$query = mysqli_query($conn, $sql);
	while ($row = mysqli_fetch_assoc($query)) {
		if ($row['prefix'] == "PV") {
			$pre_fpv_amt = $row['tamt'];
		} else if ($row['prefix'] == "RV") {
			$pre_frv_amt = $row['tamt'];
		} else if ($row['prefix'] == "JV") {
			$pre_fjv_amt = $row['tamt'];
		} else {
			$pre_foth_vou_amt = $row['tamt'];
		}
	}
	$sql = "SELECT SUM(amount) as tamt,prefix FROM `acc_vouchers` WHERE `date` < '$fdate' AND `active` = '1' AND `tcoa` IN ('$coa') GROUP BY `prefix` ORDER BY `tcoa` ASC";
	$query = mysqli_query($conn, $sql);
	while ($row = mysqli_fetch_assoc($query)) {
		if ($row['prefix'] == "PV") {
			$pre_tpv_amt = $row['tamt'];
		} else if ($row['prefix'] == "RV") {
			$pre_trv_amt = $row['tamt'];
		} else if ($row['prefix'] == "JV") {
			$pre_tjv_amt = $row['tamt'];
		} else {
			$pre_toth_vou_amt = $row['tamt'];
		}
	}
	//echo number_format_ind($pre_pur_pay_amt)."".number_format_ind($pre_cdn_amt)."".number_format_ind($pre_sdn_amt)."".number_format_ind($pre_fpv_amt)."".number_format_ind($pre_frv_amt)."".number_format_ind($pre_fjv_amt)."".number_format_ind($pre_foth_vou_amt);
	$pre_paid = $pre_pur_pay_amt + $pre_cdn_amt + $pre_sdn_amt + $pre_fpv_amt + $pre_frv_amt + $pre_fjv_amt + $pre_foth_vou_amt;
	$pre_received = $pre_cus_rct_amt + $pre_ccn_amt + $pre_scn_amt + $pre_tpv_amt + $pre_trv_amt + $pre_tjv_amt + $pre_toth_vou_amt;
	if ($pre_paid > $pre_received) {
		$pending_pay = $pre_paid - $pre_received;
		$closing_amt = $pre_received - $pre_paid;
	} else {
		$closing_amt = $pending_rct = $pre_received - $pre_paid;
	}
	$c = 0;

	$c = $d = $e = $f = $g = 0;
	$bt_fpv_amt = array();
	$bt_frv_amt = array();
	$bt_fjv_amt = array();
	$bt_foth_vou_amt = array();
	$sql = "SELECT * FROM `acc_vouchers` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1' AND `fcoa` IN ('$coa') ORDER BY `fcoa` ASC";
	$query = mysqli_query($conn, $sql);
	while ($row = mysqli_fetch_assoc($query)) {
		if ($row['prefix'] == "PV") {
			$c = $c + 1;
			$bt_fpv_amt[$row['date'] . "@" . $c] = $row['trnum'] . "@" . $row['date'] . "@" . $row['tcoa'] . "@" . $row['dcno'] . "@" . $row['amount'] . "@" . $row['warehouse'] . "@" . $row['remarks'];
		} else if ($row['prefix'] == "RV") {
			$d = $d + 1;
			$bt_frv_amt[$row['date'] . "@" . $d] = $row['trnum'] . "@" . $row['date'] . "@" . $row['tcoa'] . "@" . $row['dcno'] . "@" . $row['amount'] . "@" . $row['warehouse'] . "@" . $row['remarks'];
		} else if ($row['prefix'] == "JV") {
			$e = $e + 1;
			$bt_fjv_amt[$row['date'] . "@" . $e] = $row['trnum'] . "@" . $row['date'] . "@" . $row['tcoa'] . "@" . $row['dcno'] . "@" . $row['amount'] . "@" . $row['warehouse'] . "@" . $row['remarks'];
		} else {
			$f = $f + 1;
			$bt_foth_vou_amt[$row['date'] . "@" . $f] = $row['trnum'] . "@" . $row['date'] . "@" . $row['tcoa'] . "@" . $row['dcno'] . "@" . $row['amount'] . "@" . $row['warehouse'] . "@" . $row['remarks'];
		}
	}
	$c = $d = $e = $f = $g = 0;
	$bt_tpv_amt = array();
	$bt_trv_amt = array();
	$bt_tjv_amt = array();
	$bt_toth_vou_amt = array();
	$sql = "SELECT * FROM `acc_vouchers` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1' AND `tcoa` IN ('$coa') ORDER BY `tcoa` ASC";
	$query = mysqli_query($conn, $sql);
	while ($row = mysqli_fetch_assoc($query)) {
		if ($row['prefix'] == "PV") {
			$c = $c + 1;
			$bt_tpv_amt[$row['date'] . "@" . $c] = $row['trnum'] . "@" . $row['date'] . "@" . $row['fcoa'] . "@" . $row['dcno'] . "@" . $row['amount'] . "@" . $row['warehouse'] . "@" . $row['remarks'];
		} else if ($row['prefix'] == "RV") {
			$d = $d + 1;
			$bt_trv_amt[$row['date'] . "@" . $d] = $row['trnum'] . "@" . $row['date'] . "@" . $row['fcoa'] . "@" . $row['dcno'] . "@" . $row['amount'] . "@" . $row['warehouse'] . "@" . $row['remarks'];
		} else if ($row['prefix'] == "JV") {
			$e = $e + 1;
			$bt_tjv_amt[$row['date'] . "@" . $e] = $row['trnum'] . "@" . $row['date'] . "@" . $row['fcoa'] . "@" . $row['dcno'] . "@" . $row['amount'] . "@" . $row['warehouse'] . "@" . $row['remarks'];
		} else {
			$f = $f + 1;
			$bt_toth_vou_amt[$row['date'] . "@" . $f] = $row['trnum'] . "@" . $row['date'] . "@" . $row['fcoa'] . "@" . $row['dcno'] . "@" . $row['amount'] . "@" . $row['warehouse'] . "@" . $row['remarks'];
		}
	}


	$rows++;$rows++;
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue("A" . "" . $rows, 'Sl.No.')
		->setCellValue("B" . "" . $rows, 'Date')
		->setCellValue("C" . "" . $rows, 'Transaction No')
		->setCellValue("D" . "" . $rows, 'Transaction Type')
		->setCellValue("E" . "" . $rows, 'Doc. No.')
		->setCellValue("F" . "" . $rows, 'From Warehouse')
		->setCellValue("G" . "" . $rows, 'Remarks')
		->setCellValue("H" . "" . $rows, 'Paid')
		->setCellValue("I" . "" . $rows, 'Received')
		->setCellValue("J" . "" . $rows, 'Running Balance');

		
$from = "A1"; // or any value
$to = $ch[10] . $rows; // or any value
$objPHPExcel->getActiveSheet()->getStyle("$from:$to")->getFont()->setBold(true);

	$rows++;
	$column_colspan = $ch[1] . $rows . ":" . $ch[7] . $rows;
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[7] . $rows, "Previous Balance");
	$objPHPExcel->getActiveSheet()->getStyle($ch[7] . $rows)->getAlignment()->applyFromArray(
		array(
			"horizontal" => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			"vertical" => \PHPExcel_Style_Alignment::VERTICAL_CENTER
		)
	);

	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($ch[8] . $rows, $pre_paid);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($ch[9] . $rows, $pre_received);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($ch[10] . $rows, $closing_amt);

	$fdate = strtotime($fdate);
	$tdate = strtotime($tdate);
	$bt_paid = $bt_received = $c = 0;
	for ($currentDate = $fdate; $currentDate <= $tdate; $currentDate += (86400)) {
		$row['trnum'] . "@" . $row['date'] . "@" . $row['ccode'] . "@" . $row['docno'] . "@" . $row['cdate'] . "@" . $row['cno'] . "@" . $row['amount']
			. "@" . $row['vtype'] . "@" . $row['warehouse'] . "@" . $row['remarks'];
		$date_asc = date('Y-m-d', $currentDate);

		$ccount = sizeof($bt_fpv_amt);
		for ($i = 1; $i <= $ccount; $i++) {
			if ($bt_fpv_amt[$date_asc . "@" . $i] != "") {
				$c = $c + 1;
				$bt_fpv_val = explode("@", $bt_fpv_amt[$date_asc . "@" . $i]);
				$bt_paid = $bt_paid + $bt_fpv_val[4];
				$closing_amt = $closing_amt - $bt_fpv_val[4];

				$rows++;
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A" . "" . $rows, $c)
					->setCellValue("B" . "" . $rows, date("d.m.Y", strtotime($bt_fpv_val[1])))
					->setCellValue("C" . "" . $rows, $bt_fpv_val[0])
					->setCellValue("D" . "" . $rows, 'Payment Voucher')
					->setCellValue("E" . "" . $rows, $bt_fpv_val[3])
					->setCellValue("F" . "" . $rows, $whname[$bt_fpv_val[5]])
					->setCellValue("G" . "" . $rows, $bt_fpv_val[6])
					->setCellValue("H" . "" . $rows, $bt_fpv_val[4])
					->setCellValue("I" . "" . $rows, '')
					->setCellValue("J" . "" . $rows, $closing_amt);
			} else {
			}
		}
		$ccount = sizeof($bt_frv_amt);
		for ($i = 1; $i <= $ccount; $i++) {
			if ($bt_frv_amt[$date_asc . "@" . $i] != "") {
				$c = $c + 1;
				$bt_frv_val = explode("@", $bt_frv_amt[$date_asc . "@" . $i]);
				$bt_paid = $bt_paid + $bt_frv_val[4];
				$closing_amt = $closing_amt - $bt_frv_val[4];



				$rows++;
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A" . "" . $rows, $c)
					->setCellValue("B" . "" . $rows, date("d.m.Y", strtotime($bt_frv_val[1])))
					->setCellValue("C" . "" . $rows, $bt_frv_val[0])
					->setCellValue("D" . "" . $rows, 'Receipt Voucher')
					->setCellValue("E" . "" . $rows, $bt_frv_val[3])
					->setCellValue("F" . "" . $rows, $whname[$bt_frv_val[5]])
					->setCellValue("G" . "" . $rows, $bt_frv_val[6])
					->setCellValue("H" . "" . $rows, $bt_frv_val[4])
					->setCellValue("I" . "" . $rows, '')
					->setCellValue("J" . "" . $rows, $closing_amt);
			} else {
			}
		}
		$ccount = sizeof($bt_fjv_amt);
		for ($i = 1; $i <= $ccount; $i++) {
			if ($bt_fjv_amt[$date_asc . "@" . $i] != "") {
				$c = $c + 1;
				$bt_fjv_val = explode("@", $bt_fjv_amt[$date_asc . "@" . $i]);
				$bt_paid = $bt_paid + $bt_fjv_val[4];
				$closing_amt = $closing_amt - $bt_fjv_val[4];





				$rows++;
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A" . "" . $rows, $c)
					->setCellValue("B" . "" . $rows, date("d.m.Y", strtotime($bt_fjv_val[1])))
					->setCellValue("C" . "" . $rows, $bt_fjv_val[0])
					->setCellValue("D" . "" . $rows, 'Journal Voucher')
					->setCellValue("E" . "" . $rows, $bt_fjv_val[3])
					->setCellValue("F" . "" . $rows, $whname[$bt_fjv_val[5]])
					->setCellValue("G" . "" . $rows, $bt_fjv_val[6])
					->setCellValue("H" . "" . $rows, $bt_fjv_val[4])
					->setCellValue("I" . "" . $rows, '')
					->setCellValue("J" . "" . $rows, $closing_amt);
			} else {
			}
		}
		$ccount = sizeof($bt_foth_vou_amt);
		for ($i = 1; $i <= $ccount; $i++) {
			if ($bt_foth_vou_amt[$date_asc . "@" . $i] != "") {
				$c = $c + 1;
				$bt_foth_vou_val = explode("@", $bt_foth_vou_amt[$date_asc . "@" . $i]);
				$bt_paid = $bt_paid + $bt_foth_vou_val[4];
				$closing_amt = $closing_amt - $bt_foth_vou_val[4];

				$rows++;
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A" . "" . $rows, $c)
					->setCellValue("B" . "" . $rows, date("d.m.Y", strtotime($bt_foth_vou_val[1])))
					->setCellValue("C" . "" . $rows, $bt_foth_vou_val[0])
					->setCellValue("D" . "" . $rows, 'OTH Voucher')
					->setCellValue("E" . "" . $rows, $bt_foth_vou_val[3])
					->setCellValue("F" . "" . $rows, $whname[$bt_foth_vou_val[5]])
					->setCellValue("G" . "" . $rows, $bt_foth_vou_val[6])
					->setCellValue("H" . "" . $rows, $bt_foth_vou_val[4])
					->setCellValue("I" . "" . $rows, '')
					->setCellValue("J" . "" . $rows, $closing_amt);
			} else {
			}
		}

		$ccount = sizeof($bt_tpv_amt);
		for ($i = 1; $i <= $ccount; $i++) {
			if ($bt_tpv_amt[$date_asc . "@" . $i] != "") {
				$c = $c + 1;
				$bt_tpv_val = explode("@", $bt_tpv_amt[$date_asc . "@" . $i]);
				$bt_received = $bt_received + $bt_tpv_val[4];
				$closing_amt = $closing_amt + $bt_tpv_val[4];


				$rows++;
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A" . "" . $rows, $c)
					->setCellValue("B" . "" . $rows, date("d.m.Y", strtotime($bt_tpv_val[1])))
					->setCellValue("C" . "" . $rows, $bt_tpv_val[0])
					->setCellValue("D" . "" . $rows, 'Payment Voucher')
					->setCellValue("E" . "" . $rows, $bt_tpv_val[3])
					->setCellValue("F" . "" . $rows, $whname[$bt_tpv_val[5]])
					->setCellValue("G" . "" . $rows, $bt_tpv_val[6])
					->setCellValue("H" . "" . $rows, '')
					->setCellValue("I" . "" . $rows, $bt_tpv_val[4])
					->setCellValue("J" . "" . $rows, $closing_amt);
			} else {
			}
		}
		$ccount = sizeof($bt_trv_amt);
		for ($i = 1; $i <= $ccount; $i++) {
			if ($bt_trv_amt[$date_asc . "@" . $i] != "") {
				$c = $c + 1;
				$bt_trv_val = explode("@", $bt_trv_amt[$date_asc . "@" . $i]);
				$bt_received = $bt_received + $bt_trv_val[4];
				$closing_amt = $closing_amt + $bt_trv_val[4];



				$rows++;
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A" . "" . $rows, $c)
					->setCellValue("B" . "" . $rows, date("d.m.Y", strtotime($bt_trv_val[1])))
					->setCellValue("C" . "" . $rows, $bt_trv_val[0])
					->setCellValue("D" . "" . $rows, 'Receipt Voucher')
					->setCellValue("E" . "" . $rows, $bt_trv_val[3])
					->setCellValue("F" . "" . $rows, $whname[$bt_trv_val[5]])
					->setCellValue("G" . "" . $rows, $bt_trv_val[6])
					->setCellValue("H" . "" . $rows, '')
					->setCellValue("I" . "" . $rows, $bt_trv_val[4])
					->setCellValue("J" . "" . $rows, $closing_amt);
			} else {
			}
		}
		$ccount = sizeof($bt_tjv_amt);
		for ($i = 1; $i <= $ccount; $i++) {
			if ($bt_tjv_amt[$date_asc . "@" . $i] != "") {
				$c = $c + 1;
				$bt_tjv_val = explode("@", $bt_tjv_amt[$date_asc . "@" . $i]);
				$bt_received = $bt_received + $bt_tjv_val[4];
				$closing_amt = $closing_amt + $bt_tjv_val[4];


				$rows++;
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A" . "" . $rows, $c)
					->setCellValue("B" . "" . $rows, date("d.m.Y", strtotime($bt_tjv_val[1])))
					->setCellValue("C" . "" . $rows, $bt_tjv_val[0])
					->setCellValue("D" . "" . $rows, 'Journal Voucher')
					->setCellValue("E" . "" . $rows, $bt_tjv_val[3])
					->setCellValue("F" . "" . $rows, $whname[$bt_tjv_val[5]])
					->setCellValue("G" . "" . $rows, $bt_tjv_val[6])
					->setCellValue("H" . "" . $rows, '')
					->setCellValue("I" . "" . $rows, $bt_tjv_val[4])
					->setCellValue("J" . "" . $rows, $closing_amt);
			} else {
			}
		}
		$ccount = sizeof($bt_toth_vou_amt);
		for ($i = 1; $i <= $ccount; $i++) {
			if ($bt_toth_vou_amt[$date_asc . "@" . $i] != "") {
				$c = $c + 1;
				$bt_toth_vou_val = explode("@", $bt_toth_vou_amt[$date_asc . "@" . $i]);
				$bt_received = $bt_received + $bt_toth_vou_val[4];
				$closing_amt = $closing_amt + $bt_toth_vou_val[4];



				$rows++;
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A" . "" . $rows, $c)
					->setCellValue("B" . "" . $rows, date("d.m.Y", strtotime($bt_toth_vou_val[1])))
					->setCellValue("C" . "" . $rows, $bt_toth_vou_val[0])
					->setCellValue("D" . "" . $rows, 'OTH Voucher')
					->setCellValue("E" . "" . $rows, $bt_toth_vou_val[3])
					->setCellValue("F" . "" . $rows, $whname[$bt_toth_vou_val[5]])
					->setCellValue("G" . "" . $rows, $bt_toth_vou_val[6])
					->setCellValue("H" . "" . $rows, '')
					->setCellValue("I" . "" . $rows, $bt_toth_vou_val[4])
					->setCellValue("J" . "" . $rows, $closing_amt);
			} else {
			}
		}
	}


	$rows++;
	$column_colspan = $ch[1] . $rows . ":" . $ch[7] . $rows;
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[1] . $rows, 'Between Days Total');
	$objPHPExcel->getActiveSheet()->getStyle($ch[1] . $rows)->getAlignment()->applyFromArray(
		array(
			"horizontal" => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			"vertical" => \PHPExcel_Style_Alignment::VERTICAL_CENTER
		)
	);

	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($ch[8] . $rows, $bt_paid);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($ch[9] . $rows, $bt_received);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($ch[10] . $rows, '');

	$from = "A" . $rows; // or any value
	$to = "J" . $rows; // or any value
	$objPHPExcel->getActiveSheet()->getStyle("$from:$to")->getFont()->setBold(true);

	$rows++;
	$column_colspan = $ch[1] . $rows . ":" . $ch[7] . $rows;
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[1] . $rows, 'Closing Balance');
	$objPHPExcel->getActiveSheet()->getStyle($ch[1] . $rows)->getAlignment()->applyFromArray(
		array(
			"horizontal" => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			"vertical" => \PHPExcel_Style_Alignment::VERTICAL_CENTER
		)
	);

	$column_colspan = $ch[8] . $rows . ":" . $ch[9] . $rows;
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[8] . $rows, ($pre_received - $pre_paid) + ($bt_received - $bt_paid));
	


	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($ch[10] . $rows, '');

	$from = "A" . $rows; // or any value
	$to = "J" . $rows; // or any value
	$objPHPExcel->getActiveSheet()->getStyle("$from:$to")->getFont()->setBold(true);
} else if ($_GET['coa'] != "all" and !in_array("all", $_GET['sectors'])) {
	$fdate = date("Y-m-d", strtotime($_GET['fromdate']));
	$tdate = date("Y-m-d", strtotime($_GET['todate']));
	if ($_GET['coa'] == "all") {
		$coa = $allCoA;
	} else {
		$coa = $_GET['coa'];
	}
	// $wname = $_GET['whname'];
	$sectors = $_GET['sectors']; if($sectors == "all"){ $sec_fltr = ""; } else{ $sec_list = implode("','",explode(",",$sectors)); $sec_fltr = " AND `warehouse` IN ('$sec_list')"; }

	$sql = "SELECT SUM(amount) as tamt,prefix FROM `acc_vouchers` WHERE `date` < '$fdate' AND `active` = '1' AND `fcoa` IN ('$coa') ".$sec_fltr." GROUP BY `prefix` ORDER BY `fcoa` ASC";
	$query = mysqli_query($conn, $sql);
	while ($row = mysqli_fetch_assoc($query)) {
		if ($row['prefix'] == "PV") {
			$pre_fpv_amt = $row['tamt'];
		} else if ($row['prefix'] == "RV") {
			$pre_frv_amt = $row['tamt'];
		} else if ($row['prefix'] == "JV") {
			$pre_fjv_amt = $row['tamt'];
		} else {
			$pre_foth_vou_amt = $row['tamt'];
		}
	}
	$sql = "SELECT SUM(amount) as tamt,prefix FROM `acc_vouchers` WHERE `date` < '$fdate' AND `active` = '1' AND `tcoa` IN ('$coa') ".$sec_fltr." GROUP BY `prefix` ORDER BY `tcoa` ASC";
	$query = mysqli_query($conn, $sql);
	while ($row = mysqli_fetch_assoc($query)) {
		if ($row['prefix'] == "PV") {
			$pre_tpv_amt = $row['tamt'];
		} else if ($row['prefix'] == "RV") {
			$pre_trv_amt = $row['tamt'];
		} else if ($row['prefix'] == "JV") {
			$pre_tjv_amt = $row['tamt'];
		} else {
			$pre_toth_vou_amt = $row['tamt'];
		}
	}
	//echo number_format_ind($pre_pur_pay_amt)."".number_format_ind($pre_cdn_amt)."".number_format_ind($pre_sdn_amt)."".number_format_ind($pre_fpv_amt)."".number_format_ind($pre_frv_amt)."".number_format_ind($pre_fjv_amt)."".number_format_ind($pre_foth_vou_amt);
	$pre_paid = $pre_pur_pay_amt + $pre_cdn_amt + $pre_sdn_amt + $pre_fpv_amt + $pre_frv_amt + $pre_fjv_amt + $pre_foth_vou_amt;
	$pre_received = $pre_cus_rct_amt + $pre_ccn_amt + $pre_scn_amt + $pre_tpv_amt + $pre_trv_amt + $pre_tjv_amt + $pre_toth_vou_amt;
	if ($pre_paid > $pre_received) {
		$pending_pay = $pre_paid - $pre_received;
		$closing_amt = $pre_received - $pre_paid;
	} else {
		$closing_amt = $pending_rct = $pre_received - $pre_paid;
	}

	$c = $d = $e = $f = $g = 0;
	$bt_fpv_amt = $bt_frv_amt = $bt_fjv_amt = $bt_foth_vou_amt = $bt_tpv_amt = $bt_trv_amt = $bt_tjv_amt = $bt_toth_vou_amt = array();
	$sql = "SELECT * FROM `acc_vouchers` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1' AND `fcoa` IN ('$coa') ".$sec_fltr." ORDER BY `fcoa` ASC";
	$query = mysqli_query($conn, $sql);
	while ($row = mysqli_fetch_assoc($query)) {
		if ($row['prefix'] == "PV") {
			$c = $c + 1;
			$bt_fpv_amt[$row['date'] . "@" . $c] = $row['trnum'] . "@" . $row['date'] . "@" . $row['tcoa'] . "@" . $row['dcno'] . "@" . $row['amount'] . "@" . $row['warehouse'] . "@" . $row['remarks'];
		} else if ($row['prefix'] == "RV") {
			$d = $d + 1;
			$bt_frv_amt[$row['date'] . "@" . $d] = $row['trnum'] . "@" . $row['date'] . "@" . $row['tcoa'] . "@" . $row['dcno'] . "@" . $row['amount'] . "@" . $row['warehouse'] . "@" . $row['remarks'];
		} else if ($row['prefix'] == "JV") {
			$e = $e + 1;
			$bt_fjv_amt[$row['date'] . "@" . $e] = $row['trnum'] . "@" . $row['date'] . "@" . $row['tcoa'] . "@" . $row['dcno'] . "@" . $row['amount'] . "@" . $row['warehouse'] . "@" . $row['remarks'];
		} else {
			$f = $f + 1;
			$bt_foth_vou_amt[$row['date'] . "@" . $f] = $row['trnum'] . "@" . $row['date'] . "@" . $row['tcoa'] . "@" . $row['dcno'] . "@" . $row['amount'] . "@" . $row['warehouse'] . "@" . $row['remarks'];
		}
	}
	$c = $d = $e = $f = $g = 0;
	$sql = "SELECT * FROM `acc_vouchers` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1' AND `tcoa` IN ('$coa') ".$sec_fltr." ORDER BY `tcoa` ASC";
	$query = mysqli_query($conn, $sql);
	while ($row = mysqli_fetch_assoc($query)) {
		if ($row['prefix'] == "PV") {
			$c = $c + 1;
			$bt_tpv_amt[$row['date'] . "@" . $c] = $row['trnum'] . "@" . $row['date'] . "@" . $row['fcoa'] . "@" . $row['dcno'] . "@" . $row['amount'] . "@" . $row['warehouse'] . "@" . $row['remarks'];
		} else if ($row['prefix'] == "RV") {
			$d = $d + 1;
			$bt_trv_amt[$row['date'] . "@" . $d] = $row['trnum'] . "@" . $row['date'] . "@" . $row['fcoa'] . "@" . $row['dcno'] . "@" . $row['amount'] . "@" . $row['warehouse'] . "@" . $row['remarks'];
		} else if ($row['prefix'] == "JV") {
			$e = $e + 1;
			$bt_tjv_amt[$row['date'] . "@" . $e] = $row['trnum'] . "@" . $row['date'] . "@" . $row['fcoa'] . "@" . $row['dcno'] . "@" . $row['amount'] . "@" . $row['warehouse'] . "@" . $row['remarks'];
		} else {
			$f = $f + 1;
			$bt_toth_vou_amt[$row['date'] . "@" . $f] = $row['trnum'] . "@" . $row['date'] . "@" . $row['fcoa'] . "@" . $row['dcno'] . "@" . $row['amount'] . "@" . $row['warehouse'] . "@" . $row['remarks'];
		}
	}


	$rows++;$rows++;
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue("A" . "" . $rows, 'Sl.No.')
		->setCellValue("B" . "" . $rows, 'Date')
		->setCellValue("C" . "" . $rows, 'Transaction No')
		->setCellValue("D" . "" . $rows, 'Transaction Type')
		->setCellValue("E" . "" . $rows, 'Doc. No.')
		->setCellValue("F" . "" . $rows, 'From Warehouse')
		->setCellValue("G" . "" . $rows, 'Remarks')
		->setCellValue("H" . "" . $rows, 'Paid')
		->setCellValue("I" . "" . $rows, 'Received')
		->setCellValue("J" . "" . $rows, 'Running Balance');

		
$from = "A1"; // or any value
$to = $ch[10] . $rows; // or any value
$objPHPExcel->getActiveSheet()->getStyle("$from:$to")->getFont()->setBold(true);

	$rows++;
	$column_colspan = $ch[1] . $rows . ":" . $ch[7] . $rows;
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[7] . $rows, "Previous Balance");
	$objPHPExcel->getActiveSheet()->getStyle($ch[7] . $rows)->getAlignment()->applyFromArray(
		array(
			"horizontal" => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			"vertical" => \PHPExcel_Style_Alignment::VERTICAL_CENTER
		)
	);

	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($ch[8] . $rows, $pre_paid);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($ch[9] . $rows, $pre_received);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($ch[10] . $rows, $closing_amt);

	$fdate = strtotime($fdate);
	$tdate = strtotime($tdate);
	$bt_paid = $bt_received = $c = 0;
	for ($currentDate = $fdate; $currentDate <= $tdate; $currentDate += (86400)) {
		$row['trnum'] . "@" . $row['date'] . "@" . $row['ccode'] . "@" . $row['docno'] . "@" . $row['cdate'] . "@" . $row['cno'] . "@" . $row['amount']
			. "@" . $row['vtype'] . "@" . $row['warehouse'] . "@" . $row['remarks'];
		$date_asc = date('Y-m-d', $currentDate);

		$ccount = sizeof($bt_fpv_amt);
		for ($i = 1; $i <= $ccount; $i++) {
			if ($bt_fpv_amt[$date_asc . "@" . $i] != "") {
				$c = $c + 1;
				$bt_fpv_val = explode("@", $bt_fpv_amt[$date_asc . "@" . $i]);
				$bt_paid = $bt_paid + $bt_fpv_val[4];
				$closing_amt = $closing_amt - $bt_fpv_val[4];

				$rows++;
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A" . "" . $rows, $c)
					->setCellValue("B" . "" . $rows, date("d.m.Y", strtotime($bt_fpv_val[1])))
					->setCellValue("C" . "" . $rows, $bt_fpv_val[0])
					->setCellValue("D" . "" . $rows, 'Payment Voucher')
					->setCellValue("E" . "" . $rows, $bt_fpv_val[3])
					->setCellValue("F" . "" . $rows, $whname[$bt_fpv_val[5]])
					->setCellValue("G" . "" . $rows, $bt_fpv_val[6])
					->setCellValue("H" . "" . $rows, $bt_fpv_val[4])
					->setCellValue("I" . "" . $rows, '')
					->setCellValue("J" . "" . $rows, $closing_amt);
			} else {
			}
		}
		$ccount = sizeof($bt_frv_amt);
		for ($i = 1; $i <= $ccount; $i++) {
			if ($bt_frv_amt[$date_asc . "@" . $i] != "") {
				$c = $c + 1;
				$bt_frv_val = explode("@", $bt_frv_amt[$date_asc . "@" . $i]);
				$bt_paid = $bt_paid + $bt_frv_val[4];
				$closing_amt = $closing_amt - $bt_frv_val[4];


				$rows++;
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A" . "" . $rows, $c)
					->setCellValue("B" . "" . $rows, date("d.m.Y", strtotime($bt_frv_val[1])))
					->setCellValue("C" . "" . $rows, $bt_frv_val[0])
					->setCellValue("D" . "" . $rows, 'Receipt Voucher')
					->setCellValue("E" . "" . $rows, $bt_frv_val[3])
					->setCellValue("F" . "" . $rows, $whname[$bt_frv_val[5]])
					->setCellValue("G" . "" . $rows, $bt_frv_val[6])
					->setCellValue("H" . "" . $rows, $bt_frv_val[4])
					->setCellValue("I" . "" . $rows, '')
					->setCellValue("J" . "" . $rows, $closing_amt);
			} else {
			}
		}
		$ccount = sizeof($bt_fjv_amt);
		for ($i = 1; $i <= $ccount; $i++) {
			if ($bt_fjv_amt[$date_asc . "@" . $i] != "") {
				$c = $c + 1;
				$bt_fjv_val = explode("@", $bt_fjv_amt[$date_asc . "@" . $i]);
				$bt_paid = $bt_paid + $bt_fjv_val[4];
				$closing_amt = $closing_amt - $bt_fjv_val[4];


				$rows++;
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A" . "" . $rows, $c)
					->setCellValue("B" . "" . $rows, date("d.m.Y", strtotime($bt_fjv_val[1])))
					->setCellValue("C" . "" . $rows, $bt_fjv_val[0])
					->setCellValue("D" . "" . $rows, 'Journal Voucher')
					->setCellValue("E" . "" . $rows, $bt_fjv_val[3])
					->setCellValue("F" . "" . $rows, $whname[$bt_fjv_val[5]])
					->setCellValue("G" . "" . $rows, $bt_fjv_val[6])
					->setCellValue("H" . "" . $rows, $bt_fjv_val[4])
					->setCellValue("I" . "" . $rows, '')
					->setCellValue("J" . "" . $rows, $closing_amt);
			} else {
			}
		}
		$ccount = sizeof($bt_foth_vou_amt);
		for ($i = 1; $i <= $ccount; $i++) {
			if ($bt_foth_vou_amt[$date_asc . "@" . $i] != "") {
				$c = $c + 1;
				$bt_foth_vou_val = explode("@", $bt_foth_vou_amt[$date_asc . "@" . $i]);

				$bt_paid = $bt_paid + $bt_foth_vou_val[4];
				$closing_amt = $closing_amt - $bt_foth_vou_val[4];

				$rows++;
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A" . "" . $rows, $c)
					->setCellValue("B" . "" . $rows, date("d.m.Y", strtotime($bt_foth_vou_val[1])))
					->setCellValue("C" . "" . $rows, $bt_foth_vou_val[0])
					->setCellValue("D" . "" . $rows, 'OTH Voucher')
					->setCellValue("E" . "" . $rows, $bt_foth_vou_val[3])
					->setCellValue("F" . "" . $rows, $whname[$bt_foth_vou_val[5]])
					->setCellValue("G" . "" . $rows, $bt_foth_vou_val[6])
					->setCellValue("H" . "" . $rows, $bt_foth_vou_val[4])
					->setCellValue("I" . "" . $rows, '')
					->setCellValue("J" . "" . $rows, $closing_amt);
			} else {
			}
		}

		$ccount = sizeof($bt_tpv_amt);
		for ($i = 1; $i <= $ccount; $i++) {
			if ($bt_tpv_amt[$date_asc . "@" . $i] != "") {
				$c = $c + 1;
				$bt_tpv_val = explode("@", $bt_tpv_amt[$date_asc . "@" . $i]);
				$bt_received = $bt_received + $bt_tpv_val[4];
				$closing_amt = $closing_amt + $bt_tpv_val[4];



				$rows++;
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A" . "" . $rows, $c)
					->setCellValue("B" . "" . $rows, date("d.m.Y", strtotime($bt_tpv_val[1])))
					->setCellValue("C" . "" . $rows, $bt_tpv_val[0])
					->setCellValue("D" . "" . $rows, 'Payment Voucher')
					->setCellValue("E" . "" . $rows, $bt_tpv_val[3])
					->setCellValue("F" . "" . $rows, $whname[$bt_tpv_val[5]])
					->setCellValue("G" . "" . $rows, $bt_tpv_val[6])
					->setCellValue("H" . "" . $rows, '')
					->setCellValue("I" . "" . $rows, $bt_tpv_val[4])
					->setCellValue("J" . "" . $rows, $closing_amt);
			} else {
			}
		}
		$ccount = sizeof($bt_trv_amt);
		for ($i = 1; $i <= $ccount; $i++) {
			if ($bt_trv_amt[$date_asc . "@" . $i] != "") {
				$c = $c + 1;
				$bt_trv_val = explode("@", $bt_trv_amt[$date_asc . "@" . $i]);
				$bt_received = $bt_received + $bt_trv_val[4];
				$closing_amt = $closing_amt + $bt_trv_val[4];

				$rows++;
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A" . "" . $rows, $c)
					->setCellValue("B" . "" . $rows, date("d.m.Y", strtotime($bt_trv_val[1])))
					->setCellValue("C" . "" . $rows, $bt_trv_val[0])
					->setCellValue("D" . "" . $rows, 'Receipt Voucher')
					->setCellValue("E" . "" . $rows, $bt_trv_val[3])
					->setCellValue("F" . "" . $rows, $whname[$bt_trv_val[5]])
					->setCellValue("G" . "" . $rows, $bt_trv_val[6])
					->setCellValue("H" . "" . $rows, '')
					->setCellValue("I" . "" . $rows, $bt_trv_val[4])
					->setCellValue("J" . "" . $rows, $closing_amt);
			} else {
			}
		}
		$ccount = sizeof($bt_tjv_amt);
		for ($i = 1; $i <= $ccount; $i++) {
			if ($bt_tjv_amt[$date_asc . "@" . $i] != "") {
				$c = $c + 1;
				$bt_tjv_val = explode("@", $bt_tjv_amt[$date_asc . "@" . $i]);
				$bt_received = $bt_received + $bt_tjv_val[4];
				$closing_amt = $closing_amt + $bt_tjv_val[4];



				$rows++;
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A" . "" . $rows, $c)
					->setCellValue("B" . "" . $rows, date("d.m.Y", strtotime($bt_tjv_val[1])))
					->setCellValue("C" . "" . $rows, $bt_tjv_val[0])
					->setCellValue("D" . "" . $rows, 'Journal Voucher')
					->setCellValue("E" . "" . $rows, $bt_tjv_val[3])
					->setCellValue("F" . "" . $rows, $whname[$bt_tjv_val[5]])
					->setCellValue("G" . "" . $rows, $bt_tjv_val[6])
					->setCellValue("H" . "" . $rows, '')
					->setCellValue("I" . "" . $rows, $bt_tjv_val[4])
					->setCellValue("J" . "" . $rows, $closing_amt);
			} else {
			}
		}
		$ccount = sizeof($bt_toth_vou_amt);
		for ($i = 1; $i <= $ccount; $i++) {
			if ($bt_toth_vou_amt[$date_asc . "@" . $i] != "") {
				$c = $c + 1;
				$bt_toth_vou_val = explode("@", $bt_toth_vou_amt[$date_asc . "@" . $i]);
				$bt_received = $bt_received + $bt_toth_vou_val[4];
				$closing_amt = $closing_amt + $bt_toth_vou_val[4];


				$rows++;
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue("A" . "" . $rows, $c)
					->setCellValue("B" . "" . $rows, date("d.m.Y", strtotime($bt_toth_vou_val[1])))
					->setCellValue("C" . "" . $rows, $bt_toth_vou_val[0])
					->setCellValue("D" . "" . $rows, 'OTH Voucher')
					->setCellValue("E" . "" . $rows, $bt_toth_vou_val[3])
					->setCellValue("F" . "" . $rows, $whname[$bt_toth_vou_val[5]])
					->setCellValue("G" . "" . $rows, $bt_toth_vou_val[6])
					->setCellValue("H" . "" . $rows, '')
					->setCellValue("I" . "" . $rows, $bt_toth_vou_val[4])
					->setCellValue("J" . "" . $rows, $closing_amt);
			} else {
			}
		}
	}



	$rows++;
	$column_colspan = $ch[1] . $rows . ":" . $ch[7] . $rows;
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[1] . $rows, 'Between Days Total');
	$objPHPExcel->getActiveSheet()->getStyle($ch[1] . $rows)->getAlignment()->applyFromArray(
		array(
			"horizontal" => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			"vertical" => \PHPExcel_Style_Alignment::VERTICAL_CENTER
		)
	);

	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($ch[8] . $rows, $bt_paid);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($ch[9] . $rows, $bt_received);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($ch[10] . $rows, '');

	$from = "A" . $rows; // or any value
	$to = "J" . $rows; // or any value
	$objPHPExcel->getActiveSheet()->getStyle("$from:$to")->getFont()->setBold(true);

	$rows++;
	$column_colspan = $ch[1] . $rows . ":" . $ch[7] . $rows;
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[1] . $rows, 'Closing Balance');
	$objPHPExcel->getActiveSheet()->getStyle($ch[1] . $rows)->getAlignment()->applyFromArray(
		array(
			"horizontal" => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			"vertical" => \PHPExcel_Style_Alignment::VERTICAL_CENTER
		)
	);

	$column_colspan = $ch[8] . $rows . ":" . $ch[9] . $rows;
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[8] . $rows, ($pre_received - $pre_paid) + ($bt_received - $bt_paid));
	

	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($ch[10] . $rows, '');

	$from = "A" . $rows; // or any value
	$to = "J" . $rows; // or any value
	$objPHPExcel->getActiveSheet()->getStyle("$from:$to")->getFont()->setBold(true);
}






// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('ExpenseLedgerReport');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex();
// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="ExpenseLedgerReport.xls"');
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
