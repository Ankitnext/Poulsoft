<?php
//broiler_save_farmer.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['farmer'];

$sql='SHOW COLUMNS FROM `broiler_farmer`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("acc_holder_name", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_farmer` ADD `acc_holder_name` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `accountno`"; mysqli_query($conn,$sql); }

$sql ="SELECT MAX(incr) as incr FROM `broiler_farmer`"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
if($ccount > 0){ while($row = mysqli_fetch_assoc($query)){ $incr = $row['incr']; } $incr = $incr + 1; } else { $incr = 1; }
$prefix = "FMR";

$name = $_POST['fname'];
$mobile1 = $_POST['mobile1'];
$mobile2 = $_POST['mobile2'];
$address = $_POST['address'];
$panno = $_POST['panno'];
$aadharno = $_POST['aadharno'];
$nationalidno = $_POST['nationalidno'];
$farmer_group = $_POST['farmer_group'];
$tds_per = $_POST['tdsper'];
$acc_holder_name = $_POST['acc_holder_name'];
$accountno = $_POST['accountno'];
$ifsc_code = $_POST['ifsc_code'];
$bank_name = $_POST['bank_name'];
$branch_code = $_POST['branch_code'];
$usc = $_POST['usc'];
$serviceno = $_POST['serviceno'];

if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
$code = $prefix."-".$incr;
$sql = "INSERT INTO `broiler_farmer` (usc,serviceno,incr,prefix,code,name,farmer_group,mobile1,mobile2,panno,aadharno,nationalidno,address,tds_per,accountno,acc_holder_name,ifsc_code,bank_name,branch_code,flag,active,dflag,addedemp,addedtime,updatedtime) VALUES ('$usc','$serviceno','$incr','$prefix','$code','$name','$farmer_group','$mobile1','$mobile2','$panno','$aadharno','$nationalidno','$address','$tds_per','$accountno','$acc_holder_name','$ifsc_code','$bank_name','$branch_code','0','1','0','$addedemp','$addedtime','$addedtime')";
if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { $incr++; }
header('location:broiler_display_farmer.php?ccid='.$ccid);
?>