<?php
//chicken_supplier_balances.php
include "newConfig.php";
if(!isset($_SESSION)){ session_start(); }
date_default_timezone_set("Asia/Kolkata");
$today = date("Y-m-d");
$vendors = $_GET['vendors'];
$rows = $_GET['row_cnt'];

$old_inv = ""; $pinv = $ppay = $psdn = $pscn = $pmort = $oreturns = $obcramt = $obdramt = 0;
$sql1 = "SELECT * FROM `main_contactdetails` WHERE `code` LIKE '$vendors'"; $query = mysqli_query($conn,$sql1); 
while($row = mysqli_fetch_assoc($query)){ if($row['obtype'] == "Cr"){ $obcramt = $row['obamt']; } else if($row['obtype'] == "Dr"){ $obdramt = $row['obamt']; } else{ } }
$sql = "SELECT invoice,finaltotal FROM `pur_purchase` WHERE `date` < '$today' AND `vendorcode` LIKE '$vendors' AND `active` = '1' AND `tdflag` = '0' AND `pdflag` = '0' ORDER BY `invoice` ASC"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ if($old_inv != $row['invoice']){ $pinv += (float)$row['finaltotal']; $old_inv = $row['invoice']; } } }
$sql = "SELECT SUM(amount) as tamt FROM `pur_payments` WHERE  `date` < '$today' AND `ccode` LIKE '$vendors' AND `active` = '1'"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $ppay += (float)$row['tamt']; } }
$sql = "SELECT SUM(amount) as tamt,mode FROM `main_crdrnote` WHERE  `date` < '$today' AND `ccode` LIKE '$vendors' AND `mode` IN ('SCN','SDN') AND `active` = '1' GROUP BY `mode` ORDER BY `mode` ASC"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ if($row['mode'] == "SDN") { $psdn += (float)$row['tamt']; } else { $pscn += (float)$row['tamt']; } } }
$sql = "SELECT * FROM `main_mortality` WHERE `date` < '$today' AND `ccode` = '$vendors' AND `mtype` = 'supplier' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $pmort += (float)$row['amount']; } }
$sql = "SELECT * FROM `main_itemreturns` WHERE `date` < '$today' AND `vcode` = '$vendors' AND `mode` = 'supplier' AND `active` = '1' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql); $otcount = mysqli_num_rows($query);
if($otcount > 0){ while($row = mysqli_fetch_assoc($query)){ $oreturns += (float)$row['amount']; } }

$purchases = $pinv + $pscn + $obcramt;
$payments = $ppay + $pmort + $oreturns + $psdn + $obdramt;
$balance = $purchases - $payments;

echo $rows."[@$&]".$balance;