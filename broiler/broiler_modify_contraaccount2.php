<?php
//broiler_modify_contraaccount2.php
session_start(); include "newConfig.php";
$dbname = $_SESSION['dbase'];
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['contraaccount2'];

//Check Column Availability
$sql='SHOW COLUMNS FROM `account_contranotes`'; $query=mysqli_query($conn,$sql); $existing_col_names = array(); $i = 0;
while($row = mysqli_fetch_assoc($query)){ $existing_col_names[$i] = $row['Field']; $i++; }
if(in_array("to_batch", $existing_col_names, TRUE) == ""){ $sql = "ALTER TABLE `account_contranotes` ADD `to_batch` VARCHAR(300) NULL DEFAULT NULL COMMENT '' AFTER `tcoa`"; mysqli_query($conn,$sql); }

//Customer & Supplier Accounts
$sql = "SELECT * FROM `main_groups`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $cus_control_code[$row['code']] = $row['cus_controller_code']; $sup_control_code[$row['code']] = $row['sup_controller_code']; }
$sql = "SELECT * FROM `main_contactdetails`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $contact_group[$row['code']] = $row['groupcode']; $contact_type[$row['code']] = $row['contacttype']; }

//Farmer Accounts
$sql = "SELECT * FROM `broiler_farm` WHERE `dflag` = '0' ORDER BY `description` ASC"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $farmer_code[$row['code']] = $row['farmer_code']; $contact_type[$row['code']] = "F"; }
$sql = "SELECT * FROM `broiler_farmergroup`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $payable_acc_group[$row['code']] = $row['pay_acc_code']; }
$sql = "SELECT * FROM `broiler_farmer`"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){ $contact_group[$row['code']] = $row['farmer_group']; }

//Transaction Details
$trnum = $_POST['id_value'];
$date = date("Y-m-d",strtotime($_POST['date']));
$dcno = $_POST['dcno'];
$fcoa = $_POST['fcoa'];
$tcoa = $_POST['tcoa'];
$to_batch = $_POST['to_batch'];
$amount = $_POST['amount'];
$sector = $_POST['sector'];
$remarks = $_POST['remark'];
$flag = 0;
$active = 1;
$dflag = 0;

if($amount == "" || $amount == NULL || $amount == 0 || $amount == "0.00"){ $amount = "0.00"; }
//Add Transaction
$from_post = "UPDATE `account_contranotes` SET `date` = '$date',`dcno` = '$dcno',`fcoa` = '$fcoa',`tcoa` = '$tcoa',`to_batch` = '$to_batch',`amount` = '$amount',`warehouse` = '$sector',`remarks` = '$remarks',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `trnum` = '$trnum' AND `active` = '1' AND `dflag` = '0'";
if(!mysqli_query($conn,$from_post)){ die("Error 1:-".mysqli_error($conn)); }
else{
    //Add Account Summary
    if(!empty($contact_group[$tcoa])){
        $gcode = $to_coa = $to_vendor = ""; $gcode = $contact_group[$tcoa];
        if($contact_type[$tcoa] == "S"){ $to_coa = $sup_control_code[$gcode]; }
        else if($contact_type[$tcoa] == "F"){
            $fmr_code = $farmer_code[$tcoa];
            $gcode = $contact_group[$fmr_code];
            $to_coa = $payable_acc_group[$gcode];
        }
        else{ $to_coa = $cus_control_code[$gcode]; }
        $to_vendor = $tcoa;
    }
    else{
        $to_vendor = $to_coa = ""; $to_coa = $tcoa;
    }
    if(!empty($contact_group[$fcoa])){
        $gcode = $from_coa = $from_vendor = "";
        $gcode = $contact_group[$fcoa];
        if($contact_type[$fcoa] == "S"){ $from_coa = $sup_control_code[$gcode]; }
        else if($contact_type[$fcoa] == "F"){
            $fmr_code = $farmer_code[$fcoa];
            $gcode = $contact_group[$fmr_code];
            $from_coa = $payable_acc_group[$gcode];
        }
        else{ $from_coa = $cus_control_code[$gcode]; }
        $from_vendor = $fcoa;
    }
    else{ $from_vendor = $from_coa = ""; $from_coa = $fcoa; }
    
    $from_post = "UPDATE `account_summary` SET `coa_code` = '$from_coa',`vendor` = '$from_vendor',`date` = '$date',`dc_no` = '$dcno',`amount` = '$amount',`location` = '$sector',`remarks` = '$remarks',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `trnum` = '$trnum' AND `crdr` = 'CR'";
    if(!mysqli_query($conn,$from_post)){ die("Error 2:-".mysqli_error($conn)); } else{ }

    $from_post = "UPDATE `account_summary` SET `coa_code` = '$to_coa',`vendor` = '$to_vendor',`date` = '$date',`dc_no` = '$dcno',`amount` = '$amount',`location` = '$sector',`remarks` = '$remarks',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `trnum` = '$trnum' AND `crdr` = 'DR'";
    if(!mysqli_query($conn,$from_post)){ die("Error 3:-".mysqli_error($conn)); } else{ }
}
header('location:broiler_display_contraaccount2.php?ccid='.$ccid);
?>