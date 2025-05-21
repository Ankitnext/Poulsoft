<?php
//broiler_modify_feedformula2.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['feedformula2'];


$id = $_POST['idvalue'];
$sql ="SELECT * FROM `broiler_feed_formula` WHERE `id` = '$id'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
if($ccount > 0){
    while($row = mysqli_fetch_assoc($query)){ $incr = $row['incr']; $prefix = $row['prefix']; $code = $row['code']; }
    $sql = "DELETE FROM `broiler_feed_formula` WHERE `code` = '$code'";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
}

$date = date("Y-m-d",strtotime($_POST['date']));
$mill_code = $_POST['mill_code'];
$formula_item_code = $_POST['formula_item_code'];
$description = $_POST['description'];
$total_qty = $_POST['total_qty'];
$total_rate = $_POST['total_rate'];
$total_amt = $_POST['total_amt'];

$i = 0; foreach($_POST['item_code'] as $item_codes){ $item_code[$i] = $item_codes; $i++; }
$i = 0; foreach($_POST['unit_code'] as $unit_codes){ $unit_code[$i] = $unit_codes; $i++; }
$i = 0; foreach($_POST['item_qty'] as $item_qtys){ $item_qty[$i] = $item_qtys; $i++; }
$i = 0; foreach($_POST['rate'] as $rates){ $rate[$i] = $rates; $i++; }
$i = 0; foreach($_POST['amount'] as $amounts){ $amount[$i] = $amounts; $i++; }

$ssize = sizeof($item_code);
for($i = 0;$i < $ssize;$i++){
    if($item_qty[$i] == ""){ $item_qty[$i] = 0; }
    if($rate[$i] == ""){ $rate[$i] = 0; }
    if($amount[$i] == ""){ $amount[$i] = 0; }
    if($total_qty == ""){ $total_qty = 0; }
    if($total_rate == ""){ $total_rate = 0; }
    if($total_amt == ""){ $total_amt = 0; }
    $sql = "INSERT INTO `broiler_feed_formula` (incr,prefix,code,description,formula_item_code,date,mill_code,item_code,unit_code,item_qty,total_qty,rate,total_rate,amount,total_amt,flag,active,dflag,addedemp,addedtime,updatedtime) VALUES ('$incr','$prefix','$code','$description','$formula_item_code','$date','$mill_code','$item_code[$i]','$unit_code[$i]','$item_qty[$i]','$total_qty','$rate[$i]','$total_rate','$amount[$i]','$total_amt','0','1','0','$addedemp','$addedtime','$addedtime')";
    if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
}
header('location:broiler_display_feedformula2.php?ccid='.$ccid);
?>