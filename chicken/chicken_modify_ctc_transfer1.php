<?php
//chicken_modify_ctc_transfer1.php
session_start(); include "newConfig.php";
include "number_format_ind.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];

$ids = $_POST['idvalue']; $incr = $prefix = $trnum = $aemp = $atime = "";
$sql = "SELECT * FROM `chicken_ctc_stktransfer` WHERE `trnum` = '$ids' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    if($trnum == ""){ $incr = $row['incr']; $prefix = $row['prefix']; $trnum = $row['trnum']; $aemp = $row['addedemp']; $atime = $row['addedtime']; }
}
if($trnum != ""){
    $sql3 = "DELETE FROM `chicken_ctc_stktransfer` WHERE `trnum` = '$ids' AND `dflag` = '0'";
    if(!mysqli_query($conn,$sql3)){ die("Error: 1".mysqli_error($conn)); } else{ }
}
$active = 1;
$flag = $dflag = 0;
//Sale Information
$date = date("Y-m-d", strtotime($_POST['date']));
$d = date("d",strtotime($date));
$m = date("m",strtotime($date));
$y = date("Y",strtotime($date));
$warehouse = $_POST['warehouse'];
$bookinvoice = $_POST['bookinvoice'];
$vehicle = $_POST['vehicle'];
$driver = $_POST['driver'];

$itemcode = $jals = $birds = $tweight = $eweight = $nweight = $price = $amount = $vcode = $vcode2 = $amount2 = $price2 = $remark = array();
$i = 0; foreach($_POST['itemcode'] as $itemcodes){ $itemcode[$i] = $itemcodes; $i++; }
$i = 0; foreach($_POST['jals'] as $jalss){ $jals[$i] = $jalss; $i++; }
$i = 0; foreach($_POST['birds'] as $birdss){ $birds[$i] = $birdss; $i++; }
$i = 0; foreach($_POST['tweight'] as $tweights){ $tweight[$i] = $tweights; $i++; }
$i = 0; foreach($_POST['eweight'] as $eweights){ $eweight[$i] = $eweights; $i++; }
$i = 0; foreach($_POST['nweight'] as $nweights){ $nweight[$i] = $nweights; $i++; }
$i = 0; foreach($_POST['price'] as $prices){ $price[$i] = $prices; $i++; }
$i = 0; foreach($_POST['amount'] as $amounts){ $amount[$i] = $amounts; $i++; }
$i = 0; foreach($_POST['vcode'] as $vcodes){ $vcode[$i] = $vcodes; $i++; }
$i = 0; foreach($_POST['price2'] as $price2s){ $price2[$i] = $price2s; $i++; }
$i = 0; foreach($_POST['amount2'] as $amount2s){ $amount2[$i] = $amount2s; $i++; }
$i = 0; foreach($_POST['vcode2'] as $vcode2s){ $vcode2[$i] = $vcode2s; $i++; }
$i = 0; foreach($_POST['remark'] as $remarks){ $remark[$i] = $remarks; $i++; }

$active = 1;
$flag = $tdflag = $pdflag = 0;

$trtype = "ctc_transfer1";
$trlink = "chicken_display_ctc_transfer1.php";

//Save Purchase
$dsize = sizeof($itemcode);
for($i = 0;$i < $dsize;$i++){
    if($jals[$i] == ""){ $jals[$i] = 0; }
    if($birds[$i] == ""){ $birds[$i] = 0; }
    if($tweight[$i] == ""){ $tweight[$i] = 0; }
    if($eweight[$i] == ""){ $eweight[$i] = 0; }
    if($nweight[$i] == ""){ $nweight[$i] = 0; }
    if($price[$i] == ""){ $price[$i] = 0; }
    if($price2[$i] == ""){ $price2[$i] = 0; }
    if($amount[$i] == ""){ $amount[$i] = 0; }
    if($amount2[$i] == ""){ $amount2[$i] = 0; }
    
    $sql = "INSERT INTO `chicken_ctc_stktransfer` (`incr`,`trnum`,`date`,`bill_no`,`from_vcode`,`item`,`jals`,`birds`,`tweight`,`eweight`,`nweight`,`from_price`,`from_amt`,`to_vcode`,`to_price`,`to_amt`,`vehicle`,`driver`,`warehouse`,`remarks`,`flag`,`active`,`dflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedtime`) 
    VALUES ('$incr','$trnum','$date','$bookinvoice','$vcode[$i]','$itemcode[$i]','$jals[$i]','$birds[$i]','$tweight[$i]','$eweight[$i]','$nweight[$i]','$price[$i]','$amount[$i]','$vcode2[$i]','$price2[$i]','$amount2[$i]','$vehicle','$driver','$warehouse','$remark[$i]','$flag','$active','$dflag','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
     if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
}

header('location:chicken_display_ctc_transfer1.php?ccid='.$ccid);

