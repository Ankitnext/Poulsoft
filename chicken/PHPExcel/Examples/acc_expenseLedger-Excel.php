<?php
//CustomerLedgerMasterReport-Excel.php
session_start(); include "../../newConfig.php";
$dbname = $_SESSION['dbase'];
include "../../number_format_ind.php";

$sql = "SELECT * FROM `acc_coa` WHERE `active` = '1' AND `type` IN ('COA-0003') ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql); $allCoA = "";
while($row = mysqli_fetch_assoc($query)){ $coaname[$row['code']] = $row['description']; $coacode[$row['code']] = $row['code']; if($allCoA == "") { $allCoA = $row['code']; } else { $allCoA = $allCoA."','".$row['code']; } }
$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $whname[$row['code']] = $row['description']; $whcode[$row['code']] = $row['code']; }
$sql = "SELECT * FROM `main_contactdetails` WHERE `active` = '1' ORDER BY `name` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $cpname[$row['code']] = $row['name']; $cpcode[$row['code']] = $row['code']; }


$fdate = date("Y-m-d",strtotime($_GET['fromdate']));
$tdate =date("Y-m-d",strtotime( $_GET['todate']));
$coa = $_GET['coa'];
$wname = $_GET['whname'];

if($coa != 'all'){
	$coa_metod_cond = " AND `method` = '$coa' ";
	$coa_coa_cond = " AND `coa` = '$coa' ";
	$coa_fcoa_cond = " AND `fcoa` = '$coa' ";
	$coa_tcoa_cond = " AND `tcoa` = '$coa' ";
}else{
	$coa_metod_cond = "";
	$coa_coa_cond = "";
	$coa_fcoa_cond = "";
	$coa_tcoa_cond = "";
}

if($wname != 'all'){
	$coa_wname_cond = " AND `warehouse` = '$wname' ";
}else{
	$coa_wname_cond = "";
}

