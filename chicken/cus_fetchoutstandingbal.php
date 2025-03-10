<?php
//cus_fetchoutstandingbal.php
session_start(); include "newConfig.php";
$cuscode = $_GET['cuscode'];
$row_count = $_GET['row_count'];

$today = date("Y-m-d"); 
$cname = $cnameno = $invno = $finval = "";
$sales = $sale_total = $cdn_amt = $obdramt = $receipts = $rct_amt = $ccn_amt = $obcramt = $balance = 0;

$sql1 = "SELECT * FROM `main_contactdetails` WHERE `code` LIKE '$cuscode'"; $query = mysqli_query($conn,$sql1);
while($row = mysqli_fetch_assoc($query)){ $cname = $row['name']; $cnameno = $row['mobileno']; $ctype = $row['contacttype']; if($row['obtype'] == "Cr"){ $obcramt = $row['obamt']; $obdramt = "0.00"; } else if($row['obtype'] == "Dr"){ $obdramt = $row['obamt']; $obcramt = "0.00"; } else{ $obdramt = $obcramt = "0.00"; } }
	
$sql1 = "SELECT * FROM `master_itemfields` WHERE `active` = '1'"; $query = mysqli_query($conn,$sql1);
while($row = mysqli_fetch_assoc($query)){ $sac_print_flag = $row['sac_print_flag']; }
if($sac_print_flag == "" || $sac_print_flag == NULL){ $sac_print_flag = 0; }
	
if($ctype == "S&C" && $sac_print_flag == 1 || $ctype == "S&C" && $sac_print_flag == "1"){
	//Purchases Outstanding Balance
	$old_inv = ""; $pinv = $ppay = $pcdn = $pccn = $preturns = 0;
	$sql = "SELECT invoice,finaltotal FROM `pur_purchase` WHERE `date` <= '$today' AND `vendorcode` LIKE '$cuscode' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `invoice` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ if($old_inv != $row['invoice']){ $pinv += (float)$row['finaltotal']; $old_inv = $row['invoice']; } }
	$sql = "SELECT SUM(amount) as tamt FROM `pur_payments` WHERE  `date` <= '$today' AND `ccode` LIKE '$cuscode' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $ppay += (float)$row['tamt']; }
	$sql = "SELECT SUM(amount) as tamt,mode FROM `main_crdrnote` WHERE  `date` <= '$today' AND `ccode` LIKE '$cuscode' AND `mode` IN ('SCN','SDN') AND `active` = '1' GROUP BY `mode` ORDER BY `mode` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ if($row['mode'] == "SDN") { $pcdn += (float)$row['tamt']; } else { $pccn += (float)$row['tamt']; } }
	$obsql = "SELECT * FROM `main_itemreturns` WHERE `date` <= '$today' AND `vcode` = '$cuscode' AND `mode` = 'supplier' AND `active` = '1' AND `dflag` = '0'";
	$obquery = mysqli_query($conn,$obsql); while($obrow = mysqli_fetch_assoc($obquery)){ $preturns += (float)$obrow['amount']; }
		
	//Sales Outstanding Balance
	$old_inv = ""; $oinv = $orct = $ocdn = $occn = $omortality = $oreturns = 0;
	$sql = "SELECT invoice,finaltotal FROM `customer_sales` WHERE `date` <= '$today' AND `customercode` LIKE '$cuscode' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `invoice` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ if($old_inv != $row['invoice']){ $oinv += (float)$row['finaltotal']; $old_inv = $row['invoice']; } }
	$sql = "SELECT SUM(amount) as tamt FROM `customer_receipts` WHERE  `date` <= '$today' AND `ccode` LIKE '$cuscode' AND `active` = '1'"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ $orct += (float)$row['tamt']; }
	$sql = "SELECT SUM(amount) as tamt,mode FROM `main_crdrnote` WHERE  `date` <= '$today' AND `ccode` LIKE '$cuscode' AND `mode` IN ('CCN','CDN') AND `active` = '1' GROUP BY `mode` ORDER BY `mode` ASC"; $query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){ if($row['mode'] == "CDN") { $ocdn += (float)$row['tamt']; } else { $occn += (float)$row['tamt']; } }
	$obsql = "SELECT * FROM `main_mortality` WHERE `date` <= '$today' AND `ccode` = '$cuscode' AND `mtype` = 'customer' AND `active` = '1' AND `dflag` = '0'";
	$obquery = mysqli_query($conn,$obsql); while($obrow = mysqli_fetch_assoc($obquery)){ $omortality += (float)$obrow['amount']; }
	$obsql = "SELECT * FROM `main_itemreturns` WHERE `date` <= '$today' AND `vcode` = '$cuscode' AND `mode` = 'customer' AND `active` = '1' AND `dflag` = '0'";
	$obquery = mysqli_query($conn,$obsql); while($obrow = mysqli_fetch_assoc($obquery)){ $oreturns += (float)$obrow['amount']; }
		
	$ob_rcv = $oinv + $ocdn + $pccn + $ppay + $obdramt + $preturns;
	$ob_paid = $pinv + $pcdn + $occn + $orct + $obcramt + $oreturns + $omortality;
	
	$balance = $ob_rcv - $ob_paid;
	$finval = $balance."@".$cname."@".$cnameno."@".$row_count;
}
else{
	//Sales Outstanding Balance
	$old_inv = ""; $oinv = $orct = $ocdn = $occn = $omortality = $oreturns = 0;
	$sql = "SELECT invoice,finaltotal FROM `customer_sales` WHERE `date` <= '$today' AND `customercode` LIKE '$cuscode' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `invoice` ASC"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
	if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ if($old_inv != $row['invoice']){ $oinv += (float)$row['finaltotal']; $old_inv = $row['invoice']; } } }
	$sql = "SELECT SUM(amount) as tamt FROM `customer_receipts` WHERE  `date` <= '$today' AND `ccode` LIKE '$cuscode' AND `active` = '1'"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
	if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $orct += (float)$row['tamt']; } }
	$sql = "SELECT SUM(amount) as tamt,mode FROM `main_crdrnote` WHERE  `date` <= '$today' AND `ccode` LIKE '$cuscode' AND `mode` IN ('CCN','CDN') AND `active` = '1' GROUP BY `mode` ORDER BY `mode` ASC"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
	if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ if($row['mode'] == "CDN") { $ocdn += (float)$row['tamt']; } else { $occn += (float)$row['tamt']; } } }
	$obsql = "SELECT * FROM `main_mortality` WHERE `date` <= '$today' AND `ccode` = '$cuscode' AND `mtype` = 'customer' AND `active` = '1' AND `dflag` = '0'"; $obquery = mysqli_query($conn,$obsql); $otcount = mysqli_num_rows($query);
	if($otcount > 0){ while($obrow = mysqli_fetch_assoc($obquery)){ $omortality += (float)$obrow['amount']; } }
	$obsql = "SELECT * FROM `main_itemreturns` WHERE `date` <= '$today' AND `vcode` = '$cuscode' AND `mode` = 'customer' AND `active` = '1' AND `dflag` = '0'"; $obquery = mysqli_query($conn,$obsql); $otcount = mysqli_num_rows($query);
	if($otcount > 0){ while($obrow = mysqli_fetch_assoc($obquery)){ $oreturns += (float)$obrow['amount']; } }
	
	$sales = $oinv + $ocdn + $obdramt;
	$receipts = $orct + $omortality + $oreturns + $occn + $obcramt;
	$balance = $sales - $receipts;
		
	$finval = $balance."@".$cname."@".$cnameno."@".$row_count;
}
echo $finval;
?>