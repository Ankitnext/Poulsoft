<?php
//ReceiptReport-Excel.php
session_start(); include "../../newConfig.php";
$dbname = $_SESSION['dbase'];

$sql = "SELECT * FROM `main_companyprofile` WHERE `type` = 'Receipt Report' OR `type` = 'All' ORDER BY `id` DESC";
$query = mysqli_query($conn,$sql); $logopath = $cdetails = "";
while($row = mysqli_fetch_assoc($query)){ $logopath = $row['logopath']; $cdetails = $row['cdetails']; }

$sql = "SELECT * FROM `main_contactdetails` WHERE `contacttype` LIKE '%C%' ORDER BY `name` ASC";
$query = mysqli_query($conn,$sql); $cus_code = $cus_name = $cus_group = array();
while($row = mysqli_fetch_assoc($query)){ $cus_code[$row['code']] = $row['code']; $cus_name[$row['code']] = $row['name']; $cus_group[$row['code']] = $row['groupcode']; }

$sql = "SELECT * FROM `main_groups` WHERE `gtype` LIKE '%C%' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $grp_code = $grp_name = array();
while($row = mysqli_fetch_assoc($query)){ $grp_code[$row['code']] = $row['code']; $grp_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `main_reportfields` WHERE `field` = 'Receipt Report' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $dflag = $row['denomination']; } if($dflag == ""){ $dflag = 0; }

