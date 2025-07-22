<?php
//chicken_modify_shopprice1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];
include "chicken_generate_trnum_details.php";

//Transaction Information
$date = date("Y-m-d", strtotime($_POST['pdate']));
$warehouse = $_POST['warehouse'];
$itemcode = $_POST['itemcode'];
$price = $_POST['price']; if($price == ""){ $price = 0; }

$active = 1;
$dflag = $flag = 0;

$trtype = "shopprice1";
$trlink = "chicken_display_shopprice1.php";
$ids = $_POST['idvalue'];

//Modify Transaction
$sql = "UPDATE `item_shop_price` SET `date` = '$date',`warehouse` = '$warehouse',`itemcode` = '$itemcode',`price` = '$price',`flag` = '$flag',`active` = '$active',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `trnum` = '$ids';";
if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }
header('location:chicken_display_shopprice1.php?ccid='.$ccid);

