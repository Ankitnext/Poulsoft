<?php
//chicken_modify_debit.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];
include "chicken_generate_trnum_details.php";

//Transaction Information
$date = date("Y-m-d", strtotime($_POST['pdate']));
$company = $_POST['company'];
$itemcode = $_POST['itemcode'];
$quantity = $_POST['quantity']; if($quantity == ""){ $quantity = 0; }
$price = $_POST['price']; if($price == ""){ $price = 0; }
$amount = $_POST['amount']; if($amount == ""){ $amount = 0; }

$active = 1;
$dflag = $flag = 0;

$trtype = "debit";
$trlink = "chicken_display_debit.php";
$ids = $_POST['idvalue'];

//Modify Transaction
$sql = "UPDATE `main_mortality` SET `date` = '$date',`ccode` = '$company',`itemcode` = '$itemcode',`amount` = '$amount',`quantity` = '$quantity',`price` = '$price',`flag` = '$flag',`active` = '$active',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `code` = '$ids';";
if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
header('location:chicken_display_debit.php?ccid='.$ccid);

