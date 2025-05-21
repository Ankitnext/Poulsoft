<?php
//chicken_modify_shortagescreen.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];

//Transaction Information
$date = date("Y-m-d", strtotime($_POST['date']));
$warehouse = $_POST['warehouse'];
$a_type = $_POST['a_type'];
$itemcode = $_POST['itemcode'];
$jals = $_POST['jals']; if($jals == ""){ $jals = 0; }
$birds = $_POST['birds']; if($birds == ""){ $birds = 0; }
$quantity = $_POST['quantity']; if($quantity == ""){ $quantity = 0; }
$price = $_POST['price']; if($price == ""){ $price = 0; }
$amount = $_POST['amount']; if($amount == ""){ $amount = 0; }
$remarks = $_POST['remarks'];
$ids = $_POST['idvalue'];

$active = 1;
$flag = $tdflag = $pdflag = 0;

$trtype = "shortagescreen";
$trlink = "chicken_display_shortagescreen.php";

//Modify Transaction
$sql = "UPDATE `item_shortage_screen` SET `date` = '$date',`a_type` = '$a_type',`warehouse` = '$warehouse',`itemcode` = '$itemcode',`jals` = '$jals',`birds` = '$birds',`nweight` = '$quantity',`price` = '$price',`amount` = '$amount',`remarks` = '$remarks',`flag` = '$flag',`active` = '$active',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `trnum` = '$ids';";
if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }

header('location:chicken_display_shortagescreen.php?ccid='.$ccid);

