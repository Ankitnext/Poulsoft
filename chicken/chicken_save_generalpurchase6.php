<?php
//chicken_save_generalpurchase6.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];
include "number_format_ind.php";
include "chicken_generate_trnum_details.php";
include "chicken_send_wapp_master2.php";
include "cus_outbalfunction.php";

$sql='SHOW COLUMNS FROM `pur_purchase`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("prc_obrd", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `pur_purchase` ADD `prc_obrd` INT(100) NOT NULL DEFAULT '0' COMMENT 'Price on Bird Flag' AFTER `itemprice`"; mysqli_query($conn,$sql); }

//Transaction Information
$date = date("Y-m-d", strtotime($_POST['date']));
$d = date("d",strtotime($date));
$m = date("m",strtotime($date));
$y = date("Y",strtotime($date));

$warehouse = $_POST['warehouse'];
$bookinvoice = $_POST['bookinvoice'];
 
$vcode = $itemcode = $jals = $birds = $tweight = $eweight = $nweight = $price = $item_amt = $remarks = array();
$i = 0; foreach($_POST['vcode'] as $vcodes){ $vcode[$i] = $vcodes; $i++; }
$i = 0; foreach($_POST['itemcode'] as $itemcodes){ $itemcode[$i] = $itemcodes; $i++; }
$i = 0; foreach($_POST['jals'] as $jalss){ $jals[$i] = $jalss; $i++; }
$i = 0; foreach($_POST['birds'] as $birdss){ $birds[$i] = $birdss; $i++; }
$i = 0; foreach($_POST['tweight'] as $tweights){ $tweight[$i] = $tweights; $i++; }
$i = 0; foreach($_POST['eweight'] as $eweights){ $eweight[$i] = $eweights; $i++; }
$i = 0; foreach($_POST['nweight'] as $nweights){ $nweight[$i] = $nweights; $i++; }
$i = 0; foreach($_POST['price'] as $prices){ $price[$i] = $prices; $i++; }
$i = 0; foreach($_POST['item_amt'] as $item_amts){ $item_amt[$i] = $item_amts; $i++; }
$i = 0; foreach($_POST['remarks'] as $remarkss){ $remarks[$i] = $remarkss; $i++; }

$active = 1;
$flag = $tdflag = $pdflag = 0;

$trtype = "generalpurchase6";
$trlink = "chicken_display_generalpurchase6.php";

$dsize = sizeof($vcode);
for($i = 0;$i < $dsize;$i++){
    //Save Sale Transaction
    if($jals[$i] == ""){ $jals[$i] = 0; }
    if($birds[$i] == ""){ $birds[$i] = 0; }
    if($tweight[$i] == ""){ $tweight[$i] = 0; }
    if($eweight[$i] == ""){ $eweight[$i] = 0; }
    if($nweight[$i] == ""){ $nweight[$i] = 0; }
    if($price[$i] == ""){ $price[$i] = 0; }
    if($item_amt[$i] == ""){ $item_amt[$i] = 0; }
    if($_POST['prc_obrd'][$i] == "on" || $_POST['prc_obrd'][$i] == true || $_POST['prc_obrd'][$i] == "1" || $_POST['prc_obrd'][$i] == 1){ $prc_obrd = 1; } else{ $prc_obrd = 0; }

    //Generate Transaction No.
    $incr = 0; $prefix = $invoice = $trno_dt1 = ""; $trno_dt2 = array();
    $trno_dt1 = generate_transaction_details($date,"generalpurchase6","LBT","generate",$_SESSION['dbase']);
    $trno_dt2 = explode("@",$trno_dt1);
    $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $invoice = $trno_dt2[2]; $fy = $trno_dt2[3];

    $sql = "INSERT INTO `pur_purchase` (`incr`,`d`,`m`,`y`,`fy`,`date`,`invoice`,`bookinvoice`,`vendorcode`,`itemcode`,`jals`,`birds`,`totalweight`,`emptyweight`,`netweight`,`itemprice`,`prc_obrd`,`totalamt`,`finaltotal`,`balance`,`warehouse`,`remarks`,`flag`,`active`,`tdflag`,`pdflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updated`,`client`) 
    VALUES ('$incr','$d','$m','$y','$fy','$date','$invoice','$bookinvoice','$vcode[$i]','$itemcode[$i]','$jals[$i]','$birds[$i]','$tweight[$i]','$eweight[$i]','$nweight[$i]','$price[$i]','$prc_obrd','$item_amt[$i]','$item_amt[$i]','$item_amt[$i]','$warehouse','$remarks[$i]','$flag','$active','$tdflag','$pdflag','$trtype','$trlink','$addedemp','$addedtime','$addedtime','$client')";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
}

header('location:chicken_display_generalpurchase6.php?ccid='.$ccid);