$sql = "SELECT SUM(amount) as tamt FROM `pur_payments` WHERE `date` < '$fdate' AND `active` = '1' $coa_metod_cond  $coa_wname_cond ORDER BY `ccode` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $pre_pur_pay_amt = $row['tamt']; }
$sql = "SELECT SUM(amount) as tamt FROM `customer_receipts` WHERE `date` < '$fdate' AND `active` = '1' $coa_metod_cond $coa_wname_cond ORDER BY `ccode` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $pre_cus_rct_amt = $row['tamt']; }
$sql = "SELECT SUM(amount) as tamt,mode FROM `main_crdrnote` WHERE `date` < '$fdate' AND `active` = '1' $coa_coa_cond $coa_wname_cond GROUP BY `mode` ORDER BY `ccode` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
	if($row['mode'] == "CCN"){ $pre_ccn_amt = $row['tamt']; }
	else if($row['mode'] == "CDN"){ $pre_cdn_amt = $row['tamt']; }
	else if($row['mode'] == "SCN"){ $pre_scn_amt = $row['tamt']; }
	else if($row['mode'] == "SDN"){ $pre_sdn_amt = $row['tamt']; }
	else { $pre_oth_amt = $row['tamt']; }
}
$sql = "SELECT SUM(amount) as tamt,prefix FROM `acc_vouchers` WHERE `date` < '$fdate' AND `active` = '1' $coa_fcoa_cond $coa_wname_cond GROUP BY `prefix` ORDER BY `fcoa` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
	if($row['prefix'] == "PV"){ $pre_fpv_amt = $row['tamt']; }
	else if($row['prefix'] == "RV"){ $pre_frv_amt = $row['tamt']; }
	else if($row['prefix'] == "JV"){ $pre_fjv_amt = $row['tamt']; }
	else { $pre_foth_vou_amt = $row['tamt']; }
}
$sql = "SELECT SUM(amount) as tamt,prefix FROM `acc_vouchers` WHERE `date` < '$fdate' AND `active` = '1' $coa_tcoa_cond  $coa_wname_cond GROUP BY `prefix` ORDER BY `tcoa` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
	if($row['prefix'] == "PV"){ $pre_tpv_amt = $row['tamt']; }
	else if($row['prefix'] == "RV"){ $pre_trv_amt = $row['tamt']; }
	else if($row['prefix'] == "JV"){ $pre_tjv_amt = $row['tamt']; }
	else { $pre_toth_vou_amt = $row['tamt']; }
}
//echo number_format_ind($pre_pur_pay_amt)."".number_format_ind($pre_cdn_amt)."".number_format_ind($pre_sdn_amt)."".number_format_ind($pre_fpv_amt)."".number_format_ind($pre_frv_amt)."".number_format_ind($pre_fjv_amt)."".number_format_ind($pre_foth_vou_amt);
$pre_paid = $pre_pur_pay_amt + $pre_cdn_amt + $pre_sdn_amt + $pre_fpv_amt + $pre_frv_amt + $pre_fjv_amt + $pre_foth_vou_amt;
$pre_received = $pre_cus_rct_amt + $pre_ccn_amt + $pre_scn_amt + $pre_tpv_amt + $pre_trv_amt + $pre_tjv_amt + $pre_toth_vou_amt;
if($pre_paid > $pre_received){
	$pending_pay = $pre_paid - $pre_received;
	$closing_amt = $pre_received - $pre_paid;
}
else {
	$closing_amt = $pending_rct = $pre_received - $pre_paid;
}
$c = 0;
$sql = "SELECT * FROM `pur_payments` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1' $coa_metod_cond $coa_wname_cond ORDER BY `date` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
	$c = $c + 1;
	$bt_pur_pay_amt[$row['date']."@".$c] = $row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['cdate']."@".$row['cno']."@".$row['amount']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks'];
}
$c = 0;
$sql = "SELECT * FROM `customer_receipts` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1' $coa_metod_cond  $coa_wname_cond ORDER BY `ccode` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
	$c = $c + 1;
	$bt_cus_rct_amt[$row['date']."@".$c] = $row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['cdate']."@".$row['cno']."@".$row['amount']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks'];
}
$c = $d = $e = $f = $g = 0;
$sql = "SELECT * FROM `main_crdrnote` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1' $coa_coa_cond $coa_wname_cond ORDER BY `ccode` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
	if($row['mode'] == "CCN"){
		$c = $c + 1;
		$bt_ccn_amt[$row['date']."@".$c] = $row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['amount']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks'];
	}
	else if($row['mode'] == "CDN"){
		$d = $d + 1;
		$bt_cdn_amt[$row['date']."@".$d] = $row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['amount']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks'];
	}
	else if($row['mode'] == "SCN"){
		$e = $e + 1;
		$bt_scn_amt[$row['date']."@".$e] = $row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['amount']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks'];
	}
	else if($row['mode'] == "SDN"){
		$f = $f + 1;
		$bt_sdn_amt[$row['date']."@".$f] = $row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['amount']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks'];
	}
	else {
		$g = $g + 1;
		$bt_oth_amt[$row['date']."@".$g] = $row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['amount']."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks'];
	}
}
$c = $d = $e = $f = $g = 0;
$sql = "SELECT * FROM `acc_vouchers` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1' $coa_fcoa_cond $coa_wname_cond ORDER BY `fcoa` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
	if($row['prefix'] == "PV"){
		$c = $c + 1;
		$bt_fpv_amt[$row['date']."@".$c] = $row['trnum']."@".$row['date']."@".$row['tcoa']."@".$row['dcno']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks'];
	}
	else if($row['prefix'] == "RV"){
		$d = $d + 1;
		$bt_frv_amt[$row['date']."@".$d] = $row['trnum']."@".$row['date']."@".$row['tcoa']."@".$row['dcno']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks'];
	}
	else if($row['prefix'] == "JV"){
		$e = $e + 1;
		$bt_fjv_amt[$row['date']."@".$e] = $row['trnum']."@".$row['date']."@".$row['tcoa']."@".$row['dcno']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks'];
	}
	else {
		$f = $f + 1;
		$bt_foth_vou_amt[$row['date']."@".$f] = $row['trnum']."@".$row['date']."@".$row['tcoa']."@".$row['dcno']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks'];
	}
}
$c = $d = $e = $f = $g = 0;
$sql = "SELECT * FROM `acc_vouchers` WHERE `date` >= '$fdate' AND `date` <= '$tdate' AND `active` = '1' $coa_tcoa_cond $coa_wname_cond ORDER BY `tcoa` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
	if($row['prefix'] == "PV"){
		$c = $c + 1;
		$bt_tpv_amt[$row['date']."@".$c] = $row['trnum']."@".$row['date']."@".$row['fcoa']."@".$row['dcno']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks'];
	}
	else if($row['prefix'] == "RV"){
		$d = $d + 1;
		$bt_trv_amt[$row['date']."@".$d] = $row['trnum']."@".$row['date']."@".$row['fcoa']."@".$row['dcno']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks'];
	}
	else if($row['prefix'] == "JV"){
		$e = $e + 1;
		$bt_tjv_amt[$row['date']."@".$e] = $row['trnum']."@".$row['date']."@".$row['fcoa']."@".$row['dcno']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks'];
	}
	else {
		$f = $f + 1;
		$bt_toth_vou_amt[$row['date']."@".$f] = $row['trnum']."@".$row['date']."@".$row['fcoa']."@".$row['dcno']."@".$row['amount']."@".$row['warehouse']."@".$row['remarks'];
	}
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



