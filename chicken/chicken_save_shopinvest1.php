<?php
//chicken_save_shopinvest1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];
include "chicken_generate_trnum_details.php";

//Shop Investment Information
// $date = date("Y-m-d", strtotime($_POST['pdate']));
$vcode = $amount = $itemcode = $remarks = array();
$i = 0; foreach($_POST['pdate'] as $dates){ $date[$i] = date("Y-m-d", strtotime($dates)); $i++; }
$i = 0; foreach($_POST['vcode'] as $vcodes){ $vcode[$i] = $vcodes; $i++; }
$i = 0; foreach($_POST['amount'] as $amounts){ $amount[$i] = $amounts; $i++; }
$i = 0; foreach($_POST['itemcode'] as $itemcodes){ $itemcode[$i] = $itemcodes; $i++; }
$i = 0; foreach($_POST['remarks'] as $remarkss){ $remarks[$i] = $remarkss; $i++; }

$trtype = "shopinvest1";
$trlink = "chicken_display_shopinvest1.php";

//Save Purchase
$dsize = sizeof($vcode);
for($i = 0;$i < $dsize;$i++){
    if($amount[$i] == ""){ $amount[$i] = 0; }
    
    //Generate Transaction No.
    $incr = 0; $prefix = $trnum = $trno_dt1 = ""; $trno_dt2 = array();
    $trno_dt1 = generate_transaction_details($date[$i],"shopinvest1","VSSI","generate",$_SESSION['dbase']);
    $trno_dt2 = explode("@",$trno_dt1);
    $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fy = $trno_dt2[3];

    $sql = "INSERT INTO `shop_machine_investment` (incr,prefix,trnum,date,vcode,itemcode,amount,remarks,active,dflag,flag,trtype,trlink,addedemp,addedtime,updatedtime) 
    VALUES ('$incr','$prefix','$trnum','$date[$i]','$vcode[$i]','$itemcode[$i]','$amount[$i]','$remarks[$i]','1','0','0','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
}
header('location:chicken_display_shopinvest1.php?ccid='.$ccid);

