<?php
//layer_check_eggprod_status.php
if(!isset($_SESSION)){ session_start(); }
include "newConfig.php";

$flock_code = $_GET['flock_code'];
$beps_flag = 0;

$sql = "SELECT * FROM `layer_shed_allocation` WHERE `code` = '$flock_code' AND `active` = '1' AND `dflag` = '0' AND `cls_flag` = '0'";
$query = mysqli_query($conn,$sql); $batch_code = "";
while($row = mysqli_fetch_array($query)){ $batch_code = $row['batch_code']; }

$sql = "SELECT * FROM `layer_batch` WHERE `code` = '$batch_code' AND `active` = '1' AND `dflag` = '0'";
$query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_array($query)){ $beps_flag = $row['beps_flag']; }
echo $beps_flag;
?>