$from = "A1"; // or any value
$to = "M1"; // or any value
$objPHPExcel->getActiveSheet()->getStyle("$from:$to")->getFont()->setBold(true);

for ($col = 'A'; $col !== 'CZ'; $col++) {
	$objPHPExcel->getActiveSheet()
		->getColumnDimension($col)
		->setAutoSize(true);
}

$rows++;
$rows++;

$diaply1 = "Expense Ledger";
$column_colspan = $ch[7] . $rows . ":" . $ch[14] . $rows;
$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[7] . $rows, $diaply1);
$objPHPExcel->getActiveSheet()->getStyle($ch[7] . $rows)->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getStyle($ch[7] . $rows)->getAlignment()->applyFromArray(
	array(
		"horizontal" => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		"vertical" => \PHPExcel_Style_Alignment::VERTICAL_CENTER
	)
);

$rows++;

$diaply2 = "From Date: " . $_GET['fromdate'] . "         To Date: " . $_GET['todate'];
$column_colspan = $ch[7] . $rows . ":" . $ch[14] . $rows;
$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[7] . $rows, $diaply2);
$objPHPExcel->getActiveSheet()->getStyle($ch[7] . $rows)->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getStyle($ch[7] . $rows)->getAlignment()->applyFromArray(
	array(
		"horizontal" => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		"vertical" => \PHPExcel_Style_Alignment::VERTICAL_CENTER
	)
);


if($coa != 'all'){
	$display_coa = "CoA: ".$coaname[$coa];
}else{
	$display_coa = "CoA: ".'All';
}

if($wname != 'all'){
	$display_wname = "Warehouse: ".$whname[$wname];
}else{
	$display_wname = "Warehouse: ".'All';
}


$rows++;

$column_colspan = $ch[7] . $rows . ":" . $ch[14] . $rows;
$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[7] . $rows, $display_coa);
$objPHPExcel->getActiveSheet()->getStyle($ch[7] . $rows)->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getStyle($ch[7] . $rows)->getAlignment()->applyFromArray(
	array(
		"horizontal" => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		"vertical" => \PHPExcel_Style_Alignment::VERTICAL_CENTER
	)
);

$rows++;

$column_colspan1 = "A1" . ":" . $ch[6] . $rows;
$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan1)->setCellValue("A1" . "", strip_tags($cdetails));
$objPHPExcel->getActiveSheet()->getStyle("A1")->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getStyle("A1")->getAlignment()->applyFromArray(
	array(
		"horizontal" => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		"vertical" => \PHPExcel_Style_Alignment::VERTICAL_CENTER
	)
);


$column_colspan = $ch[7] . $rows . ":" . $ch[14] . $rows;
$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue($ch[7] . $rows, $display_wname);
$objPHPExcel->getActiveSheet()->getStyle($ch[7] . $rows)->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getStyle($ch[7] . $rows)->getAlignment()->applyFromArray(
	array(
		"horizontal" => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		"vertical" => \PHPExcel_Style_Alignment::VERTICAL_CENTER
	)
);



$rows++;$rows++;
$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue("A"."".$rows, 'Sl.No.')
	->setCellValue("B"."".$rows, 'Date')
	->setCellValue("C"."".$rows, 'Transaction No')
	->setCellValue("D"."".$rows, 'Transaction Type')
	->setCellValue("E"."".$rows, 'Doc. No.')
	->setCellValue("F"."".$rows, 'From Warehouse')
	->setCellValue("G"."".$rows, 'Paid/Received')
	->setCellValue("H"."".$rows, 'Cheque No')
	->setCellValue("I"."".$rows, 'Cheque Date')
	->setCellValue("J"."".$rows, 'Remarks')
	->setCellValue("K"."".$rows, 'Paid')
	->setCellValue("L"."".$rows, 'Received')
	->setCellValue("M"."".$rows, 'Running Balance');

	$from = "A1"; // or any value
