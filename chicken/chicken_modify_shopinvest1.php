<?php
//chicken_modify_shopinvest1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];
include "chicken_generate_trnum_details.php";

//Transaction Information
$date = date("Y-m-d", strtotime($_POST['pdate']));
$vcode = $_POST['vcode'];
$itemcode = $_POST['itemcode'];
$amount = $_POST['amount']; if($amount == ""){ $amount = 0; }
$remarks = $_POST['remarks'];

$active = 1;
$dflag = $flag = 0;

$trtype = "shopinvest1";
$trlink = "chicken_display_shopinvest1.php";
$ids = $_POST['idvalue'];

//Modify Transaction
$sql = "UPDATE `shop_machine_investment` SET `date` = '$date',`vcode` = '$vcode',`itemcode` = '$itemcode',`amount` = '$amount',`remarks` = '$remarks',`flag` = '$flag',`active` = '$active',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `trnum` = '$ids';";
if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
header('location:chicken_display_shopinvest1.php?ccid='.$ccid);

