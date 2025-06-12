<?php
//broiler_save_currency.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['currency'];

// $sql='SHOW COLUMNS FROM `main_groups`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
// while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
// if(in_array("cus_controller_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_itemreturns` ADD `cus_controller_code` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Customer Asset CoA Account' AFTER `description`"; mysqli_query($conn,$sql); }
// if(in_array("cus_prepayment_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_itemreturns` ADD `cus_prepayment_code` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Customer Advance CoA Account' AFTER `cus_controller_code`"; mysqli_query($conn,$sql); }
// if(in_array("sup_controller_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_itemreturns` ADD `sup_controller_code` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Supplier Liability CoA Account' AFTER `cus_prepayment_code`"; mysqli_query($conn,$sql); }
// if(in_array("sup_prepayment_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `broiler_itemreturns` ADD `sup_prepayment_code` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Supplier Advance CoA Account' AFTER `sup_controller_code`"; mysqli_query($conn,$sql); }

// $gtype = $_POST['gtype'];
// $gdesc = $_POST['gdesc'];
$fcountry = $_POST['fcountry'];
$tcountry = $_POST['tcountry'];
$fvalue = $_POST['fvalue'];
$tvalue = $_POST['tvalue'];
$prefix = "CCU";
// $cus_controller_code = $cus_prepayment_code = $sup_controller_code = $sup_prepayment_code = "";


$sql = "SELECT MAX(incr) as incr FROM `currency_convertor` WHERE `prefix` = '$prefix'"; $query = mysqli_query($conn,$sql); $ccount = mysqli_num_rows($query);
if($ccount > 0){
	while($row = mysqli_fetch_assoc($query)){ $incrs = $row['incr']; } $incrs = $incrs + 1; if($incrs < 10){ $incrs = '000'.$incrs; } else if($incrs >= 10 && $incrs < 100){ $incrs = '00'.$incrs; } else if($incrs >= 100 && $incrs < 1000){ $incrs = '0'.$incrs; } else { }
    $code = $prefix."-".$incrs;
}
else {
	$incrs = 1; if($incrs < 10){ $incrs = '000'.$incrs; } else if($incrs >= 10 && $incrs < 100){ $incrs = '00'.$incrs; } else if($incrs >= 100 && $incrs < 1000){ $incrs = '0'.$incrs; } else { }
	$code = $prefix."-".$incrs;
}
$sql = "INSERT INTO `currency_convertor` (incr,prefix,code,cont_code,curr_rs,curr_other,other_code,active,dflag,flag,addedemp,addedtime) VALUES 
('$incrs','$prefix','$code','$fcountry','$fvalue','$tvalue','$tcountry','1','0','0','$addedemp','$addedtime')";
if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:broiler_display_currency.php?ccid='.$ccid); }