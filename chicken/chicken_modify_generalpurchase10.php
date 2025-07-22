<?php
//chicken_modify_generalpurchase10.php
session_start(); include "newConfig.php";
include "number_format_ind.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
include "chicken_generate_trnum_details.php";
$client = $_SESSION['client'];

$ids = $_POST['idvalue']; $incr = $prefix = $fy = $invoice = $aemp = $atime = "";
$sql = "SELECT * FROM `pur_purchase` WHERE `invoice` = '$ids' AND `tdflag` = '0' AND `pdflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    if($invoice == ""){ $incr = $row['incr']; $prefix = $row['prefix']; $fy = $row['fy']; $invoice = $row['invoice']; $aemp = $row['addedemp']; $atime = $row['addedtime']; }
}
if($invoice != ""){
    $sql3 = "DELETE FROM `pur_purchase` WHERE `invoice` = '$ids' AND `tdflag` = '0' AND `pdflag` = '0'";
    if(!mysqli_query($conn,$sql3)){ die("Error: 1".mysqli_error($conn)); } else{ }
}

//Transaction Details
$date = date("Y-m-d", strtotime($_POST['date']));
$d = date("d",strtotime($date));
$m = date("m",strtotime($date));
$y = date("Y",strtotime($date));
$vcode = $_POST['vcode'];
$bookinvoice = $_POST['bookinvoice'];

$itemcode = $nweight = $price = $amount = $warehouse = array();
$i = 0; foreach($_POST['itemcode'] as $itemcodes){ $itemcode[$i] = $itemcodes; $i++; }
$i = 0; foreach($_POST['gst'] as $gsts){ $gst[$i] = $gsts; $i++; }
$i = 0; foreach($_POST['nweight'] as $nweights){ $nweight[$i] = $nweights; $i++; }
$i = 0; foreach($_POST['price'] as $prices){ $price[$i] = $prices; $i++; }
$i = 0; foreach($_POST['amount'] as $amounts){ $amount[$i] = $amounts; $i++; }
$i = 0; foreach($_POST['amount1'] as $amount1s){ $amount1[$i] = $amount1s; $i++; }
$i = 0; foreach($_POST['warehouse'] as $warehouses){ $warehouse[$i] = $warehouses; $i++; }

$roundoff = $_POST['roundoff'];
$finaltotal = $_POST['finaltotal'];
$remarks = $_POST['remarks'];

$active = 1;
$flag = $tdflag = $pdflag = 0;

$trtype = "generalpurchase10";
$trlink = "chicken_display_generalpurchase10.php";

//Save Purchase
$dsize = sizeof($itemcode);
for($i = 0;$i < $dsize;$i++){
    if($nweight[$i] == ""){ $nweight[$i] = 0; }
    if($price[$i] == ""){ $price[$i] = 0; }
    if($amount[$i] == ""){ $amount[$i] = 0; }
    if($amount1[$i] == ""){ $amount1[$i] = 0; }
    if($roundoff == ""){ $roundoff = 0; }
    if($finaltotal == ""){ $finaltotal = 0; }

    $sql = "INSERT INTO `pur_purchase` (`incr`,`d`,`m`,`y`,`fy`,`date`,`invoice`,`bookinvoice`,`vendorcode`,`itemcode`,`gst`,`amount1`,`netweight`,`itemprice`,`totalamt`,`roundoff`,`finaltotal`,`balance`,`warehouse`,`remarks`,`flag`,`active`,`tdflag`,`pdflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedemp`,`updated`) 
    VALUES ('$incr','$d','$m','$y','$fy','$date','$invoice','$bookinvoice','$vcode','$itemcode[$i]','$gst[$i]','$amount1[$i]','$nweight[$i]','$price[$i]','$amount[$i]','$roundoff','$finaltotal','$finaltotal','$warehouse[$i]','$remarks','$flag','$active','$tdflag','$pdflag','$trtype','$trlink','$aemp','$atime','$addedemp','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
}

header('location:chicken_display_generalpurchase10.php?ccid='.$ccid);

