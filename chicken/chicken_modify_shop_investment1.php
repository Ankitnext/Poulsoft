<?php
//chicken_modify_shop_investment1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];

//Transaction Information
$date = date("Y-m-d", strtotime($_POST['date']));
$customer = $_POST['customer'];
$itemcode = $_POST['itemcode'];
$amount = $_POST['amount']; if($amount == ""){ $amount = 0; }
$remarks = $_POST['remarks'];
$ids = $_POST['idvalue'];

$active = 1;
$flag = $tdflag = $pdflag = 0;

$trtype = "shop_investment1";
$trlink = "chicken_display_shop_investment1.php";

//Modify Transaction
$sql = "UPDATE `vendor_shop_investment` SET `date` = '$date',`vcode` = '$customer',`icode` = '$itemcode',`amount` = '$amount',`remarks` = '$remarks',`flag` = '$flag',`active` = '$active',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `trnum` = '$ids';";
if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }

header('location:chicken_display_shop_investment1.php?ccid='.$ccid);

