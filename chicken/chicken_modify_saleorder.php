<?php
//chicken_modify_saleorder.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];

//Transaction Information
$date = date("Y-m-d", strtotime($_POST['pdate']));
$wcodes = $_POST['wcodes'];
$customer = $_POST['cnames'];
$supplier = $_POST['snames'];
$place = $_POST['place'];
$super_no = $_POST['sv_no'];
$veh_no = $_POST['v_no'];
$itemcode = $_POST['scat'];
$cus_qty = $_POST['cus_qty']; if($cus_qty == ""){ $cus_qty = 0; }
$remarks = $_POST['narr'];

 $ids = $_POST['idvalue'];

$active = 1;
$flag = $tdflag = $pdflag = 0;

$trtype = "saleorder";
$trlink = "chicken_display_saleorder.php";

//Modify Transaction
 $sql = "UPDATE `salesorder` SET `date` = '$date',`ccode` = '$customer',`supervisor` = '$super_no',`vehicleno` = '$veh_no',`supplier` = '$supplier',`warehouse` = '$wcodes',`place` = '$place',`itemcode` = '$itemcode',`twt` = '$cus_qty',`remarks` = '$remarks',`flag` = '$flag',`active` = '$active',`updatedemp` = '$addedemp',`updatetime` = '$addedtime' WHERE `id` = '$ids';";
if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }

header('location:chicken_display_saleorder.php?ccid='.$ccid);

