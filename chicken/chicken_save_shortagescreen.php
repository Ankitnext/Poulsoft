<?php
//chicken_save_shortagescreen.php
session_start(); include "newConfig.php";
include "chicken_generate_trnum_details.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];

//Payment Information
$date = $warehouse = $a_type = $itemcode = $jals = $birds = $quantity = $price = $amount = $remarks = array();
$i = 0; foreach($_POST['date'] as $dates){ $date[$i] = date("Y-m-d", strtotime($dates)); $i++; }
$i = 0; foreach($_POST['warehouse'] as $warehouses){ $warehouse[$i] = $warehouses; $i++; }
$i = 0; foreach($_POST['a_type'] as $a_types){ $a_type[$i] = $a_types; $i++; }
$i = 0; foreach($_POST['itemcode'] as $itemcodes){ $itemcode[$i] = $itemcodes; $i++; }
$i = 0; foreach($_POST['jals'] as $jalss){ $jals[$i] = $jalss; $i++; }
$i = 0; foreach($_POST['birds'] as $birdss){ $birds[$i] = $birdss; $i++; }
$i = 0; foreach($_POST['quantity'] as $quantitys){ $quantity[$i] = $quantitys; $i++; }
$i = 0; foreach($_POST['price'] as $prices){ $price[$i] = $prices; $i++; }
$i = 0; foreach($_POST['amount'] as $amounts){ $amount[$i] = $amounts; $i++; }
$i = 0; foreach($_POST['remarks'] as $remarkss){ $remarks[$i] = $remarkss; $i++; }
$active = 1;
$flag = $dflag = 0;

$trtype = "shortagescreen";
$trlink = "chicken_display_shortagescreen.php";

//Save Payments
$dsize = sizeof($itemcode);
for($i = 0;$i < $dsize;$i++){
    //Generate Transaction No.
    $incr = 0; $prefix = $trnum = $trno_dt1 = ""; $trno_dt2 = array();
    $trno_dt1 = generate_transaction_details($date[$i],"shortagescreen","SSCS","generate",$_SESSION['dbase']);
    $trno_dt2 = explode("@",$trno_dt1);
    $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fy = $trno_dt2[3];

    if($jals[$i] == ""){ $jals[$i] = 0; }
    if($birds[$i] == ""){ $birds[$i] = 0; }
    if($quantity[$i] == ""){ $quantity[$i] = 0; }
    if($price[$i] == ""){ $price[$i] = 0; }
    if($amount[$i] == ""){ $amount[$i] = 0; }
    
    $sql = "INSERT INTO `item_shortage_screen` (`incr`,`prefix`,`trnum`,`date`,`warehouse`,`a_type`,`itemcode`,`jals`,`birds`,`nweight`,`price`,`amount`,`remarks`,`flag`,`active`,`dflag`,`trtype`,`trlink`,`addedemp`,`addedtime`,`updatedtime`) 
    VALUES ('$incr','$prefix','$trnum','$date[$i]','$warehouse[$i]','$a_type[$i]','$itemcode[$i]','$jals[$i]','$birds[$i]','$quantity[$i]','$price[$i]','$amount[$i]','$remarks[$i]','$flag','$active','$dflag','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
}
header('location:chicken_display_shortagescreen.php?ccid='.$ccid);

