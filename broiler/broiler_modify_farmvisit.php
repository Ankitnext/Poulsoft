<?php
//broiler_modify_farmvisit.php
session_start();
include "newConfig.php";
$addedemp = $_SESSION['userid'];
date_default_timezone_set("Asia/Kolkata");
$addedtime = date('Y-m-d H:i:s');
$ccid = $_SESSION['farmvisit'];
 
$ids = $_POST['trnum']; $tid = $prefex = $trnum = ""; $aemp = $atime = array();
$sql = "SELECT * FROM `trip_sheet` WHERE `trnum` = '$ids' AND `dflag` = '0'"; $query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
    if($trnum == ""){ $tid = $row['tid']; $prefex = $row['prefex']; $trnum = $row['trnum']; }

    $aemp_row[$row['trip_type']] = $row['added_empcode'];
    $atime_row[$row['trip_type']] = $row['addedtime'];
}
if($trnum != ""){
    $sql3 = "DELETE FROM `trip_sheet` WHERE `trnum` = '$ids' AND `dflag` = '0'"; if(!mysqli_query($conn,$sql3)){ die("Error: 1".mysqli_error($conn)); } else{ }
}

$date = date("Y-m-d", strtotime($_POST['date']));
$added_empcode = $_POST['added_empcode'];
$vch_number = $_POST['vch_number'];

$trip_type = $farm_code = $meter_reading = $meter_image = $total_km = $remarks = $imei = $latitude = $longitude = array();
$i = 0; foreach($_POST['trip_type'] as $trip_types){ $trip_type[$i] = $trip_types; $i++; }
$i = 0; foreach($_POST['farm_code'] as $farm_codes){ $farm_code[$i] = $farm_codes; $i++; }
$i = 0; foreach($_POST['meter_reading'] as $meter_readings){ $meter_reading[$i] = $meter_readings; $i++; }
$i = 0; foreach($_POST['meter_image'] as $meter_images){ $meter_image[$i] = $meter_images; $i++; }
$i = 0; foreach($_POST['total_km'] as $total_kms){ $total_km[$i] = $total_kms; $i++; }
$i = 0; foreach($_POST['remarks'] as $remarkss){ $remarks[$i] = $remarkss; $i++; }
$i = 0; foreach($_POST['imei'] as $imeis){ $imei[$i] = $imeis; $i++; }
$i = 0; foreach($_POST['latitude'] as $latitudes){ $latitude[$i] = $latitudes; $i++; }
$i = 0; foreach($_POST['longitude'] as $longitudes){ $longitude[$i] = $longitudes; $i++; }

$active = 1;
$dflag = 0;

$dsize = sizeof($trip_type);
for($i = 0;$i < $dsize;$i++){
    if($meter_reading[$i] == ""){ $meter_reading[$i] = 0; }
    if($total_km[$i] == ""){ $total_km[$i] = 0; }
    if($latitude[$i] == ""){ $latitude[$i] = 0; }
    if($longitude[$i] == ""){ $longitude[$i] = 0; }

    $aemp = $atime = "";
    $aemp = $aemp_row[$trip_type[$i]];
    $atime = $atime_row[$trip_type[$i]];
    $sql = "INSERT INTO `trip_sheet` (`tid`,`prefex`,`trnum`,`date`,`vch_number`,`meter_reading`,`meter_image`,`trip_type`,`total_km`,`remarks`,`farm_code`,`active`,`dflag`,`added_empcode`,`addedtime`,`edit_empcode`,`updated`,`mob_flag`,`latitude`,`longitude`,`imei`) 
    VALUES('$tid','$prefex','$trnum','$date','$vch_number','$meter_reading[$i]','$meter_image[$i]','$trip_type[$i]','$total_km[$i]','$remarks[$i]','$farm_code[$i]','$active','$dflag','$aemp','$atime','$addedemp','$addedtime','0','$latitude[$i]','$longitude[$i]','$imei[$i]')";
    if(!mysqli_query($conn,$sql)){ die("Error 1:-".mysqli_error($conn)); } else{ }
}

header('location:broiler_display_farmvisit.php?ccid='.$ccid);
?>