<?php
//chicken_modify_shopinvest1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];

//Transaction Information
$date = date("Y-m-d", strtotime($_POST['pdate']));
// $wcodes = $_POST['wcodes'];
$customer = $_POST['cnames'];
$inames = $_POST['inames'];

$cus_amt = $_POST['cus_amt']; if($cus_amt == ""){ $cus_amt = 0; }
$remarks = $_POST['narr'];

 $ids = $_POST['idvalue'];

$active = 1;
$flag = $tdflag = $pdflag = 0;

$trtype = "shopinvest1";
$trlink = "chicken_display_shopinvest1.php";

//Modify Transaction
 $sql = "UPDATE `shop_machine_investment` SET `date` = '$date',`vcode` = '$customer',`itemcode` = '$inames',`amount` = '$cus_amt',`remarks` = '$remarks',`flag` = '$flag',`active` = '$active',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `id` = '$ids';";
if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }

header('location:chicken_display_shopinvest1.php?ccid='.$ccid);

