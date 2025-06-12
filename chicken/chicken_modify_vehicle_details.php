<?php
//chicken_modify_vehicle_details.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];


// $ids = $_POST['idvalue'];
// $type = $_POST['type'];
// $days = $_POST['days'];
// $users = $_POST['users'];

$user_code = $_POST['user_code'];

$vtype = $vcomp = $vno = $myear = $chsno = $engno = $pdate = $fcupto = $insupto = $polupto = $remark = $id_alist = array();
$i = 0; foreach($_POST['id_alist'] as $ucode){ $id_alist[$i] = $ucode; $i++; }
$i = 0; foreach($_POST['vtype'] as $ucode){ $vtype[$i] = $ucode; $i++; }
$i = 0; foreach($_POST['vcomp'] as $ucode){ $vcomp[$i] = $ucode; $i++; }
$i = 0; foreach($_POST['vno'] as $ucode){ $vno[$i] = $ucode; $i++; }
$i = 0; foreach($_POST['myear'] as $ucode){ $myear[$i] = date("Y-m-d", strtotime($ucode)); $i++; }
$i = 0; foreach($_POST['chsno'] as $ucode){ $chsno[$i] = $ucode; $i++; }
$i = 0; foreach($_POST['engno'] as $ucode){ $engno[$i] = $ucode; $i++; }
// $i = 0; foreach($_POST['date'] as $dates){ $date[$i] = date("Y-m-d", strtotime($dates)); $i++; }
$i = 0; foreach($_POST['pdate'] as $pdates){ $pdate[$i] = date("Y-m-d", strtotime($pdates)); $i++; }
$i = 0; foreach($_POST['fcupto'] as $fcuptos){ $fcupto[$i] = date("Y-m-d", strtotime($fcuptos)); $i++; }
$i = 0; foreach($_POST['insupto'] as $ucode){ $insupto[$i] = date("Y-m-d", strtotime($ucode)); $i++; }
$i = 0; foreach($_POST['polupto'] as $ucode){ $polupto[$i] = date("Y-m-d", strtotime($ucode)); $i++; }
$i = 0; foreach($_POST['remark'] as $ucode){ $remark[$i] = $ucode; $i++; }

$userList = isset($users) ? implode(",", $users) : "None";
$active = 1;
$flag = $dflag = 0;

$trtype = "vehicle_details";
$trlink = "chicken_display_vehicle_details.php";
//echo $ids."@".$type."@".$days."@".$userList;
//Modify Transaction
// $sql = "UPDATE `dataentry_daterange` SET `type`='$type', `days`='$days', `users`='$userList' WHERE `id`='$ids';";
// if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }

$dsize = sizeof($vcomp);
for($i = 0;$i < $dsize;$i++){
	
	
	$sql2 = "UPDATE `vehicle_details` SET `vtype` = '$vtype[$i]',`vno` = '$vno[$i]',`engineno` = '$engno[$i]',`company` = '$vcomp[$i]',`make_year` = '$myear[$i]',`pur_date` = '$pdate[$i]',`fc_date` = '$fcupto[$i]',`inu_date` = '$insupto[$i]',`polu_date` = '$polupto[$i]',`remarks` = '$remark[$i]',`chassisno` = '$chsno[$i]',`updatedemp` = '$addedemp',`updatedtime` = '$addedtime' WHERE `id` = '$id_alist[$i]' AND `dflag` = '0'";
	if(!mysqli_query($conn,$sql2)){ echo die("Error:- ".mysqli_error($conn)); } else { }
}

header('location:chicken_display_vehicle_details.php');

