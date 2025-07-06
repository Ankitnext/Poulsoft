<?php
//chicken_modify_customerreceipt4.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];

$sql = "SELECT * FROM `extra_access` WHERE `field_name` = 'Customer Receipt' AND `field_function` = 'Hide DocNo' AND `flag` = '1'";
$query = mysqli_query($conn,$sql); $hdcno_flag = mysqli_num_rows($query);

//Payment Information
$ids = $_POST['idvalue'];
$date = date("Y-m-d", strtotime($_POST['date']));
$ccode = $_POST['ccode'];
$smtype = $_POST['smtype'];
$mode = $_POST['mode'];
$code = $_POST['code'];
$amount1 = $_POST['amount1'];
// $dcno = $_POST['dcno']; 
$sector = $_POST['sector'];
$remarks = $_POST['remarks'];
$tcds_per = $_POST['tcds_per'];
$tcds_amt = $_POST['tcds_amt'];
$amount = $_POST['amount'];
$warehouse = $_POST['warehouse'];

$vtype = "S";
$flag = $active = 1;
$tdflag = $pdflag = 0;

$trtype = "customerreceipt4";
$trlink = "chicken_display_customerreceipt4.php";

//Save Payments
if($amount1 == ""){ $amount1 = 0; }
if($tcds_per == ""){ $tcds_per = 0; }
if($tcds_amt == ""){ $tcds_amt = 0; }
if($amount == ""){ $amount = 0; }
if((float)$tcds_amt > 0){ } else{ $tcds_per = 0; }

include_once("poulsoft_store_chngmaster.php");
$chng_type = "Edit";
$edit_file = "chicken_modify_customerreceipt4.php";
$mtbl_name = "customer_receipts";
$tno_cname = "trnum";
$msg1 = array("file"=>$edit_file, "trnum"=>$ids, "tno_cname"=>$tno_cname, "edit_emp"=>$addedemp, "edit_time"=>$addedtime, "chng_type"=>$chng_type, "mtbl_name"=>$mtbl_name);
$message = json_encode($msg1);
store_modified_details($message);

 $sql = "UPDATE `customer_receipts` SET `date` = '$date',`ccode` = '$ccode',`sm_code` = '$smtype',`mode` = '$mode',`method` = '$code',`amount1` = '$amount1',`tcds_per` = '$tcds_per',`tcds_amt` = '$tcds_amt',`amount` = '$amount1',`warehouse` = '$warehouse',`remarks` = '$remarks',`flag` = '$flag',`active` = '$active',`tdflag` = '$tdflag',`pdflag` = '$pdflag',`trtype` = '$trtype',`trlink` = '$trlink',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `trnum` = '$ids';";

if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }

header('location:chicken_display_customerreceipt4.php?ccid='.$ccid);

