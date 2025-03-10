<?php
//chicken_modify_mortality1.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];

//Transaction Information
$date = date("Y-m-d", strtotime($_POST['pdate']));
$sector = $_POST['sector'];
$code = $_POST['code'];
$jalqty = $_POST['jalqty']; if($jalqty == ""){ $jalqty = 0; }
$birdqty = $_POST['birdqty']; if($birdqty == ""){ $birdqty = 0; }
$cqty = $_POST['cqty']; if($cqty == ""){ $cqty = 0; }
$cpri = $_POST['cpri']; if($cpri == ""){ $cpri = 0; }
$camt = $_POST['camt']; if($camt == ""){ $camt = 0; }
$oqty = $_POST['oqty'];
$remarks = $_POST['remarks'];
$ids = $_POST['idvalue'];

$active = 1;
$flag = $tdflag = $pdflag = 0;

$trtype = "mortality1";
$trlink = "chicken_display_mortality1.php";

//Save Transaction
$sql = "UPDATE `item_closingstock` SET `date` = '$date',`existquantity` = '$oqty',`closedbirds` = '$birdqty',`closedquantity` = '$cqty',`price` = '$cpri',`code` = '$code',`amount` = '$camt',`closedjals` = '$jalqty',`warehouse` = '$sector',`remarks` = '$remarks',`flag` = '$flag',`active` = '$active',`tdflag` = '$tdflag',`pdflag` = '$pdflag',`trtype` = '$trtype',`trlink` = '$trlink',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `trnum` = '$ids';";
if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }

header('location:chicken_display_mortality1.php?ccid='.$ccid);

