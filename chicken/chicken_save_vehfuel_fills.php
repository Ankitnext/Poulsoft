<?php
//chicken_save_vehfuel_fills.php
session_start(); include "newConfig.php";
include "chicken_generate_trnum_details.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];
$active = 1;
$flag = $tdflag = $pdflag = 0;

$trtype = "vehfuel_fills";
$trlink = "chicken_display_vehfuel_fills.php";

$date = date("Y-m-d");


$database_name = $_SESSION['dbase'];
$sql = "SELECT * FROM `log_useraccess` WHERE `dblist` LIKE '$database_name' AND `flag` = '1' AND `dflag` = '0' ORDER BY `username` ASC";
$query = mysqli_query($conns, $sql); $emp_code = $emp_name = array();
while ($row = mysqli_fetch_assoc($query)) { $emp_code[$row['empcode']] = $row['empcode']; $emp_name[$row['empcode']] = $row['username']; }

$vno = $drname = $mp_read = $mc_read = $ful_typ = $mc_read = $mc_read = array();
// $i = 0; foreach($_POST['date'] as $dates){ $date[$i] = date("Y-m-d", strtotime($dates)); $i++; }
$i = 0; foreach($_POST['vno'] as $ucode){ $vno[$i] = $ucode; $i++; }
$i = 0; foreach($_POST['drname'] as $ucode){ $drname[$i] = $ucode; $i++; }
$i = 0; foreach($_POST['mp_read'] as $ucode){ $mp_read[$i] = $ucode; $i++; }
$i = 0; foreach($_POST['mc_read'] as $ucode){ $mc_read[$i] = $ucode; $i++; }
$i = 0; foreach($_POST['ful_typ'] as $ucode){ $ful_typ[$i] = $ucode; $i++; }
$i = 0; foreach($_POST['ful_ltrs'] as $ucode){ $ful_ltrs[$i] = $ucode; $i++; }
$i = 0; foreach($_POST['price'] as $ucode){ $price[$i] = $ucode; $i++; }
$i = 0; foreach($_POST['amount'] as $ucode){ $amount[$i] = $ucode; $i++; }
$i = 0; foreach($_POST['remark'] as $ucode){ $remark[$i] = $ucode; $i++; }
$flag = $dflag = 0; $active = 1;

//Generate Transaction No.
$incr = 0; $prefix = $invoice = $trno_dt1 = ""; $trno_dt2 = array();
$trno_dt1 = generate_transaction_details($date,"vehfuel_fills","VF","generate",$_SESSION['dbase']);
$trno_dt2 = explode("@",$trno_dt1);
$incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $invoice = $trno_dt2[2]; $fy = $trno_dt2[3];

$dsize = sizeof($mp_read);
for($i = 0;$i < $dsize;$i++){

	 if($mp_read[$i] == ""){ $mp_read[$i] = 0; }
    if($mc_read[$i] == ""){ $mc_read[$i] = 0; }
    if($ful_ltrs[$i] == ""){ $ful_ltrs[$i] = 0; }
    if($price[$i] == ""){ $price[$i] = 0; }
    if($amount[$i] == ""){ $amount[$i] = 0; }

	$sql2 = "INSERT INTO `vehicle_fuelfilling` (`prefix`,`tid`,`trnum`,`date`,`vno`,`driver`,`reading`,`cur_reading`,`fueltype`,`fuel_lt`,`price`,`amount`,`remarks`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
	VALUES ('$prefix','$incr','$invoice','$date','$vno[$i]','$drname[$i]','$mp_read[$i]','$mc_read[$i]','$ful_typ[$i]','$ful_ltrs[$i]','$price[$i]','$amount[$i]','$remark[$i]','$active','$dflag','$addedemp','$addedtime','$addedtime');";
	if(!mysqli_query($conn,$sql2)){ echo die("Error:- ".mysqli_error($conn)); } else { }
	
	// $sql2 = "INSERT INTO `vehicle_fuelfilling` (`file_name`,`user_code`,`min_days`,`max_days`,`flag`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
	// VALUES ('$file_name[$i]','$ecode','$min_days[$i]','$max_days[$i]','$flag','$active','$dflag','$addedemp','$addedtime','$addedtime');";
	// if(!mysqli_query($conn,$sql2)){ echo die("Error:- ".mysqli_error($conn)); } else { }
			
	
}
       
header('location:chicken_display_vehfuel_fills.php');