$sql = "SELECT * FROM `acc_modes` WHERE `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $mode_code = $mode_name = array();
while($row = mysqli_fetch_assoc($query)){ $mode_code[$row['code']] = $row['code']; $mode_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `acc_coa` WHERE `ctype` IN ('Cash','Bank') AND `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $coa_code = $coa_name = array();
while($row = mysqli_fetch_assoc($query)){ $coa_code[$row['code']] = $row['code']; $coa_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1' ORDER BY `description` ASC";
$query = mysqli_query($conn,$sql); $sector_code = $sector_name = array();
while($row = mysqli_fetch_assoc($query)){ $sector_code[$row['code']] = $row['code']; $sector_name[$row['code']] = $row['description']; }

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
    //$sql = "SELECT * FROM `log_useraccess` WHERE `empcode` = '$users_code' AND `dblist` = '$dbname'"; $query = mysqli_query($conns,$sql);
    //while($row = mysqli_fetch_assoc($query)){ $user_name[$row['empcode']] = $row['username']; $user_code[$row['empcode']] = $row['empcode']; }
    //$addedemp = " AND `addedemp` LIKE '$users_code'";
}

$fdate = date("Y-m-d",strtotime($_GET['fdate']));
$tdate = date("Y-m-d",strtotime($_GET['tdate']));
$customers = $_GET['customers'];
$sectors = $_GET['sectors']; if($sectors == "all"){ $sec_fltr = ""; } else{ $sec_list = implode("','",explode(",",$sectors)); $sec_fltr = " AND `warehouse` IN ('$sec_list')"; }
$modes = $_GET['modes'];
$coas = $_GET['coas'];
$users = $_GET['users'];
$exports = $_GET['exports'];
$group_alist = explode("@",$_GET['groups']);

$groups = array(); $grp_all_flag = 0;
foreach($group_alist as $grps){ $groups[$grps] = $grps; if($grps == "all"){ $grp_all_flag = 1; } }
$grp_list = implode("@",$groups);


require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';
$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
->setLastModifiedBy("Maarten Balliauw")
->setTitle("Office 2007 XLSX Test Document")
->setSubject("Office 2007 XLSX Test Document")
->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
->setKeywords("office 2007 openxml php")
->setCategory("SalesReportExcel");
if($dflag == 0){
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue("A1", 'Date')
		->setCellValue("B1", 'Supplier')
		->setCellValue("C1", 'transaction No.')
		->setCellValue("D1", 'Doc No.')
		->setCellValue("E1", 'Payment Mode')
		->setCellValue("F1", 'Payment Method')
		->setCellValue("G1", 'Amount')
		->setCellValue("H1", 'Remarks')
		->setCellValue("I1", 'Warehouse')
		->setCellValue("J1", 'User');
}
else{
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue("A1", 'Date')
		->setCellValue("B1", 'Supplier')
		->setCellValue("C1", 'transaction No.')
		->setCellValue("D1", 'Doc No.')
		->setCellValue("E1", 'Payment Mode')
		->setCellValue("F1", 'Payment Method')
		->setCellValue("G1", 'Amount')
		->setCellValue("H1", 'Coins')
		->setCellValue("I1", 'C-10')
		->setCellValue("J1", 'C-20')
		->setCellValue("K1", 'C-50')
		->setCellValue("L1", 'C-100')
		->setCellValue("M1", 'C-200')
		->setCellValue("N1", 'C-500')
		->setCellValue("O1", 'C-2000')
		->setCellValue("P1", 'Remarks')
		->setCellValue("Q1", 'Warehouse')
		->setCellValue("R1", 'User');
}

$cus_filter = "";
if($customers != "all"){ $cus_filter = " AND `ccode` IN ('$customers')"; }
else if($grp_all_flag == 0){
    foreach($cus_code as $ccode){
        $gcode = $cus_group[$ccode];
        if(empty($groups[$gcode]) || $groups[$gcode] == ""){ }
        else{ if($cus_list == ""){ $cus_list = $ccode; } else{ $cus_list = $cus_list."','".$ccode; } }
    }
    $cus_filter = " AND `ccode` IN ('$cus_list')";
} else{ }

if($modes == "all"){ $mode_filter = ""; } else{ $mode_filter = " AND `mode` IN ('$modes')"; }
if($coas == "all"){ $coa_filter = ""; } else{ $coa_filter = " AND `method` IN ('$coas')"; }
if($users == "all"){ $user_filter = ""; } else{ $user_filter = " AND `addedemp` IN ('$users')"; }

$html = '';
$html .= '<tbody class="tbody1">';

$sql = "SELECT * FROM `customer_receipts` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$cus_filter."".$sec_fltr."".$mode_filter."".$coa_filter."".$user_filter." AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `date`,`trnum` ASC";
$query = mysqli_query($conn,$sql); $tccoins = $tc10 = $tc20 = $tc50 = $tc100 = $tc200 = $tc500 = $tc2000 = $tot_amount = 0; $i = 1;
while($row = mysqli_fetch_assoc($query)){
	$i++;
	$objPHPExcel->setActiveSheetIndex()->setCellValue("A".$i, date("d-m-Y",strtotime($row["date"])));
	$objPHPExcel->setActiveSheetIndex()->setCellValue("B".$i, $cus_name[$row["ccode"]]);
	$objPHPExcel->setActiveSheetIndex()->setCellValue("C".$i, $row["trnum"]);
	$objPHPExcel->setActiveSheetIndex()->setCellValue("D".$i, $row["docno"]);
	$objPHPExcel->setActiveSheetIndex()->setCellValue("E".$i, $mode_name[$row["mode"]]);
	$objPHPExcel->setActiveSheetIndex()->setCellValue("F".$i, $coa_name[$row["method"]]);
	$objPHPExcel->setActiveSheetIndex()->setCellValue("G".$i, $row["amount"]);
	
	if($dflag == 0){
		$objPHPExcel->setActiveSheetIndex()->setCellValue("H".$i, $row["remarks"]);
		$objPHPExcel->setActiveSheetIndex()->setCellValue("I".$i, $sector_name[$row["warehouse"]]);
		$objPHPExcel->setActiveSheetIndex()->setCellValue("J".$i, $user_name[$row["addedemp"]]);
	}
	else{
		$objPHPExcel->setActiveSheetIndex()->setCellValue("H".$i, $row["ccoins"]);
		$objPHPExcel->setActiveSheetIndex()->setCellValue("I".$i, $row["c10"]);
		$objPHPExcel->setActiveSheetIndex()->setCellValue("J".$i, $row["c20"]);
		$objPHPExcel->setActiveSheetIndex()->setCellValue("K".$i, $row["c50"]);
		$objPHPExcel->setActiveSheetIndex()->setCellValue("L".$i, $row["c100"]);
		$objPHPExcel->setActiveSheetIndex()->setCellValue("M".$i, $row["c200"]);
		$objPHPExcel->setActiveSheetIndex()->setCellValue("N".$i, $row["c500"]);
		$objPHPExcel->setActiveSheetIndex()->setCellValue("O".$i, $row["c2000"]);
		$objPHPExcel->setActiveSheetIndex()->setCellValue("P".$i, $row["remarks"]);
		$objPHPExcel->setActiveSheetIndex()->setCellValue("Q".$i, $sector_name[$row["warehouse"]]);
		$objPHPExcel->setActiveSheetIndex()->setCellValue("R".$i, $user_name[$row["addedemp"]]);
	}
	$tamt = $tamt + $row["amount"];
	$tccoins = $tccoins + $row["ccoins"];
	$tc10 = $tc10 + $row["c10"];
	$tc20 = $tc20 + $row["c20"];
	$tc50 = $tc50 + $row["c50"];
	$tc100 = $tc100 + $row["c100"];
	$tc200 = $tc200 + $row["c200"];
	$tc500 = $tc500 + $row["c500"];
	$tc2000 = $tc2000 + $row["c2000"];
}
$i++;
$objPHPExcel->setActiveSheetIndex()->mergeCells("A".$i.":F".$i);
$objPHPExcel->setActiveSheetIndex()->setCellValue("A".$i, "Grand Total");
$objPHPExcel->setActiveSheetIndex()->setCellValue("G".$i, $tamt);
if($dflag == 1){
$objPHPExcel->setActiveSheetIndex()->setCellValue("H".$i, $tccoins);
$objPHPExcel->setActiveSheetIndex()->setCellValue("I".$i, $tc10);
$objPHPExcel->setActiveSheetIndex()->setCellValue("J".$i, $tc20);
$objPHPExcel->setActiveSheetIndex()->setCellValue("K".$i, $tc50);
$objPHPExcel->setActiveSheetIndex()->setCellValue("L".$i, $tc100);
$objPHPExcel->setActiveSheetIndex()->setCellValue("M".$i, $tc200);
$objPHPExcel->setActiveSheetIndex()->setCellValue("N".$i, $tc500);
$objPHPExcel->setActiveSheetIndex()->setCellValue("O".$i, $tc2000);
}
$objPHPExcel->getActiveSheet()->setTitle('ReceiptReport');
$objPHPExcel->setActiveSheetIndex();
// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="ReceiptReport.xls"');
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