<?php
//chicken_modify_vehfuel_fills.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];


// $ids = $_POST['idvalue'];
// $type = $_POST['type'];
// $days = $_POST['days'];
// $users = $_POST['users'];

// $user_code = $_POST['user_code'];
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

$userList = isset($users) ? implode(",", $users) : "None";
$active = 1;
$flag = $dflag = 0;

$trtype = "vehfuel_fills";
$trlink = "chicken_display_vehfuel_fills.php";
//echo $ids."@".$type."@".$days."@".$userList;
//Modify Transaction
// $sql = "UPDATE `dataentry_daterange` SET `type`='$type', `days`='$days', `users`='$userList' WHERE `id`='$ids';";
// if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }

$dsize = sizeof($mp_read);
for($i = 0;$i < $dsize;$i++){
	
	 if($mp_read[$i] == ""){ $mp_read[$i] = 0; }
    if($mc_read[$i] == ""){ $mc_read[$i] = 0; }
    if($ful_ltrs[$i] == ""){ $ful_ltrs[$i] = 0; }
    if($price[$i] == ""){ $price[$i] = 0; }
    if($amount[$i] == ""){ $amount[$i] = 0; }
	
	$sql2 = "UPDATE `vehicle_fuelfilling` SET `driver` = '$drname[$i]',`vno` = '$vno[$i]',`reading` = '$mp_read[$i]',`cur_reading` = '$mc_read[$i]',`fueltype` = '$ful_typ[$i]',`fuel_lt` = '$ful_ltrs[$i]',`price` = '$price[$i]',`amount` = '$amount[$i]',`remarks` = '$remark[$i]',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `id` = '$id_alist[$i]' AND `dflag` = '0'";
	if(!mysqli_query($conn,$sql2)){ echo die("Error:- ".mysqli_error($conn)); } else { }
}

header('location:chicken_display_vehfuel_fills.php');

