<?php
//PaymentReport-Excel.php
session_start(); include "../../newConfig.php";
$fdate = date("Y-m-d",strtotime($_GET['fromdate']));
$tdate = date("Y-m-d",strtotime($_GET['todate']));
$cname = $_GET['ccode']; $pmode = $_GET['pmode']; $pcoa = $_GET['pcoa']; $user = "all"; // $wname = $_GET['sector']; //$user = $_GET['user'];
if($cname == "all") { $cnames = ""; } else { $cnames = " AND `ccode` = '$cname'"; }
if($pmode == "all") { $pmodes = ""; } else { $pmodes = " AND `mode` = '$pmode'"; }
if($pcoa == "all") { $pcoas = ""; } else { $pcoas = " AND `method` = '$pcoa'"; }
// if($wname == "all") { $wnames = ""; } else { $wnames = " AND `warehouse` = '$wname'"; }
$sectors = $_GET['sectors']; if($sectors == "all"){ $sec_fltr = ""; } else{ $sec_list = implode("','",explode(",",$sectors)); $sec_fltr = " AND `warehouse` IN ('$sec_list')"; }

$sql = "SELECT * FROM `main_contactdetails`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $cus_name[$row['code']] = $row['name']; }
$sql = "SELECT * FROM `acc_modes` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $pmode_name[$row['code']] = $row['description']; }
$sql = "SELECT * FROM `acc_coa` WHERE `ctype` IN ('Cash','Bank') AND `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $pcoa_name[$row['code']] = $row['description']; }
$sql = "SELECT * FROM `inv_sectors` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $sector_name[$row['code']] = $row['description']; }

$sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'Supplier Payment' AND `field_function` = 'Display TCDS Calculations' AND `user_access` = 'all' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $dtcds_flag = mysqli_num_rows($query);

require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';
$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
->setLastModifiedBy("Maarten Balliauw")
->setTitle("Office 2007 XLSX Test Document")
->setSubject("Office 2007 XLSX Test Document")
->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
->setKeywords("office 2007 openxml php")
->setCategory("SalesReportExcel");
if((int)$dtcds_flag == 1){
	$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue("A1", 'Date')
	->setCellValue("B1", 'Supplier')
	->setCellValue("C1", 'transaction No.')
	->setCellValue("D1", 'Doc No.')
	->setCellValue("E1", 'Payment Mode')
	->setCellValue("F1", 'Payment Method')
	->setCellValue("G1", 'Base Amount')
	->setCellValue("H1", 'TDS %')
	->setCellValue("I1", 'TDS Amt')
	->setCellValue("J1", 'Amount')
	->setCellValue("K1", 'Remarks')
	->setCellValue("L1", 'Warehouse');
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
	->setCellValue("H1", 'Remarks')
	->setCellValue("I1", 'Warehouse');
}
		
$payment_sql = "SELECT * FROM `pur_payments` WHERE `date` >= '$fdate' AND `date` <= '$tdate'".$cnames."".$pmodes."".$pcoas."".$sec_fltr." AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0'";
$payment_query = mysqli_query($conn,$payment_sql); $i = 1; $scount = mysqli_num_rows($payment_query); $tamt =  $tamt1 = $ttcds_amt = 0;
while($row = mysqli_fetch_assoc($payment_query)){
	$i++;
	$objPHPExcel->setActiveSheetIndex()->setCellValue("A".$i, $row["date"]);
	$objPHPExcel->setActiveSheetIndex()->setCellValue("B".$i, $cus_name[$row["ccode"]]);
	$objPHPExcel->setActiveSheetIndex()->setCellValue("C".$i, $row["trnum"]);
	$objPHPExcel->setActiveSheetIndex()->setCellValue("D".$i, $row["docno"]);
	$objPHPExcel->setActiveSheetIndex()->setCellValue("E".$i, $pmode_name[$row["mode"]]);
	$objPHPExcel->setActiveSheetIndex()->setCellValue("F".$i, $pcoa_name[$row["method"]]);
	if((int)$dtcds_flag == 1){
		$objPHPExcel->setActiveSheetIndex()->setCellValue("G".$i, $row["amount1"]);
		$objPHPExcel->setActiveSheetIndex()->setCellValue("H".$i, $row["tcds_per"]);
		$objPHPExcel->setActiveSheetIndex()->setCellValue("I".$i, $row["tcds_amt"]);
		$objPHPExcel->setActiveSheetIndex()->setCellValue("J".$i, $row["amount"]);
		$objPHPExcel->setActiveSheetIndex()->setCellValue("K".$i, $row["remarks"]);
		$objPHPExcel->setActiveSheetIndex()->setCellValue("L".$i, $sector_name[$row["warehouse"]]);
		$tamt1 += (float)$row['amount1'];
		$ttcds_amt += (float)$row['tcds_amt'];
	}
	else{
		$objPHPExcel->setActiveSheetIndex()->setCellValue("G".$i, $row["amount"]);
		$objPHPExcel->setActiveSheetIndex()->setCellValue("H".$i, $row["remarks"]);
		$objPHPExcel->setActiveSheetIndex()->setCellValue("I".$i, $sector_name[$row["warehouse"]]);
	}
	
	$tamt = $tamt + $row["amount"];
}
$i++;
$objPHPExcel->setActiveSheetIndex()->mergeCells("A".$i.":F".$i);
$objPHPExcel->setActiveSheetIndex()->setCellValue("A".$i, "Grand Total");
if((int)$dtcds_flag == 1){
	$objPHPExcel->setActiveSheetIndex()->setCellValue("G".$i, $tamt1);
	$objPHPExcel->setActiveSheetIndex()->setCellValue("H".$i, "");
	$objPHPExcel->setActiveSheetIndex()->setCellValue("I".$i, $ttcds_amt);
	$objPHPExcel->setActiveSheetIndex()->setCellValue("J".$i, $tamt);
}
else{
	$objPHPExcel->setActiveSheetIndex()->setCellValue("G".$i, $tamt);
}

$objPHPExcel->getActiveSheet()->setTitle('PaymentReport');
$objPHPExcel->setActiveSheetIndex();
// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="PaymentReport.xls"');
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