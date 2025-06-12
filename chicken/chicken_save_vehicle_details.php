<?php
//chicken_save_vehicle_details.php
session_start(); include "newConfig.php";
include "chicken_generate_trnum_details.php";
$addedemp = $_SESSION['userid'];
$addedtime = date('Y-m-d H:i:s');
$client = $_SESSION['client'];
$active = 1;
$flag = $tdflag = $pdflag = 0;

$trtype = "vehicle_details";
$trlink = "chicken_display_vehicle_details.php";

$date = date("Y-m-d");

$database_name = $_SESSION['dbase'];
$sql = "SELECT * FROM `log_useraccess` WHERE `dblist` LIKE '$database_name' AND `flag` = '1' AND `dflag` = '0' ORDER BY `username` ASC";
$query = mysqli_query($conns, $sql); $emp_code = $emp_name = array();
while ($row = mysqli_fetch_assoc($query)) { $emp_code[$row['empcode']] = $row['empcode']; $emp_name[$row['empcode']] = $row['username']; }

$vtype = $vcomp = $vno = $myear = $chsno = $engno = $pdate = $fcupto = $insupto = $polupto = $remark = array();
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
$flag = $dflag = 0; $active = 1;

//Generate Transaction No.
$incr = 0; $prefix = $invoice = $trno_dt1 = ""; $trno_dt2 = array();
$trno_dt1 = generate_transaction_details($date,"vehicle_details","VD","generate",$_SESSION['dbase']);
$trno_dt2 = explode("@",$trno_dt1);
$incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $invoice = $trno_dt2[2]; $fy = $trno_dt2[3];

$dsize = sizeof($vtype);
for($i = 0;$i < $dsize;$i++){
	$sql2 = "INSERT INTO `vehicle_details` (`prefix`,`tid`,`trnum`,`date`,`vtype`,`company`,`vno`,`make_year`,`chassisno`,`engineno`,`pur_date`,`fc_date`,`inu_date`,`polu_date`,`remarks`,`active`,`dflag`,`addedemp`,`addedtime`,`updatedtime`) 
	VALUES ('$prefix','$incr','$invoice','$date','$vtype[$i]','$vcomp[$i]','$vno[$i]','$myear[$i]','$chsno[$i]','$engno[$i]','$pdate[$i]','$fcupto[$i]','$insupto[$i]','$polupto[$i]','$remark[$i]','$active','$dflag','$addedemp','$addedtime','$addedtime');";
	if(!mysqli_query($conn,$sql2)){ echo die("Error:- ".mysqli_error($conn)); } else { }
			
}
       
header('location:chicken_display_vehicle_details.php');

