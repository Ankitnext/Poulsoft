<?php
//broiler_modify_contcurrency.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['contcurrency'];

$sql='SHOW COLUMNS FROM `main_groups`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("cus_controller_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_groups` ADD `cus_controller_code` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Customer Asset CoA Account' AFTER `description`"; mysqli_query($conn,$sql); }
if(in_array("cus_prepayment_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_groups` ADD `cus_prepayment_code` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Customer Advance CoA Account' AFTER `cus_controller_code`"; mysqli_query($conn,$sql); }
if(in_array("sup_controller_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_groups` ADD `sup_controller_code` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Supplier Liability CoA Account' AFTER `cus_prepayment_code`"; mysqli_query($conn,$sql); }
if(in_array("sup_prepayment_code", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `main_groups` ADD `sup_prepayment_code` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Supplier Advance CoA Account' AFTER `sup_controller_code`"; mysqli_query($conn,$sql); }

$gtype = $_POST['gtype'];
$gdesc = $_POST['gdesc'];
$id = $_POST['idvalue'];
// $id = $_GET['id'];

// $fcountry = $_POST['fcountry'];
// $tcountry = $_POST['tcountry'];
// $fvalue = $_POST['fvalue'];
// $tvalue = $_POST['tvalue'];

$prfx = $_POST['prfx'];
$ct_name = $_POST['ct_name'];
$cr_name = $_POST['cr_name'];
$tvalue = $_POST['tvalue'];

$sql = "UPDATE `country_currency` SET `prefix` = '$prfx',`cont_name` = '$ct_name',`curr_name` = '$cr_name',`updatedtime` = '$addedtime',`updatedemp` = '$addedemp' WHERE `id` = '$id'";
if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { header('location:broiler_display_contcurrency.php?ccid='.$ccid); }

?>
