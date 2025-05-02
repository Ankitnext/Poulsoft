<?php
//chicken_modify_generalsales5.php
session_start(); include "newConfig.php";
include "number_format_ind.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];

$ids = $_POST['idvalue']; $incr = $prefix = $trnum = $aemp = $atime = "";
$sql = "SELECT * FROM `customer_sales` WHERE `invoice` = '$ids' AND `tdflag` = '0' AND `pdflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    if($trnum == ""){ $incr = $row['incr']; $prefix = $row['prefix']; $trnum = $row['invoice']; $aemp = $row['addedemp']; $atime = $row['addedtime']; }
}
if($trnum != ""){
    $sql3 = "DELETE FROM `customer_sales` WHERE `invoice` = '$ids' AND `tdflag` = '0' AND `pdflag` = '0'";
    if(!mysqli_query($conn,$sql3)){ die("Error: 1".mysqli_error($conn)); } else{ }
}

//Sale Information
$date = date("Y-m-d", strtotime($_POST['date']));
$d = date("d",strtotime($date));
$m = date("m",strtotime($date));
$y = date("Y",strtotime($date));
$vcode = $_POST['vcode'];
$bookinvoice = $_POST['bookinvoice'];
$vehicle = $_POST['vehicle'];
$driver = $_POST['driver'];

$itemcode = $jals = $birds = $tweight = $eweight = $nweight = $price = $amount = $warehouse = array();
$i = 0; foreach($_POST['itemcode'] as $itemcodes){ $itemcode[$i] = $itemcodes; $i++; }
$i = 0; foreach($_POST['jals'] as $jalss){ $jals[$i] = $jalss; $i++; }
$i = 0; foreach($_POST['birds'] as $birdss){ $birds[$i] = $birdss; $i++; }
$i = 0; foreach($_POST['tweight'] as $tweights){ $tweight[$i] = $tweights; $i++; }
$i = 0; foreach($_POST['eweight'] as $eweights){ $eweight[$i] = $eweights; $i++; }
$i = 0; foreach($_POST['nweight'] as $nweights){ $nweight[$i] = $nweights; $i++; }
$i = 0; foreach($_POST['price'] as $prices){ $price[$i] = $prices; $i++; }
$i = 0; foreach($_POST['amount'] as $amounts){ $amount[$i] = $amounts; $i++; }
$i = 0; foreach($_POST['warehouse'] as $warehouses){ $warehouse[$i] = $warehouses; $i++; }

$tcds_chk = $_POST['tcds_chk'];
$tcds_per = $_POST['tcds_per'];
$tcds_type1 = $_POST['tcds_type1'];
$tcds_type2 = $_POST['tcds_type2'];
$tcds_amt = $_POST['tcds_amt'];
$transporter_code = $_POST['transporter_code'];
$freight_amt = $_POST['freight_amt'];
$dressing_charge = $_POST['dressing_charge'];
$roundoff_type1 = $_POST['roundoff_type1'];
$roundoff_type2 = $_POST['roundoff_type2'];
$roundoff_amt = $_POST['roundoff_amt'];
$finaltotal = $_POST['finaltotal'];
$remarks = $_POST['remarks'];

$active = 1;
$flag = $tdflag = $pdflag = 0;

$trtype = "generalsales5";
$trlink = "chicken_display_generalsales5.php";

//Save Purchase
$dsize = sizeof($itemcode);
for($i = 0;$i < $dsize;$i++){
    if($jals[$i] == ""){ $jals[$i] = 0; }
    if($birds[$i] == ""){ $birds[$i] = 0; }
    if($tweight[$i] == ""){ $tweight[$i] = 0; }
    if($eweight[$i] == ""){ $eweight[$i] = 0; }
    if($nweight[$i] == ""){ $nweight[$i] = 0; }
    if($price[$i] == ""){ $price[$i] = 0; }
    if($amount[$i] == ""){ $amount[$i] = 0; }
    if($tcds_per == ""){ $tcds_per = 0; }
    if($tcds_amt == ""){ $tcds_amt = 0; }
    if($freight_amt == ""){ $freight_amt = 0; }
    if($dressing_charge == ""){ $dressing_charge = 0; }
    if($roundoff_amt == ""){ $roundoff_amt = 0; }
    if($finaltotal == ""){ $finaltotal = 0; }

    $sql = "INSERT INTO `customer_sales` (`incr`,`d`,`m`,`y`,`fy`,`date`,`invoice`,`bookinvoice`,`customercode`,`itemcode`,`jals`,`birds`,`totalweight`,`emptyweight`,`netweight`,`itemprice`,`totalamt`,`transporter_code`,`freight_amount`,`tcdsper`,`tcds_type1`,`tcds_type2`,`tcdsamt`,`dressing_charge`,`roundoff_type1`,`roundoff_type2`,`roundoff`,`finaltotal`,`balance`,`drivercode`,`vehiclecode`,`warehouse`,`remarks`,`flag`,`active`,`tdflag`,`pdflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedemp`,`updated`) 
    VALUES ('$incr','$d','$m','$y','$pfx','$date','$trnum','$bookinvoice','$vcode','$itemcode[$i]','$jals[$i]','$birds[$i]','$tweight[$i]','$eweight[$i]','$nweight[$i]','$price[$i]','$amount[$i]','$transporter_code','$freight_amt','$tcds_per','$tcds_type1','$tcds_type2','$tcds_amt','$dressing_charge','$roundoff_type1','$roundoff_type2','$roundoff_amt','$finaltotal','$finaltotal','$driver','$vehicle','$warehouse[$i]','$remarks','$flag','$active','$tdflag','$pdflag','$trtype','$trlink','$aemp','$atime','$addedemp','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
}

header('location:chicken_display_generalsales5.php?ccid='.$ccid);

