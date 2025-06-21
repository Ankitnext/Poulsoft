<?php
//chicken_modify_stockopen1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];
include "chicken_generate_trnum_details.php";

//Transaction Information
$date = date("Y-m-d", strtotime($_POST['pdate']));
$warehouse = $_POST['warehouse'];
$itemcode = $_POST['itemcode'];
$quantity = $_POST['quantity']; if($quantity == ""){ $quantity = 0; }
$price = $_POST['price']; if($price == ""){ $price = 0; }
$amount = $_POST['amount']; if($amount == ""){ $amount = 0; }
$remarks = $_POST['remarks'];

$active = 1;
$dflag = $flag = 0;

$trtype = "stockopen1";
$trlink = "chicken_display_stockopen1.php";
$ids = $_POST['idvalue'];

//Modify Transaction
$sql = "UPDATE `item_stock_opening` SET `date` = '$date',`warehouse` = '$warehouse',`itemcode` = '$itemcode',`quantity` = '$quantity',`price` = '$price',`amount` = '$amount',`remarks` = '$remarks',`flag` = '$flag',`active` = '$active',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `trnum` = '$ids';";
if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
header('location:chicken_display_stockopen1.php?ccid='.$ccid);

