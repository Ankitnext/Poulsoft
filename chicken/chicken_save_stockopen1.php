<?php
//chicken_save_stockopen1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];
include "chicken_generate_trnum_details.php";

//Shop Investment Information
$date = date("Y-m-d", strtotime($_POST['pdate']));
$warehouse = $amount = $quantity = $price = $itemcode = $remarks = array();
$i = 0; foreach($_POST['warehouse'] as $warehouses){ $warehouse[$i] = $warehouses; $i++; }
$i = 0; foreach($_POST['quantity'] as $quantitys){ $quantity[$i] = $quantitys; $i++; }
$i = 0; foreach($_POST['price'] as $prices){ $price[$i] = $prices; $i++; }
$i = 0; foreach($_POST['amount'] as $amounts){ $amount[$i] = $amounts; $i++; }
$i = 0; foreach($_POST['itemcode'] as $itemcodes){ $itemcode[$i] = $itemcodes; $i++; }
$i = 0; foreach($_POST['remarks'] as $remarkss){ $remarks[$i] = $remarkss; $i++; }

$trtype = "stockopen1";
$trlink = "chicken_display_stockopen1.php";

//Save Purchase
$dsize = sizeof($warehouse);
for($i = 0;$i < $dsize;$i++){
    if($amount[$i] == ""){ $amount[$i] = 0; }
    
    //Generate Transaction No.
    $incr = 0; $prefix = $trnum = $trno_dt1 = ""; $trno_dt2 = array();
    $trno_dt1 = generate_transaction_details($date,"stockopen1","ISON","generate",$_SESSION['dbase']);
    $trno_dt2 = explode("@",$trno_dt1);
    $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fy = $trno_dt2[3];

    $sql = "INSERT INTO `item_stock_opening` (incr,prefix,trnum,date,warehouse,itemcode,quantity,price,amount,remarks,active,dflag,flag,trtype,trlink,addedemp,addedtime,updatedtime) 
    VALUES ('$incr','$prefix','$trnum','$date','$warehouse[$i]','$itemcode[$i]','$quantity[$i]','$price[$i]','$amount[$i]','$remarks[$i]','1','0','0','$trtype','$trlink','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
}
header('location:chicken_display_stockopen1.php?ccid='.$ccid);

