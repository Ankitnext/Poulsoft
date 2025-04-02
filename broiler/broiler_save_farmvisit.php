<?php
//broiler_save_farmvisit.php
session_start(); include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['farmvisit'];

$ids = $_POST['idvalue'];
$active = 1;
$dflag = 0;

// $gdate = $_POST['gdate'];
$date = date("Y-m-d",strtotime($_POST['date']));

$added_empcode = $_POST['added_empcode'];
$vch_number = $_POST['vch_number'];

$trip_type = $_POST['trip_type'];
$farm_code = $_POST['farm_code'];
$meter_reading = $_POST['meter_reading'];
$total_km = $_POST['total_km'];
$remarks = $_POST['remarks'];

$trip_type2 = $_POST['trip_type2'];
$farm_code2 = $_POST['farm_code2'];
$meter_reading2 = $_POST['meter_reading2'];
$total_km2 = $_POST['total_km2'];
$remarks2 = $_POST['remarks2'];

 //Generate Transaction No.
//  $incr = 0; $prefix = $trnum = $fyear = "";
//  $trno_dt1 = generate_transaction_details($date,"farmvisit","GGS","display",$_SESSION['dbase']);
//  $trno_dt2 = explode("@",$trno_dt1);
//  $incr = $trno_dt2[0]; $prefix = $trno_dt2[1]; $trnum = $trno_dt2[2]; $fyear = $trno_dt2[3];
 //Generate Invoice transaction number format
 $sql = "SELECT prefix FROM `main_financialyear` WHERE `fdate` <='$date' AND `tdate` >= '$date'"; $query = mysqli_query($conn,$sql);
 while($row = mysqli_fetch_assoc($query)){ $pfx = $row['prefix']; }
 
 $sql = "SELECT * FROM `master_generator` WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'"; $query = mysqli_query($conn,$sql);
 while($row = mysqli_fetch_assoc($query)){ $trip_sheet = $row['trip_sheet']; } $incr = $trip_sheet + 1;
 
 $sql = "UPDATE `master_generator` SET `trip_sheet` = '$incr' WHERE `fdate` <='$date' AND `tdate` >= '$date' AND `type` = 'transactions'";
 if(!mysqli_query($conn,$sql)){ die("Error:-".mysqli_error($conn)); } else { }

 if($incr < 10){ $incr = '000'.$incr; } else if($incr >= 10 && $incr < 100){ $incr = '00'.$incr; } else if($incr >= 100 && $incr < 1000){ $incr = '0'.$incr; } else { }
 $trnum = "TS-".$addedemp."-".$pfx."".$incr;

// Make sure to identify the record uniquely (e.g., using an ID or another unique column)
$id = $_POST['record_id'];
if($total_km == "") {
    $total_km = 0;
}
if($total_km2 == "") {
    $total_km2 = 0;
}

$sql = "INSERT INTO `trip_sheet` (`tid`,`prefex`,`trnum`,`date`,`vch_number`,`meter_reading`,`trip_type`,`total_km`,`remarks`,`farm_code`,`active`,`dflag`,`added_empcode`,`addedtime`,`edit_empcode`,`updated`,`mob_flag`) 
VALUES('$incr','$pfx','$trnum','$date','$vch_number','$meter_reading','$trip_type','$total_km','$remarks','$farm_code','$active','$dflag','$added_empcode','$addedtime','$added_empcode','$addedtime','0')";

if(!mysqli_query($conn,$sql)){
    die("Error:-".mysqli_error($conn));
}
$sql = "INSERT INTO `trip_sheet` (`tid`,`prefex`,`trnum`,`date`,`vch_number`,`meter_reading`,`trip_type`,`total_km`,`remarks`,`farm_code`,`active`,`dflag`,`added_empcode`,`addedtime`,`edit_empcode`,`updated`,`mob_flag`) 
VALUES('$incr','$pfx','$trnum','$date','$vch_number','$meter_reading2','$trip_type2','$total_km2','$remarks2','$farm_code2','$active','$dflag','$added_empcode','$addedtime','$added_empcode','$addedtime','0')";

if(!mysqli_query($conn,$sql)){
    die("Error:-".mysqli_error($conn));
}
header('location:broiler_display_farmvisit.php?ccid='.$ccid);
?>