$to = "M"."".$rows; // or any value
$objPHPExcel->getActiveSheet()->getStyle("$from:$to")->getFont()->setBold( true );

$rows++;
$column_colspan = "A".$rows.":"."J".$rows;
$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue("A"."".$rows, 'Previous Balance');
$objPHPExcel->getActiveSheet()->getStyle("A".$rows)->getAlignment()->applyFromArray(
	array(
		"horizontal" => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 
		"vertical" => \PHPExcel_Style_Alignment::VERTICAL_CENTER
	)
);

$objPHPExcel->setActiveSheetIndex(0)->setCellValue("K"."".$rows, $pre_paid);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue("L"."".$rows, $pre_received);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue("M"."".$rows, $closing_amt);


$fdate = strtotime($fdate);
$tdate = strtotime($tdate);
$bt_paid = $bt_received = $c = 0;
for ($currentDate = $fdate; $currentDate <= $tdate; $currentDate += (86400)) {
	$row['trnum']."@".$row['date']."@".$row['ccode']."@".$row['docno']."@".$row['cdate']."@".$row['cno']."@".$row['amount']
	."@".$row['vtype']."@".$row['warehouse']."@".$row['remarks'];
	$date_asc = date('Y-m-d', $currentDate); 
	if($bt_pur_pay_amt != null){
		$ccount = sizeof($bt_pur_pay_amt); 
	}else{
		$ccount = 0; 
	}
	
	for($i = 1;$i <=$ccount;$i++){
		if($bt_pur_pay_amt[$date_asc."@".$i] != ""){
			$c = $c + 1;
			$bt_pur_pay_val = explode("@",$bt_pur_pay_amt[$date_asc."@".$i]);
			$rows++;

			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A"."".$rows, $c);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B"."".$rows, date("d.m.Y",strtotime($bt_pur_pay_val[1])));
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("C"."".$rows, $bt_pur_pay_val[0]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("D"."".$rows, "PMT");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("E"."".$rows, $bt_pur_pay_val[3]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("F"."".$rows, $whname[$bt_pur_pay_val[8]]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("G"."".$rows, $cpname[$bt_pur_pay_val[2]]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("H"."".$rows, $bt_pur_pay_val[5]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("I"."".$rows, date("d.m.Y",strtotime($bt_pur_pay_val[4])));
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("J"."".$rows, $bt_pur_pay_val[9]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("K"."".$rows, $bt_pur_pay_val[6]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("L"."".$rows, "");
			$bt_paid = $bt_paid + $bt_pur_pay_val[6];
			$closing_amt = $closing_amt - $bt_pur_pay_val[6];
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("M"."".$rows, $closing_amt);

		}
		else{
			
		}
	}
	if($bt_cdn_amt != null){
		$ccount = sizeof($bt_cdn_amt); 
	}else{
		$ccount = 0; 
	}
	for($i = 1;$i <=$ccount;$i++){
		if($bt_cdn_amt[$date_asc."@".$i] != ""){
			$c = $c + 1;
			$bt_cdn_val = explode("@",$bt_cdn_amt[$date_asc."@".$i]);
			$rows++;

			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A"."".$rows, $c);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B"."".$rows, date("d.m.Y",strtotime($bt_cdn_val[1])));
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("C"."".$rows, $bt_cdn_val[0]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("D"."".$rows, "CDN");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("E"."".$rows, $bt_cdn_val[3]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("F"."".$rows, $whname[$bt_cdn_val[6]]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("G"."".$rows, $cpname[$bt_cdn_val[2]]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("H"."".$rows, "");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("I"."".$rows, "");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("J"."".$rows, $bt_cdn_val[7]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("K"."".$rows, $bt_cdn_val[4]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("L"."".$rows, "");
			$bt_paid = $bt_paid + $bt_cdn_val[4];
			$closing_amt = $closing_amt - $bt_cdn_val[4];
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("M"."".$rows, $closing_amt);
			
		}
		else{
			
		}
	}
	if($bt_sdn_amt != null){
		$ccount = sizeof($bt_sdn_amt); 
	}else{
		$ccount = 0; 
	}
	for($i = 1;$i <=$ccount;$i++){
		if($bt_sdn_amt[$date_asc."@".$i] != ""){
			$c = $c + 1;
			$bt_sdn_val = explode("@",$bt_sdn_amt[$date_asc."@".$i]);


			$rows++;

			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A"."".$rows, $c);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B"."".$rows, date("d.m.Y",strtotime($bt_sdn_val[1])));
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("C"."".$rows, $bt_sdn_val[0]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("D"."".$rows, "SDN");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("E"."".$rows, $bt_sdn_val[3]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("F"."".$rows, $whname[$bt_sdn_val[6]]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("G"."".$rows, $cpname[$bt_sdn_val[2]]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("H"."".$rows, "");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("I"."".$rows, "");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("J"."".$rows, $bt_sdn_val[7]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("K"."".$rows, $bt_sdn_val[4]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("L"."".$rows, "");
			$bt_paid = $bt_paid + $bt_sdn_val[4];
			$closing_amt = $closing_amt - $bt_sdn_val[4];
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("M"."".$rows, $closing_amt);
		}
		else{
			
		}
	}
	if($bt_fpv_amt != null){
		$ccount = sizeof($bt_fpv_amt); 
	}else{
		$ccount = 0; 
	}
	for($i = 1;$i <=$ccount;$i++){
		if($bt_fpv_amt[$date_asc."@".$i] != ""){
			$c = $c + 1;
			$bt_fpv_val = explode("@",$bt_fpv_amt[$date_asc."@".$i]);


			$rows++;

			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A"."".$rows, $c);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B"."".$rows, date("d.m.Y",strtotime($bt_fpv_val[1])));
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("C"."".$rows, $bt_fpv_val[0]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("D"."".$rows, "PV");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("E"."".$rows, $bt_fpv_val[3]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("F"."".$rows, $whname[$bt_fpv_val[5]]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("G"."".$rows, $coaname[$bt_fpv_val[2]]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("H"."".$rows, "");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("I"."".$rows, "");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("J"."".$rows, $bt_fpv_val[6]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("K"."".$rows, $bt_fpv_val[4]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("L"."".$rows, "");
			$bt_paid = $bt_paid + $bt_fpv_val[4];
			$closing_amt = $closing_amt - $bt_fpv_val[4];
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("M"."".$rows, $closing_amt);

		}
		else{
			
		}
	}
	if($bt_frv_amt != null){
		$ccount = sizeof($bt_frv_amt); 
	}else{
		$ccount = 0; 
	}
	for($i = 1;$i <=$ccount;$i++){
		if($bt_frv_amt[$date_asc."@".$i] != ""){
			$c = $c + 1;
			$bt_frv_val = explode("@",$bt_frv_amt[$date_asc."@".$i]);

			$rows++;

			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A"."".$rows, $c);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B"."".$rows, date("d.m.Y",strtotime($bt_frv_val[1])));
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("C"."".$rows, $bt_frv_val[0]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("D"."".$rows, "RV");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("E"."".$rows, $bt_frv_val[3]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("F"."".$rows, $whname[$bt_frv_val[5]]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("G"."".$rows, $coaname[$bt_frv_val[2]]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("H"."".$rows, "");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("I"."".$rows, "");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("J"."".$rows, $bt_frv_val[6]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("K"."".$rows, $bt_frv_val[4]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("L"."".$rows, "");
			$bt_paid = $bt_paid + $bt_frv_val[4];
			$closing_amt = $closing_amt - $bt_frv_val[4];
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("M"."".$rows, $closing_amt);

		}
		else{
			
		}
	}
	if($bt_fjv_amt != null){
		$ccount = sizeof($bt_fjv_amt); 
	}else{
		$ccount = 0; 
	}
	for($i = 1;$i <=$ccount;$i++){
		if($bt_fjv_amt[$date_asc."@".$i] != ""){
			$c = $c + 1;
			$bt_fjv_val = explode("@",$bt_fjv_amt[$date_asc."@".$i]);

			$rows++;

			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A"."".$rows, $c);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B"."".$rows, date("d.m.Y",strtotime($bt_fjv_val[1])));
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("C"."".$rows, $bt_fjv_val[0]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("D"."".$rows, "JV");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("E"."".$rows, $bt_fjv_val[3]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("F"."".$rows, $whname[$bt_fjv_val[5]]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("G"."".$rows, $coaname[$bt_fjv_val[2]]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("H"."".$rows, "");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("I"."".$rows, "");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("J"."".$rows, $bt_fjv_val[6]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("K"."".$rows, $bt_fjv_val[4]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("L"."".$rows, "");
			$bt_paid = $bt_paid + $bt_fjv_val[4];
			$closing_amt = $closing_amt - $bt_fjv_val[4];
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("M"."".$rows, $closing_amt);

		}
		else{
			
		}
	}
	if($bt_foth_vou_amt != null){
		$ccount = sizeof($bt_foth_vou_amt); 
	}else{
		$ccount = 0; 
	}
	for($i = 1;$i <=$ccount;$i++){
		if($bt_foth_vou_amt[$date_asc."@".$i] != ""){
			$c = $c + 1;
			$bt_foth_vou_val = explode("@",$bt_foth_vou_amt[$date_asc."@".$i]);

			$rows++;

			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A"."".$rows, $c);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B"."".$rows, date("d.m.Y",strtotime($bt_foth_vou_val[1])));
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("C"."".$rows, $bt_foth_vou_val[0]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("D"."".$rows, "OTH VOC");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("E"."".$rows, $bt_foth_vou_val[3]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("F"."".$rows, $whname[$bt_foth_vou_val[5]]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("G"."".$rows, $coaname[$bt_foth_vou_val[2]]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("H"."".$rows, "");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("I"."".$rows, "");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("J"."".$rows, $bt_foth_vou_val[6]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("K"."".$rows, $bt_foth_vou_val[4]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("L"."".$rows, "");
			$bt_paid = $bt_paid + $bt_foth_vou_val[4];
			$closing_amt = $closing_amt - $bt_foth_vou_val[4];
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("M"."".$rows, $closing_amt);

		}
		else{
			
		}
	}
	if($bt_cus_rct_amt != null){
		$ccount = sizeof($bt_cus_rct_amt); 
	}else{
		$ccount = 0; 
	}
	for($i = 1;$i <=$ccount;$i++){
		if($bt_cus_rct_amt[$date_asc."@".$i] != ""){
			$c = $c + 1;
			$bt_cus_rct_val = explode("@",$bt_cus_rct_amt[$date_asc."@".$i]);


			$rows++;

			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A"."".$rows, $c);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B"."".$rows, date("d.m.Y",strtotime($bt_cus_rct_val[1])));
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("C"."".$rows, $bt_cus_rct_val[0]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("D"."".$rows, "RCT");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("E"."".$rows, $bt_cus_rct_val[3]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("F"."".$rows, $whname[$bt_cus_rct_val[8]]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("G"."".$rows, $cpname[$bt_cus_rct_val[2]]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("H"."".$rows, $bt_cus_rct_val[5]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("I"."".$rows, date("d.m.Y",strtotime($bt_cus_rct_val[4])));
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("J"."".$rows, $bt_cus_rct_val[9]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("K"."".$rows, "");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("L"."".$rows, $bt_cus_rct_val[6]);
			$bt_received = $bt_received + $bt_cus_rct_val[6];
			$closing_amt = $closing_amt + $bt_cus_rct_val[6];
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("M"."".$rows, $closing_amt);

		}
		else{
			
		}
	}
	if($bt_pur_pay_bt_ccn_amtamt != null){
		$ccount = sizeof($bt_ccn_amt); 
	}else{
		$ccount = 0; 
	}
	for($i = 1;$i <=$ccount;$i++){
		if($bt_ccn_amt[$date_asc."@".$i] != ""){
			$c = $c + 1;
			$bt_ccn_val = explode("@",$bt_ccn_amt[$date_asc."@".$i]);

			$rows++;

			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A"."".$rows, $c);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B"."".$rows, date("d.m.Y",strtotime($bt_ccn_val[1])));
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("C"."".$rows, $bt_ccn_val[0]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("D"."".$rows, "CCN");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("E"."".$rows, $bt_ccn_val[3]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("F"."".$rows, $whname[$bt_ccn_val[6]]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("G"."".$rows, $cpname[$bt_ccn_val[2]]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("H"."".$rows, "");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("I"."".$rows, "");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("J"."".$rows, $bt_ccn_val[7]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("K"."".$rows, "");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("L"."".$rows, $bt_ccn_val[4]);
			$bt_received = $bt_received + $bt_ccn_val[4];
			$closing_amt = $closing_amt + $bt_ccn_val[4];
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("M"."".$rows, $closing_amt);

			
		}
		else{
			
		}
	}
	if($bt_scn_amt != null){
		$ccount = sizeof($bt_scn_amt); 
	}else{
		$ccount = 0; 
	}
	for($i = 1;$i <=$ccount;$i++){
		if($bt_scn_amt[$date_asc."@".$i] != ""){
			$c = $c + 1;
			$bt_scn_val = explode("@",$bt_scn_amt[$date_asc."@".$i]);

			
			$rows++;

			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A"."".$rows, $c);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B"."".$rows, date("d.m.Y",strtotime($bt_scn_val[1])));
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("C"."".$rows, $bt_scn_val[0]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("D"."".$rows, "SCN");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("E"."".$rows, $bt_scn_val[3]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("F"."".$rows, $whname[$bt_scn_val[6]]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("G"."".$rows, $cpname[$bt_scn_val[2]]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("H"."".$rows, "");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("I"."".$rows, "");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("J"."".$rows, $bt_scn_val[7]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("K"."".$rows, "");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("L"."".$rows, $bt_scn_val[4]);
			$bt_received = $bt_received + $bt_scn_val[4];
			$closing_amt = $closing_amt + $bt_scn_val[4];
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("M"."".$rows, $closing_amt);

		}
		else{
			
		}
	}
	if($bt_tpv_amt != null){
		$ccount = sizeof($bt_tpv_amt); 
	}else{
		$ccount = 0; 
	}
	for($i = 1;$i <=$ccount;$i++){
		if($bt_tpv_amt[$date_asc."@".$i] != ""){
			$c = $c + 1;
			$bt_tpv_val = explode("@",$bt_tpv_amt[$date_asc."@".$i]);


			$rows++;

			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A"."".$rows, $c);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B"."".$rows, date("d.m.Y",strtotime($bt_tpv_val[1])));
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("C"."".$rows, $bt_tpv_val[0]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("D"."".$rows, "PV");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("E"."".$rows, $bt_tpv_val[3]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("F"."".$rows, $whname[$bt_tpv_val[5]]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("G"."".$rows, $coaname[$bt_tpv_val[2]]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("H"."".$rows, "");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("I"."".$rows, "");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("J"."".$rows, $bt_tpv_val[6]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("K"."".$rows, "");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("L"."".$rows, $bt_tpv_val[4]);
			$bt_received = $bt_received + $bt_tpv_val[4];
			$closing_amt = $closing_amt + $bt_tpv_val[4];
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("M"."".$rows, $closing_amt);

			
		}
		else{
			
		}
	}
	if($bt_trv_amt != null){
		$ccount = sizeof($bt_trv_amt); 
	}else{
		$ccount = 0; 
	}
	for($i = 1;$i <=$ccount;$i++){
		if($bt_trv_amt[$date_asc."@".$i] != ""){
			$c = $c + 1;
			$bt_trv_val = explode("@",$bt_trv_amt[$date_asc."@".$i]);



			$rows++;

			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A"."".$rows, $c);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B"."".$rows, date("d.m.Y",strtotime($bt_trv_val[1])));
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("C"."".$rows, $bt_trv_val[0]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("D"."".$rows, "RV");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("E"."".$rows, $bt_trv_val[3]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("F"."".$rows, $whname[$bt_trv_val[5]]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("G"."".$rows, $coaname[$bt_trv_val[2]]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("H"."".$rows, "");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("I"."".$rows, "");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("J"."".$rows, $bt_trv_val[6]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("K"."".$rows, "");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("L"."".$rows, $bt_trv_val[4]);
			$bt_received = $bt_received + $bt_trv_val[4];
			$closing_amt = $closing_amt + $bt_trv_val[4];
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("M"."".$rows, $closing_amt);

			
		}
		else{
			
		}
	}
	if($bt_tjv_amt != null){
		$ccount = sizeof($bt_tjv_amt); 
	}else{
		$ccount = 0; 
	}
	for($i = 1;$i <=$ccount;$i++){
		if($bt_tjv_amt[$date_asc."@".$i] != ""){
			$c = $c + 1;
			$bt_tjv_val = explode("@",$bt_tjv_amt[$date_asc."@".$i]);


			$rows++;

			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A"."".$rows, $c);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B"."".$rows, date("d.m.Y",strtotime($bt_tjv_val[1])));
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("C"."".$rows, $bt_tjv_val[0]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("D"."".$rows, "JV");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("E"."".$rows, $bt_tjv_val[3]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("F"."".$rows, $whname[$bt_tjv_val[5]]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("G"."".$rows, $coaname[$bt_tjv_val[2]]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("H"."".$rows, "");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("I"."".$rows, "");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("J"."".$rows, $bt_tjv_val[6]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("K"."".$rows, "");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("L"."".$rows, $bt_tjv_val[4]);
			$bt_received = $bt_received + $bt_tjv_val[4];
			$closing_amt = $closing_amt + $bt_tjv_val[4];
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("M"."".$rows, $closing_amt);

		}
		else{
			
		}
	}
	if($bt_toth_vou_amt != null){
		$ccount = sizeof($bt_toth_vou_amt); 
	}else{
		$ccount = 0; 
	}
	for($i = 1;$i <=$ccount;$i++){
		if($bt_toth_vou_amt[$date_asc."@".$i] != ""){
			$c = $c + 1;
			$bt_toth_vou_val = explode("@",$bt_toth_vou_amt[$date_asc."@".$i]);


			$rows++;

			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("A"."".$rows, $c);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B"."".$rows, date("d.m.Y",strtotime($bt_toth_vou_val[1])));
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("C"."".$rows, $bt_toth_vou_val[0]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("D"."".$rows, "OTH VOC");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("E"."".$rows, $bt_toth_vou_val[3]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("F"."".$rows, $whname[$bt_toth_vou_val[5]]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("G"."".$rows, $coaname[$bt_toth_vou_val[2]]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("H"."".$rows, "");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("I"."".$rows, "");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("J"."".$rows, $bt_toth_vou_val[6]);
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("K"."".$rows, "");
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("L"."".$rows, $bt_toth_vou_val[4]);
			$bt_received = $bt_received + $bt_toth_vou_val[4];
			$closing_amt = $closing_amt + $bt_toth_vou_val[4];
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue("M"."".$rows, $closing_amt);

		}
		else{
			
		}
	}
}

$rows++;
$column_colspan = "A".$rows.":"."J".$rows;
$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue("A"."".$rows, 'Between Days Total');
$objPHPExcel->getActiveSheet()->getStyle("A".$rows)->getAlignment()->applyFromArray(
	array(
		"horizontal" => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 
		"vertical" => \PHPExcel_Style_Alignment::VERTICAL_CENTER
	)
);

$objPHPExcel->setActiveSheetIndex(0)->setCellValue("K"."".$rows, $bt_paid);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue("L"."".$rows, $bt_received);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue("M"."".$rows, "");

$from = "A".$rows; // or any value
$to = "M".$rows; // or any value
$objPHPExcel->getActiveSheet()->getStyle("$from:$to")->getFont()->setBold(true);

$rows++;
$column_colspan = "A".$rows.":"."J".$rows;
$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue("A"."".$rows, 'Closing Balance');
$objPHPExcel->getActiveSheet()->getStyle("A".$rows)->getAlignment()->applyFromArray(
	array(
		"horizontal" => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 
		"vertical" => \PHPExcel_Style_Alignment::VERTICAL_CENTER
	)
);

$column_colspan = "K".$rows.":"."L".$rows;
$objPHPExcel->setActiveSheetIndex(0)->mergeCells($column_colspan)->setCellValue("K"."".$rows, ($pre_received - $pre_paid)+($bt_received - $bt_paid));
$objPHPExcel->getActiveSheet()->getStyle("K".$rows)->getAlignment()->applyFromArray(
	array(
		"horizontal" => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 
		"vertical" => \PHPExcel_Style_Alignment::VERTICAL_CENTER
	)
);

$objPHPExcel->setActiveSheetIndex(0)->setCellValue("M"."".$rows, "");
$from = "A".$rows; // or any value
$to = "M".$rows; // or any value
$objPHPExcel->getActiveSheet()->getStyle("$from:$to")->getFont()->setBold(true);

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
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
echo "<script> window.close(); </script>";
exit;
?>